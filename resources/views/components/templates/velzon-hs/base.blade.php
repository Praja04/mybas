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
    <meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/bas_logo.jpg') }}" />

    <script src="{{ asset('assets/velzon/js/layout.js') }}"></script>
    <link href="{{ asset('assets/velzon/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

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
    <div id="layout-wrapper">
        <x-templates.velzon-hs.topbar />
        <x-templates.velzon-hs.sidebar activeMenu="{{ $activeMenu }}" nameIcon="{{ $nameIcon }}"
            shortName="{!! $shortName !!}" longName="{!! $longName !!}" menus="{!! $menus !!}" />
        <div class="main-content">
            <div class="page-content">
                {{ $slot }}
            </div>
            <x-templates.velzon-hs.footer />
        </div>
    </div>

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

    <script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/moment/locale/id.js') }}"></script>

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
        // 1. SETUP CSRF TOKEN GLOBAL (Cukup 1x aja)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 2. SETUP MOMENT LOCALE GLOBAL
        moment.locale('id');

        // 3. LOGOUT ACTION (Lebih clean dan aman dari page jump)
        $(".logout").click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ url('/logout') }}",
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.error("Logout Error:", error);
                    location.reload();
                }
            });
        });

        // 4. HELPER FUNCTIONS (Dipertahankan karena mungkin dipakai di fitur lama)
        function kasihNol(data) {
            return data < 10 ? '0' + data : data;
        }

        function formatTanggalIndonesia(tanggal) {
            const today = new Date(tanggal);
            return kasihNol(today.getDate()) + '/' + kasihNol((today.getMonth() + 1)) + '/' + today.getFullYear();
        }

        function formatTanggalIndonesia2(tanggal) {
            if (!tanggal) return '';
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            return kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + today.getFullYear();
        }

        function formatTanggalWaktuIndonesia2(tanggal) {
            if (!tanggal) return '';
            const today = new Date(tanggal);
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            return kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + today.getFullYear() + ' ' + kasihNol(
                today.getHours()) + ':' + kasihNol(today.getMinutes()) + ':' + kasihNol(today.getSeconds());
        }

        // 5. GLOBAL SESSION ALERTS (TOASTIFY)
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                gravity: "top",
                position: 'right', // Diubah ke kanan atas biar kayak notif modern
                backgroundColor: "#0ab39c", // Warna hijau sukses khas Velzon
            }).showToast();
        @endif

        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
                duration: 3000,
                gravity: "top",
                position: 'right',
                backgroundColor: "#f06548", // Warna merah error khas Velzon
            }).showToast();
        @endif
    </script>

    {{ $scripts }}
</body>

</html>
