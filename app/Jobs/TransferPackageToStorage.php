<?php

namespace App\Jobs;

use App\Interfaces\ArchivalStorageInterface;
use App\StorageLocation;
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
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StorageLocation $storageLocation, $localStorage, $uploadFileAtPath)
    {
        $this->localStorage = $localStorage;
        $this->storageLocation = $storageLocation;
        $this->uploadFileAtPath = $uploadFileAtPath;
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
            $message = "Upload failed: file does not exist '".$this->uploadFileAtPath."'";
            Log::error($message);
            $this->fail(new \Exception($message));
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
                $message = "Upload don't support directory uploads";
                Log::error($message);
                $this->fail(new \Exception($message));
                return;
            }
            $uploadPath = substr($file->getPath(), $baseDirLen);
            Log::debug("Uploading file '" . $file->getRealPath() . "' to '" . $uploadPath . "'");
            $result = $storage->upload( $this->storageLocation, $uploadPath, $file );
            if($result === false) {
                $message = "Upload failed : " . $result;
                Log::error($message);
                $this->fail(new \Exception($message));
                return;
            }
        }
    }
}
