@extends('layouts.base')

@push('styles')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* Samakan palette dengan index.blade.php */
        .live-console-card {
            border-top: 5px solid #3699FF !important;
        }

        /* Desain Tab Model Folder */
        .folder-tabs {
            display: flex;
            margin-bottom: -1px;
            /* Overlap dengan border card */
            padding-left: 20px;
        }

        .folder-tab {
            padding: 10px 25px;
            background-color: #f3f6f9;
            color: #7e8299;
            font-weight: 600;
            border: 1px solid #ebedf3;
            border-bottom: none;
            cursor: pointer;
            position: relative;
            z-index: 1;
            /* Lengkung di kiri atas dan kanan atas, sudut 90 di bawah */
            border-radius: 12px 12px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .folder-tab:hover {
            background-color: #e4e6ef;
        }

        .folder-tab.active {
            background-color: #ffffff;
            color: #3699FF;
            border-top: 3px solid #3699FF;
            z-index: 3;
            box-shadow: 0 -3px 5px rgba(0, 0, 0, 0.02);
        }

        /* Desain Filter Select */
        .filter-select {
            border: 2px solid #ebedf3;
            border-radius: 6px;
            color: #3f4254;
            font-weight: 500;
        }

        .filter-select:focus {
            border-color: #3699FF;
            box-shadow: none;
        }

        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            vertical-align: middle !important;
            background-color: #f3f6f9;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                <!-- Header & Filter Card -->
                <div class="card card-custom mb-6 shadow-sm border-0" style="border-radius: 12px;">
                    <!-- Card Header (Judul) -->
                    <div class="card-header border-bottom pt-6 pb-5">
                        <div class="d-flex align-items-center">
                            <div class="mr-4">
                                <div class="symbol symbol-50 symbol-light-primary shadow-sm">
                                    <span class="symbol-label">
                                        <!-- Icon -->
                                        <span class="svg-icon svg-icon-xl svg-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M4,7 L20,7 L20,19.5 C20,20.3284271 19.3284271,21 18.5,21 L5.5,21 C4.67157288,21 4,20.3284271 4,19.5 L4,7 Z M10,10 C9.44771525,10 9,10.4477153 9,11 C9,11.5522847 9.44771525,12 10,12 L14,12 C14.5522847,12 15,11.5522847 15,11 C15,10.4477153 14.5522847,10 14,10 L10,10 Z"
                                                        fill="#000000" />
                                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20"
                                                        height="4" rx="1" />
                                                </g>
                                            </svg>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-weight-bolder text-dark mb-1">Report Izin Keluar</h3>
                                <span class="text-muted font-weight-bold font-size-sm">Monitoring riwayat keluar masuk
                                    karyawan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body (Filter) -->
                    <div class="card-body p-5">
                        <div class="bg-light p-4 rounded shadow-sm border">
                            <h6 class="text-muted font-weight-bold mb-4"><i class="flaticon2-magnifier-tool mr-2"></i>
                                Filter Data Report</h6>
                            <div class="row align-items-center">
                                <div class="col-lg-3 mb-3 mb-lg-0">
                                    <select id="filterDivisi" class="form-control filter-select shadow-sm" style="height: 42px;">
                                        <option value="">-- Semua Divisi --</option>
                                        @foreach ($divisi as $div)
                                            <option value="{{ $div }}">{{ $div }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-3 mb-lg-0">
                                    <select id="filterStatus" class="form-control filter-select shadow-sm" style="height: 42px;">
                                        <option value="">-- Semua Status --</option>
                                        @foreach ($status as $stat)
                                            <option value="{{ $stat }}">{{ $stat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-3 mb-lg-0">
                                    <input type="date" id="filterTanggal" class="form-control shadow-sm" style="height: 42px;" data-bs-toggle="tooltip" title="Filter Berdasarkan Tanggal">
                                </div>
                                <div class="col-lg-2 mt-auto">
                                    <button id="btnTerapkan" class="btn btn-primary font-weight-bold w-100 shadow-sm"
                                        style="background-color: #3699FF; border: none; height: 42px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                                <div class="col-lg-1 mt-auto">
                                    <!-- Jika btn-soft-danger tidak jalan di Metronic, ini styling backupnya -->
                                    <button class="btn btn-light-danger w-100 shadow-sm" id="btnResetFilter"
                                        data-bs-toggle="tooltip" title="Reset Semua Filter" style="height: 42px;">
                                        <i class="flaticon2-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Tab Folder -->
                <div class="folder-tabs">
                    <!-- Beri class active untuk tab yang sedang dibuka -->
                    <div class="folder-tab active" id="tab-today" data-tab="today">
                        <i class="flaticon2-calendar-1 mr-2"></i> Hari Ini
                    </div>
                    <div class="folder-tab" id="tab-all" data-tab="all">
                        <i class="flaticon2-layers-1 mr-2"></i> Semua Riwayat
                    </div>
                </div>

                <!-- Bagian Tabel -->
                <div class="card card-custom shadow-sm border-0 live-console-card" style="border-top-left-radius: 0;">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tableIzinKeluar"
                                class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 20%;">Tanggal</th>
                                        <th style="width: 20%;">Nama Lengkap</th>
                                        <th style="width: 10%;">NIK</th>
                                        <th style="width: 10%;">Divisi</th>
                                        <th style="width: 10%;">Jam Keluar</th>
                                        <th style="width: 10%;">Jam Masuk</th>
                                        <th style="width: 10%;">Telat (Menit)</th>
                                        <th style="width: 10%;">Status</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            let activeTab = 'today';

            let table = $("#tableIzinKeluar").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: "{{ route('izin-keluar.report.getData') }}",
                    type: "GET",
                    data: function(d) {
                        d.tab = activeTab;
                        d.divisi = $('#filterDivisi').val();
                        d.status = $('#filterStatus').val();
                        d.tanggal = $('#filterTanggal').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY') : '-';
                        }
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'divisi',
                        name: 'divisi'
                    },
                    {
                        data: 'jam_keluar',
                        name: 'jam_keluar',
                        render: function(data) {
                            if (!data) return '-';
                            return moment(data).format('HH.mm');
                        }
                    },
                    {
                        data: 'jam_masuk',
                        name: 'jam_masuk',
                        render: function(data) {
                            if (!data) return '-';
                            return moment(data).format('HH.mm');
                        }
                    },
                    {
                        data: 'menit_terlambat',
                        name: 'menit_terlambat',
                        render: function(data) {
                            return data !== null ? data + ' Menit' : '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (row.jam_masuk === null) {
                                return '<span class="badge badge-warning">Belum Kembali</span>';
                            } else if (row.status === 'Tepat Waktu') {
                                return '<span class="badge badge-success">Tepat Waktu</span>';
                            } else {
                                return '<span class="badge badge-danger">Terlambat</span>';
                            }
                        }
                    }
                ]
            });

            $('.folder-tab').on('click', function() {
                $('.folder-tab').removeClass('active');
                $(this).addClass('active');
                activeTab = $(this).data('tab');
                table.ajax.reload();
            });

            $('#btnTerapkan').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
                $(this).blur();
            });

            $('#btnResetFilter').on('click', function(e) {
                e.preventDefault();
                $('#filterDivisi').val('');
                $('#filterStatus').val('');
                $('#filterTanggal').val('');
                table.ajax.reload();
                $(this).blur();
            });
        });
    </script>
@endpush
