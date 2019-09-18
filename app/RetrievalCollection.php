<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\BagResource;

class RetrievalCollection extends Model
{
    protected $table = "retrieval_collections";
    protected $fillable = ['status', 'completed_at'];

    public function retrievalFiles()
    {
        return $this->hasMany('App\RetrievalFile');
    }

    public function sourceFiles()
    {
        return $this->hasManyThrough('App\File', 'App\RetrievalFile', 'file_id', 'id');
    }

    public function firstBag()
    {
        $bag = new Bag();
        $firstSourceFile = $this->sourceFiles()->first();
        if($firstSourceFile){
            $bag = $firstSourceFile->bag()->get();
        }
        return $bag;
    }

    public function aip()
    {
        return "reader_test_reel";
    }

}
