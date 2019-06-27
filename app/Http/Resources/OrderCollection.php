<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Order';

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}