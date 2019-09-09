<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Job;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Bag;
use App\File;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Events\BagFilesEvent;
use Carbon\Carbon;

class OfflineStorageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jobs()
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $jobs = \App\Job::where('status', '=', 'created')->get();

        return Response::json($jobs);
    }

    public function bags($jobId)
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $job = \App\Job::find($jobId);

        return Response::json($job->bags);
    }

    public function archiveJobs()
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $jobs = \App\Job::where('status', '=', 'ingesting')->get();

        return Response::json($jobs);
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
        return Response::json($job);
    }

}
