<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fonds extends Model
{
    protected $table = 'fonds';
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
            throw new \InvalidArgumentException("Cannot assign owner archive to fonds - archive not found: ".$value);
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
        return $this->belongsTo('App\Fonds', 'parent_id');
    }

    /* Section: Utilities */


    /* function siblings()
     * returns all fonds at the same level as the current (self exclusive)
     */

    public function siblings()
    {
        return Fonds::orderBy( 'position' )
            ->where( 'id', '<>', $this->id )
            ->where( 'parent_id', '=', $this->parent_id);
    }

    /* function subFonds()
     * returns all first-level sub-fonds to the current fonds
     */

    public function subFonds()
    {
        return $this->hasMany('App\Fonds', 'parent_id');
    }

    /* function moveBefore($fonds_id)
     * moves a fonds before another fonds in the chain
     * returns the updated fonds
     */

    public function moveBefore( $fonds_id )
    {
        $insertPosition = Fonds::findOrFail( $fonds_id )->position;

        /* Partition the list by before and after new position */
        $parts = $this->siblings()->get()->partition(
            function ($sibling) use ($insertPosition) {
                return $sibling->position < $insertPosition;
            }
        );


        /* Insert $this between before and after*/
        $ordered = $parts->first()->concat([$this])->concat($parts->last() );

        /* Re-enumerate entries by map reduce */
        $ordered->reduce( function ( $pos, $fonds ) {
            $fonds->update(['position' => $pos]);
            return $pos+1;
        }, 0);

        return $this;
    }


    /* function moveAfter($fonds_id)
     * moves a fonds after another fonds in the chain
     * returns the updated fonds
     */

    public function moveAfter( $fonds_id )
    {
        $insertPosition = Fonds::findOrFail( $fonds_id )->position;

        /* Partition the list by before and after new position */
        $parts = $this->siblings()->get()->partition(
            function ($sibling) use ($insertPosition) {
                return $sibling->position <= $insertPosition;
            }
        );

        /* Insert $this between before and after*/
        $ordered = $parts->first()->concat( [$this] )->concat( $parts->last() );

        /* Re-enumerate entries by map reduce */
        $ordered->reduce( function ( $pos, $fonds ) {
            $fonds->update(['position' => $pos]);
            return $pos+1;
        }, 0);

        return $this;
    }



    /* function ancestor()
    /* recursively finds the top-level fonds for this fonds
     */

    public function ancestor()
    {
        return $this->parent()->with('ancestor');
    }

}
