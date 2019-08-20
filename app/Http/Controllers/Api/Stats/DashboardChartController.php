<?php

namespace App\Http\Controllers\Api\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Charts\TestChartJS;
use App\User;

class DashboardChartController extends Controller
{
    public function monthlyOnlineAIPsIngestedEndpoint()
    {
        $monthlyOnlineAIPsIngested = array_values($this->monthlyOnlineAIPsIngested(User::first()));
        $monthlyOfflineAIPsIngested = array_map(function($val) { return round($val*(rand(1,9)/10)); }, $monthlyOnlineAIPsIngested);
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Ingested', 'line', $monthlyOnlineAIPsIngested);
        $dataset->backgroundColor(collect(['#8f005240']));
        $dataset->color(collect(['#8f0052']));
        $dataset = $chart->dataset('Offline AIPs Ingested', 'line', $monthlyOfflineAIPsIngested);
        $dataset->backgroundColor(collect(['#6a4b5b40']));
        $dataset->color(collect(['#6a4b5b']));
        return $chart->api();
        //8f0052
    }

    public function monthlyOnlineDataIngestedEndpoint()
    {
        $monthlyOnlineDataIngested = $this->monthlyOnlineDataIngested(User::first());
        $chart = new TestChartJS;
        $chart->labels(array_keys($monthlyOnlineDataIngested));
        $dataset = $chart->dataset('Online Data Ingested', 'line', array_values($monthlyOnlineDataIngested));
        $dataset->backgroundColor(collect(['#d7c5cf40']));
        $dataset->color(collect(['#b092a2']));
        $dataset = $chart->dataset('Offline Data Ingested', 'line', array_map(function($val) { return round($val*(rand(1, 9)/10), 2); }, array_values($monthlyOnlineDataIngested)));
        $dataset->backgroundColor(collect(['#b092a240']));
        $dataset->color(collect(['#6a4b5b']));

        return $chart->api();
    }

    public function monthlyOnlineAIPsAccessedEndpoint()
    {
        $monthlyOnlineAIPsAccessed = [42, 65, 31, 45, 12, 78, 56, 36, 15, 14, 48, 66];
        $monthlyOfflineAIPsAccessed = [7, 4, 0, 0, 2, 5, 0, 8, 1, 11, 5, 7];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Accessed', 'line', $monthlyOnlineAIPsAccessed);
        $dataset->backgroundColor(collect(['#f47c5740']));
        $dataset->color(collect(['#f47c57']));
        $dataset = $chart->dataset('Offline AIPs Accessed', 'line', $monthlyOfflineAIPsAccessed);
        $dataset->backgroundColor(collect(['#cc3b0d40']));
        $dataset->color(collect(['#cc3b0d']));
        return $chart->api();
    }

    public function monthlyOnlineDataAccessedEndpoint()
    {
        $monthlyOnlineDataAccessed = [140, 89, 45, 15, 78, 143, 120, 110, 90, 20, 50, 74];
        $monthlyOfflineDataAccessed = [5, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 3];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Accessed', 'line', $monthlyOnlineDataAccessed);
        $dataset->backgroundColor(collect(['#f47c5740']));
        $dataset->color(collect(['#f47c57']));
        $dataset = $chart->dataset('Offline AIPs Accessed', 'line', $monthlyOfflineDataAccessed);
        $dataset->backgroundColor(collect(['#cc3b0d40']));
        $dataset->color(collect(['#cc3b0d']));

        return $chart->api();
    }

    public function dailyOnlineAIPsIngestedEndpoint()
    {
        $dailyOnlineAIPsIngested = array_values($this->dailyOnlineAIPsIngested(User::first()));;
        $dailyOfflineAIPsIngested = array_map(function($val) { return round($val*(rand(1,9)/10)); }, $dailyOnlineAIPsIngested);
        $chart = new TestChartJS;
        $chart->dataset('Online AIPs Ingested', 'line', $dailyOnlineAIPsIngested);
        $chart->dataset('Offline AIPs Ingested', 'line', $dailyOfflineAIPsIngested);
        return $chart->api();
    }

    public function dailyOnlineDataIngestedEndpoint()
    {
        $dailyOnlineDataIngested = array_values($this->dailyOnlineDataIngested(User::first()));;
        $dailyOfflineDataIngested = array_map(function($val) { return round($val*(rand(1, 9)/10), 2); }, array_values($dailyOnlineDataIngested));
        $chart = new TestChartJS;
        $chart->dataset('Online Data Ingested', 'line', $dailyOnlineDataIngested);
        $chart->dataset('Offline Data Ingested', 'line', $dailyOfflineDataIngested);
        return $chart->api();
    }

    public function dailyOnlineAIPsAccessedEndpoint()
    {
        $dailyOnlineAIPsAccessed = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
        $dailyOfflineAIPsAccessed = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
        $chart = new TestChartJS;
        $chart->dataset('Online AIPs Accessed', 'line', $dailyOnlineAIPsAccessed);
        $chart->dataset('Offline AIPs Accessed', 'line', $dailyOfflineAIPsAccessed);
        return $chart->api();
    }

    public function dailyOnlineDataAccessedEndpoint()
    {
        $dailyOnlineDataAccessed = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
        $dailyOfflineDataAccessed = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
        $chart = new TestChartJS;
        $chart->dataset('Online AIPs Accessed', 'line', $dailyOnlineDataAccessed);
        $chart->dataset('Offline AIPs Accessed', 'line', $dailyOfflineDataAccessed);
        return $chart->api();
    }

    public function fileFormatsIngestedEndpoint()
    {
        $fileFormatsIngested = $this->fileFormatsIngested(User::first());
        $chart = new TestChartJS;
        $chart->dataset('fileFormatsIngested', 'pie', array_values($fileFormatsIngested))->backgroundcolor(collect([
            // '#000000',
            // '#12070a',
            // '#250e14',
            // '#37151e',
            // '#4a1c28',
            // '#5c2332',
            '#6f2a3c',
            '#813147',
            '#943851',
            '#a63f5b',
            '#aa405d',
            '#b94665',
            '#c05974',
            '#c76b84',
            '#ce7e93',
            '#d590a2',
            '#dca3b2',
            '#e3b5c1',
            '#eac8d1',
            '#f1dae0',
            '#f8edf0',
            '#ffffff',
        ]));



        // ->backgroundcolor(collect(['#8f0052', '#aa405d', '#c27484', '#deb3b9', '#d7c5cf', '#b092a2', '#6a4b5b', '#4d324d']));
        return $chart->api();



// $white: white;
// $black: black;
// $red:  red;
// $gray: grey;

// $color_main_brand: #cc3b0d;
// $color_background: '#4d324d';
// $color_background_dark: #30242c;
// $color_background_1: #6a4b5b;
// $color_background_1_transparent: #6a4b5b40;
// $color_background_2: #b092a2;
// $color_background_2_transparent: #b092a240;
// $color_background_3: #d7c5cf;
// $color_background_3_transparent: #d7c5cf40;
// $color_secondary: #8f0052;
// $color_secondary_1: #aa405d;
// $color_secondary_2: #c27484;
// $color_secondary_2_transparent: #c2748440;
// $color_secondary_3: #deb3b9;
// $color_secondary_3_transparent: #deb3b940;
// $color_signal: #ff194d;
// $color_fill: #808080;
// $color_fill_1: #ababab;
// $color_fill_2: #c7c7c7;
// $color_fill_2_transparent: #c7c7c740;
// $color_fill_3: #e3e3e3;
// $color_fill_3_transparent: #e3e3e340;
// $color_fill_dark: #605f5f;
// $color_fill_verydark: #403f3f;
    }

    private function monthlyOnlineAIPsIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $monthlyOnlineAIPsIngested = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $creation_date = new \DateTime($bag->created_at);

            if ($creation_date > $oneYearAgo) {
                $monthlyOnlineAIPsIngested[$creation_date->format('n') - 1]++;
            }
        }

        return $this->arrayRearrangeCurrentMonthLast($monthlyOnlineAIPsIngested);
    }

    private function monthlyOnlineDataIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $monthlyOnlineDataIngested = array_fill(0, 12, 0);

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
                    $monthlyOnlineDataIngested[$date->format('n') - 1] += $file->filesize / 1000000000; // In GB
                }
            }
        }

        for ($i = 0; $i < count($monthlyOnlineDataIngested); $i++)
        {
            $monthlyOnlineDataIngested[$i] = round($monthlyOnlineDataIngested[$i], 2);
        }
        return $this->arrayRearrangeCurrentMonthLast($monthlyOnlineDataIngested);
    }

    private function dailyOnlineAIPsIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $dailyOnlineAIPsIngested = array_fill(0, 30, 0);

        $thirtyDaysAgo = new \DateTime(date('Y-m-d'));
        $thirtyDaysAgo->modify('-29 days');

        $debugArray = [];

        foreach ($bags as $bag)
        {
            $creation_date = new \DateTime($bag->created_at);

            if ($creation_date > $thirtyDaysAgo) {

                $daysAgo = new \DateTime(date('Y-m-d'));
                $daysAgo = $daysAgo->diff($creation_date);
                $dailyOnlineAIPsIngested[$daysAgo->days + 1]++;
            }
        }
        return $dailyOnlineAIPsIngested;
    }

    private function dailyOnlineDataIngested($user)
    {
        $bags = $user->bags->where('status', 'complete');

        $dailyOnlineAIPsIngested = array_fill(0, 30, 0);

        $thirtyDaysAgo = new \DateTime(date('Y-m-d'));
        $thirtyDaysAgo->modify('-29 days');

        foreach ($bags as $bag)
        {
            $creation_date = new \DateTime($bag->created_at);

            if ($creation_date > $thirtyDaysAgo) {

                $files = $bag->files;

                foreach ($files as $file)
                {
                    $daysAgo = new \DateTime(date('Y-m-d'));
                    $daysAgo = $daysAgo->diff($creation_date);
                    $dailyOnlineAIPsIngested[$daysAgo->days + 1]+= $file->filesize / 1000000; // In MB
                }
            }
        }

        for ($i = 0; $i < count($dailyOnlineAIPsIngested); $i++)
        {
            $dailyOnlineAIPsIngested[$i] = round($dailyOnlineAIPsIngested[$i], 2);
        }

        return $dailyOnlineAIPsIngested;
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
}
