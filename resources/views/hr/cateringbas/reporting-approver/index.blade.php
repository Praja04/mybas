@extends('layouts.base')

@push('styles')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <style>
        .approve-semua-pesanan {
            display: inline-block;
            /* Menambahkan ini */
            padding: 1em 2em;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #2c9caf;
            transition: all 1000ms;
            font-size: 15px;
            position: relative;
            overflow: hidden;
            outline: 2px solid #2c9caf;
            cursor: pointer;
            text-decoration: none;
            /* Menghilangkan underline */
        }

        .approve-semua-pesanan:hover {
            color: #ffffff;
            transform: scale(1.1);
            outline: 2px solid #70bdca;
            box-shadow: 4px 5px 17px -4px #268391;
        }

        .approve-semua-pesanan::before {
            content: "";
            position: absolute;
            left: -50px;
            top: 0;
            width: 0;
            height: 100%;
            background-color: #2c9caf;
            transform: skewX(45deg);
            z-index: -1;
            transition: width 1000ms;
        }

        .approve-semua-pesanan:hover::before {
            width: 250%;
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
                            <h3 class="card-label">Data Form Menu <span style="color: red">Approver</span>
                                <span class="d-block text-muted pt-2 font-size-sm">Cek dan Approve Data Catering</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Tombol Approve Semua Data Transaksi -->
                        <a href="{{ url('/reporting/approve-all-transaksi-catering') }}" class="approve-semua-pesanan mb-3"
                            onclick="return confirm('Apakah anda yakin ingin approve semua data?')">
                            Approve Semua Pesanan
                        </a>

                        <!-- Tabel Data... -->
                        <table id="pengirimCatering" class="table table-hover table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th width="4%">no</th> --}}
                                    <th class="col-md-1" style="color: red;">Nomor Transaksi</th>
                                    <th class="col-md-1">Tanggal</th>
                                    <th class="col-md-1">Catering</th>
                                    <th class="col-md-1">Foto Kendaraan</th>
                                    <th class="col-md-1">Shift</th>
                                    <th class="col-md-1">PIC kendaraan</th>
                                    <th class="col-md-1">PIC cek pesanan</th>
                                    <th class="col-md-1">PIC Sampel</th>
                                    <th class="col-md-2">Cek Kendaraan</th>
                                    <th class="col-md-2">Cek Pesanan</th>
                                    <th class="col-md-2">Cek Sampel</th>
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
                // mengubah index dari id ke id_transaksi
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: "{{ route('cateringbas.get.reportingGa') }}",
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    // {
                    //     data: 'id'
                    //     visible: false
                    // },
                    {
                        data: 'id_transaksi'
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'catering'
                    },
                    {
                        data: 'foto',
                        render: function(data) {
                            return `<a href="${data}" class=""><img src="${data}" class="clickable-image" style="max-width: 80px; height: auto;" /></a>`;
                        }
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
                                </div>`;
                            } else if (row.status_cek_kendaraan === 'menunggu approval') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('/reporting/kendaraan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-warning text-white mx-2">
                                        <i class="fa fa-eye"></i>
                                        <span>Menunggu Persetujuan</span>
                                    </a>
                                </div>`;
                            } else if (row.status_cek_kendaraan === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-eye"></i>
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
                                        </div>`;
                            } else if (row.status_cek_kedatangan === 'menunggu approval') {
                                return `
                                        <div class="mt-4 text-center">
                                            <a href="{{ url('/reporting/pesanan/') }}/${row.id_transaksi}"
                                                class="btn btn-sm btn-warning text-white mx-2">
                                                <i class="fa fa-eye"></i>
                                                <span>Menunggu Persetujuan</span>
                                            </a>
                                        </div>`;
                            } else if (row.status_cek_kedatangan === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-eye"></i>
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
                                </div>`;
                            } else if (row.status_pengambilan_sampel === 'menunggu approval') {
                                return `
                                <div class="mt-4 text-center">
                                    <a href="{{ url('/reporting/sampel/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-warning text-white mx-2">
                                        <i class="fa fa-eye"></i>
                                        <span>Menunggu Persetujuan</span>
                                    </a>
                                </div>`;
                            } else if (row.status_pengambilan_sampel === 'belum') {
                                return `
                                <div class="mt-4 text-center">
                                    <a
                                        class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-eye"></i>
                                        <span>Data Sampel Belum Dikirim</span>
                                    </a>
                                </div>`;
                            }
                        }
                    }
                ]
            });
        });
    </script>
@endpush
