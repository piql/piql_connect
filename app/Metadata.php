<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Metadata extends Model
{
    use SoftDeletes;
    use AutoGenerateUuid;

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

    protected static function boot()
    {
        parent::boot();

        static::creating( function ( $model ) {
            $model->owner_id = auth()->user()->account->uuid;
            $model->owner_type = "App\Account";
        });
    }


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
