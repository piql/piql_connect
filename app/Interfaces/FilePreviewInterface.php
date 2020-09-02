<?php

namespace App\Interfaces;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ArchivalStorageInterface;
use App\Dip;
use App\FileObject;

interface FilePreviewInterface
{
	public function storage(ArchivalStorageInterface $storage);
	public function dip(Dip $dip);
	public function fileObject(FileObject $file);
	public function getContent(boolean $forThumb);
	public function getMimeType();
	public function isPreviwableFile(string $mimeType, $forThumb=false);
	public function getCustomIcon($fileName);
}
