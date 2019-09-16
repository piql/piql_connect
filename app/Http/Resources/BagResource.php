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
            'archive_uuid' => $this->archive_uuid,
            'archive_name' => $this->archive_title,
            'holding_name' => $this->holding_name,
        ];
    }
}