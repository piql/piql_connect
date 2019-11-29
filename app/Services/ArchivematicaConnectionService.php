<?php


namespace App\Services;


use App\ArchivematicaService;
use App\Services\ArchivematicaServiceConnection;
use Log;

class ArchivematicaConnectionService implements \App\Interfaces\ArchivematicaConnectionServiceInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function getServiceConnectionByUuid($uuid) {
        $service = ArchivematicaService::find($uuid);
        if( $service == null ) {
            throw new \Exception("Could not find service connection with uuid {$uuid}");
        }
        return new ArchivematicaServiceConnection( $service );
    }

    public function getFirstAvailableDashboard() {
        $service = ArchivematicaService::whereServiceType( 'dashboard' )->first();

        if( $service == null ) {
            throw new \Exception("Could not find any ArchivematicaServices of type 'dashboard'");
        }

        return new ArchivematicaServiceConnection( $service );
    }

    public function getFirstAvailableStorageServer()  {
        $service = ArchivematicaService::whereServiceType( 'storage' )->first();
        if( $service == null ) {
            throw new \Exception("Could not find any ArchivematicaServices of type 'storage'");
        }

        return new ArchivematicaServiceConnection( $service );
    }

}
