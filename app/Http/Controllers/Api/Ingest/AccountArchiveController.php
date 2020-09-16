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
            "metadata" => "array|nullable",
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
     * @param Account $ac
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
        return response( "Archives cannot be deleted in this version", 405 );
    }

    public function test_given_an_authenticated_user_when_getting_an_archives_they_have_metadata()
    {
        $response = $this->actingAs( $this->user )
            ->get( route('api.ingest.account.archive.index', [$this->account->id]) );
        $response->assertStatus( 200 );
        $decoded = collect( json_decode( $response->getContent() )->data )->firstWhere('id', $this->archive->id);
        dump($decoded->metadata);
        $this->assertNotNull( $decoded->metadata );
    }

    public function test_given_an_authenticated_user_when_updating_metadata_it_responds_with_updated_data()
    {
        $metadata = [
            "metadata" => ["dc" => ["title" => "The best novel ever!"]]
        ];

        $response = $this->actingAs( $this->user )
            ->put( route('api.ingest.account.archive.update', [$this->account->id, $this->archive->id]),
               $metadata );

        $response->assertStatus( 200 )
                 ->assertJsonFragment( $metadata->metadata );
    }

}
