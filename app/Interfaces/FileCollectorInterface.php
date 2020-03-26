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

/* collectSingleFile
 * $sourceFilePath An absolute path to a file to collect
 * $collectionPath The path of the file inside the destination collection/archive
 * $destinationFilePath An absolute path to the file where collection/archive is stored
 * $deleteWhenCollected If true, the file is deleted after collection (use for temporary files)
 * @return bool True if successful, otherwise false.
 */


    public function collectSingleFile( string $sourceFilePath, string $collectionpath, string $destinationFilePath, bool $deleteWhenCollected ) : bool;
}
