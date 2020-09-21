<?php

namespace App\Http\Controllers\Api\Ingest;

use App\AccountMetadata;
use App\File;
use App\Http\Resources\AccountResource;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;

class AccountController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // todo: the selection needs to be modified when roles and groups gets fully implemented
        $metadata = \auth()->user()->morphMany( 'App\Account','owner');

        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return AccountResource::collection( $metadata->paginate( $limit ) );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param File $file
     * @return void
     */
    public function store(Request $request)
    {
        $requestData = $this->validateRequest($request);
        $account = new Account(
            array_merge($requestData, ["uuid" => Uuid::generate()->string])
        );

        // todo: the account needs to be  associated with either user or group
        $account->owner()->associate(auth()->user());
        $account->save();

        $metadata = new AccountMetadata([
            "modified_by" => auth()->user()->id,
            "metadata" => ["dc" => (object)null]
        ]);
        $metadata->parent()->associate($account);
        $metadata->save();

        return response()->json([ "data" => new AccountResource($account)]);
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return response()->json([ "data" => new AccountResource($account)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $requestData = $this->validateRequest($request);
        $account->update($requestData);
        $account->save();
        return response()->json([ "data" => new AccountResource($account)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account)
    {
        $account->delete();
        return response( "", 204);
    }
}
