<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

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
            "title" => "string",
            "description" => "string|nullable",
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
     * @return void
     */
    public function store(Request $request)
    {

        $validatedData = $this->validateRequest( $request );
        $template = MetadataTemplate::create([
            "metadata" => $validatedData['metadata'],
            "owner" => Auth::user(),
            "modified_by" => Auth::id()
        ]);

        return new MetadataResource( $template );
    }

    /**
     * Display a MetadataTemplate
     * @param string $id MetadataTemplate id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        return new MetadataResource( MetadataTemplate::findOrFail( $id ) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, MetadataTemplate $metadataTemplate )
    {
        $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "metadata" => "array|nullable"
            ]
        );

        $metadataTemplate->update( $validatedRequest );

        return new MetadataResource( $metadataTemplate );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Metadata  $metadata
     * @return \Illuminate\Http\Response
     */
    public function destroy(MetadataTemplate $metadataTemplate)
    {

        $metadataTemplate->parent()->dissociate();
        $metadataTemplate->owner()->dissociate();
        $metadataTemplate->delete();
        return response( "", 204);
    }
}
