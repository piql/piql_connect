<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;

class UpdateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:update {host} {path} {slug}';

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

        $slug = $this->argument('slug');

        $customer = Customer::where("slug", $slug)->first();
        $customer->portal_hostname = $this->argument('host');
        $customer->portal_path = $this->argument('path');
        $customer->save();
        \Tenancy\Facades\Tenancy::setTenant($customer);
        event(new \Tenancy\Tenant\Events\Updated($customer));


    }
}
