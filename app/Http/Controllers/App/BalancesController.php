<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class BalancesController extends Controller
{
    public function index()
    {
        $currencies = Asset::orderBy('name')->where('status', 1)->get();

        return view('app.balances', [
            'currencies' => $currencies,
        ]);
    }

    public function deposit($code)
    {
        $currency = Asset::where('code', $code)->where('status', 1)->first();

        return view('app.balances.deposit', [
            'currency' => $currency,
        ]);
    }

    public function withdrawal($code)
    {
        $currency = Asset::where('code', $code)->where('status', 1)->first();

        return view('app.balances.withdrawal', [
            'currency' => $currency,
        ]);
    }
}
