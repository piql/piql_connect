<?php

namespace Tests\Feature;
use App\Services\TarFileService;
use Tests\TestCase;

class TarFileServiceTest extends TestCase
{
    private $tarFileService;
    public function setUp() : void
    {
        parent::setUp();
        $this->tarFileService = new TarFileService(null);
    }
    public function test_tar_big_filenames_multiple_files()
    {
        $testPath = '/tmp/' . time();
        mkdir($testPath);
        $bigFileName = $testPath . '/' . str_repeat('a', 240) . '.txt';
        $tarFile = '/tmp/' . time() . '.tar';
        file_put_contents($bigFileName,'test');
        $this->tarFileService->collectMultipleFiles(array(basename($bigFileName) => $bigFileName), $tarFile, false);
        $this->assertEquals(true, file_exists($tarFile));
        unlink($bigFileName);
        rmdir($testPath);
        unlink($tarFile);
    }
    public function test_tar_big_filenames_directory()
    {
        $testPath = '/tmp/' . time();
        mkdir($testPath);
        $bigFileName = $testPath . '/' . str_repeat('a', 240) . '.txt';
        $tarFile = '/tmp/' . time() . '.tar';
        file_put_contents($bigFileName,'test');
        $this->tarFileService->collectDirectory($testPath, $tarFile);
        $this->assertEquals(true, file_exists($tarFile));
        unlink($bigFileName);
        rmdir($testPath);
        unlink($tarFile);
    }
}
