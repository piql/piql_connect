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

    public function getDefaultMetadataTemplateAttribute( string $template ) {
        $ar = json_decode( $template, true );
        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["identifier"] ) ) {
            $ar["dc"]["identifier"] = $this->attributes["uuid"]; //The default is to use the uuid for the Archive as the identifier (as per spec.)
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["title"] ) ) {
            $ar["dc"]["title"] = $this->attributes["title"]; //The default is to use the uuid for the Archive as the identifier (as per spec.)
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["description"] ) ) {
            $ar["dc"]["description"] = $this->attributes["description"]; //The default is to use the uuid for the Archive as the identifier (as per spec.)
        }
        return $ar;
    }

    public function setDefaultMetadataTemplateAttribute( Array $template ) {
        if( array_has( $template, 'dc' ) ) { //TODO: Support other schemas than DC, model level validation would be nice
            $this->attributes['defaultMetadataTemplate'] = json_encode( $template );
        }
    }
}
