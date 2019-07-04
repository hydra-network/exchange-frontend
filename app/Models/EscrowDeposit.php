<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscrowDeposit extends Model
{
    protected $table = 'escrow_deposits';//
    protected $fillable = ['asset_id', 'escrow_id', 'address_id', 'tx_id', 'confirmations', 'quantity'];

    const STATUS_NEW = 'new';
    const STATUS_WAIT_CONF = 'wait_conf';
    const STATUS_DONE = 'done';

    public function pair()
    {
        return $this->hasOne('App\Models\Pair', 'id', 'pair_id');
    }

    public function secondary()
    {
        return $this->pair->secondary;
    }

    public function primary()
    {
        return $this->pair->primary;
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
