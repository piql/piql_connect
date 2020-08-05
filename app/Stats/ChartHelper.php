<?php

namespace App\Stats;

use App\Charts\TestChartJS;
use Exception;

class ChartHelper
{

    /**
     * @param Dataset[] datasets
     */
    public function generate(array $datasets) {
        if(empty($datasets)) throw new Exception('dataset had no data');        
        $chart = new TestChartJS;
        foreach($datasets as $d) {
            $dataset = $chart->dataset($d->title, $d->type, $d->data);
            $dataset->backgroundColor($d->backgroundColor);
            $dataset->color(Colors::LineInside);
        }
        return $chart;
    }
}
