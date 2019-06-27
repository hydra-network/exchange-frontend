@extends('app.layout')

@section('title', trans('dictionary.deposit') . ' ' . $currency->name . ' (' . $currency->userBalance() . ')')

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
            <li class="active"><a href="{{ route('app.balances.deposit', ['code' => $currency->code]) }}" class="btn btn-success" title="@lang('dictionary.deposit')">@lang('dictionary.deposit')</a></li>
            <li><a href="{{ route('app.balances.withdrawal', ['code' => $currency->code]) }}" class="btn btn-success" title="@lang('dictionary.withdrawal')">@lang('dictionary.withdrawal')</a></li>
        </ul>
    </div>

    <div class="panel panel-success" style="min-height: 90vh;">
        <div class="panel-body deposit-container">
            @if (auth()->user()->isActivated())
                <div class="row">
                    <div class="col-md-12"><strong class="pull-right">Min confirmations: {{ $currency->min_confirmations }}</strong></div>
                </div>
                <deposit>
            @else
                <div class="alert alert-danger">
                    <p>Welcome to the Hydra exchange! Please activate your account via e-mail (check out link in the last letter from Hydra).</p>
                    
                    <p><i class="glyphicon glyphicon-info-sign"></i> Also don't forget to connect to our community in <a href="https://discordapp.com/invite/kCyhNUh">Discord</a>.</p>
                </div>
            @endif
        </div>
    </div>
@endsection