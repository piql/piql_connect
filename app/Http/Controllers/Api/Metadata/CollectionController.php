<?php

namespace App\Http\Controllers\Api\Metadata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Collection;
use App\Http\Resources\CollectionResource;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //TODO: Limit access to Archives per Account
        return CollectionResource::collection( Collection::all() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort( 403, "Forbidden: Users cannot persist Archives in this version" );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //TODO: Limit access to Collections per Account
        $collection = Collection::find( $id );
        if( !isset( $collection ) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Collection with id '.$id.' was found.'], 404) );
        }
        return new CollectionResource( $collection );
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
        abort( 403, "Forbidden: Users cannot edit Collections in this version" );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort( 403, "Forbidden: Users cannot delete Collections" );
    }
}
