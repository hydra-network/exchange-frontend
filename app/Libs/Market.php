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

    public function __construct(Pair $pair, ?User $user)
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
        if (!$this->user) {
            return Order::where('id', 0);
        }

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
        if (!$this->user) {
            return Deal::where('id', 0);
        }

        $userId = $this->user->id;

        return Deal::where('pair_id', $this->pair->id)->where('pair_id', $this->pair->id)
            ->where(function ($query) use ($userId) {
                $query->where('buyer_user_id', '=', auth()->user()->id)->orWhere('seller_user_id', '=', auth()->user()->id);
            })
            ->orderBy('id', 'DESC');
    }

    public function myNewDeals($limit = 10)
    {
        if (!$this->user) {
            return Deal::where('id', 0);
        }

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
            if (!$this->user) {
                return Deal::where('id', 0);
            }

            $userId = $this->user->id;
            $deals->where(function ($query) use ($userId) {
                $query->where('buyer_user_id', '=', $this->user->id)->orWhere('seller_user_id', '=', $this->user->id);
            });
        }

        if (!$sell | !$buy) {
            if ($sell) {
                $deals->where('type', Deal::TYPE_BUYER_TAKER);
            }

            if ($buy) {
                $deals->where('type', Deal::TYPE_SELLER_TAKER);
            }
        }

        $deals->orderBy('id', 'DESC');

        return $deals;
    }

    public function getTicks($period, $limit)
    {
        $pair = $this->pair;

        $from = time()-($period*$limit);

        $ticks = [
            'ohlc' => [],
            'volume' => [],
            'times' => []
        ];

        $lastI = $from;
        for ($i = 1; $i <= $limit; $i++) {
            $time = $from + ($i*$period*60);

            if (!$open = Deal::where('pair_id', $pair->id)->whereBetween('created_at1', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->orderBy('id', 'ASC')->first()) {
                $open = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->orderBy('id', 'DESC')->first();
            }

            if (!$hight = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->orderBy('price', 'DESC')->first()) {
                $hight = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->orderBy('id', 'DESC')->first();
            }

            if (!$low = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->orderBy('price', 'ASC')->first()) {
                $low = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->orderBy('id', 'DESC')->first();
            }

            if (!$close = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->orderBy('id', 'DESC')->first()) {
                $close = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->orderBy('id', 'DESC')->first();
            }

            $volume = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->sum('quantity');

            $minPrice = null;
            $maxPrice = 0;

            $minVolume = null;
            $maxVolume = 0;

            if ($volume > $maxVolume) {
                $maxVolume = $volume;
            }

            if (!$minVolume | $volume < $minVolume) {
                $minVolume = $volume;
            }

            if ($open && $hight && $low && $close) {
                $o = $open->price;
                $h = $hight->price;
                $l = $low->price;
                $c = $close->price;

                if ($hight->price > $maxPrice) {
                    $maxPrice = $hight->price;
                }

                if (!$minPrice | $low->price < $minPrice) {
                    $minPrice = $low->price;
                }

                $ticks['ohlc'][] = [$pair->primary->format2($o), $pair->primary->format2($h), $pair->primary->format2($l), $pair->primary->format2($c)];
                $ticks['volume'][] = $volume;
                $ticks['times'][] = date('H:i', $time);
            }

            $lastI = $time;
        }

        $ticks['times'] = implode(':', $ticks['times']);

        return response()->json([
            'result' => 'success',
            'ticker' => $ticks,
            'max_price' => $pair->primary->format($maxPrice),
            'min_price' => $pair->primary->format($minPrice),
            'max_volume' => $maxVolume,
            'min_volume' => $minVolume,
            'time_start' => $from,
            'time_stop' => $lastI
        ]);
    }
}
