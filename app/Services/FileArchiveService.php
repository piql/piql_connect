<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Aip;
use App\FileObject;
use App\Interfaces\FileCollectorInterface;
use Log;

class FileArchiveService implements \App\Interfaces\FileArchiveInterface
{
    private $storage;
    private $outgoing;
    private $collector;

    public function __construct( $app, $storage, $collector )
    {
        $this->storage = $storage;
        $this->outgoing = Storage::disk( 'outgoing' );
        $this->collector = $collector;
    }

    private function destinationPath( Aip $aip, string $prefix = "" )
    {
       return $this->outgoing->path( "{$prefix}{$aip->external_uuid}" );
    }

    public function buildTarFromAip( Aip $aip, $prefix = null ) : string
    {
        $prefix = ($prefix == null) ? Str::uuid()."-" : $prefix;

        $downloadpath = $this->downloadAipFiles( $aip, $prefix );
        $destinationFilePath = "{$this->destinationPath( $aip, $prefix )}.tar";

        $this->collector->collectDirectory( $downloadpath, $destinationFilePath );

        try {
            $this->outgoing->deleteDirectory( $aip->external_uuid );
        } catch ( \Exception $ex ) {
            Log::warn( "Failed to clean up temporary files from aip {$aip->external_uuid}: {$ex}" );
        }

        return $destinationFilePath;
    }

    public function buildTarFromAipIncrementally( Aip $aip, $prefix = null ) : string
    {
        $prefix = ($prefix == null) ? Str::uuid()."-" : $prefix;
        $destinationFilePath = "{$this->destinationPath( $aip, $prefix )}.tar";
        $aip->fileObjects->map( function ( $file ) use ( $aip, $destinationFilePath, $prefix ) {
            printf("source: {$file->path}");
            $downloadedFileSourcePath = $this->downloadFile( $aip, $file, $prefix );
            $downloadedFileCollectionPath = Str::after( $file->fullpath, "{$aip->online_storage_path}/" );
            $this->collector->collectSingleFile(
                $downloadedFileSourcePath,
                $downloadedFileCollectionPath,
                $destinationFilePath,
                true
            );
        });

        try {
            /* Current implementation leaves a bunch of empty directories behind that we need to delete */
            $this->outgoing->deleteDirectory( "{$prefix}{$aip->external_uuid}" );
        } catch ( \Exception $ex ) {
            Log::warn( "Failed to clean up temporary directories from aip {$aip->external_uuid}: {$ex}" );
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

    public function downloadFile( Aip $aip, FileObject $file, $prefix = "" ) : string
    {
        $relativePath = "{$prefix}{$aip->external_uuid}/{$this->stripAipOnlinePath( $file->fullpath, $aip )}";
        return $this->storage->download( $aip->online_storage_location, $file->fullpath, $relativePath );
    }

    public function downloadAipFiles( Aip $aip, $prefix = "" ) : string
    {
        $aip->fileObjects->map( function ( $file ) use ( $aip, $prefix ) {
            return $this->downloadFile( $aip, $file, $prefix );
        });

        return $this->destinationPath( $aip, $prefix );
    }

}
