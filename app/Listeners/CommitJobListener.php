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
            $aips = $job->aips()->get();
            $basename = "data_{$job->uuid}";
            Log::info('Building job data package (job id=' . $job->uuid . ', number of AIPs=' . $aips->count() . ')');
            $dataFilePath = $fileArchiveService->buildTarFromAipCollectionIncrementally($aips, $basename);

            // Send data package to S3
            Log::info('Sending job data package to outbox (job id=' . $job->uuid . ', source=' . $dataFilePath . ')');
            $destinationDir = "";
            if ($this->storage->upload($this->storageLocation, $destinationDir, $dataFilePath)) {
                unlink($dataFilePath);
            } else {
                Log::error('Failed to upload '.$job->uuid.' file '.$dataFilePath);
            }

            // Build info package
            $basename = "info_{$job->uuid}.tar";
            $infoFilePath = $this->outgoing->path($basename);
            Log::info('Building job info package for job (job id=' . $job->uuid . ')');
            $this->createInfoPackage($infoFilePath, $job);

            // Send info package to S3
            Log::info('Sending job info package to outbox (job id=' . $job->uuid . ', source=' . $infoFilePath . ')');
            $destinationDir = "";
            if ($this->storage->upload($this->storageLocation, $destinationDir, $infoFilePath)) {
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
        $user = \App\User::find($job->owner);
        if ($user === null)
        {
            throw new \Exception('Could not find user with id: '.$job->owner);
        }

        // Create job.json
        $jobInfoPath = "{$job->jobFilesDir()}/job.json";
        $content = array(
            'reference_id' => $job->uuid,
            'job_name' => $job->name,
            'owner' => array(
                'id'=> $job->owner,
                'username' => $user->username ?? "",
                'full_name' => $user->full_name ?? "",
                'email' => $user->email ?? ""
            ),
        );
        file_put_contents($jobInfoPath, json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Create TAR
        $tar = new \PharData($filePath);
        $tar->buildFromDirectory($job->jobFilesDir());
    }
}
