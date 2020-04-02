<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Tenancy\Hooks\Migration\Events\ConfigureSeeds;

class ConfigureSeedsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ConfigureSeeds $event)
    {
        dump(__CLASS__);
        $path = env("TENANT_SEED_PATH",database_path().DIRECTORY_SEPARATOR.'seeds'.DIRECTORY_SEPARATOR.'tenant'.DIRECTORY_SEPARATOR.'DatabaseSeeder.php');
        $seeder = basename($path, '.php');
        dump($seeder);
        $event->seed($seeder);
        //dump($event);
    }
}
