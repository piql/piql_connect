<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Holding extends Model
{
    protected $table = 'holdings';
    protected $fillable = [
        'title','description','parent_uuid'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Uuid::generate()->string;
        });
    }
   
    public function parent()
    {
        return Holding::find_by_uuid($this->parent_uuid);
    }

}
