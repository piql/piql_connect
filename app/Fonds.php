<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fonds extends Model
{
    protected $table = 'fonds';
    protected $fillable = [
        'title', 'description', 'owner_holding_uuid', 'parent_id', 'lhs', 'rhs', 
    ];

    public function owner_holding()
    {
        return $this->belongsTo('Holding', 'owner_holding_uuid',  'uuid');
    }

    public function parent()
    {
        return Fonds::find($this->parent_id);
    }

    /* find all fonds at the same level */
    public function siblings()
    {
        $parent_id = $this->parent()->id;
        return Fonds::all()->where('parent_id', '=', $parent_id);
    }

    /* traverse the tree up to the top-level ancestor */
    public function ancestor()
    {
        $ancestor = $this->parent();
        while( $ancestor )
        {
            $ancestor = $grandParent->parent();
        }
        return $ancestor;
    }
}
