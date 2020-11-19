<?php

namespace App;

use App\Traits\AutoGenerateUuid;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use AutoGenerateUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'organization_uuid', 'uuid');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'organization_uuid', 'uuid');
    }

}
