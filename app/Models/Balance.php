<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balance_history';
    protected $fillable = ['type', 'asset_id', 'quantity', 'user_id', 'deal_id', 'deposit_id', 'withdrawal_id', 'order_id', 'balance'];
    
    CONST TYPE_INCOME = 'in';
    CONST TYPE_OUTCOME = 'out';
    CONST TYPE_FREEZE = 'freeze';
    CONST TYPE_UNFREEZE = 'unfreeze';
    
    public function income($asset_id, $quantity, $user_id, $deal_id = null, $deposit_id = null, $withdrawal_id = null)
    {
        $balance = 0;
        
        if ($lastBalanceState = self::where(['user_id' => $user_id])->where('asset_id', $asset_id)->orderBy('id', 'DESC')->first()) {
            $balance = $lastBalanceState->balance;
        }
        
        $balance = $balance + $quantity;
        
        $this->fill(['type' => self::TYPE_INCOME, 'asset_id' => $asset_id, 'quantity' => $quantity, 'user_id' => $user_id, 'deal_id' => $deal_id, 'deposit_id' => $deposit_id, 'withdrawal_id' => $withdrawal_id, 'balance' => $balance]);

        return $this->save();
    }
    
    public function outcome($asset_id, $quantity, $user_id, $deal_id = null, $deposit_id = null, $withdrawal_id = null)
    {
        $balance = 0;
        
        if ($lastBalanceState = self::where(['user_id' => $user_id])->where('asset_id', $asset_id)->orderBy('id', 'DESC')->first()) {
            $balance = $lastBalanceState->balance;
        }
        
        $balance = $balance - $quantity;
        
        $this->fill(['type' => self::TYPE_OUTCOME, 'asset_id' => $asset_id, 'quantity' => $quantity, 'user_id' => $user_id, 'deal_id' => $deal_id, 'deposit_id' => $deposit_id, 'withdrawal_id' => $withdrawal_id, 'balance' => $balance]);

        return $this->save();
    }
}
