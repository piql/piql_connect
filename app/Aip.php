<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Job;

class Aip extends Model
{
    use SoftDeletes;

    protected $table = 'aips';
    protected $fillable = [
        'external_uuid', 'owner', 'online_storage_location_id', 'online_storage_path', 'offline_storage_location_id', 'offline_storage_path'
    ];
    protected $appends = ['size'];
    /*
     * One for each original file inside the AIP so they can be listed and retrieved (not the extra generated files)
     * Later we need to figure out how to track and manage normalized vs original files.
     */
    public function fileObjects()
    {
        return $this->morphMany('App\FileObject', 'storable', null, 'storable_id' );
    }

    /*
     * The main mets file for the AIP
     */
    public function mets()
    {
        return $this->morphOne('App\Mets', 'metsable', null, 'metsable_id', 'external_uuid');
    }

    /*
     * Scan for a DIP through storage properties
     */
    public function storagePropertiesDip()
    {
        return $this->storage_properties->dip;
    }


    /*
     * The owner of the object
     */
    public function owner()
    {
        return User::find( $this->owner );
    }

    public function storage_properties()
    {
        return $this->belongsTo( 'App\StorageProperties', 'external_uuid', 'aip_uuid' );
    }

    public function online_storage_location()
    {
        return $this->belongsTo( 'App\StorageLocation', 'online_storage_location_id' );
    }

    public function jobs()
    {
        return $this->morphToMany(Job::class, 'archivable', null, null, 'archive_id');
    }

    public function getSizeAttribute()
    {
        return $this->fileObjects()->sum('size');
    }

}
