<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use App\User;
class BagTransitionException extends \Exception {};

class Bag extends Model
{
    protected $table = 'bags';
    protected $fillable = [
        'name', 'status', 'owner' // todo: remove 'status'
    ];

    private $smConfig = [
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
                'from' => ['initiate_transfer'],
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
            $model->status = "open";
        });
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function job()
    {
        return $this->belongsToMany('App\Job')->first();
    }

    public function storagePathCreated()
    {
        return Storage::disk('bags')->path($this->zipBagFileName());
    }

    public function zipBagFileName()
    {
        return $this->name . '-' . $this->uuid . '.zip';
    }

    public function createdDirectory()
    {
        return Storage::disk('bags');
    }

    public function canTransition($transition)
    {
        return isset($this->smConfig['transactions'][$transition]['from'][$this->status]);
    }

    public function applyTransition($transition)
    {
        if(!isset($this->smConfig['transitions'][$transition]))
        {
            throw new BagTransitionException("transition '{$transition}' doesn't exist");
        }

        if(!isset($this->smConfig['transitions'][$transition]))
        {
            throw new BagTransitionException("transition '{$transition}' is not allows from state '{$this->status}'");
        }

        if(!isset($this->smConfig['transitions'][$transition]['to'])) {
            throw new BagTransitionException("No 'to' state for transition '{$transition}'' is defined");
        }

        $this->status = $this->smConfig['transitions'][$transition]['to'];
        $this->save();
    }

    public function getBagSize() {
        $files = $this->files;
        $size = 0;
        foreach ($files as $file) {
            $size += $file->filesize;
        }
        return $size;
    }
}
