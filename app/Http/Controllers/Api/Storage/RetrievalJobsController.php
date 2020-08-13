<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobCollection;
use App\Http\Resources\FileObjectResource;
use App\Http\Resources\AipRetrievalResource;
use App\Job;
use App\Aip;

class RetrievalJobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*TODO: Update this status query when we have a working state for ingested jobs */
        $aipsInJobs = Aip::with('storage_properties')
            ->whereHas('jobs', function(  $q) { $q->where('status','=', 'ingesting'); } )
            ->where( 'owner', Auth::id() )
            ->paginate( env( "DEFAULT_ENTRIES_PER_PAGE" ) );
        return AipRetrievalResource::collection( $aipsInJobs );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) 
    {
        $job = Job::find( $id );
        return new JobResource( $job );
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
