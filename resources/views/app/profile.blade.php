@extends('app.layout')

@section('title', trans('back.profile'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="content">
                    {!! Form::open(['method' => 'post', 'route' => ['app.profile'], 'files' => true]) !!}
                        <hr style="margin: 0 0 18px 0" />

                        <div class="row">
                            <div class="col-md-6">
                                Username: {!! Form::text('name', auth()->user()->name) !!}
                            </div>
                        </div>

                        <h4 class="title" style="margin: 18px 0 0 0">@lang('auth.change-password')</h4>
                        <hr style="margin: 0 0 18px 0" />

                        <div class="row">
                            <div class="col-md-6">
                                New password: {!! Form::password('password') !!}
                            </div>

                            <div class="col-md-6">
                                Confirm password: {!! Form::password('password_confirmation') !!}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
