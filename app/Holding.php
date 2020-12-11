<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Collection;

class Holding extends Model
{
    use AutoGenerateUuid;

    const DEFAULT_TEMPLATE = '{ "title": "", "description": "", "dc":  { "identifier": "" } }';

    protected $table = 'holdings';
    protected $fillable = [
        'title', 'description', 'collection_uuid',
        'uuid', 'defaultMetadataTemplate'
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
        static::creating(function ($model) {
            $validateIdentifier = Validator::make( ['uuid' => $model->uuid ], ['uuid' => 'uuid'] );
            if( !$validateIdentifier->passes() ){
                $model->uuid = Str::uuid();
            }

            $userSuppliedData = $model->defaultMetadataTemplate["dc"] ?? [];
            $model->defaultMetadataTemplate = ["dc" => ["identifier" => $model->uuid ] + $userSuppliedData ] ;
        });
    }

    public function setOwnerArchiveUuidAttribute($value)
    {
        if(! Collection::findByUuid( $value ) )
        {
            throw new \InvalidArgumentException("Cannot assign collection to holding - collection not found: ".$value);
        }

        $this->attributes['collection_uuid'] = $value;
    }


    public function collection()
    {
        return $this->belongsTo('App\Collection', 'collection_uuid', 'uuid');
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

