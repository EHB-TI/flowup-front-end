<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <style>
        body, html {
            margin: 0;
        }
    </style>
</head>
<body>
    <div id="app">
        <div id="components-layout-demo-basic">
            <a-layout>
            <a-layout-sider style="background: white;" width="81">
                <sidebar></sidebar> 
            </a-layout-sider>
            <a-layout>
                <a-layout-content style="background:white">
                    <div style="padding: 15px;">
                        @yield('content')
                    </div> 
                </a-layout-content>
            </a-layout>
            </a-layout>
        </div>
    </div>
</body>
</html>
