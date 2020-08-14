<?php

namespace App\Stats;

class IngestedDataOnline extends StatsModel
{
    protected $collection = 'ingested_data_online';
    protected $dates = ['ingest_date'];
}
