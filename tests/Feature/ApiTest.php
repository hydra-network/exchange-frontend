<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Order, User, Asset};

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
        'seller2' => ['id' => 4, 'balance' => ['bitcoin' => 0.0001, 'ether' => 700]],
    ];

    private $bitcoinAsset;
    private $etherAsset;

    public function testBalanceFreezing()
    {
        $this->createEntities();

        $quantity = 1;
        $price = 2;

        //Sellers places an order to sell
        $bitcoinBalance = $this->participantBalances['seller1']['balance']['bitcoin'];
        $etherBalance = $this->participantBalances['seller1']['balance']['ether'];
        $this->createOrder($this->seller1, Order::TYPE_SELL, $quantity, $price);
        $this->checkBalance($this->seller1, $bitcoinBalance, ($etherBalance-$quantity), 0, $quantity);

        //Buyer places an order to buy
        $bitcoinBalance = $this->participantBalances['buyer1']['balance']['bitcoin'];
        $etherBalance = $this->participantBalances['buyer1']['balance']['ether'];
        $this->createOrder($this->buyer1, Order::TYPE_BUY, $quantity, $price);
        $this->checkBalance($this->buyer1, $bitcoinBalance-($quantity*$price), $etherBalance, ($quantity*$price), 0);
    }

    /*
    public function testUserBalances()
    {
        $this->createEntities();

        foreach ($this->participantBalances as $key => $balanceData) {
            $this->checkBalance($this->{$key}, $balanceData['balance']['bitcoin'], $balanceData['balance']['ether']);
        }
    } */

    private function checkBalance($user, $bitcoinAsset, $etherAsset, $bitcoinIOBalance = 0, $etherIOBalance = 0)
    {
        $answer = $this->get(
            route('market.balance', ['code' => $this->pairName]),
            ['Authorization' => 'Bearer ' . $user->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

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
