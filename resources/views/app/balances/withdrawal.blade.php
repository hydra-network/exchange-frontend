@extends('app.layout')

@section('title', trans('dictionary.withdrawal') . ' ' . $currency->name . ' (' . $currency->userBalance() . ')')

@section('js')
    <script>
        var hydra = {
            deposit: {
                currency: '{{ $currency->code  }}'
            },
        }
    </script>
@endsection

@section('content')
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs ui-sortable-handle">
            <li><a href="{{ route('app.balances.deposit', ['code' => $currency->code]) }}" class="btn btn-success" title="@lang('dictionary.deposit')">@lang('dictionary.deposit')</a></li>
            <li class="active"><a href="{{ route('app.balances.withdrawal', ['code' => $currency->code]) }}" class="btn btn-success" title="@lang('dictionary.withdrawal')">@lang('dictionary.withdrawal')</a></li>
        </ul>
    </div>

    <div class="panel panel-danger" style="min-height: 90vh;">
        <div class="panel-body withdrawal-container">
            <div class="row">
                <div class="col-md-12">
                    <p class="pull-right"><small><strong >Withdrawal fee: {{ $currency->withdrawal_fees }}</strong><br /><strong>Min amount: {{ $currency->min_withdrawal_amount }}</strong><br /><strong>Delay time: 24h</strong></small></p>
                </div>
            </div>
            <div><withdrawal></div>
            <div class="row" style="display: none;">
                <div class="col-md-12">
                    <p class="pull-right">
                        <small>
                        Statuses:<br />
1: new<br />
4: authorized;<br />
2: done<br />
3: fail (please, contact with support <a href="mailto:support@coinmonkey.io">support@coinmonkey.io</a>)
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection