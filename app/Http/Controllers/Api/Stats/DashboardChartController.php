<?php

namespace App\Http\Controllers\Api\Stats;

use App\FileObject;
use App\Aip;
use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Charts\TestChartJS;
use App\User;
use Illuminate\Support\Facades\DB;

class DashboardChartController extends Controller
{
    private $color_cards = '#4d324d';
    private $color_chart_line_outside = '#6a4b5b';
    private $color_chart_line_outside_background = '#6a4b5b40';
    private $color_chart_line_inside = '#8f0052';
    private $color_chart_line_inside_background = '#8f005240';

   // Burgundy/pink
    private $color_chart_pie = [
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
    ];

    public function monthlyOnlineAIPsIngestedEndpoint()
    {
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'User must be authenticated to access daily ingested data.' ], 401);
        }

        $monthlyOnlineAIPsIngested = array_values($this->monthlyOnlineAIPsIngested( $currentUser ) );
        $monthlyOfflineAIPsIngested = array_map(function($val) { return round($val*(rand(1,9)/10)); }, $monthlyOnlineAIPsIngested);
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Ingested', 'line', $monthlyOnlineAIPsIngested);
        $dataset->backgroundColor($this->color_chart_line_outside_background);
        $dataset->color($this->color_chart_line_inside);
        $dataset = $chart->dataset('Offline AIPs Ingested', 'line', $monthlyOfflineAIPsIngested);
        $dataset->backgroundColor($this->color_chart_line_inside_background);
        $dataset->color($this->color_chart_line_inside);
        return $chart->api();
    }

    public function monthlyOnlineDataIngestedEndpoint()
    {
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'User must be authenticated to access daily ingested data.' ], 401);
        }

        $monthlyOnlineDataIngested = $this->monthlyOnlineDataIngested( $currentUser );
        $chart = new TestChartJS;
        $chart->labels(array_keys($monthlyOnlineDataIngested));
        $dataset = $chart->dataset('Online Data Ingested (GBs)', 'line', array_values($monthlyOnlineDataIngested));
        $dataset->backgroundColor($this->color_chart_line_outside_background);
        $dataset->color($this->color_chart_line_inside);
        $dataset = $chart->dataset('Offline Data Ingested (GBs)', 'line', array_map(function($val) { return round($val*(rand(1, 9)/10), 2); }, array_values($monthlyOnlineDataIngested)));
        $dataset->backgroundColor($this->color_chart_line_inside_background);
        $dataset->color($this->color_chart_line_inside);

        return $chart->api();
    }

    public function monthlyOnlineAIPsAccessedEndpoint()
    {
        $monthlyOnlineAIPsAccessed = [42, 65, 31, 45, 12, 78, 56, 36, 15, 14, 48, 66];
        $monthlyOfflineAIPsAccessed = [7, 4, 0, 0, 2, 5, 0, 8, 1, 11, 5, 7];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online AIPs Accessed', 'line', $monthlyOnlineAIPsAccessed);
        $dataset->backgroundColor($this->color_chart_line_outside_background);
        $dataset->color($this->color_chart_line_inside);
        $dataset = $chart->dataset('Offline AIPs Accessed', 'line', $monthlyOfflineAIPsAccessed);
        $dataset->backgroundColor($this->color_chart_line_inside_background);
        $dataset->color($this->color_chart_line_inside);
        return $chart->api();
    }

    public function monthlyOnlineDataAccessedEndpoint()
    {
        $monthlyOnlineDataAccessed = [140, 89, 45, 15, 78, 143, 120, 110, 90, 20, 50, 74];
        $monthlyOfflineDataAccessed = [5, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 3];
        $chart = new TestChartJS;
        $dataset = $chart->dataset('Online Data Accessed (GBs)', 'line', $monthlyOnlineDataAccessed);
        $dataset->backgroundColor($this->color_chart_line_outside_background);
        $dataset->color($this->color_chart_line_inside);
        $dataset = $chart->dataset('Offline Data Accessed (GBs)', 'line', $monthlyOfflineDataAccessed);
        $dataset->backgroundColor($this->color_chart_line_inside_background);
        $dataset->color($this->color_chart_line_inside);

        return $chart->api();
    }

    public function dailyOnlineAIPsIngestedEndpoint()
    {
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'User must be authenticated to access daily ingested data.' ], 401);
        }

        $dailyOnlineAIPsIngested = array_values($this->dailyOnlineAIPsIngested( $currentUser ) );
        $dailyOfflineAIPsIngested = array_map(function($val) { return round($val*(rand(1,9)/10)); }, $dailyOnlineAIPsIngested);
        $chart = new TestChartJS;
        $chart->dataset('Online AIPs Ingested', 'line', $dailyOnlineAIPsIngested);
        $chart->dataset('Offline AIPs Ingested', 'line', $dailyOfflineAIPsIngested);
        return $chart->api();
    }

    public function dailyOnlineDataIngestedEndpoint()
    {
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'User must be authenticated to access daily ingested data.' ], 401);
        }

        $dailyOnlineDataIngested = array_values( $this->dailyOnlineDataIngested( $currentUser ) );
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
        $currentUser = Auth::user();
        if( $currentUser == null ) {
            return response()->json([ 'error' => 401, 'message' => 'User must be authenticated to access daily ingested data.' ], 401);
        }

        $fileFormatsIngested = $this->fileFormatsIngested( $currentUser );
        return $fileFormatsIngested;
    }

    private function monthlyOnlineAIPsIngested($user)
    {
        return $this->arrayRearrangeCurrentMonthLast(
            collect(range(0,11))->map(function($obj) {
                return Aip::whereBetween("created_at", [
                        (new \DateTime(date("Y-m")))->modify((-$obj)." month"),
                        (new \DateTime(date("Y-m")))->modify((1-$obj)." month")
                    ])->count();
            })->toArray()
        );
    }

    private function monthlyOnlineDataIngested($user)
    {
        return $this->arrayRearrangeCurrentMonthLast(
            collect(range(0,11))->map(function($obj) {
                $val = File::whereBetween("created_at", [
                        (new \DateTime(date("Y-m")))->modify((-$obj)." month"),
                        (new \DateTime(date("Y-m")))->modify((1-$obj)." month")
                    ])->sum("filesize"); 
                
                    return round(($val/1000000000),2); //in GB


            })->toArray()
        );
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
            ->limit(7)
            ->get()
            ->flatMap(function($obj) use(&$count) {
                $count -= $obj['count(mime_type)'];
                return [$obj->mime_type => $obj['count(mime_type)']];
            });
        if($count) {
            $fileFormats['other'] = $count;
        }

        return \Response::json($fileFormats);
    }
}
