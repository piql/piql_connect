<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'title',
        'description',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function metadata()
    {
        return $this->hasMany(AccountMetadata::class, "parent_id");
    }

}
