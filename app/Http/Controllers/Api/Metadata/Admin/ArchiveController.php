<?php

namespace App\Http\Controllers\Api\Metadata\Admin;

use App\Http\Resources\ArchiveResource;
use Illuminate\Support\Facades\Auth;
use App\Archive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;

class ArchiveController extends Controller
{
    //TODO: IMPORTANT! We need a guard here to limit access to the Archive API to System Administrators only (no Superusers/local admins here)
    //
    /**
     * Display a paginated listing of Archives
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        return ArchiveResource::collection( Archive::paginate( $limit ) );
    }

    /**
     * Validate and persist a new Archive.
     *
     * @param \Illuminate\Http\Request $request title, description (optional), metadata (optional)
     * @return \Illuminate\Http\Response of ArchiveResource
     */
    public function store(Request $request )
    {
        $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "organization_uuid" => "uuid",
                "defaultMetadataTemplate" => "array|nullable"
            ]
        );
        return new ArchiveResource( Archive::create( $validatedRequest ) );
    }

    /**
     * Display a specified Archive
     *
     * @param Archive $archive
     * @return \Illuminate\Http\Response of ArchiveResource
     */
    public function show(Archive $archive)
    {
        return new ArchiveResource( $archive );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Archive $archive
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Archive $archive )
    {
        $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "organization_uuid" => "uuid",
                "defaultMetadataTemplate" => "array|nullable"
            ]
        );
        $archive->update( $validatedRequest );
        return new ArchiveResource( $archive );
    }

    /**
     * Update or create the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Archive $archive
     * @return \Illuminate\Http\Response
     */

    public function upsert( Request $request )
    {
       $validatedRequest = $this->validate( $request, [
                "title" => "string",
                "description" => "string|nullable",
                "organization_uuid" => "uuid",
                "defaultMetadataTemplate" => "array|nullable"
            ]
        );
        if( $request->id ) {
            $archive = Archive::findOrFail( $request->id );
            $archive->update( $validatedRequest );
            return new ArchiveResource( $archive );
        }

        return new ArchiveResource( Archive::create( $validatedRequest ) );
    }



    /**
     * In this version, deleting archives is not supported due to consistency issues
     *
     * @param Archive $archive
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Archive $archive)
    {
        return abort( 405, "Archives cannot be deleted in this version" );
    }
}
