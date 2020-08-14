<?php

namespace App\Console\Commands;

use App\Bag;
use App\Events\PreProcessBagEvent;
use Illuminate\Console\Command;

class BagProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bag:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bags to processing';

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
        $maxProcessingBags = env('MAX_SIMULTANEOUS_BAGS_PROCESSING', 1);

        while (true) {
            sleep(5);

            // Check if processing capacity is reached
            if (Bag::currentlyProcessingCount() >= $maxProcessingBags) {
                continue;
            }

            // Get bag to process
            $bag = Bag::where('status', '=', 'closed')->orderBy('updated_at')->first();
            if (!$bag){
                continue;
            }

            $this->info('Processing bag, id=' . $bag->id);
            event(new PreProcessBagEvent($bag));
        }
    }
}
