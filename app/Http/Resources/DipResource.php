<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StorageLocationResource;

class DipResource extends JsonResource
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
            'external_uuid' => $this->external_uuid,
            'owner' => $this->owner,
            'aip_external_uuid' => $this->aip_external_uuid,
            'storage_location_id' => $this->storage_location_id,
            'storage_path' => $this->storage_path,
            'storage_properties' => new StoragePropertiesResource( $this->storage_properties )
        ]; 
    }
}
