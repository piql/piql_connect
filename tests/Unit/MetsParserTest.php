<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Interfaces\MetsParserInterface;
use App\Services\MetsParserService;

class MetsParserTest extends TestCase
{
    protected static $singleMetsContents;
    protected static $multiMetsContents;
    protected static $noDublinCoreMetsContents;

    public static function setupBeforeClass()
    {
        self::$singleMetsContents = file_get_contents( "tests/Data/gotmetadata-METS.8149cfad-2ba1-4ccf-b132-255dd6399ed1.xml" );
        self::$multiMetsContents = file_get_contents( "tests/Data/svalbard-METS.350fbaa1-c664-4ced-9e98-adcb379f998f.xml" );
        self::$noDublinCoreMetsContents = file_get_contents( "tests/Data/no-metadata.METS.9366157d-2066-4ff8-9cb7-744e0f826b58.xml" );
    }

    public function test_when_injecting_an_instance_of_MetsParserInterface_it_makes_a_MetsParserService()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $this->assertInstanceOf( MetsParserService::class, $service );
    }

    public function test_when_testing_the_mets_parser_the_test_files_have_been_read()
    {
        $this->assertEquals( 145857, strlen( self::$singleMetsContents ) ) ;
        $this->assertEquals( 179370, strlen( self::$multiMetsContents ) );
    }

    public function test_when_testing_the_mets_parser_the_test_files_can_be_parsed_as_xml()
    {
        $parser = xml_parser_create();
        $this->assertEquals( 1, xml_parse( $parser, self::$singleMetsContents, true ) );
        xml_parser_free( $parser );
        $parser = xml_parser_create();
        $this->assertEquals( 1, xml_parse( $parser, self::$multiMetsContents, true ) );
        xml_parser_free( $parser );
    }

    public function test_given_a_mets_file_in_a_string_when_parsing_dublin_core_fields_they_are_returned_in_an_array()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $actual = $service->parseDublinCoreFields( self::$singleMetsContents );
        $this->assertIsArray( $actual );
    }

    public function test_given_a_mets_file_in_a_string_when_parsing_dublin_core_fields_they_are_the_expected_values()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $expected = [
            "FMU.420001.tif" => [
                "title" => "Krigsskip",
                "creator" => "Forsvarsmuseet",
                "subject" => "Skip",
                "description" => "Et skip som kjører fort",
                "publisher" => "En eller annen veteran",
                "contributor" => "Noen folk",
                "date" => "03.23.2020",
                "type" => "Image",
                "format" => "TIF",
                "identifier" => "FMU.420001",
                "source" => "Scanning",
                "language" => "Norsk",
                "relation" => "Ingen",
                "coverage" => "Bra",
                "rights" => "Public Domain"
            ]
        ];
        $actual = $service->parseDublinCoreFields( self::$singleMetsContents );
        $this->assertEquals( $expected, $actual );
    }

    public function test_given_a_mets_file_containing_two_ingested_files_when_parsing_it_finds_two_files()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $actual = $service->parseDublinCoreFields( self::$multiMetsContents );
        $this->assertCount( 2, $actual );
    }

    public function test_given_a_mets_file_containing_no_dublin_core_metadata_it_is_handled_gracefully()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $actual = $service->parseDublinCoreFields( self::$noDublinCoreMetsContents );
        $this->assertCount( 0, $actual );
    }


    public function test_given_a_mets_file_for_multiple_ingested_files_when_parsing_dublin_core_fields_they_are_related_to_their_file()
    {
        $service = $this->app->make( MetsParserInterface::class );
        $actual = $service->parseDublinCoreFields( self::$multiMetsContents );
        $expected = [
            "PolarBear.png" => [
                "title" => "Polar Bear on Svalbard",
                "creator" => "A photographer",
                "subject" => "A polar bear",
                "description" => "It looks quite polary and also mostly beary",
                "publisher" => "Piql AS",
                "contributor" => "A contributor",
                "date" => "3.30.2020",
                "type" => "Photograph",
                "format" => "Portable Network Graphics",
                "identifier" => "polarbear",
                "source" => "A stock photo repository",
                "language" => "n/a",
                "relation" => "AWA",
                "coverage" => "Great",
                "rights" => "Some rights reserved"
            ],
            "Svalbard.png" => [
                "title" => "Svalbard",
                "creator" => "Some photographer",
                "subject" => "AWA and the seed vault",
                "description" => "A lot of ice and snow",
                "publisher" => "Some publisher",
                "contributor" => "Some other contributor",
                "date" => "30.3.2020",
                "type" => "Photography",
                "format" => "Portable Network Graphics",
                "identifier" => "svb",
                "source" => "Stock photo",
                "language" => "N/A",
                "relation" => "AWA",
                "coverage" => "Decent",
                "rights" => "Several rights reserved"
            ]
        ];
        $this->assertEquals( $expected, $actual );
    }
}
