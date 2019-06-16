<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngestProcess extends Model
{
    protected $table = 'ingest_processes';
    protected $fillable = [
        'incomingFileId',
        'bagId',
        'sipId',
        'internalStatus',
        'lastKnownExternalStatus'
    ];
}
