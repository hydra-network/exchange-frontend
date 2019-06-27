<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Withdrawal;
use App\Models\EscrowOrder;

class WithdrawalChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawal:checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deposit Checker';

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
        $withdrawals = Withdrawal::where('status', Withdrawal::STATUS_NEW)->orderBy('id', 'ASC')->limit(10)->get();

        foreach ($withdrawals as $withdrawal) {
            echo " * New $withdrawal->id \n";
            
            $currency = $withdrawal->currency;

            if ($relativeOrder = EscrowOrder::where('withdrawal_id', $withdrawal->id)->count()) {
                $delay = 2;
            } else {
                $delay = 86999;
            }

            if ((time()-strtotime($withdrawal->created_at)) > $delay) {
                if ($rpcClient = $currency->getRpcClient()) {
                    if ($result = $rpcClient->send($withdrawal->address, $withdrawal->quantity)) {
                        if($result->txId) {
                            $withdrawal->status = Withdrawal::STATUS_AUTHORIZED;
                            $withdrawal->tx_id = $result->txId;
                            $withdrawal->save();
                            echo " --- TX created \n";
                        } else {
                            $withdrawal->status = Withdrawal::STATUS_FAIL;
                            $withdrawal->save();
                        }
                    }
                }
            } else {
                echo " --- is not mature \n";
            }
        }
        
        $withdrawals = Withdrawal::where('status', Withdrawal::STATUS_AUTHORIZED)->orderBy('id', 'ASC')->limit(100)->get();

        foreach ($withdrawals as $withdrawal) {
            echo " * Check $withdrawal->id \n";
            $currency = $withdrawal->currency;
            if ($rpcClient = $currency->getRpcClient()) {
                if ($transaction = $rpcClient->getOutcomeTransaction($withdrawal->tx_id, $withdrawal->address)) {
                    if ($transaction->id) {
                        echo " --- {$transaction->confirmations} confirmations \n";
                        $withdrawal->confirmations = $transaction->confirmations;
                        if ($transaction->confirmations >= 1) {
                            echo " ^ Have a transaction $transaction->id \n";
                            $withdrawal->status = Withdrawal::STATUS_DONE;
                        }
                        $withdrawal->save();
                    }
                }
            }
        }
    }
}
