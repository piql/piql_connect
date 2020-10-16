<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Log;

class TarFileService implements \App\Interfaces\FileCollectorInterface
{

    public function __construct( $app )
    {
    }

    public function collectDirectory( string $sourceDirectoryPath, string $destinationFilePath ) : bool
    {
        try {
            $tar = new \PharData( $destinationFilePath );
            $tar->buildFromDirectory( $sourceDirectoryPath );
        } catch ( \Exception $ex ) {
            Log::error( "Failed to collect files from {$sourceDirectoryPath} into tarball {$destinationFilePath}: {$ex->getMessage()}" );
            throw $ex;
        }
        return true;
    }


    public function collectSingleFile( string $sourceFilePath, string $collectionPath, string $destinationFilePath, bool $deleteWhenCollected ) : bool
    {
        try {
            $tar = new \PharData( $destinationFilePath );
            $tar->addFile( $sourceFilePath, $collectionPath );
            if( $deleteWhenCollected ) {
                unlink( $sourceFilePath );
            }
        } catch ( \Exception $ex ) {
            Log::error( "Failed to collect the file {$sourceFilePath} into tarball {$destinationFilePath}: {$ex->getMessage()}" );
            throw $ex;
        }
        return true;
    }

    public function collectMultipleFiles( array $sourceFilePaths, string $destinationFilePath, bool $deleteWhenCollected ) : bool
    {
        try {
            $tar = new \PharData( $destinationFilePath );
            $tar->buildFromIterator( new \ArrayIterator($sourceFilePaths) );
            if( $deleteWhenCollected ) {
                foreach($sourceFilePaths as $sourceFilePath) {
                    unlink( $sourceFilePath );
                }
            }
        } catch ( \Exception $ex ) {
            Log::error( "Failed to collect the files into tarball {$destinationFilePath}: {$ex->getMessage()}" );
            throw $ex;
        }
        return true;
    }
}
