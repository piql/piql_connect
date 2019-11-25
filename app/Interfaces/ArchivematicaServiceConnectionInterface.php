<?php

namespace App\Interfaces;

interface ArchivematicaServiceConnectionInterface
{
    public function doRequest($method, $uri, array $options = []);
}
