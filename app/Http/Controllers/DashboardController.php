<?php

namespace App\Http\Controllers;
use App\Aip;
use App\FileObject;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
