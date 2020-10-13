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

    protected $fillable = [
        'user_id',
        'interface',
        'workflow',
        'storage',
        'data',
    ];

    /* When adding new configuration groups, they must always
     * be added to $casts and $attributes to work!
     */
    protected $casts = [
        'interface' => 'array',
        'workflow' => 'array',
        'storage' => 'array',
        'data' => 'array'
    ];

    protected $attributes = [
        'interface' => '[]',
        'workflow' =>  '[]',
        'storage' => '[]',
        'data' => '[]'
    ];

    public function User()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /* Create or update a property in a group array
     *
     * @param string $property The group and property to set in the form 'group.property'
     * @param $value The value that will be associated with the property
     */
    public function set( string $property, $value ) : void
    {
        list( $group, $prop ) = $this->parsePropertyPath( $property );
        $this[$group] = [ $prop => $value ] + $this[$group];
    }

    /* Get property from a group
     *
     * @param string $property The group and property to get in the form 'group.property'
     * @param $default A default value [Optional]. Usually not required, will automatically resolve to env DEFAULT variables
     */

    public function get( string $property, $default = null )
    {
        list( $group, $prop ) = $this->parsePropertyPath( $property );
        return array_key_exists( $prop, $this[$group] )
            ? $this[$group][$prop]
            : $default ?? $this->propertyNameToDefaultEnv( $property );
    }

    /*
     * Get default value for a property from env by name
     *
     * Takes the name of a property in the form 'group.property' and turns it
     * into the form DEFAULT_GROUP_ATTRIBUTE, then fetches that value from env.
     * If no value was provided it returns null.
     *
     * @param string @property The name of a property in the form 'group.property'
     * @returns The default environment value for this property
     */

    private function propertyNameToDefaultEnv( string $property )
    {
        $variableName = ( "DEFAULT_".strtoupper(
            \Str::snake( str_replace( "."," ", $property ) ) ) );

        $envVariable = env( $variableName );
        if ( !isset( $envVariable ) ){
            throw new \Exception("No {$variableName} found in environment. Please update the relevant '.env' file accordingly.");
        }
        return $envVariable;
    }

    private function parsePropertyPath( $propertyPath )
    {
        $parsed = list( $group, $prop ) = explode( '.', $propertyPath );
        if( ! array_key_exists( $group, $this->casts ) ) {
            throw new \Exception("The configuration group '{$group}' does not exist.");
        }
        return $parsed;
    }

    public function getInterfaceLanguageAttribute()
    {
        return $this->get( 'interface.language' );
    }

    public function setInterfaceLanguageAttribute( $value )
    {
        $this->set( 'interface.language', $value );
    }    
    
    public function getInterfaceTableRowCountAttribute()
    {
        return $this->get( 'interface.tableRowCount' );
    }

    public function setInterfaceTableRowCountAttribute( $value )
    {
        $this->set( 'interface.tableRowCount', $value );
    }

    public function getDefaultAipStorageLocationIdAttribute()
    {
        return $this->get( "storage.defaultAipStorageLocationId",
            \App\StorageLocation::where('storable_type', 'App\Aip')->firstOrFail()->id );
    }

    public function setDefaultAipStorageLocationIdAttribute( $value )
    {
        $this->set( 'storage.defaultAipStorageLocationId', $value );
    }

    public function getDefaultDipStorageLocationIdAttribute()
    {
        return $this->get( "storage.defaultDipStorageLocationId",
            \App\StorageLocation::where('storable_type', 'App\Dip')->firstOrFail()->id );
    }

    public function getDefaultArchivematicaServiceDashboardUuidAttribute()
    {
        return $this->get( "storage.defaultArchivematicaServiceDashboardUuid",
            \App\ArchivematicaService::whereServiceType('dashboard')->firstOrFail()->id );
    }


    public function setDefaultArchivematicaServiceStorageServerUuidAttribute( $value )
    {
        $this->set( 'storage.defaultArchivematicaServiceStorageServerUuid', $value );
    }

    public function getDefaultArchivematicaServiceStorageServerUuidAttribute()
    {
        return $this->get( "storage.defaultArchivematicaServiceStorageServerUuid",
            \App\ArchivematicaService::whereServiceType('storage')->firstOrFail()->id );
    }


    public function setDefaultArchivematicaServiceDashboardUuidAttribute( $value )
    {
        $this->set( 'storage.defaultArchivematicaServiceDashboardUuid', $value );
    }


    public function setDefaultDipStorageLocationIdAttribute( $value )
    {
        $this->set( 'storage.defaultDipStorageLocationId', $value );
    }

    public function getIngestCompoundModeEnabledAttribute() : bool
    {
        return $this->get( 'workflow.ingestCompoundModeEnabled' );
    }

    public function setIngestCompoundModeEnabledAttribute( $value ) : void
    {
        $this->set( 'workflow.ingestCompoundModeEnabled', $value );
    }

    public function getIngestMetadataAsFileAttribute() : bool
    {
        return $this->get( 'workflow.ingestMetadataAsFileEnabled' , false);
    }

    public function setIngestMetadataAsFileAttribute( $value ) : void
    {
        $this->set( 'workflow.ingestMetadataAsFileEnabled', $value );
    }

}
