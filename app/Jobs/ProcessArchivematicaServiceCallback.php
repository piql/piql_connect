<?php

namespace App\Jobs;

use App\Aip;
use App\Bag;
use App\ArchivematicaService;
use App\Dip;
use App\Events\ErrorEvent;
use App\Interfaces\ArchivematicaStorageClientInterface;
use App\StorageLocation;
use App\StorageProperties;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Log;

class ProcessArchivematicaServiceCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $packageUuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $packageUuid )
    {
        $this->packageUuid = $packageUuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( ArchivematicaStorageClientInterface $storageClient )
    {
        Log::info("Processing callback for stored package with uuid: " . $this->packageUuid);

        $response = $storageClient->getFileDetails( $this->packageUuid );
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

            $aip = new Aip([
                'external_uuid' => $this->packageUuid,
                'owner' => $bag->owner,
                'online_storage_location_id' => $bag->owner()->settings->defaultAipStorageLocationId,
                'online_storage_path' => $contents->current_path
            ]);
            $aip->save();

            dispatch( new TransferPackageToStorage(
                StorageLocation::find($bag->owner()->settings->defaultAipStorageLocationId),
                Storage::disk('am_aip'),
                $contents->current_path
            ));
        } elseif ($contents->package_type == "DIP") {
            Log::info("DIP uuid '" . $this->packageUuid . "' is linked to bag " . $bag->uuid);
            $bag->storage_properties->dip_uuid = $this->packageUuid;
            $bag->storage_properties->save();

            $dip = new Dip([
                'external_uuid' => $this->packageUuid,
                'owner' => $bag->owner,
                'online_storage_location_id' => $bag->owner()->settings->defaultDipStorageLocationId,
                'online_storage_path' => $contents->current_path
            ]);
            $dip->save();

            dispatch( new TransferPackageToStorage(
                StorageLocation::find($bag->owner()->settings->defaultDipStorageLocationId),
                Storage::disk('am_dip'),
                $contents->current_path
            ));
        } else {
            $message = "Unsupported package type: " . $contents->package_type . " ";
            $message .= "response: " . json_encode($response->contents);
            Log::error($message);
        }

    }
}
