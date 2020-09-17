<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

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
            $model->uuid = Str::uuid();
            $userSuppliedData = $model->defaultMetadataTemplate["dc"];
            $model->defaultMetadataTemplate = ["dc" => ["identifier" => Str::uuid() ] + $userSuppliedData ] ;
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

    public function getMetadataAttribute( $value ) /*TODO: Remove after refactoring apis */
    {
        return $this->defaultMetadataTemplate;
    }

    public function setMetadataAttribute( $value ) /*TODO: Remove after refactoring apis */
    {
        if( !is_array( $value ) )
            return;

        if( array_has( $value, 'dc' ) ) { //TODO: Support other schemas than DC
            $original = $this->defaultMetadataTemplate ?? json_decode( self::DEFAULT_TEMPLATE );
            $this->defaultMetadataTemplate = ["dc" => $value["dc"] + $original["dc"] ];
        }
    }

}
