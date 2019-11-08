<?php


namespace App\Listeners;


use App\ArchivematicaService;
use App\Bag;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\TransferException;

class ArchivematicaStorageServerClient
{
    private $connection;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ArchivematicaServiceConnection $connection = null)
    {
        $this->connection = $connection ?? new ArchivematicaServiceConnection();
    }

    public function getFileDetails($uuid)
    {
        return $this->connection->doRequest('GET', 'v2/file/'.$uuid.'/');
    }

}
