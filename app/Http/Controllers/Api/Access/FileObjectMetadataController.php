<?php

namespace App\Http\Controllers\Api\Access;

use App\FileObject;
use App\Http\Resources\MetadataResource;
use App\Http\Controllers\Controller;

class FileObjectMetadataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FileObject $file)
    {
        //
        return response()->json([ "data" => $file->metadata->map(function($element) {
            // todo: remove this when ingest and access are aligned
            if(isset($element->metadata["mets"]['dc'])) {
                $element->metadata = collect($element->metadata["mets"]['dc'])->flatMap(function ($value, $key) {
                    return ['dc:' . $key => $value];
                })->toArray();
            }
            return new MetadataResource($element);
        })]);
    }

}
