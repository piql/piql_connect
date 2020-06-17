<?php

namespace App\Http\Controllers\Api\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Bag;

class StatusController extends Controller
{
    public function currentUser( Request $request )
    {
        return Auth::id();
    }

    public function currentBag( Request $request )
    {
        $userId = Auth::id();
        return Bag::whereOwner( $userId )->latest()->first();
    }

}
