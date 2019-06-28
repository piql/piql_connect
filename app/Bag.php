<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use App\User;

class Bag extends Model
{
    protected $table = 'bags';
    protected $fillable = [
        'name', 'status', 'owner'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating( function( $model ) 
        {
            $model->uuid = Uuid::generate();
            $model->status = "created";
        });
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function storagePathCreated()
    {
        return Storage::disk('bags')->path("created/".$this->zipBagFileName());
    }

    public function zipBagFileName()
    {
        return $this->name . '-' . $this->uuid . '.zip';
    }

    public function createdDirectory()
    {
        return Storage::disk('bags')->path('created');
    }
}
