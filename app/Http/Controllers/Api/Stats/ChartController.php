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

    public function onlineAIPsIngested()
    {
        return $this->onAuthenticated('online aips ingested', function ($user) {
            return $this->stats->latestOnlineAIPsIngested($user->getIdAttribute());
        });
    }

    public function monthlyOnlineAIPsIngested()
    {
        return $this->onAuthenticated('monthly online aips ingested', function ($user) {
            $onlineData = $this->stats->monthlyOnlineAIPsIngested($user->getIdAttribute());
            $offlineData = array_map(function ($d) {  return round($d * (rand(1, 9) / 10));  }, $onlineData);
            return $this->charts->generate([
                new Dataset('Online AIPs Ingested', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset('Offline AIPs Ingested', Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ])->api();
        });
    }

    public function monthlyOnlineDataIngested()
    {
        return $this->onAuthenticated('monthly online data ingested', function ($user) {
            $data = $this->stats->monthlyOnlineDataIngested($user->getIdAttribute());
            $labels = array_map(function ($d) { return $d['month']; }, $data);
            $onlineData = array_map(function ($d) { return Size::convert(Size::GB, $d['size']); }, $data);
            $offlineData = array_map(function ($d) { return round($d*(rand(1, 9)/10), 2); }, $onlineData);
            $chart = $this->charts->generate([
                new Dataset('Online Data Ingested (GBs)', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset('Offline Data Ingested (GBs)', Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ]);
            $chart->labels($labels);
            return $chart->api();
        });
    }

    public function monthlyOnlineAIPsAccessed()
    {
        $onlineData = [42, 65, 31, 45, 12, 78, 56, 36, 15, 14, 48, 66];
        $offlineData = [7, 4, 0, 0, 2, 5, 0, 8, 1, 11, 5, 7];
        return $this->charts->generate([
            new Dataset('Online AIPs Accessed', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
            new Dataset('Offline AIPs Accessed', Dataset::LineChart, Colors::LineBgInside, $offlineData),
        ])->api();
    }

    public function monthlyOnlineDataAccessed()
    {
        $onlineData = [140, 89, 45, 15, 78, 143, 120, 110, 90, 20, 50, 74];
        $offlineData = [5, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 3];
        return $this->charts->generate([
            new Dataset('Online Data Accessed (GBs)', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
            new Dataset('Offline Data Accessed (GBs)', Dataset::LineChart, Colors::LineBgInside, $offlineData),
        ])->api();
    }

    public function dailyOnlineAIPsIngested()
    {
        return $this->onAuthenticated('daily online aips ingested', function ($user) {
            $onlineData = $this->stats->dailyOnlineAIPsIngested($user->getIdAttribute());
            $offlineData = array_map(function ($d) { return round($d * (rand(1, 9) / 10)); }, $onlineData);
            return $this->charts->generate([
                new Dataset('Online AIPs Ingested', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset('Offline AIPs Ingested', Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ])->api();
        });
    }
    
    public function dailyOnlineDataIngested()
    {
        return $this->onAuthenticated('daily online data ingested', function ($user) {
            $onlineData = $this->stats->dailyOnlineDataIngested($user->getIdAttribute());
            $offlineData = array_map(function ($d) { return round($d * (rand(1, 9) / 10)); }, $onlineData);
            return $this->charts->generate([
                new Dataset('Online Data Ingested', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
                new Dataset('Offline Data Ingested', Dataset::LineChart, Colors::LineBgInside, $offlineData),
            ])->api();
        });
    }

    public function dailyOnlineAIPsAccessed()
    {
        $onlineData = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
        $offlineData = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
        return $this->charts->generate([
            new Dataset('Online AIPs Accessed', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
            new Dataset('Offline AIPs Accessed', Dataset::LineChart, Colors::LineBgInside, $offlineData),
        ])->api();
    }

    public function dailyOnlineDataAccessed()
    {
        $onlineData = [10, 2, 12, 1, 15, 9, 5, 7, 10, 4, 7, 6, 1, 11, 14, 9, 15, 2, 11, 8, 7, 3, 4, 9, 3, 10, 13, 2, 8, 3];
        $offlineData = [1, 0, 4, 1, 1, 0, 0, 4, 0, 4, 2, 5, 3, 3, 4, 5, 5, 0, 1, 2, 0, 5, 1, 2, 5, 0, 3, 1, 1, 3];
        return $this->charts->generate([
            new Dataset('Online Data Accessed', Dataset::LineChart, Colors::LineBgOutside, $onlineData),
            new Dataset('Offline Data Accessed', Dataset::LineChart, Colors::LineBgInside, $offlineData),
        ])->api();
    }

    public function onlineFileFormatsIngested()
    {
        return $this->onAuthenticated('daily ingested file formats', function ($user) {
            return $this->stats->fileFormatsIngested($user->getIdAttribute());
        });
    }

    private function onAuthenticated($statsName = null, callable $action)
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
