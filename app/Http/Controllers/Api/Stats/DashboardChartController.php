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
        $monthlyOnlineAIPsIngested = $this->monthlyOnlineAIPsIngested(User::first());
        $chart = new TestChartJS;
        $chart->dataset('monthlyOnlineAIPsIngested', 'bar', array_values($monthlyOnlineAIPsIngested));
        return $chart->api();
    }

    public function monthlyOnlineDataIngestedEndpoint()
    {
        $monthlyOnlineDataIngested = $this->monthlyOnlineDataIngested(User::first());
        $chart = new TestChartJS;
        $chart->labels(array_keys($monthlyOnlineDataIngested));
        $chart->dataset('monthlyOnlineDataIngested', 'bar', array_values($monthlyOnlineDataIngested));

        return $chart->api();
    }

    public function fileFormatsIngestedEndpoint()
    {
        $fileFormatsIngested = $this->fileFormatsIngested(User::first());
        $chart = new TestChartJS;
        $chart->dataset('fileFormatsIngested', 'pie', array_values($fileFormatsIngested));
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
