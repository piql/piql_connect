<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\AccountMetadata;
use App\Collection;
use App\ArchiveMetadata;
use App\File;
use App\Http\Resources\CollectionResource;
use App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Webpatser\Uuid\Uuid;
use Log;

class AccountCollectionController extends Controller
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
     * @param Account $account
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Account $account)
    {
        // TODO: setup guards
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return CollectionResource::collection( Collection::paginate( $limit ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param File $file
     * @return void
,    */
    public function store(Request $request, Account $account)
    {
        $validated = $this->validateRequest($request);
        $additional = [
            "modified_by" => Auth::id(),
            "account_uuid" => $account->uuid
        ];

        $total = array_merge( $validated, $additional );
        $collection = Collection::create( $total );

        return new CollectionResource( $collection );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Account $account
     * @param \App\Collection $collection
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, Collection $collection)
    {
        return response()->json([ "data" => new CollectionResource($collection)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Account $account
     * @param \App\Collection $collection
     */
    public function update(Request $request, Account $account, Collection $collection)
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
     * @param \App\Account $account
     * @return \Illuminate\Http\Response
     */

    public function upsert( Request $request, Account $account )
    {
        if( $account->id != auth()->user()->organization->account->id ) {
            //TODO: Admin access
            abort( 403, "User with id ".auth()->id()." does not have the neccesary permissions to update collections for this account" );
        }

        $validatedRequest = $this->validate( $request, [
            "title" => "string",
            "description" => "string|nullable",
            "defaultMetadataTemplate" => "array|nullable"
        ]);

        $data = array_merge( $validatedRequest, ["account_uuid" => $account->uuid] );

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
     * @param \App\Account $account
     * @param \App\Collection $collection
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account, Collection $collection)
    {
        return response( "Collection cannot be deleted in this version", 405 );
    }

}
