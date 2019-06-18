<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;

class StatusController extends Controller
{
    public function currentUser(Request $request)
    {
        return User::first()->id;
    }
}
