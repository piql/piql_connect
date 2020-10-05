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
        $dip = Dip::findOrFail($request->dipId);
        $fileObj = $dip->fileObjects->find($request->fileId);
        try {
            $stream = $storage->downloadStream($dip->storage_location, $fileObj->fullpath);
        } catch (\Exception $e) {
            Log::error("Failed to download preview for file '{$fileObj->fullpath}'");
            return response([
                "message" => "Failed to download preview"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for file '{$fileObj->fullpath}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        $streamHelper = new VideoStreamHelper($stream);
        $streamHelper->start();
        return null;
    }

    public function thumb($fileName, FilePreviewInterface $filePreview) {
        try {
            $stream = $filePreview->getCustomIcon($fileName);
        } catch (\Exception $e) {
            Log::error("Failed to read thumbnail for file '{$fileName}'");
            return response([
                "message" => "Failed to read thumbnail"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read thumbnail for file '{$fileName}'");
            return response([
                "message" => "Failed to read thumbnail"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        }, 200, ["Content-Type" , $filePreview->getMimeType()]);
    }
}
