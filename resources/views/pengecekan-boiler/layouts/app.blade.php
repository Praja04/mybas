<!DOCTYPE html>
<html>

<head>
    
    @include('pengecekan-boiler.includes.meta')
    <title>@yield('title') | dashboard monitoring boiler</title>

    {{-- favicon --}}
    <link rel="apple-touch-icon" href="">
    <link rel="shortcut icon" href="image/x-icon" href="">

    @stack('before-style')
    {{-- style --}}
    @include('pengecekan-boiler.includes.style')
    @stack('after-style')
</head>

<body>
    <div id="app">
        @include('pengecekan-boiler.includes.sidebar')
        @include('pengecekan-boiler.includes.spinner')
        @yield('content')
        @include('pengecekan-boiler.includes.footer')

        @stack('before-script')
        {{-- script --}}
        @include('pengecekan-boiler.includes.script')
        @stack('after-script')
        @include('pengecekan-boiler.components.linechart')
        @include('pengecekan-boiler.components.speedmeter')
    </div>
</body>

</html>
