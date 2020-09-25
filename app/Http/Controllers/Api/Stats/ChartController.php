<?php

namespace App\Http\Controllers\Api\Stats;

use App\Http\Controllers\Controller;
use App\Stats\ChartHelper;
use App\Stats\Colors;
use App\Stats\Dataset;
use App\Stats\Size;
use App\Stats\StatisticsData;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ChartController extends Controller
{
    private $stats;
    private $charts;

    public function __construct()
    {
        $this->stats = new StatisticsData();
        $this->charts = new ChartHelper();
    }

    public function monthlyIngestedAIPs()
    {
        return $this->onAuthenticated('monthly AIPs ingested', function ($user) {
            $data = $this->stats->monthlyIngested($user->id);
            $labels = array_map(function ($d) { return $d['month']; }, $data);
            $onlineAIPs = array_map(function ($d) { return $d['online']['aips']; }, $data);
            $offlineAIPs = array_map(function ($d) { return $d['offline']['aips']; }, $data);
            $chart = $this->charts->generate([
                new Dataset(__('dashboard.graphs.label.online'), Dataset::LineChart, Colors::LineBgOutside, $onlineAIPs),
                new Dataset(__('dashboard.graphs.label.offline'), Dataset::LineChart, Colors::LineBgInside, $offlineAIPs),
            ]);
            $chart->labels($labels);
            return $chart->api();
        });
    }

    public function monthlyIngestedData()
    {
        return $this->onAuthenticated('monthly data ingested', function ($user) {
            $data = $this->stats->monthlyIngested($user->id);
            $labels = array_map(function ($d) { return $d['month']; }, $data);
            $onlineData = array_map(function ($d) { return Size::convert(Size::GB, $d['online']['size']); }, $data);
            $offlineData = array_map(function ($d) { return Size::convert(Size::GB, $d['offline']['size']); }, $data);
            $chart = $this->charts->generate([
                new Dataset(__('dashboard.graphs.label.online'), Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset(__('dashboard.graphs.label.offline'), Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ]);
            $chart->labels($labels);
            return $chart->api();
        });
    }

    public function monthlyAccessedAIPs()
    {
        return $this->onAuthenticated('monthly AIPs accessed', function ($user) {
            $data = $this->stats->monthlyAccessed($user->id);
            $labels = array_map(function ($d) { return $d['month']; }, $data);
            $onlineAIPs = array_map(function ($d) { return $d['online']['aips']; }, $data);
            $offlineAIPs = array_map(function ($d) { return $d['offline']['aips']; }, $data);
            $chart = $this->charts->generate([
                new Dataset(__('dashboard.graphs.label.online'), Dataset::LineChart, Colors::LineBgOutside, $onlineAIPs),
                new Dataset(__('dashboard.graphs.label.offline'), Dataset::LineChart, Colors::LineBgInside, $offlineAIPs),
            ]);
            $chart->labels($labels);
            return $chart->api();
        });
    }

    public function monthlyAccessedData()
    {
        return $this->onAuthenticated('monthly data accessed', function ($user) {
            $data = $this->stats->monthlyAccessed($user->id);
            $labels = array_map(function ($d) { return $d['month']; }, $data);
            $onlineData = array_map(function ($d) { return Size::convert(Size::GB, $d['online']['size']); }, $data);
            $offlineData = array_map(function ($d) { return Size::convert(Size::GB, $d['offline']['size']); }, $data);
            $chart = $this->charts->generate([
                new Dataset(__('dashboard.graphs.label.online'), Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset(__('dashboard.graphs.label.offline'), Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ]);
            $chart->labels($labels);
            return $chart->api();
        });
    }

    // Daily statistics will be enabled after the 1.0 release (ref CON-748)
    // public function dailyOnlineAIPsIngested()
    // {
    //     return $this->onAuthenticated('daily online aips ingested', function ($user) {
    //         $onlineData = $this->stats->dailyOnlineAIPsIngested($user->id);
    //         $offlineData = $this->stats->dailyOfflineAIPsIngested($user->id);
    //         return $this->charts->generate([
    //             new Dataset('Online AIPs Ingested', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
    //             new Dataset('Offline AIPs Ingested', Dataset::LineChart, Colors::LineBgInside, $offlineData),
    //         ])->api();
    //     });
    // }
    
    // public function dailyOnlineDataIngested()
    // {
    //     return $this->onAuthenticated('daily online data ingested', function ($user) {
    //         $onlineData = $this->stats->dailyOnlineDataIngested($user->id);
    //         $offlineData = $this->stats->dailyOfflineDataIngested($user->id);
    //         return $this->charts->generate([
    //             new Dataset('Online Data Ingested', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
    //             new Dataset('Offline Data Ingested', Dataset::LineChart, Colors::LineBgInside, $offlineData),
    //         ])->api();
    //     });
    // }

    // public function dailyOnlineAIPsAccessed()
    // {
    //     $onlineData = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
    //     $offlineData = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
    //     return $this->charts->generate([
    //         new Dataset('Online AIPs Accessed', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
    //         new Dataset('Offline AIPs Accessed', Dataset::LineChart, Colors::LineBgInside, $offlineData),
    //     ])->api();
    // }

    // public function dailyOnlineDataAccessed()
    // {
    //     $onlineData = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
    //     $offlineData = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
    //     return $this->charts->generate([
    //         new Dataset('Online Data Accessed', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
    //         new Dataset('Offline Data Accessed', Dataset::LineChart, Colors::LineBgInside, $offlineData),
    //     ])->api();
    // }

    public function onlineFileFormatsIngested()
    {
        return $this->onAuthenticated('daily ingested file formats', function ($user) {
            return $this->stats->fileFormatsIngested($user->id);
        });
    }

    private function onAuthenticated($statsName, callable $action)
    {
        $message = 'User must be authenticated';
        if ($statsName != null && $statsName != '') $message = "$message to access $statsName";
        $user = Auth::user();
        if ($user == null) {
            return response()->json(['error' => 401, 'message' => "$message."], 401);
        }
        try {
            return $action($user);
        } catch (Throwable $e) {
            return response(['message' => $e->getMessage()], 400);
        }

    }
}
