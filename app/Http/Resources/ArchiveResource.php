<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(!$this->resource) return [];

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'uuid' => $this->uuid
        ];

    }
}
