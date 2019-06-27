
@extends('guest.layout')

@section('title', 'Hydra Escrow Service')

@section('content')
    <div class="flex-center position-ref full-height" id="app">
        <div class="content">
            <div class="title m-b-md">
                <a href="/app"><img src="/img/logo.png" width="250" /></a>
            </div>

            <?php if ($order->type == $order::TYPE_SELL) { ?>
                <h1>{{ $order->quantity_remain  }} {{ $order->secondaryname  }} </h1>
            <?php } else { ?>
                <h1>{{ $order->cost_remain  }} {{ $order->primary->name  }} </h1>
            <?php } ?>
            <h1><span style="color: red">for sale</span></h1>

            <?php if ($order->type == $order::TYPE_SELL) { ?>
                <h2> {{ $order->cost_remain  }} {{ $order->primary->name  }}</h2>
            <?php } else { ?>
                <h2> {{ $order->quantity_remain  }} {{ $order->secondaryname  }}</h2>
            <?php } ?>

            <p>Price is {{ $order->price }} {{ $order->primary->code  }} for 1 {{ $order->secondarycode  }}</p>

            <EscrowMaster order_id="{{ $order->id }}" />
        </div>
    </div>
@endsection
