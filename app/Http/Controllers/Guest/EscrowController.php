<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\EscrowOrder;

class EscrowController extends Controller
{
    public function form($id)
    {
        $order = Order::whereId($id)
            ->whereIn('status', [Order::STATUS_ESCROW_WAIT_SELLER, Order::STATUS_ESCROW_WAIT_DEPO, Order::STATUS_ESCROW_WAIT_CONF])
            ->firstOrFail();

        if (in_array($order->status, [Order::STATUS_ESCROW_WAIT_CONF, Order::STATUS_ESCROW_WAIT_DEPO])) {
            $escrowOrder = $order->escrowOrder;

            if (!$escrowOrder) {
                abort(404);
            }

            $timer = time() - strtotime($escrowOrder->activated_at);

            $timer = env('TIME_TO_ESCROW_CANCEL') - round(($timer/60), 0);

            return view('guest.escrow.booking_timer', [
                'order' => $order,
                'timer' => $timer
            ]);
        }

        return view('guest.escrow.form', [
            'order' => $order,
        ]);
    }

    public function view($hash)
    {
        $escrowOrder = EscrowOrder::where('hash', $hash)
            ->firstOrFail();

        if ($escrowOrder->status == EscrowOrder::STATUS_EXPIRED) {
            redirect(route('guest.escrow.buy', ['id' => $escrowOrder->order->id]));
        }

        return view('guest.escrow.view', [
            'escrowOrder' => $escrowOrder,
            'order' => $escrowOrder->order,
        ]);
    }
}
