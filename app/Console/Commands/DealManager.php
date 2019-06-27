<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Pair;
use App\Libs\Matcher;

class DealManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:manager {order_id} {echo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deal Checker';

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
        $order = Order::where('id', $this->argument('order_id'))->first();

        if (!$order) {
            $this->echoMessage("FAIL!  " . $this->argument('order_id'));
            return false;
        }

        $pair = Pair::where('id', $order->pair_id)->firstOrFail();

        $matcher = new Matcher($pair);
        $matcher->execute();
    }
    
    private function echoMessage($txt)
    {
        if ($this->argument('echo')) {
            echo $txt . "\n";
        }
    }
}
