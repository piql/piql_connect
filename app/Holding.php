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
   
    public static function findByParentUuid($parent_uuid)
    {
        return Holding::where('parent_uuid', '=', $parent_uuid)->first();
    }

    public static function findByUuid($uuid)
    {
        return Holding::where('uuid', '=', $uuid)->first();
    }

    public function setParentUuidAttribute($value)
    {
        if( !empty($value) )
        {
            if( Holding::findByParentUuid($value) == null )
            {
                throw new \Exception("The parent holding with uuid ".$value." does not exist.");
            }

            $this->attributes['parent_uuid'] = $value;
        }
    }

}
