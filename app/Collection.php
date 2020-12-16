<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class Collection extends Model
{
    use SoftDeletes;
    use AutoGenerateUuid;

    const DEFAULT_TEMPLATE = '{ "title": "", "description": "", "dc":  { "identifier": "" } }';

    protected $table = 'collections';
    protected $fillable = [
        'title', 'description', 'uuid', 'archive_uuid', 'defaultMetadataTemplate'
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

    public static function findByUuid($uuid)
    {
        return Collection::where('uuid', '=', $uuid)->first();
    }

    public function holdings()
    {
        return $this->hasMany('App\Holding', 'collection_uuid', 'uuid');
    }

    public function archive() {
        return $this->belongsTo(\App\Archive::class, 'archive_uuid', 'uuid');
    }

    public function getDefaultMetadataTemplateAttribute( string $template ) {
        $ar = json_decode( $template, true );
        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["identifier"] ) ) {
            $ar["dc"]["identifier"] = $this->attributes["uuid"]; //The default is to use the uuid as the identifier (as per spec.)
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["title"] ) ) {
            $ar["dc"]["title"] = $this->attributes["title"];    //Grab the title from the model if not present in metadata template
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["description"] ) ) {
            $ar["dc"]["description"] = $this->attributes["description"] ?? ""; //Grab the description from the model if not present in metadata template

        }
        return $ar;
    }

    public function setDefaultMetadataTemplateAttribute( Array $template ) {
        if( array_has( $template, 'dc' ) ) { //TODO: Support other schemas than DC, model level validation would be nice
            $this->attributes['defaultMetadataTemplate'] = json_encode( $template );
        }
    }
}
