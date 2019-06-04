<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class IngestController extends Controller
{
    public function __construct()
    {

    }

    public function showUpload()
    {
        Log::info("showUpload");
        return view('upload');
    }
}
