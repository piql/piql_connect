<?php

namespace App\Console\Commands;

use App\Interfaces\KeycloakClientInterface;
use Illuminate\Console\Command;
use App\User;

class KeycloakDeleteuser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keycloak:deleteuser {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user in keycloak server';

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
        $userId = $this->argument('id');

        // Find user
        $user = User::find($userId);
        if (!$user) {
            echo 'ERROR: User does not exist';
            exit(1);
        }

        // Delete user in keycloak server
        try {
            $this->keycloakClient->deleteUser($user->id);
            echo 'Deleted user with id ' . $user->id . ' in keycloak server' . PHP_EOL;
        } catch (\Throwable $e) {
            echo 'Failed to delete user' . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }
}
