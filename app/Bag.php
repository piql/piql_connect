<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;
use App\User;
use App\StorageProperties;
class BagTransitionException extends \Exception {};

class Bag extends Model
{
    use Uuids;
    protected $table = 'bags';
    protected $fillable = [
        'name', 'status', 'owner' // todo: remove 'status'
    ];

    protected const smConfig = [
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
                'from' => ['initiate_transfer', 'move_to_outbox'],
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
            'open' => [
                'from' => [
                    'empty',
                    'open',
                    'closed',
                    'complete',
                    'error'],
                'to' => 'open',
            ],
        ],
    ];

    public static function boot()
    {
        parent::boot();
        self::creating( function( $model )
        {
            $model->uuid = (string)Uuid::generate();
            if(!isset($model->status) || $model->status == "") {
                $model->owner = Auth::id();
            }

            if(!isset($model->status) || $model->status == "") {
                $model->status = "open";
            }
            $settings = UserSetting::where('user_id', $model->owner )->first();

            StorageProperties::create([
                'bag_uuid' => $model->uuid,
                'aip_initial_online_storage_location' => $settings->defaultAipStorageLocationId,
                'dip_initial_storage_location' =>  $settings->defaultDipStorageLocationId,
                'archivematica_service_dashboard_uuid' => $settings->defaultArchivematicaServiceDashboardUuid,
                'archivematica_service_storage_server_uuid' => $settings->defaultArchivematicaServiceStorageServerUuid
            ]);
        });

        self::deleting( function( $model )
        {
            $model->storage_properties->delete();
        });
    }

    public function setOwnerIdAttribute($value)
    {
        return $this->attributes['owner'] = $this->bin2Uuid($value);
    }

    public function getOwnerIdAttribute()
    {
        return $this->uuid2Bin($this->attributes['owner']);
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
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
        return !(array_search($this->status, Bag::smConfig["transitions"][$transition]["from"]) === false);
    }

    public function applyTransition($transition, bool $force = false)
    {

        if(!isset(Bag::smConfig['transitions'][$transition]))
        {
            throw new BagTransitionException("transition '{$transition}' doesn't exist");
        }

        if(!($this->canTransition($transition) || $force))
        {
            throw new BagTransitionException("transition '{$transition}' is not allowed from state '{$this->status}'");
        }

        if(!isset(Bag::smConfig['transitions'][$transition]['to'])) {
            throw new BagTransitionException("No 'to' state for transition '{$transition}'' is defined");
        }

        $this->status = Bag::smConfig['transitions'][$transition]['to'];
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

    public static function transitions() {
        return Bag::smConfig['transitions'];
    }

    public function events()
    {
        return $this->morphMany('App\EventLogEntry', 'context');
    }
}
