<?php

namespace App\Http\Controllers\Api\Access;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FileObject;
use App\Dip;
use App\Interfaces\ArchivalStorageInterface;

class ThumbnailController extends Controller
{

    public function testThumbnail( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::latest()->first();
        if ($dip === null) {
            Log::error("Failed to find dip");
            return response([
                "message" => "Failed to find dip"
            ], 400);
        }
        $file = FileObject::where([
            'storable_id' => $dip->id, 
            'storable_type' => "App\Dip"])
            ->where('path', 'LIKE', "%thumbnails%" )
            ->where('path', 'LIKE', "%.jpg" )
            ->first();
        if ($file === null) {
            Log::error("Failed to find file");
            return response([
                "message" => "Failed to find file"
            ], 400);
        }

        try {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
        } catch (\Exception $e) {
            Log::error("Failed to download thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download thumbnail"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read thumbnail for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read thumbnail"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        })->header("Content-Type" , "image/jpeg");
    }

    public function testPreview( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::latest()->first();
        if ($dip === null) {
            Log::error("Failed to find dip");
            return response([
                "message" => "Failed to find dip"
            ], 400);
        }
        $file = FileObject::where([
            'storable_id' => $dip->id, 
            'storable_type' => "App\Dip"])
            ->where('path', 'LIKE', "%objects%" )
            ->where('path', 'LIKE', "%.jpg" )
            ->first();
        if ($file === null) {
            Log::error("Failed to find file");
            return response([
                "message" => "Failed to find file"
            ], 400);
        }

        try {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
        } catch (\Exception $e) {
            Log::error("Failed to download preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to download preview"
            ], 400);
        }
        if (!is_resource($stream)) {
            Log::error("Failed to read preview for file '{$file->fullpath}'");
            return response([
                "message" => "Failed to read preview"
            ], 400);
        }
        return response()->stream( function () use( $stream ) {
            fpassthru($stream);
            fclose($stream);
        })->header("Content-Type" , "image/jpeg");
    }

}
