<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use App\Archive;

class Holding extends Model
{
    use AutoGenerateUuid;

    const DEFAULT_TEMPLATE = '{ "title": "", "description": "", "dc":  { "identifier": "" } }';

    protected $table = 'holdings';
    protected $fillable = [
        'title', 'description', 'owner_archive_uuid', 'parent_id','position',
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
            $model->position = $model->siblings()->count()+1;

            $validateIdentifier = Validator::make( ['uuid' => $model->uuid ], ['uuid' => 'uuid'] );
            if( !$validateIdentifier->passes() ){
                $model->uuid = Str::uuid();
            }

            $userSuppliedData = $model->defaultMetadataTemplate["dc"];
            $model->defaultMetadataTemplate = ["dc" => ["identifier" => $model->uuid ] + $userSuppliedData ] ;
        });
    }

    /* Section: Mutators and accessors */
    public function setOwnerArchiveUuidAttribute($value)
    {
        if(! Archive::findByUuid( $value ) )
        {
            throw new \InvalidArgumentException("Cannot assign owner archive to holdings - archive not found: ".$value);
        }

        $this->attributes['owner_archive_uuid'] = $value;
    }


    /* Section: Relations */
    public function owner_archive()
    {
        return $this->belongsTo(\App\Archive::class, 'owner_archive_uuid',  'uuid');
    }

    public function parent()
    {
        return $this->belongsTo('App\Holding', 'parent_id');
    }

    /* Section: Utilities */


    /* function siblings()
     * returns all holdings at the same level as the current (self exclusive)
     */

    public function siblings()
    {
        return Holding::orderBy( 'position' )
            ->where( 'id', '<>', $this->id )
            ->where( 'parent_id', '=', $this->parent_id);
    }

    /* function subHolding()
     * returns all first-level sub-holdings for the current holding
     */

    public function subHolding()
    {
        return $this->hasMany('App\Holding', 'parent_id');
    }

    /* function moveBefore($holding_id)
     * moves a holding before another holding in the chain
     * returns the updated holding
     */

    public function moveBefore( $holding_id )
    {
        $insertPosition = Holding::findOrFail( $holding_id )->position;

        /* Partition the list by before and after new position */
        $parts = $this->siblings()->get()->partition(
            function ($sibling) use ($insertPosition) {
                return $sibling->position < $insertPosition;
            }
        );


        /* Insert $this between before and after*/
        $ordered = $parts->first()->concat([$this])->concat($parts->last() );

        /* Re-enumerate entries by map reduce */
        $ordered->reduce( function ( $pos, $holding ) {
            $holding->update(['position' => $pos]);
            return $pos+1;
        }, 0);

        return $this;
    }


    /* function moveAfter($holding_id)
     * moves a holding after another holding in the chain
     * returns the updated holding */

    public function moveAfter( $holding_id )
    {
        $insertPosition = Holding::findOrFail( $holding_id )->position;

        /* Partition the list by before and after new position */
        $parts = $this->siblings()->get()->partition(
            function ($sibling) use ($insertPosition) {
                return $sibling->position <= $insertPosition;
            }
        );

        /* Insert $this between before and after*/
        $ordered = $parts->first()->concat( [$this] )->concat( $parts->last() );

        /* Re-enumerate entries by map reduce */
        $ordered->reduce( function ( $pos, $holding ) {
            $holding->update(['position' => $pos]);
            return $pos+1;
        }, 0);

        return $this;
    }



    /* function ancestor()
    /* recursively finds the top-level holdings for this holding
     */

    public function ancestor()
    {
        return $this->parent()->with('ancestor');
    }

    public function setDefaultMetadataTemplateAttribute( Array $value )
    {
        if( array_has( $value, 'dc' ) ) { //TODO: Support other schemas than DC
            $original = $this->defaultMetadataTemplate ?? json_decode( self::DEFAULT_TEMPLATE );
            $this->defaultMetadataTemplate = ["dc" => $value["dc"] + $original["dc"] ];
        }
    }
}

