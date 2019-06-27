<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Consensus\Engine;
use App\Consensus\Trader;
use App\Consensus\Blockchain;

class SandboxManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandbox:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consensus Test';

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
        $blockchain = new Blockchain();

        $trader1 = new Trader('Peeter');
        $trader2 = new Trader('John');
        $trader3 = new Trader('Nick');
        $trader4 = new Trader('Rick');
        $trader5 = new Trader('Match');

        $traders = [$trader1, $trader2, $trader3, $trader4, $trader5];

        $engine = new Engine($blockchain, $traders);

        $pk = self::makeBlockChainTx($blockchain);
        $engine->depo($trader1, $pk);
    }

    protected static function makeBlockChainTx(Blockchain $blockchain)
    {
        $bytes = [];
        for ($i = 0; $i <= 32; $i++) {
            $bytes[] = substr(md5(rand(0, 99999) . time()), 0, 2);
        }

        $blockchain->balances[md5(json_encode($bytes))] = 5;

        return $bytes;
    }
}
