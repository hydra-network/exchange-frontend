<?php

namespace App\Libs;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Order as OrderModel;
use App\Models\Pair;
use App\Models\User;

class Broker
{
    private $client;

    public function __construct(User $client)
    {
        $this->client = $client;
    }

    public function addOrder(Pair $pair, $type, $quantity, $price)
    {
        $order = new OrderModel;

        $client = $this->client;

        DB::transaction(function() use ($order, $pair, $type, $quantity, $price, $client) {

            $cost = ($quantity*$price)*$pair->primary->subunits;
            $quantity = $quantity*$pair->secondary->subunits;

            $price = $price*$pair->primary->subunits;

            $order->fill([
                'pair_id' => $pair->id,
                'user_id' => $client->id,
                'type' => $type,
                'status' => OrderModel::STATUS_NEW,
                'quantity' => $quantity,
                'quantity_remain' => $quantity,
                'price' => $price,
                'cost' => $cost,
                'cost_remain' => $cost,
            ]);

            $order->freezeAssetsAndSave($client);
        });

        return $order;
    }

    public function cancelOrder($id)
    {
        $order = OrderModel::where('id', $id)->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])->where('user_id', $this->client->id)->firstOrFail();

        $client = $this->client;

        \DB::transaction(function() use ($order, $client) {
            $pair = $order->pair;

            $currency = ($order->type == Order::TYPE_BUY) ? $pair->primary : $pair->secondary;

            $quantity = ($order->type == Order::TYPE_BUY) ? $order->cost_remain : $order->quantity_remain;

            $client->unfreezeAssets($currency, $order, $quantity);

            $order->status = Order::STATUS_CANCEL;
        });

        return $order->save();
    }
}
