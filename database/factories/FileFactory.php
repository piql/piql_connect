<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {

	$chance = rand(0, 99);

	if ($chance < 34)
	{
		$extension = "pdf";
		$size = rand(100000, 100000000); // Between 100KB and 100MB
	} elseif ($chance < 54)
	{
		$extension = "tif";
		$size = rand(10000000, 500000000); // Between 10MB and 500MB
	} elseif ($chance < 69)
	{
		$extension = "jpg";
		$size = rand(500000, 10000000); // Between 500KB and 10MB
	} elseif ($chance < 79)
	{
		$extension = "xml";
		$size = rand(1000, 10000000); // Between 1KB and 10MB
	} elseif ($chance < 89)
	{
		$extension = "png";
		$size = rand(500000, 10000000); // Between 500KB and 10MB
	} elseif ($chance < 92)
	{
		$extension = "zip";
		$size = rand(100000, 100000000000); // Between 100KB and 100GB
	} elseif ($chance < 95)
	{
		$extension = "docx";
		$size = rand(100000, 10000000); // Between 100KB and 10MB
	} elseif ($chance < 97)
	{
		$extension = "avi";
		$size = rand(10000000, 10000000000); // Between 10MB and 100GB
	} else
	{
		$extension = "mp4";
		$size = rand(10000000, 100000000000); // Between 10MB and 100GB
	}

    return [
        'bag_id' => factory(App\Bag::class)->create(),
        'filename' => "test_" . $faker->numberBetween(0, 10000) . "." . $extension,
        'filesize' => $size,
        'uuid' => $faker->uuid,
    ];
});
