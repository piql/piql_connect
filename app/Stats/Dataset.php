<?php

namespace App\Stats;

class Dataset
{
    public $title;
    public $type;
    public $data;

    public $backgroundColor;

    const LineChart = 'line';
    const PieChart  = 'pie';

    public function __construct($title, $type, $bgColor, array $data)
    {
        $this->title = $title;
        $this->type = $type;
        $this->data = $data;
        $this->backgroundColor = $bgColor;
    }
}
