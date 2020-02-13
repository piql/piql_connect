<?php


namespace App\Services;

use App\Interfaces\MetadataGeneratorInterface;

class MetadataGeneratorService implements MetadataGeneratorInterface
{
    public function createMetadataWriter(array $parameter)
    {
        return new DublinCoreMetadataWriter($parameter);
    }
}
