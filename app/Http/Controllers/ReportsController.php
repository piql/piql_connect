<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class ReportsController extends Controller
{
     public function __construct()
    {

    }

    public function showReports()
    {
        Log::info("showReports");
        return view('reports');
    }
}
