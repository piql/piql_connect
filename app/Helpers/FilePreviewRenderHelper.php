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
	private const ICON_EXT_MAP = array(
			'docx'=>'doc',
			'xlsx'=>'xls',
			'ppsx'=>'pps',
			'pptx'=>'ppt',
			'jpeg'=>'jpg',
			'mpeg'=>'mpg',
			'tar.gz'=>'tgz',
			'gz'=>'tgz',
			'html'=>'htm');
	private static $extList;
	
	public function __construct(ArchivalStorageInterface $storage, Dip $dip, FileObject $file) {
		$this->storage = $storage;
		$this->dip = $dip;
		$this->file = $file;
	}
	
	public function getContent() {
		$pathInfo = pathinfo($this->file->fullpath);
		$ext = strtolower($pathInfo['extension']);
		switch ($ext) {
			case 'pdf':
				return $this->getPdfContent();
			case 'png':
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'tif':
			case 'tiff':
			case 'bmp':
			case 'ico':
				return $this->getRegularContent();
			default:
				return $this->getCustonIcon($ext);
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
	
	public static function getPreviwableFileArr() {
		return array('png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'bmp', 'ico');
	}
	
	public static function isPreviwableFile($file) {
		$pathInfo = pathinfo($file);
		$ext = strtolower($pathInfo['extension']);
		return in_array($ext, self::getPreviwableFileArr());
	}
	
	public static function isIconableFile($file) {
		$pathInfo = pathinfo($file);
		$ext = strtolower($pathInfo['extension']);
		return in_array($ext, self::getAllExtArr());
	}
	
	public static function getAllExtArr() {
		if (!self::$extList) {
			$retArr = array();
			foreach (self::ICON_EXT_MAP as $key=>$value) {
				$retArr[] = $key;
			}
			$fileArr = scandir(resource_path() . '/images/file_icon/');
			foreach ($fileArr as $file) {
				$matches = array();
				preg_match('/icon_([a-z0-9]+)\\.png/', $file, $matches);
				if (count($matches) == 2) {
					$retArr[] = $matches[1];
				}
			}
			self::$extList = $retArr;
		}
		return self::$extList;
	}
	
	private function getCustonIcon($ext) {
		if (array_key_exists($ext, self::ICON_EXT_MAP)) {
			$ext = self::ICON_EXT_MAP[$ext];
		}
		$path = resource_path() . '/images/file_icon/icon_'.$ext.'.png';
		if(!\File::exists($path)) {
			$path = resource_path() . '/images/file_icon/icon_piql.png';
		}
		$this->mimeType = \File::mimeType($path);
		return \File::get($path);
	}
	
	public function getMimeType() {
		return $this->mimeType;
	}
}
?>
