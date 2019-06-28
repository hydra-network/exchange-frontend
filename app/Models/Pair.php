<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    
    public function secondary()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'secondary_asset_id');
    }

    public function primary()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'primary_asset_id');
    }
    
    public function getLastPrice()
    {
        if ($lastDeal = Deal::where('pair_id', $this->id)->orderBy('created_at', 'DESC')->first()) {
            return $this->primary->format($lastDeal->price);
        }
        
        return null;
    }
    
    public function getBidPrice()
    {
        if ($order = Order::where('pair_id', $this->id)
                ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
                ->where('type', Order::TYPE_BUY)
                ->orderBy('price', 'DESC')
                ->first()) {
            return $this->primary->format($order->price);
        }
        
        return null;
    }
    
    public function getAskPrice()
    {
        if ($order = Order::where('pair_id', $this->id)
                ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
                ->where('type', Order::TYPE_SELL)
                ->orderBy('price', 'ASC')
                ->first()) {
            return $this->primary->format($order->price);
        }
        
        return null;
    }
    
    public function getDailyVolume()
    {
        return Deal::whereBetween('created_at', [date('Y-m-d H:i:s', time()-86400), date('Y-m-d H:i:s', time())])
            ->where('pair_id', $this->id)
            ->sum('cost');
    }

    public function getSizes()
    {
        $bidSize = Order::where('pair_id', $this->id)
                ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
                ->where('type', Order::TYPE_BUY)
                ->get()
                ->sum('cost_remain');

        $askSize = Order::where('pair_id', $this->id)
                ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
                ->where('type', Order::TYPE_SELL)
                ->get()
                ->sum('quantity_remain');
                
        return [
            'bid' => $this->secondary->format($bidSize),
            'ask' => $this->secondary->format($askSize),
        ];
    }

    public function userInOrdersPrimaryBalance()
    {
        if ($sum = Order::where('pair_id', $this->id)
            ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
            ->where('user_id', auth()->user()->id)
            ->where('type', Order::TYPE_BUY)
            ->sum('cost_remain')) {
            return $this->primary->format($sum);
        }

        return 0;
    }

    public function userInOrdersSecondaryBalance()
    {
        if ($sum = Order::where('pair_id', $this->id)
            ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
            ->where('user_id', auth()->user()->id)
            ->where('type', Order::TYPE_SELL)
            ->sum('quantity_remain')) {
            return $this->primary->format($sum);
        }

        return 0;
    }
}
