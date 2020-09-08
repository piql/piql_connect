<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;


class Archive extends Model
{
    use SoftDeletes;
    use AutoGenerateUuid;

    protected $table = 'archives';
    protected $fillable = [
        'title','description','parent_uuid', 'uuid', 'account_uuid'
    ];

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

    public function metadata()
    {
        return $this->hasMany(\App\ArchiveMetadata::class, "parent_id");
    }

    public function account() {
        return $this->belongsTo(\App\Account::class, 'account_uuid', 'uuid');
    }
}
