<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Events\InitiateTransferToArchivematicaEvent;
use App\Interfaces\MetadataGeneratorInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;
use BagitUtil;
use App\Traits\BagOperations;
use App\MetadataPath;

class CommitFilesToBagListener implements ShouldQueue
{
    use BagOperations;
    protected $metadataGenerator;
    protected $bagIt;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(BagitUtil $bagIt = null, MetadataGeneratorInterface $metadataGenerator)
    {
        $this->bagIt = $bagIt ?? new BagitUtil();
        $this->metadataGenerator = $metadataGenerator;
    }

    /**
     * Handle the event.
     *
     * @param  BagFilesEvent  $event
     * @return void
     */
    public function handle( $event )
    {
        $bag = $event->bag;
        if( !$this->tryBagTransition( $bag, "bag_files" ) ){
            Log::error(" ArchivematicaIngestingListener: Failed transition for bag with id {$bag->id} from state '{$bag->status}' to state 'bag_files'" );
            return;
        }

        $files = $bag->files;

        // Create a bag
        if(!$bag->files->count())
        {
            Log::error("Empty bag(".$bag->id.")");
            event( new ErrorEvent ($bag) );
            return;
        }

        $metadataType = env( 'APP_AM_INGEST_METADATA_FILE_FORMAT', "csv");
        $metadataFileName = Str::random(40)."-metadata.".$metadataType;
        $metadataWriter = $this->metadataGenerator->createMetadataWriter([
            'filename' => $metadataFileName,
            'type' => $metadataType,
        ]);

        if( $bag->owner()->first()->settings->getIngestMetadataAsFileAttribute() !== true ) {
            $metadataPlaceholders = [
                (object)[
                    "class" => \App\AccountMetadata::class,
                    "object" => MetadataPath::ACCOUNT_OBJECT
                ],
                (object)[
                    "class" => \App\ArchiveMetadata::class,
                    "object" => MetadataPath::ARCHIVE_OBJECT
                ],
                (object)[
                    "class" => \App\HoldingMetadata::class,
                    "object" => MetadataPath::HOLDING_OBJECT
                ]
            ];

            $uuid = $bag->uuid;
            $metadataGenerated = collect($metadataPlaceholders)->map(function($metadataPlaceholder) use($metadataWriter, $uuid){

                $query = $metadataPlaceholder->class::whereHasMorph('parent', [\App\Bag::class], function(Builder $query) use ($uuid) {
                    $query->where("uuid", $uuid);
                });
                // append metadata to file
                $writeSuccess = $metadataWriter->write([
                    'object' => $metadataPlaceholder->object,
                    'metadata' => $query->get()->first()->metadata
                ]);

                if (!$writeSuccess) {
                    Log::error("Generating " . $metadataPlaceholder->class . "metadata for Bag " . $uuid . " failed!");
                }

                return $writeSuccess;
            })->reduce(function($a, $b) { return $a && $b; }, true);

            if (!$metadataGenerated) {
                event(new ErrorEvent($bag));
            }
        }

        foreach ($files as $file)
        {
            if( ($file->filename === "metadata.csv") && $bag->owner()->first()->settings->getIngestMetadataAsFileAttribute() )
                $this->bagIt->addMetadataFile($file->storagePathCompleted(), MetadataPath::FILE_OBJECT_PATH.$file->filename);
            else
                $this->bagIt->addFile($file->storagePathCompleted(), MetadataPath::FILE_OBJECT_PATH.$file->filename);

            if( $bag->owner()->first()->settings->getIngestMetadataAsFileAttribute() !== true ) {
                if ($file->metadata->count() > 0) {
                    // append metadata to file
                    $retval = $metadataWriter->write([
                        'object' => MetadataPath::FILE_OBJECT_PATH.$file->filename,
                        'metadata' => $file->metadata[0]->metadata
                    ]);
                    if (!$retval) {
                        Log::error("Generating metadata for Bag " . $bag->id . " failed!");
                        event(new ErrorEvent($bag));
                    }
                }
            }
        }
        if(!$metadataWriter->close()) {
            Log::error("Generating metadata for Bag " . $bag->id . " failed!, Unable to close file");
            event(new ErrorEvent($bag));
        }

        // add metadata file to bagit tool
        if( Storage::exists($metadataFileName) && ( $bag->owner()->first()->settings->getIngestMetadataAsFileAttribute() !== true ) ) {
            $this->bagIt->addMetadataFile(Storage::path($metadataFileName), "metadata.".$metadataType);
        }

        $result = $this->bagIt->createBag($bag->storagePathCreated());

        // delete metadata file
        if( Storage::exists($metadataFileName) && ( ! env('APP_DEBUG_SAVE_METADATA_FILE', false) ) ){
            Storage::delete($metadataFileName);
        }

        if($result)
        {
            event( new BagCompleteEvent ($bag) );
        }
        else
        {
            Log::error("Bag ".$bag->id." failed!");
            event( new ErrorEvent($bag) );
        }
        if(file_exists($metadataFileName)) {
            unlink($metadataFileName);
        }
    }
}
