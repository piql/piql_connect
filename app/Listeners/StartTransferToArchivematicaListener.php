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
        $service = ArchivematicaService::latest()->first();
        $this->apiClient = new Guzzle([
            'base_uri' => "http://connect.local/api/v1/ingest/am/",
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
        Log::debug("event start transfer for bag: ".$event->bag);
        $bagId = $event->bag->id;
        $bag = Bag::find($bagId);
        $bag->status = "transferring";
        $bag->save();
        Log::debug("Handling StartTransferToArchivematicaEvent for bag ".$bag->zipBagFileName()." with id: ".$bag->id);
        $this->startTransfer($bag->id);
        $waitingForTransfer = true;
        while($waitingForTransfer)
        {
            sleep(2);
            $currentStatuses = $this->getTransferStatus();
            foreach($currentStatuses->results as $status)
            {
                if($status->name == $bag->zipBagFileName())
                {
                    $this->approveTransfer($bag->id);
                    $bag->status = "ingesting";
                    $bag->save();
                    $waitingForTransfer = false;
                }
            }
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

    public function approveTransfer($bagId)
    {
        return json_decode($this->apiClient->post("transfer/approve/".$bagId)->getBody()->getContents());
    }
}
