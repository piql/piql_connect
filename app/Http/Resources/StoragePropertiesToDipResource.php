<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoragePropertiesToDipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request )
    {
        $dip = $this->dip;
        if(!$dip) return [];
        return [
            'id' => $dip->id,
            'external_uuid' => $dip->external_uuid,
            'owner' => $dip->owner,
            'aip_external_uuid' => $dip->aip_external_uuid,
            'storage_location_id' => $dip->storage_location_id,
            'storage_path' => $dip->storage_path,
            'storage_properties' => new StoragePropertiesResource( $this ),
            'archived_at' => $dip->created_at
        ];
    }
}
