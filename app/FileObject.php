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
        'fullpath', 'filename', 'path', 'size',
        'object_type', 'info_source', 'mime_type',
        'storable_type', 'storable_id'
    ];

    public function storable()
    {
        return $this->morphTo();
    }

    public function storedObjectType()
    {
        return Str::lower( Str::after( $this->storable_type, "\\" ) );
    }
}
