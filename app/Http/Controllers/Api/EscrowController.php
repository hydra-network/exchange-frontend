<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order as OrderModel;
use App\Http\Resources\Order;
use App\Http\Resources\EscrowOrder;
use App\Models\EscrowOrder as EscrowOrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EscrowController extends Controller
{
    public function generateLink(Request $request)
    {
        $order = OrderModel::whereId($request->id)->whereIn('status', [OrderModel::STATUS_ACTIVE, OrderModel::STATUS_NEW, OrderModel::STATUS_PARTIAL])->where('user_id', auth()->user()->id)->firstOrFail();

        $order->status = OrderModel::STATUS_ESCROW_WAIT_SELLER;
        $order->save();

        return new Order($order);
    }

    public function getOrder($id)
    {
        $order = OrderModel::whereId($id)->firstOrFail();

        return new Order($order);
    }

    public function getEscrowOrder($id)
    {
        $order = EscrowOrderModel::whereId($id)->firstOrFail();

        return new EscrowOrder($order);
    }

    public function activateOrder($id, Request $request)
    {
        $address = $request->address;

        $order = OrderModel::whereId($id)->firstOrFail();

        if ($order->type == $order::TYPE_SELL) {
            $primary_asset = $order->primary;
            $secondary_asset = $order->secondary;
        } else {
            $primary_asset = $order->secondary;
            $secondary_asset = $order->primary;
        }

        if (!$rpcClient1 = $primary_asset->getRpcClient()) {
            return response()->json(['result' => 'fault', 'error' => "Coin 1 network failed"], 422);
        }

        if (!$rpcClient2 = $secondary_asset->getRpcClient()) {
            return response()->json(['result' => 'fault', 'error' => "Coin 2 network failed"], 422);
        }

        if (!$address | !$rpcClient2->isAddressValid($address)) {
            return response()->json(['result' => 'fault', 'error' => "Invalid address"], 422);
        }

        if ($addressData = $rpcClient1->generateAddress()) {
            $depositAddress = new Address();
            $depositAddress->asset_id = $primary_asset->id;
            $depositAddress->private_key = $addressData->private;
            $depositAddress->address = $addressData->public;
            $depositAddress->user_id = $order->user_id;
            $depositAddress->save();
        } else {
            return response()->json(['result' => 'fault', 'error' => "Failed to generate the new address"], 500);
        }

        $order->status = OrderModel::STATUS_ESCROW_WAIT_DEPO;
        $order->save();

        $escrowOrder = new EscrowOrderModel();
        $escrowOrder->fill([
            'hash' => Str::random(35),
            'order_id' => $order->id,
            'ip' => \Request::ip(),
            'status' => EscrowOrderModel::STATUS_NEW,
            'quantity' => $order->quantity_remain,
            'cost' => $order->cost_remain,
            'pair_id' => $order->pair_id,
            'user_address' => $address,
            'deposit_address_id' => $depositAddress->id,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $escrowOrder->save();

        return new EscrowOrder($escrowOrder);
    }

    public function cancel(Request $request)
    {
        $order = OrderModel::whereId($request->id)->where('user_id', auth()->user()->id)->firstOrFail();

        $order->status = OrderModel::STATUS_ACTIVE;
        $order->save();

        return new Order($order);
    }
}
