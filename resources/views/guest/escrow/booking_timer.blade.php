
@extends('guest.layout')

@section('title', 'Hydra')

@section('content')
    <div class="flex-center position-ref full-height" id="app">
        <div class="content">
            <div class="title m-b-md">
                <a href="/app"><img src="/img/logo.png" width="250" /></a>
            </div>

            <h1>{{ $order->quantity_remain  }} {{ $order->secondaryname  }} </h1>
            <h1><span style="color: red">reserved for</span></h1>

            <h2> {{ $timer  }} minutes </h2>

            <p>Price is {{ $order->price }} {{ $order->primary->code  }} for 1 {{ $order->secondarycode  }}</p>

            <p>Sorry, but this order has been reserved by another person. If he will not send his assets within {{ $timer  }} minutes, this order will be put up for sale again.</p>
        </div>
    </div>
@endsection
