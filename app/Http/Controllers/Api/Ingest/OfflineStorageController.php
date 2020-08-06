<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Dip;
use App\Http\Resources\AipCollection;
use App\Http\Resources\AipToDipResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Job;
use App\User;
use App\Bag;
use App\File;
use App\Http\Resources\JobResource;
use App\Http\Resources\JobCollection;
use App\Http\Resources\BagCollection;
use App\Events\BagFilesEvent;
use Response;
use Log;

class OfflineStorageController extends Controller
{
    private $nameValidationRule = '/(^[^:\\<>"\/?*|]{3,64}$)/';
    private $newNameValidationRule = '/(^[^:\\<>"\/?*|]{0,64}$)/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jobs()
    {
        $jobs = Job::where('owner', Auth::id() )
            ->where(function($query) {
                $query->where('status', 'created')
                    ->orWhere('status', 'closed');
            })
            ->withCount('aips')
            ->latest()
            ->paginate( env('DEFAULT_ENTRIES_PER_PAGE') );

        return new JobCollection( $jobs );
    }

    public function job($jobId)
    {
        $job = Job::findOrFail($jobId);
        return  response()->json( ["data" => $job] );
    }

    public function archiveJobs()
    {
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $jobs = \App\Job::where('status', '=', 'ingesting');

        return new JobCollection( $jobs->paginate( env('DEFAULT_ENTRIES_PER_PAGE') ) );
    }


    public function dips($jobId)
    {
        $job = Job::findOrFail($jobId);
        return AipToDipResource::collection( $job->aips()->paginate( env('DEFAULT_ENTRIES_PER_PAGE') ) );
    }

    public function detachDip($jobId, $dipId)
    {
        $job = Job::find($jobId);
        if(!isset($job) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Job with id '.$jobId.' was found.'], 404) );
        }

        $dip = Dip::find($dipId);
        if(!isset($dip) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Dip with id '.$dipId.' was found.'], 404) );
        }

        $job->aips()->detach($dip->storage_properties->aip);
        return response()->json([], 204 );
    }


    public function update($jobId)
    {
        $job = Job::findOrFail($jobId);
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $data = request()->validate([
            'name' => 'string|nullable|max:64',
            'status' => 'string',
        ]);

        if(array_key_exists('name', $data)) {
            if($this->validateFails($job->name, $this->newNameValidationRule))
            {
                abort(response()->json(["error" => 424, "message" => "Bucket doesn't have a valid name: {$job->name}"], 424));
            }
            $job->name = $data['name'] ?? "";
        }

        if(isset($data['status']) && ($data['status'] == 'ingesting' )) {
            if($this->validateFails($job->name))
            {
                abort(response()->json(["error" => 424, "message" => "Bucket doesn't have a valid name: {$job->name}"], 424));
            }
            $job->status = "ingesting";
        }
        $job->save();
        return new JobResource( $job );
    }

    public function delete($jobId)
    {
        $job = Job::find($jobId);
        if(!isset($job) ) {
            abort( response()->json(['error' => 404, 'message' => 'No Job with id '.$jobId.' was found.'], 404) );
        }
        $job->aips()->detach();
        $job->delete();
        return response()->json([], 204 );
    }

    private function validateFails($name, $rule = null) : bool {
        if(!preg_match($rule ?? $this->nameValidationRule, $name, $matches, PREG_OFFSET_CAPTURE)) {
            return true;
        }
        return false;
    }

}
