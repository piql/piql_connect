<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncomingFile extends Model
{
    protected $table = 'incoming_files';
    protected $fillable = [
        'fileName', 'uuid'
    ];

}
