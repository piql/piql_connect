<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Aip;
use Illuminate\Http\Request;
use App\Interfaces\ArchivalStorageInterface;
use App\FileObject;

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
    public function show(ArchivalStorageInterface $storage, Request $request)
    {
        $aip = Aip::find($request->aipId);
        $file = $aip->fileObjects->first();

        return response()->streamDownload(function () use( $storage, $aip, $file ) {
            echo $storage->stream( $aip->online_storage_location, $file->path );
        }, basename( $file->path ), [
            "Content-Type" => "application/x-tar",
            "Content-Disposition" => "attachment; { basename( $file->path ) }"

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
