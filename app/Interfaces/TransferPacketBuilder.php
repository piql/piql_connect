<?php


namespace App\Interfaces;


interface TransferPacketBuilder
{
    public function addFile($filePathSource, $filePathDestination) : void;
    public function addMetadataFile($filePathSource, $filePathDestination) : void;
    public function build($outputFile) : bool;
    public function errorMessage() : string;
}
