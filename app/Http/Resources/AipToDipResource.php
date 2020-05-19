<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\StorageLocationResource;

class AipToDipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        if(!$this->storage_properties) return [];
        $dip = $this->storage_properties->dip;
        if(!$dip) return [];
        return [
            'id' => $dip->id,
            'external_uuid' => $dip->external_uuid,
            'owner' => $dip->owner,
            'aip_external_uuid' => $dip->aip_external_uuid,
            'storage_location_id' => $dip->storage_location_id,
            'storage_path' => $dip->storage_path,
            'storage_properties' => new StoragePropertiesResource( $dip->storage_properties ),
            'archived_at' => $dip->created_at
        ];
    }
}
