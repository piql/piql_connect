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

    private function storedAIPsCount($user)
    {
        $bags = $user->bags;

        $storedAIPsCount = array_fill(0, 12, 0);

        $oneYearAgo = new \DateTime(date('Y-m-d'));
        $oneYearAgo->modify('-1 year');
        $oneYearAgo->modify('first day of next month');

        foreach ($bags as $bag)
        {
            $date = new \DateTime($bag->created_at);

            if ($date > $oneYearAgo && $bag->status == "complete") {
                $storedAIPsCount[$date->format("n") - 1]++;
            }
        }

        return $storedAIPsCount;
    }

    private function monthlyStoredAIPs($user)
    {
        $storedAIPsCount = $this->storedAIPsCount($user);

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

        $todaysMonth = date("n") - 1;
        $arrangedAIPCount = [];

        for ($i = $todaysMonth + 1; $i < 12; $i++)
        {
            $arrangedAIPCount[$months[$i]] = $storedAIPsCount[$i];
        }
        for ($i = 0; $i <= $todaysMonth; $i++)
        {
            $arrangedAIPCount[$months[$i]] = $storedAIPsCount[$i];
        }
        return $arrangedAIPCount;
    }

    private function fileFormatsStored($user)
    {
        $bags = $user->bags;

        $fileFormats = [];

        foreach ($bags as $bag)
        {
            if ($bag->status == "complete")
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
        }
        arsort($fileFormats);
        return $fileFormats;
    }

    public function showDashboard()
    {
        Log::info("showDashboard");

        $storedAIPsCount = $this->monthlyStoredAIPs(auth()->user());
        $fileFormatCount = $this->fileFormatsStored(auth()->user());

        $chart1 = new TestChartJS;
        $chart2 = new TestChartJS;
        $chart3 = new TestChartJS;
        $chart4 = new TestChartJS;

        $chart1->title("Monthly AIPs stored");
        $chart1->labels(array_keys($storedAIPsCount));
        $chart1->dataset('monthyl-aips-stored', 'bar', array_values($storedAIPsCount));
        $chart1->title("Monthly AIPs stored");
        $chart1->displayLegend(False);

        $chart2->title("File formats");
        $chart2->labels(array_keys($fileFormatCount));
        $chart2->dataset('file-formats', 'pie', array_values($fileFormatCount));
        $chart2->displayLegend(False);

        $chart3->title("Online space");
        $chart3->labels(['Used', 'Unused']);
        $chart3->dataset('online-space-usage', 'doughnut', [643, (750-643)]);
        $chart3->displayLegend(False);

        $chart4->title("Daily access requets");
        $chart4->labels(['30', '29', '27', '26', '25', '24', '23', '22', '21', '20', '19', '18', '17', '16', '15', '14', '13', '13', '12', '12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1']);
        $chart4->dataset('daily-access-requests', 'line', [17, 5, 8, 19, 18, 2, 3, 15, 1, 19,3, 12, 12, 12, 1, 8, 1, 10, 13, 8, 14, 8, 4, 16, 8, 18, 14, 4, 18, 12]);
        $chart4->displayLegend(False);
        // $chart4->dataset('My dataset 2', 'line', [4, 3, 2, 1]);

        return view('dashboard', compact('chart1', 'chart2', 'chart3', 'chart4', ));
    }
}
