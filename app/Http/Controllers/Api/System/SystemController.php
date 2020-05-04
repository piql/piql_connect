<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function languages( Request $request )
    {
        return response()->json([
            '0' => [ 'title' => 'English', 'code' => 'en' ], '1' => [ 'title' => 'Norsk Bokmål', 'code' => 'nb_no' ] ], 200);
    }
}
