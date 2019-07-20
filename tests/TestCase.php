<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private $seeded = false;

    public function setUp() : void
    {
        parent::setUp();

        Artisan::call('db:seed --class=TestSeeder');
    }

    static function getTestUserToken()
    {
        $token = '';

        return $token;
    }
}
