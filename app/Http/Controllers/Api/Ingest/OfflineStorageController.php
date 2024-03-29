<?php

namespace App\Http\Controllers\Api\Ingest;

use App\Aip;
use App\Dip;
use App\Http\Resources\AipCollection;
use App\Http\Resources\AipToDipResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
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
use App\Events\CommitJobEvent;
use App\Interfaces\FileArchiveInterface;
use App\Mail\PiqlIt;
use App\Traits\UserSettingRequest;
use Illuminate\Support\Facades\DB;
use Response;
use Log;

class OfflineStorageController extends Controller
{
    use UserSettingRequest;
    
    private $nameValidationRule = '/(^[^:\\<>"\/?*|]{3,64}$)/';
    private $newNameValidationRule = '/(^[^:\\<>"\/?*|]{0,64}$)/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function jobs( Request $request)
    {
        $jobs = Job::where('owner', Auth::id() )
            ->whereIn('status', ['created','closed'])
            ->withCount('aips')
            ->latest()
            ->paginate($this->rowLimit(Auth::user(), $request));

        return new JobCollection( $jobs );
    }

    public function job($jobId)
    {
        $job = Job::findOrFail($jobId);
        return  response()->json( ["data" => $job] );
    }

    public function archiveJobs( Request $request)
    {
        
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $jobs = \App\Job::whereIn('status', ['transferring', 'preparing', 'writing', 'storing']);
        return new JobCollection( $jobs->paginate( $this->rowLimit(Auth::user(), $request) ) );
    }


    public function dips(Request $request ,$jobId)
    {
        $job = Job::findOrFail($jobId);
        return AipToDipResource::collection($job->aips()->paginate($this->rowLimit(Auth::user(), $request)));
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


    public function update(Request $request, $jobId)
    {
        $job = Job::findOrFail($jobId);
        // This is a bit nasty because there is no owner validation here
        // Should be safe when used internally e.i when owner is valid
        $data = request()->validate([
            'name' => 'string|nullable|max:64',
            'status' => 'string',
        ]);

        if(array_key_exists('name', $data)) {
            if($this->validateFails($data['name'], $this->newNameValidationRule))
            {
                abort(response()->json(["error" => 400, "message" => "Bucket doesn't have a valid name: {$job->name}"], 400));
            }
            $job->name = $data['name'] ?? "";
            $job->save();
        }

        if(isset($data['status']) && ($data['status'] == 'commit')) {
            if($this->validateFails($job->name))
            {
                abort(response()->json(["error" => 424, "message" => "Bucket doesn't have a valid name: {$job->name}"], 424));
            }
            $job->applyTransition('piql_it');
            $job->save();
            $emailTo = env('PIQLIT_NOTIFY_EMAIL_TO');
            if ($emailTo) {
                try {
                    Mail::to($emailTo)->send(new PiqlIt($job, $request->getSchemeAndHttpHost()));
                } catch (\Throwable $e) {
                    Log::error("Error on sending e-mail to: " . $emailTo);
                    Log::error($e->getMessage());
                }
            }
            event(new CommitJobEvent($job));
        }

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
