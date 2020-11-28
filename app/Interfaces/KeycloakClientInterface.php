<?php


namespace App\Interfaces;

use App\Auth\Group;
use App\User;

interface KeycloakClientInterface
{
    /**
     * @return []User
    */
    public function getUsers() : array;

    //UserAuthProvider
    public function changePassword($id, $password);
    public function logoutUser($id);
    public function blockUser($id);
    public function unblockUser($id);
    public function createUser(string $organizationId, User $user) : User;
    public function editUser(string $userId, User $user) : User;
    public function deleteUser(string $userId) : void;

    public function getUserById($id);
    public function searchOrganizationUsers($params=[], $limit=20, $offset=0);

    public function getRoles(): array;
    public function getRoleByName($name): array;
    public function getRoleUsers($name): array;
    public function createGroup(Group $group): array;
    public function getGroups($q='', $limit = 20, $offset = 0): array;
    public function getGroupUsers($limit = 20, $offset = 0): array;
    public function getUserGroups($q='', $limit = 20, $offset = 0): array;
    public function addUserToGroup($userId, $groupId): array;
    public function addRoleToGroup($roleId, $groupId): array;
    public function getGroupRoles($limit = 20, $offset = 0): array;

    //method implementations to ve skipped due to scope change
    /* public function removeUserFromGroup($userId, $groupId): array;
    public function removeRoleFromGroup($userId, $roleId): array;
    public function showUserPermissions($userId): array; */
}
