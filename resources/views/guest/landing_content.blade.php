@extends('guest.landing')

@section('title', 'Coinmonkey 2.0')

@section('content')

<header>
  <div id="header" class="carousel slide">
    <div class="container">
      <div class="header-item">
        <div class="inn">
            <div class="row">
                <div class="col-sm-3 logo-cont"><a href="/"><img src="/img/logored.png" style="padding-top: 20px;" title="Hydra DEX" /></a></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row register-form">
                <div class="col-md-7 welcome-message" style="padding-top: 48px;">
                    <h1 style="font-size: 55px;">Our first step to real decentralized exchange platform</h1>
                    <h3 style="color: #ccc;">Start using Hydra exchange platform to buy or sell coins that you can't find anywhere!</h3>
                    <br />
                    <div style="text-align: center"><a href="/app/" class="btn btn-success" style="background-color: #e40045; border-color: #fff; font-size: 30px;">Start trading</a></div>
                    
                    <div class="follow">
                    </div>
                </div>
                <div class="col-md-1">
                
                </div>
                <div class="col-md-4" style="background-color: #fff; color: #1e1e1e; padding: 10px; border-radius: 4px;">
                    <?php if (!auth()->user()) { ?>
                        <script src='https://www.google.com/recaptcha/api.js'></script>

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
                                    <label>{!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}</label>

                                    <label>{!! Form::email('email', null, ['placeholder' => 'your@email.com', 'class' => 'form-control']) !!}</label>

                                    <label>{!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}</label>

                                    <label>{!! Form::password('password_confirmation', ['placeholder' => 'Password confirmation', 'class' => 'form-control']) !!}</label>
                                    
                                    <div class="captcha" style="width: 250px;padding-left: 60px;">
                                        <div class="g-recaptcha" style="text-align: center; transform:scale(0.83);-webkit-transform:scale(0.83);transform-origin:0 0;-webkit-transform-origin:0 0;" data-sitekey="6LcV5WcUAAAAALXLGRJ4mkYLNB14nmEEM9fMCw8j"></div>
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
                                            <label>{!! Form::email('email', null, ['placeholder' => 'E-mail', 'class' => 'form-control']) !!}</label>

                                            <label>{!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}</label>

                                                <label class="checkbox">
                                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} data-toggle="checkbox">
                                                    {{ trans('auth.remember') }}
                                                </label>
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

<!-- Page Content -->
<div class="container-fluid2" style="">
    <section class="for-coins" style="padding-top: 70px; padding-bottom: 70px; min-height: 300px; background-image: radial-gradient(rgb(102, 157, 240) 0%, rgb(10, 18, 64) 100%); color: #FFF;">
        <div class="container">
            <div class="row">
                <div class="col-md-5 text-right">
                    <h1>For coins founders</h1>
                </div>
                <div class="col-md-2 text-center">
                    <i class="glyphicon glyphicon-arrow-right" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-5  text-left">
                    <a href="mailto:admin@coinmonkey.io" class="btn btn-success" style="background-color: #e40045; border-color: #fff; font-size: 30px;">Get listed on Hydra</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center" style="padding-top: 80px;">
                    <i class="fa fa-rocket" style="color:#fff; font-size: 101px;"></i>
                    
                    <h3>Special conditions for the <span style="font-size: 120%; font-weight: bolder;">first 10 coins</span></h3>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.container -->

@endsection