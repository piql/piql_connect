<?php


namespace App\Interfaces;


interface KeycloakClientInterface
{
    public function getUsers();

    //UserAuthProvider
    public function changePassword($id, $password);
    public function logoutUser($id);
    public function blockUser($id);
    public function unblockUser($id);
}
