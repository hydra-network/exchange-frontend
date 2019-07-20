<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Order;

class BalanceChecker implements Rule
{
    private $pair;
    private $type;
    private $quantity;
    private $price;

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
        $primaryAsset = $this->pair->primary;
        $secondaryAsset = $this->pair->secondary;
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($this->type == Order::TYPE_BUY) {
            $balance = $user->getBalance($primaryAsset);

            $cost = ($this->quantity * $this->price)*$primaryAsset->subunits;

            if ($balance < $cost) {
                return false;
            }
        } else {
            $balance = $user->getBalance($secondaryAsset);

            if ($balance < $this->quantity) {
                return false;
            }
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
        return 'Insufficient funds [1]';
    }
}
