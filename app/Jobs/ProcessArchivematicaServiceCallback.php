<?php

namespace App\Jobs;

use App\Bag;
use App\ArchivematicaService;
use App\Events\ErrorEvent;
use App\Listeners\ArchivematicaServiceConnection;
use App\Listeners\ArchivematicaStorageServerClient;
use App\Interfaces\ArchivematicaConnectionServiceInterface;
use App\StorageProperties;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class ProcessArchivematicaServiceCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $packageUuid;
    private $serviceUuid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($serviceUuid, $packageUuid)
    {
        $this->serviceUuid = $serviceUuid;
        $this->packageUuid = $packageUuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ArchivematicaConnectionServiceInterface $connectionService)
    {
        Log::info("Processing uploaded package with uuid: " . $this->packageUuid);

        $serviceConnection = $connectionService->getServiceConnectionByUuid($this->serviceUuid);
        if($serviceConnection == null) {
            // todo : make proper action
            $message = "No service found with uuid " . $this->serviceUuid;

            Log::error($message);
            return;
        }
        $client = new  ArchivematicaStorageServerClient( $serviceConnection);
        $response = $client->getFileDetails($this->packageUuid);
        $contents = $response->contents;

        if($response->statusCode != 200) {

            // todo : make proper action
            $message = "Get file details failed with error code " . $response->statusCode;
            if (isset($contents->error) && ($contents->error == true)) {
                $message += " and error message: " . $contents->message;
            }

            Log::error($message);
            return;
        }

        $parts = explode('/', $response->contents->current_path);
        $subject = array_pop($parts);
        preg_match('/(\w{8})-(\w{4})-(\w{4})-(\w{4})-(\w{12})/', $subject, $matches, PREG_OFFSET_CAPTURE);
        $bag = Bag::where('uuid', $matches[0][0])->first();
        if($bag == null) {
            // todo : make proper action
            $message = "Could not find any storage properties linked to this uuid: " . $this->packageUuid . " ";
            $message .= "response: " . json_encode($response->contents);
            Log::error($message);
            return;
        }

        if($contents->package_type == "AIP") {
            Log::info("AIP uuid '" . $this->packageUuid . "' is linked to bag " . $bag->uuid);
            $bag->storage_properties->aip_uuid = $this->packageUuid;
            $bag->storage_properties->save();
        } elseif ($contents->package_type == "DIP") {
            Log::info("DIP uuid '" . $this->packageUuid . "' is linked to bag " . $bag->uuid);
            $bag->storage_properties->dip_uuid = $this->packageUuid;
            $bag->storage_properties->save();
        } else {
            $message = "Unsupported package type: " . $contents->package_type . " ";
            $message .= "response: " . json_encode($response->contents);
            Log::error($message);
        }

    }
}
