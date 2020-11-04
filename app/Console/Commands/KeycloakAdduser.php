<?php

namespace App\Console\Commands;

use App\Interfaces\KeycloakClientInterface;
use Illuminate\Console\Command;
use App\User;

class KeycloakAdduser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keycloak:adduser {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add piqlConnect user to keycloak realm';

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

        // Add user to keycloak
        try {
            $addedUser = $this->keycloakClient->createUser($user->account_uuid, $user);
            echo 'Added user with id ' . $addedUser->id . ' to keycloak server' . PHP_EOL;
        } catch (\Throwable $e) {
            echo 'Failed to add user' . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }
}
