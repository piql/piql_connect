<?php

namespace App\Console\Commands;

use App\Bag;
use App\BagTransitionException;
use App\Events\BagFilesEvent;
use App\Traits\CommandDisplayUtil;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class BagChangeState extends Command
{

    use CommandDisplayUtil;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bag:transition {id?} {--transition=} {--name=} {--dry-run} {--force} {--list-transitions}';

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
        $transition = $this->option('transition', "");
        $listTransitions = $this->option('list-transitions', "");
        $force = $this->option('force');
        $dryRun = $this->option('dry-run', "");

        if($listTransitions) {
            $this->info("\nTransitions");
            $this->info("--------------------");
            collect(array_keys(Bag::transitions()))->map(function($transition) {
                $this->info($transition);
            });
            return;
        }

        $query = Bag::query();

        if ($id != "") {
            $this->info('id');
            $query->where("id", $id);
        }

        if ($name != "") {
            $this->info('name');
            $query->where("name", 'like', "%$name%");
        }

        $query->get()->map(function($bag) use($dryRun, $transition, $force) {
            try {
                $bag->applyTransition($transition, $force);
            } catch( BagTransitionException $e) {
                $this->info($bag->id." ".$bag->status." ".$bag->name);
                $this->info("\nValid transitions from state '$bag->status' is :");
                $this->info("--------------------");
                collect(Bag::transitions())->flatMap(function ($transition, $key) use ($bag){
                    if(!(array_search($bag->status, $transition['from']) === false))
                        $this->info($key);
                });
                return;
            }

            if(!$dryRun) {
                $bag->save();
            }
            $this->displayBag($bag);
        });

        //
    }
}
