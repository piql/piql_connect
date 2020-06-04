<?php

namespace App\Http\Resources;

use App\Enums\PermissionType;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "id" => $this->id,
            "type" => PermissionType::getDescription($this->type),
            "name" => $this->name,
            "description" => $this->description,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
        switch($this->type) {
            case PermissionType::Group:
                if($this->actions) $data['actions'] = $this->actions;
            break;
        }
        return $data;
    }
}
