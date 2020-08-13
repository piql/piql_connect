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
        $dipUuid = $this->storage_properties->dip ? $this->storage_properties->dip->external_uuid : "";
        return [
            'id' => $this->id,
            'title' => $this->storage_properties->name,
            'external_uuid' => $this->external_uuid,
            'owner' => $this->owner,
            'aip_external_uuid' => $this->external_uuid,
            'dip_external_uuid' => $dipUuid,
            'archived_at' => $this->created_at,
            'bucket_name' => $this->jobs->first()->name
        ];
    }
}
