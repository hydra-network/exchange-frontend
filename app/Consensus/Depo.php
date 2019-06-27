<?php

namespace App\Consensus;

use Illuminate\Support\Str;

class Depo
{
    public $id;
    public $amount;
    public $trader;

    public function __construct($trader, $amount)
    {
        $this->id = Str::random();
        $this->amount = $amount;
        $this->trader = $trader;
    }
}
