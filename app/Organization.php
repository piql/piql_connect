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

    protected $dispatchesEvents = [
        'created' => \App\Events\OrganizationCreatedEvent::class,
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'organization_uuid', 'uuid');
    }

    public function archive()
    {
        return $this->hasOne(Archive::class, 'organization_uuid', 'uuid');
    }

}
