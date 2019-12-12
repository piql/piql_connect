<?php

namespace App\Listeners;

use App\EventLogEntry;
use App\Events\ArchivematicaApproveTransferError;
use App\Events\ArchivematicaGetFileDetailsError;
use App\Events\ArchivematicaGetTransferStatusError;
use App\Events\ArchivematicaGetUnapprovedListError;
use App\Events\ArchivematicaTransferError;
use App\Events\ArchivematicaTransferringEvent;
use App\Events\NotificationEvent;
use Illuminate\Events\Dispatcher;

class NotificationDispatcher
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
     * Handle info events.
     */
    public function dispatchErrorEvents($event) {

        $vars = get_object_vars($event);
        event(new NotificationEvent("Error", $event->bag->owner, [
            'type' => get_class($event),
            'bag' => $event->bag,
            'message' => $vars['message'] ?? "",
            'exception' => $vars['exception'] ?? ""
        ]));
    }



    /**
     * Handle info events.
     */
    public function dispatchInfoEvents($event) {

        $vars = get_object_vars($event);
        event(new NotificationEvent("Info", $event->bag->owner, [
            'type' => get_class($event),
            'bag' => $event->bag
        ]));
    }




    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen([
            'App\Events\ConnectionError',
            'App\Events\ArchivematicaInitiateTransferError',
            'App\Events\ArchivematicaGetUnapprovedListError',
            'App\Events\ArchivematicaApproveTransferError',
            'App\Events\ArchivematicaGetTransferStatusError',
            'App\Events\ArchivematicaTransferError',
            'App\Events\ArchivematicaGetIngestStatusError',
            'App\Events\ArchivematicaIngestError',
            'App\Events\ArchivematicaGetFileDetailsError',
            'App\Events\BagStateTransitionError',
            'App\Events\ErrorEvent',
        ],
            'App\Listeners\NotificationDispatcher@dispatchErrorEvents'
        );

        $events->listen([
            'App\Events\ApproveTransferToArchivematicaEvent',
            'App\Events\ArchivematicaIngestingEvent',
            'App\Events\ArchivematicaTransferringEvent',
            'App\Events\BagCompleteEvent',
            'App\Events\BagFilesEvent',
            'App\Events\ClearIngestStatusEvent',
            'App\Events\ClearTmpFilesEvent',
            'App\Events\ClearTransferStatusEvent',
            'App\Events\FileUploadedEvent',
            'App\Events\IngestCompleteEvent',
            'App\Events\InitiateTransferToArchivematicaEvent',
            'App\Events\StartTransferToArchivematicaEvent',
        ],
            'App\Listeners\NotificationDispatcher@dispatchInfoEvents'
        );

    }
}
