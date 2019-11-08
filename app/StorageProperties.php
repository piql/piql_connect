<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageProperties extends Model
{
    protected $table ='storage_properties';
    protected $fillable = [ 'bag_uuid', 'archive_uuid', 'holding_name',
        'sip_uuid', 'dip_uuid', 'aip_uuid' ];

    public function bag()
    {
        return $this->belongsTo('App\Bag', 'bag_uuid', 'uuid');
    }
}
