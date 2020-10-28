<?php

namespace Tests\Feature;
use App\Services\TarFileService;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use splitbrain\PHPArchive\Tar;

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
        $bigFileName = str_repeat('a', 240) . '.txt';
        $storage = Storage::fake('tar1');
        $storage->put($bigFileName, "fake file");
        $tarFile = $storage->path(time() . '.tar');
        $bigFileNamePath = $storage->path($bigFileName);
        $this->tarFileService->collectMultipleFiles(array(basename($bigFileName) => $bigFileNamePath), $tarFile, false);
        $this->assertEquals(true, file_exists($tarFile));
        $tar = new Tar();
        $tar->open($tarFile);
        $outPath = $storage->path('out');
        mkdir($outPath);
        $tar->extract($outPath);
        $this->assertEquals(true, file_exists($outPath . '/' . $bigFileName));
        $this->assertStringContainsString('POSIX', exec('file ' . $tarFile));
        unlink($bigFileNamePath);
        unlink($tarFile);
    }

    public function test_tar_big_filenames_directory()
    {
        $bigFileName = str_repeat('a', 240) . '.txt';
        $pathTmp = 'tmp/';
        $storage = Storage::fake('tar2');
        $storage->put($pathTmp . $bigFileName, "fake file");
        $testPath = $storage->path($pathTmp);
        $bigFileName = str_repeat('a', 240) . '.txt';
        $bigFileNamePath = $testPath . str_repeat('a', 240) . '.txt';
        $tarFile = $storage->path(time() . '.tar');
        $this->tarFileService->collectDirectory($testPath, $tarFile);
        $this->assertEquals(true, file_exists($tarFile));
        $tar = new Tar();
        $tar->open($tarFile);
        $outPath = $storage->path('out');
        mkdir($outPath);
        $tar->extract($outPath);
        $this->assertEquals(true, file_exists($outPath . '/' . $bigFileName));
        $this->assertStringContainsString('POSIX', exec('file ' . $tarFile));
        unlink($bigFileNamePath);
        unlink($tarFile);
    }

}
