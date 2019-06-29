<?php

namespace App\Libs;

use App\Models\Pair;
use App\Models\User;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;

class Market
{
    private $pair;
    private $user;

    public function __construct(Pair $pair, User $user)
    {
        $this->user = $user;
        $this->pair = $pair;
    }

    public function activeOrders($type = null)
    {
        $list =  DB::table('orders')
            ->select(DB::raw('price, SUM(quantity) as quantity, SUM(quantity_remain) as quantity_remain, SUM(cost) as cost, SUM(cost_remain) as cost_remain, count(DISTINCT id) as count'))
            ->groupBy('price');

        $list->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL]);

        if ($type == Order::TYPE_SELL) {
            $list->where('type', Order::TYPE_SELL)->orderBy('price', 'asc');
        } elseif($type == Order::TYPE_BUY) {
            $list->where('type', Order::TYPE_BUY)->orderBy('price', 'desc');
        } else {
            $list->orderBy('id', 'desc');
        }

        return $list;
    }

    public function myActiveOrders($type = null)
    {
        $list = Order::where('pair_id', $this->pair->id)
            ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])
            ->where('user_id', $this->user->id);

        if ($type == Order::TYPE_SELL) {
            $list->where('type', Order::TYPE_SELL)->orderBy('price', 'asc');
        } elseif($type == Order::TYPE_BUY) {
            $list->where('type', Order::TYPE_BUY)->orderBy('price', 'desc');
        } else {
            $list->orderBy('id', 'desc');
        }

        return $list;
    }

    public function myDeals()
    {
        $userId = $this->user->id;

        return Deal::where('pair_id', $this->pair->id)->where('pair_id', $this->pair->id)
            ->where(function ($query) use ($userId) {
                $query->where('buyer_user_id', '=', auth()->user()->id)->orWhere('seller_user_id', '=', auth()->user()->id);
            })
            ->orderBy('id', 'DESC');
    }

    public function myNewDeals($limit = 10)
    {
        $userId = $this->user->id;

        return Deal::where('pair_id', $this->pair->id)->where('pair_id', $this->pair->id)
            ->where(function ($query) use ($userId) {
                $query->where('buyer_user_id', '=', auth()->user()->id)->orWhere('seller_user_id', '=', auth()->user()->id);
            })
            ->where('notify_at', null)
            ->limit($limit)
            ->orderBy('id', 'ASC');
    }

    public function dealsHistory(bool $my = false, $buy = true, $sell = true)
    {
        $deals = Deal::where('pair_id', $this->pair->id)->where('pair_id', $this->pair->id);

        if ($my) {
            $userId = $this->user->id;
            $deals->where(function ($query) use ($userId) {
                $query->where('buyer_user_id', '=', auth()->user()->id)->orWhere('seller_user_id', '=', auth()->user()->id);
            });
        }

        if (!$sell | !$buy) {
            if ($sell) {
                $deals->where('type', Deal::TYPE_SELL);
            }

            if ($buy) {
                $deals->where('type', Deal::TYPE_BUY);
            }
        }

        $deals->orderBy('id', 'DESC');

        return $deals;
    }
}
