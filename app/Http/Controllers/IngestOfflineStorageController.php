<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;

class IngestOfflineStorageController extends Controller
{
    public function index() {
        return view('ingest.jobs.index');
    }

    public function show($jobId) {
        return view('ingest.jobs.show', ['jobId' => $jobId]);
    }

    public function metadataEdit(Job $job) {
        return view('ingest.metadata.edit', ['job' => $job]);
    }

    public function configurationEdit(Job $job) {
        //$job  = Job::findOrFail($jobId);
        return view('ingest.jobs.configuration', ['job' => $job]);
    }

}
