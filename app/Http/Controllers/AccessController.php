<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\RetrievalCollection;
use Illuminate\Http\Request;
use Log;

class AccessController extends Controller
{
    public function __construct()
    {

    }

    public function retrieve()
    {
        return view('access.retrieve.index', ['files' => RetrievalCollection::latest()->first()->retrievalFiles()->get()->map( function ($rf) { return $rf->sourceFile->get(); } )->flatten()->take(10)]);
    }

    public function ready()
    {
        return view('access.retrieve.ready');
    }

    public function retrieving()
    {
        return view('access.retrieve.retrieving');
    }

    public function downloadable()
    {
        return view('access.retrieve.downloadable');
    }

    public function history()
    {
        return view('access.retrieve.history');
    }

}
