<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\SecondaryAssetRestrictions;
use App\Rules\BalanceChecker;
use App\Models\Pair;
use App\Models\Order;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $pair = Pair::where('code', $this->input('pair'))->first();

        return [
            'pair' => ['required', 'exists:pairs,code'],
            'type' => ['required', Rule::in([Order::TYPE_SELL, Order::TYPE_BUY])],
            'quantity' => [
                'required',
                'numeric',
                new BalanceChecker($pair, $this->input('type'), $this->input('quantity'), $this->input('price')),
                new SecondaryAssetRestrictions($pair, $this->input('type'), $this->input('quantity'), $this->input('price')),
            ],
            'price' => 'required|numeric|min:0.00000001',
        ];
    }

    public function save()
    {
        return $this->save();
    }
}
