<?php

namespace App\Services;

use \App\Interfaces\PreProcessBagInterface;
use \App\Interfaces\IngestValidationInterface;
use App\Collection;
use App\Bag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use Webpatser\Uuid\Uuid;

class FMUIngestService implements PreProcessBagInterface, IngestValidationInterface
{
    private $app;
    private $fileNamePattern = '/(\w{3})\w?\.(\d+)(-\d+)?/';
    private $holdingPattern = '/(\d+)-(\d+)/';

    public function __construct( $app )
    {
        $this->app = $app;
    }

    public function process( \App\Bag $bag) : array {

        $bags = [];
        $inputBag = $bag;
        $bag->files->map(function($file) use(&$bags, $inputBag) {
            preg_match( $this->fileNamePattern, $file->filename, $matches, PREG_OFFSET_CAPTURE);
            if(count($matches) < 2) {
                return;
            }
            $collectionName = $matches[1][0];
            $holdingNr = $matches[2][0];
            $holdingSubNr = (count($matches) > 3) ? $matches[3][0] : "";
            $key = "$collectionName.$holdingNr"."$holdingSubNr";
            if(!isset($bags[$key])) {

                Collection::where('title','LIKE', "%$collectionName%")->get()->first(function($collection) use(&$bags, $key, $holdingNr, $inputBag) {
                    // figure out holding
                    $holding = $collection->holdings->first(function($holding) use ($holdingNr) {
                        preg_match( $this->holdingPattern, $holding->title, $matches, PREG_OFFSET_CAPTURE);
                        $startRange =  $matches[1][0];
                        $endRange =  $matches[2][0];
                        return !(($startRange+0 > $holdingNr) || ($endRange+0 < $holdingNr));
                    });
                    if(!$holding) {
                        return;
                    }

                    // create a new bag
                    $bag = $inputBag->replicate();
                    $bag->name = $key;
                    $bag->push();

                    $bag = $bag->fresh();
                    $bag->storage_properties()->update([
                        'collection_uuid' => $collection->uuid,
                        'holding_name' => $holding->title
                    ]);
                    $bags[$key] = $bag;
                });
            }
            $file->bag_id = $bags[$key]->id;
            $file->save();
        });
        if($bag->refresh()->files()->count() > 0) {
            $bags[$bag->name] =$bag;
        } else {
            $bag->status = "complete";
            $bag->save();
        }
        // push bag back to the open pool
        return collect($bags)->flatten()->all();
    }


    public function validateFileName( $filename) : bool {
        $pattern = $this->fileNamePattern;
        preg_match( $pattern, $filename, $matches, PREG_OFFSET_CAPTURE);

        if(count($matches) == 0) {
            return false;
        }

        $collectionName = $matches[1][0];
        $holdingNr = $matches[2][0];
        $collections = Collection::where('title','LIKE', "%$collectionName%")->get()->first(function($collection) use ($holdingNr) {
            if(!$collection) {
                return false;
            }
            // figure out holding
            $holding = $collection->holdings->first(function($holding) use ($holdingNr) {
                preg_match( $this->holdingPattern, $holding->title, $matches, PREG_OFFSET_CAPTURE);
                $startRange =  $matches[1][0];
                $endRange =  $matches[2][0];
                return !(($startRange+0 > $holdingNr) || ($endRange+0 < $holdingNr));
            });

            return $holding != null;

        });


        return $collections != null;
    }
}
