<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\FilesystemAdapter;
use Illuminate\Support\Str;
use Log;

class ArchivalStorageService implements \App\Interfaces\ArchivalStorageInterface
{
    private $app;
    private $filesystem;
    private $destinationDisk;

    public function __construct( $app, \App\Interfaces\FilesystemDriverInterface $filesystem, FilesystemAdapter $destinationDisk = null )
    {
        $this->app = $app;
        $this->filesystem = $filesystem;
        $this->destinationDisk = $destinationDisk ?? Storage::disk('outgoing');
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

    private function getDriverFromConfig( \App\StorageLocation $storageLocation )
    {
        if( $storageLocation->locatableType() == "s3configuration" )
        {
            $configDto = $this->s3ConfigDto( $storageLocation->locatable );
            return $this->filesystem->createDriver( "S3", $configDto );
        }
        if( $storageLocation->locatable_type == "fakestorage" )
        {
            return Storage::disk('testdata');
        }

        throw new \Exception("Unsupported filesystem type requested: {$storageLocation->locatableType() }");
    }

    public function ls( \App\StorageLocation $storage, string $storagePath = "", bool $recursive = false )
    {
        $driver = $this->getDriverFromConfig( $storage );
        return $recursive
            ? $driver->allFiles( $storagePath )
            : $driver->files( $storagePath );
    }

    public function upload( \App\StorageLocation $storage, string $storagePath, string $localPath )
    {
        $driver = $this->getDriverFromConfig( $storage );
        $file = new \Illuminate\Http\File( $localPath );
        return $driver->putFileAs( $storagePath, $file, pathinfo( $localPath, PATHINFO_BASENAME ) );
    }

    public function download( \App\StorageLocation $storage, string $storagePath, string $destinationPath )
    {
        $driver = $this->getDriverFromConfig( $storage );
        $downloadFileContents = $driver->get( $storagePath );
        $this->destinationDisk->put( $destinationPath, $downloadFileContents );
        return $this->destinationDisk->path( $destinationPath );
    }

    public function downloadContent( \App\StorageLocation $storage, string $storagePath )
    {
        $driver = $this->getDriverFromConfig( $storage );
        return $driver->get( $storagePath );
    }

    public function downloadStream( \App\StorageLocation $storage, string $storagePath )
    {
        // todo: Refactor this function
        if( $storage->locatableType() == "s3configuration" )
        {
            $adapter = Storage::disk('s3')->getAdapter();
            $client = $adapter->getClient();
            $client->registerStreamWrapper();
            $context = stream_context_create(["s3" => ["seekable" => true]]);
            if (($stream = fopen("s3://{$adapter->getBucket()}/{$storagePath}", "rb", false, $context)))
            {
                fseek($stream, 0, SEEK_SET);
            }
            return $stream;
        }
        if( $storage->locatable_type == "fakestorage" )
        {
            return fopen(Storage::disk('testdata')->path($storagePath), "rb", false);
        }

        throw new \Exception("Unsupported filesystem type requested: {$storage->locatableType() }");
    }

    public function delete( \App\StorageLocation $storage, string $remotePathToDelete)
    {
        $driver = $this->getDriverFromConfig( $storage );
        return $driver->delete( $remotePathToDelete );
    }
}
