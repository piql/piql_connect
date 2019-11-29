<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
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

    public function index()
    {
        return Dip::with(['storage_properties','storage_properties.bag'])->latest()->paginate(5);
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
