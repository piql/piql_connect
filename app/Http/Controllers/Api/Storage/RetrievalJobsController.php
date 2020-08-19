<?php

namespace App\Http\Controllers\Api\Storage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobCollection;
use App\Http\Resources\FileObjectResource;
use App\Http\Resources\AipRetrievalResource;
use App\Job;
use App\Aip;
use Log;

class RetrievalJobsController extends Controller
{
    /**
     * Display a listing of Aips belonging to Jobs in the "stored" state
     *
     * @return \Illuminate\Http\Response
     */
    public function aipsOnFilm( Request $request )
    {
        $terms = collect(explode("%20", $request->query('search') ))->reject("");
        $archiveUuid = $request->query('archive');
        $holdingTitle = $request->query('holding');
        $fromDate = $request->query('archived_from');
        $toDate = $request->query('archived_to');

        $aq = Aip::query();

        $aq->whereHas('jobs', function(  $q) {
            /*TODO: Update this status query when we have a working state for ingested jobs */
            $q->where('status','=', 'stored'); 
        } )->where( 'owner', Auth::id() );

        if( $fromDate ) {
            try {
                $cq = new \Carbon\Carbon( $fromDate );
            } catch ( \Exception $ex ) {
                Log::warn( "failed to parse archived_from date ".$ex->getMessage() );
            }
            if( $cq ) {
                $aq->whereDate('created_at', '>=', $cq->toDateString() );
            }
        }
        if( $toDate ) {
            try {
                $cq = new \Carbon\Carbon( $toDate );
            } catch ( \Exception $ex ) {
                Log::warn( "failed to parse archived_to date ".$ex->getMessage() );
            }
            if( $cq ) {
                $aq->whereDate('created_at', '<=', $cq->toDateString() );
            }
        }

        if($archiveUuid) {
            $aq->whereHas('storage_properties',
                function ($sp) use ($archiveUuid) {
                    $sp->where('archive_uuid', $archiveUuid);
                });
            if($holdingTitle) {
                $aq->whereHas('storage_properties',
                    function ($sp) use ($holdingTitle) {
                        $sp->where('holding_name', $holdingTitle);
                    });
            }
        }

        if( $terms->count() > 0) {
            $terms->first( function ($term, $key) use ($aq) {
                $aq->whereHas('storage_properties', function ($sp) use($term) {
                    $sp->where('name','LIKE',"%{$term}%");
                });
            });
            
            $terms->slice(1)->each( function ($term, $key) use ($aq) {
                $aq->whereHas('storage_properties', function ($sp) use($term) {
                    $sp->orWhere('name','LIKE',"%{$term}%");
                });
            });
            
        }

        $limit = $request->limit ? $request->limit : env('DEFAULT_ENTRIES_PER_PAGE');
        $aipsInJobs = $aq->paginate( $limit );
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
        if( $job->owner != Auth::id() ) {
            abort(403, "You are not the owner of this job");
        }
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
