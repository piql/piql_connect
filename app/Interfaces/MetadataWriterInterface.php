<?php


namespace App\Interfaces;


interface MetadataWriterInterface
{
    public function write(array $parameter) : bool;
}
