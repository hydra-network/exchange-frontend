@extends('app.layout')

@section('title', $primary_asset->code . '/' . $secondary_asset->code . " trade")

@section('head')

@endsection

@section('js')
    <script>
        window.hydra = {
            market: {
                auth: {{(auth()->user()) ? 'true' : 'false'}},
                pair: {!! json_encode($pair->toArray()) !!},
                primary_asset: {!! json_encode($primary_asset->toArray()) !!},
                secondary_asset: {!! json_encode($secondary_asset->toArray()) !!},
                primary_asset_balance: '{{ $primary_asset->userBalance() }}',
                secondary_asset_balance: '{{ $secondary_asset->userBalance() }}',
                primary_asset_unc_balance: '{{ $primary_asset->userUnconfirmedBalance() }}',
                secondary_asset_unc_balance: '{{ $secondary_asset->userUnconfirmedBalance() }}',
                primary_asset_io_balance: '{{ $pair->userInOrdersPrimaryBalance() }}',
                secondary_asset_io_balance: '{{ $pair->userInOrdersSecondaryBalance() }}',
                primary_asset_volume: '{{ $primary_asset->format($pair->getSizes()['bid']) }}',
                secondary_asset_volume: '{{ $secondary_asset->format($pair->getSizes()['ask']) }}',
                user_id: {{(auth()->user()) ? auth()->user()->id : 0 }},
                chart_period: {{ ($period = session('chart_period')) ? (int) $period : 60 }}
            },
            auth_token: '{{(auth()->user() ? JWTAuth::fromUser(auth()->user()) : '')}}'
        }
    </script>
@endsection

@section('content')
            <trade>
    <div style="clear: both;"></div>
@endsection