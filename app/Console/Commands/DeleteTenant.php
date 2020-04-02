<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;

class DeleteTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:delete {slug}';

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

        if(!$customer) {
            return;
        }
        $customer->delete();
        \Tenancy\Facades\Tenancy::setTenant($customer);
        event(new \Tenancy\Tenant\Events\Deleted($customer));
    }
}
