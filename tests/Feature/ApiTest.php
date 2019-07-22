<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Deal, Order, User, Asset};
use Illuminate\Support\Facades\Artisan;

class ApiTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    private $buyer1;
    private $buyer2;
    private $seller1;
    private $seller2;

    private $pairName = 'BTC-ETH';

    private $participantBalances = [
        'buyer1' => ['id' => 1, 'balance' => ['bitcoin' => 100, 'ether' => 0]], //[id, [bitcoin, ether]]
        'buyer2' => ['id' => 2, 'balance' => ['bitcoin' => 300, 'ether' => 100]],
        'seller1' => ['id' => 3, 'balance' => ['bitcoin' => 0, 'ether' => 500]],
        'seller2' => ['id' => 4, 'balance' => ['bitcoin' => 1, 'ether' => 500]],
    ];

    private $bitcoinAsset;
    private $etherAsset;

    public function testBasicUserExchange()
    {
        $this->createEntities();

        //placing of all orders from all users
        $orders = [
            'buyer1' => [[Order::TYPE_BUY, 10, 2]],
            'buyer2' => [[Order::TYPE_BUY, 1.1, 3]],
            'seller1' => [[Order::TYPE_SELL, 1.5, 1]],
            'seller2' => [[Order::TYPE_SELL, 200, 5]],
        ];

        foreach ($orders as $userKey => $orders) {
            foreach ($orders as $order) {
                $this->createOrder($this->{$userKey}, $order[0], $order[1], $order[2]);
            }
        }

        //first tick
        Artisan::call('order:matcher');
        sleep(1);

        $buyOrders = $this->getOrders(Order::TYPE_BUY);
        $this->assertEquals(count($buyOrders), 1);
        $this->assertEquals(10, $buyOrders[0]['quantity_remain']);

        $sellOrders = $this->getOrders(Order::TYPE_SELL);
        $this->assertEquals(count($sellOrders), 2);
        $this->assertEquals(0.4, $sellOrders[0]['quantity_remain']);
        $this->assertEquals(200, $sellOrders[1]['quantity_remain']);

        //second tick
        Artisan::call('order:matcher');
        sleep(1);

        //last tick
        Artisan::call('order:matcher');
        sleep(1);

        $buyOrders = $this->getOrders(Order::TYPE_BUY);
        $this->assertEquals(count($buyOrders), 1);
        $this->assertEquals(9.6, $buyOrders[0]['quantity_remain']);

        $sellOrders = $this->getOrders(Order::TYPE_SELL);
        $this->assertEquals(count($sellOrders), 1);
        $this->assertEquals(200, $sellOrders[0]['quantity_remain']);

        $deals = $this->get(
            route('market.deals', ['code' => $this->pairName, 'my' => 1, 'sell' => 1, 'buy' => 1]),
            ['Authorization' => 'Bearer ' . $this->buyer1->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        $deals = json_decode($deals, true)['data'];
        $this->assertEquals(count($deals), 1);
        $this->assertEquals($deals[0]['price'], 2);
        $this->assertEquals($deals[0]['quantity'], 0.4);
        $this->assertEquals($deals[0]['cost'], 0.8);
        $this->assertEquals($deals[0]['type'], Deal::TYPE_SELLER_TAKER);
    }

    public function testUserBalances()
    {
        $this->createEntities();

        foreach ($this->participantBalances as $key => $balanceData) {
            $this->checkBalance($this->{$key}, $balanceData['balance']['bitcoin'], $balanceData['balance']['ether']);
        }
    }

    public function testBalanceFreezing()
    {
        $this->createEntities();

        $quantity = 1.0012;
        $price = 2.0023;

        //Sellers places an order to sell
        $bitcoinBalance = $this->participantBalances['seller1']['balance']['bitcoin'];
        $etherBalance = $this->participantBalances['seller1']['balance']['ether'];
        $this->createOrder($this->seller1, Order::TYPE_SELL, $quantity, $price);
        $this->checkBalance($this->seller1, $bitcoinBalance, ($etherBalance-$quantity), 0, $quantity);

        //Buyer places an order to buy
        $bitcoinBalance = $this->participantBalances['buyer1']['balance']['bitcoin'];
        $etherBalance = $this->participantBalances['buyer1']['balance']['ether'];
        $order = $this->createOrder($this->buyer1, Order::TYPE_BUY, $quantity, $price);
        $this->checkBalance($this->buyer1, $bitcoinBalance-($quantity*$price), $etherBalance, ($quantity*$price), 0);

        $this->post(
            route('market.order.remove'),
            ['id' => $order['order_id']],
            ['Authorization' => 'Bearer ' . $this->buyer1->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        $this->checkBalance($this->buyer1, $bitcoinBalance, $etherBalance);
    }

    private function getOrders($type)
    {
        $orders = $this->get(
            route('market.orders', ['code' => $this->pairName, 'type' => $type]),
            ['Authorization' => 'Bearer ' . $this->buyer1->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        return json_decode($orders, true)['list']['data'];
    }

    private function checkBalance($user, $bitcoinAsset, $etherAsset, $bitcoinIOBalance = 0, $etherIOBalance = 0)
    {
        $answer = $this->get(
            route('market.balance', ['code' => $this->pairName]),
            ['Authorization' => 'Bearer ' . $user->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        //echo substr($answer, 0, 300);

        $json = json_decode($answer, true);

        $this->assertEquals($bitcoinAsset, str_replace(" ", '', $json['primary_asset']));
        $this->assertEquals($etherAsset, str_replace(" ", '', $json['secondary_asset']));

        $this->assertEquals(0, $json['primary_asset_unc_balance']);
        $this->assertEquals(0, $json['secondary_asset_unc_balance']);
        $this->assertEquals($bitcoinIOBalance, str_replace(" ", '', $json['primary_asset_io_balance']));
        $this->assertEquals($etherIOBalance, str_replace(" ", '', $json['secondary_asset_io_balance']));
    }

    private function createOrder($user, $type, $quantity, $price)
    {
        $data = [
            'type' => $type,
            'quantity' => $quantity,
            'price' => $price,
            'pair' => $this->pairName
        ];

        $answer = $this->post(
            route('market.order.add'),
            $data,
            ['Authorization' => 'Bearer ' . $user->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        //echo  "\n\n====\n" . route('market.order.add') . substr(str_replace(["\r", "\n"], '', $answer), 0, 8000) . "\n====\n";

        $json = json_decode($answer, true);

        $this->assertEquals("success", $json['result']);
        $this->assertIsInt($json['order_id']);

        return $json;
    }

    private function createEntities()
    {
        $this->bitcoinAsset = Asset::whereCode('BTC')->firstOrFail();
        $this->etherAsset = Asset::whereCode('ETH')->firstOrFail();

        foreach ($this->participantBalances as $key => $balanceData) {
            $this->{$key} = User::whereId($balanceData['id'])->firstOrFail();
            $this->{$key}->cheatTransaction($this->bitcoinAsset, $balanceData['balance']['bitcoin']*$this->bitcoinAsset->subunits);
            $this->{$key}->cheatTransaction($this->etherAsset, $balanceData['balance']['ether']*$this->etherAsset->subunits);
        }
    }
}
