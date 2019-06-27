@extends('app.theme')

@section('body')
    <div class="wrapper">
        @include('app.layout.sidebar')

        <div class="main-panel">
            @include('app.layout.header')

            <div class="content">
                <div class="container-fluid">
                    @include('flash::message')

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{ mix('js/app.js') }}"></script>
@endpush