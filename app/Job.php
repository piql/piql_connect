<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class JobTransitionException extends \Exception {};

class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'name', 'status', 'owner'
    ];
    protected $appends = ['archive_objects', 'bucket_size', 'size'];

    // NOTE: States are not all that complex at the moment, but it will be extended later on to include
    //          - error handling
    //          - option to cancel/modify/restart a bucket (probably only relevant before a physical write has been initiated)
    //          - strict transitions (the current version allows skipping notifications from earlier steps in the last four states)

    protected const smConfig = [
        'states' => [
            'created',             // not full, bags can be added
            'closed',              // full, no more bags can be added
            'transferring',        // transferring to production facility
            'preparing',           // preparing data for writing
            'writing',             // printing/developing/verifying
            'storing',             // packaging/sending/placing physical reel in vault
            'stored',              // reels are securely placed in vault
        ],
        'transitions' => [
            'close' => [
                'from' => ['created'],
                'to' => 'closed',
            ],
            'piql_it' => [
                'from' => ['created', 'closed'],
                'to' => 'transferring',
            ],
            'transferred' => [
                'from' => ['transferring'],
                'to' => 'preparing',
            ],
            'prepared' => [
                'from' => ['transferring', 'preparing'],
                'to' => 'writing',
            ],
            'written' => [
                'from' => ['transferring', 'preparing', 'writing'],
                'to' => 'storing',
            ],
            'stored' => [
                'from' => ['transferring', 'preparing', 'writing', 'storing'],
                'to' => 'stored',
            ],
        ],
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(
            function( $model )
            {
                $model->uuid = Uuid::generate();
                $model->status = "created";
                self::jobStorage()->makeDirectory("{$model->uuid}/visual_files");
            }
        );
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

        if(!$job) {
            $job = Job::create([
                'name' => '',
                'owner' => $owner
            ]);
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
        return env('APP_INGEST_BUCKET_SIZE', 120*1000*1000*1000);
    }

    public function metadata()
    {
        return $this->morphMany('App\Metadata', 'parent');
    }

    public function visualFilesDir()
    {
        return self::jobStorage()->path("{$this->uuid}/visual_files");
    }

    public function jobFilesDir()
    {
        return self::jobStorage()->path("{$this->uuid}");
    }

    private static function jobStorage()
    {
        return Storage::disk('jobs');
    }

    public function canTransition($transition)
    {
        return !(array_search($this->status, Job::smConfig["transitions"][$transition]["from"]) === false);
    }

    public function applyTransition($transition, bool $force = false)
    {
        if(!isset(Job::smConfig['transitions'][$transition]))
        {
            throw new JobTransitionException("transition '{$transition}' doesn't exist");
        }

        if(!($this->canTransition($transition) || $force))
        {
            throw new JobTransitionException("transition '{$transition}' is not allowed from state '{$this->status}'");
        }

        if(!isset(Job::smConfig['transitions'][$transition]['to'])) {
            throw new JobTransitionException("No 'to' state for transition '{$transition}'' is defined");
        }

        $this->status = Job::smConfig['transitions'][$transition]['to'];
        return $this;
    }

    public static function transitions()
    {
        return Job::smConfig['transitions'];
    }

}
