<?php

namespace App\Http\Controllers\Api\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\RetrievalCollectionResource;
use App\Http\Resources\RetrievalCollectionResourceCollection;
use App\Http\Resources\RetrievaFileResource;
use App\Http\Resources\FileCollection;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\RetrievalCollection;
use App\RetrievalFile;

class RetrievalCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $retrievalCollection = RetrievalCollection::latest()->get();
        return new RetrievalCollectionResource($retrievalCollection);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function latest()
    {
        $retrievalCollection = RetrievalCollection::with(['sourceFiles'])->where('status', 'open')->latest()->first();
        return new RetrievalCollectionResource($retrievalCollection);
    }

    public function ready()
    {
        $rc = RetrievalCollection::with(['sourceFiles'])->where('status','ready')->get();
        return $rc;
    }

    public function takeOffline($id)
    {
        $rc = RetrievalCollection::find($id);
        $rc->update(['status' => 'offline']);
        return [];

    }

    public function download($id)
    {
        $path = storage_path("/piql_reader_test_reel.tar");
        return response()->download($path);
    }


    public function retrieving()
    {
        $retrievalCollection = RetrievalCollection::with(['sourceFiles'])->where('status', 'requesting')->get();
        return new RetrievalCollectionResourceCollection($retrievalCollection);
    }


    public function addToLatest(Request $request)
    {
        $retrievalCollection = RetrievalCollection::latest()->where('status', 'open')->first();
        if($retrievalCollection == null){
            $retrievalCollection = RetrievalCollection::create(['status' => 'open']);
        }

        RetrievalFile::create([
            'retrieval_collection_id' => $retrievalCollection->id,
            'file_id' => $request->fileObjectId
        ]);
        return new RetrievalCollectionResource($retrievalCollection->refresh());
    }

    public function files($id)
    {
        $rc = RetrievalCollection::with(['retrievalFiles'])->find($id)->retrievalFiles()->get();
        $files = $rc->map( function($sf) {
            return \App\File::find($sf->file_id);
        });
        return new FileCollection($files);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $retrievalCollection = RetrievalCollection::create(['status' => 'open']);
         return new RetrievalCollectionResource($retrievalCollection);
    }

    public function close(Request $request, $id)
    {
        $retrievalCollection = RetrievalCollection::find($id);
        if($retrievalCollection){
            $retrievalCollection->update(['status' => 'requesting']);
        }
        $new = RetrievalCollection::with(['source_files'])->create(['status' => 'open']);
        return new RetrievalCollectionResource($new); //TODO: improve response semantics
    }

    public function toReady(Request $request)
    {
        $retrievalCollections = RetrievalCollection::where('status', 'requesting')->get();
        $retrievalCollections->map(function ($rc) {
            $rc->update(['status' => 'ready']);
        });
        return new RetrievalCollectionResourceCollection($retrievalCollections);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\RetrievalCollection  $retrievalCollection
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $retrievalCollection = RetrievalCollection::find($id);
        return new RetrievalCollectionResource($retrievalCollection);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RetrievalCollection  $retrievalCollection
     * @return \Illuminate\Http\Response
     */
    public function edit(RetrievalCollection $retrievalCollection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RetrievalCollection  $retrievalCollection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RetrievalCollection $retrievalCollection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RetrievalCollection  $retrievalCollection
     * @return \Illuminate\Http\Response
     */
    public function destroy(RetrievalCollection $retrievalCollection)
    {
        //
    }
}
