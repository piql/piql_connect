<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\ArchiveMetadata;
use App\Collection;
use App\CollectionMetadata;
use App\File;
use App\Http\Resources\CollectionResource;
use App\Archive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Webpatser\Uuid\Uuid;
use Log;

class ArchiveCollectionController extends Controller
{
    private function validateRequest(Request $request) {
        return $request->validate([
            "title" => "string|nullable",
            "description" => "string|nullable",
            "defaultMetadataTemplate" => "array|nullable",
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Archive $archive
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Archive $archive)
    {
        // TODO: setup guards
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return CollectionResource::collection( Collection::paginate( $limit ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Archive $archive
     * @return void
,    */
    public function store(Request $request, Archive $archive)
    {
        $validated = $this->validateRequest($request);
        $additional = [
            "modified_by" => Auth::id(),
            "archive_uuid" => $archive->uuid
        ];

        $total = array_merge( $validated, $additional );
        $collection = Collection::create( $total );

        return new CollectionResource( $collection );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Archive $archive
     * @param \App\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Archive $archive, Collection $collection)
    {
        return response()->json([ "data" => new CollectionResource($collection)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Archive $archive
     * @param \App\Collection $collection
     */
    public function update(Request $request, Archive $archive, Collection $collection)
    {
       $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "defaultMetadataTemplate" => "array|nullable"
            ]
        );
        $collection->update( $request->all() );
        return new CollectionResource( $collection );
    }

    /**
     * Update or create the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Archive $archive
     * @return \Illuminate\Http\Response
     */

    public function upsert( Request $request, Archive $archive )
    {
        if( $archive->id != auth()->user()->organization->archive->id ) {
            //TODO: Admin access
            abort( 403, "User with id ".auth()->id()." does not have the neccesary permissions to update collections for this archive" );
        }

        $validatedRequest = $this->validate( $request, [
            "title" => "string",
            "description" => "string|nullable",
            "defaultMetadataTemplate" => "array|nullable"
        ]);

        $data = array_merge( $validatedRequest, ["archive_uuid" => $archive->uuid] );

        if( isset($request->id) ) {
            $collection = Collection::findOrFail( $request->id );
            $collection->update( $data );
            return new CollectionResource( $collection );
        }

        return new CollectionResource( Collection::create( $data ) );
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Archive $archive
     * @param \App\Collection $collection
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Archive $archive, Collection $collection)
    {
        return response( "Collection cannot be deleted in this version", 405 );
    }

}
