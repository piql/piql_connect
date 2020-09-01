<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait MorphableSelf
{

    protected static function bootMorphableSelf()
    {
        static::addGlobalScope(static::class, function (Builder $builder) {
            $builder->where("type", static::class);
        });

        self::creating(function( $model )
        {
            $model->type = static::class;
        });
    }

    private function morphSelf()
    {
        return $this->morphTo(null, "type", "uuid");
    }

}
