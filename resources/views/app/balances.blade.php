@extends('app.layout')

@section('title', trans('back.balances'))

@section('content')
    @foreach ($currencies as $currency)
        <div class="row">
            <div class="col-md-8">
                <h4><a href="{{ route('app.balances.deposit', ['code' => $currency->code]) }}" title="@lang('dictionary.deposit')">{{ $currency->name  }}</a></h4>
                <p>{{ $currency->code  }}</p>
            </div>
            <div class="col-md-4">
                <h4>{{ $currency->userBalance()  }}</h4>

                <a href="{{ route('app.balances.deposit', ['code' => $currency->code]) }}" class="btn btn-success" title="@lang('dictionary.deposit')"><i class="glyphicon glyphicon-log-in"></i></a>
                <a href="{{ route('app.balances.withdrawal', ['code' => $currency->code]) }}" class="btn btn-danger" title="@lang('dictionary.withdrawal')"><i class="glyphicon glyphicon-log-out"></i></a>
            </div>
        </div>
        <hr />
    @endforeach
@endsection