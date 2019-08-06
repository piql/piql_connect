<?php

namespace App\Listeners;

use App\Events\StartTransferToArchivematicaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Suppoer\Facades\Storage;
use GuzzleHttp\Client as Guzzle;
use Log;
use App\Bag;
use App\ArchivematicaService;

class StartTransferToArchivematicaListener implements ShouldQueue
{
    public $apiClient;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $am_proxy = url('/api/v1/ingest/am')."/";
        $this->apiClient = new Guzzle([
            'base_uri' => $am_proxy,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(StartTransferToArchivematicaEvent $event)
    {
        $bagId = $event->bag->id;
        $bag = Bag::find($bagId);
        Log::info("Handling StartTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);
        $bag->status = "transferring";
        $bag->save();
        $this->startTransfer($bag->id);
        $waitingForTransfer = true;
        while($waitingForTransfer)
        {
            $currentTransferStatuses = $this->getTransferStatus();
            foreach($currentTransferStatuses->results as $status)
            {
               if($status->name == $bag->zipBagFileName())
               {
                    $this->approveTransfer($bag->id);
                    $bag->status = "ingesting";
                    $bag->save();
                    $waitingForTransfer = false;
                    break;
               }
            }
            sleep(2);
        }

        while(true)
        {
            $currentIngestStatuses = $this->getIngestStatus();
            foreach($currentIngestStatuses->results as $status)
            {
                if($status->name.".zip" == $bag->zipBagFileName() && $status->status == "COMPLETE")
                {
                    Log::info("Ingest complete for SIP with bag id ".$bag->id);
                    $bag->status = "complete";
                    $bag->save();
                    return;
                }
            }
            sleep(2);
        }

    }

    public function startTransfer($bagId)
    {
        return json_decode($this->apiClient->post("transfer/start/".$bagId)->getBody()->getContents());
    }

    public function getTransferStatus()
    {
        return json_decode($this->apiClient->get("transfer/status/")->getBody()->getContents());
    }

    public function getIngestStatus()
    {
        return json_decode($this->apiClient->get("ingest/status/")->getBody()->getContents());
    }


    public function approveTransfer($bagId)
    {
        return json_decode($this->apiClient->post("transfer/approve/".$bagId)->getBody()->getContents());
    }
}
