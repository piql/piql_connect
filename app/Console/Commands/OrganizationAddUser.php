<?php

namespace App\Console\Commands;

use App\Interfaces\KeycloakClientInterface;
use App\Organization;
use App\Traits\CommandDisplayUtil;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Webpatser\Uuid\Uuid;

class OrganizationAddUser extends Command
{
    use CommandDisplayUtil;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organization:adduser {organizationId} {firstName} {lastName} {email} {username} {--yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to an existing organization';

    private $keycloakClient;

    /**
     * Create a new command instance.
     *
     * @param KeycloakClientInterface $keycloakClient
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
        $organizationId = $this->argument('organizationId');
        $firstName = $this->argument('firstName');
        $lastName = $this->argument('lastName');
        $email = $this->argument('email');
        $username = $this->argument('username');
        $alwaysYes = $this->option('yes');

        $validator = Validator::make([
            "firstName" => $firstName,
            "lastName" => $lastName,
            "userName" => $username,
            "email" => $email
        ], [
            'firstName' => 'required',
            'lastName' => 'required',
            'userName' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }
            return 1;
        }

        $organization = Organization::where("uuid",$organizationId)->first();
        if(!isset($organization)) {
            $this->error("No organization with uuid '{$organizationId}' found");
            return 1;
        }

        /**
         * todo:
         *   for this to really work we need to tie an organization to an realm
         *   or specific keycloak client
         */
        $user = User::make([
            'username' => $username,
            'password' => Hash::make(Uuid::generate()),
            'full_name' => $firstName." ".$lastName,
            'email' => $email,
            'organization_uuid' => $organization->uuid,
        ]);


        if (!$alwaysYes) {
            $this->displayUser($user, true);
            if(!$this->confirm("Add this user to organization '{$organization->name}'. Do you wish to continue?")) {
                return 1;
            }
        }

        try {
            DB::transaction(function() use ($user, $organization){
                $user->save();
                $organization->users()->save($user);
                $this->keycloakClient->createUser($organization->uuid, $user);
            });
        } catch (\Throwable $e) {
            $this->error('Failed to add user');
            $this->error($e->getMessage());
            return 1;
        }
        $this->info("Added user with id " . $user->id . " to '{$organization->name}'");
        return 0;
    }
}
