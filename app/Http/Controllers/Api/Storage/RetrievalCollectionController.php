<?php

namespace App\Http\Controllers\Api\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\RetrievalCollectionResource;
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
        $retrievalCollection = RetrievalCollection::latest()->where('status', 'open')->first();
        return new RetrievalCollectionResource($retrievalCollection);
    }

    public function addToLatest(Request $request)
    {
        $retrievalCollection = RetrievalCollection::latest()->where('status', 'open')->first();
        if($retrievalCollection == null){
            $retrievalCollection = RetrievalCollection::create(['status' => 'open']);
        }

        RetrievalFile::create([
            'retrieval_collection_id' => $retrievalCollection->id,
            'file_id' => $request->fileId
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
