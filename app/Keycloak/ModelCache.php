<?php

namespace App\Keycloak;

use Illuminate\Database\Eloquent\Model;

class ModelCache extends Model 
{

    protected $fillable = [
        'name', 'organization_id', 'entity', 'entity_id'
    ];

}