{{-- ================================================================
    HALAMAN: Monitoring Scan Makan
    DESKRIPSI: Dashboard untuk memantau realisasi scan makan
               vs quota pesanan per shift dan per kategori.
    DATA DARI: EcafeSeedapController@monitoringScan
    ================================================================ --}}
@extends('layouts.base')

@section('content')
    {{-- ============================================================
         STYLE: Semua CSS khusus halaman monitoring
         ============================================================ --}}
    <style>
        /* -- Header Halaman -- */
        .monitoring-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
            color: #fff;
        }
        .monitoring-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }
        .monitoring-header .sub-text {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        /* -- Card KPI Ringkasan -- */
        .kpi-card {
            border: none;
            border-radius: 12px;
            padding: 20px 22px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .kpi-card .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }
        .kpi-card .kpi-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }
        .kpi-card .kpi-value {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1;
        }

        /* Warna card KPI per jenis */
        .kpi-scan { background: linear-gradient(135deg, #e8f0fe, #d2e3fc); }
        .kpi-scan .kpi-icon { background: #1a73e8; color: #fff; }
        .kpi-scan .kpi-label { color: #1a73e8; }
        .kpi-scan .kpi-value { color: #174ea6; }

        .kpi-quota { background: linear-gradient(135deg, #e6f4ea, #ceead6); }
        .kpi-quota .kpi-icon { background: #34a853; color: #fff; }
        .kpi-quota .kpi-label { color: #34a853; }
        .kpi-quota .kpi-value { color: #1e8e3e; }

        .kpi-lebihan { background: linear-gradient(135deg, #fce8e6, #f8d7da); }
        .kpi-lebihan .kpi-icon { background: #ea4335; color: #fff; }
        .kpi-lebihan .kpi-label { color: #ea4335; }
        .kpi-lebihan .kpi-value { color: #c5221f; }

        .kpi-staff { background: linear-gradient(135deg, #e8eaf6, #d1d5f0); }
        .kpi-staff .kpi-icon { background: #5c6bc0; color: #fff; }
        .kpi-staff .kpi-label { color: #5c6bc0; }
        .kpi-staff .kpi-value { color: #3949ab; }

        .kpi-nonstaff { background: linear-gradient(135deg, #fff3e0, #ffe0b2); }
        .kpi-nonstaff .kpi-icon { background: #fb8c00; color: #fff; }
        .kpi-nonstaff .kpi-label { color: #fb8c00; }
        .kpi-nonstaff .kpi-value { color: #e65100; }

        .kpi-snack { background: linear-gradient(135deg, #fce4ec, #f8bbd0); }
        .kpi-snack .kpi-icon { background: #e91e63; color: #fff; }
        .kpi-snack .kpi-label { color: #e91e63; }
        .kpi-snack .kpi-value { color: #c2185b; }

        /* -- Bagian Monitoring per Shift -- */
        .shift-section {
            margin-bottom: 24px;
        }
        .shift-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }
        .shift-badge i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* -- Card Monitoring per Kategori -- */
        .monitor-card {
            border: none;
            border-radius: 12px;
            padding: 22px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left: 5px solid #e0e0e0;
        }
        .monitor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .monitor-card.status-aman {
            border-left-color: #34a853;
        }
        .monitor-card.status-over {
            border-left-color: #ea4335;
            background: linear-gradient(135deg, #fff5f5, #fff);
        }
        .monitor-card .card-kategori {
            font-size: 0.95rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
            color: #333;
        }
        .monitor-card .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .monitor-card .data-row:last-child {
            border-bottom: none;
            padding-top: 10px;
        }
        .monitor-card .data-label {
            font-size: 0.82rem;
            color: #666;
            font-weight: 500;
        }
        .monitor-card .data-value {
            font-size: 1rem;
            font-weight: 700;
            color: #333;
        }
        .monitor-card .selisih-over {
            color: #ea4335;
            font-weight: 800;
            font-size: 1.1rem;
        }
        .monitor-card .selisih-aman {
            color: #34a853;
            font-weight: 800;
            font-size: 1.1rem;
        }
        .monitor-card .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .monitor-card .badge-over {
            background: #fce8e6;
            color: #c5221f;
        }
        .monitor-card .badge-aman {
            background: #e6f4ea;
            color: #1e8e3e;
        }

        /* -- Bar Filter Tanggal & Kategori -- */
        .filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
    </style>

    <div class="container-fluid">

        {{-- ========================================================
             BAGIAN 1: Header Halaman
             ======================================================== --}}
        <div class="monitoring-header">
            <h4><i class="fa fa-chart-bar mr-2"></i> Monitoring Scan Makan</h4>
            <div class="sub-text">Dashboard real-time monitoring porsi makan karyawan</div>
        </div>

        {{-- ========================================================
             BAGIAN 2: Bar Filter (Tanggal & Kategori)
             Pengguna bisa memfilter data berdasarkan tanggal
             dan kategori (Staff / Non-Staff / Snack / Semua)
             ======================================================== --}}
        <div class="filter-bar d-flex justify-content-between align-items-end flex-wrap">
            <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-end" style="gap: 12px; flex-wrap: wrap;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1" style="font-size: 0.8rem;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1" style="font-size: 0.8rem;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1" style="font-size: 0.8rem;">Kategori</label>
                    <select name="kategori_filter" class="form-control">
                        <option value="semua" {{ $selectedKategori == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="staff" {{ $selectedKategori == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="non-staff" {{ $selectedKategori == 'non-staff' ? 'selected' : '' }}>Non-Staff</option>
                        <option value="non-staff-snack" {{ $selectedKategori == 'non-staff-snack' ? 'selected' : '' }}>Non-Staff Snack</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1" style="font-size: 0.8rem;">Shift</label>
                    <select name="shift_filter" class="form-control">
                        <option value="semua" {{ $selectedShift == 'semua' ? 'selected' : '' }}>Semua Shift</option>
                        <option value="1" {{ $selectedShift == '1' ? 'selected' : '' }}>Shift 1</option>
                        <option value="2" {{ $selectedShift == '2' ? 'selected' : '' }}>Shift 2</option>
                        <option value="3" {{ $selectedShift == '3' ? 'selected' : '' }}>Shift 3</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <i class="fa fa-filter mr-1"></i> Filter
                </button>
            </form>
            <a href="{{ route('ecafesedaap.monitoring-scan.export', request()->all()) }}" class="btn btn-success" style="height: 38px;">
                <i class="fa fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>

        {{-- ========================================================
             BAGIAN 3: Card KPI Ringkasan
             Menampilkan total scan, total quota, lebihan,
             dan jumlah scan per kategori (staff, non-staff, snack)
             Data berasal dari variabel $summary di Controller
             ======================================================== --}}
        <div class="row mb-4">
            {{-- Card: Total Scan --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-scan">
                    <div class="kpi-icon"><i class="fa fa-qrcode"></i></div>
                    <div class="kpi-label">Total Scan</div>
                    <div class="kpi-value">{{ $summary['total_scan'] }}</div>
                </div>
            </div>
            {{-- Card: Total Quota --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-quota">
                    <div class="kpi-icon"><i class="fa fa-clipboard-list"></i></div>
                    <div class="kpi-label">Total Quota</div>
                    <div class="kpi-value">{{ $summary['total_quota'] }}</div>
                </div>
            </div>
            {{-- Card: Total Sisa Lauk (Jumlah porsi yang belum dimakan) --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-quota">
                    <div class="kpi-icon"><i class="fa fa-utensils"></i></div>
                    <div class="kpi-label">Total Sisa Lauk</div>
                    <div class="kpi-value">{{ $summary['lebihan'] }}</div>
                </div>
            </div>
            {{-- Card: Jumlah Scan Staff --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-staff">
                    <div class="kpi-icon"><i class="fa fa-user-tie"></i></div>
                    <div class="kpi-label">Staff</div>
                    <div class="kpi-value">{{ $summary['per_kategori']['staff'] }}</div>
                </div>
            </div>
            {{-- Card: Jumlah Scan Non-Staff --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-nonstaff">
                    <div class="kpi-icon"><i class="fa fa-users"></i></div>
                    <div class="kpi-label">Non-Staff</div>
                    <div class="kpi-value">{{ $summary['per_kategori']['non-staff'] }}</div>
                </div>
            </div>
            {{-- Card: Jumlah Scan Snack --}}
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <div class="kpi-card kpi-snack">
                    <div class="kpi-icon"><i class="fa fa-cookie-bite"></i></div>
                    <div class="kpi-label">Snack</div>
                    <div class="kpi-value">{{ $summary['per_kategori']['non-staff-snack'] }}</div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             BAGIAN 4: Monitoring per Shift
             ======================================================== --}}
        <div class="monitoring-header" style="padding: 16px 22px;">
            <h4 style="font-size: 1.1rem;"><i class="fa fa-layer-group mr-2"></i> Monitoring per Shift</h4>
        </div>

        @if(count($monitoringPerCategory) > 0)
            {{-- Loop 1: Per Kategori --}}
            @foreach($monitoringPerCategory as $category => $shifts)
                <div class="category-section mb-4" style="background: rgba(255,255,255,0.5); padding: 20px; border-radius: 15px; border: 1px solid #edf2f7;">
                    {{-- Label Kategori --}}
                    <div class="d-flex align-items-center mb-3">
                        <div style="width: 4px; height: 24px; background: #1a73e8; border-radius: 2px; margin-right: 12px;"></div>
                        <h5 class="mb-0 font-weight-bold" style="color: #2d3748; letter-spacing: 0.5px;">
                            {{ strtoupper($category) }}
                        </h5>
                    </div>

                    <div class="row">
                        {{-- Loop 2: Per Shift di dalam Kategori ini --}}
                        @foreach($shifts as $shift => $item)
                            <div class="col-lg-4 col-md-6 mb-3">
                                {{-- Card Monitoring: 1 card = 1 kombinasi shift+kategori --}}
                                <div class="monitor-card {{ $item['status'] === 'OVER_QUOTA' ? 'status-over' : 'status-aman' }}">

                                    {{-- Baris atas: Nama Shift + Badge Status --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="card-kategori"><i class="fa fa-clock mr-1"></i>  <span style="font-size: 19gpx; font-weight: bold;">SHIFT {{ $shift }}</span></div>
                                    </div>

                                    {{-- Baris data: Quota Order --}}
                                    <div class="data-row">
                                        <span class="data-label">Quota Order</span>
                                        <span class="data-value">{{ $item['kuota_order'] }}</span>
                                    </div>

                                    {{-- Baris data: Total Scan --}}
                                    <div class="data-row">
                                        <span class="data-label">Total Scan</span>
                                        <span class="data-value">{{ $item['total_scan'] }}</span>
                                    </div>

                                    {{-- Baris data: Selisih (Sisa Lauk) --}}
                                    <div class="data-row">
                                        <span class="data-label">Sisa Lauk</span>
                                        <span class="{{ $item['selisih'] < 0 ? 'selisih-over' : 'selisih-aman' }}">
                                            {{ $item['selisih'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            {{-- Tampilan jika belum ada data pesanan --}}
            <div class="text-center py-5">
                <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada data pesanan untuk range tanggal ini.</p>
            </div>
        @endif

        {{-- ========================================================
             BAGIAN 5: Tabel Log Scan
             ======================================================== --}}
        <div class="card mt-4" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1a73e8, #4285f4); border: none; padding: 16px 20px;">
                <h5 class="mb-0 text-white font-weight-bold">
                    <i class="fa fa-list-alt mr-2"></i> History Scan Makan
                </h5>
                <div class="d-flex align-items-center" style="gap: 15px;">
                    <span class="badge badge-light" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 6px;">
                        @if($startDate == $endDate)
                            {{ date('d M Y', strtotime($startDate)) }}
                        @else
                            {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}
                        @endif
                    </span>

                    {{-- Filter Departemen khusus Tabel --}}
                    <form action="{{ url()->current() }}" method="GET" class="mb-0 d-flex align-items-center" style="gap: 10px;">
                        {{-- Hidden inputs to preserve other filters --}}
                        <input type="hidden" name="tanggal_mulai" value="{{ $startDate }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $endDate }}">
                        <input type="hidden" name="kategori_filter" value="{{ $selectedKategori }}">
                        <input type="hidden" name="shift_filter" value="{{ $selectedShift }}">
                        
                        <select name="departemen_filter" class="form-control form-control-sm" style="width: 150px; border-radius: 6px; border: none;" onchange="this.form.submit()">
                            <option value="semua" {{ $selectedDept == 'semua' ? 'selected' : '' }}>Semua Departemen</option>
                            @foreach($listDepartemen as $dept)
                                <option value="{{ $dept }}" {{ $selectedDept == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="table-kantin">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Departemen</th>
                            <th>Kategori</th>
                            <th>Shift</th>
                            <th>Tanggal</th>
                            <th>Waktu Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataScan as $key => $item)
                            <tr>
                                <td>{{ ($dataScan->currentPage() - 1) * $dataScan->perPage() + $key + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->nik }}</td>
                                <td>{{ $item->departemen ?? '-' }}</td>
                                <td><span class="badge badge-info">{{ $item->kategori }}</span></td>
                                <td><span class="badge badge-secondary">Shift {{ $item->shift }}</span></td>
                                <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ date('H:i:s', strtotime($item->waktu)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $dataScan->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ============================================================
     SCRIPT: Inisialisasi DataTables pada tabel log scan
     ============================================================ --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables jika belum aktif
            if (!$.fn.DataTable.isDataTable('#table-kantin')) {
                $('#table-kantin').DataTable({
                    responsive: true,
                    paging: false, // Disable DataTables pagination since we use Laravel Pagination
                    searching: true,
                    info: false,
                    order: [[5, 'desc']], 
                });
            }
        });
    </script>
@endpush
