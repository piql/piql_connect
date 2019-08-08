<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {

	$chance = rand(0, 99);

	if ($chance < 34)
	{
		$extension = "pdf";
	} elseif ($chance < 54)
	{
		$extension = "tif";
	} elseif ($chance < 69)
	{
		$extension = "jpg";
	} elseif ($chance < 79)
	{
		$extension = "xml";
	} elseif ($chance < 89)
	{
		$extension = "png";
	} elseif ($chance < 92)
	{
		$extension = "zip";
	} elseif ($chance < 95)
	{
		$extension = "docx";
	} elseif ($chance < 97)
	{
		$extension = "avi";
	} else
	{
		$extension = "mp4";
	}

    return [
        'bag_id' => factory(App\Bag::class)->create(),
        'filename' => "test_" . $faker->numberBetween(0, 10000) . "." . $extension,
        'uuid' => $faker->uuid,
    ];
});
