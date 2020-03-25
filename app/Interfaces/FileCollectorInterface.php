<?php

namespace App\Interfaces;

interface FileCollectorInterface
{

/* collectDirectory
 * $sourceDirectoryPath An absolute path to a directory to collect
 * $destinationFilePath An absolute path to the file where collection/archive is stored
 * @return bool True if successful, otherwise false.
 */

    public function collectDirectory( string $sourceDirectoryPath, string $destinationFilePath ) : bool; 
}
