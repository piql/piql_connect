<!doctype html>
<html>
    <head lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <title>piqlConnect</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    </head>
    <body id="{{ Request::path() == 'login' ? 'loginPage' : '' }}">
        <div id="app">
            @section('top')
               @include('includes.top')
            @show
            @section('sidebar')
                @include('includes.sidebar')
            @show
            @yield('content')
        </div>
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
