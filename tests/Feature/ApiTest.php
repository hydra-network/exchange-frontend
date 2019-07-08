<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function testBasicUseCase()
    {
        $this->createEntities();
        $answer = $this->json(
            'post',
            '/api/v1/market/getSummary/BTCT-LTCT',
            ['count' => 1],
            ['Authorization' => 'Bearer ' . self::getTestUserToken()]
        )   ->assertStatus(200)
            ->getOriginalContent();

        $this->assertEquals(1, $answer['response']['test']);
    }

    private function createEntities()
    {

    }
}
