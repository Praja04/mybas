<!DOCTYPE html>
<html lang="en">

<head>
    <base href="{{ url('/') }}">
    <meta charset="utf-8" />
    <title>My BAS Online</title>
    <meta name="description" content="PT. Bumi Alam Segar Applications Base" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
    <link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css?v=7.0.5" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('/') }}/assets/css/style.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/css/themes/layout/header/base/light.css?v=7.0.5" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('/') }}/assets/css/themes/layout/brand/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/css/themes/layout/aside/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/bas_logo.jpg" />
    <style type="text/css">
        .hide {
            display: none;
        }

        .form-label-group-select select {
            padding-left: .5rem;
            padding-top: 1.25rem;
            padding-bottom: .25rem;
            height: 100%;
        }

        .form-label-group-select .select2-selection__rendered {
            padding-bottom: .25rem !important;
            padding-left: .75rem !important;
            padding-top: 1.7rem !important;
        }

        .form-label-group-select {
            position: relative;
        }

        .form-label-group-select label {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            margin-bottom: 0;
            /* Override default `<label>` margin */
            line-height: 1.5;
            /* color: #495057; */
            pointer-events: none;
            cursor: text;
            /* Match the input under the label */
            border: 1px solid transparent;
            border-radius: .25rem;
            transition: all .1s ease-in-out;
            height: 3.125rem;
            padding: .75rem;
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #999 !important;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .form-label-group input,
        .form-label-group label {
            height: 3.125rem;
            padding: .75rem;
        }

        .form-label-group label {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            margin-bottom: 0;
            /* Override default `<label>` margin */
            line-height: 1.5;
            color: #495057;
            pointer-events: none;
            cursor: text;
            /* Match the input under the label */
            border: 1px solid transparent;
            border-radius: .25rem;
            transition: all .1s ease-in-out;
        }

        .form-label-group input::-webkit-input-placeholder {
            color: transparent;
        }

        .form-label-group input::-moz-placeholder {
            color: transparent;
        }

        .form-label-group input:-ms-input-placeholder {
            color: transparent;
        }

        .form-label-group input::-ms-input-placeholder {
            color: transparent;
        }

        .form-label-group input::placeholder {
            color: transparent;
        }

        .form-label-group input:not(:-moz-placeholder-shown) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:-ms-input-placeholder) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:placeholder-shown) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:-moz-placeholder-shown)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:not(:-ms-input-placeholder)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:not(:placeholder-shown)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:-webkit-autofill~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        .loading-overlay {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.is-active {
            display: flex;
        }

        .code {
            font-family: monospace;
            color: #dd4a68;
            background-color: rgb(238, 238, 238);
            padding: 0 3px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/loading.css') }}">
    @stack('styles')
</head>

<body id="kt_body"
    class="header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading aside-minimize">
    @include('sweetalert::alert')

    <div id="kt_header_mobile " class="header-mobile align-items-center header-mobile-fixed no-print">
        <div class="d-flex align-items-center">
            <button class="btn p-0" id="kt_aside_mobile_toggle"><i class="fas fa-angle-double-right"></i>
                <span></span>
            </button>
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle"><i
                    class="fas fa-angle-down"></i>
            </button>
        </div>
    </div>
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
                <div class="brand flex-column-auto" id="kt_brand">
                    @if (in_array('cycle_count', $permissions))
                        <img alt="Logo" src="{{ url('/') }}/assets/media/cartoon/cycle-count/cycle-count.png"
                            style="width: 70%" />
                    @else
                        @yield('logo')
                    @endif
                    <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                        <span class="svg-icon svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                                        fill="#000000" fill-rule="nonzero"
                                        transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
                                    <path
                                        d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3"
                                        transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
                    <div id="kt_aside_menu" class="aside-menu mb-4" data-menu-vertical="1" data-menu-scroll="1"
                        data-menu-dropdown-timeout="500">
                        <ul class="menu-nav">
                            <li class="menu-section mt-0">
                                <h4 class="menu-text">Hello, {{ Auth::user()->name }}</h4>
                                <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                            </li>
                            @if (Request::is('edoc') or Request::is('edoc/*'))
                                <li class="menu-section mt-0">
                                    <h4 class="menu-text">E-DOC LOG</h4>
                                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                                </li>
                                @if (in_array('security', $permissions))
                                    <li class="menu-item {{ request()->is('edoc') ? 'menu-item-active' : '' }}"
                                        aria-haspopup="true">
                                        <a class="menu-link" href="{{ url('edoc') }}">
                                            <i class="fas fa-home menu-icon"></i>
                                            <span class="menu-text">Main Menu</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="menu-item {{ request()->is('edoc/history') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a class="menu-link" href="{{ url('edoc/history?filter=1') }}">
                                        <i class="fas fa-history menu-icon"></i>
                                        <span class="menu-text">History E-doc</span>
                                    </a>
                                </li>
                                @if (in_array('edoc_master_pic', $permissions))
                                    <li class="menu-item {{ request()->is('edoc/masterpic') ? 'menu-item-active' : '' }}"
                                        aria-haspopup="true">
                                        <a class="menu-link" href="{{ url('edoc/masterpic') }}">
                                            <i class="fas fa-database menu-icon"></i>
                                            <span class="menu-text">Master PIC</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="menu-item {{ request()->is('edoc/pengiriman') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a class="menu-link" href="{{ url('edoc/pengiriman') }}">
                                        <i class="fas fa-truck menu-icon"></i>
                                        <span class="menu-text">Pengiriman Barang</span>
                                    </a>
                                </li>
                            @endif
                            <li class="menu-section">
                                <h4 class="menu-text">------------------------------</h4>
                                <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                            </li>

                            <li class="menu-item mt-3" aria-haspopup="true">
                                <a href="{{ url('/') }}" class="menu-link">
                                    <i class="las la-arrow-left menu-icon"></i>
                                    <span class="menu-text">Go Home</span>
                                </a>
                            </li>
                            <li class="menu-item mt-3" aria-haspopup="true">
                                <a class="menu-link logout">
                                    <i class="la la-power-off menu-icon"></i>
                                    <span class="menu-text">Logout</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <div id="kt_header" class="header header-fixed">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="header-menu-wrapper header-menu-wrapper-left d-flex" id="kt_header_menu_wrapper">
                            <div class="header-logo" style="width: 60px">
                                <img alt="Logo" class="mt-4"
                                    src="{{ url('/') }}/assets/media/logos/bas_logo.jpg" style="width: 60%" />
                            </div>
                        </div>
                        <div class="topbar">
                            <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                                <span
                                    class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3 text-right">
                                    <h6 class="logo-title mb-0 mt-4"><strong>PT. BAS</strong></h6>
                                    <div>
                                        <strong class="date font-weight-normal"></strong>
                                    </div>
                                </span>
                                <span class="symbol symbol-35 symbol-light-danger mt-4 no-print">
                                    <span class="symbol-label font-size-h5 font-weight-bold" style="width: 100px">
                                        <span class="time "></span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content d-flex flex-column flex-colusssmn-fluid" id="kt_content">
                    <div class="loading-overlay justify-content-center">
                        <div class="drawing">
                            <div class="loading-dot"></div>
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        var HOST_URL = "{{ url('/') }}";
    </script>
    <script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
    <script src="{{ url('/') }}/assets/js/scripts.bundle.js?v=7.0.5"></script>
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ajaxSend(function(event, jqxhr, settings) {
            // Start pace
            $('.loading-overlay').addClass('d-flex');
            // $('#clock-loader').fadeIn('normal');
        });

        $(document).ajaxComplete(function(event, jqxhr, settings) {
            // Stop pace
            $('.loading-overlay').removeClass('d-flex');
            // $('#clock-loader').fadeOut('normal')
        });

        $(function() {
            $('body').on('click', '.menu-item', function() {
                $('.menu-item').removeClass('active');
                $(this).closest('.menu-item').addClass('active');
            });
        });

        function initDropZone(id, transactionId, transaction_type) {
            // var id = '#dropzone';

            // set the preview element template
            var previewNode = $(id + " .dropzone-item");
            previewNode.id = "";
            var previewTemplate = previewNode.parent('.dropzone-items').html();
            previewNode.remove();

            var myDropzone5 = new Dropzone(id, {
                url: "{{ url('/local-attachment/upload') }}",
                parallelUploads: 20,
                maxFilesize: 10,
                previewTemplate: previewTemplate,
                previewsContainer: id + " .dropzone-items",
                clickable: id + " .dropzone-select",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                // resizeWidth: 1000,
                // resizeQuality: 0.4
            });

            myDropzone5.on("sending", function(file, xhr, formData) {
                formData.append('transaction_id', $(transactionId).val());
                formData.append('transaction_type', transaction_type);
            });

            myDropzone5.on("addedfile", function(file) {
                // Hookup the start button
                $(document).find(id + ' .dropzone-item').css('display', '');
            });

            // Update the total progress bar
            myDropzone5.on("totaluploadprogress", function(progress) {
                $(id + " .progress-bar").css('width', progress + "%");
            });

            myDropzone5.on("sending", function(file) {
                // Show the total progress bar when upload starts
                $(id + " .progress-bar").css('opacity', "1");
            });

            myDropzone5.on("error", function(file, errorMessage) {
                alert(JSON.stringify(errorMessage.message))
            })

            // Hide the total progress bar when nothing's uploading anymore
            myDropzone5.on("complete", function(progress) {
                var thisProgressBar = id + " .dz-complete";
                setTimeout(function() {
                    $(thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress").css('opacity',
                        '0');
                }, 300)
            });

            return myDropzone5;
        }

        $(".logout").click(function() {
            $.ajax({
                url: "{{ URL::to('/') }}/logout",
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    if (response.success == 1) {
                        location.reload();
                        return false;
                    }
                },
                error: function(error) {
                    location.reload();
                    console.log(error);
                }
            });
        });


        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif


        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif


        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif


        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        function kasihNol($data) {
            if ($data < 10) {
                return '0' + $data;
            } else {
                return $data;
            }
        }

        function get_time() {
            const today = new Date();
            const time = kasihNol(today.getHours()) + ":" + kasihNol(today.getMinutes()) + ":" + kasihNol(today
                .getSeconds());
            const date = kasihNol(today.getDate()) + '/' + kasihNol((today.getMonth() + 1)) + '/' + kasihNol(today
                .getFullYear());
            $('.date').text(date);
            $('.time').text(time);
        }

        get_time();

        setInterval(function() {
            get_time();
        }, 1000);

        function formatTanggalIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

        function formatTanggalWaktuIndonesia2(tanggal) {
            var formated;
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear()) +
                ' ' + kasihNol(today.getHours()) + ':' + kasihNol(today.getMinutes()) + ':' + kasihNol(today.getSeconds());

            if (tanggal == null || tanggal == '') {
                formated = '';
            }

            return formated;
        }

        $(".datepicker-year").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        $(".datepicker-month").datepicker({
            format: "mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        function alert_outstanding() {
            $('.Outstanding').fadeOut(700);
            $('.Outstanding').fadeIn(700);
        }
        setInterval(alert_outstanding, 2000);
        $(".logout").click(function() {
            $.ajax({
                url: "{{ URL::to('/') }}/logout",
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    if (response.success == 1) {
                        //   location.reload();
                        location.href = "{{ url('/') }}";
                    }
                },
                error: function(error) {
                    location.reload();
                    console.log(error);
                }
            });
        });
    </script>

    @stack('scripts')

    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>
