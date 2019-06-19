<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Bag;

class StatusController extends Controller
{
    public function currentUser(Request $request)
    {
        return User::first()->id;
    }

    public function currentBag(Request $request)
    {
        return Bag::latest()->first();
    }
}
