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
        set_time_limit(0);

        $btcSubunits = 100000000;
        $usdSubunits = 100;

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
            'min_trade_amount' => 0.0001,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => $btcSubunits,
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
            'min_trade_amount' => 0.0001,
            'withdrawal_fees' => 1,
            'exchange_fees' => 0,
            'subunits' => $usdSubunits,
            'round' => 2,
        ]);

        Pair::create([
            'id' => 1,
            'code' => 'DA1-DA2',
            'status' => Pair::STATUS_ACTIVE,
            'primary_asset_id' => 2,
            'secondary_asset_id' => 1,
            'min_trade_amount' => 0.0001,
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

        User::create([
            'id' => 2,
            'name' => 'DemoUser2',
            'email' => 'demouser2@dexdev.ru',
            //'password' => bcrypt('p' . rand(0, 9999) . time() . rand(0, 999999))
            'password' => bcrypt(123123)
        ]);

        User::create([
            'id' => 3,
            'name' => 'DemoUser3',
            'email' => 'demouser3@dexdev.ru',
            //'password' => bcrypt('p' . rand(0, 9999) . time() . rand(0, 999999))
            'password' => bcrypt(123123)
        ]);

        User::create([
            'id' => 4,
            'name' => 'DemoUser4',
            'email' => 'demouser4@dexdev.ru',
            //'password' => bcrypt('p' . rand(0, 9999) . time() . rand(0, 999999))
            'password' => bcrypt(123123)
        ]);

        for ($i = 1; $i <= 4; $i++) {
            Balance::create([
                'type' => 'in',
                'asset_id' => 1,
                'user_id' => $i,
                'quantity' => 1000*$btcSubunits,
                'balance' => 1000*$btcSubunits,
            ]);

            Balance::create([
                'type' => 'in',
                'asset_id' => 2,
                'user_id' => $i,
                'quantity' => 700000*$usdSubunits,
                'balance' => 700000*$usdSubunits,
            ]);
        }

        $time = time()-(1000*60);
        for ($i = 1; $i <= 1000; $i++) {
            if (rand(1, 2) == 2) { //sell order
                $type = Order::TYPE_SELL;
                $price = rand(10100, 12999);
            } else { //buy order
                if (rand(1, 2) == 1) {
                    $price = rand(10000, 10500);
                } else {
                    $price = rand(1000, 10500);
                }

                $type = Order::TYPE_BUY;
            }

            $quantity = rand(1, 20000000)/$btcSubunits;

            Order::create([
                'pair_id' => 1,
                'user_id' => rand(1, 4),
                'type' => $type,
                'status' => Order::STATUS_ACTIVE,
                'quantity' => $quantity*$btcSubunits,
                'quantity_remain' => $quantity*$btcSubunits,
                'price' => $price*100,
                'cost' => ($quantity*$price)*$usdSubunits,
                'cost_remain' => ($quantity*$price)*$usdSubunits,
                'created_at' => $time
            ]);

            Artisan::call('order:matcher', ['dealTime' => $time]);

            $time = $time+60;
        }
    }
}
