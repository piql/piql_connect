<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class AccessControl
 * 
 * @package App
 * 
 * @OA\Schema(
 *     description="Holds data for Permissions, Roles, Permission",
 *     title="AccessControl",
 *     required={"name"}
 * )
*/
class AccessControl extends Model
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
