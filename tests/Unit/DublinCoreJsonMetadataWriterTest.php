<?php

namespace Tests\Unit;

use App\Services\DublinCoreJsonMetadataWriter;
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

class DublinCoreJsonMetadataWriterTest extends TestCase
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
        $this->writer = new DublinCoreJsonMetadataWriter([
            'filename' => $this->filename
        ]);

    }

    public function test_creating_a_json_metadata_file_one_entry_of_data_containing_fields()
    {
        $expectedData = '[
            {
                "filename":"objects/test.txt",
                "dc.title":"title",
                "dc.creator":"creator",
                "dc.subject":"subject",
                "dc.description":"description",
                "dc.publisher":"publisher",
                "dc.contributor":"contributor",
                "dc.date":"date",
                "dc.type":"type",
                "dc.format":"format",
                "dc.identifier":"identifier",
                "dc.source":"source",
                "dc.language":"language",
                "dc.relation":"relation",
                "dc.coverage":"coverage",
                "dc.rights":"rights"
            }
        ]';

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
        $data = Storage::disk()->get($this->filename);
        $this->assertJsonStringEqualsJsonString($expectedData, $data);
    }

    public function test_creating_a_json_metadata_file_one_entry_of_data_containing_two_fields()
    {
        $expectedData = json_encode([[
            "filename" => "objects/".$this->filename,
            "dc.title"       => "title",
            "dc.creator"     => "creator",
        ]]);

        $this->writer->write([
            "object" => $this->filename,
            'metadata' => [
                "dc:title"       => "title",
                "dc:creator"     => "creator",
            ]
        ]);
        $this->writer->close();

        $this->assertFileExists( Storage::path($this->filename) );
        $data = Storage::disk()->get($this->filename);
        $this->assertJsonStringEqualsJsonString($expectedData, $data);
    }


}
