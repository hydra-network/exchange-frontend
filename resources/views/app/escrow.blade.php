@extends('app.layout')
@section('title', 'Escrow service')

@section('content')
    <div class="alert alert-warning">
        <p>Here is your all your offers from all <a href="{{ route('app.dashboard') }}">markets</a>. You can sell it directly to seller, just give him the link. Seller will able to buy it without registration.</p>
        <p><i clas="glyphicon glyphicon-info-sign"></i> Please note, if you got the link, your order will be disabled from the common orderbook. You always able to return the previous status.</p>
    </div>

    <escrow></escrow>
@endsection