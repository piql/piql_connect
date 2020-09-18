<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Http\Resources\AccountResource;
use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;

class AccountController extends Controller
{
    /**
     * Display a paginated listing of Accounts
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return AccountResource::collection( Account::paginate( $limit ) );
    }

    /**
     * Validate and persist a new Account.
     *
     * @param \Illuminate\Http\Request $request title, description (optional), metadata (optional)
     * @return \Illuminate\Http\Response of AccountResource
     */
    public function store(Request $request )
    {
        $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "metadata" => "array|nullable"
            ]
        );
        if( array_key_exists( "metadata", $validatedRequest ) ) {
            $validatedRequest["defaultMetadataTemplate"] = $validatedRequest["metadata"]; //TODO: Fix api
            unset($validatedRequest["metadata"]);
        }
        $account = Account::create( $validatedRequest );
        return new AccountResource( $account );
    }

    /**
     * Display a specified Account
     *
     * @param Account $account
     * @return \Illuminate\Http\Response of AccountResource
     */
    public function show(Account $account)
    {
        return new AccountResource( $account );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Account $account )
    {
        $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "metadata" => "array|nullable"
            ]
        );
        $account->update( $validatedRequest );
        return new AccountResource( $account );
    }

    /**
     * In this version, deleting accounts is not supported due to consistency issues
     *
     * @param Account $account
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account)
    {
        return response( "Accounts cannot be deleted in this version", 405);
    }
}
