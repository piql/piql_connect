<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
Use Webpatser\Uuid\Uuid;
Use App\Traits\Uuids;

class User extends Authenticatable 
{
    use Notifiable, Uuids;

    public $incrementing = false; /* Do not try to auto increment the id (uuid) */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'username', 'password', 'full_name', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function( $model ) /*Create a uuid converted to binary16 */
        {
            $model->id = Uuid::generate();
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
}
