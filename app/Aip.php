<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aip extends Model
{
    use SoftDeletes;

    protected $table = 'aips';
    protected $fillable = [ 
        'external_uuid', 'owner', 'online_url', 'offline_url'
    ];

    public function fileObjects()
    {
        return $this->morphMany('App\FileObject', 'storable', null, 'storable_id', 'external_uuid');
    }

    public function mets()
    {
        return $this->morphOne('App\Mets', 'metsable', null, 'metsable_id', 'external_uuid');
    }

    public function owner()
    {
        return User::find( $this->owner );
    }

    public function dip()
    {
        return Dip::where( 'aip_external_uuid', $this->external_uuid )->first();
    }

}
