<?php


namespace App\Services;

use App\Interfaces\MetadataGeneratorInterface;

class MetadataGeneratorService implements MetadataGeneratorInterface
{
    public function createMetadataWriter(array $parameter)
    {
        $packetType = env( 'APP_TRANSFER_PACKET_TYPE', "AM_ZIPPED_BAG");

        if($packetType == "AM_ZIPPED_BAG") {
            $parameter = array_merge(["filenameColumnPreFix" => "data/"], $parameter);
        } else if($packetType == "AM_STANDARD") {
            $parameter = array_merge(["filenameColumnPreFix" => "objects/"], $parameter);
        }

        if($parameter['type'] == 'json')
            return new DublinCoreJsonMetadataWriter($parameter);
        else
            return new DublinCoreCSVMetadataWriter($parameter);
    }
}
