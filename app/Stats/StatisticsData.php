<?php

namespace App\Stats;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;

class StatisticsData
{

    public function monthlyIngested($userId)
    {
        $first = new \DateTime('-11 months');
        $last = new \DateTime('+1 month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($first, $interval, $last);

        $result = [];
        foreach ($period as $date) {
            $result[] = [
                'month' => $date->format('M'),
                'online' => $this->monthlyOnlineIngested($date, $userId),
                'offline' => $this->monthlyOfflineIngested($date, $userId),
            ];
        }
        return $result;       
    }

    private function monthlyOnlineIngested($date, $userId)
    {
        $stats_data = IngestedDataOnline::where('owner', $userId)
            ->whereBetween('ingest_date', [
                new DateTime('first day of ' . $date->format('Y-m')),
                new DateTime('last day of ' . $date->format('Y-m'))]);

        $stats_aips = IngestedAIPOnline::where('owner', $userId)
            ->whereBetween('ingest_date', [
                new DateTime('first day of ' . $date->format('Y-m')),
                new DateTime('last day of ' . $date->format('Y-m'))]);
            
        return [
            'aips' => $stats_aips->sum('aips'),
            'bags' => $stats_data->sum('bags'),
            'size' => $stats_data->sum('size')
        ];
    }

    private function monthlyOfflineIngested($date, $userId)
    {
        $stats = IngestedStatsOffline::where('owner', $userId)
            ->whereBetween('ingest_date', [
                new DateTime('first day of ' . $date->format('Y-m')),
                new DateTime('last day of ' . $date->format('Y-m'))]);

        return [
            'aips'=>$stats->sum('aips'),
            'size'=>$stats->sum('size')
        ];
    }

    public function monthlyAccessed($userId)
    {
        $first = new \DateTime('-11 months');
        $last = new \DateTime('+1 month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($first, $interval, $last);

        $result = [];
        foreach ($period as $date) {
            $result[] = [
                'month' => $date->format('M'),
                'online' => $this->monthlyOnlineAccessed($date, $userId),
                'offline' => $this->monthlyOfflineAccessed($date, $userId),
            ];
        }
        return $result;       
    }

    private function monthlyOnlineAccessed($date, $userId)
    {
        // Online access is not logged at the moment, revisit after 1.0 release
        // $data = IngestedDataOnline::where('owner', $userId)
        //         ->whereBetween('ingest_date', [
        //                 new DateTime('first day of ' . $date->format('Y-m')),
        //                 new DateTime('last day of ' . $date->format('Y-m'))
        //                 ])
        //         ->orderBy('recorded_at', 'desc')->take(1)->get(['aips', 'bags', 'size']);

        // if ($data != null && !empty($data) && isset($data[0])) {
        //     return [ 'aips'=>$data[0]->aips, 'bags'=>$data[0]->bags, 'size'=>$data[0]->size ];
        // }

        return [ 'aips'=>0, 'bags'=>0, 'size'=>0 ];
    }

    private function monthlyOfflineAccessed($date, $userId)
    {
        // Offline access is not enabled yet
        // $data = IngestedDataOnline::where('owner', $userId)
        //         ->whereBetween('ingest_date', [
        //                 new DateTime('first day of ' . $date->format('Y-m')),
        //                 new DateTime('last day of ' . $date->format('Y-m'))
        //                 ])
        //         ->orderBy('recorded_at', 'desc')->take(1)->get(['aips', 'bags', 'size']);
        //
        // if ($data != null && !empty($data) && isset($data[0])) {
        //     return [ 'aips'=>$data[0]->aips, 'bags'=>$data[0]->bags, 'size'=>$data[0]->size ];
        // }

        return [ 'aips'=>0, 'bags'=>0, 'size'=>0 ];
    }

    // Daily statistics will be enabled after the 1.0 release (ref CON-748)
    // public function dailyOnlineAIPsIngested($userId)
    // {
    //     $first = new \DateTime('-29 days');
    //     $last = new \DateTime();

    //     $interval = DateInterval::createFromDateString('1 day');
    //     $period = new DatePeriod($first, $interval, $last);

    //     $data = [];
    //     foreach ($period as $date) {
    //         $aip = IngestedAIPOnline::where([
    //             'ingest_date' => $date,
    //             'owner' => $userId,
    //         ])->orderBy('recorded_at', 'desc')->take(1)->get(['aips']);
    //         $data[] = ($aip == null || empty($aip) || !isset($latest[0])) ? 0 : $aip[0]->aips;
    //     }
    //     return $data;
    // }

    // Daily statistics will be enabled after the 1.0 release (ref CON-748)
    // public function dailyOnlineDataIngested($userId)
    // {
    //     $first = new \DateTime('-29 days');
    //     $last = new \DateTime();
    //     $data = IngestedDataOnline::whereBetween('ingest_date', [$first, $last])
    //         ->where('owner', $userId)
    //         ->orderBy('recorded_at', 'desc')
    //         ->groupBy('ingest_date')
    //         ->get(['ingest_date', 'bags', 'size']);
    //     return ($data == null || empty($data) || !isset($data[0])) ? [] : $data;
    // }

    public function fileFormatsIngested($userId)
    {
        $formats=IngestedMimeOnline::where('owner',$userId)->orderBy('ingested','desc')->get(['mime_type','ingested']);

        // Create a dummy if no file formats are registered
        if ($formats == null || empty($formats) || !isset($formats[0])) {
            $formats = [ (object)['ingested'=>1, 'mime_type'=>'none'] ];
        }

        $ingested = collect($formats)->sum(function ($f) { return $f->ingested; } );
        
        $data = collect($formats)->take(7)->mapWithKeys(function ($f) use (&$ingested) {
            $ingested -= $f->ingested;
            return [$f->mime_type => $f->ingested];
        });

        if ($ingested > 0) $data['other'] = $ingested;
        
        return $data;
    }
}
