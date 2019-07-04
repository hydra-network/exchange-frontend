<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EscrowOrder extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'hash' => $this->hash,
            'order_id' => $this->order_id,
            'status' => $this->status,
            'quantity' =>  $this->quantity,
            'primary_asset' =>  $this->primary,
            'secondary_asset' =>  $this->secondary,
            'pair' =>  $this->pair,
            'deposit_address' =>  $this->getDepositAddress(),
            'user_address' =>  $this->user_address,
            'deposit_tx_amount' => $this->getDepositAmount(),
            'deposit_tx_id' => $this->getDepositTxId(),
            'deposit_confirmations_count' => $this->getDepositConfirmationCount(),
            'deposit_date' => $this->getDepositDate(),
            'deposit_amount' => $this->getDepositAmount(),
        ];
    }

    public function getDepositAddress()
    {
        if ($address = $this->resource->getDepositAddress()) {
            return $address->address;
        }

        return false;
    }

    public function pair()
    {
        return $this->resource->pair;
    }

    public function secondary()
    {
        return $this->resource->pair->secondary;
    }

    public function primary()
    {
        return $this->resource->pair->primary;
    }

    public function getDepositTxId()
    {
        if ($depo = $this->resource->getDeposit()) {
            return $depo->tx_id;
        }

        return false;
    }

    public function getDepositAmount()
    {
        if ($depo = $this->resource->getDeposit()) {
            return $depo->quantity;
        }

        return null;
    }

    public function getDepositDate()
    {
        if ($depo = $this->resource->getDeposit()) {
            return date('H:i', strtotime($depo->created_at));
        }

        return false;
    }

    public function getDepositConfirmationCount()
    {
        if ($depo = $this->resource->getDeposit()) {
            return $depo->confirmations;
        }

        return false;
    }
}
