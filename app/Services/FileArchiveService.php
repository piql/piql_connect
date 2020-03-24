<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Aip;
use App\FileObject;

class FileArchiveService implements \App\Interfaces\FileArchiveInterface
{
    private $storage;
    private $outgoing;

    public function __construct( $app, $storage, $outgoing = null )
    {
        $this->storage = $storage;
        $this->outgoing = $outgoing ?? Storage::disk( 'outgoing' );
    }

    private function destinationPath( Aip $aip )
    {
       return $this->outgoing->path( $aip->external_uuid );
    }

    public function buildTarFromAip( Aip $aip ) : string
    {
        $storedFiles = $this->getAipFileNames( $aip );
        return $this->destinationPath( $aip );
    }
        
    public function getAipFileNames( Aip $aip ) : array
    {
        $objectPath = $aip->online_storage_path;
        $storedFiles =
            $aip->fileObjects
                   ->pluck( 'fullpath' )
                   ->map( function( $path, $key ) use( $objectPath ) { 
                       return Str::after( $path, $objectPath."/" );
                    });

        return $storedFiles->toArray();
    }

    public function downloadFile( Aip $aip, FileObject $file ) : string
    {
        $destinationPath = $this->destinationPath( $aip );
        $this->outgoing->makeDirectory( $destinationPath );
        $location = $aip->online_storage_location;

        return $this->storage->download( $location, $file->fullpath, $destinationPath );
    }

    public function downloadAipFiles( Aip $aip ) : string
    {
        $location = $aip->online_storage_location;
        $destinationPath = $this->destinationPath( $aip );
        $this->outgoing->makeDirectory( $destinationPath );

        $aip->fileObjects->map( function ( $file ) use ( $aip ) {
            return $this->downloadFile( $aip, $file );
        });

        return $destinationPath;
    }

}
