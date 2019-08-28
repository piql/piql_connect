<?php


namespace App\Listeners;


use GuzzleHttp\Client as Guzzle;

class ArchivematicaClient
{
    private $apiClient;
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

    public function initiateTransfer($bagId)
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

    public function hideTransferStatus($uuid)
    {
        return json_decode($this->apiClient->delete("transfer/".$uuid)->getBody()->getContents());
    }

    public function hideIngestStatus($uuid)
    {
        return json_decode($this->apiClient->delete("ingest/".$uuid)->getBody()->getContents());
    }
}
