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
        //TODO: This should probably gather stats based on account rather than user.
        //      Revisit when the keycloak authentication has settled.
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'Must authenticate to access per-user statistics' ], 401);
        }
        if( $userId !== "current" && $currentUser->id != $userId ) {
            return response()->json([ 'error' => 401, 'message' => 'Access to user statistics is restricted' ], 401);
        }

        $infoArray = [
            'onlineDataIngested'   => IngestedDataOnline::where('owner', $currentUser->id)->sum('size'),
            'offlineDataIngested'  => 0,    //TODO: [DUMMY] Replace with a proper query
            'onlineAIPsIngested'   => Aip::where('owner',$currentUser->id)->count(),
            'offlineAIPsIngested'  => 0,    //TODO: [DUMMY] Replace with a proper query
            'offlineReelsCount'    => Job::where('owner',$currentUser->id)
                                        ->whereIn('status', ['transferring', 'preparing', 'writing', 'storing'])->count(),
            'offlinePagesCount'    => 0,    //TODO: Visuals on film will not be implemented in 1.0
            'offlineAIPsRetrieved' => 0,    //TODO: Retrieval from film will not be implemented in 1.0
            'offlineDataRetrieved' => 0     //TODO: Retrieval from film will not be implemented in 1.0
        ];

        return new UserStatsResource( $infoArray );
    }

}
