<?php


namespace App\Interfaces;

use App\User;

interface KeycloakClientInterface
{
    // public function getUsers();
    public function getUsers() : array;

    //UserAuthProvider
    public function changePassword($id, $password);
    public function logoutUser($id);
    public function blockUser($id);
    public function unblockUser($id);
    public function createUser(string $organizationId, User $user) : User;
    public function editUser(string $userId, User $user) : User;
    public function deleteUser(string $organizationId) : void;

    public function getUserById($id);
    public function searchOrganizationUsers($orgId, $params=[], $limit=20, $offset=0);
}
