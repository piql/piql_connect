<?php

namespace App\Http\Controllers\Api\Ingest;

use App\File;
use App\Http\Resources\MetadataResource;
use App\MetadataTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

class MetadataTemplateController extends Controller
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
    public function index(Request $request)
    {
        $metadata = \auth()->user()->morphMany( MetadataTemplate::class,'owner');
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return MetadataResource::collection( $metadata->paginate( $limit ) );

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

        if(!isset($requestData->dc))
        {
            $requestData =["dc" => (object)null];
        }

        $metadata = new MetadataTemplate([
            "uuid" => Uuid::generate()->string,
            "modified_by" => Auth::user()->id,
            "metadata" => $requestData,
        ]);
        $metadata->save();

        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function show(MetadataTemplate $metadata)
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
    public function update(Request $request, MetadataTemplate $metadata)
    {
        \Log::debug($request);
        $requestData = $this->validateRequest($request);
        \Log::debug($requestData);
        if(isset($requestData['metadata'])) {
            $metadata->metadata = $requestData['metadata'] + $metadata->metadata;
            $metadata->save();
        }

        return response()->json([ "data" => new MetadataResource($metadata)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function destroy(MetadataTemplate $metadata)
    {

        $metadata->parent()->dissociate();
        $metadata->owner()->dissociate();
        $metadata->delete();
        return response()->json([ "data" => new MetadataResource(null)]);
    }
}
