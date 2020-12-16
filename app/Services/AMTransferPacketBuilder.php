<?php


namespace App\Services;


use App\Interfaces\TransferPacketBuilder;
use BagitUtil;

class AMTransferPacketBuilder implements TransferPacketBuilder
{
    private $inputFiles = [];
    private $errorMessage;

    public function __construct()
    {
    }

    public function addFile($filePathSource, $filePathDestination): void
    {
        $this->inputFiles[] = (object)["source" =>$filePathSource, "destination" => $filePathDestination];
    }

    public function addMetadataFile($filePathSource, $filePathDestination): void
    {
        $this->inputFiles[] = (object)["source" =>$filePathSource, "destination" => 'metadata/' . $filePathDestination];
    }

    public function build($outputFile): bool
    {
        // Create output directory
        if(!$this->createOutputDirectory($outputFile)) {
            return false;
        }

        foreach ($this->inputFiles as $file) {
            if (!file_exists($file->source)) {
                $this->errorMessage = "Failed to add file to bag. Source file don't exist: '{$file->source}'";
                return false;
            }

            $destination = $outputFile."/".$file->destination;

            $dirname = dirname($destination);
            if (! is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }

            // copy file to destination
            if(!copy($file->source, $destination)){
                $this->errorMessage = 'Failed to add file to bag. Source=' . $file->source . ' destination=' . $destination;
                return false;
            }
        }

        return true;

    }

    public function errorMessage(): string
    {
        return $this->errorMessage;
    }

    private function createOutputDirectory($directory)
    {
        // test destination
        if(file_exists($directory)) {
            $this->errorMessage = "'{$directory}' already exists. Can't continue";
            return false;
        }

        if (!mkdir($directory))
        {
            $this->errorMessage = "Failed creating directory '{$directory}'";
            return false;
        }
        return true;
    }

}
