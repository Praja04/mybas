@props([
    'title' => '',
    'menus' => [],
    'shortName' => '',
    'longName' => '',
    'nameIcon' => 'heart',
    'activeMenu' => null,
])

<!DOCTYPE html>
<html lang="id" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="enable">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title != '' ? $title . ' - ' : '' }}My BAS Online</title>
    <meta name="description" content="PT. BUMI ALAM SEGAR Applications Base" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/bas_logo.png" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/velzon/js/layout.js') }}"></script>
    <link href="{{ asset('assets/velzon/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!--datatable css-->
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/select.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/fixedColumns.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .pas-background-color {
            background-color: #a80000 !important;
        }

        .pas-color {
            color: #a80000 !important;
        }

        #preloader {
            background-color: rgba(255, 255, 255, .3)
        }
    </style>

    @livewireStyles

    {{ $styles }}
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <x-templates.velzon.topbar />
        <x-templates.velzon.sidebar activeMenu="{{ $activeMenu }}" nameIcon="{{ $nameIcon }}"
            shortName="{!! $shortName !!}" longName="{!! $longName !!}" menus="{!! $menus !!}" />
        <div class="main-content">
            <div class="page-content">
                {{ $slot }}
            </div>
            <x-templates.velzon.footer />
        </div>
    </div>

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/plugins/global/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/velzon/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/velzon/js/app.js?v=2') }}"></script>

    <!--datatable js-->
    <script src="{{ asset('assets/velzon/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/datatables/js/dataTables.fixedColumns.min.js') }}"></script>

    <script src="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/tilt.jquery.min.js') }}"></script>

    @livewireScripts

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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

        function kasihNol(data) {
            if (data < 10) {
                return '0' + data;
            } else {
                return data;
            }
        }

        function formatTanggalIndonesia(tanggal) {
            const today = new Date(tanggal);
            return kasihNol(today.getDate()) + '/' + kasihNol((today.getMonth() + 1)) + '/' + kasihNol(today.getFullYear());
        }

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
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // If page has return by laravel ->with() function
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                gravity: "top",
                position: 'center',
                backgroundColor: "linear-gradient(to right, rgb(10, 179, 156), rgb(64, 81, 137))",
            }).showToast();
        @endif

        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
                duration: 3000,
                gravity: "top",
                position: 'center',
                backgroundColor: "linear-gradient(135deg,#f06548 0,#3577f1 100%)",
            }).showToast();
        @endif
    </script>

    {{ $scripts }}
</body>

</html>
