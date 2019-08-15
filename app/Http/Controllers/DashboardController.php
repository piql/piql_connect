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
        $fileFormatCount = $this->fileFormatsIngested(auth()->user());
        $onlineDataIngested = $this->onlineDataIngested(auth()->user());
        
        $infoArray['onlineDataIngested'] = $this->byteToMetricbyte($onlineDataIngested);
        $infoArray['offlineDataIngested'] = $this->byteToMetricbyte(23 * 110000000000);
        $infoArray['onlineAIPsIngested'] = $this->onlineAIPsIngested(auth()->user());
        $infoArray['oflineAIPsIngested'] = '' . (23 * 55); 
        $infoArray['offlineReelsCount'] = '23';
        $infoArray['offlinePagesCount'] = '' . (23 * 3 * 8000) . ' pages';

        $monthlyOnlineAIPsIngested = new TestChartJS;
        $monthlyOfflineAIPsIngested = new TestChartJS;
        $monthlyOnlineAIPsAccessed = new TestChartJS;
        $monthlyOfflineAIPsAccessed = new TestChartJS;
        $monthlyOnlineDataIngested = new TestChartJS;
        $monthlyOfflineDataIngested = new TestChartJS;
        $monthlyOnlineDataAccessed = new TestChartJS;
        $monthlyOfflineDataAccessed = new TestChartJS;
        $dailyOnlineAIPsIngested = new TestChartJS;
        $dailyOfflineAIPsIngested = new TestChartJS;
        $dailyOnlineAIPsAccessed = new TestChartJS;
        $dailyOfflineAIPsAccessed = new TestChartJS;
        $dailyOnlineDataIngested = new TestChartJS;
        $dailyOfflineDataIngested = new TestChartJS;
        $dailyOnlineDataAccessed = new TestChartJS;
        $dailyOfflineDataAccessed = new TestChartJS;
        $fileFormatsIngested = new TestChartJS;
        

        $monthlyOnlineAIPsIngested->title('Monthly AIPs Ingested')->load(url('api/v1/stats/monthlyOnlineAIPsIngested'));
        $monthlyOnlineAIPsIngested->labels(array_keys($last12Months));
        $monthlyOnlineAIPsIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ]
        ]); 
  
        $monthlyOfflineAIPsIngested->title('Monthly Offline AIPs Ingested');
        $monthlyOfflineAIPsIngested->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOfflineAIPsIngested->dataset('monthly-offline-aips-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOfflineAIPsIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOnlineAIPsAccessed->title('monthlyOnlineAIPsAccessed');
        $monthlyOnlineAIPsAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOnlineAIPsAccessed->dataset('monthly-online-aips-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOnlineAIPsAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOfflineAIPsAccessed->title('monthlyOfflineAIPsAccessed');
        $monthlyOfflineAIPsAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOfflineAIPsAccessed->dataset('monthly-offline-aips-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOfflineAIPsAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOnlineDataIngested->title('Monthly data Ingested');
        $monthlyOnlineDataIngested->labels(array_keys($last12Months))->load(url('api/v1/stats/monthlyOnlineDataIngested'));
        $monthlyOnlineDataIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOfflineDataIngested->title('monthlyOfflineDataIngested');
        $monthlyOfflineDataIngested->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOfflineDataIngested->dataset('monthly-offline-data-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOfflineDataIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOnlineDataAccessed->title('monthlyOnlineDataAccessed');
        $monthlyOnlineDataAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOnlineDataAccessed->dataset('monthly-online-data-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOnlineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $monthlyOfflineDataAccessed->title('monthlyOfflineDataAccessed');
        $monthlyOfflineDataAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOfflineDataAccessed->dataset('monthly-offline-data-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOfflineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOnlineAIPsIngested->title('dailyOnlineAIPsIngested');
        $dailyOnlineAIPsIngested->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOnlineAIPsIngested->dataset('daily-online-aips-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOnlineAIPsIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOfflineAIPsIngested->title('dailyOfflineAIPsIngested');
        $monthlyOnlineDataAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $monthlyOnlineDataAccessed->dataset('daily-offline-aips-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $monthlyOnlineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOnlineAIPsAccessed->title('dailyOnlineAIPsAccessed');
        $dailyOnlineAIPsAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOnlineAIPsAccessed->dataset('daily-online-aips-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOnlineAIPsAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOfflineAIPsAccessed->title('dailyOfflineAIPsAccessed');
        $dailyOfflineAIPsAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOfflineAIPsAccessed->dataset('daily-offline-aips-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOfflineAIPsAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOnlineDataIngested->title('dailyOnlineDataIngested');
        $dailyOnlineDataIngested->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOnlineDataIngested->dataset('daily-online-data-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOnlineDataIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOfflineDataIngested->title('dailyOfflineDataIngested');
        $dailyOfflineDataIngested->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOfflineDataIngested->dataset('daily-offline-data-ingested', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOfflineDataIngested->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOnlineDataAccessed->title('dailyOnlineDataAccessed');
        $dailyOnlineDataAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOnlineDataAccessed->dataset('daily-online-data-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOnlineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $dailyOfflineDataAccessed->title('dailyOfflineDataAccessed');
        $dailyOfflineDataAccessed->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $dailyOfflineDataAccessed->dataset('daily-offline-data-accessed', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $dailyOfflineDataAccessed->options([
            'legend' => [
                'display' => false
            ],
            'tooltips' => [
                'mode' => 'x',
                'intersect' => false
            ],
        ]);

        $fileFormatsIngested->title('File formats Ingested');
        $fileFormatsIngested->labels(array_keys($fileFormatCount))->load(url('api/v1/stats/fileFormatsIngested'));
        $fileFormatsIngested->options([
            'legend' => [
                'display' => false
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
            'monthlyOfflineAIPsIngested',
            'monthlyOnlineAIPsAccessed',
            'monthlyOfflineAIPsAccessed',
            'monthlyOnlineDataIngested',
            'monthlyOfflineDataIngested',
            'monthlyOnlineDataAccessed',
            'monthlyOfflineDataAccessed',
            'dailyOnlineAIPsIngested',
            'dailyOfflineAIPsIngested',
            'dailyOnlineAIPsAccessed',
            'dailyOfflineAIPsAccessed',
            'dailyOnlineDataIngested',
            'dailyOfflineDataIngested',
            'dailyOnlineDataAccessed',
            'dailyOfflineDataAccessed',
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
}
