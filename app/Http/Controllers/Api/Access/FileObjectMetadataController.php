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
            $element->metadata = $element->metadata["mets"];
            return new MetadataResource($element);
        })]);
    }

}
