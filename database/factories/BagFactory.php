<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Bag;
use Faker\Generator as Faker;

$factory->define(Bag::class, function (Faker $faker) {
    
	$chance = rand(0,1);

	$year = date("Y");

	if ($chance == 0)
	{
		$year--;
	}

    $dateTime = $year . "-" . $faker->month . "-" . $faker->dayOfMonth . " " . $faker->time;
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

    return [
	    'name' => $name,
        'status' => $status,
        'owner' => "07da0453-857f-4645-92ac-9a3add6427cf",
        'created_at' => $dateTime
    ];
});
