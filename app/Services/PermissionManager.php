<?php

namespace App\Services;

use App\Enums\PermissionType;
use App\Permission;

class PermissionManager 
{
    public static function createGroup($name, $description) 
    {
        $group = new Permission;
        $group->name = $name;
        $group->description = $description;
        $group->type = PermissionType::Group;
        return $group;
    } 

    public static function createAction($groupId, $name, $description) 
    {
        $action = new Permission;
        $action->name = $name;
        $action->description = $description;
        $action->parent_id = $groupId;
        $action->type = PermissionType::Action;
        return $action;
    } 
}