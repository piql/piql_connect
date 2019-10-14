<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;


class Archive extends Model
{
    use SoftDeletes;

    protected $table = 'archives';
    protected $fillable = [
        'title','description','parent_uuid', 'uuid'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(empty($model->uuid)){
                $model->uuid = Uuid::generate()->string;
            }
        });
    }
   
    public static function findByParentUuid($parent_uuid)
    {
        return Archive::where('parent_uuid', '=', $parent_uuid)->first();
    }

    public static function findByUuid($uuid)
    {
        return Archive::where('uuid', '=', $uuid)->first();
    }

    public function setParentUuidAttribute($value)
    {
        if( !empty($value) )
        {
            if( Archive::findByParentUuid($value) == null )
            {
                throw new \Exception("The parent archive with uuid ".$value." does not exist.");
            }

            $this->attributes['parent_uuid'] = $value;
        }
    }

    public function holdings()
    {
        return $this->hasMany('App\Holding', 'owner_archive_uuid', 'uuid');
    }


}
