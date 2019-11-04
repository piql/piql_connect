<?php


namespace App\Listeners;


use App\ArchivematicaService;
use App\Bag;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\TransferException;

class ArchivematicaClient
{
    private $connection;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ArchivematicaServiceConnection $connection)
    {
        $this->connection = $connection;
        // todo: move this to controller
        //$service = ArchivematicaService::first();
        //if( $service == null ) {
        //    abort( response()->json( ['error' => 428, 'message' => 'No archivematica service configuration could be found on this instance. A valid service configuration must be created first.'], 428 ) );
        //}
    }

    public function initiateTransfer($transferName, $accession, $directory)
    {
        $locationId = env('STORAGE_LOCATION_ID');
        $paths = base64_encode($locationId.":/".$directory);
        $formData =
            [
                "name" => $transferName,
                "type" => "zipped bag",
                "accession" => $accession,
                "paths[]" => $paths,
                "row_ids[]" => ""
            ];

        return $this->connection->doRequest('POST', 'transfer/start_transfer/', ['form_params' => $formData]);
    }

    public function getUnapprovedList()
    {
        return $this->connection->doRequest('GET', 'transfer/unapproved/');
    }

    public function getTransferStatus($uuid = "")
    {
        return $this->connection->doRequest('GET', 'transfer/status/'.$uuid);
    }

    public function getIngestStatus($uuid = "")
    {
        return $this->connection->doRequest('GET', 'ingest/status/'.$uuid);
    }

    public function approveTransfer($directory)
    {
        $formData =
            [
                "type" => "zipped bag",
                "directory" => $directory,
            ];

        return $this->connection->doRequest('POST', 'transfer/approve/',
            ['form_params' => $formData]
        );
    }

    public function hideTransferStatus($uuid)
    {
        return $this->connection->doRequest('DELETE', "transfer/{$uuid}/delete/");
    }

    public function hideIngestStatus($uuid)
    {
        return $this->connection->doRequest('DELETE', "ingest/{$uuid}/delete/");
    }
}
