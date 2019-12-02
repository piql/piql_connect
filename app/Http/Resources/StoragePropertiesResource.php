<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BagResource;

class StoragePropertiesResource extends JsonResource
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
            'bag_uuid' => $this->bag_uuid,
            'transfer_uuid' => $this->transfer_uuid,
            'archive_uuid' => $this->archive_uuid,
            'dip_uuid' => $this->dip_uuid,
            'aip_uuid' => $this->aip_uuid,
            'aip_initial_online_storage_location' => $this->aip_initial_online_storage_location,
            'dip_initial_storage_location' => $this->dip_initial_storage_location,
            'archivematica_service_dashboard_uuid' => $this->archivematica_service_dashboard_uuid,
            'archivematica_service_storage_server_uuid' => $this->archivematica_service_storag_service_uuid,
            'bag' => new BagResource( $this->bag ),
            'archive_title' => $this->archive ? $this->archive->title : null,
            'holding_name' => $this->holding_name
        ];
    }
}
