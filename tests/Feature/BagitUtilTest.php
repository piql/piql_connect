<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BagitUtil;

class BagitUtilTest extends TestCase
{
    private $testFiles = array('file1.txt', 'file2.pdf', 'file3.mp3');
    private $testFileSizes = array(132, 1, 12345);
        
    private function createTestFiles()
    {
        for ($i = 0; $i < count($this->testFiles); $i++)
        {
            $fp = fopen($this->testFiles[$i], 'w');
            fseek($fp, $this->testFileSizes[$i]-1,SEEK_CUR);
            fwrite($fp,'a');
            fclose($fp);
        }
    }

    private function deleteTestFiles()
    {
        for ($i = 0; $i < count($this->testFiles); $i++)
        {
            $this->assertTrue(unlink($this->testFiles[$i]));
        }
    }
    
    protected function setUp() : void
    {
        $this->createTestFiles();
    
        parent::setUp();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->deleteTestFiles();
    }
    
    public function test_create_working_bag()
    {
        $bag = new BagitUtil();

        // Add files
        for ($i = 0; $i < count($this->testFiles); $i++)
        {
            $bag->addFile($this->testFiles[$i], basename($this->testFiles[$i]));
        }

        // Create bag
        $outputFile = 'testbag-7724-4a16-bde8-7fba7c6e4a0a.zip';
        $this->assertTrue($bag->createBag($outputFile));
    }
}
