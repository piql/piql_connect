<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'name', 'status', 'owner'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating( function( $model )
        {
            $model->uuid = Uuid::generate();
            $model->status = "created";
        });
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function bags()
    {
        return $this->belongsToMany('App\Bag');
    }

    public static function currentJob($owner)
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $job = \App\Job::where('status', '=', 'preparing')->where('owner', '=', $owner)->latest()->first();

        if($job == null) {
            $job = new Job();
            $job->name = Carbon::now()->format("YmdHis");
            $job->owner = $owner;
            $job->status = 'preparing';
            $job->save();
        }

        return $job;
    }

}
