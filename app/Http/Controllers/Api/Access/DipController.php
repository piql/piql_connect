<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\DipResource;
use App\Http\Resources\FileObjectResource;
use App\Dip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Interfaces\ArchivalStorageInterface;
use App\FileObject;
use App\Bag;

class DipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index( Request $request )
    {
        return \App\Http\Resources\DipResource::collection(
            Dip::with(['storage_properties', 'storage_properties.bag'])
                ->latest()->paginate( env('DEFAULT_ENTRIES_PER_PAGE') )
        );
    }

    public function package_thumbnail( Request $request, ArchivalStorageInterface $storage )
    {
        $dip = Dip::find( $request->dipId );
        $file = $dip->fileObjects->filter( function ($file, $key) {
            return Str::contains( $file->fullpath, '/thumbnails' );
        })->first();

        return response($storage->stream( $dip->storage_location, $file->fullpath ))
            ->header("Content-Type" , "image/jpeg");
    }

    public function package_preview( Request $request, ArchivalStorageInterface $storage )
    {
        $dip = Dip::find( $request->dipId );
        $file = $dip->fileObjects->filter( function ($file, $key) {
            return Str::contains( $file->fullpath, '/objects' );
        })->first();

        return response($storage->stream( $dip->storage_location, $file->fullpath ))
            ->header("Content-Type" , "image/jpeg");
    }

    public function files( Request $request )
    {
        $dip = Dip::find( $request->dipId );
        $files = $dip->fileObjects()
                     ->where( 'path', 'LIKE', "%/objects" )
                     ->paginate( env('DEFAULT_ENTRIES_PER_PAGE') );

        return FileObjectResource::collection( $files );
    }

    public function file_preview( Request $request, ArchivalStorageInterface $storage )
    {
        $dip = Dip::find( $request->dipId );
        $file = $dip->fileObjects->find( $request->fileId );

        return response($storage->stream( $dip->storage_location, $file->fullpath ))
            ->header("Content-Type" , "image/jpeg");
    }

    public function file_download( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::find( $request->dipId );
        $file = $dip->fileObjects->find( $request->fileId );

        return response()->streamDownload( function () use( $storage, $dip, $file ) {
            echo $storage->stream( $dip->storage_location, $file->fullpath );
        }, basename( $file->path ), [
            "Content-Type" => "application/octet-stream",
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }

    public function file_thumbnail( ArchivalStorageInterface $storage, Request $request )
    {
        $dip = Dip::find( $request->dipId );
        $file = $dip->fileObjects->find( $request->fileId );
        $thumbnail = $dip->fileObjects->filter( function ($thumb, $key) use( $file ) {
                return Str::contains( $thumb->path, '/thumbnails' );
        })->filter( function ($thumb, $key) use ( $file ) {
            return Str::contains( $file->filename, pathinfo( $thumb->filename, PATHINFO_FILENAME ) );
        })->first();


        return response($storage->stream( $dip->storage_location, $thumbnail->fullpath ))
            ->header("Content-Type" , "image/jpeg");
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, ArchivalStorageInterface $storage )
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function edit(Dip $dip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dip $dip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dip  $dip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dip $dip)
    {
        //
    }
}
