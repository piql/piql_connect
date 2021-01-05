<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Holding;

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
            'bagSize' => $this->getBagSize(),
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at,
            'collection_uuid' => $this->storage_properties->collection_uuid,
            'holding_uuid' => $this->storage_properties->holding_uuid,
            'collection_name' => $this->collection_title,
            'holding_name' => $this->storage_properties->holding_name,
            'retrieval_id' => $this->retrieval_id
        ];
    }
}
