@extends('app.layout')

@section('title', trans('back.profile'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="content">
                    {!! Form::open(['method' => 'post', 'route' => ['app.profile'], 'files' => true]) !!}
                        <h4 class="title" style="margin: 18px 0 0 0">Profile info</h4>
                        <hr style="margin: 0 0 18px 0" />

                        <div class="row">
                            <div class="col-md-6">
                                {!! Form::openGroup('name', trans('dictionary.name')) !!}
                                {!! Form::text('name', auth()->user()->name) !!}
                                {!! Form::closeGroup() !!}
                            </div>

                            <!--div class="col-md-6">
                                {!! Form::openGroup('email', trans('dictionary.email')) !!}
                                {!! Form::email('email', auth()->user()->email) !!}
                                {!! Form::closeGroup() !!}
                            </div-->
                        </div>

                        <h4 class="title" style="margin: 18px 0 0 0">@lang('auth.change-password')</h4>
                        <hr style="margin: 0 0 18px 0" />

                        <div class="row">
                            <div class="col-md-6">
                                {!! Form::openGroup('password', trans('dictionary.password')) !!}
                                {!! Form::password('password') !!}
                                {!! Form::closeGroup() !!}
                            </div>

                            <div class="col-md-6">
                                {!! Form::openGroup('password_confirmation', trans('auth.password-confirmation')) !!}
                                {!! Form::password('password_confirmation') !!}
                                {!! Form::closeGroup() !!}
                            </div>
                        </div>

                        <button class="btn btn-info btn-fill pull-right">@lang('dictionary.update')</button>
                        <div class="clearfix"></div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="content">
                    <div class="author">
                        <a href="{{ route('app.profile') }}">
                            <h4 class="title">{{ auth()->user()->name }}<br/>
                                <small>{{ auth()->user()->email }}</small>
                            </h4>
                        </a>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>
                            <br/>
                            <p>To Enable Two Factor Authentication on your Account, you need to do following steps</p>
                            <strong>
                                <ol>
                                    <li>Click on Generate Secret Button , To Generate a Unique secret QR code for your profile</li>
                                    <li>Verify the OTP from Google Authenticator Mobile App</li>
                                </ol>
                            </strong>
                            <br/>
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(!count($data['user']->passwordSecurity))
                                <form class="form-horizontal" method="POST" action="{{ route('app.generate2faSecret') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Generate Secret Key to Enable 2FA
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @elseif(!$data['user']->passwordSecurity->google2fa_enable)
                                <strong>1. Scan this barcode with your Google Authenticator App:</strong><br/>
                                <img src="{{$data['google2fa_url'] }}" alt="">
                                <br/><br/>
                                <strong>2.Enter the pin the code to Enable 2FA</strong><br/><br/>
                                <form class="form-horizontal" method="POST" action="{{ route('app.enable2fa') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
                                        <label for="verify-code" class="col-md-4 control-label">Authenticator Code</label>
                                        <div class="col-md-6">
                                            <input id="verify-code" type="password" class="form-control" name="verify-code" required>
                                            @if ($errors->has('verify-code'))
                                                <span class="help-block">
<strong>{{ $errors->first('verify-code') }}</strong>
</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Enable 2FA
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @elseif($data['user']->passwordSecurity->google2fa_enable)
                                <div class="alert alert-success">
                                    2FA is Currently <strong>Enabled</strong> for your account.
                                </div>
                                <p>If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.</p>
                                <form class="form-horizontal" method="POST" action="{{ route('app.disable2fa') }}">
                                    <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                        <label for="change-password" class="col-md-4 control-label">Current Password</label>
                                        <div class="col-md-6">
                                            <input id="current-password" type="password" class="form-control" name="current-password" required>
                                            @if ($errors->has('current-password'))
                                                <span class="help-block">
<strong>{{ $errors->first('current-password') }}</strong>
</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-md-offset-5">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-primary ">Disable 2FA</button>
                                    </div>
                                </form>
                                @endif
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
