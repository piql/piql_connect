<?php

namespace App\Traits;

use Webpatser\Uuid\Uuid;

trait AutoGenerateUuid
{

    protected static function bootAutoGenerateUuid()
    {
        self::creating(function( $model )
        {
            if(empty($model->uuid)) {
                $model->uuid = Uuid::generate()->string;
            }
        });

    }

}
