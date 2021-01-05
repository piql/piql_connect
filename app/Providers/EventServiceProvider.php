<?php

namespace App\Providers;

use App\Events\ArchivematicaTransferringEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Events\Dispatcher;
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
            \App\Listeners\CommitFilesToTransferPacketListener::class,
        ],
        \App\Events\BagCompleteEvent::class => [
            \App\Listeners\SendBagToArchivematicaListener::class,
        ],
        \App\Events\InitiateTransferToArchivematicaEvent::class => [
            \App\Listeners\InitiateTransferToArchivematicaListener::class
        ],
        \App\Events\ApproveTransferToArchivematicaEvent::class => [
            \App\Listeners\ApproveTransferToArchivematicaListener::class
        ],
        \App\Events\IngestCompleteEvent::class => [
            \App\Listeners\IngestCompleteListener::class
        ],
        \App\Events\ArchivematicaTransferringEvent::class => [
            \App\Listeners\ArchivematicaTransferringListener::class
        ],
        \App\Events\ArchivematicaIngestingEvent::class => [
            \App\Listeners\ArchivematicaIngestingListener::class
        ],
        \App\Events\ErrorEvent::class => [
            \App\Listeners\ErrorListener::class
        ],
        \App\Events\ClearTransferStatusEvent::class => [
            \App\Listeners\ClearTransferStatus::class
        ],
        \App\Events\ClearIngestStatusEvent::class => [
            \App\Listeners\ClearIngestStatus::class
        ],
        \App\Events\ClearTmpFilesEvent::class => [
            \App\Listeners\ClearTmpFiles::class
        ],
        \App\Events\InformationPackageUploaded::class => [
            \App\Listeners\AddAipToBucketListener::class
        ],
        \App\Events\PreProcessBagEvent::class => [
            \App\Listeners\PreProcessBagListener::class
        ],
        \App\Events\CommitJobEvent::class => [
            \App\Listeners\CommitJobListener::class
        ],
        \App\Events\OrganizationCreatedEvent::class => [
            \App\Listeners\OrganizationCreatedListener::class
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\EventLogger',
        'App\Listeners\NotificationDispatcher'
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
