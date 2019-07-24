<?php

namespace App\Hooks;

use App\Models\Balance;
use App\Models\Asset;

class Pudge
{
    public function userRegistered($data)
    {
        if (!isset($data['user']['id'])) {
            return false;
        }

        $assets = Asset::whereType('demo-asset')->get();

        foreach ($assets as $asset) {
            Balance::create([
                'type' => 'in',
                'asset_id' => $asset->id,
                'user_id' => $data['user']['id'],
                'quantity' => rand(1, 999)*$asset->subunits,
                'balance' => rand(1, 999)*$asset->subunits,
            ]);

            Balance::create([
                'type' => 'in',
                'asset_id' => $asset->id,
                'user_id' => $data['user']['id'],
                'quantity' => rand(1, 999)*$asset->subunits,
                'balance' => rand(1, 999)*$asset->subunits,
            ]);
        }
    }
}