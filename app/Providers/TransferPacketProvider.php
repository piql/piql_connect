<?php

namespace App\Providers;

use App\Services\ArchivematicaDashboardClientService;
use Illuminate\Support\ServiceProvider;

class TransferPacketProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $packetType = env( 'APP_TRANSFER_PACKET_TYPE', "AM_ZIPPED_BAG");

        if($packetType == "AM_ZIPPED_BAG") {
            $this->app->bind('App\Interfaces\ArchivematicaDashboardClientInterface', function( $app ) {
                $connectionService = $app->make( 'App\Interfaces\ArchivematicaConnectionServiceInterface' );
                return new ArchivematicaDashboardClientService( $connectionService,
                    ArchivematicaDashboardClientService::TRANSFER_TYPE_ZIPPED_BAG );
            });

            $this->app->bind('App\Interfaces\TransferPacketBuilder', function( $app ) {
                return new \App\Services\BagItBuilder($app);
            });

        } else if($packetType == "AM_STANDARD") {
            $this->app->bind('App\Interfaces\ArchivematicaDashboardClientInterface', function( $app ) {
                $connectionService = $app->make( 'App\Interfaces\ArchivematicaConnectionServiceInterface' );
                return new ArchivematicaDashboardClientService( $connectionService,
                    ArchivematicaDashboardClientService::TRANSFER_TYPE_STANDARD);
            });

            $this->app->bind('App\Interfaces\TransferPacketBuilder', function( $app ) {
                return new \App\Services\AMTransferPacketBuilder($app);
            });

        } else {
            throw new \Exception("APP_TRANSFER_PACKET_TYPE is not properly set '{$packetType}'.".
                "Valid options are AM_ZIPPED_BAG and AM_STANDARD");
        }


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot( )
    {
    }

}
