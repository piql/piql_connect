<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use App\User;
use App\StorageProperties;
class BagTransitionException extends \Exception {};

class Bag extends Model
{
    protected $table = 'bags';
    protected $fillable = [
        'name', 'status', 'owner' // todo: remove 'status'
    ];

    protected $smConfig = [
        'states' => [
            'empty',               // empty
            'open',                // files can be added
            'closed',              // no more files can be added
            'creating_bag',        // packing files in a bag
            'move_to_outbox',      // moving bag to outbox
            'initiate_transfer',   // initiate transfer to AM
            'approve_transfer',    // approving the transfer
            'transferring',        // transferring
            'ingesting',           // ingesting
            'complete',            // bag is stored in AM
            'error',               // bag encountered an error
        ],
        'transitions' => [
            'add_files' => [
                'from' => ['empty'],
                'to' => 'open',
            ],
            'close' => [
                'from' => ['empty', 'open'],
                'to' => 'closed',
            ],
            'bag_files' => [
                'from' => ['closed'],
                'to' => 'bag_files',
            ],
            'move_to_outbox' => [
                'from' => ['bag_files'],
                'to' => 'move_to_outbox',
            ],
            'initiate_transfer' => [
                'from' => ['move_to_outbox'],
                'to' => 'initiate_transfer',
            ],
            'approve_transfer' => [
                'from' => ['initiate_transfer', 'approve_transfer'],
                'to' => 'approve_transfer',
            ],
            'transferring' => [
                'from' => ['transferring','approve_transfer'],
                'to' => 'transferring',
            ],
            'ingesting' => [
                'from' => ['ingesting','transferring'],
                'to' => 'ingesting',
            ],
            'complete' => [
                'from' => ['ingesting'],
                'to' => 'complete',
            ],
            'error' => [
                'from' => [
                    'empty',
                    'open',
                    'closed',
                    'bag_files',
                    'move_to_outbox',
                    'initiate_transfer',
                    'approve_transfer',
                    'transferring',
                    'ingesting',
                    'complete'],
                'to' => 'error',
            ],
        ],
    ];

    public static function boot()
    {
        parent::boot();
        self::creating( function( $model )
        {
            $model->uuid = Uuid::generate();
            if(!isset($model->status) || $model->status == "") {
                $model->status = "open";
            }
            StorageProperties::create([ 'bag_uuid' => $model->uuid ]);
        });

        self::deleting( function( $model )
        {
            $model->storage_properties->delete();
        });
    }

    public function owner()
    {
        return User::find($this->owner);
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function job()
    {
        return $this->belongsToMany('App\Job')->using('App\BagJob');
    }

    public function storage_properties()
    {
        return $this->hasOne('App\StorageProperties', 'bag_uuid', 'uuid');
    }

    public function storagePathCreated()
    {
        return Storage::disk('bags')->path($this->zipBagFileName());
    }

    public function BagFileNameNoExt()
    {
        return $this->name . '-' . $this->uuid;
    }

    public function zipBagFileName()
    {
        return $this->BagFileNameNoExt() . '.zip';
    }

    public function createdDirectory()
    {
        return Storage::disk('bags');
    }

    public function canTransition($transition)
    {
        return !(array_search($this->status, $this->smConfig["transitions"][$transition]["from"]) === false);
    }

    public function applyTransition($transition)
    {
        if(!isset($this->smConfig['transitions'][$transition]))
        {
            throw new BagTransitionException("transition '{$transition}' doesn't exist");
        }

        if(!$this->canTransition($transition))
        {
            throw new BagTransitionException("transition '{$transition}' is not allowed from state '{$this->status}'");
        }

        if(!isset($this->smConfig['transitions'][$transition]['to'])) {
            throw new BagTransitionException("No 'to' state for transition '{$transition}'' is defined");
        }

        $this->status = $this->smConfig['transitions'][$transition]['to'];
        return $this;
    }

    public function getBagSize() {
        $files = $this->files;
        $size = 0;
        foreach ($files as $file) {
            $size += $file->filesize;
        }
        return $size;
    }

    public function events()
    {
        return $this->morphMany('App\EventLogEntry', 'context');
    }
}
