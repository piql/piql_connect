<?php


namespace App\Services;


use App\ArchivematicaService;

    class ArchivematicaConnectionService implements \App\Interfaces\ArchivematicaConnectionServiceInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function getServiceConnectionByUuid($uuid) {
        $service = ArchivematicaService::find($this->serviceUuid);
    }
}
