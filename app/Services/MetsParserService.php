<?php

namespace App\Services;

/**
 * MetsParserService is a service for extracting contents of Archivematica METS files
 *
 * MetsParserService implements the MetsParserInterface and currently provides one public method,
 * parseDublinCoreFields. This takes the raw contents of a METS in the form of a string
 * and returns a nested array with one entry per each of the 15 fields from the
 * Dublin Core™ Metadata Element Set, Version 1.1
 * as per https://www.dublincore.org/specifications/dublin-core/dces/
 */

class MetsParserService implements \App\Interfaces\MetsParserInterface
{

    public function __construct( $app )
    {
    }

    public function parseDublinCoreFields( string $mets ) : array
    {
        $xml = simplexml_load_string( $mets );
        $files = collect( $this->parseStructMapFiles( $xml ) );
        $dc = $files->filter( function ($file, $filename) { return isset( $file["dmdId"] ); } )
                    ->flatMap( function ( $file, $filename ) use ( $xml ) {
            return [ $filename => $this->mapDublinCoreFields( $xml, $file["dmdId"] ) ];
        } );
        return $dc->toArray();
    }


    private function parseDublinCoreFieldsForFile( string $mets, string $filename ) : array
    {
        return [$filename => $this->parseDublinCoreFields( $mets, $filename )];
    }

    public function parseStructMapFiles( \SimpleXMLElement $xml ): array
    {


        $filenames = collect( $xml->xpath(
            "//mets:mets/mets:structMap[@TYPE='physical']".
            "/mets:div[@TYPE='Directory']".
            "/mets:div[@LABEL='objects'][@TYPE='Directory']".
            "/mets:div[@TYPE='Item']/@LABEL"
        ))->map( function( $label ) { return (string)$label; } );


        $dmdIds = collect(
            $filenames->flatMap( function ( $file ) use ( $xml ) {
                return collect( $xml->xpath(
                    "//mets:mets/mets:structMap[@TYPE='physical']".
                    "/mets:div[@TYPE='Directory']".
                    "/mets:div[@LABEL='objects'][@TYPE='Directory']".
                    "/mets:div[@TYPE='Item'][@LABEL='{$file}']".
                    "/@DMDID"
                ) )->flatMap( function ($f) use ( $file )  {
                    return [ $file => [ "dmdId" => (string)$f ] ];
                } );
            } )
        );

        $fileIds = collect(
            $filenames->flatMap( function ( $file ) use ( $xml ) {
                return collect( $xml->xpath(
                    "//mets:mets/mets:structMap[@TYPE='physical']".
                    "/mets:div[@TYPE='Directory']".
                    "/mets:div[@LABEL='objects'][@TYPE='Directory']".
                    "/mets:div[@TYPE='Item'][@LABEL='{$file}']".
                    "/mets:fptr/@FILEID"
                ) )->flatMap( function ( $f ) use ( $file ) {
                    return [ $file => [ "fileId" => (string)$f ] ];
                } );
            } )
        );

        $files = $dmdIds->mergeRecursive( $fileIds );
        return $files->toArray();
    }

    public function mapDublinCoreFields( \SimpleXMLElement $xml, string $dmdId ) : array
    {
        $xml->registerXPathNamespace("dc", "http://purl.org/dc/elements/1.1/");
        $xml->registerXPathNamespace("dcterms", "http://purl.org/dc/terms/");

        $dcFields = collect([
            'title','creator','subject','description','publisher',
            'contributor','date','type','format','identifier',
            'source','language','relation','coverage','rights'
        ]);

        $result = $dcFields->flatMap( function ( $field ) use ( $xml, $dmdId ) {
            return [ $field => (string)$xml->xpath("//mets:mets/mets:dmdSec[@ID='{$dmdId}']/mets:mdWrap[@MDTYPE='DC']/mets:xmlData/dcterms:dublincore/dc:{$field}")[0] ];
        });
        return $result->toArray();
    }
}
