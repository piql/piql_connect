<!doctype html>
<html>
    <head lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <title>piqlConnect</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript">Window.locale = '{{\App::getLocale()}}';</script>
        <link href="{{ asset('css/vendor.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
        <script type="text/javascript" src="{{ mix('js/vendor.js') }}" defer></script>
        <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
    </head>

@if (Route::current()->getName() == 'login')
    <body class="loginBody">
        <div id="app">
            @yield('content')
        </div>
@else
    <body>
        <div id="app">
            @section('top')
                @include('includes.top')
            @show
            @section('sidebar')
                @include('includes.sidebar')
            @show
            <div class="contentContainer">
            @hasSection('heading')
                    <h1>@yield('heading')</h1>
            @endif
            @yield('content')
            </div>
        </div>
@endif
    </body>
</html>
