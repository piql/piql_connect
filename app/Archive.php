<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class Archive extends Model
{
    use SoftDeletes;
    use AutoGenerateUuid;

    const DEFAULT_TEMPLATE = '{ "title": "", "description": "", "dc":  { "identifier": "" } }';

    protected $table = 'archives';
    protected $fillable = [
        'title','description','parent_uuid', 'uuid', 'account_uuid', 'defaultMetadataTemplate'
    ];
    protected $casts = [
        'defaultMetadataTemplate' => 'array'
    ];

    protected $attributes = [
        'defaultMetadataTemplate' => self::DEFAULT_TEMPLATE
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating( function ( $model ) {
            $validateIdentifier = Validator::make( ['uuid' => $model->uuid ], ['uuid' => 'uuid'] );
            if( !$validateIdentifier->passes() ){
                $model->uuid = Str::uuid();
            }

            /* No identifier supplied, create one */
            $userSuppliedData = $model->defaultMetadataTemplate["dc"];
            $model->defaultMetadataTemplate = ["dc" => ["identifier" => $model->uuid ] + $userSuppliedData ] ;
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

    public function account() {
        return $this->belongsTo(\App\Account::class, 'account_uuid', 'uuid');
    }


    public function setDefaultMetadataTemplateAttribute( Array $value )
    {
        if( array_has( $value, 'dc' ) ) { //TODO: Support other schemas than DC
            $original = $this->defaultMetadataTemplate ?? json_decode( self::DEFAULT_TEMPLATE );
            $this->defaultMetadataTemplate = ["dc" => $value["dc"] + $original["dc"] ];
        }
    }

}
