<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'status' => $this->status,
            'owner' => $this->owner,
            'fileCount' => $this->files->count(),
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at,
            'archive_uuid' => $this->storage_properties->archive_uuid,
            'holding_name' => $this->storage_properties->holding_name,
            'sip_uuid' => $this->storage_properties->sip_uuid,
            'dip_uuid' => $this->storage_properties->dip_uuid,
            'aip_uuid' => $this->storage_properties->aip_uuid
        ];
    }
}
