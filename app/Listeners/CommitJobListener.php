<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use App\Events\CommitJobEvent;
use Log;

class CommitJobListener implements ShouldQueue
{
    private $outgoing;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->outgoing = Storage::disk('outgoing');
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
            $basename = "{$job->uuid}_data";
            $dataFilePath = $fileArchiveService->buildTarFromAipCollectionIncrementally($aips, $basename);

            // Send data package to S3
            // TODO: Send data package to S3

            // Build info package
            $infoFilePath = $this->outgoing->path("{$job->uuid}_info.tar");
            $this->createInfoPackage($infoFilePath, $job);

            // Send info package to S3
            // TODO: Send info package to S3

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
