<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {host} {path} {slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $host = $this->argument('host');
        $path = $this->argument('path');
        $slug = $this->argument('slug');

        if(Customer::where("slug", $slug)->count() > 0) {
            return;
        }

         $customer = new Customer([
            "portal_hostname" => $host,
            "portal_path" => $path,
            "slug" => $slug,
        ]);
        $customer->save();
        \Tenancy\Facades\Tenancy::setTenant($customer);

        event(new \Tenancy\Tenant\Events\Created($customer));
    }
}
