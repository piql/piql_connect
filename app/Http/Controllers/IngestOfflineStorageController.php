<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IngestOfflineStorageController extends Controller
{
    public function index() {
        return view('ingest.jobs.index');
    }

    public function show($jobId) {
        return view('ingest.jobs.show', ['jobId' => $jobId]);
    }

}
