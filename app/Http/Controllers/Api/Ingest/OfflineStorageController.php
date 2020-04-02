<?php

namespace App\Http\Controllers\Api\Ingest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Job;
use App\User;
use App\Bag;
use App\File;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobCollection;
use App\Http\Resources\BagCollection;
use App\Events\BagFilesEvent;
use Response;
use Log;

class OfflineStorageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jobs()
    {
        $jobs = Job::where('owner', Auth::id() )
            ->where(function($query) {
                $query->where('status', 'created')
                    ->orWhere('status', 'closed');
            })
            ->withCount('aips')
            ->latest()
            ->paginate( env('DEFAULT_ENTRIES_PER_PAGE') );

        return new JobCollection( $jobs );
    }

    public function archiveJobs()
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $jobs = \App\Job::where('status', '=', 'ingesting');

        return new JobCollection( $jobs->paginate( env('DEFAULT_ENTRIES_PER_PAGE') ) );
    }

    public function archiveJob($jobId)
    {
        $job = Job::findOrFail($jobId);
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $data = request()->validate([
            'name' => 'string',
            'status' => 'string',
        ]);

        if(isset($data['name'])) {
            $job->name = $data['name'];
        }

        if(isset($data['status']) && ($data['status'] == 'ingesting' )) {
            $job->status = "ingesting";
        }
        $job->save();
        return new JobResource( $job );
    }

    public function bags( $jobId )
    {
        $job = Job::with(['bags'])->find($jobId);
        if( $job->owner !== Auth::id() ){
            abort( response()->json([ 'error' => 401, 'message' => 'The current user is not authorized to view job with id '.$jobId ], 401 ) );
        }

        return new BagCollection( $job->bags()->paginate( env('DEFAULT_ENTRIES_PER_PAGE') ) );
    }


}
