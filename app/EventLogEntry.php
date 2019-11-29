<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EventLogEntry extends Model
{
    use SoftDeletes;

    protected $table = 'event_log_entries';
    protected $fillable = [
        'type',
        'severity',
        'message',
        'exception',
        'context_type',
        'context_id'
    ];

    public function context()
    {
        return $this->morphTo();
    }

    public function contextType()
    {
        return Str::lower( Str::after( $this->context_type, "\\" ) );
    }

}
