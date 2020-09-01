<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Archive;
use App\File;
use App\Holding;
use App\HoldingMetadata;
use App\Account;
use App\Http\Resources\HoldingResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

class AccountArchiveHoldingController extends Controller
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
     * @param Archive $archive
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Account $account, Archive $archive)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        if($account->owner_id !== auth()->user()->id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid account' ], 404 ) );
        }

        $holdings = $archive->holdings();

        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return HoldingResource::collection( $holdings->paginate( $limit ) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param File $file
     * @return void
     */
    public function store(Request $request, Account $account, Archive $archive)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        if($account->owner_id !== auth()->user()->id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid account' ], 404 ) );
        }

        $requestData = $this->validateRequest($request);
        $holding = new Holding(
            array_merge($requestData, [
                "owner_archive_uuid" => $archive->uuid,
            ]));
        $holding->setOwnerArchiveUuidAttribute($archive->uuid);
        $holding->save();

        $metadata = new HoldingMetadata([
            "modified_by" => auth()->user()->id,
            "metadata" => ["dc" => (object)null]
        ]);
        $metadata->parent()->associate($holding);
        $metadata->save();

        return response()->json([ "data" => new HoldingResource($holding)]);
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, Archive $archive, Holding $holding)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        if($account->owner_id !== auth()->user()->id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid account' ], 404 ) );
        }

        return response()->json([ "data" => new HoldingResource($holding)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, Archive $archive, Holding $holding)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        if($account->owner_id !== auth()->user()->id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid account' ], 404 ) );
        }

        $requestData = $this->validateRequest($request);
        $holding->update($requestData);
        $holding->save();
        return response()->json([ "data" => new HoldingResource($holding)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account, Archive $archive, Holding $holding)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        if($account->owner_id !== auth()->user()->id) {
            abort( response()->json([ 'error' => 404, 'message' => 'Invalid account' ], 404 ) );
        }

        $holding->delete();
        return response()->json([ "data" => new HoldingResource(null)]);
    }
}
