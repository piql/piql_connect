<?php

namespace App\Http\Controllers\Api\Ingest;

use App\AccountMetadata;
use App\Archive;
use App\ArchiveMetadata;
use App\File;
use App\Http\Resources\ArchiveResource;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Webpatser\Uuid\Uuid;

class AccountArchiveController extends Controller
{
    private function validateRequest(Request $request) {
        return $request->validate([
            "title" => "string|nullable",
            "description" => "string|nullable",
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
        $archives = Account::with('archives')->first()->archives()->with('metadata');
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return ArchiveResource::collection( $archives->paginate( $limit ) );
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
        $requestData = $this->validateRequest($request);
        $archive = new Archive(
            array_merge($requestData, [
                "account_uuid" => $account->uuid,
            ]));
        $archive->save();

        $metadata = new ArchiveMetadata([
            "modified_by" => auth()->user()->id,
            "metadata" => ["dc" => (object)null]
        ]);
        $metadata->parent()->associate($archive);
        $metadata->save();

        return response()->json([ "data" => new ArchiveResource($archive)]);
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
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, Archive $archive)
    {
        $requestData = $this->validateRequest( $request );
        $archive->update( $requestData );
        return new ArchiveResource( $archive );
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
        $archive->delete();
        return response( "", 204);
    }
}
