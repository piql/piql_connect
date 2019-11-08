<?php

namespace App\Interfaces;

interface ArchivalStorageInterface
{
    public function ls( \App\StorageLocation $storage, string $storagePath = "" ); /* Query the storage location for contents, optional relative path */
    public function upload( \App\StorageLocation $storage, string $storagePath, string $localPath ); /* Upload a file to the storage location */
    public function download( \App\StorageLocation $storage, string $storagePath, string $destinationPath ); /* Download a file from the storage location */
    public function delete( \App\StorageLocation $storage, string $storagePath ); /* Delete a file from the storage location */
}
