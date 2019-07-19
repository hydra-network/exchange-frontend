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

    private $pairName = 'DA1-DA2';

    private $participantBalances = [
        'buyer1' => [1, [0.00001, 25600]],
        'buyer2' => [2, [0, 100.14]],
        'seller1' => [3, [12, 5670.1]],
        'seller2' => [4, [0.0001, 99.1]],
    ];

    private $primaryAsset;
    private $secondaryAsset;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    private function testUserBalances()
    {
        $this->createEntities();

        foreach ($this->participantBalances as $key => $balancesData) {
            $this->checkBalance($this->{$key}, $balancesData[1][0], $balancesData[1][1]);
        }
    }

    public function testBalanceFreezing()
    {
        $this->createEntities();

        $quantity = 1.1;
        $price = 13100.09;


        //Buyer places order to buy
        $json = $this->createOrder($this->buyer1, Order::TYPE_BUY, $quantity, $price, $this->pairName);

        $this->assertEquals(1, $json['order_id']);

        $primaryBalance = $this->participantBalances['buyer1'][1][0];
        $secondaryBalance = $this->participantBalances['buyer1'][1][1];

        $this->checkBalance($this->seller1, $primaryBalance-($quantity*$price), $secondaryBalance, 0, 0);

        //Sellers places order to sell
        $json = $this->createOrder($this->seller1, Order::TYPE_SELL, $quantity, $price, $this->pairName);

        $this->assertEquals(1, $json['order_id']);

        $primaryBalance = $this->participantBalances['seller1'][1][0];
        $secondaryBalance = $this->participantBalances['seller1'][1][1];

        $this->checkBalance($this->seller1, $primaryBalance, ($secondaryBalance-$quantity), 0, $quantity);
    }

    private function checkBalance($user, $primaryAsset, $secondaryAsset, $primaryIOBalance = 0, $secondaryIOBalance = 0)
    {
        $answer = $this->get(
            route('market.balance', ['code' => $this->pairName]),
            ['Authorization' => 'Bearer ' . $user->getAuthToken()]
        )
            ->assertStatus(200)
            ->getContent();

        $json = json_decode($answer, true);

        $this->assertEquals($primaryAsset, str_replace(" ", '', $json['primary_asset']));
        $this->assertEquals($secondaryAsset, str_replace(" ", '', $json['secondary_asset']));

        $this->assertEquals(0, $json['primary_asset_unc_balance']);
        $this->assertEquals(0, $json['secondary_asset_unc_balance']);
        $this->assertEquals($primaryIOBalance, str_replace(" ", '', $json['primary_asset_io_balance']));
        $this->assertEquals($secondaryIOBalance, str_replace(" ", '', $json['secondary_asset_io_balance']));
    }

    private function createOrder($user, $type, $quantity, $price, $pair)
    {
        $answer = $this->post(
            route('market.order.add'),
            [
                'type' => $type,
                'quantity' => $quantity,
                'price' => $price,
                'pair' => $pair
            ],
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
        $this->primaryAsset = Asset::whereId(1)->firstOrFail();
        $this->secondaryAsset = Asset::whereId(2)->firstOrFail();

        foreach ($this->participantBalances as $key => $balancesData) {
            $this->{$key} = User::whereId($balancesData[0])->firstOrFail();

            if ($balancesData[1][0]) {
                $this->{$key}->cheatTransaction($this->primaryAsset, $balancesData[1][0]*$this->primaryAsset->subunits);
            }

            if ($balancesData[1][1]) {
                $this->{$key}->cheatTransaction($this->secondaryAsset, $balancesData[1][1]*$this->secondaryAsset->subunits);
            }
        }
    }
}
