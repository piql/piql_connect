<?php

namespace App\Interfaces;

interface MetsParserInterface
{
   public function parseDublinCoreFields( string $mets ) : array;
}
