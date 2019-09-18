<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetrievalFile extends Model
{
    protected $table = 'retrieval_files';
    protected $fillable = ['file_id', 'retrieval_collection_id'];

    public function retrievalCollection()
    {
        return $this->belongsTo('App\RetrievalCollection');
    }

    public function sourceFile()
    {
        return $this->hasOne('App\File', 'id', 'file_id');
    }

    public function firstBag()
    {
        return $this->sourceFile()->bag();
    }

}
