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
                            <i class="ri-folder-check-line text-success me-2"></i> Report Finalisasi Karyawan Keluar (HRD IR)
                        </h5>
                    </div>
                    <div class="card-body pb-4">

                        <div class="bg-light p-3 rounded mb-4 shadow-sm border">
                            <h6 class="text-muted fw-bold mb-3"><i class="ri-filter-3-line me-1"></i> Filter Data Report
                            </h6>
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-3">
                                    <label class="form-label mb-1" style="font-size: 0.85rem; font-weight: 500;">Berdasarkan
                                        Tanggal Keluar</label>
                                    <select class="js-example-basic-single form-control shadow-sm" id="tanggalFilter">
                                        <option value="">-- Semua Tanggal --</option>
                                        @foreach ($tanggalTersedia as $tgl)
                                            <option value="{{ $tgl }}">
                                                {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-soft-danger w-100 shadow-sm" id="btnResetFilter"
                                        data-bs-toggle="tooltip" title="Reset Tanggal">
                                        <i class="ri-refresh-line align-bottom me-1"></i> Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 15%;">NIK</th>
                                        <th style="width: 25%;">Nama Lengkap</th>
                                        <th style="width: 15%;">Departemen</th>
                                        <th>Alasan Keluar</th>
                                        <th style="width: 15%;">Tanggal Keluar Resmi</th>
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
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Init Select2 biar cakep
            $('.js-example-basic-single').select2({
                width: '100%'
            });

            let defaultTanggal = "";
            $("#tanggalFilter").val(defaultTanggal).trigger('change');

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                // dom: "<'row mb-3'<'col-sm-12 col-md-4 d-flex align-items-center'l><'col-sm-12 col-md-8 d-flex justify-content-end gap-2'Bf>>" +
                //     "<'row'<'col-sm-12'tr>>" +
                //     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                // buttons: [{
                //     extend: 'excel',
                //     text: '<i class="ri-file-excel-2-line align-bottom me-1"></i> Export to Excel',
                //     className: 'btn btn-success shadow-sm',
                //     filename: 'HRD IR - Report Karyawan Keluar',
                //     exportOptions: {
                //         columns: [0, 1, 2, 3, 4]
                //     }
                // }],
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-hrd/report-karyawan-keluar/getDataReport') }}",
                    data: function(d) {
                        d.tanggal = defaultTanggal;
                    }
                },
                columns: [{
                        data: 'nik',
                        name: 'nik',
                        render: data => `<span class="fw-bold text-secondary">${data}</span>`
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'kode_bagian',
                        name: 'kode_bagian',
                        render: data => data ? `<center>${data}</center>` : `<center>-</center>`
                    },
                    {
                        data: 'alasan_keluar',
                        name: 'alasan_keluar',
                        render: data => data ? data :
                            `<span class="text-muted fst-italic">Tidak ada alasan</span>`
                    },
                    {
                        data: 'tanggal_keluar',
                        name: 'tanggal_keluar',
                        render: function(data) {
                            return (!data || data === '0000-00-00') ? `<center>-</center>` :
                                `<center><span class="badge bg-light text-dark border shadow-sm">${moment(data).format('DD MMMM YYYY')}</span></center>`;
                        }
                    },
                ]
            });

            // Trigger saat dropdown diubah
            $(document).on("change", "#tanggalFilter", function() {
                defaultTanggal = $(this).val();
                table.draw();
            });

            // Trigger buat tombol Reset Filter
            $("#btnResetFilter").on('click', function() {
                $("#tanggalFilter").val('').trigger('change'); // Pakai trigger change biar Select2 ke-reset
                defaultTanggal = "";
                table.search('').draw();
            });
        });
    </script>
@endpush
