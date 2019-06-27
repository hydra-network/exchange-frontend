@extends('app.layout')

@section('title', 'Trade markets')

@section('content')
    @include('flash::message')

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs ui-sortable-handle">
            <li class="active"><a href="#btc-markets" class="btn btn-success">Bitcoin</a></li>
            <li><a href="#bmk-markets" class="btn btn-default" onclick="alert('Coming soon'); return false;">Ether</a></li>
            <li><a href="#bmk-markets" class="btn btn-default" style="opacity: 0.8;" onclick="alert('Coming soon'); return false;">USDT</a></li>
            <li><a href="#bmk-markets" class="btn btn-default" style="opacity: 0.8;" onclick="alert('Coming soon'); return false;">🐵 BitMonk</a></li>
        </ul>
    </div>

    <div class="tab-content" id="marketsTabContent">
        <div class="tab-pane active" id="btc-markets" role="tabpanel" aria-labelledby="btc-markets">
            <table class="table">
                <tr>
                    <th>Asset</th>
                    <th>Market</th>
                    <th>Last price</th>
                    <th>Bid</th>
                    <th>Ask</th>
                    <th>24 Volume</th>
                    <th>Actions</th>
                </tr>
                @foreach ($pairs as $pair)
                    @php
                        $primary_asset = $pair->primary;
                        $secondary_asset = $pair->secondary;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('app.market.pair', ['code' => $pair->code]) }}"><strong>{{ $secondary_asset->name }}</strong></a>
                        </td>
                        <td>
                            <a href="{{ route('app.market.pair', ['code' => $pair->code]) }}"> {{ $primary_asset->code }}-{{ $secondary_asset->code }}</a>
                        </td>
                        <td>{{ $pair->getLastPrice() }}</td>
                        <td>{{ $pair->getBidPrice() }}</td>
                        <td>{{ $pair->getAskPrice() }}</td>
                        <td>{{ $pair->getDailyVolume() }}</td>
                        <td>
                            <a href="{{ route('app.market.pair', ['code' => $pair->code]) }}" class="btn btn-default">Trade</a>

                            <a href="{{ route('app.balances.deposit', ['code' => $secondary_asset->code]) }}" class="btn btn-success" title="@lang('dictionary.deposit')"><i class="glyphicon glyphicon-log-in"></i></a>
                            <a href="{{ route('app.balances.withdrawal', ['code' => $secondary_asset->code]) }}" class="btn btn-danger" title="@lang('dictionary.withdrawal')"><i class="glyphicon glyphicon-log-out"></i></a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="tab-pane" id="bmk-markets" role="tabpanel" aria-labelledby="btc-markets">
            <p>Mainnet coming soon.</p>
        </div>
    </div>
@endsection