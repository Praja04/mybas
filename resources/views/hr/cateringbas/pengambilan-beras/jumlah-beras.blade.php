@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        #pengirimCatering th {
            font-size: 14px;
        }

        /* style date */
        input[type="date"] {
            display: block;
            position: relative;
            padding: 1rem 3.5rem 1rem 0.75rem;

            font-size: 1rem;
            font-family: monospace;

            border: 1px solid #8292a2;
            border-radius: 0.25rem;
            background:
                white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='22' viewBox='0 0 20 22'%3E%3Cg fill='none' fill-rule='evenodd' stroke='%23688EBB' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' transform='translate(1 1)'%3E%3Crect width='18' height='18' y='2' rx='2'/%3E%3Cpath d='M13 0L13 4M5 0L5 4M0 8L18 8'/%3E%3C/g%3E%3C/svg%3E") right 1rem center no-repeat;

            cursor: pointer;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #3acfff;
            box-shadow: 0 0 0 0.25rem rgba(0, 120, 250, 0.1);
        }

        ::-webkit-datetime-edit {}

        ::-webkit-datetime-edit-fields-wrapper {}

        ::-webkit-datetime-edit-month-field:hover,
        ::-webkit-datetime-edit-day-field:hover,
        ::-webkit-datetime-edit-year-field:hover {
            background: rgba(0, 120, 250, 0.1);
        }

        ::-webkit-datetime-edit-text {
            opacity: 0;
        }

        ::-webkit-clear-button,
        ::-webkit-inner-spin-button {
            display: none;
        }

        ::-webkit-calendar-picker-indicator {
            position: absolute;
            width: 2.5rem;
            height: 100%;
            top: 0;
            right: 0;
            bottom: 0;

            opacity: 0;
            cursor: pointer;

            color: rgba(0, 120, 250, 1);
            background: rgba(0, 120, 250, 1);

        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            opacity: 0.05;
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator:hover {
            opacity: 0.15;
        }

        .custom-date-input {
            width: 300px;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
        }

        /* Style the search icon inside the search input */
        .dataTables_wrapper .dataTables_filter input::after {
            content: "\f002";
            font-family: FontAwesome;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* end style date */


        /* loading */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* end loading */

        /* styling image */
        @media (max-width: 767px) {
            .karung-image {
                display: none;
            }

            .image-content {
                display: none;
            }
        }

        .karung-image {
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
        }

        .karung-image:hover {
            transform: scale(1.1);
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        #pengirimCatering {
            border-collapse: collapse;
            width: 100%;
        }

        #pengirimCatering th,
        #pengirimCatering td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #pengirimCatering th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4c57af;
            color: white;
        }

        /* media responsive */
        @media (max-width: 767px) {
        #highlight-beras {
            position: relative;
            left: 0 !important; 
            display: block !important; 
            margin-top: 10px; 
            }
        }

        /* hover input */
        input:focus, input:hover {
            outline: none;
            border-color: rgba(76, 79, 234, 0.4);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgb(234 76 137 / 10%);
        }
            
        /* end styling image */
    </style>
@endpush

@section('content')
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        {{-- <div class="page-heading">
            <h3>Stock Beras</h3>
        </div> --}}
        <div class="page-content">
            <div class="row">
                {{-- <div class="col-lg-3 image-content"> --}}
                {{-- <div class="card card-custom">
                        <div class="justify-content-center container p-4">
                            <div class="card-body"> --}}
                {{-- <div class="justify-content-center container p-4">
                                    <img class="karung-image"
                                        src="{{ asset('assets/mazer/dist/assets/compiled/png/karung-beras.png') }}"
                                        alt="Karung Beras" width="100%">
                                    <div class="text-center container p-4 keterangan">
                                        <h3 class="badge bg-secondary" style="font-size: 24px;">
                                            @if ($jumlahStock->isNotEmpty())
                                                {{ $jumlahStock->last()->jumlah_stock }}
                                            @else
                                                belum ada data
                                            @endif
                                        </h3>
        
                                        <h3 class="badge bg-secondary" style="font-size: 24px;">
                                            @if ($jumlahStock->isNotEmpty())
                                                {{ $jumlahStock->last()->satuan_berat }}
                                            @else
                                                belum ada data
                                            @endif
                                        </h3>
        
                                    </div>
                                </div> --}}
                {{-- <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div> --}}
                {{-- </div> --}}
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header flex-wrap border-0 pt-6 pb-0">
                            <div class="card-title pt-2 pb-2">
                                <h3 class="card-label">Stock Gudang<span style="color: red"> Beras</span></h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                            {{-- <form action="{{ route('kedatangan-beras.jumlah.beras') }}" method="GET">
                                <div class="form-group">
                                    <label for="datepicker">Cari Berdasarkan Tanggal:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="datepicker" name="search"
                                            placeholder="Select date" value="{{ request()->search }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Search</button>
                                </div>
                            </form> --}}
                            <div class="container-tanggal-highlight d-flex gap-20">
                                <div class="row"> 
                                    <div class="col-md-4"> 
                                        <div class="form-group">
                                            <label for="minDate">Cari Dari Tanggal:</label>
                                            <input type="date" id="tanggal_awal" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="maxDate">Sampai Dengan:</label>
                                            <input type="date" id="tanggal_akhir" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div id="highlight-beras" style="position: relative; left: 200px;">
                                                <img class="karung-image"
                                                    src="{{ asset('assets/mazer/dist/assets/compiled/png/karung-beras.png') }}"
                                                    alt="Karung Beras" 
                                                    width="120px">
                                                <div style="margin-top: 10px;">
                                                    @php
                                                    $lastBerasJumlah = App\BerasJumlah::where('id_stock', '<>', '')
                                                    ->orderBy('id_stock', 'desc')
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();
                                                    @endphp
                                                
                                                    <div class="d-flex gap-2">
                                                        <h3 class="badge bg-secondary" style="font-size: 28px; margin-bottom: 10px;"> 
                                                            @if ($lastBerasJumlah)
                                                                {{ $lastBerasJumlah->jumlah_stock_sesudah }}
                                                            @else
                                                                -
                                                            @endif
                                                        </h3>
                                                    
                                                        <h3 class="badge bg-secondary" style="font-size: 28px; margin-bottom: 10px;"> 
                                                            @if ($lastBerasJumlah)
                                                                {{ $lastBerasJumlah->satuan_berat }}
                                                            @else
                                                                -
                                                            @endif
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="table-responsive datatable-minimal">
                                <table id="pengirimCatering" class="table table-hover table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Stock</th>
                                            <th class="text-center col-md-1">Tanggal</th>
                                            <th class="text-center col-md-1">Status</th>
                                            <th class="text-center col-md-1">Stock Sebelum</th>
                                            <th class="text-center col-md-1">Transaksi In</th>
                                            <th class="text-center col-md-1">Transaksi Out</th>
                                            <th class="text-center col-md-1">Stock Sesudah</th>
                                            <th class="text-center col-md-1">Keterangan</th>
                                            {{-- <th class="text-center col-md-1">Adjustment Transaksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Kedatangan beras Bulan Ini</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        </section>
    </div>
    </div>
@endsection
@push('after-script')
    {{-- <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> --}}
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#pengirimCatering').DataTable({
            ajax: {
                url: '{{ route('cateringbas.get-all-stock-beras-petugas') }}',
                data: function(d) {
                    d.minDate = $('#tanggal_awal').val();
                    d.maxDate = $('#tanggal_akhir').val();
                },
            },
            columns: [
                {
                data: 'id_stock',
                name: 'id_stock',
                visible: false,
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row) {
                        var dateTimeParts = data.split(' ');
                        var datePart = dateTimeParts[0];
                        var timePart = dateTimeParts[1];

                        var dateComponents = datePart.split('-');
                        var year = dateComponents[0];
                        var month = dateComponents[1];
                        var day = dateComponents[2];

                        var formattedDate = `${day} ${month} ${year}`;

                        return `<div style="display: flex; flex-direction: column; align-items: start;">
                            <span><i class="fas fa-calendar-alt"></i> ${formattedDate}</span>
                            <span><i class="fas fa-clock"></i> ${timePart}</span>
                        </div>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jumlah_stock',
                    name: 'jumlah_stock'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (data.transaksi_masuk === 0 || data.transaksi_masuk === null) {
                            return '<span class="badge bg-secondary">-</span>';
                        } else {
                            return '<span class="badge bg-success">' + data.transaksi_masuk + ' ' + data.satuan_berat + '</span>';
                        }
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (data.transaksi_keluar === 0 || data.transaksi_keluar === null) {
                            return '<span class="badge bg-secondary">-</span>';
                        } else {
                            let displayText;
                            if (data.transaksi_keluar === 1) {
                                displayText = '1 sak';
                            } else if (data.transaksi_keluar === 0.5) {
                                displayText = '1/2 sak';
                            } else if (data.transaksi_keluar === 0.33) {
                                displayText = '1/3 sak';
                            } else {
                                displayText = data.transaksi_keluar + ' ' + data.satuan_berat;
                            }
                            return '<span class="badge bg-danger">' + displayText + '</span>';
                        }
                    }
                },
                {
                    data: 'jumlah_stock_sesudah',
                    name: 'jumlah_stock_sesudah'
                },
                {
                    data: "keterangan",
                    render: function(data, type, row) {
                        if (data === null || data.trim() === "") {
                            return '<span class="badge bg-secondary">-</span>';
                        } else {
                            return data;
                        }
                    }
                }
                // {
                //     data: "adjustment_kedatangan",
                //     render: function(data, type, row) {
                //         if (data === null || data === 0) {
                //             return '<a href="#" class="add-adjustment badge bg-secondary" data-id="' + row.id + '">-</a>';
                //         } else {
                //             return '<span class="badge bg-warning">' + data + ' sak</span>';
                //         }
                //     }
                // }
            ],
            columnDefs: [
                {
                    targets: [0],
                    orderData: [0, 1],
                    type: 'string', 
                }
            ],
            order: [[0, 'desc'], [1, 'desc']],
            language: {
                info: "",
                infoEmpty: "",
            }
        });



        $('#tanggal_awal, #tanggal_akhir').on('change', function() {
        console.log('Reloading table...');
        table.ajax.reload(function(json) {
        console.log('Data from server:', json);
        });
        });

        });


        $(window).on('load', function() {
            $('#loading-overlay').fadeOut('slow');
        });

        let ctx = document.getElementById('pieChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Jumlah Stock', 'Transaksi Masuk', 'Transaksi Keluar'],
                datasets: [{
                    label: 'Data',
                    data: [{{ $latestBerasJumlah }}, {{ $transaksiMasukCount }},
                        {{ $transaksiKeluarCount }}
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
@endpush
