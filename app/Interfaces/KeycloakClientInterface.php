<?php


namespace App\Interfaces;

interface KeycloakClientInterface
{
    public function getUsers();
    
    public function getUserById($id);
}
