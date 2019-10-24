<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Mets extends Model
{
    use SoftDeletes;

    protected $table = 'mets';
    protected $fillable = [ 'source_url' ];

    public static function metsable()
    {
        return $this->morphTo();
    }
}
