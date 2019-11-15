<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FilesystemDriverService implements \App\Interfaces\FilesystemDriverInterface
{
    private $fsm;

    public function __construct( $app )
    {
        $this->fsm = new \Illuminate\Filesystem\FilesystemManager( $app );
    }


    public function createDriver( string $type, array $config )
    {
        if( empty( $type ) )
        {
            throw new \Exception("A filesystem type must be provided. Currently supported: S3 and local");
        }
        else if( $type == "S3" )
        {
            return $this->fsm->createS3Driver( $config );
        }
        else if( $type == "local" )
        {
            return $this->fsm->createLocalDriver( $config );
        }
        throw new \Exception("Unsupported filesystem driver {$type}");
    }
}
