<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Str;

class StorageLocation extends Model
{
    use SoftDeletes;

    protected $table = 'storage_locations';
    protected $fillable = [
        'owner_id',
        'locatable_id', 'locatable_type',
        'storable_type',
        'human_readable_name'
    ];

    public function locatable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id', 'id');
    }

    public function storableType()
    {
        return Str::lower( Str::after( $this->storable_type, "\\" ) );
    }

}
