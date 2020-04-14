<!doctype html>
<html>
    <head lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <title>piqlConnect</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes">
        <link href="{{ asset('css/vendor.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />

@if (Route::current()->getName() == 'login')
		<script type="text/javascript">
			let timeout = {{env('SESSION_LIFETIME', 120) * 60000 }} - 1000;
			setTimeout(function(){
				location.reload();
			}, timeout );
        </script>
@else
        <script type="text/javascript">Window.locale = '{{\App::getLocale()}}';</script>
        <script type="text/javascript" src="{{ mix('js/vendor.js') }}" defer></script>
        <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
@endif
    </head>

@if (Route::current()->getName() == 'login')
    <body class="loginBody">
        <div id="app" class="container-fluid">
            @yield('content')
        </div>
@else
    <body>
        <div id="app">
            <router-view></router-view>
        </div>
    </div>
@endif
    </body>
</html>
