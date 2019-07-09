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

    private $primaryAsset;
    private $secondaryAsset;

    public function testBasicUseCase()
    {
        $this->createEntities();

        $answer = $this->json(
            'post',
            '/api/v1/market/getSummary/BTCT-LTCT',
            ['count' => 1]
        )   ->assertStatus(200)
            ->getOriginalContent();

        $this->assertEquals(1, $answer['response']['test']);
    }

    private function createEntities()
    {
        $this->buyer1 = User::whereId(1)->firstOrFail();
        $this->buyer2 = User::whereId(2)->firstOrFail();

        $this->seller1 = User::whereId(3)->firstOrFail();
        $this->seller2 = User::whereId(4)->firstOrFail();

        $this->primaryAsset = Asset::whereId(1)->firstOrFail();
        $this->secondaryAsset = Asset::whereId(2)->firstOrFail();

        $this->buyer1->cheatTransaction($this->primaryAsset, 1*100000000);
        $this->buyer1->cheatTransaction($this->secondaryAsset, 10*100000000);

        $this->seller1->cheatTransaction($this->primaryAsset, 2*100000000);
        $this->seller2->cheatTransaction($this->secondaryAsset, 20*100000000);
    }
}
