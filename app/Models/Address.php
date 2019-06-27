<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libs\RpcClient;

class Address extends Model
{
    protected $table = 'crypto_addresses';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public function getAsset()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'asset_id')->first();
    }
    
    public function getIncomeTransactions() : array
    {
        if($client = $this->currency->getRpcClient()) {
            return $client->getIncome($this->address);
        }
        
        return [];
    }

    public function sendCoins($amount) : string
    {
        if($client = $this->currency->getRpcClient()) {
            if ($result = $client->send($this->address, $amount)) {
                return $result->txId;
            }
        }

        return "";
    }
}
