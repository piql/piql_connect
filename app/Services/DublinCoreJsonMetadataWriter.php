<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\MetadataWriterInterface;

class DublinCoreJsonMetadataWriter implements MetadataWriterInterface
{
    private $filename;
    private $isOpen;

    private $header = [
        "dc:title"       => "dc.title",
        "dc:creator"     => "dc.creator",
        "dc:subject"     => "dc.subject",
        "dc:description" => "dc.description",
        "dc:publisher"   => "dc.publisher",
        "dc:contributor" => "dc.contributor",
        "dc:date"        => "dc.date",
        "dc:type"        => "dc.type",
        "dc:format"      => "dc.format",
        "dc:identifier"  => "dc.identifier",
        "dc:source"      => "dc.source",
        "dc:language"    => "dc.language",
        "dc:relation"    => "dc.relation",
        "dc:coverage"    => "dc.coverage",
        "dc:rights"      => "dc.rights"
    ];

    public function __construct( array $params )
    {
        $this->isOpen = true;
        $this->filename = $params['filename'];

        // append header
        Storage::append(
            $this->filename, "["
        );
    }

    public function __destruct()
    {

        $this->close();
    }

    public function write(array $parameter): bool
    {
        $metadata = $parameter['metadata'];

        // serialize metadata
        return Storage::append( $this->filename, json_encode( array_merge(
                ["filename" => 'objects/'.$parameter['object']],
                collect($this->header)->flatMap(function($col, $key) use ($metadata) {
                    return isset($metadata[$key]) ? [$col => $metadata[$key]] : null;
                })->except(null)->toArray()
        )));
    }

    public function close(): bool
    {
        if($this->isOpen) {
            $this->isOpen = false;
            return Storage::append(
                $this->filename, "]"
            );
        }
        return true;
    }
}
