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
        $transform= [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'status' => $this->status,
            'owner' => $this->owner,
            'fileCount' => $this->files->count(),
            'bagSize' => $this->getBagSize(),
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at
        ];
        if($this->storage_properties){
            /*TODO: This can be part of all results after full system upgrade */
            $archive_uuid = $this->storage_properties->archive_uuid;
            $archive_name = $archive_uuid ? Holding::where('uuid', $archive_uuid)->first()->title : "";
            $transform += [
            'archive_uuid' => $archive_uuid,
            'archive_name' => $archive_name,
            'holding_name' => $this->storage_properties->holding_name,
            'sip_uuid' => $this->storage_properties->sip_uuid,
            'dip_uuid' => $this->storage_properties->dip_uuid,
            'aip_uuid' => $this->storage_properties->aip_uuid
            ];
        };
        return $transform;
    }
}
