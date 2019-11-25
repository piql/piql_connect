<?php


namespace App\Services;

use App\Interfaces\ArchivematicaStorageClientInterface;

class ArchivematicaStorageClientService implements ArchivematicaStorageClientInterface
{
    private $storageService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( $storageConnection )
    {
        $this->storageService = $storageConnection->getFirstAvailableStorageServer();
    }

    public function getFileDetails( $uuid )
    {
        return $this->storageService->doRequest('GET', 'v2/file/'.$uuid.'/');
    }

}
