<?php

namespace App\Console\Commands;

use App\Organization;
use App\Traits\CommandDisplayUtil;
use Illuminate\Console\Command;

class OrganizationList extends Command
{
    use CommandDisplayUtil;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organization:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List organizations';

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
        Organization::all()->map(function($organization) {
            $this->displayOrganization($organization);
        });
        return 0;
    }
}
