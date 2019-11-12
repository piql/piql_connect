<?php


namespace App\Services;


use App\ArchivematicaService;
use App\Listeners\ArchivematicaServiceConnection;

class ArchivematicaConnectionService implements \App\Interfaces\ArchivematicaConnectionServiceInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function getServiceConnectionByUuid($uuid) {
        $service = ArchivematicaService::find($uuid);
        return $service ? new ArchivematicaServiceConnection($service) : null;
    }
}
