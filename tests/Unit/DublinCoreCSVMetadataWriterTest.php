<?php

namespace Tests\Unit;

use App\Services\DublinCoreCSVMetadataWriter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Faker\Factory as faker;
use Webpatser\Uuid\Uuid;
use App\Aip;
use App\Bag;
use App\Dip;
use App\StorageLocation;
use App\StorageProperties;
use App\S3Configuration;

class DublinCoreCSVMetadataWriterTest extends TestCase
{
    use DatabaseTransactions;

    private $testUser;
    private $writer;
    private $filename;

    public function setUp() : void
    {
        parent::setUp();
        Storage::fake();
        $this->testUser = factory( \App\User::class )->create();
        Passport::actingAs( $this->testUser );
        $this->filename = 'test.txt';
        $this->writer = new DublinCoreCSVMetadataWriter([
            'filename' => $this->filename
        ]);

    }

    public function test_creating_a_csv_metadata_file_with_header_and_one_row_of_data()
    {
        $this->writer->write([
           "object" => $this->filename,
           'metadata' => [
               "dc:title"       => "title",
               "dc:creator"     => "creator",
               "dc:subject"     => "subject",
               "dc:description" => "description",
               "dc:publisher"   => "publisher",
               "dc:contributor" => "contributor",
               "dc:date"        => "date",
               "dc:type"        => "type",
               "dc:format"      => "format",
               "dc:identifier"  => "identifier",
               "dc:source"      => "source",
               "dc:language"    => "language",
               "dc:relation"    => "relation",
               "dc:coverage"    => "coverage",
               "dc:rights"      => "rights"
           ]
        ]);
        $this->writer->close();

        $this->assertFileExists( Storage::path($this->filename) );
        $data = explode("\n", Storage::disk()->get($this->filename));
        $this->assertEquals(2, count($data));
        $this->assertEquals("filename,dc.title,dc.creator,dc.subject,dc.description,dc.publisher,dc.contributor,dc.date,dc.type,dc.format,dc.identifier,dc.source,dc.language,dc.relation,dc.coverage,dc.rights", $data[0]);
        $this->assertEquals("data/test.txt,title,creator,subject,description,publisher,contributor,date,type,format,identifier,source,language,relation,coverage,rights", $data[1]);
    }



}
