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

/* collectMultipleFiles
 * $sourceFilePaths A list of files. The array index is the destination path in the archive and value is an absolute path to the source file on disk
 * $destinationFilePath An absolute path to the file where collection/archive is stored
 * $deleteWhenCollected If true, the file is deleted after collection (use for temporary files)
 * @return bool True if successful, otherwise false.
 */

    public function collectMultipleFiles( array $sourceFilePaths, string $destinationFilePath, bool $deleteWhenCollected ) : bool;
}
