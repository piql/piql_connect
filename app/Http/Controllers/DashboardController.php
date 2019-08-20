<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Charts\TestChartJS;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    public function showDashboard()
    {
        Log::info('showDashboard');

        $last12Months = $this->arrayRearrangeCurrentMonthLast(array_fill(0, 12, 0));
        $last30days = $this->last30days();
        $fileFormatCount = $this->fileFormatsIngested(auth()->user());
        $onlineDataIngested = $this->onlineDataIngested(auth()->user());
        
        $infoArray['onlineDataIngested'] = $this->byteToMetricbyte($onlineDataIngested);
        $infoArray['offlineDataIngested'] = $this->byteToMetricbyte(23 * 110000000000);
        $infoArray['onlineAIPsIngested'] = $this->onlineAIPsIngested(auth()->user());
        $infoArray['offlineAIPsIngested'] = '' . (23 * 55); 
        $infoArray['offlineReelsCount'] = '23';
        $infoArray['offlinePagesCount'] = (23 * 3 * 8000);

        $monthlyOnlineAIPsIngested = new TestChartJS;
        $monthlyOnlineAIPsAccessed = new TestChartJS;
        $monthlyOnlineDataIngested = new TestChartJS;
        $monthlyOnlineDataAccessed = new TestChartJS;
        // $dailyOnlineAIPsIngested = new TestChartJS;
        // $dailyOnlineAIPsAccessed = new TestChartJS;
        // $dailyOnlineDataIngested = new TestChartJS;
        // $dailyOnlineDataAccessed = new TestChartJS;
        $fileFormatsIngested = new TestChartJS;
        

        $monthlyOnlineAIPsIngested->title('Monthly AIPs Ingested')->load(url('api/v1/stats/monthlyOnlineAIPsIngested'));
        $monthlyOnlineAIPsIngested->labels(array_keys($last12Months));
        $monthlyOnlineAIPsIngested->options([
            'legend' => [
                'display' => false,
            ],
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false
            ]
        ]);

        $monthlyOnlineAIPsAccessed->title('Monthly AIPs Accessed')->load(url('api/v1/stats/monthlyOnlineAIPsAccessed'));
        $monthlyOnlineAIPsAccessed->labels(array_keys($last12Months));
        $monthlyOnlineAIPsAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false
            ],
        ]);

        $monthlyOnlineDataIngested->title('Monthly Data Ingested');
        $monthlyOnlineDataIngested->labels(array_keys($last12Months))->load(url('api/v1/stats/monthlyOnlineDataIngested'));
        $monthlyOnlineDataIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false
            ],
        ]);

        $monthlyOnlineDataAccessed->title('Monthly Data Accessed')->load(url('api/v1/stats/monthlyOnlineDataAccessed'));;
        $monthlyOnlineDataAccessed->labels(array_keys($last12Months));
        $monthlyOnlineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false
            ],
        ]);

        // $dailyOnlineAIPsIngested->title('Ingested AIPs Last 30 days')->load(url('api/v1/stats/dailyOnlineAIPsIngested'));
        // $dailyOnlineAIPsIngested->labels($last30days);
        // $dailyOnlineAIPsIngested->options([
        //     'legend' => [
        //         'display' => false
        //     ],
        //     'tooltips' => [
        //         'mode' => 'index',
        //         'intersect' => false
        //     ],
        // ]);

        // $dailyOnlineAIPsAccessed->title('AIPs Accessed Last 30 Days')->load(url('api/v1/stats/dailyOnlineAIPsAccessed'));
        // $dailyOnlineAIPsAccessed->labels($last30days);
        // $dailyOnlineAIPsAccessed->options([
        //     'legend' => [
        //         'display' => false
        //     ],
        //     'tooltips' => [
        //         'mode' => 'index',
        //         'intersect' => false
        //     ],
        // ]);

        // $dailyOnlineDataIngested->title('Data Ingested Last 30 Days')->load(url('api/v1/stats/dailyOnlineDataIngested'));
        // $dailyOnlineDataIngested->labels($last30days);
        // $dailyOnlineDataIngested->options([
        //     'legend' => [
        //         'display' => false
        //     ],
        //     'tooltips' => [
        //         'mode' => 'index',
        //         'intersect' => false
        //     ],
        // ]);

        // $dailyOnlineDataAccessed->title('Date Accessed Last 30 Days')->load(url('api/v1/stats/dailyOnlineDataAccessed'));
        // $dailyOnlineDataAccessed->labels($last30days);
        // $dailyOnlineDataAccessed->options([
        //     'legend' => [
        //         'display' => false
        //     ],
        //     'tooltips' => [
        //         'mode' => 'index',
        //         'intersect' => false
        //     ],
        // ]);

        $fileFormatsIngested->title('File formats Ingested');
        $fileFormatsIngested->labels(array_keys($fileFormatCount))->load(url('api/v1/stats/fileFormatsIngested'));
        $fileFormatsIngested->options([
            'legend' => [
                'display' => true,
                'position' => 'right',
                // 'onHover' => 'chartHoverHighlight()'
            ],
            // 'tooltips' => [
            //     'enabled' => true
            // ],
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
        $onlineDataIngested = 0;
        $bags = $user->bags;

        foreach ($bags as $bag)
        {
            if ($bag->status == 'complete')
            {
                $files = $bag->files;

                foreach ($files as $file)
                {
                    $onlineDataIngested += $file->filesize;
                }
            }
        }
        return $onlineDataIngested;
    }

    private function onlineAIPsIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        return $bags->count();
    }

    private function monthlyOnlineAIPsIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $monthlyAIPsIngested = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $creation_date = new \DateTime($bag->created_at);

            if ($creation_date > $oneYearAgo) {
                $monthlyAIPsIngested[$creation_date->format('n') - 1]++;
            }
        }

        return $this->arrayRearrangeCurrentMonthLast($monthlyAIPsIngested);
    }

    private function monthlyOnlineDataIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $monthlyDataIngested = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $creation_date = new \DateTime($bag->created_at);

            if ($creation_date > $oneYearAgo) {

                $files = $bag->files;

                foreach ($files as $file)
                {
                    $monthlyDataIngested[$creation_date->format('n') - 1] += $file->filesize;
                }
            }
        }
        return $this->arrayRearrangeCurrentMonthLast($monthlyDataIngested);
    }

    /**
     * Takes an array with 12 values and transforms it to an associative array arranged with current month last. The keys are three letter month names.
     *
     * @param  array  $inputArray Array of size 12, 0 will bre treated as Jan, 1 as Feb etc.
     * @return array 0 will be next month from today, 1 will be the month after etc, 11 will be this month.
     */
    private function arrayRearrangeCurrentMonthLast($inputArray)
    {
        $months = array(
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul ',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
            // 'January',
            // 'February',
            // 'March',
            // 'April',
            // 'May',
            // 'June',
            // 'July',
            // 'August',
            // 'September',
            // 'October',
            // 'November',
            // 'December',
        );

        $todaysMonth = date('n') - 1;
        $rearrangedArray = [];

        for ($i = $todaysMonth + 1; $i < 12; $i++)
        {
            $rearrangedArray[$months[$i]] = $inputArray[$i];
        }
        for ($i = 0; $i <= $todaysMonth; $i++)
        {
            $rearrangedArray[$months[$i]] = $inputArray[$i];
        }
        return $rearrangedArray;
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
