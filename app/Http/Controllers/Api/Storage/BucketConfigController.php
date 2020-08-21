<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FilePreviewRenderHelper;
use App\Job;
use Log;

class BucketConfigController extends Controller {

	private function getPath() {
		$job = new Job();
		$path = $job->visualFilesDir() . '/';
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		return $path;
	}
	
    public function upload(Request $request) {
    	$file = $request->file('qqfile');
    	$path = $this->getPath() . $file->getClientOriginalName();
    	if (file_exists($path)) {
    		unlink($path);
    	}
    	$file->move($this->getPath(), $file->getClientOriginalName());
        return response()->json(['success' => true]);
    }

    public function showFiles() {
		$fileArr = array();
		$path = $this->getPath();
		if (file_exists($path)) {
			foreach (scandir($path) as $fileTmp) {
				$file = $path . $fileTmp;
				if (is_file($file)) {
					$fileArr[] = array('name'=>pathinfo($file, PATHINFO_BASENAME), 'size'=>filesize($file), 'type'=>mime_content_type($file));
				}
			}
		}
		return $fileArr;
	}
	
	public function removeFile($name) {
		unlink($this->getPath() . $name);
		return 'true';
	}
	
	public function showFile($name) {
		$filePath = $this->getPath() . $name;
		$filePreviewRenderHelper = new FilePreviewRenderHelper();
		$filePreviewRenderHelper->setFile($filePath);
        return response($filePreviewRenderHelper->getContent(true))
        ->header("Content-Type" , $filePreviewRenderHelper->getMimeType());
	}
}
