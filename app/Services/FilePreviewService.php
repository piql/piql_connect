<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\ArchivalStorageInterface;
use App\Dip;
use App\FileObject;
use Log;

class FilePreviewService implements \App\Interfaces\FilePreviewInterface {
    private $app;
    private $dip;
    private $file;
    private $filePath;
    private $mimeType;
	private $extMap;
    
    public function __construct( $app ) {
    	$this->app = $app;
    }
    
    public function storage(ArchivalStorageInterface $storage) {
    	$this->storage = $storage;
    	return $this;
    }
    
    public function dip(Dip $dip) {
    	$this->dip = $dip;
    	return $this;
    }
    
    public function fileObject(FileObject $file) {
    	$this->file = $file;
    	return $this;
    }
    
    public function file($filePath) {
    	$this->file = new \stdClass();
    	$this->file->fullpath = $filePath;
    	$this->file->mime_type = mime_content_type($filePath);
    	return $this;
    }
    
    public function getContent($forThumb=false) {
    	$this->mimeType = $this->getMimeTypeFromFileName($this->file->fullpath);
    	if ($this->mimeType == 'application/pdf') {
    		return $this->getPdfContent();
    	} elseif ($this->isPreviwableFile($this->file->mime_type, $forThumb)) {
    		return $this->getRegularContent();
    	} else {
    		$path = $this->getCustomIcon($this->file->fullpath);
    		return \File::get($path);
    	}
    }
    
    private function getRegularContent() {
    	if ($this->dip != null) {
                return $this->storage->downloadContent( $this->dip->storage_location, $this->file->fullpath );
    	} elseif ($this->file != null) {
    		return file_get_contents($this->file->fullpath);
    	}
    }
    
    private function getPdfContent() {
    	$file = 'tmp/'.md5($this->file->fullpath).'.pdf';
    	if ($this->dip != null) {
                Storage::disk('local')->put($file, $this->storage->downloadContent( $this->dip->storage_location, $this->file->fullpath ));
    	} elseif ($this->file != null) {
    		Storage::disk('local')->put($file, file_get_contents($this->file->fullpath));
    	}
    	$im = new \Imagick(Storage::path($file).'[0]');
    	$im->setImageFormat('jpg');
    	$im->setBackgroundColor('white');
    	$im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
    	$im->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
    	$im->borderImage('black', 3, 3);
    	Storage::delete($file);
    	return $im;
    }
    
    public function getPreviwableFileArr($forThumb=false) {
    	$extArr = array('image');
    	if (!$forThumb) {
    		$extArr = array_merge($extArr, array('audio', 'video'));
    	}
    	return $extArr;
    }
    
    public function isPreviwableFile($mimeType, $forThumb=false) {
    	$mimeTypeArr = explode('/', $mimeType);
    	foreach ($mimeTypeArr as $mimeTypeTmp) {
    		if (in_array($mimeTypeTmp, $this->getPreviwableFileArr($forThumb))) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function getCustomIcon($fileName) {
		$mimeType = $this->getMimeTypeFromFileName($fileName);
    	$mimeTmp = preg_replace("/[^[:alnum:]]/u", '_', $mimeType);
    	$path = resource_path() . '/images/file_icon/icon_'.$mimeTmp.'.png';
    	if(!\File::exists($path)) {
    		$path = resource_path() . '/images/file_icon/icon_piql.png';
    	}
    	return $path;
    }
    
    private function getMimeTypeFromFileName($fileName) {
		if (!$this->extMap) {
			$this->extMap = array();
			if ($mimeFile = fopen('/etc/mime.types', 'r')) {
				while (($line = fgets($mimeFile)) !== false) {
					$line = fgets($mimeFile);
					$line = preg_replace("/\\t+/", "\t", str_replace("\n", '', $line));
					$lineArr = explode("\t", $line);
					if ($line && substr($line, 0, 1) != '#' && array_key_exists(1, $lineArr)) {
						foreach (explode(' ', $lineArr[1]) as $ext) {
							$this->extMap[$ext] = $lineArr[0];
						};
					}
				}
			}
			fclose($mimeFile);
		}
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		return array_key_exists($ext, $this->extMap) ? $this->extMap[$ext] : null;
    }
    
    public function getMimeType() {
    	return $this->mimeType;
    }
}
