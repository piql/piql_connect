<?php

namespace App\Interfaces;

interface FilesystemDriverInterface
{
    public function createDriver( string $type, array $config );
}
