<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class BalancesController extends Controller
{
    public function index()
    {
        $currencies = Asset::orderBy('name')->where('status', Asset::STATUS_ACTIVE)->get();

        return view('app.balances', [
            'currencies' => $currencies,
        ]);
    }

    public function deposit($code)
    {
        $currency = Asset::where('code', $code)->where('status', Asset::STATUS_ACTIVE)->firstOrFail();

        return view('app.balances.deposit', [
            'currency' => $currency,
        ]);
    }

    public function withdrawal($code)
    {
        $currency = Asset::where('code', $code)->where('status', Asset::STATUS_ACTIVE)->firstOrFail();

        return view('app.balances.withdrawal', [
            'currency' => $currency,
        ]);
    }
}
