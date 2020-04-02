<?php

namespace App\Http\Resources;
use App\Metadata;
use Webpatser\Uuid\Uuid;

use Illuminate\Http\Resources\Json\JsonResource;

class FileObjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        return [
            'id' => $this->id,
            'fullpath' => $this->fullpath,
            'filename' => $this->filename,
            'path' => $this->path,
            'size' => $this->size,
            'object_type' => $this->object_type,
            'info_source' => $this->info_source,
            'mime_type' => $this->mime_type,
            'storable_type' => $this->storable_type,
            'storable_id' => $this->storable_id,
            'metadata' => [ 'ingested' => $this->firstDublinCoreMetadata() ]
        ];
    }
}
