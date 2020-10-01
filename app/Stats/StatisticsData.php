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
       $stats_data = IngestedDataOnline::whereBetween('ingest_date', [
            new DateTime('first day of ' . $date->format('Y-m')),
            new DateTime('last day of ' . $date->format('Y-m'))]);

       $stats_aips = IngestedAIPOnline::whereBetween('ingest_date', [
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
        $stats = IngestedStatsOffline::whereBetween('ingest_date', [
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
        return [ 'aips'=>0, 'bags'=>0, 'size'=>0 ];
    }

    private function monthlyOfflineAccessed($date, $userId)
    {
        // Offline access is not enabled yet
        return [ 'aips'=>0, 'bags'=>0, 'size'=>0 ];
    }

    public function fileFormatsIngested($userId)
    {
        $formats=IngestedMimeOnline::select()->orderBy('ingested','desc')->get(['mime_type','ingested']);

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
