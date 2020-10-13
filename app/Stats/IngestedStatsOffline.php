<?php

namespace App\Stats;

class IngestedStatsOffline extends StatsModel
{
    protected $collection = 'ingested_stats_offline';
    protected $dates = ['ingest_date'];
}
