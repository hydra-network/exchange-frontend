
    @extends('guest.layout')

    @section('title', 'Hydra')

    @section('content')
        <div class="flex-center position-ref full-height">
            <div class="content">
                <h1>Hydra</h1>

                <div class="links">
                    <p><a href="/register" class="btn btn-default">Register</a></p>
                    <p><a href="/login"  class="btn btn-success">Enter</a></p>
                </div>
                <div class="title m-b-md">
                    <a href="/login"><img src="/img/logo.png" width="250" /></a>
                </div>
            </div>
        </div>
    @endsection
