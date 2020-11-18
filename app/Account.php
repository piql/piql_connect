<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

class Account extends Model
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

    public function archives()
    {
        return $this->hasMany('App\Archive', 'account_uuid', 'uuid');
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization', 'organization_uuid', 'uuid');
    }

    public function users()
    {
        //TODO: This method should be obsoleted as part of the archive refactoring
        return $this->organization->users();
    }

    public function getDefaultMetadataTemplateAttribute( string $template ) {
        $ar = json_decode( $template, true );
        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["identifier"] ) ) {
            $ar["dc"]["identifier"] = $this->attributes["uuid"]; //The default is to use the uuid for the Archive as the identifier (as per spec.)
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["title"] ) ) {
            $ar["dc"]["title"] = $this->attributes["title"] ?? "";  //TODO: accounts.title is nullable, not sure if this is ok?
        }

        if(isset( $ar["dc"] ) && !isset( $ar["dc"]["description"] ) ) {
            $ar["dc"]["description"] = $this->attributes["description"] ?? ""; //Grab the description from the model if not present in metadata template
        }
        return $ar;
    }

}
