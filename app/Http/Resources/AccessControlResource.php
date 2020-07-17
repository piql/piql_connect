<?php

namespace App\Http\Resources;

use App\Enums\AccessControlType;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessControlResource extends JsonResource
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
            "type" => AccessControlType::getDescription($this->type),
            "name" => $this->name,
            "description" => $this->description,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
            "group_id" => $this->group_id
        ];
        switch($this->type) {
            case AccessControlType::PermissionGroup:
            case AccessControlType::Role:
                if($this->actions) $data['permissions'] = $this->permissions;
            break;
        }
        return $data;
    }
}
