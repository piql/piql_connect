<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        /*
        if (is_null($this->resource)) {
            return [];
        }
         */

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'status' => $this->status,
            'owner' => $this->owner,
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at,
            'size' => $this->size
        ];
    }
}
