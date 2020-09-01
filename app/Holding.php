<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holding extends Model
{
    protected $table = 'holdings';
    protected $fillable = [
        'title', 'description', 'owner_archive_uuid', 'parent_id', 'position'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->position = $model->siblings()->count()+1;
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
        return $this->belongsTo('Archive', 'owner_archive_uuid',  'uuid');
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

    public function metadata()
    {
        return $this->hasMany(\App\HoldingMetadata::class, "parent_id");
    }
}
