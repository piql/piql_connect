<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\Archive;
use App\File;
use App\Holding;
use App\HoldingMetadata;
use App\Account;
use App\Http\Resources\HoldingResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Illuminate\Support\Facades\Validator;

class AccountArchiveHoldingController extends Controller
{
    /**
     * Display a listing of Holdings for the current Archive
     *
     * @param Request $request
     * @param Account $account
     * @param Archive $archive
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Account $account, Archive $archive)
    {
        $holdings = $archive->holdings();
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return HoldingResource::collection( $holdings->paginate( $limit ) );

    }

    /**
     * Persist a holding
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param Archive $archive
     * @param Holding $holding
     *
     * @param File $file
     * @return void
     */
    public function store(Request $request, Account $account, Archive $archive)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding'
        ]);

        if( $validator->fails() )
        {
            $message = $validator->errors();
            return response($message, 422);
        }
        $data = [
            'title' => $request->title,
            'owner_archive_uuid' => $request->owner_archive_uuid,
            'description' => $request->description ?? '',
        ];

        $lhs = $request->lhs;
        $rhs = $request->rhs;

        $holding = new Holding(
            array_merge($data, [
                "owner_archive_uuid" => $archive->uuid,
            ]));
        $holding->setOwnerArchiveUuidAttribute($archive->uuid);
        $holding->save();

        return new HoldingResource( $holding );
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @param Archive $archive
     * @param Holding $holding
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, Archive $archive, Holding $holding)
    {
        return new HoldingResource( $holding );
    }

    /**
     * Update the Holding in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param Archive $archive
     * @param Holding $holding
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, Archive $archive, Holding $holding)
    {
        $validData = $request->validate( [
            'title' => 'required|string|max:100',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding'
        ]);


        $holding->update($validData);
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
        abort(409, "Holdings cannot be deleted in this version");
    }
}
