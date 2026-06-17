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
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4 shadow-sm">
                                    <i class="ri-file-chart-line"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-1" style="font-weight: 600;">Report Karyawan Masuk</h5>
                                <p class="text-muted mb-0 fs-13">Rekapitulasi data riwayat karyawan yang telah berhasil
                                    menyelesaikan proses onboarding.</p>
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
                                    {{-- <input type="date" class="form-control shadow-sm" id="pilihTanggalMasuk"
                                        data-bs-toggle="tooltip" title="Filter Berdasarkan Tanggal Masuk"> --}}
                                    <select id="pilihTanggalMasuk" class="form-control shadow-sm" data-bs-toggle="tooltip"
                                        title="Filter Berdasarkan Tanggal Masuk" value="">-- Semua Bulan --</select>
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
                                        <th style="width: 15%;">Tanggal Masuk</th>
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
    <script src="{{ asset('assets/velzon/libs/moment/locale/id.js') }}"></script>
    <script>
        $(document).ready(function() {
            moment.locale('id');
            $('.js-example-basic-single').select2({
                width: '100%'
            });
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#pilihTanggalMasuk').select2({
                width: '100%',
                placeholder: '-- Semua Bulan --',
                allowClear: true,
                ajax: {
                    url: "{{ route('hr-connect.reportKaryawanMasuk.getFilterBulanTahunIn') }}",
                    dataType: 'JSON',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
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
                    cache: true,
                }
            })

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                paging: true,
                pageLength: 25,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/report/getDataKaryawanMasuk') }}",
                    data: function(d) {
                        d.tanggal = $('#pilihTanggalMasuk').val();
                    }
                },
                columns: [{
                        data: 'nama',
                        name: 'hr_karyawan.nama',
                        searchable: true,
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'nik',
                        name: 'hr_karyawan.nik',
                        searchable: true,
                    },
                    {
                        data: 'kode_divisi',
                        name: 'hr_karyawan.kode_divisi',
                        searchable: true,
                        render: data => data ? data : '-'
                    },
                    {
                        data: 'kode_bagian',
                        name: 'hr_karyawan.kode_bagian',
                        searchable: true,
                        render: data => data ? data : '-'
                    },
                    {
                        data: 'kode_group',
                        name: 'hr_karyawan.kode_group',
                        searchable: true,
                        render: data => data ? data : '-'
                    },
                    // {
                    //     data: 'kode_blok',
                    //     name: 'loker_transaksi.kode_rak', // FIX BUG: Disesuaikan dengan tabel JOIN History
                    //     render: function(data) {
                    //         return data ?
                    //             `<center><span class="badge bg-light text-dark border shadow-sm">${data}</span></center>` :
                    //             `<center>-</center>`;
                    //     }
                    // },
                    // {
                    //     data: 'no_loker',
                    //     name: 'loker_transaksi.no_loker', // FIX BUG: Disesuaikan dengan tabel JOIN History
                    //     render: function(data) {
                    //         return data ?
                    //             `<center><span class="badge bg-light text-dark border shadow-sm">${data}</span></center>` :
                    //             `<center>-</center>`;
                    //     }
                    // },
                    {
                        data: 'status_in',
                        name: 'status_in',
                        searchable: false,
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.status_in === 'NO-IN' || row.in_complete === 'N') {
                                return `<span class="badge bg-danger shadow-sm px-2 py-1">NO-IN</span>`;
                            } else if (row.status_in === 'IN') {
                                return `<span class="badge bg-success shadow-sm px-2 py-1">IN</span>`;
                            }
                            return `<span class="badge bg-secondary shadow-sm px-2 py-1">${data}</span>`;
                        }
                    },
                    {
                        data: 'tanggal_masuk',
                        name: 'hr_karyawan.tanggal_masuk',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            return (!data || data === '0000-00-00') ?
                                `<span class="text-muted fst-italic">Belum diset</span>` : moment(
                                    data).format('DD MMMM YYYY');
                        }
                    },
                ]
            });

            // Filter Eksekusi
            $("#pilihDivisi").on('change', function() {
                table.column(2).search($(this).val()).draw();
            });
            $("#pilihBagian").on('change', function() {
                table.column(3).search($(this).val()).draw();
            });
            $("#pilihKodeGroup").on('change', function() {
                table.column(4).search($(this).val()).draw();
            });
            $("#pilihTanggalMasuk").on('change', function() {
                let nilaiBulan = $(this).val();
                console.log('Bulan yang dikirim ke Controller: ' + nilaiBulan);
                table.draw();
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

            $("#btnResetFilter").on('click', function() {
                $('.js-example-basic-single').val('').trigger('change');
                $('#pilihTanggalMasuk').val(null).trigger('change');
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endpush
