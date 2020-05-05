<?php


namespace App\Services;

use App\Interfaces\MetadataGeneratorInterface;

class MetadataGeneratorService implements MetadataGeneratorInterface
{
    public function createMetadataWriter(array $parameter)
    {
        if($parameter['type'] == 'json')
            return new DublinCoreJsonMetadataWriter($parameter);
        else
            return new DublinCoreCSVMetadataWriter($parameter);
    }
}
