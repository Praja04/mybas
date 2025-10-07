<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head><base href="{{ url('/') }}">
    <meta charset="utf-8" />
    <title>BAS APPS</title>
    <meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--begin::Fonts-->
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
{{--    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />--}}
    <!--end::Fonts-->
    <!--begin::Page Vendors Styles(used by this page)-->
{{-- <link href="{{ url('/') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" /> --}}
<!--end::Page Vendors Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ url('/') }}/assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ url('/') }}/assets/css/style.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="{{ url('/') }}/assets/css/themes/layout/header/base/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/css/themes/layout/header/menu/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ url('/') }}/assets/css/themes/layout/brand/light.css?v=7.0.5" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ url('/') }}/assets/css/themes/layout/aside/dark.css?v=7.0.5" rel="stylesheet" type="text/css" /> --}}
<!--end::Layout Themes-->
    <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/bas_logo.jpg" />
    @stack('styles')
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="header-fixed header-mobile-fixed page-loading">
<!--begin::Main-->
<!--begin::Header Mobile-->
<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed" style="right: 50px">
    <!--begin::Logo-->
    <a href="{{ url('/display ') }}">
        <img alt="Logo" src="{{ url('/') }}/assets/media/logos/bas_logo.jpg" width="140px" />
    </a>
    <!--end::Logo-->
    <!--begin::Toolbar-->
    <!--end::Toolbar-->
</div>
<!--end::Header Mobile-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            <!--begin::Header-->
            <div id="kt_header" class="header header-fixed">
                <!--begin::Container-->
                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Header Menu Wrapper-->
                    <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                        <!--begin::Header Logo-->
                        <div class="header-logo" style="width: 150px">
                            <a href="{{ url('/display') }}">
                                <img alt="Logo" src="{{ url('/') }}/assets/media/logos/bas_logo.jpg" style="width: 100%"/>
                            </a>
                        </div>

                        <!--end::Header Menu-->
                    </div>
                    <div class="topbar">
                        <div class="topbar-item">
                            <h2>
                                @section('title')
                                   DISPLAY PT. BAS
                                @show
                            </h2>
                        </div>
                    </div>
                    <div class="topbar">
                        <!--end::Languages-->
                        <!--begin::User-->
                        <div class="topbar-item">
                            <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
{{--                                <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>--}}
                                <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3 text-right">
                                    <h6 class="logo-title mb-0"><strong>PT. BAS</strong></h6>
                                    <div>
                                        <strong class="date font-weight-normal"></strong>
                                    </div>
                                </span>
                                <span class="symbol symbol-35 symbol-light-danger">
                                    <span class="symbol-label font-size-h5 font-weight-bold" style="width: 100px">
                                        <span class="time"></span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <!--end::User-->
                    </div>
                {{-- @endif --}}
                <!--end::Topbar-->
                </div>
                <!--end::Container-->
            </div>

            <!--end::Header-->
            <!--begin::Content-->
            {{-- Di sini konten --}}
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--end::Subheader-->
                <!--begin::Entry-->
                <div class="d-flex flex-column-fluid">
                    <!--begin::Container-->
                @yield('content')
                <!--end::Container-->
                </div>
                <!--end::Entry-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
        @include('layouts.footer')
        <!--end::Footer-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Main-->
<!--end::Global Config-->
<script>var HOST_URL = "{{ url('/') }}";</script>
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
{{-- <script src="{{ url('/') }}/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5"></script> --}}
{{-- <script src="{{ asset('/assets/js/jquery-1.12.4.min.js') }}"></script> --}}

<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });

    function kasihNol($data) {
        if($data < 10)
        {
            return '0'+$data;
        }else{
            return $data;
        }
    }

    function get_time()
    {
        const today = new Date();
        const time = kasihNol(today.getHours()) + ":" + kasihNol(today.getMinutes()) + ":" + kasihNol(today.getSeconds());
        const date = kasihNol(today.getDate())+'/'+kasihNol((today.getMonth()+1))+'/'+kasihNol(today.getFullYear());
        $('.date').text(date);
        $('.time').text(time);
    }

    get_time();

    setInterval(function () {
        get_time();
    }, 1000);


     @if(Session::has('success'))
  		toastr.success("{{ Session::get('success') }}");
  @endif


  @if(Session::has('info'))
  		toastr.info("{{ Session::get('info') }}");
  @endif


  @if(Session::has('warning'))
  		toastr.warning("{{ Session::get('warning') }}");
  @endif


  @if(Session::has('error'))
  		toastr.error("{{ Session::get('error') }}");
  @endif
</script>

@stack('scripts')

</body>
<!--end::Body-->
</html>
