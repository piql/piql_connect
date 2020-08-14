<?php

namespace App;

/**
 * Class AccessControlResponse
 * 
 * @package App
 * 
 * @OA\Schema(
 *     description="Holds data for Permissions, Roles, Permission",
 *     title="AccessControlResponse",
 *     required={"name"}
 * )
*/
class AccessControlResponse
{
    public $name;
    /**
     * @OA\Property(
     *     description="Access Control Name",
     *     title="name",
     *     format="string"
     * )
     */   
    
    public $description;
     /**
     * @OA\Property(
     *     description="Access Control Description",
     *     title="description",
     *     format="string"
     * )
     */

    
}
