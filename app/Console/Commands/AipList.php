<?php

namespace App\Console\Commands;

use App\Aip;
use App\Traits\CommandDisplayUtil;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class AipList extends Command
{
    use CommandDisplayUtil;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aip:list {id?} {--limit=} {--name=} {--no-bucket} {--no-files} {--validate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List AIPs';

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
        $name = $this->option('name', "");
        $limit = $this->option('limit', false);
        $noBucket = $this->option('no-bucket');
        $noFiles = $this->option('no-files');
        $validate = $this->option('validate');

        $query = Aip::query();

        if($id != "") {
            $query->where("id",$id);
        }

        if ($name != "") {
            $query->where("online_storage_path", 'like', "%$name%");
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        if($noBucket) {
           $query->whereNotIn('id', function (Builder $query) {
                $query->select('archivable_id')
                    ->from('archivables')
                    ->where('archivable_type', "App\\Aip");
            });
        }

        if($noFiles) {
            $query->whereNotIn('id', function (Builder $query) {
                $query->select('storable_id')->distinct()
                    ->from('file_objects')
                    ->where('storable_type', "App\\Aip");
            });
        }

        $query->get()->map(function($aip) use ($validate) {
            if($validate) {
                $errorMsg = "";
                $bagName = "";

                if (!$aip->storage_properties) {
                    $errorMsg = "No storage properties";
                } else {
                    $bag = $aip->storage_properties->bag;
                    if ($bag) {
                        $bagName = $bag->name;
                    }

                    if (!$bag) {
                        $errorMsg .= $errorMsg == "" ? "" : ", ";
                        $errorMsg .= "No bag";

                    } elseif ($bag->status == "error") {
                        $errorMsg .= $errorMsg == "" ? "" : ", ";
                        $errorMsg .= "Bag error";
                    }

                    if (!$aip->storage_properties->dip) {
                        $errorMsg .= $errorMsg == "" ? "" : ", ";
                        $errorMsg .= "No dip";
                    }
                }
                if($errorMsg == "") {
                    return;
                }

                $this->warn($aip->id." ".$aip->external_uuid." ".$bagName." : ".$errorMsg);
            } else {
                $this->displayAipFull($aip);

            }
        });

        //
    }
}
