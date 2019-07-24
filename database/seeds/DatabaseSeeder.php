<?php

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Pair;
use App\Models\User;
use App\Models\Order;
use App\Models\Balance;

class DatabaseSeeder extends Seeder
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
            'name' => 'DemoBTC',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'BTCD',
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
            'name' => 'DemoUSD',
            'type' => 'demo-asset',
            'description' => '',
            'code' => 'USDD',
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
            'id' => 1,
            'code' => 'DA1-DA2',
            'status' => Pair::STATUS_ACTIVE,
            'primary_asset_id' => 2,
            'secondary_asset_id' => 1,
            'min_trade_amount' => 1,
            'limit_from_one_person' => null,
            'max_price' => null,
            'min_price' => null,
            'daily_volume_limit' => null,
        ]);


        User::create([
            'id' => 1,
            'name' => 'DemoUser',
            'email' => 'demouser@dexdev.ru',
            //'password' => bcrypt('p' . rand(0, 9999) . time() . rand(0, 999999))
            'password' => bcrypt(123123)
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 1,
            'user_id' => 1,
            'quantity' => 1000*100000000,
            'balance' => 1000*100000000,
        ]);

        Balance::create([
            'type' => 'in',
            'asset_id' => 2,
            'user_id' => 1,
            'quantity' => 700000*100,
            'balance' => 700000*100,
        ]);

        for ($i = 1; $i <= 1000; $i++) {
            if (rand(1, 2) == 2) { //sell order
                $type = Order::TYPE_SELL;
                $price = rand(101000, 500000)/100;
            } else { //buy order
                $price = rand(50000, 128000)/100;
                $type = Order::TYPE_BUY;
            }

            $quantity = rand(1, 2000000)/100000000;

            Order::create([
                'pair_id' => 1,
                'user_id' => 1,
                'type' => $type,
                'status' => Order::STATUS_ACTIVE,
                'quantity' => $quantity*100000000,
                'quantity_remain' => $quantity*100000000,
                'price' => $price*100,
                'cost' => ($quantity*$price)*100,
                'cost_remain' => ($quantity*$price)*100,
            ]);

            Artisan::call('order:matcher');
        }
    }
}
