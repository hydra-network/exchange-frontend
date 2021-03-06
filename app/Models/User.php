<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'confirm_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function isActivated()
    {
        return $this->activated_at;
    }

    public function passwordSecurity()
    {
        return $this->hasOne('App\PasswordSecurity');
    }

    public function getBalance(Asset $asset)
    {
        $balance = 0;

        if ($balanceHistory = Balance::where('user_id', $this->id)->where('asset_id', $asset->id)->orderBy('id', 'DESC')->first()) {
            $balance = $balanceHistory->balance;
        }

        return $balance;
    }
    
    public function freezeAssets(Asset $asset, Order $order, $quantity)
    {
        $balance = $this->getBalance($asset);
        $user = $this;
        $balance = $balance - $quantity;

        //$problemUser = self::where('email', 'demobuyer1@dexdev.ru')->first();

        //echo "\n=====\nfreezeAssetsAndSave ({$problemUser->id}=={$this->id} pu amount of BTC is " . $problemUser->getBalance(Asset::whereCode('btc')->first()) . ": " . $this->email . "-balance of " . $asset->code . " is " . $this->getBalance($asset) . ", quantity to freeze is $quantity\n====\n";

        if ($balance < 0) {
            throw new \Exception('Balance < 0 (ID ' . $this->email . ', balance ' . $this->getBalance($asset) . ' < ' . $quantity . ')');
        }

        $balanceModel = new Balance;
        $balanceModel->fill(['type' => Balance::TYPE_FREEZE, 'asset_id' => $asset->id, 'quantity' => $quantity, 'user_id' => $user->id, 'order_id' => $order->id, 'deal_id' => null, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }
    
    public function unfreezeAssets(Asset $asset, Order $order, $quantity)
    {
        $balance = $this->getBalance($asset);
        $user = $this;
        $balance = $balance + $quantity;

        if ($balance < 0) {
            throw new \Exception('Balance < 0');
        }

        $balanceModel = new Balance;
        $balanceModel->fill(['type' => Balance::TYPE_UNFREEZE, 'asset_id' => $asset->id, 'quantity' => $quantity, 'user_id' => $user->id, 'order_id' => $order->id, 'deal_id' => null, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }

    public function cheatTransaction(Asset $asset, $balance)
    {
        if ($balance < 0) {
            throw new \Exception('Balance < 0');
        }

        $balanceModel = new Balance;

        $balanceModel->fill(['type' => Balance::TYPE_INCOME, 'asset_id' => $asset->id, 'user_id' => $this->id, 'order_id' => null, 'deal_id' => null, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }

    public function transaction(Asset $asset, Deal $deal, $balance)
    {
        if ($balance < 0) {
            throw new \Exception('Balance < 0');
        }

        $balanceModel = new Balance;

        $balanceModel->fill(['type' => Balance::TYPE_INCOME, 'asset_id' => $asset->id, 'user_id' => $this->id, 'order_id' => null, 'deal_id' => $deal->id, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }

    public function income(Asset $asset, Deal $deal, $quantity)
    {
        $balance = $this->getBalance($asset);
        $user = $this;
        $balance = $balance + $quantity;

        if ($balance < 0) {
            throw new \Exception('Balance < 0');
        }

        $balanceModel = new Balance;
        $balanceModel->fill(['type' => Balance::TYPE_INCOME, 'asset_id' => $asset->id, 'quantity' => $quantity, 'user_id' => $user->id, 'order_id' => null, 'deal_id' => $deal->id, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }
    
    public function outcome(Asset $asset, Deal $deal, $quantity)
    {
        $balance = $this->getBalance($asset);
        $user = $this;
        $balance = $balance - $quantity;

        if ($balance < 0) {
            throw new \Exception('Balance < 0');
        }

        $balanceModel = new Balance;
        
        $balanceModel->fill(['type' => Balance::TYPE_OUTCOME, 'asset_id' => $asset->id, 'quantity' => $quantity, 'user_id' => $user->id, 'order_id' => null, 'deal_id' => $deal->id, 'deposit_id' => null, 'withdrawal_id' => null, 'balance' => $balance]);

        return $balanceModel->save();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthToken()
    {
        return \JWTAuth::fromUser($this);
    }
}
