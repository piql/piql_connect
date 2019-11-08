<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\StorageLocationResource;
use App\Http\Resources\StorageLocationCollection;
use App\StorageLocation;

class StorageLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storageLocations = StorageLocation::where('owner_id', Auth::id() )
            ->paginate( env("DEFAULT_ENTRIES_PER_PAGE") );
        return new StorageLocationCollection($storageLocations);
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
        $validator = Validator::make( $request->all(), [
            'locatable_id' => 'required|int|exists:storage_locations'
        ]);

        if( $validator->fails() ) {
            return response()->json( ['errors' => [
                'status' => 422,
                'details' => $validator->errors()
            ]], 422 );
        }

        $created = StorageLocation::create([
            'owner_id' => Auth::id(),
            'locatable_id' => $request->locatable_id,
            'locatable_type' => $request->locatable_type,
            'storable_type' => $request->storable_type
        ]);
        return new StorageLocationResource( $created );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $storageLocation = StorageLocation::find( $id );
        return new StorageLocationResource( $storageLocation );
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
        //
    }

}
