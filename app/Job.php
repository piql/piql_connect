<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Log;


class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'name', 'status', 'owner'
    ];
    protected $appends = ['archive_objects', 'bucket_size', 'size'];

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
        return $this->belongsToMany('App\Bag')->using('App\BagJob');
    }

    public function aips()
    {
        return $this->morphedByMany(Aip::class, 'archivable', null, 'archive_id');
    }

    public static function currentJob($owner)
    {
        Log::info("currentJob() owner".$owner);
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $job = \App\Job::where('status', '=', 'created')->where('owner', '=', $owner)->latest()->first();
        Log::info("currentJob() ".$job);

        if($job == null) {
            $job = new Job();
            $job->name = "";
            $job->owner = $owner;
            $job->save();
        }

        return $job;
    }

    public function getArchiveObjectsAttribute() {
        return $this->aips()->count();
    }

    public function getSizeAttribute() {
        return $this->aips()->sum("size");
    }

    public function getBucketSizeAttribute()
    {
        return env('APP_INGEST_BUCKET_SIZE', 150*1000*1000*1000);
    }

    public function metadata()
    {
        return $this->morphMany('App\Metadata', 'parent');
    }

}
