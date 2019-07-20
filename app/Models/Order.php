<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Workers\BalanceShapeShift;

class Order extends Model
{
    protected $fillable = ['pair_id', 'user_id', 'type', 'owner_type', 'status', 'quantity', 'quantity_remain', 'cost', 'cost_remain', 'price'];
    protected $hidden = ['user_id'];

    public $timestamps = true;
    
    const TYPE_SELL = 'sell';
    const TYPE_BUY = 'buy';

    const OWNER_TYPE_MAKER = 'maker';
    const OWNER_TYPE_TAKER = 'taker';

    const STATUS_ACTIVE = 1;
    const STATUS_PARTIAL = 2;
    const STATUS_EMPTY = 3;
    const STATUS_NEW = 4;
    const STATUS_CANCEL = 5;

    public function isNew()
    {
        return ($this->status == self::STATUS_NEW);
    }

    public function breakAndSave()
    {
        $this->owner_type = self::OWNER_TYPE_TAKER;
        $this->status = self::STATUS_PARTIAL;
        
        return $this->save();
    }
    
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    
    public function pair()
    {
        return $this->hasOne('App\Models\Pair', 'id', 'pair_id');
    }

    public function getEscrowOrder()
    {
        return $this->hasOne('App\Models\EscrowOrder', 'order_id', 'id');
    }

    public function secondary()
    {
        return $this->pair->secondary;
    }

    public function primary()
    {
        return $this->pair->primary;
    }

    public function freezeAssetsAndSave(User $user)
    {
        $primary = $this->pair->primary;
        $secondary = $this->pair->secondary;

        $asset = ($this->type == self::TYPE_BUY) ? $primary : $secondary;
        $quantity = ($this->type == self::TYPE_BUY) ? $this->cost : $this->quantity;

        if (!$this->id) {
            $this->save();
        }

        $user->freezeAssets($asset, $this, $quantity);

        return $this->save();
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
