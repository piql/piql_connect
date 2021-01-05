<?php

namespace App\Services;

use App\ArchivematicaService;
use App\Interfaces\ArchivematicaDashboardClientInterface;
use App\Interfaces\ArchivematicaConnectionServiceInterface;
use App\Services\ArchivematicaServiceConnection;
use App\Bag;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\TransferException;
use Log;

class ArchivematicaDashboardClientService implements ArchivematicaDashboardClientInterface
{
    public  const TRANSFER_TYPE_STANDARD = "standard";
    public  const TRANSFER_TYPE_ZIPPED_BAG = "zipped bag";
    private $dashboard;
    private $transferType;

    /**
     * Create the event listener.
     *
     * @param ArchivematicaConnectionServiceInterface $connectionService
     * @param string $transferType
     */
    public function __construct( ArchivematicaConnectionServiceInterface $connectionService, $transferType = ArchivematicaDashboardClientService::TRANSFER_TYPE_ZIPPED_BAG)
    {
        $this->dashboard = $connectionService->getFirstAvailableDashboard();
        $this->transferType = $transferType;
    }

    public function initiateTransfer($transferName, $accession, $directory)
    {
        $locationId = env('STORAGE_LOCATION_ID');
        $paths = base64_encode($locationId.":/".$directory);
        $formData =
            [
                "name" => $transferName,
                "type" => $this->transferType,
                "accession" => $accession,
                "paths[]" => $paths,
                "row_ids[]" => ""
            ];

        return $this->dashboard->doRequest('POST', 'transfer/start_transfer/', ['form_params' => $formData]);
    }

    public function getUnapprovedList()
    {
        return $this->dashboard->doRequest('GET', 'transfer/unapproved/');
    }

    public function getTransferStatus($uuid = "")
    {
        return $this->dashboard->doRequest('GET', 'transfer/status/'.$uuid);
    }

    public function getIngestStatus($uuid = "")
    {
        return $this->dashboard->doRequest('GET', 'ingest/status/'.$uuid);
    }

    public function approveTransfer($directory)
    {
        $formData =
            [
                "type" => $this->transferType,
                "directory" => $directory,
            ];

        return $this->dashboard->doRequest('POST', 'transfer/approve/',
            ['form_params' => $formData]
        );
    }

    public function hideTransferStatus($uuid)
    {
        return $this->dashboard->doRequest('DELETE', "transfer/{$uuid}/delete/");
    }

    public function hideIngestStatus($uuid)
    {
        return $this->dashboard->doRequest('DELETE', "ingest/{$uuid}/delete/");
    }
}
