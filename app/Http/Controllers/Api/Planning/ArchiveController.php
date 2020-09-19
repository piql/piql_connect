<?php

namespace App\Http\Controllers\Api\Planning;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Archive;
use App\Http\Resources\ArchiveResource;
use App\Http\Resources\ArchiveCollection;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ArchiveCollection(Archive::all());
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
        $data = [
            'title' => $request->title,
            'description' => $request->description
        ];

        if( $request->filled( 'uuid' ) ) {
            $data += ['uuid' => $request->uuid ] ;
        }

        $archive = Archive::create( $data )->refresh();
        return new ArchiveResource( $archive );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $archive = Archive::find( $id );
        if( !isset( $archive ) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Archive with id '.$id.' was found.'], 404) );
        }
        return new ArchiveResource( $archive );
    }
    
    public function showByUuid($uuid)
    {
    	$archive = Archive::query()->where('uuid', $uuid)->first();
    	return $archive ? new ArchiveResource($archive) : null;
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $archive = Archive::find($id);
        if(!isset($archive) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Archive with id '.$id.' was found.'], 404) );
        }
        $archive->delete();
        return response()->json([], 204 );
    }
}
