<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\ArchivalStorageInterface;
use App\Dip;
use App\FileObject;

class FilePreviewRenderHelper {
    private $dip;
    private $file;
    private $filePath;
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

    public function __construct(ArchivalStorageInterface $storage=null, Dip $dip=null, FileObject $file=null) {
        $this->storage = $storage;
        $this->dip = $dip;
        $this->file = $file;
    }
    
    public function setFile($filePath) {
    	$this->file = new \stdClass();
    	$this->file->fullpath = $filePath;
    	$this->file->mime_type = mime_content_type($filePath);
    }

    public function getContent($forThumb=false) {
        $pathInfo = pathinfo($this->file->fullpath);
        $ext = strtolower($pathInfo['extension']);
        if ($ext == 'pdf') {
            return $this->getPdfContent();
        } elseif (self::isPreviwableFile($this->file->mime_type, $forThumb)) {
            return $this->getRegularContent();
        } else {
            $path = self::getCustomIcon($ext);
            $this->mimeType = \File::mimeType($path);
            return \File::get($path);
        }
    }
    
    private function getRegularContent() {
    	if ($this->dip != null) {
    		return $this->storage->stream( $this->dip->storage_location, $this->file->fullpath );
    	} elseif ($this->file != null) {
    		return file_get_contents($this->file->fullpath);
    	}
    }
    
    private function getPdfContent() {
        $file = 'tmp/'.md5($this->file->fullpath).'.pdf';
        if ($this->dip != null) {
        	Storage::disk('local')->put($file, $this->storage->stream( $this->dip->storage_location, $this->file->fullpath ));
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
    
    public static function getPreviwableFileArr($forThumb=false) {
        $extArr = array('image');
        if (!$forThumb) {
            $extArr = array_merge($extArr, array('audio', 'video'));
        }
        return $extArr;
    }
    
    public static function isPreviwableFile($mimeType, $forThumb=false) {
        $mimeTypeArr = explode('/', $mimeType);
        foreach ($mimeTypeArr as $mimeTypeTmp) {
            if (in_array($mimeTypeTmp, self::getPreviwableFileArr($forThumb))) {
                return true;
            }
        }
        return false;
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
    
    public static function getCustomIcon($ext) {
        if (array_key_exists($ext, self::ICON_EXT_MAP)) {
            $ext = self::ICON_EXT_MAP[$ext];
        }
        $path = resource_path() . '/images/file_icon/icon_'.$ext.'.png';
        if(!\File::exists($path)) {
            $path = resource_path() . '/images/file_icon/icon_piql.png';
        }
        return $path;
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
