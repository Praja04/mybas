<!DOCTYPE html>
<html>

<head>
    @include('includes.meta')
    <title>@yield('title') | dashboard monitoring boiler</title>

    {{-- favicon --}}
    <link rel="apple-touch-icon" href="">
    <link rel="shortcut icon" href="image/x-icon" href="">
    <style>
        body {
            background-image: url('/assets/media/bg/sc-home1-bg.png');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            width: 100%;
        }

        body::before {
            content: "";
            background: rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
    @stack('before-style')
    {{-- style --}}
    @include('includes.style')

    @stack('after-style')
</head>

<body>
    <div id="app">
        <div class="content-container" style="padding-top: 120px;">
            @yield('content')
        </div>
        @stack('before-script')
        {{-- script --}}
        @include('includes.script')

        @stack('after-script')
    </div>
</body>

</html>
