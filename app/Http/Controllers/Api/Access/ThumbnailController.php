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
        $file = FileObject::where([
            'storable_id' => $dip->id, 
            'storable_type' => "App\Dip"])
            ->where('path', 'LIKE', "%thumbnails%" )
            ->where('path', 'LIKE', "%.jpg" )
            ->first();

        return response()->stream( function () use( $dip, $file ) {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
            fpassthru($stream);
        })->header("Content-Type" , "image/jpeg");
    }

    public function testPreview( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::latest()->first();
        $file = FileObject::where([
            'storable_id' => $dip->id, 
            'storable_type' => "App\Dip"])
            ->where('path', 'LIKE', "%objects%" )
            ->where('path', 'LIKE', "%.jpg" )
            ->first();

        return response()->stream( function () use( $dip, $file ) {
            $stream = $storage->downloadStream( $dip->storage_location, $file->fullpath );
            fpassthru($stream);
        })->header("Content-Type" , "image/jpeg");
    }

}
