<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\FilePreviewInterface;
use App\Job;
use Log;

class BucketConfigController extends Controller {

    private function getPath($jobId) {
        $job = Job::find($jobId);
        $path = $job->visualFilesDir() . '/';
        return $path;
    }

    public function upload(Request $request, $jobId) {
        $file = storage_path('uploader/bucketImg/'.$request->result["name"]);
        $path = $this->getPath($jobId) . $request->fileName;
        if (file_exists($path)) {
            unlink($path);
        }
        rename($file, $path);
        return response()->json(['success' => true]);
    }

    public function showFiles($jobId) {
        $fileArr = array();
        $path = $this->getPath($jobId);
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

    public function removeFile($jobId, $name) {
        unlink($this->getPath($jobId) . $name);
        return 'true';
    }

    public function showFile($jobId, $name, FilePreviewInterface $filePreview) {
        $filePath = $this->getPath($jobId) . $name;
        $filePreview->file($filePath);
        return response($filePreview->getContent(true))
        ->header("Content-Type" , $filePreview->getMimeType());
    }
}
