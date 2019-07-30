<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = "user_settings";
    protected $fillable = ["user", "interfaceLanguage"];


    protected function user()
    {
        return $this->belongsTo('App\User');
    }
}
