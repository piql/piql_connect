<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\ArchivalStorageInterface;
use App\Dip;
use App\FileObject;
use Log;

class FilePreviewRender extends Model
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
		$file = sys_get_temp_dir().'/'.base64_encode($this->file->fullpath).'.pdf';
		file_put_contents($file, $this->storage->stream( $this->dip->storage_location, $this->file->fullpath ));
		$im = new \Imagick($file.'[0]');
		$im->setImageFormat('jpg');
		unlink($file);
		return $im;
	}
	
	public function getMimeType() {
		return $this->mimeType;
	}
}
?>