<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Helpers\VideoStreamHelper;
use App\Dip;
use App\Interfaces\ArchivalStorageInterface;
use App\Interfaces\FilePreviewInterface;
use Log;

class MediaController extends Controller {
    public function showDipFile(Request $request, ArchivalStorageInterface $storage) {
        $dip = Dip::find($request->dipId);
        $fileObj = $dip->fileObjects->find($request->fileId);
        $streamResource = $storage->downloadStream($dip->storage_location, $fileObj->fullpath);
        $stream = new VideoStreamHelper($streamResource);
        $stream->start();
        return null;
    }

    public function thumb($fileName, FilePreviewInterface $filePreview) {
        $icon = $filePreview->getCustomIcon($fileName);
        return response()->stream( function () use( $icon ) {
            fpassthru($icon);
        }, 200, ["Content-Type" , $filePreview->getMimeType()]);
    }
}
