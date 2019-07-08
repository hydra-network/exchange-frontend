<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Order;
use App\Models\Deal;

class SecondaryAssetRestrictions implements Rule
{
    private $pair;
    private $message;
    private $price;
    private $quantity;
    private $type;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($pair, $type, $quantity, $price)
    {
        $this->pair = $pair;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pair = $this->pair;

        if ($pair->min_price && $pair->min_price > $this->price) {
            $this->message = "Sorry, but the minimal price is {$pair->min_price}";
            return false;
        }

        if ($pair->max_price && $pair->max_price < $this->price) {
            $this->message = "Sorry, but the maximum price is {$pair->max_price}";

            return false;
        }

        if ($value < $this->pair->min_trade_amount) {
            $this->message = "Minimum trade amount is " . $this->pair->min_trade_amount;
            return false;
        }


        if ($pair->limit_from_one_person && $this->type == Order::TYPE_SELL) {
            $personVolume = Order::where('user_id', auth()->user()->id)->where('pair_id', $this->pair->pair_id)->whereIn('status', [Order::STATUS_NEW, Order::STATUS_ACTIVE, Order::STATUS_PARTIAL])->where('type', Order::TYPE_SELL)->sum('quantity_remain');

            $remaining = ($pair->limit_from_one_person-$personVolume);
            if ($remaining < $this->quantity) {
                return response()->json(['result' => 'fail', 'errors' => ["Remaining limit for you is " . $remaining . '. Your current volume is ' . $personVolume]]);
            }
        }

        $volume24 = Deal::where('pair_id', $this->pair->pair_id)->whereBetween('created_at', [date('Y-m-d H:i:s', time()-86400), date('Y-m-d H:i:s', time())])
            ->where('pair_id',  $this->pair->pair_id)
            ->sum('cost');

        if ($pair->daily_volume_limit && $volume24 > $pair->daily_volume_limit) {
            return response()->json(['result' => 'fail', 'errors' => ["Sorry, but we've achieved the daily volume limit for this market"]]);
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
