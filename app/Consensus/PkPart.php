<?php

namespace App\Consensus;

class PkPart
{
    public $value;
    public $depo;
    public $owner;
    public $keeper;

    public function __construct(Depo $depo, Trader $keeper, Trader $owner, $value, $pos)
    {
        $this->depo = $depo;
        $this->value = $value;
        $this->owner = $owner;
        $this->pos = $pos;
        $this->keeper = $keeper;
    }
}