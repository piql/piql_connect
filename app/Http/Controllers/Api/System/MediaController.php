<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Helpers\VideoStreamHelper;
use App\Dip;
use App\Interfaces\ArchivalStorageInterface;
use App\Helpers\FilePreviewRenderHelper;
use Log;

class MediaController extends Controller {
    public function show() {
        $file = storage_path('custom-files/evn.mp4');
        $stream = new VideoStreamHelper($file);
        $stream->start();
        return null;
    }
    public function showDipFile(Request $request, ArchivalStorageInterface $storage) {
        $dipId = $request->dipId;
        $fileId = $request->fileId;
        $filePath = 'tmp/'.$dipId.'-'.$fileId.'-'.md5($dipId.'-'.$fileId).'.media.tmp';
        $fileFullPath = Storage::path($filePath);
        if (!file_exists($fileFullPath)) {
            $dip = Dip::find( $dipId );
            $fileObj = $dip->fileObjects->find($fileId);
            Storage::disk('local')->put($filePath, $storage->stream($dip->storage_location, $fileObj->fullpath));
        }
        $stream = new VideoStreamHelper($fileFullPath);
        $stream->start();
        return null;
    }
    public function thumb($fileName) {
        $pathInfo = pathinfo($fileName);
        $iconPath = FilePreviewRenderHelper::getCustomIcon(strtolower($pathInfo['extension']));
        return response(\File::get($iconPath))->header("Content-Type" , \File::mimeType($iconPath));
    }
}
