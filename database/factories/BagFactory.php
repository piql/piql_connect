<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\User;
use App\Bag;
use Faker\Generator as Faker;

$factory->define(Bag::class, function (Faker $faker) {

    $chance = rand(0,1);

    $year = date("Y");

    if ($chance == 0)
    {
        $year--;
    }

    $today = date('Y-n-j H:i:s');

    do
    {
        $dateTime = $year . "-" . $faker->month . "-" . $faker->dayOfMonth . " " . $faker->time;
    } while (strtotime($dateTime) > strtotime($today));
    $name = str_replace([" ", "-", ":"], "", $dateTime);

    $chance = rand(0,9);

    if ($chance == 0)
    {
        $status = "created";
    } elseif ($chance == 1)
    {
        $status = "processing";
    } else
    {
        $status = "complete";
    }
    $owner = "";
    $user = User::all()->where('username', 'Alfredo')->first();
    if(isset($user))
        $owner = $user->id;
    return [
        'name' => $name,
        'status' => $status,
        'owner' => $owner,
        'created_at' => $dateTime
    ];
});
