<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use App\Models\Address;
use App\Models\EscrowDeposit;
use App\Models\EscrowOrder;
use App\Models\Deposit;
use App\Models\Balance;

class DepositChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:checker';

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
        $checkTimeout = 100;

        $currencyIds = Asset::where('status', Asset::STATUS_ACTIVE)->get()->map(function ($currency) {
            return $currency->id;
        });

        $addresses = Address::
            where(function($query) use ($checkTimeout) {
                $query->where('last_check_depo', null);
                $query->orWhere('last_check_depo', '<', (time()-$checkTimeout));
            })
            ->where(function($query) use ($checkTimeout) {
                $query->where('status', null);
                $query->orWhere('status', Address::STATUS_ACTIVE);
            })
            ->whereIn('asset_id', $currencyIds)
            ->orderBy('id', 'ASC')
            ->limit(100)
            ->get();
        
        foreach ($addresses as $address) {
            if (true) {
                $currency = $address->currency;

                echo " * {$currency->code} $address->address\n";
                if ($lastTransactions = $address->getIncomeTransactions($address->address)) {
                    foreach ($lastTransactions as $transaction) {
                        if ($transaction->id) {
                            if (!$deposit = Deposit::where('tx_id', $transaction->id)->first()) {
                                $deposit = new Deposit;

                                $deposit->fill(['tx_id' => $transaction->id, 'asset_id' => $address->asset_id, 'user_id' => $address->user_id, 'address_id' => $address->id, 'status' => Deposit::STATUS_NEW, 'quantity' => $transaction->amount, 'confirmations' => $transaction->confirmations]);
                                $deposit->save();

                                echo "  + New transaction $deposit->id ($transaction->id) \n";
                            } else {
                                $deposit->confirmations = $transaction->confirmations;
                                $deposit->save();

                                echo "  ^ Update confirmations to $deposit->confirmations ($transaction->id) \n";
                            }

                            if ($escrowOrder = EscrowOrder::where('deposit_address_id', $address->id)->whereIn('status', [EscrowOrder::STATUS_NEW, EscrowOrder::STATUS_WAIT_CONF])->first()) {
                                if (!$escrowDeposit = EscrowDeposit::where('tx_id', $transaction->id)->first()) {
                                    $escrowDeposit = new EscrowDeposit;

                                    $escrowDeposit->fill(['asset_id' => $address->asset_id, 'tx_id' => $transaction->id, 'escrow_id' => $escrowOrder->id, 'address_id' => $address->id, 'quantity' => $transaction->amount, 'confirmations' => $transaction->confirmations]);
                                    $escrowDeposit->save();

                                    echo "  + New transaction for escrow $deposit->id ($transaction->id) \n";
                                } else {
                                    $escrowDeposit->confirmations = $transaction->confirmations;
                                    $escrowDeposit->save();

                                    echo "  ^ Update confirmations for escrow to $deposit->confirmations ($transaction->id) \n";
                                }
                            }

                            $currency = $address->currency;

                            if ($transaction->confirmations >= $currency->min_confirmations){
                                if (!$balance = Balance::where('deposit_id', $deposit->id)->first()) {
                                    $balance = new Balance;
                                    $balance->income($address->asset_id, $transaction->amount, $address->user_id, null, $deposit->id);

                                    echo "  ^ Set income balance transaction \n";
                                }
                            }
                        }
                    }
                }

                $address->last_check_depo = time();
                $address->save();
            }
        }
    }
}


/*
 *         $address = $escrowOrder->getDepositAddress();

        if ($lastTransactions = $address->getIncomeTransactions($address->address)) {
            foreach ($lastTransactions as $transaction) {
                if (!$deposit = EscrowDeposit::where('tx_id', $transaction->id)->first()) {
                    $deposit = new EscrowDeposit;

                    $deposit->fill(['asset_id' => $address->asset_id, 'tx_id' => $transaction->id, 'escrow_id' => $escrowOrder->id, 'address_id' => $address->id, 'quantity' => $transaction->amount, 'confirmations' => $transaction->confirmations]);
                    $deposit->save();

                    echo "  + New transaction $deposit->id ($transaction->id) \n";
                } else {
                    $deposit->confirmations = $transaction->confirmations;
                    $deposit->save();

                    echo "  ^ Update confirmations to $deposit->confirmations ($transaction->id) \n";
                }

                if ($transaction->amount == $escrowOrder->cost) {
                    return $transaction;
                }
            }
        }

        return null;
 */