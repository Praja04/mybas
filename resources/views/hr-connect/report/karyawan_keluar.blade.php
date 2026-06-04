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
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-danger text-danger rounded-circle fs-4 shadow-sm">
                                    <i class="ri-file-chart-line"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-1" style="font-weight: 600;">Report Karyawan Keluar (Clearance GA)
                                </h5>
                                <p class="text-muted mb-0 fs-13">Arsip data riwayat operasional karyawan keluar berdasarkan
                                    proses cabut loker oleh tim GA.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4">

                        <div class="bg-light p-3 rounded mb-4 shadow-sm border">
                            <h6 class="text-muted fw-bold mb-3"><i class="ri-filter-3-line me-1"></i> Filter Data Report
                            </h6>
                            <div class="row g-2">
                                <div class="col-lg-3">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihDivisi">
                                        <option value="">-- Semua Divisi --</option>
                                        @foreach ($kodeDivisi as $divisi)
                                            <option value="{{ $divisi }}">{{ $divisi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihBagian">
                                        <option value="">-- Semua Bagian --</option>
                                        @foreach ($kodeBagian as $bagian)
                                            <option value="{{ $bagian }}">{{ $bagian }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <select class="js-example-basic-single form-control shadow-sm" id="pilihKodeGroup">
                                        <option value="">-- Semua Group --</option>
                                        @foreach ($kodeGroup as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    {{-- <input type="date" class="form-control shadow-sm" id="pilihTanggalKeluar"
                                        data-bs-toggle="tooltip" title="Filter Berdasarkan Tanggal Cabut Loker"> --}}
                                    <select id="pilihTanggalKeluar" class="form-control shadow-sm" data-bs-toggle="tooltip"
                                        title="Filter Berdasarkan Bulan Clearance">
                                        <option value="" selected>-- Semua Bulan --</option>
                                    </select>
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
                                        <th style="width: 20%;">Nama Lengkap</th>
                                        <th style="width: 10%;">NIK</th>
                                        <th>Kode Divisi</th>
                                        <th>Kode Bagian</th>
                                        <th>Kode Group</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 15%;">Catatan GA</th>
                                        <th style="width: 15%;">Tanggal Clearance</th>
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
            $('.js-example-basic-single').select2({
                width: '100%'
            });
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#pilihTanggalKeluar').select2({
                placeholder: '-- Semua Bulan --',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('hr-connect.reportKaryawanKeluar.getFilterBulanTahunOut') }}",
                    dataType: 'JSON',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                paging: true,
                pageLength: 25,
                dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/report/getDataKaryawanKeluar') }}",
                    data: function(d) {
                        d.tanggal = $('#pilihTanggalKeluar').val();
                    }
                },
                columns: [{
                        data: 'nama',
                        name: 'nama',
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'kode_divisi',
                        name: 'kode_divisi'
                    },
                    {
                        data: 'kode_bagian',
                        name: 'kode_bagian'
                    },
                    {
                        data: 'kode_group',
                        name: 'kode_group'
                    },
                    {
                        data: 'status_in',
                        name: 'status_in',
                        className: 'text-center',
                        render: function(data) {
                            if (data === 'NO-IN') {
                                return `<span class="badge bg-danger shadow-sm px-2 py-1">NO-IN</span>`;
                            } else if (data === 'IN') {
                                return `<span class="badge bg-success shadow-sm px-2 py-1">IN</span>`;
                            }
                            return `<span class="badge bg-secondary shadow-sm px-2 py-1">${data}</span>`;
                        }
                    },
                    {
                        data: 'alasan_ga',
                        name: 'alasan_ga',
                        render: function(data) {
                            return data ? data :
                                `<span class="text-muted fst-italic">Tidak ada catatan</span>`;
                        }
                    },
                    {
                        data: 'tgl_shift_out',
                        name: 'tgl_shift_out',
                        className: 'text-center',
                        render: function(data) {
                            if (!data || data === '0000-00-00') {
                                return `<span class="text-muted fst-italic">Belum diset</span>`;
                            }
                            return moment(data).format('DD MMMM YYYY');
                        }
                    }
                ]
            });

            $("#pilihDivisi").on('change', function() {
                table.column(2).search($(this).val()).draw();
            });
            $("#pilihBagian").on('change', function() {
                table.column(3).search($(this).val()).draw();
            });
            $("#pilihKodeGroup").on('change', function() {
                table.column(4).search($(this).val()).draw();
            });

            $("#pilihTanggalKeluar").on('change', function() {
                table.draw();
            });

            $("#btnResetFilter").on('click', function() {
                $('.js-example-basic-single').val('').trigger('change');
                $('#pilihTanggalKeluar').val(null).trigger('change');

                table.search('').columns().search('').draw();
            });
        });
    </script>
@endpush
