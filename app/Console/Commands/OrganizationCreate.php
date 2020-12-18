<?php

namespace App\Console\Commands;

use App\Organization;
use Illuminate\Console\Command;

class OrganizationCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organization:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new organization';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        if(strlen($name) === 0) {
            $this->error("No name given");
            return 1;
        }

        if(Organization::where("name", $name)->count() > 0) {
            $this->error("Organization with name '".$name."' already exists");
            return 1;
        }

        $org = Organization::create(["name" => $name]);
        $this->info("New Organization created with name: '{$name}' and uuid ".$org->uuid);
        return 0;
    }
}
