<?php

namespace App\Console\Commands;

use App\Interfaces\KeycloakClientInterface;
use Illuminate\Console\Command;

class KeycloakListusers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keycloak:listusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List keycloak users in realm';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KeycloakClientInterface $keycloakClient)
    {
        parent::__construct();
        $this->keycloakClient = $keycloakClient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = $this->keycloakClient->getUsers();
        echo str_pad('Id', 38) . str_pad('Username', 20) . str_pad('Email', 35) . str_pad('Name', 0) . PHP_EOL;
        foreach ($users as $user) {
            echo str_pad($user['id'], 38);
            echo str_pad($user['username'], 20);
            echo isset($user['email']) ? str_pad($user['email'], 35) : str_pad('', 35);
            echo isset($user['firstName']) ? str_pad($user['firstName'], 0) . ' ' : '';
            echo isset($user['lastName']) ? str_pad($user['lastName'], 0) : '';
            echo PHP_EOL;
        }
    }
}
