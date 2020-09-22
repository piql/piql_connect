<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HoldingResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'owner_archive_uuid' => $this->owner_archive_uuid,
            'position' => $this->position,
            'parent_id' => $this->parent_id,
            'uuid' => $this->uuid,
            'defaultMetadataTemplate' => $this->defaultMetadataTemplate
        ];
    }
}
