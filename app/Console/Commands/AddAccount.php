<?php

namespace App\Console\Commands;

use App\Services\AccountService;
use App\Services\UserRegistrationService;
use App\User;
use Illuminate\Console\Command;
use Throwable;

class AddAccount extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:add {uuid} {--title=} {--description=} {--user_id=} {--user_name=} {--user_email=} {--user_full_name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $uuid = $this->argument('uuid');
        $title = $this->option('title', "");
        $description = $this->option('description', "");
        $userId = $this->option('user_id', "");
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error('userid '.$userId.' not found');
            }        
        } else {
            $userName = $this->option('user_name', "");
            $userEmail = $this->option('user_email', "");
            $userFullName = $this->option('user_full_name', "");
            $errArr = array();
            if (!$userName) {
                $errArr[] = '--user_name';
            }
            if (!$userEmail) {
                $errArr[] = '--user_email';
            }
            if (!$userFullName) {
                $errArr[] = '--user_full_name';
            }
            if (!$errArr) {
                try {
                    $user = UserRegistrationService::registerUser($userFullName, $userName, $userEmail, env('APP_URL'));
                    $this->info("Added user: ".$user->id);
                } catch (Throwable $e) {
                    $user = null;
                    $this->error($e->getMessage());
                }
            } else {
                $user = null;
                $this->error('You are trying to add a new account with a new user, so you must provide the required options:');
                $this->error(implode(', ', $errArr));
                $this->error('If you want to take an existing user, please use --user_id=xxx');
            }
        }
        if ($user) {
            $account = AccountService::createAccount($uuid, $title, $description, get_class($user), $user->id);
            $this->info("Added account: ".$account->uuid);
        }

    }
}
