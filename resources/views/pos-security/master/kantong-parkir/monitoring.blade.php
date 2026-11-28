@extends('pos-security.layouts.base')

@section('title', 'Real Time Parking Monitoring')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=JetBrains+Mono:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        body,
        .card,
        .table,
        .modal,
        .form-control,
        .form-select,
        .btn,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace !important;
        }

        /* KPI Card Styles */
        .kpi-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.1);
        }

        .kpi-icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        /* Mini Slot Tile (Seat-map / Cinema Matrix Style) */
        .mini-slot-tile {
            width: 52px;
            height: 56px;
            border-radius: 8px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            user-select: none;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
        }

        .mini-slot-tile:hover {
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
            z-index: 5;
        }

        .mini-slot-tile .tile-icon {
            font-size: 18px;
            line-height: 1;
            margin-bottom: 3px;
        }

        .mini-slot-tile .tile-code {
            font-size: 11px;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.2px;
            font-family: 'JetBrains Mono', monospace !important;
        }

        /* Top Notch / Seat Back Accent */
        .mini-slot-tile::before {
            content: '';
            position: absolute;
            top: 2px;
            width: 22px;
            height: 3px;
            border-radius: 2px;
            background-color: currentColor;
            opacity: 0.45;
        }

        /* Status Colors */
        .tile-kosong {
            background-color: #dcfce7;
            border: 2px solid #22c55e;
            color: #15803d;
        }

        .tile-kosong:hover {
            background-color: #bbf7d0;
            border-color: #16a34a;
            box-shadow: 0 6px 14px rgba(34, 197, 94, 0.3);
        }

        .tile-terisi {
            background-color: #fee2e2;
            border: 2px solid #ef4444;
            color: #b91c1c;
        }

        .tile-terisi:hover {
            background-color: #fecaca;
            border-color: #dc2626;
            box-shadow: 0 6px 14px rgba(239, 68, 68, 0.3);
        }

        .tile-reserved {
            background-color: #e0f2fe;
            border: 2px solid #0ea5e9;
            color: #0369a1;
        }

        .tile-reserved:hover {
            background-color: #bae6fd;
            border-color: #0284c7;
            box-shadow: 0 6px 14px rgba(14, 165, 233, 0.3);
        }

        .tile-maintenance {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            color: #b45309;
        }

        .tile-maintenance:hover {
            background-color: #fde68a;
            border-color: #d97706;
            box-shadow: 0 6px 14px rgba(245, 158, 11, 0.3);
        }

        /* Pulse badge for occupied live status */
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            background-color: #22c55e;
            box-shadow: 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-2" id="parkingDashboardContainer">

        {{-- Top Navigation & Controls Bar --}}
        <div class="row mb-3 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('pos-security.master.kantong-parkir.index') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm px-3">
                        <i class="mdi mdi-arrow-left fs-5"></i>
                        <span>Kembali ke Master</span>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 fw-bold text-dark">
                                <i class="mdi mdi-car-brake-parking text-primary me-1"></i> Dashboard Kapasitas Parkir
                            </h4>
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-1 px-2 py-1 fs-12">
                                <span class="live-dot"></span> LIVE
                            </span>
                        </div>
                        <small class="text-muted">Monitoring visual real-time kapasitas kantong parkir & status
                            kendaraan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-5 text-md-end mt-3 mt-md-0">
                <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                    {{-- Realtime Clock --}}
                    <div
                        class="bg-white border rounded px-3 py-1 text-dark small fw-semibold shadow-sm font-mono d-flex align-items-center gap-1">
                        <i class="mdi mdi-clock-outline text-primary"></i>
                        <span id="liveClock">--:--:-- WIB</span>
                    </div>

                    {{-- Auto Refresh Switch --}}
                    <div class="form-check form-switch bg-white border rounded px-3 py-1 shadow-sm d-flex align-items-center gap-2 mb-0"
                        style="min-height: 38px;">
                        <input class="form-check-input ms-0 mt-0" type="checkbox" id="switchAutoRefresh" checked
                            onchange="toggleAutoRefresh(this.checked)">
                        <label class="form-check-label small fw-semibold text-muted mb-0" for="switchAutoRefresh">Auto
                            30s</label>
                    </div>

                    {{-- Refresh Button --}}
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm"
                        id="btnManualRefresh" onclick="refreshDashboardData()">
                        <i class="mdi mdi-refresh" id="iconManualRefresh"></i>
                        <span>Refresh</span>
                    </button>

                    {{-- Fullscreen Button --}}
                    <button type="button" class="btn btn-outline-dark d-inline-flex align-items-center shadow-sm"
                        onclick="toggleFullScreen()" title="Layar Penuh">
                        <i class="mdi mdi-fullscreen fs-5" id="iconFullscreen"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- 4 KPI Summary Cards --}}
        <div class="row g-3">
            {{-- Total Kapasitas --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-12 d-block mb-1">Total
                                    Kapasitas</span>
                                <h3 class="fw-bold mb-0 text-dark" id="kpiTotalSlots">{{ $totalSlots }}</h3>
                                <small class="text-muted">Slot di semua zona aktif</small>
                            </div>
                            <div class="kpi-icon-circle bg-primary-subtle text-primary">
                                <i class="mdi mdi-view-grid"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tersedia (Kosong) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success text-uppercase fw-semibold fs-12 d-block mb-1">Slot Tersedia
                                    (Kosong)</span>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="fw-bold mb-0 text-success" id="kpiTotalKosong">{{ $totalKosong }}</h3>
                                    <span class="badge bg-success-subtle text-success fs-12" id="kpiPersenKosong">
                                        {{ $totalSlots > 0 ? round(($totalKosong / $totalSlots) * 100) : 0 }}%
                                    </span>
                                </div>
                                <small class="text-muted">Siap untuk digunakan</small>
                            </div>
                            <div class="kpi-icon-circle bg-success-subtle text-success">
                                <i class="mdi mdi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terisi (Occupied) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-danger text-uppercase fw-semibold fs-12 d-block mb-1">Slot Terisi
                                    Kendaraan</span>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="fw-bold mb-0 text-danger" id="kpiTotalTerisi">{{ $totalTerisi }}</h3>
                                    <span class="badge bg-danger-subtle text-danger fs-12" id="kpiPersenTerisi">
                                        {{ $totalSlots > 0 ? round(($totalTerisi / $totalSlots) * 100) : 0 }}%
                                    </span>
                                </div>
                                <small class="text-muted">Sedang parkir aktif</small>
                            </div>
                            <div class="kpi-icon-circle bg-danger-subtle text-danger">
                                <i class="mdi mdi-car"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Occupancy Rate --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-12 d-block">Tingkat Keterisian</span>
                                <h3 class="fw-bold mb-0 text-dark" id="kpiOccupancyRate">{{ $occupancyRate }}%</h3>
                            </div>
                            <div class="kpi-icon-circle bg-warning-subtle text-warning">
                                <i class="mdi mdi-chart-donut"></i>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $occupancyRate > 85 ? 'bg-danger' : ($occupancyRate > 60 ? 'bg-warning' : 'bg-primary') }}"
                                role="progressbar" id="kpiProgressBar" style="width: {{ $occupancyRate }}%"
                                aria-valuenow="{{ $occupancyRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Legend Bar --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center justify-content-between">
                    {{-- Color Legend --}}
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-wrap align-items-center gap-3 small">
                            <span class="fw-bold text-dark"><i class="mdi mdi-palette me-1"></i>Legenda:</span>
                            <div class="d-flex align-items-center gap-1">
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-1 border border-success tile-kosong"
                                    style="width: 22px; height: 22px; font-size: 12px;">
                                    <i class="mdi mdi-parking"></i>
                                </span>
                                <span class="fw-semibold text-success">Kosong / Tersedia</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-1 border border-danger tile-terisi"
                                    style="width: 22px; height: 22px; font-size: 12px;">
                                    <i class="mdi mdi-truck"></i>
                                </span>
                                <span class="fw-semibold text-danger">Terisi (Klik Detail)</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-1 border border-info tile-reserved"
                                    style="width: 22px; height: 22px; font-size: 12px;">
                                    <i class="mdi mdi-bookmark"></i>
                                </span>
                                <span class="text-info fw-semibold">Reserved</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-1 border border-warning tile-maintenance"
                                    style="width: 22px; height: 22px; font-size: 12px;">
                                    <i class="mdi mdi-wrench"></i>
                                </span>
                                <span class="text-warning fw-semibold">Maintenance</span>
                            </div>
                        </div>
                    </div>

                    {{-- Search & Status Filter --}}
                    <div class="col-12 col-lg-6">
                        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                            <div class="input-group input-group-sm" style="max-width: 250px;">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="filterSearch" class="form-control border-start-0"
                                    placeholder="Cari Slot / Nopol / Supir..." oninput="applyFilters()">
                            </div>
                            <select id="filterStatus" class="form-select form-select-sm" style="width: auto;"
                                onchange="applyFilters()">
                                <option value="all">Semua Status</option>
                                <option value="kosong">Hanya Kosong (Tersedia)</option>
                                <option value="terisi">Hanya Terisi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Zones and Visual Parking Bays Container --}}
        <div id="dashZonesContainer">
            @if (isset($zones) && count($zones) > 0)
                @foreach ($zones as $zone)
                    @php
                        $zTotal = count($zone->slots);
                        $zTerisi = $zone->slots->where('status_slot', 'terisi')->count();
                        $zKosong = $zone->slots->where('status_slot', 'kosong')->count();
                        $zRate = $zTotal > 0 ? round(($zTerisi / $zTotal) * 100) : 0;
                    @endphp
                    <div class="zone-card-section card border-0 shadow-sm mb-3" data-zone-id="{{ $zone->id }}"
                        id="zone-section-{{ $zone->id }}">
                        <div class="card-body p-3">
                            <div
                                class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                {{-- Left: Zone Info Box --}}
                                <div class="zone-info-box d-flex flex-row flex-lg-column justify-content-between justify-content-lg-center align-items-start flex-shrink-0"
                                    style="min-width: 220px; max-width: 260px;">
                                    <div class="w-100">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary fs-13 px-2 py-1">Zona
                                                {{ $zone->kode_zona }}</span>
                                            <h6 class="mb-0 fw-bold text-dark text-truncate"
                                                title="{{ $zone->nama_zona }}">
                                                {{ $zone->nama_zona }}
                                            </h6>
                                        </div>
                                        @if ($zone->keterangan)
                                            <small class="text-muted d-block text-truncate mb-1"
                                                title="{{ $zone->keterangan }}">
                                                {{ $zone->keterangan }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1 w-100 flex-wrap">
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                            <i class="mdi mdi-check-circle me-1"></i>{{ $zKosong }} Kosong
                                        </span>
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">
                                            <i class="mdi mdi-car me-1"></i>{{ $zTerisi }} Terisi
                                        </span>
                                        <span
                                            class="small font-mono text-muted fs-11 fw-bold">({{ $zRate }}%)</span>
                                    </div>
                                    <div class="progress w-100 mt-2 d-none d-lg-flex" style="height: 5px;">
                                        <div class="progress-bar {{ $zRate > 85 ? 'bg-danger' : ($zRate > 60 ? 'bg-warning' : 'bg-primary') }}"
                                            style="width: {{ $zRate }}%"></div>
                                    </div>
                                </div>

                                {{-- Vertical separator on desktop --}}
                                <div class="vr d-none d-lg-block opacity-25"
                                    style="align-self: stretch; min-height: 54px;"></div>

                                {{-- Right: Slots Row (Cinema/Seat Style Mini Tiles) --}}
                                <div class="zone-slots-container flex-grow-1 overflow-x-auto py-1">
                                    @if ($zTotal > 0)
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            @foreach ($zone->slots as $slot)
                                                @php
                                                    $isKosong = $slot->status_slot === 'kosong';
                                                    $isTerisi = $slot->status_slot === 'terisi';
                                                    $isReserved = $slot->status_slot === 'reserved';
                                                    $isMaintenance = $slot->status_slot === 'maintenance';
                                                    $active = $slot->activeAssignment;

                                                    $tileClass = 'tile-kosong';
                                                    $iconClass = 'mdi-parking';
                                                    if ($isTerisi) {
                                                        $tileClass = 'tile-terisi';
                                                        $iconClass = 'mdi-truck';
                                                    } elseif ($isReserved) {
                                                        $tileClass = 'tile-reserved';
                                                        $iconClass = 'mdi-bookmark';
                                                    } elseif ($isMaintenance) {
                                                        $tileClass = 'tile-maintenance';
                                                        $iconClass = 'mdi-wrench';
                                                    }

                                                    $tooltip = '<strong>Slot ' . e($slot->kode_slot) . '</strong>';
                                                    if ($isKosong) {
                                                        $tooltip .=
                                                            '<br><span class="text-success">Tersedia (Kosong)</span>';
                                                        if ($slot->jenis_kendaraan) {
                                                            $tooltip .=
                                                                '<br><small class="text-muted">' .
                                                                e($slot->jenis_kendaraan) .
                                                                '</small>';
                                                        }
                                                    } elseif ($isTerisi && $active) {
                                                        $tooltip .=
                                                            '<br><span class="text-danger fw-bold font-mono">' .
                                                            e($active->no_polisi) .
                                                            '</span>';
                                                        if ($active->nama_driver) {
                                                            $tooltip .=
                                                                '<br><small>' . e($active->nama_driver) . '</small>';
                                                        }
                                                        $tooltip .=
                                                            '<br><span class="badge bg-light text-dark mt-1">Klik untuk detail</span>';
                                                    } elseif ($isReserved) {
                                                        $tooltip .= '<br><span class="text-info">Reserved</span>';
                                                    } else {
                                                        $tooltip .= '<br><span class="text-warning">Maintenance</span>';
                                                    }
                                                @endphp

                                                <div class="dash-slot-item mini-slot-tile {{ $tileClass }}"
                                                    data-slot-id="{{ $slot->id }}"
                                                    data-slot-code="{{ $slot->kode_slot }}"
                                                    data-zone-id="{{ $zone->id }}"
                                                    data-zone-name="{{ $zone->nama_zona }} ({{ $zone->kode_zona }})"
                                                    data-status="{{ $slot->status_slot }}"
                                                    data-nopol="{{ $active ? $active->no_polisi : '' }}"
                                                    data-driver="{{ $active ? $active->nama_driver : '' }}"
                                                    data-jenis="{{ $slot->jenis_kendaraan ?? '' }}"
                                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top"
                                                    data-bs-title="{{ $tooltip }}"
                                                    @if ($isTerisi && $active) data-detail="{{ json_encode(
                                                        [
                                                            'slot_code' => $slot->kode_slot,
                                                            'zone_name' => $zone->nama_zona . ' (' . $zone->kode_zona . ')',
                                                            'no_polisi' => $active->no_polisi,
                                                            'nama_driver' => $active->nama_driver,
                                                            'no_hp_driver' => $active->no_hp_driver,
                                                            'jenis_kendaraan' => $active->jenis_kendaraan ?? $slot->jenis_kendaraan,
                                                            'waktu_masuk' => $active->waktu_masuk
                                                                ? \Carbon\Carbon::parse($active->waktu_masuk)->timezone(config('app.timezone', 'Asia/Jakarta'))->translatedFormat('d-m-Y H:i:s') . ' WIB'
                                                                : '-',
                                                            'durasi_parkir' => $active->waktu_masuk
                                                                ? \Carbon\Carbon::parse($active->waktu_masuk)->diffForHumans(null, true)
                                                                : '-',
                                                            'catatan' => $active->catatan,
                                                            'assignment_id' => $active->id,
                                                        ],
                                                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT,
                                                    ) }}" @endif
                                                    onclick="onSlotTileClick(this)" id="dash-slot-{{ $slot->id }}">
                                                    <i class="mdi {{ $iconClass }} tile-icon"></i>
                                                    <span class="tile-code">{{ $slot->kode_slot }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted small py-2">
                                            <i class="mdi mdi-alert-circle-outline me-1"></i>Belum ada slot parkir yang
                                            terdaftar di zona ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="mdi mdi-car-parking-lot fs-1 text-muted d-block mb-2"></i>
                        <h5 class="fw-bold text-dark">Tidak Ada Data Zona Parkir Aktif</h5>
                        <p class="text-muted mb-0">Silakan pastikan master kantong parkir sudah dikonfigurasi dan berstatus
                            aktif.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Search Empty State Alert --}}
        <div id="dashSearchEmptyState" class="card border-0 shadow-sm text-center py-5 my-4" style="display: none;">
            <div class="card-body">
                <i class="mdi mdi-magnify-close fs-1 text-muted d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Tidak Ada Slot Parkir yang Cocok</h5>
                <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau ganti filter status di atas.</p>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="resetDashFilters()">Reset
                    Filter</button>
            </div>
        </div>

    </div>

    {{-- Modal Detail Kendaraan Terparkir --}}
    <div class="modal fade" id="modalVehicleDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header bg-danger text-white py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-car-info fs-4"></i>
                        <h5 class="modal-title fw-bold text-white mb-0" id="detailModalTitle">Detail Kendaraan Terparkir
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Slot & Zone Banner --}}
                    <div class="card border bg-light mb-3">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold d-block">Lokasi Slot</small>
                                <h5 class="fw-bold text-dark mb-0" id="detailSlotCode">Slot A-01</h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted text-uppercase fw-semibold d-block">Area / Zona</small>
                                <span class="badge bg-primary fs-13" id="detailZoneName">Zona WFG</span>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Plate Prominent --}}
                    <div class="text-center p-3 bg-danger-subtle rounded-3 border border-danger-subtle mb-3">
                        <small class="text-danger fw-semibold text-uppercase d-block mb-1">Nomor Polisi</small>
                        <h2 class="fw-bold text-danger font-mono mb-0" id="detailNoPolisi" style="letter-spacing: 2px;">B
                            1234 CD</h2>
                    </div>

                    {{-- Detail List --}}
                    <ul class="list-group list-group-flush border rounded-3 mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted small"><i class="mdi mdi-account me-1"></i>Nama Driver / Supir:</span>
                            <strong class="text-dark" id="detailNamaDriver">-</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted small"><i class="mdi mdi-phone me-1"></i>Nomor HP:</span>
                            <span id="detailNoHpContainer">
                                <strong class="text-dark font-mono" id="detailNoHp">-</strong>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted small"><i class="mdi mdi-truck me-1"></i>Peruntukan / Jenis:</span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle"
                                id="detailJenisKendaraan">-</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted small"><i class="mdi mdi-clock-in me-1"></i>Waktu Masuk:</span>
                            <strong class="text-dark font-mono small" id="detailWaktuMasuk">-</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted small"><i class="mdi mdi-timer-outline me-1"></i>Durasi Parkir:</span>
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-semibold"
                                id="detailDurasiParkir">-</span>
                        </li>
                        <li class="list-group-item py-2 px-3">
                            <span class="text-muted small d-block mb-1"><i
                                    class="mdi mdi-note-text-outline me-1"></i>Catatan:</span>
                            <p class="mb-0 text-muted small fst-italic" id="detailCatatan">-</p>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning d-flex align-items-center gap-1" id="btnReleaseVehicle"
                        onclick="triggerReleaseVehicle()">
                        <i class="mdi mdi-logout"></i>
                        <span>Keluarkan Kendaraan (Checkout)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Vacant Slot Detail Modal --}}
    <div class="modal fade" id="modalVacantDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header bg-success text-white py-2 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-parking fs-4"></i>
                        <h6 class="modal-title fw-bold text-white mb-0">Informasi Slot Parkir</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="badge bg-success-subtle text-success p-3 rounded-circle mb-3">
                        <i class="mdi mdi-check-circle-outline display-5"></i>
                    </div>
                    <h3 class="fw-bold text-dark font-mono mb-1" id="vacantSlotCode">A-01</h3>
                    <p class="text-muted small mb-3" id="vacantZoneName">Zona WFG</p>
                    <div class="alert alert-success py-2 px-3 mb-3 small fw-semibold">
                        <i class="mdi mdi-car-check me-1"></i> Status: Kosong / Tersedia
                    </div>
                    <div class="text-muted small" id="vacantJenisKendaraan">
                        Peruntukan: <strong>Semua Jenis Kendaraan</strong>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3 text-center d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let activeAssignmentIdToRelease = null;
        let autoRefreshInterval = null;

        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Toggle Auto Refresh
        function toggleAutoRefresh(enable) {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }

            if (enable) {
                autoRefreshInterval = setInterval(function() {
                    refreshDashboardData(true);
                }, 30000); // 30 seconds
            }
        }
        // Start auto refresh by default
        toggleAutoRefresh(true);

        // Fullscreen toggle
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.warn('Error attempting to enable fullscreen:', err.message);
                });
                document.getElementById('iconFullscreen')?.classList.replace('mdi-fullscreen', 'mdi-fullscreen-exit');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                document.getElementById('iconFullscreen')?.classList.replace('mdi-fullscreen-exit', 'mdi-fullscreen');
            }
        }

        // Refresh Dashboard Data via AJAX `/api/kantong-parkir`
        function refreshDashboardData(isSilent = false) {
            const btn = document.getElementById('btnManualRefresh');
            const icon = document.getElementById('iconManualRefresh');

            if (!isSilent) {
                if (icon) icon.classList.add('mdi-spin');
                if (btn) btn.disabled = true;
            }

            $.ajax({
                url: '/api/kantong-parkir',
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && res.status === 'success' && res.data) {
                        renderDashboardData(res.data);
                        applyFilters();

                        if (!isSilent && typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            Toast.fire({
                                icon: 'info',
                                title: 'Data dashboard kapasitas diperbarui!'
                            });
                        }
                    }
                },
                error: function() {
                    console.warn('Gagal memuat pembaruan data kantong parkir.');
                },
                complete: function() {
                    if (!isSilent) {
                        if (icon) icon.classList.remove('mdi-spin');
                        if (btn) btn.disabled = false;
                    }
                }
            });
        }

        // Render Dashboard zones, slots & KPI counters
        function renderDashboardData(zonesData) {
            let totalSlots = 0;
            let totalKosong = 0;
            let totalTerisi = 0;
            let totalOther = 0;

            const container = document.getElementById('dashZonesContainer');
            if (!container) return;

            let html = '';

            zonesData.forEach(zone => {
                const zTotal = zone.slots ? zone.slots.length : 0;
                let zTerisi = 0;
                let zKosong = 0;

                if (zone.slots) {
                    zone.slots.forEach(s => {
                        totalSlots++;
                        if (s.status_slot === 'kosong') {
                            totalKosong++;
                            zKosong++;
                        } else if (s.status_slot === 'terisi') {
                            totalTerisi++;
                            zTerisi++;
                        } else {
                            totalOther++;
                        }
                    });
                }

                const zRate = zTotal > 0 ? Math.round((zTerisi / zTotal) * 100) : 0;

                html += `
                <div class="zone-card-section card border-0 shadow-sm mb-3" data-zone-id="${zone.id}" id="zone-section-${zone.id}">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                            {{-- Left: Zone Info Box --}}
                            <div class="zone-info-box d-flex flex-row flex-lg-column justify-content-between justify-content-lg-center align-items-start flex-shrink-0"
                                style="min-width: 220px; max-width: 260px;">
                                <div class="w-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary fs-13 px-2 py-1">Zona ${zone.kode_zona}</span>
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" title="${zone.nama_zona}">${zone.nama_zona}</h6>
                                    </div>
                                    ${zone.keterangan ? `<small class="text-muted d-block text-truncate mb-1" title="${zone.keterangan}">${zone.keterangan}</small>` : ''}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1 w-100 flex-wrap">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                        <i class="mdi mdi-check-circle me-1"></i>${zKosong} Kosong
                                    </span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">
                                        <i class="mdi mdi-car me-1"></i>${zTerisi} Terisi
                                    </span>
                                    <span class="small font-mono text-muted fs-11 fw-bold">(${zRate}%)</span>
                                </div>
                                <div class="progress w-100 mt-2 d-none d-lg-flex" style="height: 5px;">
                                    <div class="progress-bar ${zRate > 85 ? 'bg-danger' : (zRate > 60 ? 'bg-warning' : 'bg-primary')}" 
                                         style="width: ${zRate}%"></div>
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block opacity-25" style="align-self: stretch; min-height: 54px;"></div>

                            {{-- Right: Slots Row (Mini Tiles) --}}
                            <div class="zone-slots-container flex-grow-1 overflow-x-auto py-1">
            `;

                if (zone.slots && zone.slots.length > 0) {
                    html += `<div class="d-flex flex-wrap align-items-center gap-2">`;
                    zone.slots.forEach(slot => {
                        const isKosong = (slot.status_slot === 'kosong');
                        const isTerisi = (slot.status_slot === 'terisi');
                        const isReserved = (slot.status_slot === 'reserved');
                        const active = slot.active_vehicle;

                        let tileClass = 'tile-kosong';
                        let iconClass = 'mdi-parking';
                        let tooltipText =
                            `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-success'>Tersedia (Kosong)</span>`;
                        if (slot.jenis_kendaraan) {
                            tooltipText += `<br><small class='text-muted'>${slot.jenis_kendaraan}</small>`;
                        }
                        let detailAttr = '';

                        if (isTerisi) {
                            tileClass = 'tile-terisi';
                            iconClass = 'mdi-truck';
                            tooltipText = `<strong>Slot ${slot.kode_slot}</strong>`;
                            if (active) {
                                tooltipText +=
                                    `<br><span class='text-danger fw-bold font-mono'>${active.no_polisi || '-'}</span>`;
                                if (active.nama_driver) {
                                    tooltipText += `<br><small>${active.nama_driver}</small>`;
                                }
                                tooltipText +=
                                    `<br><span class='badge bg-light text-dark mt-1'>Klik untuk detail</span>`;

                                const detailData = {
                                    slot_code: slot.kode_slot,
                                    zone_name: `${zone.nama_zona} (${zone.kode_zona})`,
                                    no_polisi: active.no_polisi,
                                    nama_driver: active.nama_driver,
                                    no_hp_driver: active.no_hp_driver,
                                    jenis_kendaraan: active.jenis_kendaraan || slot.jenis_kendaraan,
                                    waktu_masuk: active.waktu_masuk,
                                    durasi_parkir: active.durasi_parkir || '-',
                                    catatan: active.catatan,
                                    assignment_id: active.id
                                };
                                const encodedDetail = encodeURIComponent(JSON.stringify(detailData));
                                detailAttr = `data-detail-uri="${encodedDetail}"`;
                            }
                        } else if (isReserved) {
                            tileClass = 'tile-reserved';
                            iconClass = 'mdi-bookmark';
                            tooltipText =
                                `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-info'>Reserved</span>`;
                        } else if (!isKosong) {
                            tileClass = 'tile-maintenance';
                            iconClass = 'mdi-wrench';
                            tooltipText =
                                `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-warning'>Maintenance</span>`;
                        }

                        html += `
                            <div class="dash-slot-item mini-slot-tile ${tileClass}"
                                 data-slot-id="${slot.id}"
                                 data-slot-code="${slot.kode_slot}"
                                 data-zone-id="${zone.id}"
                                 data-zone-name="${zone.nama_zona} (${zone.kode_zona})"
                                 data-status="${slot.status_slot}"
                                 data-nopol="${active ? (active.no_polisi || '') : ''}"
                                 data-driver="${active ? (active.nama_driver || '') : ''}"
                                 data-jenis="${slot.jenis_kendaraan || ''}"
                                 data-bs-toggle="tooltip"
                                 data-bs-html="true"
                                 data-bs-placement="top"
                                 data-bs-title="${tooltipText.replace(/"/g, '&quot;')}"
                                 ${detailAttr}
                                 onclick="onSlotTileClick(this)"
                                 id="dash-slot-${slot.id}">
                                <i class="mdi ${iconClass} tile-icon"></i>
                                <span class="tile-code">${slot.kode_slot}</span>
                            </div>
                        `;
                    });
                    html += `</div>`;
                } else {
                    html += `
                        <div class="text-muted small py-2">
                            <i class="mdi mdi-alert-circle-outline me-1"></i>Belum ada slot parkir yang terdaftar di zona ini.
                        </div>
                    `;
                }

                html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            });

            container.innerHTML = html;
            initTooltips();

            // Update KPI Counters
            const elTotalSlots = document.getElementById('kpiTotalSlots');
            const elTotalKosong = document.getElementById('kpiTotalKosong');
            const elTotalTerisi = document.getElementById('kpiTotalTerisi');
            const elPersenKosong = document.getElementById('kpiPersenKosong');
            const elPersenTerisi = document.getElementById('kpiPersenTerisi');
            const elRate = document.getElementById('kpiOccupancyRate');
            const elBar = document.getElementById('kpiProgressBar');

            if (elTotalSlots) elTotalSlots.textContent = totalSlots;
            if (elTotalKosong) elTotalKosong.textContent = totalKosong;
            if (elTotalTerisi) elTotalTerisi.textContent = totalTerisi;

            const pKosong = totalSlots > 0 ? Math.round((totalKosong / totalSlots) * 100) : 0;
            const pTerisi = totalSlots > 0 ? Math.round((totalTerisi / totalSlots) * 100) : 0;

            if (elPersenKosong) elPersenKosong.textContent = `${pKosong}%`;
            if (elPersenTerisi) elPersenTerisi.textContent = `${pTerisi}%`;
            if (elRate) elRate.textContent = `${pTerisi}%`;

            if (elBar) {
                elBar.style.width = `${pTerisi}%`;
                elBar.className =
                    `progress-bar ${pTerisi > 85 ? 'bg-danger' : (pTerisi > 60 ? 'bg-warning' : 'bg-primary')}`;
            }
        }

        // Filter slots by search query & status
        function applyFilters() {
            const searchVal = (document.getElementById('filterSearch')?.value || '').toLowerCase().trim();
            const statusVal = document.getElementById('filterStatus')?.value || 'all';

            let visibleCount = 0;

            document.querySelectorAll('.dash-slot-item').forEach(item => {
                const slotCode = (item.getAttribute('data-slot-code') || '').toLowerCase();
                const nopol = (item.getAttribute('data-nopol') || '').toLowerCase();
                const driver = (item.getAttribute('data-driver') || '').toLowerCase();
                const status = (item.getAttribute('data-status') || '').toLowerCase();
                const zoneName = (item.getAttribute('data-zone-name') || '').toLowerCase();

                const matchSearch = !searchVal ||
                    slotCode.includes(searchVal) ||
                    nopol.includes(searchVal) ||
                    driver.includes(searchVal) ||
                    zoneName.includes(searchVal);

                let matchStatus = true;
                if (statusVal === 'kosong') {
                    matchStatus = (status === 'kosong');
                } else if (statusVal === 'terisi') {
                    matchStatus = (status === 'terisi');
                }

                if (matchSearch && matchStatus) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide/Show Zones if all children are hidden
            document.querySelectorAll('.zone-card-section').forEach(section => {
                const hasVisibleSlots = Array.from(section.querySelectorAll('.dash-slot-item')).some(item => item
                    .style.display !== 'none');
                section.style.display = hasVisibleSlots ? '' : 'none';
            });

            // Empty state alert
            const emptyState = document.getElementById('dashSearchEmptyState');
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        // Filter by Zone Tabs
        function filterByZone(zoneId, btnEl) {
            if (btnEl) {
                document.querySelectorAll('#dashZoneTabs .nav-link').forEach(btn => btn.classList.remove('active'));
                btnEl.classList.add('active');
            }

            document.querySelectorAll('.zone-card-section').forEach(section => {
                if (zoneId === 'all' || section.getAttribute('data-zone-id') == zoneId) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });

            applyFilters();
        }

        // Reset Filters
        function resetDashFilters() {
            const searchInput = document.getElementById('filterSearch');
            const statusSelect = document.getElementById('filterStatus');

            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = 'all';

            applyFilters();
        }

        // Format datetime string nicely (e.g. 07-09-2026 13:37:51 WIB)
        function formatWaktuMasuk(val) {
            if (!val || val === '-') return '-';
            if (typeof val === 'string' && (val.includes('WIB') || /^\d{2}-\d{2}-\d{4}/.test(val))) {
                return val;
            }

            try {
                const d = new Date(val);
                if (isNaN(d.getTime())) return val;
                const pad = (n) => String(n).padStart(2, '0');
                const day = pad(d.getDate());
                const month = pad(d.getMonth() + 1);
                const year = d.getFullYear();
                const hours = pad(d.getHours());
                const minutes = pad(d.getMinutes());
                const seconds = pad(d.getSeconds());
                return `${day}-${month}-${year} ${hours}:${minutes}:${seconds} WIB`;
            } catch (e) {
                return val;
            }
        }

        // Show Vehicle Detail Modal
        function showVehicleDetailModal(data) {
            if (!data) return;

            document.getElementById('detailSlotCode').textContent = `Slot ${data.slot_code || '-'}`;
            document.getElementById('detailZoneName').textContent = data.zone_name || '-';
            document.getElementById('detailNoPolisi').textContent = data.no_polisi || '-';
            document.getElementById('detailNamaDriver').textContent = data.nama_driver || '-';

            const hpEl = document.getElementById('detailNoHp');
            if (hpEl) {
                hpEl.textContent = data.no_hp_driver || '-';
            }

            document.getElementById('detailJenisKendaraan').textContent = data.jenis_kendaraan || 'KENDARAAN';
            document.getElementById('detailWaktuMasuk').textContent = formatWaktuMasuk(data.waktu_masuk);

            const durasiEl = document.getElementById('detailDurasiParkir');
            if (durasiEl) {
                durasiEl.textContent = data.durasi_parkir || '-';
            }

            document.getElementById('detailCatatan').textContent = data.catatan || 'Tidak ada catatan.';

            activeAssignmentIdToRelease = data.assignment_id || null;

            const modalEl = document.getElementById('modalVehicleDetail');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            }
        }

        // Checkout / Release Vehicle from parking slot directly
        function triggerReleaseVehicle() {
            if (!activeAssignmentIdToRelease) return;

            Swal.fire({
                title: 'Keluarkan Kendaraan?',
                text: 'Apakah kendaraan ini sudah keluar dan slot parkir akan dikosongkan kembali?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai Parkir',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/pos-security/master/kantong-parkir/assignment/release/${activeAssignmentIdToRelease}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                const modalEl = document.getElementById('modalVehicleDetail');
                                if (modalEl) {
                                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                    if (modalInstance) modalInstance.hide();
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message ||
                                        'Kendaraan telah checkout dan slot kembali kosong.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                refreshDashboardData(true);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message ||
                                        'Terjadi kesalahan saat checkout parkir.'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Gagal menghubungi server.'
                            });
                        }
                    });
                }
            });
        }

        // Universal click handler for slot tiles
        function onSlotTileClick(el) {
            if (!el) return;
            const status = el.getAttribute('data-status');
            const slotCode = el.getAttribute('data-slot-code');
            const zoneName = el.getAttribute('data-zone-name');
            const jenis = el.getAttribute('data-jenis');

            if (status === 'terisi') {
                const rawDetail = el.getAttribute('data-detail');
                const uriDetail = el.getAttribute('data-detail-uri');
                if (rawDetail) {
                    try {
                        const data = JSON.parse(rawDetail);
                        showVehicleDetailModal(data);
                        return;
                    } catch (e) {
                        console.error('Error parsing data-detail:', e);
                    }
                } else if (uriDetail) {
                    try {
                        const data = JSON.parse(decodeURIComponent(uriDetail));
                        showVehicleDetailModal(data);
                        return;
                    } catch (e) {
                        console.error('Error parsing data-detail-uri:', e);
                    }
                }
            } else if (status === 'kosong') {
                showVacantSlotModal(slotCode, zoneName, jenis || 'Semua Jenis Kendaraan');
            } else if (status === 'reserved') {
                showReservedSlotModal(slotCode, zoneName);
            } else {
                showMaintenanceSlotModal(slotCode, zoneName);
            }
        }

        // Initialize tooltips cleanly
        function initTooltips() {
            document.querySelectorAll('.tooltip').forEach(el => el.remove());
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                try {
                    const existing = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                    if (existing) existing.dispose();
                    new bootstrap.Tooltip(tooltipTriggerEl);
                } catch (e) {}
            });
        }

        // Show Vacant Slot Modal
        function showVacantSlotModal(slotCode, zoneName, vehicleType) {
            const elCode = document.getElementById('vacantSlotCode');
            const elZone = document.getElementById('vacantZoneName');
            const elJenis = document.getElementById('vacantJenisKendaraan');
            if (elCode) elCode.textContent = slotCode;
            if (elZone) elZone.textContent = zoneName;
            if (elJenis) elJenis.innerHTML = `Peruntukan: <strong>${vehicleType || 'Semua Jenis Kendaraan'}</strong>`;

            const modalEl = document.getElementById('modalVacantDetail');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            }
        }

        // Show Reserved Slot Alert
        function showReservedSlotModal(slotCode, zoneName) {
            Swal.fire({
                icon: 'info',
                title: `Slot ${slotCode} (Reserved)`,
                text: `Slot parkir di ${zoneName} sedang dibooking / direservasi.`,
                confirmButtonColor: '#0ea5e9'
            });
        }

        // Show Maintenance Slot Alert
        function showMaintenanceSlotModal(slotCode, zoneName) {
            Swal.fire({
                icon: 'warning',
                title: `Slot ${slotCode} (Maintenance)`,
                text: `Slot parkir di ${zoneName} sedang dalam perbaikan / tidak dapat digunakan.`,
                confirmButtonColor: '#f59e0b'
            });
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initTooltips();
        });
    </script>
@endpush
