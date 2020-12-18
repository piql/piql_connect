<?php

namespace App\Listeners;

use App\Events\BagFilesEvent;
use App\Events\BagCompleteEvent;
use App\Events\ErrorEvent;
use App\Interfaces\MetadataGeneratorInterface;
use App\Interfaces\TransferPacketBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\BagOperations;
use App\MetadataPath;
use Illuminate\Support\Facades\Log;

class CommitFilesToTransferPacketListener implements ShouldQueue
{
    use BagOperations;
    protected $metadataGenerator;
    protected $packetBuilder;

    /**
     * Create the event listener.
     *
     * @param TransferPacketBuilder $packetBuilder
     * @param MetadataGeneratorInterface $metadataGenerator
     */
    public function __construct(TransferPacketBuilder $packetBuilder, MetadataGeneratorInterface $metadataGenerator)
    {
        $this->packetBuilder = $packetBuilder;
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
        $metadataFileName = Str::uuid()."-metadata.".$metadataType;
        $metadataWriter = $this->metadataGenerator->createMetadataWriter([
            'filename' => $metadataFileName,
            'type' => $metadataType,
        ]);

        foreach (['archive', 'collection', 'holding'] as $type) {
            $meta = (!isset($bag->metadata[$type])) ? [] : $bag->metadata[$type];
            $retval = $metadataWriter->write([
                'object' => MetadataPath::of($type),
                'metadata' => $meta
            ]);
        }

        foreach ($files as $file) {
            if(($file->filename === "metadata.csv") && $bag->owner()->first()->settings->ingestMetadataAsFile)
                $this->packetBuilder->addMetadataFile($file->storagePathCompleted(), MetadataPath::FILE_OBJECT_PATH . $file->filename);
            else
                $this->packetBuilder->addFile($file->storagePathCompleted(), MetadataPath::FILE_OBJECT_PATH . $file->filename);

            if($bag->owner()->first()->settings->ingestMetadataAsFile !== true) {
                if ($file->metadata->count() > 0) {
                    // append metadata to file
                    $retval = $metadataWriter->write([
                        'object' => MetadataPath::FILE_OBJECT_PATH . $file->filename,
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
        if( Storage::exists( $metadataFileName ) ){
            $this->packetBuilder->addMetadataFile(Storage::path($metadataFileName), "metadata.".$metadataType);
        }

        $result = $this->packetBuilder->build($bag->storagePathCreated());

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
            Log::error("Bag ".$bag->id." failed! Root cause: ".$this->packetBuilder->errorMessage());
            event( new ErrorEvent($bag) );
        }
        if(file_exists($metadataFileName)) {
            unlink($metadataFileName);
        }
    }
}
