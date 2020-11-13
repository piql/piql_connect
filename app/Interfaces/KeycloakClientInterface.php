<?php


namespace App\Interfaces;

interface KeycloakClientInterface
{
    public function getUsers();
    
    public function getUserById($id);
    
    public function searchOrganizationUsers($orgId, $params=[], $limit=20, $offset=0);
}
