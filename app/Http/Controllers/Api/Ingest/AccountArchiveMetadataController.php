<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Account;
use App\Archive;
use App\ArchiveMetadata;
use App\Http\Resources\MetadataResource;
use App\AccountMetadata;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountArchiveMetadataController extends Controller
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
    public function index(Request $request, Account $account, Archive $archive)
    {
        $metadata = $archive->metadata();
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
    public function store(Request $request, Account $account, Archive $archive)
    {
        $requestData = $this->validateRequest($request);

        if($archive->metadata()->count() > 0) {
//            $archive->metadata()->delete(); //Should softdelete/deactivate existing templates
        }

        $metadata = new ArchiveMetadata([
            "modified_by" => Auth::user()->id,
            "metadata" => $requestData['metadata'] ?? [],
        ]);

        $metadata->owner()->associate($account->owner());
        $metadata->parent()->associate($archive);
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
    public function show(Account $account, Archive $archive, ArchiveMetadata $metadata)
    {
        if($archive->id !== $metadata->parent_id) {
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
    public function update(Request $request, Account $account, Archive $archive, ArchiveMetadata $metadata)
    {
        if($archive->id !== $metadata->parent_id) {
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
    public function destroy(Account $account, Archive $archive, ArchiveMetadata $metadata)
    {
        if($archive->id !== $metadata->parent_id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid metadata' ], 404 ) );
        }

        $metadata->parent()->dissociate();
        $metadata->owner()->dissociate();
        $metadata->delete();
        return response( "", 204);
    }
}
