<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchivalStorageService implements \App\Interfaces\ArchivalStorageInterface
{
    private $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

    private function s3ConfigDto( \App\S3Configuration $config )
    {
        $param = [
            'endpoint' => $config->url,
            'key' => $config->key_id,
            'secret' => $config->secret,
            'region' => $config->region ?? " ",
            'bucket' => $config->bucket ?? " ",
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true
        ];
        return $param;
    }

    private function getDriverFromConfig( $locatable )
    {
        $configDto = $this->s3ConfigDto( $locatable );
        $fsm = new \Illuminate\Filesystem\FilesystemManager( $this->app );
        return $fsm->createS3Driver( $configDto );
    }

    public function ls( \App\StorageLocation $storage, string $storagePath = "", bool $recursive = false )
    {
        $driver = $this->getDriverFromConfig( $storage->locatable );
        return $recursive
            ? $driver->allFiles( $storagePath )
            : $driver->files( $storagePath );
    }

    public function upload( \App\StorageLocation $storage, string $storagePath, string $localPath )
    {
        $driver = $this->getDriverFromConfig( $storage->locatable );
        $file = new \Illuminate\Http\File( $localPath );
        return $driver->putFileAs( $storagePath, $file, pathinfo( $localPath, PATHINFO_BASENAME ) );
    }

    public function download( \App\StorageLocation $storage, string $storagePath, string $destinationPath )
    {
        $driver = $this->getDriverFromConfig( $storage->locatable );
        $downloadFileContents = $driver->get( $storagePath );
        $filename = pathinfo( $storagePath, PATHINFO_BASENAME );
        $localFilenameWithDir = Str::finish( $destinationPath, "/" ) . $filename;
        Storage::disk('local')->put( $localFilenameWithDir, $downloadFileContents );
        return $localFilenameWithDir;
    }

    public function delete( \App\StorageLocation $storage, string $remotePathToDelete)
    {
        $driver = $this->getDriverFromConfig( $storage->locatable );
        return $driver->delete( $remotePathToDelete );
    }
}
