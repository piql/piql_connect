<?php

namespace App\Http\Controllers\Api\Planning;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Holding;
use App\Http\Resources\HoldingResource;
use App\Http\Resources\HoldingCollection;

class HoldingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new HoldingCollection(Holding::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description
        ];

        if( $request->filled( 'uuid' ) ) {
            $data += ['uuid' => $request->uuid ] ;
        }

        $holding = Holding::create( $data )->refresh();
        return new HoldingResource( $holding );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $holding = Holding::find( $id );
        if( !isset( $holding ) ) {
            return response()->json(['error' => 'HOLDING WITH ID '.$id.' NOT FOUND'], 404);
        }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holding = Holding::find($id);
        if(!isset($holding) ) {
            return response()->json(['error' => 'HOLDING WITH ID '.$id.' NOT FOUND'], 404);
        }
        $holding->delete();
        return response()->json([], 204 );
    }
}
