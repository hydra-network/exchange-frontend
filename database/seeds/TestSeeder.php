<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Pair;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Asset::create([
            'id' => 1,
            'name' => 'Bitcoin',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'BTC',
            'icon' => '',
            'status' => Asset::STATUS_ACTIVE,
            'address_example' => '',
            'min_confirmations' => 1,
            'min_withdrawal_amount' => 100,
            'min_trade_amount' => 0.0001,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => 100000000,
            'round' => 8,
        ]);

        Asset::create([
            'id' => 2,
            'name' => 'Ether',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'ETH',
            'icon' => '',
            'status' => Asset::STATUS_ACTIVE,
            'address_example' => '',
            'min_confirmations' => 1,
            'min_withdrawal_amount' => 100,
            'min_trade_amount' => 0.0001,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => 100000000,
            'round' => 8,
        ]);

        Pair::create([
            'id' => 1,
            'code' => 'BTC-ETH',
            'status' => Pair::STATUS_ACTIVE,
            'primary_asset_id' => 1,
            'secondary_asset_id' => 2,
            'min_trade_amount' => 0.001,
            'limit_from_one_person' => null,
            'max_price' => null,
            'min_price' => null,
            'daily_volume_limit' => null,
        ]);

        User::create([
            'id' => 1,
            'name' => 'Buyer1',
            'email' => 'demobuyer1@dexdev.ru',
            'password' => bcrypt('123123')
        ]);

        User::create([
            'id' => 2,
            'name' => 'Buyer2',
            'email' => 'demobuyer2@dexdev.ru',
            'password' => bcrypt('123123')
        ]);

        User::create([
            'id' => 3,
            'name' => 'Seller1',
            'email' => 'demoseller1@dexdev.ru',
            'password' => bcrypt('123123')
        ]);

        User::create([
            'id' => 4,
            'name' => 'Seller2',
            'email' => 'demoseller2@dexdev.ru',
            'password' => bcrypt('123123')
        ]);
    }
}
