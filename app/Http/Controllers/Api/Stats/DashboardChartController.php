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
        $dataset->backgroundColor(collect(['#6a4b5b40']));
        $dataset->color(collect(['#6a4b5b']));
        $dataset = $chart->dataset('Offline AIPs Ingested', 'line', $monthlyOfflineAIPsIngested);
        $dataset->backgroundColor(collect(['#8f005240']));
        $dataset->color(collect(['#8f0052']));
        return $chart->api();
    }

    public function monthlyOnlineDataIngestedEndpoint()
    {
        $monthlyOnlineDataIngested = $this->monthlyOnlineDataIngested(User::first());
        $chart = new TestChartJS;
        $chart->labels(array_keys($monthlyOnlineDataIngested));
        $dataset = $chart->dataset('Online Data Ingested', 'line', array_values($monthlyOnlineDataIngested));
        $dataset->backgroundColor(collect(['#6a4b5b40']));
        $dataset->color(collect(['#6a4b5b']));
        $dataset = $chart->dataset('Offline Data Ingested', 'line', array_map(function($val) { return round($val*(rand(1, 9)/10), 2); }, array_values($monthlyOnlineDataIngested)));
        $dataset->backgroundColor(collect(['#8f005240']));
        $dataset->color(collect(['#8f0052']));

        return $chart->api();
    }

    public function monthlyOnlineAIPsAccessedEndpoint()
    {
        $monthlyOnlineAIPsAccessed = [42, 65, 31, 45, 12, 78, 56, 36, 15, 14, 48, 66];
        $monthlyOfflineAIPsAccessed = [7, 4, 0, 0, 2, 5, 0, 8, 1, 11, 5, 7];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Accessed', 'line', $monthlyOnlineAIPsAccessed);
        $dataset->backgroundColor(collect(['#6a4b5b40']));
        $dataset->color(collect(['#6a4b5b']));
        $dataset = $chart->dataset('Offline AIPs Accessed', 'line', $monthlyOfflineAIPsAccessed);
        $dataset->backgroundColor(collect(['#8f005240']));
        $dataset->color(collect(['#8f0052']));
        return $chart->api();
    }

    public function monthlyOnlineDataAccessedEndpoint()
    {
        $monthlyOnlineDataAccessed = [140, 89, 45, 15, 78, 143, 120, 110, 90, 20, 50, 74];
        $monthlyOfflineDataAccessed = [5, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 3];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Accessed', 'line', $monthlyOnlineDataAccessed);
        $dataset->backgroundColor(collect(['#6a4b5b40']));
        $dataset->color(collect(['#6a4b5b']));
        $dataset = $chart->dataset('Offline AIPs Accessed', 'line', $monthlyOfflineDataAccessed);
        $dataset->backgroundColor(collect(['#8f005240']));
        $dataset->color(collect(['#8f0052']));

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
            // Purple
            '#4d324d',
            '#5d3c5d',
            '#6c476c',
            '#7b517b',
            '#8b5b8b',
            '#9a659a',
            '#a474a4',
            '#ae84ae',
            '#b893b8',
            '#c3a2c3',
            '#cdb2cd',
            '#d7c1d7',
            '#e1d1e1',
            '#ebe0eb',
            '#f5f0f5',

            // Burgundy/pink
            // '#6f2a3c',
            // '#813147',
            // '#943851',
            // '#a63f5b',
            // '#aa405d',
            // '#b94665',
            // '#c05974',
            // '#c76b84',
            // '#ce7e93',
            // '#d590a2',
            // '#dca3b2',
            // '#e3b5c1',
            // '#eac8d1',
            // '#f1dae0',
            // '#f8edf0',
            // '#ffffff',
        ]));
        return $chart->api();
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
