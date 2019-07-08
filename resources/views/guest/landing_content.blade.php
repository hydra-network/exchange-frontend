@extends('guest.landing')

@section('title', 'Hydra DEX')

@section('content')

<header>
  <div id="header" class="carousel slide">
    <div class="container">
      <div class="header-item">
        <div class="inn">
            <br />
            <div class="row register-form">
                <div class="col-md-7 welcome-message" style="padding-top: 48px; text-align: center;">
                    <h1>Hydra DEX</h1>
                    <p>Our first step to the first real decentralized exchange platform</p>
                    <p>Start using Hydra exchange platform right now!</p>

                    <a href="/app/"><img src="/img/hydra.png" width="500px;" /></a>

                    <br />
                    <br />
                    <div><a href="/app/" class="btn btn-success" style="background-color: #960b0b; border-color: #000; font-size: 30px;">Start trading</a></div>
                    
                    <div class="follow">
                    </div>
                </div>
                <div class="col-md-1">
                
                </div>
                <div class="col-md-4" style="background-color: #fff; color: #1e1e1e; padding: 10px; padding-bottom: 100px; border-radius: 4px;">
                    <br />
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    <?php if (!auth()->user()) { ?>
                        <ul class="nav nav-tabs mb-2">
                            <li class=" active">
                                <a class="nav-link " data-toggle="tab" href="#sign-up" role="tab" aria-controls="sign-up" aria-selected="true">Sign up</a>
                            </li>
                            <li class="">
                                <a class="nav-link" data-toggle="tab" href="#sign-in" role="tab" aria-controls="sign-in" aria-selected="false">Sign in</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="sign-up" role="tabpanel" aria-labelledby="sign-up">
                                {!! Form::open(['method' => 'post', 'route' => ['register']]) !!}
                                <div class="card">
                                    <div class=" text-center">
                                        {!! Form::openGroup('name', trans('dictionary.name')) !!}
                                        {!! Form::text('name') !!}
                                        {!! Form::closeGroup() !!}

                                        {!! Form::openGroup('email', trans('dictionary.email')) !!}
                                        {!! Form::email('email') !!}
                                        {!! Form::closeGroup() !!}

                                        {!! Form::openGroup('password', trans('dictionary.password')) !!}
                                        {!! Form::password('password') !!}
                                        {!! Form::closeGroup() !!}

                                        {!! Form::openGroup('password_confirmation', trans('auth.password-confirmation')) !!}
                                        {!! Form::password('password_confirmation') !!}
                                        {!! Form::closeGroup() !!}

                                        <div class="captcha" style="width: 250px;padding-left: 60px;">
                                            <div class="g-recaptcha" style="text-align: center; transform:scale(0.83);-webkit-transform:scale(0.83);transform-origin:0 0;-webkit-transform-origin:0 0;" data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}"></div>
                                        </div>
                                    </div>

                                    <div class=" text-center">
                                        <button class="btn btn-fill btn-default btn-wd">{{ trans('dictionary.register') }}</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="sign-in" role="tabpanel" aria-labelledby="sign-in">
                                {!! Form::open(['method' => 'post', 'route' => ['login']]) !!}
                                <div class="card">
                                    <div class="content-no-padding padding-h-40">
                                        <div style="margin-left: 10px;">
                                            {!! Form::openGroup('email', trans('dictionary.email')) !!}
                                            {!! Form::email('email', null) !!}
                                            {!! Form::closeGroup() !!}

                                            {!! Form::openGroup('password', trans('dictionary.password')) !!}
                                            {!! Form::password('password') !!}
                                            {!! Form::closeGroup() !!}

                                            <div class="form-group">
                                                <label class="checkbox" style="text-align: center;">
                                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} data-toggle="checkbox">
                                                    {{ trans('auth.remember') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer text-center">
                                        <button class="btn btn-fill btn-default btn-wd">{{ trans('dictionary.login') }}</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    <?php } else { ?>
                        <p align="center">Hello, {{ auth()->user()->name }}!. <br /> <br /> <a href="/app" class="btn btn-default">Enter</a></p>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</header>

@endsection