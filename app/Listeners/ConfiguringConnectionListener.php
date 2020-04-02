<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ConfiguringConnectionListener
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
    public function handle($event)
    {
        dump(__CLASS__);
        $path = config_path().DIRECTORY_SEPARATOR.'tenancy'.DIRECTORY_SEPARATOR.'tenant_connection.php';
        dump($path);
        $event->useConfig($path, [
                'database' => $event->tenant->getTenantKey(),
            ]);
        //dump($event);
    }
}
