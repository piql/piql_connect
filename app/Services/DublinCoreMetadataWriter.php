<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\MetadataWriterInterface;

class DublinCoreMetadataWriter implements MetadataWriterInterface
{
    private $filename;

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
        $this->filename = $params['filename'];

        // append header
        Storage::append(
            $this->filename,
            'filename,'.collect($this->header)->map(function($col) {
                return $col;
            })->implode(',')
        );
    }

    public function write(array $parameter): bool
    {
        $metadata = $parameter['metadata'];

        // serialize metadata
        Storage::append(
            $this->filename,
            'data/'.$parameter['object'].','.collect($this->header)->map(function($col, $key) use ($metadata) {
                return ($metadata[$key] ?? '');
            })->implode(',')
        );
        return true;
    }
}
