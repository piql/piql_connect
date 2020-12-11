<?php

namespace App\Http\Controllers\Api\Metadata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HoldingResource;
use App\Holding;
use App\Collection;

class CollectionHoldingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request, Collection $collection )
    {
        $holdings = $collection->holdings;
        return HoldingResource::collection( $holdings );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort( 403, "Forbidden: Users cannot create Holdings" );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort( 403, "Forbidden: Users cannot persist Holdings" );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request, Collection $collection, Holding $holding )
    {
        return new HoldingResource( $holding );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort( 403, "Forbidden: Users cannot edit Holdings" );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort( 403, "Forbidden: Users cannot update Holdings" );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort( 403, "Forbidden: Users cannot delete Holdings" );
    }
}
