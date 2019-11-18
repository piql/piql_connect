<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Dip extends Model
{
    use SoftDeletes;
    protected $table = 'dips';
    protected $fillable = [
        'external_uuid', 'owner', 'aip_external_uuid', 'storage_location_id', 'storage_location_path'
    ];

    /*
     * One for each normalized output file in the DIP (not the generated files)
     */
    public function fileObjects()
    {
        return $this->morphMany( 'App\FileObject', 'storable', null, 'storable_id', 'external_uuid' );
    }

    /* 
     * Pointer to the main mets of the AIP
     */
    public function mets()
    {
        return $this->morphOne( 'App\Mets', 'metsable', null, 'metsable_id', 'external_uuid' );
    }

    /*
     * The related AIP
     */
    public function aip()
    {
        return $this->hasOne( 'App\Aip', 'external_uuid', 'aip_external_uuid' );
    }

    /*
     * The owner of the object
     */
    public function owner()
    {
        return User::find( $this->owner );
    }

}
