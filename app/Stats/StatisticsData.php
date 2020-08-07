<?php

namespace App\Stats;

use DateInterval;
use DatePeriod;
use DateTime;

class StatisticsData
{

    public function latestOnlineAIPsIngested($userId)
    {
        return IngestedAIPOnline::orderBy('recorded_at', 'desc')
            ->where('owner', $userId)
            ->take(1)
            ->get(['aips', 'ingest_date']);
    }

    public function dailyOnlineAIPsIngested($userId)
    {
        $first = new \DateTime('-29 days');
        $last = new \DateTime();

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($first, $interval, $last);

        $data = array_fill(0, 30, 0);
        foreach ($period as $date) {
            $aip = IngestedAIPOnline::where([
                'ingest_date' => $date,
                'owner' => $userId,
            ])->orderBy('recorded_at', 'desc')->take(1)->get(['aips']);
            if (empty($aip)) continue;
            $month = +$date->format('m') - 1;
            $data[$month] = $aip[0]->aips;
        }
        return $data;
    }

    public function monthlyOnlineAIPsIngested($userId)
    {
        $aips = $this->latestOnlineAIPsIngested($userId);
        if (empty($aips)) return [];
        $data = array_fill(0, 12, 0);
        foreach ($aips as $a) {
            $date = new DateTime(strtotime($a->ingest_date));
            $data[+$date->format('m') - 1] += $a->aips;
        }
        return $data;
    }

    public function dailyOnlineDataIngested($userId)
    {
        $first = new \DateTime('-29 days');
        $last = new \DateTime();
        return IngestedDataOnline:: //distinct('ingest_date')
            whereBetween('ingest_date', [$first, $last])
            ->where('owner', $userId)
            ->orderBy('recorded_at', 'desc')
            ->groupBy('ingest_date')
            ->get(['ingest_date', 'bags', 'size']);
    }

    public function monthlyOnlineDataIngested($userId)
    {
        $first = new \DateTime('-9 months');
        $last = new \DateTime('+2 month');

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($first, $interval, $last);

        $result = range(0, 11);
        foreach ($period as $date) {
            $month = +$date->format('m') - 1;
            if (empty($result[$month]) || !isset($result[$month]['bags']))
                $result[$month] = ['bags' => 0, 'size' => 0, 'month' => $date->format('M')];

            $data = IngestedDataOnline::where('owner', $userId)
                ->whereBetween('ingest_date', [$date, new DateTime('last day of ' . $date->format('Y-m'))])
                ->orderBy('recorded_at', 'desc')->take(1)->get(['bags', 'size']);
            if ($data == null || empty($data) || !isset($data[0])) continue;
            $result[$month]['bags'] += $data[0]->bags;
            $result[$month]['size'] += $data[0]->size;
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
