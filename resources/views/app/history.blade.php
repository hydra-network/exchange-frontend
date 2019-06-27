@extends('app.layout')

@section('title', 'Trade history')

@section('content')
    <table class="table table-hover">
        <tr>
            <th>Market</th>
            <th>Quantity / Remain</th>
            <th>Cost / Remain</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        @if( !$orders->count())
            <tr>
                <td colspan="5">Empty</td>
            </tr>
        @endif
        
        @foreach ($orders as $order)
            <tr>
                <td><a href="{{ route('app.market.pair', ['code' => $order->pair->code]) }}">{{ $order->pair->code }}</a></td>
                <td>{{ $order->quantity }} / {{ $order->quantity_remain }}</td>
                <td>{{ $order->cost }} / {{ $order->cost_remain }}</td>
                <td>{{ $order->created_at }}</td>
                <td>{{ $order->status }}</td>
            </tr>
        @endforeach
    </table>
@endsection