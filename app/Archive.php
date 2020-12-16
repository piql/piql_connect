<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class Archive extends Model
{
    const DEFAULT_TEMPLATE = '{ "title": "", "description": "", "dc":  { "identifier": "" } }';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'organization_uuid',
        'defaultMetadataTemplate'
    ];

    protected $attributes = [
        'defaultMetadataTemplate' => self::DEFAULT_TEMPLATE
    ];


    protected $casts = [
        'defaultMetadataTemplate' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating( function ( $model ) {
            $validateIdentifier = Validator::make( ['uuid' => $model->uuid], ['uuid' => 'uuid'] );
            if( !$validateIdentifier->passes() ){
                $model->uuid = Str::uuid();
            }

            $userSuppliedData = $model->defaultMetadataTemplate["dc"] ?? [];
            $model->defaultMetadataTemplate = ["dc" => ["identifier" => $model->uuid ] + $userSuppliedData ] ;
        });
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function collections()
    {
        return $this->hasMany('App\Collection', 'archive_uuid', 'uuid');
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization', 'organization_uuid', 'uuid');
    }

    public function getDefaultMetadataTemplateAttribute( string $template ) {
        $ar = json_decode( $template, true );
        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["identifier"] ) ) {
            $ar["dc"]["identifier"] = $this->attributes["uuid"]; //The default is to use the uuid as the identifier (as per spec.)
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["title"] ) ) {
            $ar["dc"]["title"] = $this->attributes["title"] ?? "";  //TODO: archives.title is nullable, not sure if this is ok?
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["description"] ) ) {
            $ar["dc"]["description"] = $this->attributes["description"] ?? ""; //Grab the description from the model if not present in metadata template
        }
        return $ar;
    }

}
