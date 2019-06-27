<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;

class HistoryController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->limit(100)->get();
        
        return view('app.history', [
            'orders' => $orders,
        ]);
    }
}
