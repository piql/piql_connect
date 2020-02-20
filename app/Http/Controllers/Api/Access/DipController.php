<?php

namespace App\Http\Controllers\Api\Access;

use App\Http\Controllers\Controller;
use App\Http\Resources\DipResource;
use App\Http\Resources\FileObjectResource;
use App\Dip;
use http\Env\Response;
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
        $q = Dip::query();
        $terms = collect(explode(" ", $request->query('search')));
        $archiveUuid = $request->query('archive');
        $holdingTitle = $request->query('holding');

        if($archiveUuid) {
            $q->whereHas('storage_properties',
                function ($storage_property) use ($archiveUuid, $holdingTitle) {
                    $storage_property->where('archive_uuid', $archiveUuid);
                    if($holdingTitle) {
                        $storage_property->where('holding_name', $holdingTitle);
                    }
                });
        }

        if($terms->count() == 1) {
            return DipResource::collection(
                $q->whereHas('storage_properties.bag',
                function( $bag ) use( $terms ) {
                    $bag->where('name', 'LIKE', "%{$terms->first()}%");
                })->paginate( env('DEFAULT_ENTRIES_PER_PAGE') )
            );
        } elseif( $terms->count() > 1) {
                $terms->each( function ($term, $key) use ($q) {
                        $q->whereHas('storage_properties.bag',
                            function( $bag ) use ($term) {
                                $bag->where('name','LIKE',"%{$term}%");
                            });
                });
                return DipResource::collection(
                    $q->paginate( env('DEFAULT_ENTRIES_PER_PAGE') )
                );
        }
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

    public function aipFile( Request $request )
    {
        $dipFileObject = FileObject::find( $request->fileId );
        $dip = Dip::find( $request->dipId );

        if(!preg_match('/(\/objects\/.*)(\w{8}-\w{4}-\w{4}-\w{4}-\w{12}-(.*)?(\..*))$/', $dipFileObject->fullpath, $matches, PREG_OFFSET_CAPTURE)){
            return response();
        }
        $aipFileObjectPath = $matches[1][0].$matches[3][0];

        $files = $dip->storage_properties->aip->fileObjects()->where('fullpath', 'LIKE', "%".$aipFileObjectPath."%")->get();
        if(count($files))
            return FileObjectResource::collection( $files );

        return response();
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
