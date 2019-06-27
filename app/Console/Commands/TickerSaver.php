<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pair;
use App\Models\Deal;
use App\Models\Tick;

class TickerSaver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticker:saver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ticker Saver';

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
        //die($this->fakerTicks());
        $pairs = Pair::where('status', Pair::STATUS_ACTIVE)->get();

        foreach ($pairs as $pair) {
            echo " * check {$pair->code}\n";
            if ($lastDeal = Deal::where('pair_id', $pair->id)->orderBy('id', 'DESC')->first()) {
                $tick = new Tick;
                $tick->pair_id = $pair->id;
                $tick->min = $lastDeal->price;
                $tick->max = $lastDeal->price;
                $tick->avg = $lastDeal->price;
                $tick->save();
                
                echo " - result {$lastDeal->price}\n";
            }
        }
    }
    
    public function fakerTicks()
    {
        $time = time()-1500;
        for ($i = 1; $i <= 1000; $i++) {
            $time = $time + (60*$i);
            
            $base = rand(75, 145);
            $base = $base * 0.0001;
            
            $tick = new Tick;
            $tick->pair_id = 1;
            $tick->created_at = $time;
            $tick->min = $base;
            $tick->max = $base;
            $tick->avg = $base;
            $tick->save();
        }
    }
}
