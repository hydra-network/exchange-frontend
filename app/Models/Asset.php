<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libs\RpcClient;

class Asset extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    protected $fillable = ['type', 'asset_id', 'quantity', 'user_id', 'deal_id', 'deposit_id', 'withdrawal_id', 'order_id', 'balance'];

    public function getNode()
    {
        return $this->hasOne('App\Models\NodeServer');
    }

    public function getRpcClient()
    {
        if($node = $this->getNode()->first()) {
            return new RpcClient($node->base_url);
        }
    }
    
    public function userBalance()
    {
        if (!auth()->user()) {
            return 0;
        }

        $balance = 0;

        if ($balanceHistory = Balance::where('user_id', auth()->user()->id)->where('asset_id', $this->id)->orderBy('id', 'DESC')->first()) {
            $balance = $balanceHistory->balance;
        }

        return $this->format($balance);
    }

    public function userUnconfirmedBalance()
    {
        if (!auth()->user()) {
            return 0;
        }

        $balance = 0;

        if ($depoSum = Deposit::where('user_id', auth()->user()->id)->where('asset_id', $this->id)->where('confirmations', '<', $this->min_confirmations)->sum('quantity')) {
            $balance = $depoSum;
        }

        return $this->format($balance);
    }

    public function onMarket($marketCode)
    {
        $marketInfo = new \stdClass;

        $marketInfo->last_price = 0;
        $marketInfo->bid = 1;
        $marketInfo->ask = 2;

        return $marketInfo;
    }

    public function format($quantity)
    {
        $quantity = (int) $quantity;

        if ($this->subunits) {
            $quantity = $quantity/$this->subunits;
        }

        return rtrim(rtrim(number_format(round($quantity, $this->round), $this->round, ".", " "), '0'), '.');
    }

    public function format2($quantity)
    {
        return $quantity;
        $quantity = (int) $quantity;

        return $quantity/$this->subunits;
        return round($quantity/$this->subunits, 8, PHP_ROUND_HALF_DOWN);
    }
}
