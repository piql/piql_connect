<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Dip extends Model
{
    use SoftDeletes;
    protected $table = 'dips';
    protected $fillable = [
        'external_uuid', 'owner', 'aip_external_uuid', 'online_url', 'offline_url'
    ];

    public function fileObjects()
    {
        return $this->morphMany( 'App\FileObject', 'storable', null, 'storable_id', 'external_uuid' );
    }

    public function mets()
    {
        return $this->morphOne( 'App\Mets', 'metsable', null, 'metsable_id', 'external_uuid' );
    }

    public function aip()
    {
        return $this->hasOne( 'App\Aip', 'external_uuid', 'aip_external_uuid' );
    }

    public function owner()
    {
        return User::find( $this->owner );
    }


}
