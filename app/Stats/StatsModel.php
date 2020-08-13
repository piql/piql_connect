<?php

namespace App\Stats;

use Jenssegers\Mongodb\Eloquent\Model as MongoDbModel;

class StatsModel extends MongoDbModel
{
    protected $connection = 'mongodb';
}
