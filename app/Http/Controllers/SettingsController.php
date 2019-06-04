<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class SettingsController extends Controller
{
    public function __construct()
    {

    }

    public function showSettings()
    {
        Log::info("showSettings");
        return view('settings');
    }

}
