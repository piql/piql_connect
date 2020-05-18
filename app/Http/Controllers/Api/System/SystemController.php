<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function languages( Request $request )
    {
        return collect([ ['id' => '0', 'title' => 'English', 'code' => 'en'], ['id' => '1', 'title' => 'Norsk Bokmål', 'code' => 'nb_no'] ]);
    }

    public function sessionLifetime( Request $request )
    {
        return env('SESSION_LIFETIME', 120);
    }
}
