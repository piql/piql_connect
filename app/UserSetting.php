<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/*
 * UserSetting - hold user configurable settings and options
 *
 * The fields are json arrays that is automatically deserialized to a php array on access.
 * Interface contains the user's personal interface settings
 * Workflow contains settings that affect how data is processed and retrieved
 * Storage contains defaults for storing DIPs, AIPs etc
 * Data contains settings general data like base64 images, metadata schemes etc.
 *
 * Values that are looked up often, like interface["language"] should be given
 * accessors and mutators and a cache entry to avoid uneccesary lookups.
 */

class UserSetting extends Model
{
    protected $table = "user_settings";

    protected $appends = [ 'interfaceLanguage', 'defaultAipStorageLocationId', 'defaultDipStorageLocationId' ];

    protected $fillable = [
        'owner',
        'interface',
        'workflow',
        'storage',
        'data',
        'interfaceLanguage',
        'defaultAipStorageLocationId',
        'defaultDipStorageLocationId'
    ];

    protected $casts = [
        'interface' => 'array',
        'workflow' => 'array',
        'storage' => 'array',
        'data' => 'array'
    ];

    public function User()
    {
        return $this->belongsTo('App\User');
    }

    public function getInterfaceLanguageAttribute()
    {
        return array_key_exists( 'language', $this->interface )
            ? $this->interface['language']
            : env("DEFAULT_INTERFACE_LANGUAGE");
    }

    public function setInterfaceLanguageAttribute( $value )
    {
        $interface = ['language' => $value] + $this->interface;
        $this->interface = $interface;
    }

    public function getDefaultAipStorageLocationIdAttribute()
    {
        return array_key_exists( 'defaultAipStorageLocationId', $this->storage )
            ? $this->storage['defaultAipStorageLocationId']
            : \App\StorageLocation::where('storable_type', 'App\Aip')->firstOrFail()->id;
    }

    public function setDefaultAipStorageLocationIdAttribute( $value )
    {
        $storage = [ 'defaultAipStorageLocationId' => $value ] + $this->storage;
        $this->storage = $storage;
    }

    public function getDefaultDipStorageLocationIdAttribute()
    {
        return array_key_exists( 'defaultDipStorageLocationId', $this->storage )
            ? $this->storage['defaultDipStorageLocationId']
            : \App\StorageLocation::where('storable_type', 'App\Dip')->firstOrFail()->id;
    }

    public function setDefaultDipStorageLocationIdAttribute( $value )
    {
        $storage = [ 'defaultDipStorageLocationId' => $value ] + $this->storage;
        $this->storage = $storage;
    }

}
