<?php

namespace App\Stats;

use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;

class StatisticsData
{

    public function dailyOnlineAIPsIngested($userId)
    {
        $first = new \DateTime('-29 days');
        $last = new \DateTime();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($first, $interval, $last);

        $data = [];
        foreach ($period as $date) {
            $aip = IngestedAIPOnline::where([
                'ingest_date' => $date,
                'owner' => $userId,
            ])->orderBy('recorded_at', 'desc')->take(1)->get(['aips']);
            $data[] = ($aip == null || empty($aip) || !isset($latest[0])) ? 0 : $aip[0]->aips;
        }
        return $data;
    }

    public function monthlyOnlineAIPsIngested($userId)
    {
        $first = new \DateTime('-11 months');
        $last = new \DateTime('+1 month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($first, $interval, $last);

        $result = [];
        foreach ($period as $date) {
            $data = IngestedAIPOnline::where('owner', $userId)
                ->whereBetween('ingest_date', [
                    new DateTime('first day of ' . $date->format('Y-m')),
                    new DateTime('last day of ' . $date->format('Y-m'))
                ])
                ->orderBy('recorded_at', 'desc')->take(1)->get(['aips']);
            $result[] = ($data != null && !empty($data) && isset($data[0])) ? $data[0]->aips : 0;
        }
        return $result;
    }

    public function dailyOnlineDataIngested($userId)
    {
        $first = new \DateTime('-29 days');
        $last = new \DateTime();
        $data = IngestedDataOnline::whereBetween('ingest_date', [$first, $last])
            ->where('owner', $userId)
            ->orderBy('recorded_at', 'desc')
            ->groupBy('ingest_date')
            ->get(['ingest_date', 'bags', 'size']);
        return ($data == null || empty($data) || !isset($data[0])) ? [] : $data;
    }

    public function monthlyOnlineDataIngested($userId)
    {
        $first = new \DateTime('-11 months');
        $last = new \DateTime('+1 month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($first, $interval, $last);

        $result = [];
        foreach ($period as $date) {
            $ingested = ['bags' => 0, 'size' => 0, 'month' => $date->format('M')];
            $data = IngestedDataOnline::where('owner', $userId)
                ->whereBetween('ingest_date', [
                    new DateTime('first day of ' . $date->format('Y-m')),
                    new DateTime('last day of ' . $date->format('Y-m'))
                ])
                ->orderBy('recorded_at', 'desc')->take(1)->get(['bags', 'size']);
            if ($data != null && !empty($data) && isset($data[0])) {
                $ingested['bags'] += $data[0]->bags;
                $ingested['size'] += $data[0]->size;
            }
            $result[] = $ingested;
        }
        return $result;
    }

    public function latestOnlineFileFormatsIngested($userId)
    {
        $latest = IngestedMimeOnline::orderBy('recorded_at', 'desc')->take(1)->get(['recorded_at']);
        if ($latest == null || empty($latest) || !isset($latest[0])) return [(object)[
            'ingested' => 1,
            'mime_type' => 'none',
        ]];
        return IngestedMimeOnline::where([
            'recorded_at' => new DateTime($latest[0]['recorded_at']),
            // 'owner'=> $userId, //not yet querry-able
        ])->orderBy('ingested', 'desc')
            ->get(['mime_type', 'ingested']);
    }

    public function fileFormatsIngested($userId)
    {
        $formats = $this->latestOnlineFileFormatsIngested($userId);
        if ($formats == null || empty($formats) || !isset($formats[0])) return [];
        $ingested = collect($formats)->sum(function ($f) {
            return $f->ingested;
        });
        $data = collect($formats)->take(7)->mapWithKeys(function ($f) use (&$ingested) {
            $ingested -= $f->ingested;
            return [$f->mime_type => $f->ingested];
        });
        if ($ingested > 0) $data['other'] = $ingested;
        return $data;
    }
}
