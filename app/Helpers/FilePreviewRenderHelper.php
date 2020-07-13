<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\ArchivalStorageInterface;
use App\Dip;
use App\FileObject;
use Log;

class FilePreviewRenderHelper
{
	private $dip;
	private $file;
	private $mimeType = "image/jpeg";
	
	public function __construct(ArchivalStorageInterface $storage, Dip $dip, FileObject $file) {
		$this->storage = $storage;
		$this->dip = $dip;
		$this->file = $file;
	}
	
	public function getContent() {
		$pathInfo = pathinfo($this->file->fullpath);
		switch (strtolower($pathInfo['extension'])) {
			case 'pdf':
				return $this->getPdfContent();
			default:
				return $this->getRegularContent();
		}
		
	}
	
	private function getRegularContent() {
		return $this->storage->stream( $this->dip->storage_location, $this->file->fullpath );
	}
	
	private function getPdfContent() {
		$file = 'tmp/'.md5($this->file->fullpath).'.pdf';
		Storage::disk('local')->put($file, $this->storage->stream( $this->dip->storage_location, $this->file->fullpath ));
		$im = new \Imagick(Storage::path($file).'[0]');
		$im->setImageFormat('jpg');
		$im->setBackgroundColor('white');
		$im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
		$im->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
		$im->borderImage('black', 3, 3);
		Storage::delete($file);
		return $im;
	}
	
	public function getMimeType() {
		return $this->mimeType;
	}
}
?>