<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscrowOrder extends Model
{
    protected $table = 'escrow_order';
    protected $fillable = ['hash', 'order_id', 'ip', 'status', 'quantity', 'pair_id', 'user_address', 'deposit_address_id', 'cost', 'withdrawal_id', 'activated_at'];

    const STATUS_NEW = 'new';
    const STATUS_WAIT_CONF = 'wait_conf';
    const STATUS_DONE = 'done';
    const STATUS_EXPIRED = 'expired';

    public function pair()
    {
        return $this->hasOne('App\Models\Pair', 'id', 'pair_id');
    }

    public function getOrder()
    {
        return $this->hasOne('App\Models\Order', 'id', 'order_id');
    }

    public function secondary()
    {
        return $this->pair->secondary;
    }

    public function primary()
    {
        return $this->pair->primary;
    }

    public function getDepositAddress()
    {
        return $this->hasOne('App\Models\Address', 'id', 'deposit_address_id')->first();
    }

    public function getDeposit()
    {
        return $this->hasOne('App\Models\EscrowDeposit', 'escrow_id', 'id')->orderBy('id', 'DESC')->first();
    }

    public function getCreatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i');
    }

    public function getUpdatedAtAttribute($date)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i');
    }
}
