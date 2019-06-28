<?php

namespace App\Http\Controllers\Api;

use App\Libs\Broker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pair as PairModel;
use App\Models\Order as OrderModel;
use App\Helpers\Formatter;
use App\Libs\Market;
use App\Models\Deal;
use App\Http\Resources\OrderCollection;
use App\Http\Requests\OrderRequest;
use App\Jobs\Matching;

class MarketController extends Controller
{
    public function getMyOrderModelsList()
    {
        $orders = OrderModel::where('user_id', auth()->user()->id)
            ->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])
            ->OrderBy('id', 'DESC')
            ->limit(100);

        return new OrderCollection($orders->paginate(100));
    }

    public function getDeals($code, $my = false, $buy = true, $sell = true)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();

        $market = new Market($pair, auth()->user());

        $dealsList = $market->dealsHistory($my, $buy, $sell)->paginate(50);

        return $dealsList;
    }

    public function getOrdersList($code, $type)
    {
        return response()->json($this->getOrders($code, $type));
    }

    public function addOrder(OrderRequest $request)
    {
        $type = $request->input('type');
        $quantity = $request->input('quantity');
        $price = $request->input('price');

        $pair = PairModel::where('code', $request->input('pair'))->firstOrFail();

        $broker = new Broker(auth()->user());
        $order = $broker->addOrder($pair, $type, $quantity, $price);

        if (!$order->id) {
            abort(500, "System error [1]");
        }

        Matching::dispatch($pair->id)->onQueue('matching');

        return response()->json(['result' => 'success', 'order_id' => $order->id]);
    }
    
    public function removeOrder(Request $request)
    {
        $id = $request->input('id');

        $broker = new Broker(auth()->user());

        $broker->cancelOrder($id);

        return response()->json(['result' => 'success']);
    }
    
    public function getBalances($code)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();
        
        $primaryAsset = $pair->primary;
        $secondaryAsset = $pair->secondary;
        
        return response()->json([
            'result' => 'success',
            'primary_asset' => $primaryAsset->userBalance(),
            'secondary_asset' => $secondaryAsset->userBalance(),
            'primary_asset_unc_balance' => $pair->primary->userUnconfirmedBalance(),
            'secondary_asset_unc_balance' => $pair->secondary->userUnconfirmedBalance(),
            'primary_asset_io_balance' => $pair->userInOrdersPrimaryBalance(),
            'secondary_asset_io_balance' => $pair->userInOrdersSecondaryBalance(),
        ]);
    }
    
    public function getSummaryInfo($code)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();

        $volumes = $pair->getSizes();
        $sizes = $pair->getSizes();
        
        $lastPrice = Deal::where('pair_id', $pair->id)->where('pair_id', $pair->id)->OrderBy('id', 'DESC')->first();
        
        $volume24 = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', time()-86400), date('Y-m-d H:i:s', time())])
            ->where('pair_id', $pair->id)
            ->sum('cost');

        $h24 = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', time()-86400), date('Y-m-d H:i:s', time())])
            ->where('pair_id', $pair->id)
            ->max('price');

        $l24 = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', time()-86400), date('Y-m-d H:i:s', time())])
            ->where('pair_id', $pair->id)
            ->min('price');
        
        return response()->json([
            'primary_asset_volume' => $pair->primary->format($volumes['bid']),
            'secondary_asset_volume' => $pair->secondary->format($volumes['ask']),
            'daily_volume' =>  $pair->primary->format($volume24),
            'bid_size' => $pair->secondary->format($sizes['bid']),
            'ask_size' => $pair->primary->format($sizes['ask']),
            'h24' => ($h24) ? $h24 : 0,
            'l24' => ($l24) ? $l24 : 0,
            'last_price' => ($lastPrice) ? $pair->primary->format($lastPrice->price) : 0,
        ]);
    }
    
    public function getTicks($code, $period = 1, $limit = 40, Request $request) //$period - in minuts
    {
        session(['chart_period' => $period]);
        
        $pair = PairModel::where('code', $code)->firstOrFail();

        $from = time()-($period*$limit);
        
        $ticks = [
            'ohlc' => [],
            'volume' => [],
            'times' => []
        ];
        
        $lastI = $from;
        for ($i = 1; $i <= $limit; $i++) {
            $time = $from + ($i*$period*60);

            if (!$open = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->OrderBy('id', 'ASC')->first()) {
                $open = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->OrderBy('id', 'DESC')->first();
            }

            if (!$hight = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->OrderBy('price', 'DESC')->first()) {
                $hight = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->OrderBy('id', 'DESC')->first();
            }

            if (!$low = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->OrderBy('price', 'ASC')->first()) {
                $low = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->OrderBy('id', 'DESC')->first();
            }

            if (!$close = Deal::where('pair_id', $pair->id)->whereBetween('created_at', [date('Y-m-d H:i:s', $lastI), date('Y-m-d H:i:s', $time)])->OrderBy('id', 'DESC')->first()) {
                $close = Deal::where('pair_id', $pair->id)->where('created_at', '<', date('Y-m-d H:i:s', $time))->OrderBy('id', 'DESC')->first();
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

                $ticks['ohlc'][] = [$pair->primary->format($o), $pair->primary->format($h), $pair->primary->format($l), $pair->primary->format($c)];
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

    protected function getOrders($code, $type)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();

        $market = new Market($pair, auth()->user());

        $list = $market->activeOrders($type)->paginate(10)->toArray();
        $myList = $market->myactiveOrders($type)->limit(100)->get()->toArray();

        if ($myPrices = OrderModel::where('user_id', auth()->user()->id)->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])->groupBy('price')->pluck('price')->toArray()) {
            foreach ($list['data'] as $order) {
                $order->my = (in_array($order->price, $myPrices)) ? true : false;
            }
        }

        $list['data'] = Formatter::formatObjects($pair, $list['data']);

        return [
            'list' => $list,
            'my_list' => Formatter::formatObjects($pair, $myList),
        ];
    }
}
