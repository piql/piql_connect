<?php

namespace Tests\Unit;

use App\Services\AMTransferPacketBuilder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
Use Faker\Factory as Faker;

class AMTransferPacketBuilderTest extends TestCase
{
    private $srcStorage;
    private $dstStorage;
    private $outputFilePath;

    private $testFiles=[];
    private $testMetadataFiles=[];

    public function setUp() : void
    {
        parent::setUp();

        $this->srcStorage = Storage::fake('src');
        $this->dstStorage = Storage::fake('dst');

        $this->testFiles[] = "test.txt";
        $this->testMetadataFiles[] = "metadata.csv";
        foreach($this->testFiles as $testFile) {
            $this->srcStorage->put($testFile, "Hello world");
        }
        foreach($this->testMetadataFiles as $testMetadataFile) {
            $this->srcStorage->put($testMetadataFile, "column_title\nHello world");
        }

        $faker = Faker::create();
        $this->outputFilePath = $faker->slug(1)."-".$faker->uuid().".zip";
    }

    public function tearDown() : void
    {
        parent::tearDown();
    }

   public function test_it_creates_a_valid_bag()
    {
        $packetBuilder = new AMTransferPacketBuilder();
        foreach($this->testFiles as $testFile) {
            $packetBuilder->addFile($this->srcStorage->path($testFile), $testFile);
        }
        foreach($this->testMetadataFiles as $testMetadataFile) {
            $packetBuilder->addMetadataFile($this->srcStorage->path($testMetadataFile), $testMetadataFile);
        }
        $createdBag = $packetBuilder->build($this->dstStorage->path("").$this->outputFilePath);

        $this->assertTrue( $createdBag, "Failed to create a transfer packet" );
        foreach($this->testFiles as $testFile) {
            $this->assertFileExists($this->dstStorage->path($this->outputFilePath."/".$testFile));
        }
        foreach($this->testMetadataFiles as $testMetadataFile) {
            $this->assertFileExists($this->dstStorage->path($this->outputFilePath."/metadata/".$testMetadataFile));
        }

    }
}
