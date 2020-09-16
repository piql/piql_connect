<!doctype html>
<html>

<head lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <title>piqlConnect</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=yes">
    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />


    <script type="text/javascript">
        Window.locale = '{{\App::getLocale()}}';
    </script>
    <script type="text/javascript" src="{{ mix('js/manifest.js') }}" defer></script>
    <script type="text/javascript" src="{{ mix('js/vendor.js') }}" defer></script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
</head>

<body>
    <div id="app" class="fullHeight"></div>
</body>

</html>