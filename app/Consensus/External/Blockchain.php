<?php

namespace App\Consensus\External;

class Blockchain
{
    public $balances;

    public function getAmount($hash)
    {
        return $this->balances[$hash];
    }
}