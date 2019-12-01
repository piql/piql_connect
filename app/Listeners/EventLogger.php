<?php

namespace App\Listeners;

use App\EventLogEntry;
use App\Events\ArchivematicaApproveTransferError;
use App\Events\ArchivematicaGetFileDetailsError;
use App\Events\ArchivematicaGetTransferStatusError;
use App\Events\ArchivematicaGetUnapprovedListError;
use App\Events\ArchivematicaTransferError;
use App\Events\ArchivematicaTransferringEvent;
use Illuminate\Events\Dispatcher;

class EventLogger
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
    public function logInfoEvents($event) {
        $vars = get_object_vars($event);
        $eventLogEntry = new EventLogEntry([
            'severity' => "INFO",
            'type' => get_class($event),
            'message' => $vars['message'] ?? "",
            'exception' => $vars['exception'] ?? "",
        ]);

        if(isset($vars['bag'])) {
            $eventLogEntry->context()->associate($vars['bag']);
        }
        $eventLogEntry->save();
    }


    /**
     * Handle info events.
     */
    public function logErrorEvents($event) {
        $vars = get_object_vars($event);
        $eventLogEntry = new EventLogEntry([
            'severity' => "ERROR",
            'type' => get_class($event),
            'message' => $vars['message'] ?? "",
            'exception' => $vars['exception'] ?? "",
        ]);

        if(isset($vars['bag'])) {
            $eventLogEntry->context()->associate($vars['bag']);
        }
        $eventLogEntry->save();
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        if(false) {
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
                'App\Listeners\EventLogger@logInfoEvents'
            );
        }

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
            'App\Listeners\EventLogger@logErrorEvents'
        );

    }
}
