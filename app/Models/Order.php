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
    
    const STATUS_NEW = 'new';
    const STATUS_MATCHING = 'matching';
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVE = 'archive';
    const STATUS_CANCEL = 'cancel';
    const STATUS_PARTIAL = 'broken';
    const STATUS_ESCROW_WAIT_SELLER = 'escrow_ws';
    const STATUS_ESCROW_WAIT_DEPO = 'escrow_wd';
    const STATUS_ESCROW_WAIT_CONF = 'escrow_wc';
    const STATUS_ESCROW_DONE = 'escrow_done';

    public function isNew()
    {
        return ($this->status == self::STATUS_NEW);
    }

    public function activateAndSave()
    {
        $this->status = self::STATUS_ACTIVE;
        
        return $this->save();
    }
    
    public function archiveAndSave()
    {
        $this->status = self::STATUS_ARCHIVE;

        return $this->save();
    }
    
    public function matchingAndSave()
    {
        $this->status = self::STATUS_MATCHING;

        return $this->save();
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

        if ($this->type == self::TYPE_BUY) {
            $user->freezeAssets($primary, $this, ($this->quantity*$this->price));
        } else {
            $user->freezeAssets($secondary, $this, $this->quantity);
        }

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
