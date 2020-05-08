<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\MetadataWriterInterface;

class DublinCoreCSVMetadataWriter implements MetadataWriterInterface
{
    private $filename;

    private $header = [
        "title"       => "dc.title",
        "creator"     => "dc.creator",
        "subject"     => "dc.subject",
        "description" => "dc.description",
        "publisher"   => "dc.publisher",
        "contributor" => "dc.contributor",
        "date"        => "dc.date",
        "type"        => "dc.type",
        "format"      => "dc.format",
        "identifier"  => "dc.identifier",
        "source"      => "dc.source",
        "language"    => "dc.language",
        "relation"    => "dc.relation",
        "coverage"    => "dc.coverage",
        "rights"      => "dc.rights"
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

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function write(array $parameter): bool
    {
        $metadata = $parameter['metadata']['dc'];

        // serialize metadata
        Storage::append(
            $this->filename,
            'data/'.$parameter['object'].','.collect($this->header)->map(function($col, $key) use ($metadata) {
                return ($metadata[$key] ?? '');
            })->implode(',')
        );
        return true;
    }

    public function close() : bool {
        return true;
    }
}
