<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class TestChartJS extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        return $this->options([
            'legend' => [
                'display' => false,
            ],
            'tooltips' => [
                'mode' => 'index',
                'intersect' => false
            ],
            'title' => [
                 'display'    => true,
                 'fontFamily' => 'Agenda',
                 'fontSize'   => 18,
                 'fontColor'  => '#605f5f',
            ],
        ]);
    }
}
