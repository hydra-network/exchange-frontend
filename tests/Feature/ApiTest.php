<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Asset};

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
        'buyer1' => [1, [100000000, 2560000]],
        'buyer2' => [2, [0, 1000000.14]],
        'seller1' => [3, [0, 0]],
        'seller2' => [4, [1200000000, 990000.1]],
    ];

    private $primaryAsset;
    private $secondaryAsset;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testUserBalancesCase()
    {
        $this->createEntities();

        foreach ($this->participantBalances as $key => $balancesData) {
            $answer = $this->get(
                route('market.balance', ['code' => $this->pairName]),
                ['Authorization' => 'Bearer ' . $this->{$key}->getAuthToken()]
            )
                ->assertStatus(200)
                ->getContent();

            $json = json_decode($answer, true);

            $this->assertEquals($balancesData[1][0], $json['primary_asset']);
            $this->assertEquals($balancesData[1][1], $json['secondary_asset']);

            $this->assertEquals(0, $json['primary_asset_unc_balance']);
            $this->assertEquals(0, $json['secondary_asset_unc_balance']);
            $this->assertEquals(0, $json['primary_asset_io_balance']);
            $this->assertEquals(0, $json['secondary_asset_io_balance']);
        }
    }

    private function createEntities()
    {
        $this->primaryAsset = Asset::whereId(1)->firstOrFail();
        $this->secondaryAsset = Asset::whereId(2)->firstOrFail();

        foreach ($this->participantBalances as $key => $balancesData) {
            $this->{$key} = User::whereId($balancesData[0])->firstOrFail();

            if ($balancesData[1][0]) {
                $this->{$key}->cheatTransaction($this->primaryAsset, $balancesData[1][0]);
            }

            if ($balancesData[1][1]) {
                $this->{$key}->cheatTransaction($this->primaryAsset, $balancesData[1][1]);
            }
        }
    }
}
