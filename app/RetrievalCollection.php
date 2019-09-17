<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetrievalCollection extends Model
{
    protected $table = "retrieval_collections";
    protected $fillable = ['status', 'completed_at'];

    public function retrievalFiles()
    {
        return $this->hasMany('App\RetrievalFile');
    }
}
