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

            $userSuppliedData = $model->defaultMetadataTemplate["dc"];
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

    public function setDefaultMetadataTemplateAttribute( Array $value )
    {
        if( array_has( $value, 'dc' ) ) { //TODO: Support other schemas than DC
            $original = $this->defaultMetadataTemplate ?? json_decode( self::DEFAULT_TEMPLATE );
            $this->defaultMetadataTemplate = ["dc" => $value["dc"] + $original["dc"] ];
        }
    }

}
