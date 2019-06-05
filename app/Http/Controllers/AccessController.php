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

    public function index()
    {
        Log::info("showRetrieve");
        return view('retrieve');   
    }
}
