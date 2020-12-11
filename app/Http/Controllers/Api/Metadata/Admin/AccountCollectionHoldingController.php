<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\Collection;
use App\File;
use App\Holding;
use App\HoldingMetadata;
use App\Account;
use App\Http\Resources\HoldingResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Illuminate\Support\Facades\Validator;

class AccountCollectionHoldingController extends Controller
{
    /**
     * Display a listing of Holdings for the current Collection
     *
     * @param Request $request
     * @param Account $account
     * @param Collection $collection
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Account $account, Collection $collection)
    {
        $holdings = $collection->holdings();
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return HoldingResource::collection( $holdings->paginate( $limit ) );

    }

    /**
     * Persist a holding
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param Collection $collection
     * @param Holding $holding
     *
     * @param File $file
     * @return void
     */
    public function store(Request $request, Account $account, Collection $collection)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding',
            'defaultMetadataTemplate' => 'array|nullable',
        ]);

        $holding = Holding::create(
            array_merge($validated, [
                "collection_uuid" => $collection->uuid,
            ])
        );

        return new HoldingResource( $holding );
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @param Collection $collection
     * @param Holding $holding
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account, Collection $collection, Holding $holding)
    {
        return new HoldingResource( $holding );
    }

    /**
     * Update the Holding in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param Collection $collection
     * @param Holding $holding
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account, Collection $collection, Holding $holding)
    {
        $validated = $request->validate( [
            'title' => 'required|string|max:100',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding',
            'defaultMetadataTemplate' => 'array|nullable'
        ]);

        $holding->update( $validated );
        return new HoldingResource( $holding );
    }

    /**
     * Create or update a Holding.
     *
     * @param \Illuminate\Http\Request $request
     * @param Account $account
     * @param Collection $collection
     * @param Holding $holding
     * @return \Illuminate\Http\Response
     */
    public function upsert(Request $request, Account $account, Collection $collection, Holding $holding)
    {
        $validated = $request->validate( [
            'title' => 'required|string|max:100',
            'description' => 'string|max:500',
            'lhs' => 'int|exists:holding',
            'rhs' => 'int|exists:holding',
            'defaultMetadataTemplate' => 'array|nullable'
        ]);

        if( $request->id ) {
            $holding = Holding::findOrFail( $request->id );
            $holding->update( $validated );
            return new HoldingResource( $holding );
        }

        $data = array_merge( $validated, [ 'collection_uuid' => $collection->uuid ] );

        return new HoldingResource( Holding::create( $data ) );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Account $account
     * @param \App\Collection $collection
     * @param \App\Holding $holding
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Account $account, Collection $collection, Holding $holding)
    {
        abort(409, "Holdings cannot be deleted in this version");
    }
}
