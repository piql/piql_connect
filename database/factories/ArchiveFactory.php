<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Archive;
use App\Organization;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Archive::class, function (Faker $faker) {
    if(Organization::count() == 0){
        throw new Exception("Archive factory failed: You need to create at least one Organization before creating an archive.");
    }

    return [
        'uuid' => $faker->uuid,
        'title' => $faker->text,
        'description' => $faker->text,
        'organization_uuid' => Organization::pluck('uuid')->random(),
        'defaultMetadataTemplate' => ["dc" => ["relation" => $faker->text(8) ]]
    ];
});
