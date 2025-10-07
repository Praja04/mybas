@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        .table-header-color th {
            background-color: #af4c6b;
            color: white;
        }

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
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #db34ba; /* Blue */
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        /* Animation for the loader */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* style adjustment button */
        .adjustment-button {
        appearance: button;
        background-color: #d61877;
        border: solid transparent;
        border-radius: 16px;
        border-width: 0 0 4px;
        box-sizing: border-box;
        color: #FFFFFF;
        cursor: pointer;
        display: inline-block;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .8px;
        line-height: 20px;
        margin: 0;
        outline: none;
        overflow: visible;
        padding: 13px 19px;
        text-align: center;
        text-transform: uppercase;
        touch-action: manipulation;
        transform: translateZ(0);
        transition: filter .2s;
        user-select: none;
        -webkit-user-select: none;
        vertical-align: middle;
        white-space: nowrap;
        }

        .adjustment-button:after {
        background-clip: padding-box;
        background-color: #f61c9b;
        border: solid transparent;
        border-radius: 16px;
        border-width: 0 0 4px;
        bottom: -4px;
        content: "";
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        z-index: -1;
        }

        .adjustment-button:main, .adjustment-button:focus {
        user-select: auto;
        }

        .adjustment-button:hover:not(:disabled) {
        filter: brightness(1.1);
        }

        .adjustment-button:disabled {
        cursor: auto;
        }

        .adjustment-button:active:after {
        border-width: 0 0 0px;
        }
        .adjustment-button:active {
        padding-bottom: 10px;
        }

        /* style btn submit anjay*/
        .btn-anjay {
        background-color: #bf008c;
        padding: 8px 18px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        border-radius: 10px;
        border: 2px dashed #bf0096;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        transition: .4s;
        }

        .btn-anjay span:last-child {
        display: none;
        }

        .btn-anjay:hover {
        transition: .4s;
        border: 2px dashed #bf008c;
        background-color: #fff;
        color: #bf008f;
        }

        .btn-anjay:active {
        background-color: #d587db;
        }

         /* karung image modal animation */
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        .vector-karung {
            animation: bounce 0.5s infinite;
        }
        /* end karung image modal */

        /* adjustment button excel */

        .adjustment-button-excel {
        appearance: button;
        background-color: #167649;
        border: solid transparent;
        border-radius: 16px;
        border-width: 0 0 4px;
        box-sizing: border-box;
        color: #FFFFFF;
        cursor: pointer;
        display: inline-block;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .8px;
        line-height: 20px;
        margin: 0;
        outline: none;
        overflow: visible;
        padding: 13px 19px;
        text-align: center;
        text-transform: uppercase;
        touch-action: manipulation;
        transform: translateZ(0);
        transition: filter .2s;
        user-select: none;
        -webkit-user-select: none;
        vertical-align: middle;
        white-space: nowrap;
        }

        .adjustment-button-excel:after {
        background-clip: padding-box;
        background-color: #167649;
        border: solid transparent;
        border-radius: 16px;
        border-width: 0 0 4px;
        bottom: -4px;
        content: "";
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        z-index: -1;
        }

        .adjustment-button-excel:main, .adjustment-button:focus {
        user-select: auto;
        }

        .adjustment-button-excel:hover:not(:disabled) {
        filter: brightness(1.1);
        }

        .adjustment-button-excel:disabled {
        cursor: auto;
        }

        .adjustment-button-excel:active:after {
        border-width: 0 0 0px;
        }
        .adjustment-button-excel:active {
        padding-bottom: 10px;
        }

        /* atur agar responsive */
        @media (max-width: 767px) {
        #highlight-beras {
            position: relative;
            left: 0 !important; 
            display: block !important; 
            margin-top: 10px; 
            }
        }

    </style>
@endpush

@section('content')
{{-- <div id="loading-overlay">
    <div class="loader"></div>
</div> --}}
    <div id="main">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title pt-2 pb-2">
                            <h3 class="card-label">Transaksi Pemakaian<span style="color: red"> Beras</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
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
                                        <div id="highlight-beras" style="position: relative; left: 250px;">
                                            <img class="literan-image"
                                                src="{{ asset('assets/mazer/dist/assets/compiled/png/literan.png') }}"
                                                alt="literan Beras" 
                                                width="120px">
                                            <div style="margin-top: 10px;">
                                                @php
                                                $lastBerasPengambilan = App\BerasPengambilan::where('id_stock', '<>', '')
                                                ->orderBy('id_stock', 'desc')
                                                ->orderBy('created_at', 'desc')
                                                ->first();
                                                @endphp
                                            
                                            <div class="d-flex gap-2">
                                                <h3 class="badge bg-secondary" style="font-size: 28px; margin-bottom: 10px;"> 
                                                    @if ($lastBerasPengambilan)
                                                        {{ $lastBerasPengambilan->jumlah_pengambilan_sesudah }}
                                                    @else
                                                        -
                                                    @endif
                                                </h3>
                                            
                                                <h3 class="badge bg-secondary" style="font-size: 28px; margin-bottom: 10px;"> 
                                                    @if ($lastBerasPengambilan)
                                                        {{ $lastBerasPengambilan->satuan_berat }}
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
                        <div class="d-flex justify-content-start mb-4 mt-4"> 
                            <div class="row">
                                <div class="form-group col-md-6 text-left">
                                    <button class="adjustment-button" data-bs-toggle="modal" data-bs-target="#AdjustmentModal" style="margin-right: 10px;">
                                        <i class="fas fa-cogs"></i> Tambahkan Adjustment
                                    </button>
                                </div>
                                <div class="form-group col-md-6 text-center"> 
                                    <button id="exportToExcel" class="adjustment-button-excel">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>                           
                        <div id="export-buttons" style="display: none; margin-top: 7px; margin-bottom: 7px;">
                            <button id="exportExcel" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                            <button id="exportPDF" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </button>
                        </div>
                        <div class="table-responsive datatable-minimal">
                            <table id="pemakaianStock" class="table table-hover table-bordered table-striped">
                                <thead class="table-header-color">
                                    <tr>
                                        <th>ID Stock</th>
                                        <th class="col-md-1">Tanggal</th>
                                        <th class="col-md-1">Shift</th>
                                        <th class="col-md-1">Status</th>
                                        <th class="col-md-1">Dapur</th>
                                        <th class="col-md-1">Transaksi In</th>
                                        <th class="col-md-1">Transaksi Out</th>
                                        <th class="col-md-1">Dapur sesudah</th>
                                        <th class="col-md-1">Keterangan</th>
                                        {{-- <th class="col-md-1">Adjustment Transaksi</th> --}}
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="AdjustmentModal" tabindex="-1" aria-labelledby="AdjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Adjustment Transaksi Pemakaian<span style="color: red;"> Beras</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="keteranganForm">
                        <!-- CSRF Token -->
                        @csrf
                        <div class="mb-3 row text-center">
                            <div>
                                <img class="vector-karung"
                                    src="{{ asset('assets/mazer/dist/assets/compiled/png/rice-vektor.png') }}"
                                    alt="Karung Beras" 
                                    width="80px">
                            </div>
                            <div>
                                <label for="disabledInput">Jumlah Pengeluaran Saat Ini</label>
                                @php
                                $lastBerasJumlah = App\BerasPengambilan::where('id_stock', '<>', '')
                                    ->orderBy('id_stock', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                                @endphp
                                @if ($lastBerasJumlah)
                                <h3 id="stockValue">{{ $lastBerasJumlah->jumlah_pengambilan_sesudah }}</h3>
                                <input type="hidden" name="jenis_adjustment" id="jenisAdjustment" value="">
                                <input type="text" class="form-control" id="disabledInput" 
                                    style="display: none;" 
                                    value="{{ $lastBerasJumlah->jumlah_pengambilan_sesudah }}" 
                                    name="jumlah_pengambilan_sesudah">
                                <input type="text" class="form-control" id="rowId" 
                                    style="display: none;" 
                                    value="{{ $lastBerasJumlah->id }}" 
                                    name="id">
                                @endif
                            </div>
                        </div>
    
                        <div class="mb-3 d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-primary" id="btnTambah">
                                <i class="bi bi-plus"></i> Tambah
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnKurang">
                                <i class="bi bi-dash"></i> Kurang
                            </button>
                        </div>
                        <div class="mb-3 d-none" id="jumlahKeteranganTambah">
                            <label for="keterangan-jumlah-tambah" class="form-label">Masukkan Jumlah <span style="color: #3498db; font-weight:800;">(Tambah)</span><span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="keterangan-jumlah-tambah" name="jumlah_keterangan_tambah">
                        </div>
                        <div class="mb-3 d-none" id="jumlahKeteranganKurang">
                            <label for="keterangan-jumlah-kurang" class="form-label">Masukkan Jumlah  <span style="color: #db3434; font-weight:800;">(Kurang)</span><span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="keterangan-jumlah-kurang" name="jumlah_keterangan_kurang">
                        </div>
                        <button type="button" id="submitKeteranganBtn" class="btn-anjay">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('after-script')

<script>
    $(document).ready(function() {
        $('#loading-overlay').show();
        var table = $('#pemakaianStock').DataTable({
            initComplete: function(settings, json) {
                $('#loading-overlay').fadeOut('slow');
            },
            ajax: {
                url: '{{ route('cateringbas.get-all-stock-pengambilan') }}',
                data: function(d) {
                    d.minDate = $('#tanggal_awal').val();
                    d.maxDate = $('#tanggal_akhir').val();
                },
            },
            responsive: true,
            dom: '<"toolbar">frtip',
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
                { data: "Shift" },
                { data: "status", name: "status"},
                {
                    data: "jumlah_pengambilan_sebelum",
                    render: function(data, type, row) {
                        return data + ' ' + row.satuan_berat;
                    }
                },
                { 
                    data: "transaksi_masuk",
                    render: function(data, type, row) {
                        if (data === null || data === 0) {
                            return '<span class="badge bg-secondary text-white">-</span>';
                        } else {
                            return '<span class="badge bg-success text-white">' + data + ' ' + row.satuan_berat + '</span>';
                        }
                    }
                },
                { 
                    data: "transaksi_keluar",
                    render: function(data, type, row) {
                        if (data === null || data === 0) {
                            return '<span class="badge bg-secondary">-</span>';
                        } else {
                            return '<span class="badge bg-danger">' + data + ' ' + row.satuan_berat + '</span>';
                        }
                    }
                },
                {
                    data: "jumlah_pengambilan_sesudah",
                    render: function(data, type, row) {
                        return data + ' ' + row.satuan_berat;
                    }
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
                },
            ],
            columnDefs: [
                {
                    targets: [0],
                    orderData: [0, 1],
                    type: 'string', 
                }
            ],
            order: [[0, 'desc']]
        });

        $('#tanggal_awal, #tanggal_akhir').on('change', function() {
            console.log('Reloading table...');
            table.ajax.reload(function(json) {
                console.log('Data from server:', json);
            });
        });

        $('#exportToExcel').on('click', function() {
            var minDate = $('#tanggal_awal').val();
            var maxDate = $('#tanggal_akhir').val();

            if (!minDate || !maxDate) {
                Swal.fire({
                    title: 'Info',
                    text: 'Harap pilih rentang tanggal awal dan akhir dahulu.',
                    icon: 'info',
                    confirmButtonText: 'Ok'
                });
            } else {
                Swal.fire({
                    title: 'Sedang melakukan export excel beras',
                    text: 'Dimohon untuk menunggu...',
                    icon: 'success',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000 
                });

            $.ajax({
                url: '{{ route('kedatangan-beras.cetak-buffer-stock-beras') }}',
                method: 'GET',
                data: {
                    minDate: minDate,
                    maxDate: maxDate
                },
                xhrFields: {
                    responseType: 'blob' 
                },
                headers: {
                    'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
                },
                success: function(response) {
                    var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'buffer_stock_beras.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        });
    });
</script>


<script>
    // $(document).on("click", ".add-adjustment", function(e) {
    //     e.preventDefault();
    //     var id = $(this).data("id");
    //     var jumlahStockSesudah = $(this).data("jumlah-pengambilan-sebelum"); 

    //     $('#rowId').val(id);
    //     $('#jumlah-pengambilan-sebelum').val(jumlahStockSesudah);

    //     console.log("edit ke id:", id);
    //     console.log("Jumlah Stock Sesudah:", jumlahStockSesudah);

    //     $('#AdjustmentModal').modal('show');
    // });


    // $(document).ready(function() {
    //     $("#btnTambah").click(function() {
    //         $("#jumlahKeteranganTambah").removeClass('d-none');
    //         $("#jumlahKeteranganKurang").addClass('d-none');
    //         $("#submitKeterangan").removeClass('d-none');
    //     });

    //     $("#btnKurang").click(function() {
    //         $("#jumlahKeteranganTambah").addClass('d-none');
    //         $("#jumlahKeteranganKurang").removeClass('d-none');
    //         $("#submitKeterangan").removeClass('d-none');
    //     });

    //     $('#submitKeterangan').on('click', function() {
    //         var id = $('#rowId').val();
    //         var jumlahTambah = $('#keterangan-jumlah-tambah').val();
    //         var jumlahKurang = $('#keterangan-jumlah-kurang').val();
    //         var jumlahStock = $('#jumlah-pengambilan-sebelum').val();
    //         var data = {
    //             id: id,
    //             jumlah_pengambilan_sebelum: jumlahStock,
    //             _token: '{{ csrf_token() }}'
    //         };

    //         if (!$("#jumlahKeteranganTambah").hasClass('d-none')) {
    //             data['jumlah_keterangan_tambah'] = jumlahTambah;
    //         } else {
    //             data['jumlah_keterangan_kurang'] = jumlahKurang;
    //         }

    //         Swal.fire({
    //             title: 'Konfirmasi',
    //             text: "Apakah anda yakin dengan penyesuaian ini?",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Ya, yakin!',
    //             cancelButtonText: 'Batal'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 data['jumlah_pengambilan_sebelum'] = jumlahStock;
    //                 $.ajax({
    //                     url: '{{ route("cateringbas.GAadjustPengeluaran") }}',
    //                     type: 'POST',
    //                     data: data,
    //                     success: function(response) {
    //                         $('#AdjustmentModal').modal('hide');
    //                         var table = $('#pemakaianStock').DataTable();
    //                         table.ajax.reload();
    //                         Swal.fire({
    //                             title: 'Success!',
    //                             text: 'Keterangan berhasil ditambahkan',
    //                             icon: 'success',
    //                             confirmButtonText: 'OK'
    //                         });
    //                     },
    //                     error: function(error) {
    //                         Swal.fire({
    //                             title: 'Error!',
    //                             text: 'Terjadi kesalahan, coba lagi.',
    //                             icon: 'error',
    //                             confirmButtonText: 'OK'
    //                         });
    //                     }
    //                 });
    //             }
    //         });
    //     });
    // });
</script>

{{-- revisi penambahan adjustment --}}
<script>
    $(document).ready(function () {

        $("#btnTambah").click(function() {
            $("#jumlahKeteranganTambah").removeClass('d-none');
            $("#jumlahKeteranganKurang").addClass('d-none');
            $("#submitKeteranganBtn").removeClass('d-none');
            $("#jenisAdjustment").val('tambah');
        });

        // Mengatur perilaku ketika tombol 'Kurang' diklik
        $("#btnKurang").click(function() {
            $("#jumlahKeteranganTambah").addClass('d-none');
            $("#jumlahKeteranganKurang").removeClass('d-none');
            $("#submitKeteranganBtn").removeClass('d-none');
            $("#jenisAdjustment").val('kurang');
        });

        $('#submitKeteranganBtn').click(function () {
            var id = $('#rowId').val();
            var jumlahTambah = $('#keterangan-jumlah-tambah').val();
            var jumlahKurang = $('#keterangan-jumlah-kurang').val();
            var jumlahStock = $('#jumlah-stock').val(); 
            var jenisAdjustments = $('#jenisAdjustment').val(); 
            var data = {
                id: id,
                jumlah_stock: jumlahStock,
                jenis_adjustment: jenisAdjustments,
                _token: '{{ csrf_token() }}'
            };

            if (!$("#jumlahKeteranganTambah").hasClass('d-none')) {
                data['jumlah_keterangan_tambah'] = jumlahTambah;
            } else {
                data['jumlah_keterangan_kurang'] = jumlahKurang;
            }

            Swal.fire({
                title: 'Apakah Anda yakin ingin menambahkan adjustment kedatangan?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('cateringbas.GAadjustPengeluaran') }}',
                        type: 'POST',
                        data: $('#keteranganForm').serialize(),
                        success: function (response) {
                            if (response.success) {
                                console.log(response);
                                Swal.fire('Sukses', 'Adjustment berhasil dilakukan', 'success').then(() => {
                                    $('#AdjustmentModal').modal('hide');
                                    var table = $('#tablekedatanganBeras').DataTable();
                                    table.ajax.reload();
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', 'Transaksi sudah melakukan adjustment. Silahkan tunggu kedatangan transaksi berikutnya', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat melakukan permintaan.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#btnTambah").click(function() {
            $(this).addClass("active");
            $("#btnKurang").removeClass("active");
        });

        $("#btnKurang").click(function() {
            $(this).addClass("active");
            $("#btnTambah").removeClass("active");
        });
    });
</script>
@endpush
