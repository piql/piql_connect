<?php

namespace App\Console\Commands;

use App\Aip;
use App\Interfaces\ArchivalStorageInterface;
use App\Traits\CommandDisplayUtil;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

class AipDelete extends Command
{

    use CommandDisplayUtil;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aip:delete {id} {--dry-run} {--delete-bag} {--yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Aip, Dip (and Bag)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        $dryRun = $this->option('dry-run');
        $alwaysYes = $this->option('yes');
        $deleteBag = $this->option('delete-bag');

        $aip = Aip::find($id);
        if(!isset($aip)) {
            $this->error("No AIP found with id ".$id);
            return;
        }

        if($dryRun) {
            $this->displayAipFull($aip);
            return;
        }

        if (!$alwaysYes) {
            if(!$this->confirm("Deleting AIP with id $id.\nDo you wish to continue? [y|N]")) {
                return;
            }
        }

        $this->deleteObjects($aip, $deleteBag);

    }

    private function deleteObjects($aip, $deleteBag) {
        $bag = NULL;
        $dip = NULL;

        if($aip->storage_properties) {
            $bag = $aip->storage_properties->bag;
            $dip = $aip->storage_properties->dip;
        }

        if($deleteBag && $bag) {

            $deleteFile = function ($file) use ($bag) {
                if(!is_dir("$file") && file_exists("$file")) {
                    if(!unlink("$file")) {
                        $this->warn(
                            get_called_class() .
                            "Failed deleting file {$file} from bag " .
                            $bag->zipBagFileName() . " with id: " . $bag->id
                        );
                    }
                }
            };

            $bag->files->map(function($file) use($deleteFile) {
                $deleteFile($fileName = $file->storagePathCompleted());
            });
            $deleteFile($bag->storagePathCreated());
            Storage::disk('am')->delete($bag->zipBagFileName());

            $bag->delete();
        }


        if($dip) {
            $dip->fileObjects->map(function ($file) use ($dip) {
                $storage = \App::make(ArchivalStorageInterface::class);
                $result = $storage->delete($dip->storage_location, $file->fullpath);
                if ($result === false) {
                    $this->warn("delete failed : " . $result);
                    return;
                }
                $file->delete();
            });
            $dip->delete();
        }

        $aip->fileObjects->map(function($file) use($aip) {
            $storage = \App::make(ArchivalStorageInterface::class );
            $result = $storage->delete( $aip->online_storage_location, $file->fullpath);
            if($result === false) {
                $this->warn("delete failed : " . $result);
                return;
            }

            $file->delete();
        });

        // disconnect jobs
        $aip->jobs()->detach();

        $aip->delete();
    }
}
