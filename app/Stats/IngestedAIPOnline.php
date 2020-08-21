<?php

namespace App\Stats;

class IngestedAIPOnline extends StatsModel
{
    protected $collection = 'ingested_aips_online';
    protected $dates = ['ingest_date'];
}
