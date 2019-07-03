@extends('app.layout')

@section('title', 'Trade markets')

@section('content')
        @foreach ($primaryCurrencies as $primary)
            <h4>{{$primary->name}} markets</h4>

            <table class="table table-hover">
                <tr>
                    <th width="30%">Asset</th>
                    <th width="10%">Last price</th>
                    <th width="10%">Bid</th>
                    <th width="10%">Ask</th>
                    <th width="10%">24 Volume</th>
                    <th>Actions</th>
                </tr>
                @foreach ($pairs as $pair)
                    @php
                        $primaryAsset = $pair->primary;
                        $secondaryAsset = $pair->secondary;
                    @endphp

                    @if ($primaryAsset->id == $primary->id)
                        <tr>
                            <td>
                                <a href="{{ route('app.market.pair', ['code' => $pair->code]) }}"><strong>{{ $secondaryAsset->name }}</strong></a>
                            </td>
                            <td>{{ $pair->getLastPrice() }}</td>
                            <td>{{ $pair->getBidPrice() }}</td>
                            <td>{{ $pair->getAskPrice() }}</td>
                            <td>{{ $pair->getDailyVolume() }} {{$primaryAsset->code}}</td>
                            <td>
                                <a href="{{ route('app.market.pair', ['code' => $pair->code]) }}" class="btn btn-default">Trade</a>

                                <a href="{{ route('app.balances.deposit', ['code' => $secondaryAsset->code]) }}" class="btn btn-success" title="@lang('dictionary.deposit')"><i class="glyphicon glyphicon-log-in"></i></a>
                                <a href="{{ route('app.balances.withdrawal', ['code' => $secondaryAsset->code]) }}" class="btn btn-danger" title="@lang('dictionary.withdrawal')"><i class="glyphicon glyphicon-log-out"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        @endforeach
@endsection