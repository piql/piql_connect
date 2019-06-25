<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;
use App\Traits\Uuids;
use Illuminate\Support\Facades\Hash;

class ArchivematicaService extends Model
{
    Use Uuids;

    protected $table = 'archivematica_services';
    protected $fillable = [
        'id',
        'url',
        'api_token'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function( $model ) /*Create a uuid converted to binary16 */
        {
            $model->id = Uuid::generate();
            $model->api_token = Hash::make(Uuid::generate());
        });
    }

    public function setIdAttribute($value)
    {
        return $this->attributes['id'] = $this->uuid2Bin($value);
    }

    public function getIdAttribute()
    {
        return $this->bin2Uuid($this->attributes['id']);
    }

    public static function find($id)
    {
        return self::all()->find($id); 
    }

}
