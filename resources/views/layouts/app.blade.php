<!doctype html>
<html>
    <head lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <title>piqlConnect</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes">
        <script type="text/javascript">Window.locale = '{{\App::getLocale()}}';</script>
        <link href="{{ asset('css/vendor.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
        <script type="text/javascript" src="{{ mix('js/vendor.js') }}" defer></script>
        <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
    </head>

@if (Route::current()->getName() == 'login')
    <body class="loginBody">
        <div id="app" class="container-fluid">
            @yield('content')
        </div>
@else
    <body>
        <div id="app" style=" max-height: 70vh;">
            <div class="container-fluid ml-0 mr-0 pl-0 pr-0">
            <div class="row mb-0">
                @section('top')
                    @include('includes.top')
                @show
            </div>
            <div class="row">
                <div id="sidebarWrapper" class="col-sm-1 p-0 m-0 sidebarWrapper">
                    @section('sidebar')
                        @include('includes.sidebar')
                    @show
                </div>
                <div id="mainContent" class="col ml-5 mr-5 mt-4">
                    @hasSection('heading')
                            <h1>@yield('heading')</h1>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endif
    </body>
</html>
