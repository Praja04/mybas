@extends('layouts.base')

@push('styles')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">

    <style>
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

        @media (max-width: 768px) {
            .tanggal {
                flex-direction: column;
                align-items: center; 
            }
        
            .tanggal .form-group {
                width: 100%; 
            }

            .tanggal .form-group input {
                width: 80%; 
                display: block; 
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data <span style="color: red">Reporting Detail Catering</span>
                                <span class="d-block text-muted pt-2 font-size-sm">Lihat Detail Data Yang sudah Di
                                    Approve</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tanggal" style="display: flex; gap: 20px;">
                            <div class="form-group">
                                <label for="minDate">Cari Dari Tanggal:</label>
                                <input type="date" id="minDate" class="form-control custom-date-input">
                            </div>
                            <div class="form-group">
                                <label for="maxDate">Sampai Dengan Tanggal:</label>
                                <input type="date" id="maxDate" class="form-control custom-date-input">
                            </div>
                        </div>
                        <table id="pengirimCatering" class="table table-hover table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="4%">no</th>
                                    <th class="col-md-1" style="color: red;">Nomor Transaksi</th>
                                    {{-- <th class="col-md-1">Foto</th> --}}
                                    <th class="col-md-1">Foto</th>
                                    <th class="col-md-1">Tanggal</th>
                                    <th class="col-md-1">Catering</th>
                                    <th class="col-md-1">Shift</th>
                                    <th class="col-md-1">PIC kendaraan</th>
                                    <th class="col-md-1">PIC cek pesanan</th>
                                    <th class="col-md-1">PIC Sampel</th>
                                    <th class="col-md-2"><i class="fas fa-car pr-2"></i> Detail Cek Kendaraan</th>
                                    <th class="col-md-2"><i class="fas fa-utensils pr-2"></i> Detail Cek Pesanan</th>
                                    <th class="col-md-2"><i class="fas fa-utensil-spoon pr-2"></i> Detail Cek Sampel</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table kedatangan lauk --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    {{-- filepon --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script type="text/javascript">
        // open modal pesanan catering
        function showModalCreateCatering() {
            $('#modal-title-pesanan').text('Upload Data Pengirim Catering');
            $('#modalPesananCatering').modal('show');
        }

        // getall data pengirim
        $(document).ready(function() {
            var table = $('#pengirimCatering').DataTable({
                paging: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('cateringbas.get.reporting-GA-Detail') }}",
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'id_transaksi'
                    },
                    {
                        data: 'foto',
                        render: function(data) {
                            return `<a href="${data}" class=""><img src="${data}" class="clickable-image" style="max-width: 80px; height: auto;" /></a>`;
                        }
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'catering'
                    },
                    {
                        data: 'shift'
                    },
                    {
                        data: 'nama_petugas_security'
                    },
                    {
                        data: 'nama_petugas_kantin_pesanan'
                    },
                    {
                        data: 'nama_petugas_kantin_sampel'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.status_cek_kendaraan === 'sudah') {
                                return `
                                <div class="mt-4 text-center">
                                    <a
                                        class="btn btn-sm btn-success text-white mx-2">
                                        <i class="fa fa-check"></i>
                                        <span>sudah di approve</span>
                                    </a>
                                    <a class="btn btn-info" href="{{ url('reporting/detail-kendaraan/') }}/${row.id_transaksi}"> <i class="fa fa-eye"></i></a>
                                </div>`;
                            } else if (row.status_cek_kendaraan === 'menunggu approval') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('reporting/detail-kendaraan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-warning text-white mx-2">
                                        <i class="fa fa-clock"></i>
                                        <span>Menunggu Persetujuan</span>
                                    </a>
                                </div>`;
                            } else if (row.status_cek_kendaraan === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('reporting/detail-kendaraan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-clock"></i>
                                        <span>Pengecekan Kendaraan Belum Dikirim</span>
                                    </a>
                                </div>`;
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.status_cek_kedatangan === 'sudah') {
                                return `
                                        <div class="mt-4 text-center">
                                            <a
                                                class="btn btn-sm btn-success text-white mx-2">
                                                <i class="fa fa-check"></i>
                                                <span>sudah di approve</span>
                                            </a>
                                            <a class="btn btn-info" href="{{ url('reporting/detail-pesanan/') }}/${row.id_transaksi}"> <i class="fa fa-eye"></i></a>
                                        </div>`;
                            } else if (row.status_cek_kedatangan === 'menunggu approval') {
                                return `
                                        <div class="mt-4 text-center">
                                            <a href="{{ url('reporting/detail-pesanan/') }}/${row.id_transaksi}"
                                                class="btn btn-sm btn-warning text-white mx-2">
                                                <i class="fa fa-clock"></i>
                                                <span>Menunggu Persetujuan</span>
                                            </a>
                                        </div>`;
                            } else if (row.status_cek_kedatangan === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('reporting/detail-pesanan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-clock"></i>
                                        <span>Data Pesanan Belum Dikirim</span>
                                    </a>
                                </div>`;
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.status_pengambilan_sampel === 'sudah') {
                                return `
                                <div class="mt-4 text-center">
                                    <a
                                        class="btn btn-sm btn-success text-white mx-2">
                                        <i class="fa fa-check"></i>
                                        <span>sudah di approve</span>
                                    </a>
                                    <a class="btn btn-info" href="{{ url('reporting/detail-sampel/') }}/${row.id_transaksi}"> <i class="fa fa-eye"></i></a>
                                </div>`;
                            } else if (row.status_pengambilan_sampel === 'menunggu approval') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('reporting/detail-sampel/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-warning text-white mx-2">
                                        <i class="fa fa-clock"></i>
                                        <span>Menunggu Persetujuan</span>
                                    </a>
                                </div>`;
                            } else if (row.status_pengambilan_sampel === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('reporting/detail-sampel/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-clock"></i>
                                        <span>Data Sampel Belum Dikirim</span>
                                    </a>
                                </div>`;
                            }
                        }
                    }
                ]
            });
            $('#minDate, #maxDate').on('input', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var minDate = new Date($('#minDate').val());
                var maxDate = new Date($('#maxDate').val());
                var currentDate = new Date(data[3] || 0); 

                if ( (isNaN(minDate.getTime()) && isNaN(maxDate.getTime())) ||
                    (isNaN(minDate.getTime()) && currentDate <= maxDate) ||
                    (minDate <= currentDate && isNaN(maxDate.getTime())) ||
                    (minDate <= currentDate && currentDate <= maxDate)) {
                    return true;
                }
                return false;
            }
        );
        });
    </script>
@endpush
