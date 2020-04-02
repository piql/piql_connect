<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileObject extends Model
{
    use SoftDeletes;

    protected $table = 'file_objects';
    protected $fillable = [
        'fullpath', 'filename', 'path', 'size',
        'object_type', 'info_source', 'mime_type',
        'storable_type', 'storable_id'
    ];

    public function storable()
    {
        return $this->morphTo();
    }

    public function storedObjectType()
    {
        return Str::lower( Str::after( $this->storable_type, "\\" ) );
    }

    public function metadata()
    {
        return $this->morphMany('App\Metadata', 'parent');
    }

    public function firstDublinCoreMetadata()
    {
        $dcFields = $this->metadata->filter( function( $md ){
            return( isset( $md->metadata["dc"] ) );
        })->first()->metadata["dc"] ?? [];
        return ["dc" => $dcFields ];
    }

    public function populateMetadata( \App\Interfaces\FileArchiveInterface $fileArchive = null, \App\Interfaces\MetsParserInterface $metsParser = null )
    {
        if( $this->storable_type != "App\Aip" ) return; //TODO: For now we only support AIPs. Add DIP mets parsing when needed.

        if( $fileArchive == null) {
            $fileArchive = \App::make( \App\Interfaces\FileArchiveInterface::class );
        }
        if( $metsParser == null) {
            $metsParser = \App::make( \App\Interfaces\MetsParserInterface::class );
        }

        $metsFileObject = $this->storable->fileObjects
                              ->where('filename', "METS.{$this->storable->external_uuid}.xml" )
                              ->first();
        if( $metsFileObject == null )  return;

        $metsPath = $fileArchive->downloadFile( $this->storable, $metsFileObject );
        Log::debug( "Downloading mets to: {$metsPath}");
        $metsXmlContents = file_get_contents( $metsPath );
        $dublinCore = $metsParser->parseDublinCoreFields( $metsXmlContents );
        $fileDc = $dublinCore[$this->filename];

        return $this->populateMetadataFromString( $fileDc );
    }

    public function populateMetadataFromArray( array $fileMetadata )
    {
        $metadata = new Metadata([
            "uuid" => Str::uuid(),
            "modified_by" => $this->storable->owner,
            "metadata" => ['dc' => $fileMetadata]
        ]);
        $metadata->parent()->associate($this);
        $metadata->save();
        return $metadata;
    }
}
