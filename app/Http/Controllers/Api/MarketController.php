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
use Auth;

class MarketController extends Controller
{
    public function getMyOrderModelsList()
    {
        $orders = OrderModel::where('user_id', $this->getUser()->id)
            ->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])
            ->OrderBy('id', 'DESC')
            ->limit(100);

        return new OrderCollection($orders->paginate(100));
    }

    public function getDeals($code, $my = false, $buy = true, $sell = true)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();

        $market = new Market($pair, $this->getUser());

        $dealsList = $market->dealsHistory($my, $buy, $sell)->paginate(50)->toArray();

        $dealsList['data'] = Formatter::formatObjects($pair, $dealsList['data']);

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

        $broker = new Broker($this->getUser());
        $order = $broker->addOrder($pair, $type, $quantity, $price);

        if (!$order->id) {
            abort(500, "System error [1]");
        }

        if (env('APP_ENV') != 'testing') {
            Matching::dispatch($pair->id)->onQueue('matching');
        }

        return response()->json(['result' => 'success', 'order_id' => $order->id]);
    }
    
    public function removeOrder(Request $request)
    {
        $id = $request->input('id');

        $broker = new Broker($this->getUser());

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

        $alerts = [];

        $market = new Market($pair, $this->getUser());

        $freshDeals = $market->myNewDeals()->get();

        foreach ($freshDeals as $deal) {
            $alerts[] = [
                'class' => 'success',
                'message' => "{$deal->pair->code}: new deal, price is {$deal->getPrice()}",
                'link' => route('app.history')
            ];

            $deal->notify_at = date('Y-m-d H:i:s');
            $deal->save();
        }

        return response()->json([
            'primary_asset_volume' => $pair->primary->format($sizes['bid']),
            'secondary_asset_volume' => $pair->secondary->format($sizes['ask']),
            'daily_volume' =>  $pair->primary->format($volume24),
            'bid_size' => $pair->primary->format($sizes['bid']),
            'ask_size' => $pair->secondary->format($sizes['ask']),
            'h24' => ($h24) ? $pair->primary->format($h24) : 0,
            'l24' => ($l24) ? $pair->primary->format($l24) : 0,
            'alerts' => $alerts,
            'last_price' => ($lastPrice) ? $pair->primary->format($lastPrice->price) : 0,
        ]);
    }
    
    public function getTicks($code, $period = 1, $limit = 40) //$period - in minuts
    {
        session(['chart_period' => $period]);
        
        $pair = PairModel::where('code', $code)->firstOrFail();

        $market = new Market($pair, $this->getUser());

        return $market->getTicks($period, $limit);
    }

    protected function getOrders($code, $type)
    {
        $pair = PairModel::where('code', $code)->firstOrFail();

        $market = new Market($pair, $this->getUser());

        $list = $market->activeOrders($type)->paginate(10)->toArray();
        $myList = $market->myactiveOrders($type)->limit(100)->get()->toArray();

        if ($this->getUser()) {
            if ($myPrices = OrderModel::where('user_id', $this->getUser()->id)->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])->groupBy('price')->pluck('price')->toArray()) {
                foreach ($list['data'] as $order) {
                    $order->my = (in_array($order->price, $myPrices)) ? true : false;
                }
            }
        }

        $list['data'] = Formatter::formatObjects($pair, $list['data']);

        return [
            'list' => $list,
            'my_list' => Formatter::formatObjects($pair, $myList),
        ];
    }

    private function getUser()
    {
        return auth()->user();
    }
}
