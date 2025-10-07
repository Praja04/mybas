@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        /* style table */

        @media (min-width: 768px) {
        .table th, .table td {
            width: 12.5%; /* Contoh: setiap kolom sama lebar */
        }
    }
            #tablekedatanganBeras {
            border-collapse: collapse;
            width: 100%;
        }

        #tablekedatanganBeras th,
        #tablekedatanganBeras td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #tablekedatanganBeras th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4c57af;
            color: white;
        }
        /* endstyle */

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
        
        /* styling loading spinner */
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

    </style>
@endpush

@section('content')
    {{-- <div id="loading-overlay">
        <!-- Your loading animation goes here -->
        <div class="loader"></div>
    </div> --}}
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        {{-- <div class="page-heading">
            <h3>Master Data Beras</h3>
        </div> --}}
        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom">
                            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                <div class="card-title">
                                    <h3 class="card-label">Report Bulanan <span style="color:red;">Beras</span></h3>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                <div class="tanggal mb-3" style="display: flex; gap: 20px;">
                                    <div class="form-group">
                                        <label for="minDate">Cari Dari Tanggal:</label>
                                        <input type="date" id="minDate" class="form-control custom-date-input">
                                    </div>
                                    <div class="form-group">
                                        <label for="maxDate">Sampai Dengan Tanggal:</label>
                                        <input type="date" id="maxDate" class="form-control custom-date-input">
                                    </div>
                                </div>
                                <button id="searchButton" class="btn btn-primary btn-lg mb-1">Cari</button>
                                <div id="exportButtons" style="display: none; text-align: center; margin-bottom: 10px;">
                                    <button id="exportExcelButton" class="btn btn-success" style="margin-right: 10px;">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                                <div id="tableContainer" class="mt-3">
                                    <!-- Tampilkan table yang akan di-append -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script type="text/javascript">
        
        function exportExcel() {
            var table = document.getElementById("tablekedatanganBeras");
            var ws = XLSX.utils.table_to_sheet(table);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            XLSX.writeFile(wb, "laporan_stock_beras.xlsx");
        }
    
        $(document).ready(function() {
            $('#searchButton').click(function() {
                var tanggalAwal = $('#minDate').val();
                var tanggalAkhir = $('#maxDate').val();
    
                if (!tanggalAwal || !tanggalAkhir) {
                    Swal.fire('Silahkan pilih tanggal terlebih dahulu');
                    return;
                }
    
                $.ajax({
                    url: '/dashboard/cetak-report-stock-beras',
                    type: 'GET',
                    data: {
                        tanggal_awal: tanggalAwal,
                        tanggal_akhir: tanggalAkhir
                    },
                    success: function(data) {
                        $('#tableContainer').empty();
                        var table = $('<table>').attr('id', 'tablekedatanganBeras').addClass('table');
                        var thead = $('<thead>');
                        var tbody = $('<tbody>');
                        thead.append('<tr><th>Tanggal</th><th>Jumlah Stock</th><th>Satuan Berat</th><th>Transaksi Masuk</th><th>Transaksi Keluar</th><th>Jumlah Stock Sesudah</th><th>Keterangan</th><th>Adjustment Kedatangan</th></tr>');
    
                        $.each(data, function(i, item) {
                            var row = $('<tr>');
                            var formattedDate = item.tanggal.split(' ')[0];
                            row.append($('<td>').text(formattedDate));
                            row.append($('<td>').text(item.jumlah_stock));
                            row.append($('<td>').text(item.satuan_berat));
                            row.append($('<td>').html(item.transaksi_masuk ? item.transaksi_masuk : '<span>-</span>'));
                            row.append($('<td>').html(item.transaksi_keluar ? item.transaksi_keluar : '<span>-</span>'));
                            row.append($('<td>').text(item.jumlah_stock_sesudah));
                            row.append($('<td>').html(item.keterangan ? item.keterangan : '<span>-</span>'));
                            row.append($('<td>').html(item.adjustment_kedatangan ? item.adjustment_kedatangan : '<span>-</span>'));
                            tbody.append(row);
                        });
    
                        table.append(thead).append(tbody);
                        $('#tableContainer').append(table);
                        $('#exportButtons').show();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
    
            $('#exportExcelButton').click(function() {
                exportExcel();
            });
        });

        $(document).ready(function() {
            $('#loading-overlay').show();
            $(window).on('load', function() {
                $('#loading-overlay').fadeOut('slow');
            });
        });
    </script>
@endpush
