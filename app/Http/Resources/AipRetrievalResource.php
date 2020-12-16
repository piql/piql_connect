<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AipRetrievalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        $dipId = $this->storage_properties->dip ? $this->storage_properties->dip->id : "";
        return [
            'id' => $this->id,
            'title' => $this->storage_properties->name,
            'external_uuid' => $this->external_uuid,
            'owner' => $this->owner,
            'aip_external_uuid' => $this->external_uuid,
            'dipId' => $dipId,
            'archived_at' => $this->created_at,
            'bucket_name' => $this->jobs->first()->name,
            'size_on_disk' => $this->size,
            'file_count' => $this->fileObjects->count(),
            'collection_uuid' => $this->storage_properties->collection_uuid,
            'holding_name' => $this->storage_properties->holding_name
        ];
    }
}
