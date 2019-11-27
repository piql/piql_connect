<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aip extends Model
{
    use SoftDeletes;

    protected $table = 'aips';
    protected $fillable = [ 
        'external_uuid', 'owner', 'online_storage_location_id', 'online_storage_path', 'offline_storage_location_id', 'offline_storage_path'
    ];

    /*
     * One for each original file inside the AIP so they can be listed and retrieved (not the extra generated files)
     * Later we need to figure out how to track and manage normalized vs original files.
     */
    public function fileObjects()
    {
        return $this->morphMany('App\FileObject', 'storable', null, 'storable_id', 'external_uuid');
    }

    /*
     * The main mets file for the AIP
     */
    public function mets()
    {
        return $this->morphOne('App\Mets', 'metsable', null, 'metsable_id', 'external_uuid');
    }

    /*
     * The related DIP
     */
    public function dip()
    {
        return \App\Dip::whereExternalUuid($this->storageProperties->dip_uuid)->first();
    }


    /*
     * The owner of the object
     */
    public function owner()
    {
        return User::find( $this->owner );
    }

    public function storageProperties()
    {
        return $this->belongsTo( 'App\StorageProperties', 'external_uuid', 'aip_uuid');
    }

}
