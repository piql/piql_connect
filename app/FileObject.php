<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class FileObject extends Model
{
    use SoftDeletes;

    protected $table = 'file_objects';
    protected $fillable = [
        'object_type', 'mime_type', 'storable_type', 'storable_id'
    ];

    public static function storable()
    {
        return $this->morphTo();
    }

    public function storageType()
    {
        return Str::lower( Str::after( $this->storable_type, "\\" ) );
    }
}
