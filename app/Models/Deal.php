<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = ['pair_id', 'buyer_user_id', 'seller_user_id', 'ask_id', 'bid_id', 'quantity', 'price', 'cost', 'type'];
    protected $hidden = ['buyer_user_id', 'seller_user_id'];
    public $timestamps = true;

    const TYPE_SELL = 'sell';
    const TYPE_BUY = 'buy';

    public function pair()
    {
        return $this->hasOne('App\Models\Pair', 'id', 'pair_id');
    }
}
