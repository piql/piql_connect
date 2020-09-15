<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use File;
use App\StorageLocation;
use App\Interfaces\ArchivalStorageInterface;
use App\Job;

class MessageProcess extends Command
{
    private $pollingInterval = 30;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for new messages in inbox and process them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Listening for messages');

        $storageLocation = StorageLocation::where('storable_type', 'App\Message')->first();
        $storage = \App::make(ArchivalStorageInterface::class);

        while (true) {
            sleep($this->pollingInterval);

            // Fetch messages from inbox
            $messageFiles = $storage->ls($storageLocation);
            foreach ($messageFiles as $messageFile) {
                $messageFilePath = $storage->download($storageLocation, $messageFile, $messageFile);

                // Give all systems a chance to fetch the message before we delete it
                // TODO Remove this delay when each instance has it's own inbox
                sleep($this->pollingInterval);

                if (!$storage->delete($storageLocation, $messageFile)) {
                    $this->warn('Failed to delete message : ' . $messageFile);
                }
                $this->moveMessage('outgoing', 'messages-processing', $messageFile);
                $this->info('Fetched message: ' . $messageFile);
            }

            // Process messages
            $messageFiles = Storage::disk('messages-processing')->files();
            foreach ($messageFiles as $messageFile) {
                try {
                    $this->processMessage($messageFile);
                } catch (\Exception $ex) {
                    $this->error('Failed to process message: ' . $ex->getMessage());
                    $this->moveMessage('messages-processing', 'messages-failed', $messageFile);
                }
	    }
        }
    }

    private function processMessage($messageFile)
    {
        $this->info('Processing message: ' . $messageFile);
        $message = json_decode(File::get(Storage::disk('messages-processing')->path($messageFile)));
        if (!isset($message->{'message-type'}))
        {
            $this->error('Message missed type: ' . $messageFile);
            $this->moveMessage('messages-processing', 'messages-failed', $messageFile);
        }
        $messageType = $message->{'message-type'};

        $success = false;
        switch ($messageType) {
            case 'ingest-status':
               $success = $this->processIngestMessage($message);
               break;
        }

        if ($success) {
            $this->info('Message was processed: ' . $messageFile);
            $this->moveMessage('messages-processing', 'messages-processed', $messageFile);
        }
        else {
            $this->info('Failed to process message: ' . $messageFile);
            $this->moveMessage('messages-processing', 'messages-failed', $messageFile);
        }
    }

    private function processIngestMessage($message)
    {
        $signal = $message->{'signal'};
        $referenceId = $message->{'reference-id'};
        $this->info('Got signal: ' . $signal . ' for job ' . $referenceId);

        // Find job
        $job = Job::where('uuid', $referenceId)->first();
        if (!$job) {
            return false;
        }

        // Set job state
        $job->applyTransition($signal)->save();
        return true;
    }

    private function moveMessage($fromDisk, $toDisk, $message)
    {
        rename(Storage::disk($fromDisk)->path($message), Storage::disk($toDisk)->path($message)); // TODO: Use Storage::move
    }
}
