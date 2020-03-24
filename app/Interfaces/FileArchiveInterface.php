<?php

namespace App\Interfaces;

interface FileArchiveInterface
{
    public function buildTarFromAip( \App\Aip $aip ): string;
    public function getAipFileNames( \App\Aip $aip ): array;
    public function downloadFile( \App\Aip $aip, \App\FileObject $file ): string;
}
