<?php

namespace App\Http\Controllers\Api\Ingest;

use App\File;
use App\Http\Resources\MetadataResource;
use App\Metadata;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class FileMetadataController extends Controller
{
    private function validateRequest(Request $request) {
        return $request->validate([
            "metadata.dc.title" => "string|nullable",
            "metadata.dc.creator" => "string|nullable",
            "metadata.dc.subject" => "string|nullable",
            "metadata.dc.description" => "string|nullable",
            "metadata.dc.publisher" => "string|nullable",
            "metadata.dc.contributor" => "string|nullable",
            "metadata.dc.date" => "string|nullable",
            "metadata.dc.type" => "string|nullable",
            "metadata.dc.format" => "string|nullable",
            "metadata.dc.identifier" => "string|nullable",
            "metadata.dc.source" => "string|nullable",
            "metadata.dc.language" => "string|nullable",
            "metadata.dc.relation" => "string|nullable",
            "metadata.dc.coverage" => "string|nullable",
            "metadata.dc.rights" => "string|nullable",
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(File $file)
    {
        //
        return response()->json([ "data" => $file->metadata->map(function($element) {
            return new MetadataResource($element);
        })]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param File $file
     * @return void
     */
    public function store(Request $request, File $file)
    {
        if($file->bag->status != "open") {
            abort( response()->json([ 'error' => 400, 'message' => 'Metadata is read only' ], 400 ) );
        }

        $requestData = $this->validateRequest($request);

        if(!isset($requestData->dc))
        {
            $requestData =["dc" => (object)null];
        }

        $metadata = new Metadata([
            "uuid" => Uuid::generate()->string,
            "modified_by" => Auth::user()->id,
            "metadata" => $requestData,
        ]);
        $metadata->parent()->associate($file);
        $metadata->save();

        return response()->json([ "data" => $file->refresh()->metadata->map(function($element) {
            return new MetadataResource($element);
        })]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function show(File $file, Metadata $metadata)
    {
        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file, Metadata $metadata)
    {
        if($file->bag->status != "open") {
            abort( response()->json([ 'error' => 400, 'message' => 'Metadata is read only' ], 400 ) );
        }
        \Log::debug($request);
        $requestData = $this->validateRequest($request);
        \Log::debug($requestData);
        if(isset($requestData['metadata'])) {
            $metadata->metadata = $requestData['metadata'] + $metadata->metadata;
            $metadata->save();
        }

        return response()->json([ "data" => $file->refresh()->metadata->map(function($element) {
            return new MetadataResource($element);
        })]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file, Metadata $metadata)
    {
        if($file->bag->status != "open") {
            abort( response()->json([ 'error' => 400, 'message' => 'Metadata is read only' ], 400 ) );
        }

        $metadata->parent()->dissociate();
        $metadata->delete();
        return response()->json([ "data" => $file->refresh()->metadata->map(function($element) {
            return new MetadataResource($element);
        })]);
    }
}
