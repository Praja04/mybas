<!DOCTYPE html>
<html>

<head>
    @include('hr.cateringbas.pengambilan-beras.includes.meta')
    <title>@yield('title') | dashboard kedatangan beras</title>

    {{-- favicon --}}
    <link rel="apple-touch-icon" href="">
    <link rel="shortcut icon" href="image/x-icon" href="">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    @stack('before-style')
    {{-- style --}}
    @include('hr.cateringbas.pengambilan-beras.includes.style')

    @stack('after-style')
</head>

<body>
    <div id="app">
        @include('hr.cateringbas.pengambilan-beras.includes.sidebar')
        @include('hr.cateringbas.pengambilan-beras.includes.spinner')
        @yield('content')
        @include('hr.cateringbas.pengambilan-beras.includes.footer')
        @stack('before-script')
        {{-- script --}}
        @include('hr.cateringbas.pengambilan-beras.includes.script')
        @stack('after-script')
    </div>
</body>

</html>
