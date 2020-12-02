<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Organization;
use App\Auth\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    $user = json_decode(file_get_contents(resource_path('data/keycloak/users/token.json')));
    $name = $faker->name;
    $names = explode(" ", $name, 2);
    $user->iat = time();
    $user->exp = $user->iat + (5 * 60);
    $user->sub = (string)Webpatser\Uuid\Uuid::generate();
    $user->name = $name;
    $user->preferred_username = str_replace(" ", ".", strtolower($name));
    $user->email = $user->preferred_username.'@example.com';
    $user->given_name = $names[0];
    $user->family_name = $names[1];
    $user->organization_uuid = Organization::pluck('uuid')->random();
    \App\UserSetting::create([ 'user_id' => $user->sub ]);
    \App\User::create([
        'full_name' => $user->name,
        'username' => $user->preferred_username,
        'password' => bcrypt("secret"),
        'email' => $user->email,
        'email_verified_at' => now(),
        'organization_uuid' => $user->organization_uuid,
        'remember_token' => Str::random(10),
    ]);
    return (array) $user;
});
