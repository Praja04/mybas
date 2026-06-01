@extends('hr-connect.layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            height: 36px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            vertical-align: middle !important;
            text-align: center;
        }

        #tableAjax tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="card-title mb-0" style="font-weight: 600;">
                            <i class="ri-file-chart-line text-danger me-2"></i> Report Karyawan Keluar
                        </h5>
                    </div>
                    <div class="card-body pb-4">

                        <div class="bg-light p-3 rounded mb-4 shadow-sm border">
                            <h6 class="text-muted fw-bold mb-3"><i class="ri-filter-3-line me-1"></i> Filter Data Report
                            </h6>
                            <div class="row g-2">
                                <div class="col-lg-2">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihDivisi">
                                        <option value="">-- Semua Divisi --</option>
                                        @foreach ($kodeDivisi as $divisi)
                                            <option value="{{ $divisi }}">{{ $divisi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihBagian">
                                        <option value="">-- Semua Bagian --</option>
                                        @foreach ($kodeBagian as $bagian)
                                            <option value="{{ $bagian }}">{{ $bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihKodeGroup">
                                        <option value="">-- Semua Group --</option>
                                        @foreach ($kodeGroup as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihLoker">
                                        <option value="">-- Semua Loker --</option>
                                        @foreach ($lokers as $loker)
                                            <option value="{{ $loker->kode_blok . '-' . $loker->no_loker }}">
                                                Rak {{ $loker->kode_blok }} - Loker {{ $loker->no_loker }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <input type="date" class="form-control shadow-sm" id="pilihTanggalKeluar"
                                        data-bs-toggle="tooltip" title="Filter Berdasarkan Tanggal Keluar">
                                </div>
                                <div class="col-lg-1">
                                    <button class="btn btn-soft-danger w-100 shadow-sm" id="btnResetFilter"
                                        data-bs-toggle="tooltip" title="Reset Semua Filter">
                                        <i class="ri-refresh-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th rowspan="2" style="width: 20%;">Nama Lengkap</th>
                                        <th rowspan="2" style="width: 10%;">NIK</th>
                                        <th rowspan="2">Kode Divisi</th>
                                        <th rowspan="2">Kode Bagian</th>
                                        <th rowspan="2">Kode Group</th>
                                        <th colspan="2" class="text-center bg-soft-danger">History Loker</th>
                                        <th rowspan="2" style="width: 15%;">Tanggal Keluar</th>
                                    </tr>
                                    <tr class="bg-soft-danger">
                                        <th style="width: 8%;">Kode Rak</th>
                                        <th style="width: 8%;">No. Loker</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
    <script>
        $(document).ready(function() {
            // --- INIT SELECT2 ---
            $('.js-example-basic-single').select2({
                width: '100%'
            });
            $('[data-bs-toggle="tooltip"]').tooltip();

            // --- INIT DATATABLES ---
            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                paging: true, // Hidupkan paging
                pageLength: 25,
                dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/report/getDataKaryawanKeluar') }}"
                },
                columns: [
                    {
                        data: 'nama',
                        name: 'hr_karyawan.nama',
                        render: function(data) {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        data: 'nik',
                        name: 'hr_karyawan.nik'
                    },
                    {
                        data: 'kode_divisi',
                        name: 'hr_karyawan.kode_divisi'
                    },
                    {
                        data: 'kode_bagian',
                        name: 'hr_karyawan.kode_bagian'
                    },
                    {
                        data: 'kode_group',
                        name: 'hr_karyawan.kode_group'
                    },
                    {
                        data: 'kode_blok',
                        name: 'loker_transaksi.kode_rak', // INI KUNCI PENYELAMATNYA (Pake tabel transaksi)
                        render: function(data) {
                            return (data && data !== '-') ?
                                `<center><span class="badge bg-light text-dark border">${data}</span></center>` :
                                `<center>-</center>`;
                        }
                    },
                    {
                        data: 'no_loker',
                        name: 'loker_transaksi.no_loker', // INI KUNCI PENYELAMATNYA (Pake tabel transaksi)
                        render: function(data) {
                            return (data && data !== '-') ?
                                `<center><span class="badge bg-light text-dark border">${data}</span></center>` :
                                `<center>-</center>`;
                        }
                    },
                    {
                        data: 'tanggal_keluar',
                        name: 'hr_karyawan.tanggal_keluar',
                        render: function(data) {
                            if (!data || data === '0000-00-00') {
                                return `<span class="text-muted fst-italic">Belum diset</span>`;
                            }
                            return moment(data).format('DD MMMM YYYY');
                        }
                    },
                ]
            });

            // --- LOGIKA FILTER ---
            $("#pilihDivisi").on('change', function() {
                table.column(2).search($(this).val()).draw();
            });
            $("#pilihBagian").on('change', function() {
                table.column(3).search($(this).val()).draw();
            });
            $("#pilihKodeGroup").on('change', function() {
                table.column(4).search($(this).val()).draw();
            });

            $("#pilihLoker").on('change', function() {
                let search = $(this).val();
                if (search) {
                    let parts = search.split('-');
                    table.column(5).search(parts[0]).draw();
                    table.column(6).search(parts[1]).draw();
                } else {
                    table.column(5).search('').draw();
                    table.column(6).search('').draw();
                }
            });

            $("#pilihTanggalKeluar").on('change', function() {
                table.column(7).search($(this).val()).draw();
            });

            // --- TOMBOL RESET FILTER ---
            $("#btnResetFilter").on('click', function() {
                $('.js-example-basic-single').val('').trigger('change');
                $('#pilihTanggalKeluar').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endpush
