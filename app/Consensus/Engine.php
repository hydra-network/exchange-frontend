<?php

namespace App\Consensus;

class Engine
{
    public $traders = [];
    public $blockchain = [];

    public function __construct(Blockchain $blockchain, $traders)
    {
        $this->blockchain = $blockchain;
        $this->traders = $traders;
    }

    public function depo(Trader $trader, $bytes)
    {
        $amount = $this->blockchain->getAmount(md5(json_encode($bytes)));

        $depo = new Depo($trader, $amount);

        //Разделяем приватный ключ на 5 равных частей
        $parts = $this->separatePK($depo, $trader, $bytes);


    }

    protected function separatePK($depo, $owner, $bytes)
    {
        $parts = [];
        $size = floor(count($bytes) / count($this->traders));

        $current = 0;
        $pos = 1;
        foreach ($this->traders as $trader) {
            $part = array_slice($bytes, $current, $size);
            $parts[] = new PkPart($depo, $trader, $owner, $part, $pos);
            $current = $current+$size;
            $pos++;
        }

        return $parts;
    }
}