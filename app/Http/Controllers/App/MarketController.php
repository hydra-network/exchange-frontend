<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Pair;

class MarketController extends Controller
{
    public function pair($code)
    {
        $pair = Pair::where('code', $code)->firstOrFail();

        $primaryAsset = $pair->primary;
        $secondaryAsset = $pair->secondary;

        return view('app.market.pair', [
            'pair' => $pair,
            'primary_asset' => $primaryAsset,
            'secondary_asset' => $secondaryAsset,
        ]);
    }
}
