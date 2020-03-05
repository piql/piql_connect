<?php

namespace App\Http\Controllers;
use App\Aip;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Charts\TestChartJS;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $fileFormatCount = $this->fileFormatsIngested(auth()->user());
        $onlineDataIngested = $this->onlineDataIngested(auth()->user());

        $infoArray['onlineDataIngested'] = $this->byteToMetricbyte($onlineDataIngested);
        $infoArray['offlineDataIngested'] = $this->byteToMetricbyte(23 * 110000000000);
        $infoArray['onlineAIPsIngested'] = $this->onlineAIPsIngested(auth()->user());
        $infoArray['offlineAIPsIngested'] = '' . (23 * 8);
        $infoArray['offlineReelsCount'] = '23';
        $infoArray['offlinePagesCount'] = (23 * 3 * 4654);
        $infoArray['AIPsRetrievedCount'] = 8;
        $infoArray['DataRetrieved'] = $this->byteToMetricbyte(8 * 14000000);

        $monthlyOnlineAIPsIngested = new TestChartJS;
        $monthlyOnlineAIPsAccessed = new TestChartJS;
        $monthlyOnlineDataIngested = new TestChartJS;
        $monthlyOnlineDataAccessed = new TestChartJS;
        $fileFormatsIngested = new TestChartJS;


        $monthlyOnlineAIPsIngested->load(url('api/v1/stats/monthlyOnlineAIPsIngested'));
        $monthlyOnlineAIPsIngested->options([
            'title' => [
                 'text' => 'AIPs Ingested (monthly)'
            ],
        ]);

        $monthlyOnlineAIPsAccessed->load(url('api/v1/stats/monthlyOnlineAIPsAccessed'));
        $monthlyOnlineAIPsAccessed->options([
            'title' => [
                 'text' => 'AIPs Accessed (monthly)'
            ],
        ]);

        $monthlyOnlineDataIngested->load(url('api/v1/stats/monthlyOnlineDataIngested'));
        $monthlyOnlineDataIngested->options([
            'title' => [
                 'text' => 'Data Ingested (monthly)'
            ],
        ]);

        $monthlyOnlineDataAccessed->load(url('api/v1/stats/monthlyOnlineDataAccessed'));
        $monthlyOnlineDataAccessed->options([
            'title' => [
                 'text' => 'Data Accessed (monthly)'
            ],
        ]);


        $fileFormatsIngested->labels(array_keys($fileFormatCount))->load(url('api/v1/stats/fileFormatsIngested'));
        $fileFormatsIngested->options([
            'title' => [
                 'text' => 'File formats Ingested'
            ],
            'legend' => [
                'display' => true,
                'position' => 'right',
                // 'onHover' =>
            ],
            'tooltips' => [
                'mode' => 'point',
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true
            ],
            'scales' => [
                'yAxes'=> [
                    'display' => false
                ]
            ]
        ]);

        return view('dashboard', compact(
            'monthlyOnlineAIPsIngested',
            'monthlyOnlineAIPsAccessed',
            'monthlyOnlineDataIngested',
            'monthlyOnlineDataAccessed',
            'fileFormatsIngested',
            'infoArray'
        ));
    }

    private function onlineDataIngested($user)
    {
        return Aip::where("owner", $user->id)->get()->map(function($aip) { return $aip->size; })->sum();
    }

    private function onlineAIPsIngested($user)
    {
        return Aip::where("owner", $user->id)->count();
    }

    private function monthlyOnlineAIPsIngested($user)
    {
        $aips = Aip::where("owner", $user->id)->get();

        $monthlyAIPsIngested = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($aips as $aip)
        {
            $creation_date = new \DateTime($aip->created_at);

            if ($creation_date > $oneYearAgo) {
                $monthlyAIPsIngested[$creation_date->format('n') - 1]++;
            }
        }

        return $this->arrayRearrangeCurrentMonthLast($monthlyAIPsIngested);
    }

    private function monthlyOnlineDataIngested($user)
    {
        $aips = Aip::where("owner", $user->id)->get();

        $monthlyDataIngested = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($aips as $aip)
        {
            $creation_date = new \DateTime($aip->created_at);

            if ($creation_date > $oneYearAgo) {

                $files = $aip->files;

                foreach ($files as $file)
                {
                    $monthlyDataIngested[$creation_date->format('n') - 1] += $file->filesize;
                }
            }
        }
        return $this->arrayRearrangeCurrentMonthLast($monthlyDataIngested);
    }

    private function fileFormatsIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $fileFormats = [];

        foreach ($bags as $bag)
        {
            $files = $bag->files;

            foreach ($files as $file)
            {
                $extension = pathinfo($file->filename)['extension'];

                if (array_key_exists($extension, $fileFormats))
                {
                    $fileFormats[$extension]++;
                } else
                {
                    $fileFormats[$extension] = 1;
                }
            }
        }
        arsort($fileFormats);
        return $fileFormats;
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
