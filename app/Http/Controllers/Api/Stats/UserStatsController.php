<?php

namespace App\Http\Controllers\Api\Stats;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatsResource;
use App\Aip;
use App\FileObject;
use Log;
use App\User;
use App\Stats\IngestedDataOnline;
use App\Stats\IngestedStatsOffline;
use App\Job;

class UserStatsController extends Controller
{

    /**
     * Gather statistics for a single user. If userId is the string "current", fetch for the currently authenticated user;
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke( $userId )
    {
        $infoArray = [
            'onlineDataIngested'   => IngestedDataOnline::select()->sum('size'),
            'offlineDataIngested'  => IngestedStatsOffline::select()->sum('size'),
            'onlineAIPsIngested'   => Aip::count(),
            'offlineAIPsIngested'  => IngestedStatsOffline::select()->sum('aips'),
            'offlineReelsCount'    => Job::whereIn('status', ['transferring', 'preparing', 'writing', 'storing'])->count(),
            'offlinePagesCount'    => 0,    //TODO: Visuals on film will not be implemented in 1.0
            'offlineAIPsRetrieved' => 0,    //TODO: Retrieval from film will not be implemented in 1.0
            'offlineDataRetrieved' => 0     //TODO: Retrieval from film will not be implemented in 1.0
        ];

        return new UserStatsResource( $infoArray );
    }

}
