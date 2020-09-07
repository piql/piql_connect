<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = [
        'filename', 'uuid', 'bag_id'
    ];

    public function bag()
    {
        return $this->belongsTo('App\Bag', 'bag_id', 'id');
    }

    public function storagePath()
    {
        return 'completed/' . $this->uuid . '.' . pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public function storagePathCompleted()
    {
        return Storage::disk('uploader')->path($this->storagePath());
    }

    public function metadata()
    {
        return $this->morphMany('App\Metadata', 'parent');
    }
}
