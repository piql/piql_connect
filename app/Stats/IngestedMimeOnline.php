<?php

namespace App\Stats;

class IngestedMimeOnline extends StatsModel
{
    protected $collection = 'ingested_file_formats';
    protected $dates = ['recorded_at'];
}
