<!doctype html>
<html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <title>piqlConnect</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/app.css') }} " rel="stylesheet" />
    </head>
    <body id="{{ Request::path() == 'login' ? 'loginPage' : '' }}">
        <div class="container">
        @section('sidebar')
            @include('includes.sidebar')
        @show
        @yield('content')
        </div>

        </div>
    </body>
</html>
