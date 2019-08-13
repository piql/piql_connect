<?php

namespace App\Http\Controllers\Api\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Charts\TestChartJS;
use App\User;

class DashboardChartController extends Controller
{
    public function monthlyAIPsStored()
    {
        $monthlyStoredAIPsCount = $this->monthlyStoredAIPsCount(User::first());
        $chart = new TestChartJS;
        $chart->dataset('monthly-aips-stored', 'bar', array_values($monthlyStoredAIPsCount));
        return $chart->api();
    }

    public function monthlyDataStored()
    {
        $monthlyStoredAIPsSize = $this->monthlyStoredAIPsSize(User::first());
        $chart = new TestChartJS;
        $chart->labels(array_keys($monthlyStoredAIPsSize));
        $chart->dataset('monthly-aips-stored', 'bar', array_values($monthlyStoredAIPsSize));

        return $chart->api();
    }

    public function fileFormats()
    {
        $fileFormatsStored = $this->fileFormatsStored(User::first());
        $chart = new TestChartJS;
        $chart->dataset('monthly-aips-stored', 'pie', array_values($fileFormatsStored));
        return $chart->api();
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
                    $storedAIPsSize[$date->format('n') - 1] += $file->filesize / 1000000000; // In GB
                }
            }
        }

        for ($i = 0; $i < count($storedAIPsSize); $i++)
        {
            $storedAIPsSize[$i] = round($storedAIPsSize[$i], 2);
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
}
