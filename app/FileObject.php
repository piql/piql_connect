<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Log;

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

    public function metsDublinCoreMetadata()
    {
        return $this->metadata->filter(
            function( $md ){
                return( isset( $md->metadata["mets"] ) && isset( $md->metadata["mets"]["dc"] ) );
            }
        )->first()->metadata["mets"]["dc"] ?? ["mets" => ["dc" => [] ]];
    }

    /*
     * populateDcMetadata is used for "manually" parsing and setting METS Dublin Core fields for the model
     * */
    public function populateDcMetadata( \App\Interfaces\FileArchiveInterface $fileArchive = null, \App\Interfaces\MetsParserInterface $metsParser = null, string $source = "mets" )
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
        $metsXmlContents = file_get_contents( $metsPath );
        if( $metsXmlContents === false ) {
            Log::warn("Could not get contents of mets at {$metsPath}");
            return;
        }
        $dublinCore = $metsParser->parseDublinCoreFields( $metsXmlContents );
        $fileDc = $dublinCore[$this->filename];

        return $this->populateMetadataFromArray( [ $source => [ "dc" => $fileDc ] ] );
    }

    /*
     * populateMetadataFromArray is used by services to associate metadata fields with the model
     *
     * $fileMetadata - An array
     * */
    public function populateMetadataFromArray( array $fileMetadata, string $source )
    {
        $metadata = new Metadata([
            "uuid" => Str::uuid(),
            "modified_by" => $this->storable->owner,
            "metadata" => [$source => $fileMetadata],
            "owner_id" => $this->storable->owner()->organization->archive->uuid,
            "owner_type" => "App\Archive",
        ]);
        $metadata->parent()->associate($this);
        $metadata->save();
        return $metadata;
    }
}
