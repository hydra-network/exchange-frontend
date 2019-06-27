<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Order extends Resource
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
            'id' => $this->id,
            'pair_id' => $this->pair_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'owner_type' => $this->owner_type,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'amount_for_buyer' => $this->getAmount(),
            'cost_for_seller' => $this->getCost(),
            'deal_name' => $this->getDealName(),
            'quantity_remain' => $this->quantity_remain,
            'price' => $this->price,
            'cost' => $this->cost,
            'cost_remain' => $this->cost_remain,
            'pair' => $this->pair,
            'primary_asset' => $this->primary(),
            'secondary_asset' => $this->secondary(),
            'primary_asset' => $this->resource->pair->primary,
            'secondary_asset' => $this->resource->pair->secondary,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function pair()
    {
        return $this->resource->pair;
    }

    public function secondary()
    {
        $order = $this->resource;

        if ($order->type == $order::TYPE_SELL) {
            return $order->pair->secondary;
        } else {
            return $order->pair->primary;
        }
    }

    public function primary()
    {
        $order = $this->resource;

        if ($order->type == $order::TYPE_SELL) {
            return $order->pair->primary;
        } else {
            return $order->pair->secondary;
        }
    }

    public function getAmount()
    {
        $order = $this->resource;

        if ($order->type == $order::TYPE_SELL) {
            return $order->cost_remain;
        } else {
            return $order->quantity_remain;
        }
    }

    public function getCost()
    {
        $order = $this->resource;

        if ($order->type != $order::TYPE_SELL) {
            return $order->cost_remain;
        } else {
            return $order->quantity_remain;
        }
    }

    public function getDealName()
    {
        $order = $this->resource;

        if ($order->type == $order::TYPE_SELL) {
            return 'Sell ' . $order->quantity_remain . ' ' . $this->secondaryname;
        } else {
            return 'Buy ' . $order->quantity_remain . ' ' . $this->primary->name;
        }
    }
}
