<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;

class IngestOfflineStorageController extends Controller
{
    public function index() {
        return view('ingest.jobs.index', ['enable_offline_actions' => env('ENABLE_OFFLINE_ACTIONS', true)] );
    }

    public function show($jobId) {
        $job = Job::findOrFail($jobId);
        return view('ingest.jobs.show', ['job' => $job]);
    }

    public function metadataEdit($jobId) {
        $job = Job::findOrFail($jobId);
        return view('ingest.metadata.edit', ['job' => $job]);
    }

    public function configurationEdit( $jobId) {
        $job = Job::findOrFail($jobId);
        return view('ingest.jobs.configuration', ['job' => $job]);
    }

}
