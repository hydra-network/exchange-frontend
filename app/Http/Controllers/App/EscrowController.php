<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;

class EscrowController extends Controller
{
    public function orders()
    {
        $orders = Order::where('user_id', auth()->user()->id)
            ->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL, Order::STATUS_ESCROW_WAIT_SELLER, Order::STATUS_ESCROW_WAIT_DEPO, Order::STATUS_ESCROW_WAIT_CONF, Order::STATUS_ESCROW_DONE])
            ->orderBy('id', 'DESC')
            ->limit(100)
            ->get();
        
        return view('app.escrow', [
            'orders' => $orders,
        ]);
    }
}
