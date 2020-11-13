<?php


namespace App\Interfaces;

use App\User;

interface KeycloakClientInterface
{
    public function getUsers() : array;
    public function createUser(string $organizationId, User $user) : User;
    public function editUser(string $userId, User $user) : User;
    public function deleteUser(string $organizationId) : void;

    public function getUserById($id);
    public function searchOrganizationUsers($orgId, $params=[], $limit=20, $offset=0);
}
