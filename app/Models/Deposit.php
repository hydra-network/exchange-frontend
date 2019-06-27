<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = ['tx_id', 'asset_id', 'user_id', 'address_id', 'status', 'quantity', 'confirmations'];
    
    CONST STATUS_NEW = 1;
}
