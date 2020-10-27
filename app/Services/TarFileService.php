<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use splitbrain\PHPArchive\Tar;
use Log;

class TarFileService implements \App\Interfaces\FileCollectorInterface
{

    public function __construct( $app )
    {
    }

    public function collectDirectory( string $sourceDirectoryPath, string $destinationFilePath ) : bool
    {
        try {
            $tar = new Tar();
            $tar->create( $destinationFilePath );
            $this->buildFromDirectory($tar, $sourceDirectoryPath );
        } catch ( \Exception $ex ) {
            Log::error( "Failed to collect files from {$sourceDirectoryPath} into tarball {$destinationFilePath}: {$ex->getMessage()}" );
            throw $ex;
        }
        return true;
    }

    public function collectMultipleFiles( array $sourceFilePaths, string $destinationFilePath, bool $deleteWhenCollected ) : bool
    {
        try {
            $tar = new Tar();
            $tar->create( $destinationFilePath );
            $this->buildFromIterator($tar, new \ArrayIterator($sourceFilePaths) );
        } catch ( \Exception $ex ) {
            Log::error( "Failed to collect the files into tarball {$destinationFilePath}: {$ex->getMessage()}" );
            throw $ex;
        }

        if( $deleteWhenCollected ) {
            foreach( $sourceFilePaths as $sourceFilePath ) {
                if ( !unlink($sourceFilePath) ) {
                    Log::warning("Failed to delete file");
                }
            }
        }

        return true;
    }

    private function buildFromIterator(&$tar, $array) {
        foreach ($array as $fileName=>$file) {
            if (is_file($file)){
                $tar->addFile($file, $fileName);
            }
        }
    }

    private function buildFromDirectory(&$tar, $dir) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $dirLen = strlen($dir);
        if (substr($dir, -1) != '/') {
            $dirLen++;
        }
        foreach ($rii as $file) {
            if ($file->isFile()){
                $path = $file->getPathname();
                $tar->addFile($path, substr($path, $dirLen - strlen($path)));
            }
        }
    }
}
