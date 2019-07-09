<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (!Auth::guest())
        <meta name="api-token" content="{{ Auth::user()->api_token }}">
    @endif

    @stack('styles')
    @yield('head')
    @routes
    <!-- Scripts -->
    @if (Auth::guest())
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
            ]) !!};
        </script>
    @else
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'apiToken' => Auth::user()->api_token
            ]) !!};
        </script>
    @endif
</head>
<body>
    <div class="phone-message">
        <br />
        <img src="/img/logo.png" height="74"/>
        <br /> <br />
        <div class="alert alert-danger">
            <p>Sorry, but Coinmonkey does not support mobile devices yet.</p>
        </div>
    </div>
    <div id="app">
        @if(flash()->message)
            <div class="{{ flash()->class }}">
                {{ flash()->message }}
            </div>
        @endif

        @yield('body')
    </div>
    <footer class="page-footer font-small blue pt-4">
        <div class="footer-copyright text-center py-3">

        </div>
    </footer>
    <!-- Footer -->
    @yield('js')
    @stack('scripts')
    @yield('page-scripts')
</body>
</html>