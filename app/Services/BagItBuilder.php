<?php


namespace App\Services;


use App\Interfaces\TransferPacketBuilder;
use BagitUtil;

class BagItBuilder implements TransferPacketBuilder
{
    private $util;

    public function __construct()
    {
        $this->util = new BagitUtil();
    }

    public function addFile($filePathSource, $filePathDestination): void
    {
        $this->util->addFile($filePathSource, $filePathDestination);
    }

    public function addMetadataFile($filePathSource, $filePathDestination): void
    {
        $this->util->addMetadataFile($filePathSource, $filePathDestination);
    }

    public function build($outputFile): bool
    {
        return $this->util->createBag($outputFile);
    }

    public function errorMessage(): string
    {
        return $this->util->errorMessage();
    }
}
