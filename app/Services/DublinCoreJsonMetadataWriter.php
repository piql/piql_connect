<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Interfaces\MetadataWriterInterface;

class DublinCoreJsonMetadataWriter implements MetadataWriterInterface
{
    private $filename;
    private $isOpen;

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
        $metadata = $parameter['metadata']['dc'];

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
