<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Aip;
use Illuminate\Http\Request;
use App\Interfaces\ArchivalStorageInterface;
use App\FileObject;
use Log;

class AipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */

    public function show( Request $request )
    {
        $aip = Aip::find( $request->aipId );
        return response( $aip );
    }

    public function filename( Request $request )
    {
        $aip = Aip::find( $request->aipId );
        return response ( $aip->fileObjects->first()->filename );
    }

    public function filenameFromDipId( Request $request )
    {
        $dip = \App\Dip::find ($request->dipId );
        $aip = $dip->storage_properties->aip;
        return response ( $aip->fileObjects->first()->filename );
    }


    public function fileDownload(ArchivalStorageInterface $storage, Request $request)
    {
        $aip = Aip::find($request->aipId);
        $file = $aip->fileObjects()->findOrFail($request->fileId);

        return response()->streamDownload(function () use( $storage, $aip, $file ) {
            echo $storage->stream( $aip->online_storage_location, $file->fullpath );
        }, basename( $file->path ), [
            "Content-Type" => $file->mime_type,
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }

    public function download(ArchivalStorageInterface $storage, Request $request)
    {
        $aip = Aip::find($request->aipId);
        $file = $aip->fileObjects->first();

        return response()->streamDownload(function () use( $storage, $aip, $file ) {
            echo $storage->stream( $aip->online_storage_location, $file->fullpath );
        }, basename( $file->path ), [
            "Content-Type" => "application/x-tar",
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }

    public function downloadFromDipId(ArchivalStorageInterface $storage, Request $request)
    {
        $dip = \App\Dip::find( $request->dipId );
        $aip = $dip->storage_properties->aip;
        $file = $aip->fileObjects->first();

        return response()->streamDownload(function () use( $storage, $aip, $file ) {
            echo $storage->stream( $aip->online_storage_location, $file->fullpath );
        }, basename( $file->path ), [
            "Content-Type" => "application/x-tar",
            "Content-Disposition" => "attachment; { $file->filename }"
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function edit(Aip $aip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aip $aip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Aip  $aip
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aip $aip)
    {
        //
    }
}
