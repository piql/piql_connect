<?php

namespace App\Auth;

use App\File;
use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function getIdAttribute()
    {
        return isset($this->token) ? $this->token->sub : $this->sub;
    }

    public function settings()
    {
        //todo: remove this at the point when we can create keycloak users via this client
        if(!\App\UserSetting::where([ 'user_id' => $this->getIdAttribute()])->exists())
            \App\UserSetting::create([ 'user_id' => $this->getIdAttribute() ]);
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

    public function save(array $options = [])
    {
        $id = $this->getIdAttribute();
        try {
            $dir=storage_path("app/data/keycloak/users/");
            if(!File::isDirectory($dir))File::makeDirectory($dir, 0777, true, true);
            file_put_contents(storage_path("app/data/keycloak/users/$id.json"), $this->toJson()['token']);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function givenName() {
        return isset($this->token) ? $this->token->given_name : null;
    }
}
