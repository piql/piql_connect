<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use App\StorageLocation;
use App\Interfaces\ArchivalStorageInterface;
use App\Events\CommitJobEvent;
use Log;

class CommitJobListener implements ShouldQueue
{
    private $outgoing;
    private $storageLocation;
    private $storage;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->outgoing = Storage::disk('outgoing');
        $this->storageLocation = StorageLocation::where('storable_type', 'App\Job')->first();
        $this->storage = \App::make(ArchivalStorageInterface::class);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CommitJobEvent $event)
    {
        try {
            $job = $event->job;
            $fileArchiveService = \App::make( \App\Interfaces\FileArchiveInterface::class );

            // Build data package
            $aips = $job->aips()->get()->all();
            $basename = "data_{$job->uuid}";
            $dataFilePath = $fileArchiveService->buildTarFromAipCollectionIncrementally($aips, $basename);

            if ($this->storage->upload($this->storageLocation, $basename, $dataFilePath)) {
                unlink($dataFilePath);
            } else {
                Log::error('Failed to upload '.$job->uuid.' file '.$dataFilePath);
            }

            // Build info package
            $basename = "info_{$job->uuid}.tar";
            $infoFilePath = $this->outgoing->path($basename);
            $this->createInfoPackage($infoFilePath, $job);

            if ($this->storage->upload($this->storageLocation, $basename, $infoFilePath)) {
                unlink($infoFilePath);
            } else {
                Log::error('Failed to upload '.$job->uuid.' file '.$infoFilePath);
            }
        } catch (Throwable $e) {
            Log::error('Failed to commit job {$job->uuid}');
        }
    }

    /**
     * Create package with additional job info.
     *
     * @param  string  $filePath
     * @return bool
     */
    private function createInfoPackage($filePath, $job)
    {
        // Create job.json
        $jobInfoPath = "{$job->jobFilesDir()}/job.json";
        $content = array(
            'reference_id' => $job->uuid,
            'job_name' => $job->name,
            'owner' => $job->owner );
        file_put_contents($jobInfoPath, json_encode($content));

        // Create TAR
        $tar = new \PharData($filePath);
        $tar->buildFromDirectory($job->jobFilesDir());
    }
}
