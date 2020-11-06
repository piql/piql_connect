<?php


namespace App\Interfaces;

use App\User;

interface KeycloakClientInterface
{
    public function getUsers() : array;
    public function createUser(string $accountId, User $user) : User;
    public function editUser(string $userId, User $user) : User;
    public function deleteUser(string $userId) : void;
}
