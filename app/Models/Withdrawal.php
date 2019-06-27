<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['tx_id', 'asset_id', 'user_id', 'address', 'status', 'quantity', 'confirmations', 'ip'];

    CONST STATUS_NEW = 1;
    CONST STATUS_AUTHORIZED = 4;
    CONST STATUS_DONE = 2;
    CONST STATUS_FAIL = 3;

    public function getAsset()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'asset_id');
    }
    
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
