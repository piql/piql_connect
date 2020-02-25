<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BagitUtil;
use Webpatser\Uuid\Uuid;
Use Faker\Factory as Faker;

class BagitUtilTest extends TestCase
{
    private $generatedImageFilePaths;
    private $outputFileName;
    private $outputFilePath;
    private $targetPath;

    public function setUp() : void
    {
        $this->targetPath = sys_get_temp_dir() . "/target";
        if(!file_exists( $this->targetPath ) ) {
            mkdir(  $this->targetPath );
        }
        $faker = Faker::create();
        $this->generatedImageFilePaths = array_map( function () use (&$faker) { return $faker->file( $this->targetPath ); }, range(0,4) );
        $this->outputFileName = $faker->slug(1)."-".$faker->uuid().".zip";
        $this->outputFilePath = $this->targetPath .'/'.$this->outputFileName;

        parent::setUp();
    }

    public function tearDown() : void
    {
        parent::tearDown();

        array_map( function ($filePath) { unlink($filePath); }, $this->generatedImageFilePaths );
        unlink($this->outputFilePath);
    }
  
   public function test_it_creates_a_valid_bag()
    {
        $bagIt = new BagitUtil();
        array_map( function ($filePath) use (&$bagIt ) { 
            $bagIt->addFile($filePath, basename($filePath));
        }, $this->generatedImageFilePaths );
        $createdBag = $bagIt->createBag($this->outputFilePath);

        $this->assertTrue( $createdBag, "BagIt failed to create a bag" );
    }

    public function test_the_created_bag_is_a_valid_zip_archive()
    {
        $bagIt = new BagitUtil();
        $bagIt->createBag($this->outputFilePath);
        $zip_archive = zip_open($this->outputFilePath);
        $this->assertTrue(is_resource($zip_archive), "The zip file created by BagIt cannot be read");
        zip_close($zip_archive);
    }
}
