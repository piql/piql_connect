<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Tenancy\Hooks\Migration\Events\ConfigureMigrations;

class ConfiguringMigrationListener
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
    public function handle(ConfigureMigrations $event)
    {
        dump(__CLASS__);
        $path = env("TENANT_MIGRATION_PATH",database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.'tenant');
        $event->path($path);
        dump($path);
    }
}
