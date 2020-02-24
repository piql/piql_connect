<?php

namespace App\Jobs;

use App\Interfaces\ArchivalStorageInterface;
use App\StorageLocation;
use App\FileObject;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Log;

class TransferPackageToStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $storageLocation;
    private $uploadFileAtPath;
    private $localStorage;
    private $storable_type;
    private $storable_id;
    /**
     * Create a new job instance.
     *
     * @param storageLocation The remote storage location that we will upload to
     * @param localStorage The local disk on where we will upload files from
     * @param uploadFileAtPath The file or folder in localStorage that we want to upload
     * @param storable_type The model name for the file object storable morph, e.g. App\Aip, App\Dip
     * @param storable_id The model id for the file object storable morph, aip or dip id
     * @return void
     */
    public function __construct(StorageLocation $storageLocation, $localStorage, $uploadFileAtPath, $storable_type, $storable_id)
    {
        $this->localStorage = $localStorage;
        $this->storageLocation = $storageLocation;
        $this->uploadFileAtPath = $uploadFileAtPath;
        $this->storable_type = $storable_type;
        $this->storable_id = $storable_id;
    }

    /**
     * Execute the job.
     *
     * @param ArchivalStorageInterface $storage
     * @return void
     */
    public function handle(ArchivalStorageInterface $storage)
    {
        Log::info("Uploading files to " . $this->storageLocation->human_readable_name);

        if($this->localStorage->exists($this->uploadFileAtPath) !== true) {
            Log::error("Upload failed: file does not exist '".$this->uploadFileAtPath."'");
            return;
        }
        $baseDir = $this->localStorage->path('');
        $baseDirLen = strlen($baseDir);
        $files = $this->localStorage->allFiles($this->uploadFileAtPath);
        if(count($files) == 0) {
            $files = [ $this->uploadFileAtPath ];
        }
        foreach ($files as $filePath) {
            $file = new \Illuminate\Http\File($this->localStorage->path($filePath), false);
            if($file->isDir()) {
                throw new \Exception("ArchivalStorage cannot upload directories yet");
                return;
            }
            $uploadPath = substr($file->getPath(), $baseDirLen);
            $uploadRealPath = "{$uploadPath}/{$file->getFileName()}";
            Log::debug("Uploading file to '{$this->storageLocation->human_readable_name}:{$uploadRealPath}'" );
            $result = $storage->upload( $this->storageLocation, $uploadPath, $file );
            if($result === false) {
                Log::error("Upload failed : " . $result);
                return;
            }

            try {
            FileObject::create([
                'fullpath' => $uploadRealPath,
                'filename' => $file->getFilename(),
                'path' => $file->getPath(),
                'size' => $file->getSize(),
                'object_type' => 'file',
                'info_source' => "connect",
                'mime_type' => $file->getMimeType(),
                'storable_type' => $this->storable_type,
                'storable_id' => $this->storable_id
            ]);
            } catch( \Exception $ex )
            {
                Log::error("Failed to create file object for file at {$this->storageLocation}:{$uploadPath}");
            }
        }

        if($this->localStorage->deleteDirectory($this->uploadFileAtPath) === false)
        {
            Log::error("Failed to delete source files {$this->uploadFileAtPath} after the upload");
        }
    }
}
