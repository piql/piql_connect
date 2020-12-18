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
            'collection_uuid' => $this->collection_uuid,
            'uuid' => $this->uuid,
            'defaultMetadataTemplate' => $this->defaultMetadataTemplate
        ];
    }
}
