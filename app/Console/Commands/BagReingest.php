<?php

namespace App\Console\Commands;

use App\Bag;
use App\Events\BagFilesEvent;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class BagReingest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bag:reingest {id?} {--name=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $name = $this->option('name', "");
        $id = $this->argument('id', "");
        $dryRun = $this->option('dry-run', "");

        $query = Bag::query();

        if ($id != "") {
            $query->where("id", $id);
        }

        if ($name != "") {
            $query->where("name", 'like', "%$name%");
        }

        $query->get()->map(function($bag) use($dryRun) {
            $this->info($bag->id." ".$bag->status." ".$bag->name);

            $missingFiles = [];
            // validate files
            $bag->files->map(function($file) use($bag, &$missingFiles) {
                if(!is_dir("$file") && !file_exists("$file")) {
                    $missingFiles[] = $file;
                }
            });

            if(count($missingFiles) > 0) {
                $this->warn("Can't reingest bag $bag->name ($bag->id). Files not found");
                collect($missingFiles)->map(function($file) {
                    $this->warn("Missing file: ".$file->filename);
                });
                return;
            }

            if(!$dryRun) {
                $bag->status = 'closed';
                $bag->save();
                event(new BagFilesEvent($bag));
            }
        });

        //
    }
}
