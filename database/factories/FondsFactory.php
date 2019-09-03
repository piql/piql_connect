<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Fonds;
use App\Holding;
use Faker\Generator as Faker;

$factory->define(Fonds::class, function (Faker $faker) {
    if(Holding::count() == 0){
        throw new Exception("You need to create at least one holding first");
    }
    return [
        'title' => $faker->slug(1),
        'description' => $faker->text(80),
        'owner_holding_uuid' => Holding::where('parent_uuid', '=', null)->pluck('uuid')->random(),
    ];
});

$factory->state(Fonds::class, 'subfonds', function (Faker $faker) {
    if(Fonds::count() == 0){
        throw new Exception("You need to create at least one fonds before creating any subfonds");
    }
    return [
        'parent_id' => Fonds::pluck('id')->random()
    ];
});
