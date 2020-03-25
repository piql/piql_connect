<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Aip;
use App\FileObject;
use Log;

class FileArchiveService implements \App\Interfaces\FileArchiveInterface
{
    private $storage;
    private $outgoing;

    public function __construct( $app, $storage )
    {
        $this->storage = $storage;
        $this->outgoing = Storage::disk( 'outgoing' );
    }

    private function destinationPath( Aip $aip )
    {
       return $this->outgoing->path( $aip->external_uuid );
    }

    public function buildTarFromAip( Aip $aip ) : string
    {
        $downloadpath = $this->downloadAipFiles( $aip );
        $destinationFilePath = "{$this->destinationPath( $aip )}.tar";

        try {
            $tar = new \PharData( $destinationFilePath );
            $tar->buildFromDirectory( $downloadpath );
        } catch ( \Exception $ex ) {
            Log::error( "Failed to build tar from aip {$aip->external_uuid}: {$ex}" );
            throw $ex;
        }

        try {
            $this->outgoing->deleteDirectory( $aip->external_uuid );
        } catch ( \Exception $ex ) {
            Log::warn( "Failed to clean up temporary files from aip {$aip->external_uuid}: {$ex}" );
        }

        return $destinationFilePath;
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

    private static function stripAipOnlinePath( string $filepath, Aip $aip ) : string
    {
        return Str::after( $filepath, "{$aip->online_storage_path}/" );
    }

    public function downloadFile( Aip $aip, FileObject $file ) : string
    {
        $relativePath = "{$aip->external_uuid}/{$this->stripAipOnlinePath( $file->fullpath, $aip )}";
        $this->outgoing->makeDirectory( $aip->external_uuid );
        return $this->storage->download( $aip->online_storage_location, $file->fullpath, $relativePath );
    }

    public function downloadAipFiles( Aip $aip ) : string
    {
        $location = $aip->online_storage_location;
        $destinationPath = $this->destinationPath( $aip );

        $aip->fileObjects->map( function ( $file ) use ( $aip ) {
            return $this->downloadFile( $aip, $file );
        });

        return $destinationPath;
    }

}
