<?php

namespace App\Interfaces;

interface MetsParserInterface
{
    public function parseDublinCoreFields( string $mets ) : array;
    public function findOriginalFileName( string $mets, $fileId );
}
