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

        $infoArray['onlineDataIngested'] = $this->onlineDataIngested( $currentUser );
        $infoArray['offlineDataIngested'] = $this->offlineDataIngested( $currentUser );
        $infoArray['onlineAIPsIngested'] = $this->onlineAIPsIngested( $currentUser );
        $infoArray['offlineAIPsIngested'] = $this->offlineAIPsIngested( $currentUser );
        $infoArray['offlineReelsCount'] = $this->offlineReelsCount( $currentUser );
        $infoArray['offlinePagesCount'] = 0; //TODO: Visuals on film will not be implemented in 1.0
        $infoArray['offlineAIPsRetrieved'] = 0; //TODO: Retrieval from film will not be implemented in 1.0
        $infoArray['offlineDataRetrieved'] = 0;  //TODO: Retrieval from film will not be implemented in 1.0

        return new UserStatsResource( $infoArray );
    }

    //TODO: Potentially expensive query, consider using mongoDB for this
    private function onlineDataIngested($user)
    {
        return FileObject::where("storable_type", 'App\Aip')
            ->where('path','NOT LIKE','%/data/objects/metadata/%')
            ->where('path','NOT LIKE','%/data/objects/submissionDocumentation/%')
            ->where('path','LIKE','%/data/objects%')
            ->sum('size');
    }

    private function offlineDataIngested($user)
    {
        //TODO: [DUMMY] Replace with a proper query
        return 0;
    }

    private function onlineAIPsIngested($user)
    {
        return Aip::count();
    }

    private function offlineAIPsIngested($user)
    {
        //TODO: [DUMMY] Replace with a proper query
        return 0;
    }

    private function offlineReelsCount($user)
    {
        //TODO: [DUMMY] Replace with a proper query
        return 0;
    }

}
