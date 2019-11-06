<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class S3Configuration extends Model
{
    use SoftDeletes;

    protected $table = 's3_configurations';
    protected $fillable = ['url', 'key_id', 'secret', 'bucket'];

    public function configurations()
    {
        return $this->morphMany('App\StorageLocation', 'locatable' );
    }

}
