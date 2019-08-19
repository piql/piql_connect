<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class AccessController extends Controller
{
    public function __construct()
    {

    }

    public function retrieve()
    {
        return view('access.retrieve.index');
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
