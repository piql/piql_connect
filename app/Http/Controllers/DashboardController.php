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

        $months = $this->arrayRearrangeCurrentMonthLast(array_fill(0, 12, 0));
        $fileFormatCount = $this->fileFormatsStored(auth()->user());
        $infoArray['sizeOfStoredAIPs'] = $this->sizeOfStoredAIPs(auth()->user());
        $infoArray['sizeOfStoredAIPs'] = $this->byteToMetricbyte($infoArray['sizeOfStoredAIPs']);
        $infoArray['numberOfStoredAIPs'] = $this->numberOfStoredAIPs(auth()->user());

        $chart1 = new TestChartJS;
        $chart2 = new TestChartJS;
        $chart3 = new TestChartJS;
        $chart4 = new TestChartJS;

        $chart1->title('Monthly AIPs stored')->load(url('api/v1/stats/monthly-aips-stored'));
        $chart1->labels(array_keys($months));
        $chart1->options([
                'legend' => [
                    'display' => false // or false, depending on what you want.
                ],
                'tooltips' => [
                    'mode' => 'x',
                    'intersect' => false
                ]
            ]);
        // $chart1->dataset('monthly-aips-stored', 'bar', array_values($monthlyStoredAIPsCount));
        // $chart1->labels(['test1', 'test2', 'test3'])->load($api);

        $chart2->title('Monthly data stored');
        $chart2->labels(array_keys($months))->load(url('api/v1/stats/monthly-data-stored'));
        // $chart2->dataset('monthly-data-stored', 'bar', array_values($monthlyStoredAIPsSize));
        $chart2->options([
                'legend' => [
                    'display' => false
                ],
                'tooltips' => [
                    'mode' => 'x',
                    'intersect' => false
                ],
                'scales' => [
                    'display' => false
                ]
            ]);

        $chart3->title('File formats');
        $chart3->labels(array_keys($fileFormatCount))->load(url('api/v1/stats/file-formats'));
        // $chart3->dataset('file-formats', 'pie', array_values($fileFormatCount));
        $chart3->options([
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

        // $chart3->title('Online space');
        // $chart3->labels(['Used', 'Unused']);
        // $chart3->dataset('online-space-usage', 'doughnut', [643, (750-643)]);
        // $chart3->displayLegend(False);

        $chart4->title('Daily access requets');
        $chart4->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $chart4->dataset('daily-access-requests', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $chart4->displayLegend(False);
        // $chart4->dataset('My dataset 2', 'line', [4, 3, 2, 1]);

        return view('dashboard', compact('chart1', 'chart2', 'chart3', 'chart4', 'infoArray'));
    }
    private function sizeOfStoredAIPs($user)
    {
        $totalSize = 0;
        $bags = $user->bags;

        foreach ($bags as $bag)
        {
            if ($bag->status == 'complete')
            {
                $files = $bag->files;

                foreach ($files as $file)
                {
                    $totalSize += $file->filesize;
                }
            }
        }
        return $totalSize;
    }

    private function numberOfStoredAIPs($user)
    {
        $bags = $user->bags->where('status', 'complete');

        return $bags->count();
    }

    private function monthlyStoredAIPsCount($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $storedAIPsCount = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $date = new \DateTime($bag->created_at);

            if ($date > $oneYearAgo) {
                $storedAIPsCount[$date->format('n') - 1]++;
            }
        }

        return $this->arrayRearrangeCurrentMonthLast($storedAIPsCount);
    }

    private function monthlyStoredAIPsSize($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $storedAIPsSize = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $date = new \DateTime($bag->created_at);

            if ($date > $oneYearAgo) {

                $files = $bag->files;

                foreach ($files as $file)
                {
                    $storedAIPsSize[$date->format('n') - 1] += $file->filesize;
                }
            }
        }
        return $this->arrayRearrangeCurrentMonthLast($storedAIPsSize);
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
        $arrangedArray = [];

        for ($i = $todaysMonth + 1; $i < 12; $i++)
        {
            $arrangedArray[$months[$i]] = $inputArray[$i];
        }
        for ($i = 0; $i <= $todaysMonth; $i++)
        {
            $arrangedArray[$months[$i]] = $inputArray[$i];
        }
        return $arrangedArray;
    }

    private function fileFormatsStored($user)
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
