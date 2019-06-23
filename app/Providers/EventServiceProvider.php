<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\FileUploadedEvent::class=> [
            \App\Listeners\LogUploadedFilesListener::class,
        ],
        \App\Events\BagFilesEvent::class => [
            \App\Listeners\CommitFilesToBagListener::class,
        ],
        \App\Events\BagCompleteEvent::class => [
            \App\Listeners\SendBagToArchivematicaListener::class
        ],
        \App\Events\ReceivedStatusFromArchivematicaEvent::class => [
            \App\Listeners\UpdateIngestStatusListener::class
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
