<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Address;
use App\Models\Balance;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Exchange\Libs\Wallet;

class BalancesController extends Controller
{
    public function getDepositAddress($code)
    {
        $currency = Asset::where('code', $code)->firstOrFail();

        $address = Address::where('asset_id', $currency->id)->where('user_id', auth()->user()->id)->orderBy('id', 'ASC')->first();

        if (!$address && $rpcClient = $currency->getRpcClient()) {
            if ($addressData = $rpcClient->generateAddress()) {
                $address = new Address();
                $address->asset_id = $currency->id;
                $address->private_key = $addressData->private;
                $address->address = $addressData->public;
                $address->user_id = auth()->user()->id;
                $address->save();
            }
        }
        
        if (!$address) {
            return response()->json(['error' => 'Unknown error']);
        }

        return response()->json(['address' => $address->address, 'id' => $address->id]);
    }
    
    public function getDepositList($code)
    {
        $currency = Asset::where('code', $code)->firstOrFail();

        $deposits = Deposit::where('asset_id', $currency->id)->where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get()->toArray();

        return response()->json(['list' => $deposits]);
    }

    public function getWithdrawalList($code)
    {
        $currency = Asset::where('code', $code)->firstOrFail();

        $withdrawals = Withdrawal::where('asset_id', $currency->id)->where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get()->toArray();

        return response()->json(['list' => $withdrawals]);
    }

    public function withdrawalOrder(Request $request)
    {
        $currency = Asset::where('code', $request->input('code'))->firstOrFail();

        $address = $request->input('address');
        $amount = $request->input('amount');

        if ($currency->userBalance() < ($amount+$currency->withdrawal_fees)) {
            return response()->json(['result' => 'fault', 'error' => "insufficient funds"]);
        }

        if ($currency->min_withdrawal_amount && $currency->min_withdrawal_amount > $amount) {
            return response()->json(['result' => 'fault', 'error' => "Minimum withdrawal amount is " . $currency->min_withdrawal_amount]);
        }

        if (Address::where('address', $address)->count()) {
            return response()->json(['result' => 'fault', 'error' => "You can't send coins inside the exchange"]);
        }
        
        if ($currency->min_withdrawal_amount && $currency->min_withdrawal_amount > $amount) {
            return response()->json(['result' => 'fault', 'error' => "Minimum withdrawal amount is " . $currency->min_withdrawal_amount]);
        }

        if (!$rpcClient = $currency->getRpcClient()) {
            return response()->json(['result' => 'fault', 'error' => "Coin network failed"]);
        }

        if (!$address | !$rpcClient->isAddressValid($address)) {
            return response()->json(['result' => 'fault', 'error' => "Invalid address"]);
        }

        $amount = $request->amount;

        $wallet = new Wallet($currency, auth()->user());

        if ($withdrawal = $wallet->withdrawal($address, $amount)) {
            $balance = new Balance;
            $balance->outcome(
                $currency->id,
                $withdrawal->quantity + $currency->withdrawal_fees,
                $withdrawal->user_id,
                null,
                null,
                $withdrawal->id
            );

            return response()->json(['result' => 'success', 'txId' => null, 'error' => false]);
        }

        return response()->json(['result' => 'fault', 'error' => 'Unknown error']);
    }
}
