@extends('layouts.base')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/velzon/libs/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/velzon/libs/flatpickr/themes/material_blue.css') }}">

<style>
    .hd-title { color: #4a148c; font-weight: 700; font-size: 1.6rem; margin-bottom: 1rem; }
    .hd-card { background: #fff; border-radius: 6px; padding: 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
    .hd-card h5 { font-weight: 700; }
    .hd-stat { text-align: center; padding: 1rem; border-radius: 6px; display: flex; flex-direction: column; justify-content: center; }
    .hd-stat .hd-stat-value { font-size: 1.8rem; font-weight: 700; }
    .hd-stat .hd-stat-label { font-size: 1.1rem; color: #666; display: block; width: 100%; }
    .hd-stat-blue { background: #e3f2fd; color: #1565c0; }
    .hd-stat-green { background: #e8f5e9; color: #2e7d32; }
    .hd-stat-orange { background: #fff3e0; color: #e65100; }
    .hd-stat-sm { padding: .55rem .4rem; }
    .hd-stat-sm .hd-stat-value { font-size: 1.25rem; }
    .hd-stat-sm .hd-stat-label { font-size: .7rem; }
    .hd-gender-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: .8rem .6rem; height: 100%; }
    .hd-gender-title { font-size: .9rem; font-weight: 700; color: #333; margin-bottom: .5rem; text-align: center; }
    .hd-gender-body { display: flex; align-items: center; justify-content: space-around; padding: .25rem 0; }
    .hd-gender-half { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .35rem; }
    .hd-gender-divider { width: 1px; background: #bdbdbd; align-self: stretch; }
    .hd-gender-icon { font-size: 2.4rem; line-height: 1; }
    .hd-gender-stats { display: flex; align-items: baseline; gap: .4rem; }
    .hd-gender-pct-big { font-size: 2rem; font-weight: 800; color: #1a237e; line-height: 1; }
    .hd-gender-count-inline { font-size: 2.15rem; color: #555; font-weight: 600; line-height: 1; }
    .hd-gender-count { font-size: .85rem; color: #444; text-align: center; margin-top: .5rem; line-height: 1.3; font-weight: 600; }
    .hd-table { font-size: .82rem; }
    .hd-table th { background: #f5f5f5; white-space: nowrap; }
    .hd-table td { vertical-align: middle; }
    .hd-pagination .page-link { padding: .2rem .5rem; font-size: .82rem; }
    .hd-form-label { font-weight: 600; font-size: .82rem; margin-bottom: .2rem; }
    .hd-card-toggle { display: flex; align-items: center; gap: .4rem; }
    .hd-chevron {
        display: inline-block;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 7px solid #4a148c;
        transition: transform .2s;
    }
    .hd-card-toggle.collapsed .hd-chevron { transform: rotate(-90deg); }
    #employeeTypeChart { min-height: 300px; }
    #employeeInChart { min-height: 320px; }
    #distribusiUsiaChart { min-height: 320px; }
    #employeeOutChart { min-height: 320px; }
    select[multiple] { min-height: auto; }
    select[multiple] option:checked {
        background: #4a148c linear-gradient(0deg, #4a148c 0%, #4a148c 100%);
        color: #fff;
    }
    .hd-multi-select { position: relative; }
    .hd-ms-btn {
        width: 100%;
        text-align: left;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: .25rem .5rem;
        font-size: .82rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #495057;
        cursor: pointer;
    }
    .hd-ms-btn:hover { border-color: #4a148c; }
    .hd-ms-caret { font-size: .9rem; color: #4a148c; }
    .hd-ms-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        margin-top: 2px;
        max-height: 320px;
        overflow: hidden;
        flex-direction: column;
    }
    .hd-multi-select.open .hd-ms-dropdown { display: flex; }
    .hd-ms-search { margin: 6px; }
    .hd-ms-actions {
        display: flex;
        gap: 4px;
        padding: 0 6px 6px;
        border-bottom: 1px solid #eee;
    }
    .hd-ms-action {
        flex: 1;
        font-size: .75rem;
        padding: .2rem .4rem;
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
    }
    .hd-ms-action:hover { background: #4a148c; color: #fff; border-color: #4a148c; }
    .hd-ms-list {
        overflow-y: auto;
        max-height: 240px;
        padding: 4px 0;
    }
    .hd-ms-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        font-size: .82rem;
        cursor: pointer;
        user-select: none;
    }
    .hd-ms-item:hover { background: #f3e5f5; }
    .hd-ms-item input { margin: 0; cursor: pointer; }
    .hd-section { position: relative; padding: 1rem; background: #fafafa; border-radius: 8px; }
    .hd-section:fullscreen { background: #fff; padding: 2rem; }
    .hd-section:fullscreen .row { display: flex; flex-wrap: wrap; }
    .hd-section:fullscreen .col-md-4 { flex: 0 0 25%; max-width: 25%; }
    .hd-section:fullscreen #employeeTypeChart { min-height: 70vh; }
    .hd-section:fullscreen .hd-stat-value { font-size: 4rem; }
    .hd-fs-btn {
        position: absolute; top: 0.75rem; right: 0.75rem; z-index: 10;
        background: rgba(74, 20, 140, 0.08); border: 1px solid rgba(74, 20, 140, 0.2);
        color: #4a148c; border-radius: 4px; padding: 0.15rem 0.5rem;
        font-size: 0.9rem; cursor: pointer; line-height: 1;
    }
    .hd-fs-btn:hover { background: rgba(74, 20, 140, 0.15); }
    .hd-section:fullscreen .hd-fs-btn { top: 1.5rem; right: 1.5rem; font-size: 1.1rem; padding: 0.3rem 0.7rem; }
    .hd-tab-nav { border-bottom: 2px solid #e0e0e0; margin-bottom: 1rem; }
    .hd-tab-btn {
        color: #555; font-weight: 600; border: none; border-bottom: 3px solid transparent;
        padding: .65rem 1.25rem; background: transparent; cursor: pointer;
        transition: all .15s ease;
    }
    .hd-tab-btn:hover { color: #4a148c; background: rgba(74, 20, 140, 0.05); }
    .hd-tab-btn.active { color: #4a148c; border-bottom-color: #4a148c; background: rgba(74, 20, 140, 0.05); }
    .hd-tab-btn i { margin-right: .35rem; }
    #hdFullscreenWrap:fullscreen {
        background: #fafafa;
        padding: 1.5rem;
        overflow: auto;
    }
    #hdFullscreenWrap:fullscreen .hd-section { background: transparent; padding: 0; }
    #hdFullscreenWrap:fullscreen .hd-stat-value { font-size: 3rem; }
    #btnToggleAutoCycle.btn-success { background: #2e7d32; border-color: #2e7d32; color: #fff; }
    #btnToggleAutoCycle.btn-success:hover { background: #1b5e20; border-color: #1b5e20; color: #fff; }
    #btnToggleAutoCycle i.la-refresh { animation: hd-spin 2s linear infinite; }
    #btnToggleAutoCycle.btn-outline-secondary i.la-refresh { animation: none; }
    @keyframes hd-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<div class="container-fluid">
    <h1 class="hd-title">
        @if(($typeKaryawanMode ?? null) === 'mitra_kerja')
            HR DASHBOARD Mitra Kerja
        @elseif(($typeKaryawanMode ?? null) === 'BAS')
            HR DASHBOARD BAS
        @else
            HR Dashboard
        @endif
    </h1>
{{-- 
    @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
        @php
            $modeLabel = $typeKaryawanMode === 'mitra_kerja'
                ? 'Mode Mitra Kerja — hanya menampilkan Tipe Karyawan KMJ & Fortuna'
                : 'Mode BAS — hanya menampilkan Tipe Karyawan Staff & Non Staff';
        @endphp
        <div class="alert alert-warning d-flex align-items-center" role="alert" style="font-size:.85rem; padding:.5rem .75rem;">
            <i class="la la-filter mr-2"></i>
            <div>
                <strong>{{ $modeLabel }}.</strong>
                Pilihan di dropdown filter Tipe Karyawan sudah dikunci sesuai mode ini.
            </div>
        </div>
    @endif --}}

    {{-- FILTER --}}
    <div class="hd-card" id="filterDataCard">
        <h5 class="hd-card-toggle" data-target="#filterCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Filter Data
        </h5>
        <div class="collapse show" id="filterCollapse">
            <form id="filterForm" class="mt-2">
            <div class="row">
                {{-- Rentang Data (point-in-time headcount) --}}
                <div class="col-md-2 mb-2">
                    <label class="hd-form-label">
                        Rentang Data
                        <small class="text-muted" style="font-weight:400;">— Tgl Masuk &le; end (aktif + leavers)</small>
                    </label>
                    <input type="text" class="form-control form-control-sm flatpickr-range" name="rentang_data_range" id="rentang_data_range" placeholder="Pilih rentang tanggal" autocomplete="off">
                </div>

                {{-- Tgl Masuk Range (untuk chart joiners/in) --}}
                <div class="col-md-2 mb-2">
                    <label class="hd-form-label">
                        Tanggal Masuk
                        <small class="text-muted" style="font-weight:400;">— range chart</small>
                    </label>
                    <input type="text" class="form-control form-control-sm flatpickr-range" name="tgl_masuk_range" id="tgl_masuk_range" placeholder="Pilih rentang tanggal" autocomplete="off">
                </div>

                {{-- Tgl Keluar Range (Valid From) --}}
                <div class="col-md-2 mb-2">
                    <label class="hd-form-label">Tanggal Keluar</label>
                    <input type="text" class="form-control form-control-sm flatpickr-range" name="tgl_keluar_range" id="tgl_keluar_range" placeholder="Pilih rentang tanggal" autocomplete="off">
                </div>

                {{-- Departmen (Dropdown Multi-Select) --}}
                <div class="col-md-3 mb-2">
                    <label class="hd-form-label">Departemen</label>
                    <div class="hd-multi-select" data-target="departmen" data-placeholder="-- Semua Departemen --">
                        <button type="button" class="hd-ms-btn">
                            <span class="hd-ms-label">-- Semua Departemen --</span>
                            <span class="hd-ms-caret">▾</span>
                        </button>
                        <div class="hd-ms-dropdown">
                            <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari departemen...">
                            <div class="hd-ms-actions">
                                <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                                <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                            </div>
                            <div class="hd-ms-list">
                                @foreach ($departments as $d)
                                    <label class="hd-ms-item">
                                        <input type="checkbox" name="departmen[]" value="{{ $d }}">
                                        <span>{{ $d }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub Departmen (Dropdown Multi-Select) --}}
                <div class="col-md-3 mb-2">
                    <label class="hd-form-label">Sub Departemen</label>
                    <div class="hd-multi-select" data-target="sub_departmen" data-placeholder="-- Semua Sub Departemen --">
                        <button type="button" class="hd-ms-btn">
                            <span class="hd-ms-label">-- Semua Sub Departemen --</span>
                            <span class="hd-ms-caret">▾</span>
                        </button>
                        <div class="hd-ms-dropdown">
                            <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari sub departmen...">
                            <div class="hd-ms-actions">
                                <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                                <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                            </div>
                            <div class="hd-ms-list">
                                @foreach ($subDepartments as $sd)
                                    <label class="hd-ms-item">
                                        <input type="checkbox" name="sub_departmen[]" value="{{ $sd }}">
                                        <span>{{ $sd }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tipe Karyawan (Dropdown Multi-Select) --}}
                <div class="col-md-4 mb-2">
                    <label class="hd-form-label">Tipe Karyawan</label>
                    <div class="hd-multi-select" data-target="tipe_karyawan" data-placeholder="-- Semua Tipe Karyawan --">
                        <button type="button" class="hd-ms-btn">
                            <span class="hd-ms-label">-- Semua Tipe Karyawan --</span>
                            <span class="hd-ms-caret">▾</span>
                        </button>
                        <div class="hd-ms-dropdown">
                            <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari tipe...">
                            <div class="hd-ms-actions">
                                <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                                <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                            </div>
                            <div class="hd-ms-list">
                                @foreach ($types as $t)
                                    <label class="hd-ms-item">
                                        <input type="checkbox" name="tipe_karyawan[]" value="{{ $t }}">
                                        <span>{{ $t }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-2 d-flex align-items-end" style="gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReset">Reset</button>
                    <button type="button" class="btn btn-success btn-sm ms-auto" id="btnExport">Export CSV</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    {{-- WT&O Filter (di bawah Filter Data, hanya muncul saat tab WT&O aktif) --}}
    @include('hr.dashboard.partials.wto-filter')

    {{-- TAB NAVIGATION --}}
    <ul class="nav nav-tabs hd-tab-nav" id="hdDashboardTabs" role="tablist" style="display:none;">
        <li class="nav-item" role="presentation">
            <button class="nav-link hd-tab-btn active" id="hdTabMasterBtn" data-target="hdStatsSection" type="button" role="tab">
                <i class="la la-users"></i> Master Employee Dashboard
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link hd-tab-btn" id="hdTabWtoBtn" data-target="hdWtoSection" type="button" role="tab">
                <i class="la la-clock-o"></i> Working Time &amp; Overtime Dashboard
            </button>
        </li>
        <li class="nav-item ms-auto align-self-center" style="padding-right:.75rem;">
            <button type="button" id="btnToggleAutoCycle"
                    class="btn btn-sm btn-outline-secondary"
                    style="font-size:.78rem; display:none;"
                    title="Klik untuk ON/OFF auto-cycle antara tab">
                <i class="la la-refresh"></i> Auto-cycle: <span id="hdTabAutoStatusText">OFF</span>
            </button>
        </li>
    </ul>

    {{-- FULLSCREEN WRAPPER: contains BOTH tab contents so fullscreen stays during auto-cycle --}}
    <div id="hdFullscreenWrap" style="display:none;">
        {{-- TAB CONTENT: Master Employee (charts preserved) --}}
        <div class="hd-tab-content" id="hdStatsSection" style="display:none;">
            <div class="hd-section">
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <h5 class="mb-0" style="color:#4a148c; font-weight:700;">Master Employee Dashboard</h5>
                    <button type="button" class="hd-fs-btn" data-target="hdFullscreenWrap" title="Fullscreen / Auto-cycle">
                        <span class="hd-fs-icon-expand">&#x26F6;</span>
                    </button>
                </div>

                {{-- STATS (existing content) --}}
                <div class="row" id="statsRow" style="display:flex; align-items:stretch;">
                    <div class="col-md-3 mb-3">
                        <div class="hd-stat hd-stat-blue h-100">
                            <div class="hd-stat-value" id="statTotal">0</div>
                            <div class="hd-stat-label">Total HeadCount</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="hd-stat hd-stat-green h-100">
                            <div class="hd-stat-value" id="statNew">0</div>
                            <div class="hd-stat-label">Permanent Employee</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="hd-stat hd-stat-orange h-100">
                            <div class="hd-stat-value" id="statLeavers">0</div>
                            <div class="hd-stat-label">Contract &amp; Employee</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="hd-gender-card h-100">
                            <div class="hd-gender-title">Gender Distribution</div>
                            <div class="hd-gender-body">
                                <div class="hd-gender-half">
                                    <span class="hd-gender-icon">&#128104;</span>
                                    <div class="hd-gender-stats">
                                        <span class="hd-gender-pct-big" id="statGenderLPct">0%</span>
                                        <span class="hd-gender-count-inline">|</span>
                                        <span class="hd-gender-count-inline" id="statGenderL">0</span>
                                    </div>
                                </div>
                                <div class="hd-gender-divider"></div>
                                <div class="hd-gender-half">
                                    <span class="hd-gender-icon">&#128105;</span>
                                    <div class="hd-gender-stats">
                                        <span class="hd-gender-pct-big" id="statGenderPPct">0%</span>
                                        <span class="hd-gender-count-inline">|</span>
                                        <span class="hd-gender-count-inline" id="statGenderP">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="hd-gender-count">
                                Total: <span id="statGenderTotal">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHART 1 + 2 --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="hd-card" id="chartCard">
                            <h5 style="display:flex; align-items:center; gap:.4rem;">
                                <span class="hd-chevron" style="visibility:hidden;"></span> Employee Type Distribution
                            </h5>
                            <div id="employeeTypeChart"></div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <div class="hd-card" id="lineChartCard">
                            <h5 style="display:flex; align-items:center; gap:.4rem;">
                                <span class="hd-chevron" style="visibility:hidden;"></span> Employee In
                            </h5>
                            <div id="employeeInChart"></div>
                        </div>
                    </div>
                </div>

                {{-- CHART 3 + 4 --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="hd-card" id="ageChartCard">
                            <h5 style="display:flex; align-items:center; gap:.4rem;">
                                <span class="hd-chevron" style="visibility:hidden;"></span> Age Distribution
                            </h5>
                            <div id="distribusiUsiaChart"></div>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <div class="hd-card" id="employeeOutChartCard">
                            <h5 style="display:flex; align-items:center; gap:.4rem;">
                                <span class="hd-chevron" style="visibility:hidden;"></span> Employee Out
                            </h5>
                            <div id="employeeOutChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB CONTENT: Working Time & Overtime (WT&O) - stats only, in fullscreen wrap --}}
        @include('hr.dashboard.partials.wto-stats')
    </div>

    {{-- WT&O Extras (filter + data table) - OUTSIDE fullscreen wrap --}}
    @include('hr.dashboard.partials.wto-extras')

    {{-- DATA TABLE --}}
    <div class="hd-card" id="dataCard" style="display:none;">
        <h5 class="hd-card-toggle" data-target="#tableCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Data Karyawan
        </h5>
        <div class="collapse show" id="tableCollapse">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div></div>
                <div>
                    <span class="text-muted" style="font-size:.82rem;">Tampilkan</span>
                    <select id="perPage" class="form-control form-control-sm d-inline-block" style="width:auto;">
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    <span class="text-muted" style="font-size:.82rem;">per halaman</span>
                </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm hd-table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Tgl Lahir</th>
                        <th>Tgl Masuk</th>
                        <th>Valid From</th>
                        <th>Tgl Keluar</th>
                        <th>Departmen</th>
                        <th>Sub Departmen</th>
                        <th>Section</th>
                        <th>Tipe Karyawan</th>
                        <th>Jabatan</th>
                        <th>Jenis Kelamin</th>
                        <th>Work Status</th>
                        <th>Status Nikah</th>
                        <th>Aktif</th>
                        <th>Aktif (Rentang Data)</th>
                        <th>Distribusi Usia</th>
                    </tr>
                </thead>
                <tbody id="dataTbody">
                    <tr><td colspan="17" class="text-center text-muted">Silakan terapkan filter untuk melihat data.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2" id="tbPagination">
            <small class="text-muted" id="pageInfo"></small>
            <div id="pageNumbers"></div>
        </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('assets/velzon/libs/flatpickr/l10n/id.js') }}"></script>
<script src="{{ asset('assets/velzon/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    let currentPage = 1;

    // Toggle collapse untuk card header
    $(document).on('click', '.hd-card-toggle', function () {
        let $this = $(this);
        let target = $this.data('target');
        $(target).collapse('toggle');
        $this.toggleClass('collapsed');
    });

    // === Multi-Select Dropdown ===
    // Toggle buka/tutup dropdown
    $(document).on('click', '.hd-ms-btn', function (e) {
        e.stopPropagation();
        let $wrap = $(this).closest('.hd-multi-select');
        // Tutup yang lain
        $('.hd-multi-select').not($wrap).removeClass('open');
        $wrap.toggleClass('open');
        if ($wrap.hasClass('open')) {
            $wrap.find('.hd-ms-search').focus();
        }
    });

    // Klik di luar → tutup
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.hd-multi-select').length) {
            $('.hd-multi-select').removeClass('open');
        }
    });

    // Search filter
    $(document).on('keyup', '.hd-ms-search', function () {
        let q = $(this).val().toLowerCase();
        $(this).closest('.hd-multi-select').find('.hd-ms-item').each(function () {
            let txt = $(this).text().toLowerCase();
            $(this).toggle(txt.indexOf(q) > -1);
        });
    });

    // Pilih semua / kosongkan
    $(document).on('click', '.hd-ms-action', function () {
        let $wrap = $(this).closest('.hd-multi-select');
        let action = $(this).data('action');
        $wrap.find('.hd-ms-item').each(function () {
            // Hanya toggle yang visible (sesuai search)
            if ($(this).is(':visible')) {
                let $cb = $(this).find('input[type=checkbox]');
                $cb.prop('checked', action === 'all');
            }
        });
        updateMsLabel($wrap);
    });

    // Update label saat checkbox berubah
    $(document).on('change', '.hd-ms-item input[type=checkbox]', function () {
        updateMsLabel($(this).closest('.hd-multi-select'));
    });

    function updateMsLabel($wrap) {
        let checked = $wrap.find('.hd-ms-item input[type=checkbox]:checked');
        let $label = $wrap.find('.hd-ms-label');
        let placeholder = $wrap.data('placeholder');
        if (checked.length === 0) {
            $label.text(placeholder);
        } else if (checked.length === 1) {
            $label.text(checked.first().val());
        } else {
            $label.text(checked.length + ' dipilih');
        }
    }

    // Inisialisasi label awal (kosong)
    $('.hd-multi-select').each(function () {
        updateMsLabel($(this));
    });

    // === Mode Tipe Karyawan (?type_karyawan=mitra_kerja | BAS) ===
    // Auto-select semua checkbox Tipe Karyawan yang tersedia di dropdown
    // (karena controller sudah memfilter list ke allowed values saja).
    @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
        function applyTipeKaryawanModePreselect() {
            $('input[name="tipe_karyawan[]"]').prop('checked', true);
            $('input[name="wto_tipe_karyawan[]"]').prop('checked', true);
            $('.hd-multi-select').each(function () {
                updateMsLabel($(this));
            });
        }
        applyTipeKaryawanModePreselect();
    @endif

    // Ambil nilai checkbox untuk filter
    function getMultiSelectValues(target) {
        return $('input[name="' + target + '[]"]:checked').map(function () {
            return this.value;
        }).get();
    }

    // Fullscreen toggle untuk section
    $(document).on('click', '.hd-fs-btn', function () {
        let targetId = $(this).data('target');
        let el = document.getElementById(targetId);
        if (!el) return;
        if (!document.fullscreenElement) {
            (el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen).call(el);
        } else {
            (document.exitFullscreen || document.webkitExitFullscreen || document.msExitFullscreen).call(document);
        }
    });

    // Resize chart saat keluar/masuk fullscreen
    document.addEventListener('fullscreenchange', function () {
        if (employeeTypeChart) {
            setTimeout(function () { employeeTypeChart.updateOptions({}); }, 200);
        }
    });

    // Init flatpickr untuk semua date input
    flatpickr('.flatpickr-date', {
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
    });

    // Init flatpickr range untuk Rentang Data (point-in-time snapshot)
    const rentangDataRange = flatpickr('#rentang_data_range', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });

    // Init flatpickr range untuk Tgl Masuk
    const tglMasukRange = flatpickr('#tgl_masuk_range', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });

    // Init flatpickr range untuk Tgl Keluar
    const tglKeluarRange = flatpickr('#tgl_keluar_range', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, c => (
            { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]
        ));
    }

    function getFilterParams() {
        let p = {
            page: currentPage,
            per_page: $('#perPage').val(),
            departmen: getMultiSelectValues('departmen'),
            sub_departmen: getMultiSelectValues('sub_departmen'),
            tipe_karyawan: getMultiSelectValues('tipe_karyawan'),
            @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            type_karyawan: @json($typeKaryawanMode),
            @endif
        };

        // Bersihkan array kosong
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;

        // Parse Rentang Data:
        //   end_date = snapshot untuk karyawan Aktif (Tgl Masuk <= end_date)
        //   start_date = cutoff untuk leavers (Valid From <= start_date)
        let rdRange = $('#rentang_data_range').val();
        if (rdRange) {
            let parts = rdRange.split(/\s+to\s+|\s+-\s+/);
            if (parts.length === 2) {
                p.rentang_data_from = parts[0].trim();
                p.rentang_data_to   = parts[1].trim();
            } else if (parts.length === 1 && rdRange) {
                p.rentang_data_to = rdRange.trim();
            }
        }

        // Parse Tgl Masuk range
        let tmRange = $('#tgl_masuk_range').val();
        if (tmRange) {
            let parts = tmRange.split(/\s+to\s+|\s+-\s+/);
            if (parts.length === 2) {
                p.tgl_masuk_from = parts[0].trim();
                p.tgl_masuk_to   = parts[1].trim();
            } else if (parts.length === 1 && tmRange) {
                p.tgl_masuk_from = tmRange.trim();
            }
        }

        // Parse Tgl Keluar range (Valid From, hanya untuk karyawan Aktif = N)
        let tkRange = $('#tgl_keluar_range').val();
        if (tkRange) {
            let parts = tkRange.split(/\s+to\s+|\s+-\s+/);
            if (parts.length === 2) {
                p.tgl_keluar_from = parts[0].trim();
                p.tgl_keluar_to   = parts[1].trim();
            } else if (parts.length === 1 && tkRange) {
                p.tgl_keluar_from = tkRange.trim();
            }
        }

        return p;
    }

    function loadData() {
        let params = getFilterParams();
        $.get("{{ url('/hr/hrdashboard/data') }}", params, function (res) {
            // Stats
            $('#statTotal').text(res.total_active || 0);
            $('#statNew').text(res.karyawan_tetap || 0);
            $('#statLeavers').text(res.karyawan_kontrak || 0);

            // Gender Count
            $('#statGenderL').text(res.gender_laki || 0);
            $('#statGenderP').text(res.gender_perempuan || 0);
            $('#statGenderTotal').text(res.gender_total || 0);
            $('#statGenderLPct').text((res.gender_laki_pct || 0) + '%');
            $('#statGenderPPct').text((res.gender_perempuan_pct || 0) + '%');

            $('#hdFullscreenWrap').show();
            $('#hdStatsSection').show();
            $('#hdDashboardTabs').show();
            $('#btnToggleAutoCycle').show();
            $('#dataCard').show();
            // WT&O hidden by default — user switches via tab
            $('#hdWtoSection').hide();

            // Initial tab state
            if (typeof window.switchDashboardTab === 'function') {
                window.switchDashboardTab('hdStatsSection');
            }

            // Table
            renderTable(res);
            renderPagination(res);

            // Chart
            renderEmployeeTypeChart(res);
            renderEmployeeInChart(res);
            renderEmployeeOutChart(res);
            renderDistribusiUsiaChart(res);
        });
    }

    let employeeTypeChart = null;
    function renderEmployeeTypeChart(res) {
        // Pakai tipe_distribution dari server (sudah disesuaikan dgn mode).
        // Fallback ke karyawan_staff / karyawan_non_staff utk backward compat.
        let labels, data;
        if (res.tipe_distribution && Array.isArray(res.tipe_distribution.labels)) {
            labels = res.tipe_distribution.labels;
            data   = res.tipe_distribution.data || [];
        } else {
            labels = ['Staff', 'Non Staff'];
            data   = [res.karyawan_staff || 0, res.karyawan_non_staff || 0];
        }
        const colors = ['#4a148c', '#e65100', '#1e88e5', '#43a047', '#e53935', '#8e24aa'];
        let options = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            series: [{
                name: 'Jumlah',
                data: data
            }],
            xaxis: {
                categories: labels
            },
            yaxis: {
                title: { text: 'Jumlah Karyawan' }
            },
            colors: colors.slice(0, labels.length),
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '13px', colors: ['#000'] }
            },
            title: {
                align: 'left',
                style: { fontSize: '14px', color: '#4a148c' }
            }
        };

        if (employeeTypeChart) {
            employeeTypeChart.updateOptions({
                series: [{ name: 'Jumlah', data: data }],
                xaxis: { categories: labels },
                colors: colors.slice(0, labels.length)
            });
        } else {
            employeeTypeChart = new ApexCharts(document.querySelector('#employeeTypeChart'), options);
            employeeTypeChart.render();
        }
    }

    let employeeInChart = null;
    function renderEmployeeInChart(res) {
        // emp_in dari server: { years: [...], 'KMJ': [...], 'Fortuna': [...] }
        // atau { years: [...], staff: [...], non_staff: [...] } — dinamis sesuai mode.
        let empIn   = res.emp_in || { years: [] };
        let years   = empIn.years || [];
        let tipeKeys = Object.keys(empIn).filter(k => k !== 'years');
        const colors = ['#1e88e5', '#fb8c00', '#43a047', '#e53935', '#8e24aa', '#00acc1'];

        let series = tipeKeys.map((k) => ({
            name: k,
            data: empIn[k] || [],
        }));

        let options = {
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false }
            },
            series: series,
            xaxis: {
                categories: years
            },
            yaxis: {
                title: { text: 'Jumlah Karyawan Masuk' }
            },
            colors: colors.slice(0, tipeKeys.length),
            stroke: {
                width: 3,
                curve: 'straight'
            },
            markers: {
                size: 5
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '11px', colors: ['#000'] },
                offsetY: -5
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            title: {
                align: 'left',
                style: { fontSize: '14px', color: '#4a148c', fontWeight: 700 }
            },
            grid: {
                borderColor: '#e0e0e0'
            }
        };

        if (employeeInChart) {
            employeeInChart.updateOptions({
                series: series,
                xaxis: { categories: years },
                colors: colors.slice(0, tipeKeys.length)
            });
        } else {
            employeeInChart = new ApexCharts(document.querySelector('#employeeInChart'), options);
            employeeInChart.render();
        }
    }

    let employeeOutChart = null;
    function renderEmployeeOutChart(res) {
        // emp_out dari server: { years: [...], 'KMJ': [...], 'Fortuna': [...] }
        // atau { years: [...], staff: [...], non_staff: [...] } — dinamis sesuai mode.
        let empOut   = res.emp_out || { years: [] };
        let years    = empOut.years || [];
        let tipeKeys = Object.keys(empOut).filter(k => k !== 'years');
        const colors = ['#e53935', '#fb8c00', '#43a047', '#1e88e5', '#8e24aa', '#00acc1'];

        let series = tipeKeys.map((k) => ({
            name: k,
            data: empOut[k] || [],
        }));

        let options = {
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false }
            },
            series: series,
            xaxis: {
                categories: years
            },
            yaxis: {
                title: { text: 'Jumlah Karyawan Keluar' }
            },
            colors: colors.slice(0, tipeKeys.length),
            stroke: {
                width: 3,
                curve: 'straight'
            },
            markers: {
                size: 5
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '11px', colors: ['#000'] },
                offsetY: -5
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            title: {
                align: 'left',
                style: { fontSize: '14px', color: '#4a148c', fontWeight: 700 }
            },
            grid: {
                borderColor: '#e0e0e0'
            }
        };

        if (employeeOutChart) {
            employeeOutChart.updateOptions({
                series: series,
                xaxis: { categories: years },
                colors: colors.slice(0, tipeKeys.length)
            });
        } else {
            employeeOutChart = new ApexCharts(document.querySelector('#employeeOutChart'), options);
            employeeOutChart.render();
        }
    }

    let distribusiUsiaChart = null;
    function renderDistribusiUsiaChart(res) {
        let dist = res.distribusi_usia || {};
        let categories = ['>55', '51-55', '41-50', '31-40', '18-30', '<18'];
        let data = categories.map(k => dist[k] || 0);

        let options = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false }
            },
            series: [{
                name: 'Jumlah',
                data: data
            }],
            xaxis: {
                categories: categories
            },
            yaxis: {
                title: { text: 'Jumlah Karyawan' }
            },
            colors: ['#4a148c'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '12px', colors: ['#000'] }
            },
            title: {
                align: 'left',
                style: { fontSize: '14px', color: '#4a148c', fontWeight: 700 }
            }
        };

        if (distribusiUsiaChart) {
            distribusiUsiaChart.updateOptions({
                series: [{ name: 'Jumlah', data: data }],
                xaxis: { categories: categories }
            });
        } else {
            distribusiUsiaChart = new ApexCharts(document.querySelector('#distribusiUsiaChart'), options);
            distribusiUsiaChart.render();
        }
    }

    function renderTable(res) {
        let rows = '';
        if (!res.data || res.data.length === 0) {
            rows = '<tr><td colspan="17" class="text-center text-muted">Tidak ada data yang cocok.</td></tr>';
        } else {
            res.data.forEach(r => {
                let tglKeluar = (r.Aktif === 'N' || r.Aktif === 'n') ? (r['Valid From'] || '') : '';
                let distribusiUsia = '';
                // Tampilkan Distribusi Usia jika:
                // - Rentang Data diisi: hanya untuk yang aktif di rentang tsb
                // - Rentang Data tidak diisi (default): hanya untuk yang kolom Aktif = 'Y'
                const rdRange = $('#rentang_data_range').val();
                const hasRentang = rdRange && rdRange.trim() !== '';
                const isActive = hasRentang
                    ? calcAktifRentangData(r) === 'Y'
                    : (r.Aktif && String(r.Aktif).toUpperCase() === 'Y');
                if (isActive && r['Tgl Lahir']) {
                    const lahir = new Date(r['Tgl Lahir']);
                    // Ambil reference date dari rentang data, fallback ke today
                    let ref = new Date();
                    const rdRange = $('#rentang_data_range').val();
                    if (rdRange) {
                        const parts = rdRange.split(/\s+to\s+|\s+-\s+/);
                        const endStr = parts.length === 2 ? parts[1].trim() : (parts[0] || '').trim();
                        if (endStr) ref = new Date(endStr);
                    }
                    let age = ref.getFullYear() - lahir.getFullYear();
                    const m = ref.getMonth() - lahir.getMonth();
                    if (m < 0 || (m === 0 && ref.getDate() < lahir.getDate())) age--;
                    if      (age > 55) distribusiUsia = '>55';
                    else if (age >= 51) distribusiUsia = '51-55';
                    else if (age >= 41) distribusiUsia = '41-50';
                    else if (age >= 31) distribusiUsia = '31-40';
                    else if (age >= 18) distribusiUsia = '18-30';
                    else                distribusiUsia = '<18';
                }
                rows += `
                    <tr>
                        <td>${escapeHtml(r.NIK)}</td>
                        <td>${escapeHtml(r.Nama)}</td>
                        <td>${escapeHtml(r['Tgl Lahir'])}</td>
                        <td>${escapeHtml(r['Tgl Masuk'])}</td>
                        <td>${escapeHtml(r['Valid From'])}</td>
                        <td>${escapeHtml(tglKeluar)}</td>
                        <td>${escapeHtml(r.Departmen)}</td>
                        <td>${escapeHtml(r['Sub Departmen'])}</td>
                        <td>${escapeHtml(r.Section)}</td>
                        <td>${escapeHtml(r['Tipe Karyawan'])}</td>
                        <td>${escapeHtml(r.Jabatan)}</td>
                        <td>${escapeHtml(r['Jenis Kelamin'])}</td>
                        <td>${escapeHtml(r['Work Status'])}</td>
                        <td>${escapeHtml(r['Status Nikah'])}</td>
                        <td>${escapeHtml(r.Aktif)}</td>
                        <td>${escapeHtml(calcAktifRentangData(r))}</td>
                        <td>${escapeHtml(distribusiUsia)}</td>
                    </tr>
                `;
            });
        }
        $('#dataTbody').html(rows);
    }

    /**
     * Hitung status aktif per-row AS OF rentang data snapshot (end_date).
     * Logika:
     *   - Tanpa rentang data → '-' (tidak ada snapshot)
     *   - Aktif = 'Y' saat ini → 'Y' (masih aktif di snapshot)
     *   - Aktif = 'N' + Valid From > end_date → 'Y' (saat snapshot dia masih aktif, baru keluar nanti)
     *   - Aktif = 'N' + Valid From <= end_date → 'N' (sudah keluar sebelum/saat snapshot)
     *   - Tanpa Valid From → 'Y' (data historis, diasumsikan masih aktif)
     */
    function calcAktifRentangData(r) {
        let rdRange = $('#rentang_data_range').val();
        if (!rdRange) return '-';
        let parts = rdRange.split(/\s+to\s+|\s+-\s+/);
        let endDate = parts.length === 2 ? parts[1].trim() : (parts[0] || '').trim();
        if (!endDate) return '-';

        if (!r.Aktif) return '-';
        const aktif = String(r.Aktif).toUpperCase();
        if (aktif === 'Y') return 'Y';

        // Aktif = N
        const vf = r['Valid From'];
        if (!vf) return 'Y';
        return vf > endDate ? 'Y' : 'N';
    }

    function renderPagination(res) {
        let total    = res.total || 0;
        let page     = res.page || 1;
        let lastPage = res.last_page || 1;
        let perPage  = res.per_page || 25;

        let from = total ? (page - 1) * perPage + 1 : 0;
        let to   = Math.min(page * perPage, total);
        $('#pageInfo').text(`Menampilkan ${from}–${to} dari ${total} data`);

        if (lastPage <= 1) {
            $('#pageNumbers').html('');
            return;
        }

        let html = '<nav><ul class="pagination hd-pagination mb-0">';
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:;" data-p="1">&laquo;</a></li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:;" data-p="${page - 1}">&lsaquo;</a></li>`;

        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-p="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="javascript:;" data-p="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-p="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" href="javascript:;" data-p="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" href="javascript:;" data-p="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#pageNumbers').html(html);
    }

    $(document).on('click', '#pageNumbers .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        currentPage = parseInt($(this).data('p'));
        loadData();
    });

    $('#perPage').on('change', function () {
        currentPage = 1;
        loadData();
    });

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        currentPage = 1;
        loadData();
    });

    $('#btnReset').on('click', function () {
        $('#filterForm')[0].reset();
        $('.flatpickr-date').each(function () {
            this._flatpickr && this._flatpickr.clear();
        });
        rentangDataRange.clear();
        tglMasukRange.clear();
        tglKeluarRange.clear();
        // Reset multi-select dropdowns
        $('.hd-multi-select').each(function () {
            $(this).find('input[type=checkbox]').prop('checked', false);
            updateMsLabel($(this));
        });
        @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            // Mode terkunci: re-apply auto-select setelah reset
            applyTipeKaryawanModePreselect();
        @endif
        currentPage = 1;
    });

    $('#btnExport').on('click', function () {
        let params = $.param(getFilterParams());
        window.open("{{ url('/hr/hrdashboard/export') }}?" + params, '_blank');
    });

    // Load default data on page load
    $(function () {
        loadData();
    });

    // Auto-refresh data setiap 60 detik
    let autoRefreshInterval = setInterval(function () {
        loadData();
    }, 600000);

    // Pause auto-refresh saat tab tidak terlihat (hemat request)
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        } else {
            autoRefreshInterval = setInterval(function () {
                loadData();
            }, 600000);
        }
    });
</script>

{{-- WT&O scripts: filter, table, auto-cycle antar section --}}
@include('hr.dashboard.partials.wto-scripts')

@endpush
