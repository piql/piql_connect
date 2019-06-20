<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = [
        'fileName', 'uuid', 'bag_uuid'
    ];

    public function bag()
    {
        return $this->belongsTo('App\Bag', 'uuid', 'bag_uuid');
    }

    public function storagePathCompleted()
    {
        return Storage::disk('uploader')->path('completed/' . $this->uuid . '.' . pathinfo($this->filename, PATHINFO_EXTENSION));
    }
}
