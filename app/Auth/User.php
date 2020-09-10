<?php

namespace App\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    // protected $fillable = [
    //     "scope", "email_verified", "name", "preferred_username", "given_name", "family_name", "email", 
    // ];

    public function getIdAttribute()
    {
        return $this->token->sub;
    }

    public function settings()
    {
        return $this->hasOne('App\UserSetting', 'user_id');
    }

    public function bags()
    {
        return $this->hasMany('App\Bag', 'owner');
    }

    public function jobs()
    {
        return $this->hasMany('App\Job', 'owner');
    }

    public function aips()
    {
        return $this->hasMany('App\Aip', 'owner');
    }

    public function dips()
    {
        return $this->hasMany('App\Dip', 'owner');
    }

    public function storageLocations()
    {
        return $this->hasMany('App\StorageLocation', 'owner_id');
    }
}
