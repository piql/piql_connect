<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = [
        'fileName', 'uuid', 'bag_uuid'
    ];

    public function bag()
    {
        return $this->belongsTo('App\Bag', 'uuid', 'bag_uuid');
    }

}
