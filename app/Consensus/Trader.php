<?php

namespace App\Consensus;

class Trader
{
    public $name = '';

    public function __construct($name)
    {
        $this->name = $name;
    }
}