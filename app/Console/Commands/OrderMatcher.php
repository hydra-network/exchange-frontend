<?php

namespace App\Console\Commands;

use App\Models\Pair;
use App\Jobs\Matching;
use Illuminate\Console\Command;

class OrderMatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:matcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order Matcher';

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
          $pairs = Pair::all();

          foreach ($pairs as $pair) {
              echo "Pair {$pair->id} \n";
              Matching::dispatchNow($pair->id);
          }
    }
}