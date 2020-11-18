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

    // public function getRoles(): array;
    // public function getRoleByName($name);
    // public function getRoleById($id);
    // public function getSubRoles($id);
    // public function getUserRoles($name);
    // public function createGroup($group);
    // public function getUserGroup($userId);
    // public function addUserToGroup($userId, $groupId);
    // public function addUserToRole($userId, $roleId);
    // public function addRoleToGroup($roleId, $groupId);
    // public function removeUserFromGroup($userId, $groupId);
    // public function removeRoleFromGroup($userId, $roleId);
    // public function showUserPermissions($userId);
}
