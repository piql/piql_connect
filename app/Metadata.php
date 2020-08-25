<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Metadata extends Model
{
    use SoftDeletes;

    protected $table = 'metadata';
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'modified_by',
        'parent_type',
        'parent_id',
        'owner_type',
        'owner_id',
        'metadata',
    ];
    protected $casts = [
        'metadata' => 'array',
    ];

    public function parent()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function parentType()
    {
        return Str::lower(Str::after($this->parent_type, "\\"));
    }

}
