<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Balance;
use App\Models\Asset;
use App\Models\Pair;

class UserBalancesSeeder extends Seeder
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
            'name' => 'DemoA1',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'DA1',
            'icon' => '',
            'status' => Asset::STATUS_ACTIVE,
            'address_example' => '',
            'min_confirmations' => 1,
            'min_withdrawal_amount' => 100,
            'min_trade_amount' => 1,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => 100000000,
            'round' => 8,
        ]);

        Asset::create([
            'id' => 2,
            'name' => 'DemoA2',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'DA2',
            'icon' => '',
            'status' => Asset::STATUS_ACTIVE,
            'address_example' => '',
            'min_confirmations' => 1,
            'min_withdrawal_amount' => 100,
            'min_trade_amount' => 1,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => 100,
            'round' => 2,
        ]);

        Pair::create([
            'code' => 'DA1-DA2',
            'status' => Pair::STATUS_ACTIVE,
            'primary_asset_id' => 1,
            'secondary_asset_id' => 2,
            'min_trade_amount' => 1,
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

        Balance::create([
            'type' => 'in',
            'asset_id' => 1,
            'user_id' => 1,
            'quantity' => 5*100000000,
            'balance' => 500000000,
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 2,
            'user_id' => 1,
            'quantity' => 100*100,
            'balance' => 100*100,
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 1,
            'user_id' => 3,
            'quantity' => 5*100000000,
            'balance' => 500000000,
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 2,
            'user_id' => 3,
            'quantity' => 5*100000000,
            'balance' => 900000000,
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 2,
            'user_id' => 4,
            'quantity' => 100*100,
            'balance' => 100*100,
        ]);
    }
}
