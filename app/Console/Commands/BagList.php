<?php

namespace App\Console\Commands;

use App\Bag;
use App\Events\BagFilesEvent;
use App\Traits\CommandDisplayUtil;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;

class BagList extends Command
{
    use CommandDisplayUtil;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bag:list {id?} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List bags';

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
        $id = $this->argument('id', "");
        $name = $this->option('name', "");
        $verbose = $this->option('verbose');

        $query = Bag::query();

        if ($id != "") {
            $query->where("id", $id);
        }

        if ($name != "") {
            $query->where("name", 'like', "%$name%");
        }
        $this->info("");
        $query->get()->map(function($bag) use($verbose) {
            $this->displayBag($bag, $verbose);
        });

        //
    }
}
