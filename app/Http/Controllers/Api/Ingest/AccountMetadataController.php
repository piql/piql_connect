<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Account;
use App\Http\Resources\MetadataResource;
use App\AccountMetadata;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class AccountMetadataController extends Controller
{
    private function validateRequest(Request $request) {
        return $request->validate([
            "metadata.dc.title" => "string|nullable",
            "metadata.dc.creator" => "string|nullable",
            "metadata.dc.subject" => "string|nullable",
            "metadata.dc.description" => "string|nullable",
            "metadata.dc.publisher" => "string|nullable",
            "metadata.dc.contributor" => "string|nullable",
            "metadata.dc.date" => "string|nullable",
            "metadata.dc.type" => "string|nullable",
            "metadata.dc.format" => "string|nullable",
            "metadata.dc.identifier" => "string|nullable",
            "metadata.dc.source" => "string|nullable",
            "metadata.dc.language" => "string|nullable",
            "metadata.dc.relation" => "string|nullable",
            "metadata.dc.coverage" => "string|nullable",
            "metadata.dc.rights" => "string|nullable",
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
        $metadata = $account->metadata();
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return MetadataResource::collection( $metadata->paginate( $limit ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @return void
     * @throws \Exception
     */
    public function store(Request $request, Account $account)
    {
        $requestData = $this->validateRequest($request);

        if(!isset($requestData->dc))
        {
            $requestData =["dc" => (object)null];
        }

        $metadata = new AccountMetadata([
            "uuid" => Uuid::generate()->string,
            "modified_by" => Auth::user()->id,
            "metadata" => $requestData,
        ]);
        $metadata->owner()->associate($account->owner());
        $metadata->parent()->associate($account);
        $metadata->save();

        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @param AccountMetadata $metadata
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, AccountMetadata $metadata)
    {
        if($account->id !== $metadata->parent_id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid metadata' ], 404 ) );
        }
        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param AccountMetadata $metadata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, AccountMetadata $metadata)
    {
        if($account->id !== $metadata->parent_id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid metadata' ], 404 ) );
        }

        $requestData = $this->validateRequest($request);
        if(isset($requestData['metadata'])) {
            $metadata->metadata = $requestData['metadata'] + $metadata->metadata;
            $metadata->save();
        }

        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @param AccountMetadata $metadata
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account, AccountMetadata $metadata)
    {
        if($account->id !== $metadata->parent_id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid metadata' ], 404 ) );
        }

        $metadata->parent()->dissociate();
        $metadata->owner()->dissociate();
        $metadata->delete();
        return response()->json([ "data" => new MetadataResource(null)]);
    }
}
