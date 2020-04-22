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
     * Display stats for a user. If userId is the string "current", fetch for the currently authenticated user;
     *
     * @return \Illuminate\Http\Response
     */
    public function userStats( $userId, Request $request )
    {
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'Must authenticate to access per-user statistics' ], 401);
        }
        if( $userId !== "current" && $currentUser->id != $userId ) {
            return response()->json([ 'error' => 401, 'message' => 'Access to user statistics is restricted' ], 401);
        }

        $fileFormatCount = $this->fileFormatsIngested( $currentUser );
        $onlineDataIngested = $this->onlineDataIngested( $currentUser );

        $infoArray['onlineDataIngested'] = $this->byteToMetricbyte($onlineDataIngested);
        $infoArray['offlineDataIngested'] = $this->byteToMetricbyte(115*3*1000*1000*1000); // FMU DUMMY NUMBERS
        $infoArray['onlineAIPsIngested'] = $this->onlineAIPsIngested(auth()->user());
        $infoArray['offlineAIPsIngested'] = 12100; // FMU DUMMY NUMBERS
        $infoArray['offlineReelsCount'] = '3'; // FMU DUMMY NUMBERS
        $infoArray['offlinePagesCount'] = 0; // FMU DUMMY NUMBERS
        $infoArray['AIPsRetrievedCount'] = 0; // FMU DUMMY NUMBERS
        $infoArray['DataRetrieved'] = 0; // FMU DUMMY NUMBERS

        return new UserStatsResource( $infoArray );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function onlineDataIngested($user)
    {
        return FileObject::where("storable_type", 'App\Aip')
            ->where('path','NOT LIKE','%/data/objects/metadata/%')
            ->where('path','NOT LIKE','%/data/objects/submissionDocumentation/%')
            ->where('path','LIKE','%/data/objects%')
            ->sum('size');
    }

    private function onlineAIPsIngested($user)
    {
        return Aip::count();
    }

    private function monthlyOnlineAIPsIngested($user)
    {
        return $this->arrayRearrangeCurrentMonthLast(
            collect([0,1,2,3,4,5,6,7,8,9,10,11])->map(function($obj) {
                return FileObject::where("storable_type", 'App\Aip')
                    ->whereBetween("created_at", [
                    (new \DateTime(date("Y-m")))->modify((-$obj)." month"),
                    (new \DateTime(date("Y-m")))->modify((1-$obj)." month")
                ])->count();
            })
        );

    }

    private function monthlyOnlineDataIngested($user)
    {
        return $this->arrayRearrangeCurrentMonthLast(
            collect([0,1,2,3,4,5,6,7,8,9,10,11])->map(function($obj) {
                return FileObject::where("storable_type", 'App\Aip')
                    ->where('path','NOT LIKE','%/data/objects/metadata/%')
                    ->where('path','NOT LIKE','%/data/objects/submissionDocumentation/%')
                    ->where('path','LIKE','%/data/objects%')
                    ->whereBetween("created_at", [
                    (new \DateTime(date("Y-m")))->modify((-$obj)." month"),
                    (new \DateTime(date("Y-m")))->modify((1-$obj)." month")
                ])->sum("size");
            })
        );
    }

    private function fileFormatsIngested($user)
    {
        $count = FileObject::where('storable_type', 'App\Aip')
            ->where('path','NOT LIKE','%/data/objects/metadata/%')
            ->where('path','NOT LIKE','%/data/objects/submissionDocumentation/%')
            ->where('path','LIKE','%/data/objects%')
            ->count();

        $fileFormats = FileObject::select('mime_type',DB::raw('count(mime_type)'))
            ->where('storable_type', 'App\Aip')
            ->where('path','NOT LIKE','%/data/objects/metadata/%')
            ->where('path','NOT LIKE','%/data/objects/submissionDocumentation/%')
            ->where('path','LIKE','%/data/objects%')
            ->groupBy('mime_type')
            ->orderBy('count(mime_type)', 'desc')
            ->limit(4)
            ->get()
            ->flatMap(function($obj) use(&$count) {
                $count -= $obj['count(mime_type)'];
                return [$obj->mime_type => $obj['count(mime_type)']];
            });
        if($count) {
            $fileFormats['other'] = $count;
        }

        $keys = $fileFormats->toArray();
        arsort($keys);
        return $keys;
    }

    private function byteToMetricbyte($bytes)
    {
        if ($bytes > 1000000000000000)
        {
            return (int) ($bytes/1000000000000000) . ' PB';
        }
        if ($bytes > 1000000000000)
        {
            return (int) ($bytes/1000000000000) . ' TB';
        }
        if ($bytes > 1000000000)
        {
            return (int) ($bytes/1000000000) . ' GB';
        }
        if ($bytes > 1000000)
        {
            return (int) ($bytes/1000000) . ' MB';
        }
        if ($bytes > 1000)
        {
            return (int) ($bytes/1000) . ' KB';
        }

        return $bytes . ' B';
    }

    private function last30days()
    {
        $last30days = [];

        for ($i=0; $i < 30; $i++) {
            array_unshift($last30days, date('j. M', strtotime('-' . $i . ' days')));
        }

        return $last30days;
    }

}
