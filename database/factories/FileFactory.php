<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {

	$chance = rand(0, 99);

	if ($chance < 34)
	{
		$extension = "PDF";
	} elseif ($chance < 54)
	{
		$extension = "TIFF";
	} elseif ($chance < 69)
	{
		$extension = "JPEG";
	} elseif ($chance < 79)
	{
		$extension = "XML";
	} elseif ($chance < 89)
	{
		$extension = "PNG";
	} elseif ($chance < 92)
	{
		$extension = "ZIP";
	} elseif ($chance < 95)
	{
		$extension = "DOC";
	} elseif ($chance < 97)
	{
		$extension = "AVI";
	} else
	{
		$extension = "MP4";
	}



    return [
        'bag_id' => factory(App\Bag::class)->create(),
        'filename' => "test_" . $faker->numberBetween(0, 10000) . "." . $extension,
        'uuid' => $faker->uuid,
    ];
});
