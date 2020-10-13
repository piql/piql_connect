<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\AccountMetadata;
use App\Archive;
use App\ArchiveMetadata;
use App\File;
use App\Http\Resources\ArchiveResource;
use App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Webpatser\Uuid\Uuid;
use Log;

class AccountArchiveController extends Controller
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
        return ArchiveResource::collection( Archive::paginate( $limit ) );
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
        $archive = Archive::create( $total );

        return new ArchiveResource( $archive );
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, Archive $archive)
    {
        return response()->json([ "data" => new ArchiveResource($archive)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Account $acccount
     * @param \App\Archive $archive
     */
    public function update(Request $request, Account $account, Archive $archive)
    {
       $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "defaultMetadataTemplate" => "array|nullable"
            ]
        );

        $archive->update( $request->all() );
        return new ArchiveResource( $archive );
    }

    /**
     * Update or create the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @return \Illuminate\Http\Response
     */

    public function upsert( Request $request, Account $account )
    {
        if( $account->id != auth()->user()->account->id ) {
            //TODO: Admin access
            abort( 403, "User with id ".auth()->id()." does not have the neccesary permissions to update archives for this account" );
        }

        $validatedRequest = $this->validate( $request, [
            "title" => "string",
            "description" => "string|nullable",
            "defaultMetadataTemplate" => "array|nullable"
        ]);

        $data = array_merge( $validatedRequest, ["account_uuid" => $account->uuid] );

        if( isset($request->id) ) {
            $archive = Archive::findOrFail( $request->id );
            $archive->update( $data );
            return new ArchiveResource( $archive );
        }

        return new ArchiveResource( Archive::create( $data ) );
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account, Archive $archive)
    {
        return response( "Archives cannot be deleted in this version", 405 );
    }

}
