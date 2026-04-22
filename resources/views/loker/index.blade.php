@extends('layouts.base')

@section('content')
    <div class="container-fluid px-8 py-6">

        {{-- ========== HEADER ========== --}}
        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header rounded-xl d-flex align-items-center justify-content-between flex-wrap p-7">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title mb-1">Panel Kontrol Loker</h2>
                            <div class="bas-header-sub">Monitoring Real-time &bull; PT Bumi Alam Segar</div>
                        </div>
                    </div>

                    {{-- Ringkasan Status --}}
                    <div class="bas-stat-group d-flex align-items-center mt-4 mt-md-0 rounded-lg px-5 py-3">
                        <div class="bas-stat-item text-center px-5">
                            <div class="bas-stat-label">Total Unit</div>
                            <div class="bas-stat-value">{{ number_format($grandTotal['total']) }}</div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5" data-toggle="tooltip"
                            title="Jumlah unit kosong yang siap digunakan oleh karyawan baru">
                            <div class="bas-stat-label">Tersedia</div>
                            <div class="bas-stat-value bas-stat-success">{{ number_format($grandTotal['tersedia']) }}</div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5" data-toggle="tooltip"
                            title="Unit yang sedang dalam masa perbaikan dan tidak dapat di-plotting">
                            <div class="bas-stat-label">Rusak</div>
                            <div class="bas-stat-value bas-stat-danger">{{ number_format($grandTotal['rusak']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== TOOLBAR ========== --}}
        <div class="row mb-6 align-items-center">
            <div class="col-md-5 col-lg-4 mb-3 mb-md-0">
                <div class="bas-search-wrap">
                    <span class="bas-search-icon"><i class="flaticon2-search-1"></i></span>
                    <input type="text" id="search_loker_input" class="bas-search-input"
                        placeholder="Cari nomor unit, nama, atau NIK..." data-toggle="tooltip" data-placement="top"
                        title="Cari cepat berdasarkan nomor unit, NIK, atau nama karyawan pada tab yang aktif">
                </div>
            </div>
            <div class="col-md-7 col-lg-8 text-right">
                <button type="button" onclick="openModalPlotting()" class="bas-btn bas-btn-primary mr-2"
                    data-toggle="tooltip" title="Daftarkan penempatan karyawan baru ke dalam unit loker">
                    <i class="fas fa-user-plus mr-2"></i> Plotting Baru
                </button>
                <div class="dropdown d-inline-block">
                    <button class="bas-btn bas-btn-outline" data-toggle="dropdown">
                        <i class="fas fa-file-export mr-2"></i> Kelola Data <i
                            class="fas fa-chevron-down ml-2 font-size-xs"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right bas-dropdown shadow border-0 p-3"
                        style="min-width: 220px;">
                        <div class="bas-dropdown-section-label">Unduh Laporan (Excel)</div>
                        <a class="bas-dropdown-item" href="{{ route('loker.export', 'L') }}" data-toggle="tooltip"
                            data-placement="left" title="Unduh template laporan (excel) area Pria">
                            <span class="bas-dropdown-icon bas-icon-success"><i class="far fa-file-excel"></i></span>
                            Loker Pria
                        </a>
                        <a class="bas-dropdown-item" href="{{ route('loker.export', 'P') }}" data-toggle="tooltip"
                            data-placement="left" title="Unduh template laporan (excel) area Wanita">
                            <span class="bas-dropdown-icon bas-icon-danger"><i class="far fa-file-excel"></i></span>
                            Loker Wanita
                        </a>
                        <div class="bas-dropdown-divider"></div>
                        <div class="bas-dropdown-section-label">Unggah Data</div>
                        <a class="bas-dropdown-item" href="javascript:void(0)" onclick="openModalImport('L')"
                            data-toggle="tooltip" data-placement="left" title="Masukkan laporan (excel) area Pria">
                            <span class="bas-dropdown-icon bas-icon-primary"><i class="fas fa-upload"></i></span>
                            Impor Loker Pria
                        </a>
                        <a class="bas-dropdown-item" href="javascript:void(0)" onclick="openModalImport('P')"
                            data-toggle="tooltip" data-placement="left" title="Masukkan laporan (excel) area Wanita">
                            <span class="bas-dropdown-icon bas-icon-primary"><i class="fas fa-upload"></i></span>
                            Impor Loker Wanita
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== LEGEND ========== --}}
        <div class="bas-legend-bar d-flex align-items-center flex-wrap mb-5 px-5 py-3 rounded-lg">
            <span class="bas-legend-title mr-4">Status:</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-kosong"></span> Kosong</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-terisi"></span> Terisi (1/2)</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-penuh"></span> Penuh</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-rusak"></span> Perbaikan</span>
        </div>

        {{-- ========== TAB + GRID ========== --}}
        <div class="bas-tab-card rounded-xl p-6">

            {{-- Tab Pills --}}
            <ul class="nav bas-tab-nav mb-6" id="lokerTab" role="tablist">
                @foreach ($dashboardData as $label => $data)
                    @php $genderKey = ($label == 'Pria') ? 'L' : 'P'; @endphp
                    <li class="nav-item">
                        <a class="bas-tab-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab"
                            href="#tab_content_{{ $genderKey }}" role="tab">
                            <i class="{{ $label == 'Pria' ? 'fas fa-mars mr-2' : 'fas fa-venus mr-2' }}"></i>
                            Loker {{ $label }}
                            <span class="bas-tab-badge ml-2">{{ count($data['lockers']) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Tab Content --}}
            @include('loker.partials.table_loker')
        </div>

    </div>

    @include('loker.components.modal_detail')
    @include('loker.components.modal_plotting')
    @include('loker.components.modal_import')
@endsection

@push('scripts')
    <style>
        :root {
            --bas-primary: #F59E0B;
            --bas-primary-dark: #D97706;
            --bas-primary-light: #FEF3C7;
            --bas-success: #10B981;
            --bas-success-light: #D1FAE5;
            --bas-danger: #EF4444;
            --bas-danger-light: #FEE2E2;
            --bas-neutral: #6B7280;
            --bas-neutral-light: #F3F4F6;
            --bas-dark: #374151;
            --bas-dark-soft: #4B5563;
            --bas-border: #E5E7EB;
            --bas-surface: #FFFFFF;
            --bas-radius-sm: 8px;
            --bas-radius-md: 12px;
            --bas-radius-lg: 18px;
            --bas-transition: all 0.2s ease;
        }

        /* ---- HEADER ---- */
        .bas-header {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bas-header-icon {
            width: 56px;
            height: 56px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: var(--bas-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--bas-primary);
            flex-shrink: 0;
        }

        .bas-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            margin-bottom: 0;
        }

        .bas-header-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        .bas-stat-group {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .bas-stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 4px;
        }

        .bas-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1;
        }

        .bas-stat-value.bas-stat-success {
            color: #34D399;
        }

        .bas-stat-value.bas-stat-danger {
            color: #F87171;
        }

        .bas-stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* ---- SEARCH ---- */
        .bas-search-wrap {
            position: relative;
        }

        .bas-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bas-neutral);
            font-size: 14px;
            pointer-events: none;
            z-index: 2;
        }

        .bas-search-input {
            width: 100%;
            height: 44px;
            padding: 0 14px 0 40px;
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-md);
            background: var(--bas-surface);
            color: var(--bas-dark);
            font-size: 14px;
            outline: none;
            transition: var(--bas-transition);
            font-family: inherit;
        }

        .bas-search-input::placeholder {
            color: var(--bas-neutral);
        }

        .bas-search-input:focus {
            border-color: var(--bas-primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        /* ---- BUTTONS ---- */
        .bas-btn {
            display: inline-flex;
            align-items: center;
            height: 44px;
            padding: 0 20px;
            border-radius: var(--bas-radius-md);
            font-size: 14px;
            font-weight: 600;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: var(--bas-transition);
            white-space: nowrap;
            text-decoration: none;
            font-family: inherit;
        }

        .bas-btn:focus {
            outline: none;
            box-shadow: none;
        }

        .bas-btn-primary {
            background: var(--bas-primary);
            border-color: var(--bas-primary);
            color: #FFFFFF;
        }

        .bas-btn-primary:hover {
            background: var(--bas-primary-dark);
            border-color: var(--bas-primary-dark);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
            text-decoration: none;
        }

        .bas-btn-outline {
            background: var(--bas-surface);
            border-color: var(--bas-border);
            color: var(--bas-dark);
        }

        .bas-btn-outline:hover {
            background: var(--bas-neutral-light);
            border-color: #D1D5DB;
            color: var(--bas-dark);
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* ---- DROPDOWN ---- */
        .bas-dropdown {
            border-radius: var(--bas-radius-md) !important;
            border: 1.5px solid var(--bas-border) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .bas-dropdown-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--bas-neutral);
            padding: 6px 10px 4px;
        }

        .bas-dropdown-divider {
            height: 1px;
            background: var(--bas-border);
            margin: 8px 0;
        }

        .bas-dropdown-item {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            border-radius: var(--bas-radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--bas-dark);
            cursor: pointer;
            text-decoration: none;
            transition: var(--bas-transition);
        }

        .bas-dropdown-item:hover {
            background: var(--bas-neutral-light);
            color: var(--bas-dark);
            text-decoration: none;
        }

        .bas-dropdown-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .bas-icon-success {
            background: var(--bas-success-light);
            color: var(--bas-success);
        }

        .bas-icon-danger {
            background: var(--bas-danger-light);
            color: var(--bas-danger);
        }

        .bas-icon-primary {
            background: var(--bas-primary-light);
            color: var(--bas-primary-dark);
        }

        /* ---- LEGEND ---- */
        .bas-legend-bar {
            background: var(--bas-surface);
            border: 1.5px solid var(--bas-border);
        }

        .bas-legend-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--bas-neutral);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bas-legend-item {
            display: inline-flex;
            align-items: center;
            font-size: 12px;
            font-weight: 500;
            color: var(--bas-dark-soft);
            margin-right: 18px;
        }

        .bas-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            flex-shrink: 0;
        }

        .bas-dot-kosong {
            background: var(--bas-neutral-light);
            border: 1.5px solid var(--bas-border);
        }

        .bas-dot-terisi {
            background: var(--bas-success);
        }

        .bas-dot-penuh {
            background: var(--bas-danger);
        }

        .bas-dot-rusak {
            background: #9CA3AF;
        }

        /* ---- TAB CARD ---- */
        .bas-tab-card {
            background: var(--bas-surface);
            border: 1.5px solid var(--bas-border);
        }

        /* ---- TAB NAV ---- */
        .bas-tab-nav {
            display: flex;
            gap: 6px;
            border: none;
            background: var(--bas-neutral-light);
            border-radius: var(--bas-radius-md);
            padding: 5px;
            width: fit-content;
            margin-bottom: 0;
            list-style: none;
            padding-left: 5px;
        }

        .bas-tab-link {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            border-radius: var(--bas-radius-sm);
            font-size: 13px;
            font-weight: 600;
            color: var(--bas-neutral);
            cursor: pointer;
            border: 1.5px solid transparent;
            background: transparent;
            transition: var(--bas-transition);
            text-decoration: none;
        }

        .bas-tab-link:hover {
            color: var(--bas-dark);
            text-decoration: none;
        }

        .bas-tab-link.active {
            background: var(--bas-surface);
            color: var(--bas-dark);
            border-color: var(--bas-border);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            text-decoration: none;
        }

        .bas-tab-badge {
            background: var(--bas-neutral-light);
            border: 1px solid var(--bas-border);
            color: var(--bas-neutral);
            font-size: 11px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 99px;
        }

        .bas-tab-link.active .bas-tab-badge {
            background: var(--bas-primary-light);
            border-color: rgba(245, 158, 11, 0.3);
            color: var(--bas-primary-dark);
        }

        /* ---- LOKER GRID ---- */
        .bas-loker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 12px;
        }

        /* ---- LOKER CARD ---- */
        .bas-loker-card {
            position: relative;
            border-radius: var(--bas-radius-lg);
            padding: 16px 12px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            border: 1.5px solid var(--bas-border);
            background: var(--bas-surface);
            transition: all 0.22s ease;
            min-height: 120px;
            justify-content: center;
            overflow: hidden;
        }

        .bas-loker-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
            z-index: 1;
        }

        .bas-loker-card:active {
            transform: translateY(-2px) scale(0.98);
        }

        /* Status card styles */
        .bas-loker-kosong {
            background: var(--bas-surface);
            border-color: var(--bas-border);
        }

        .bas-loker-kosong:hover {
            border-color: #D1D5DB;
        }

        .bas-loker-terisi {
            background: #F0FDF4;
            border-color: #86EFAC;
        }

        .bas-loker-terisi:hover {
            border-color: var(--bas-success);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        }

        .bas-loker-penuh {
            background: #FFF5F5;
            border-color: #FCA5A5;
        }

        .bas-loker-penuh:hover {
            border-color: var(--bas-danger);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.15);
        }

        .bas-loker-rusak {
            background: #F9FAFB;
            border-color: #D1D5DB;
            opacity: 0.75;
        }

        .bas-loker-rusak:hover {
            opacity: 0.9;
            border-color: #9CA3AF;
        }

        /* Indicator dot */
        .bas-loker-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .bas-loker-kosong .bas-loker-indicator {
            background: #D1D5DB;
        }

        .bas-loker-terisi .bas-loker-indicator {
            background: var(--bas-success);
        }

        .bas-loker-penuh .bas-loker-indicator {
            background: var(--bas-danger);
        }

        .bas-loker-rusak .bas-loker-indicator {
            background: #9CA3AF;
        }

        /* Card inner text */
        .bas-loker-kat {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: var(--bas-neutral);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .bas-loker-terisi .bas-loker-kat {
            color: #065F46;
        }

        .bas-loker-penuh .bas-loker-kat {
            color: #991B1B;
        }

        .bas-loker-no {
            font-size: 17px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--bas-dark);
            margin-bottom: 7px;
            letter-spacing: -0.3px;
        }

        .bas-loker-terisi .bas-loker-no {
            color: #065F46;
        }

        .bas-loker-penuh .bas-loker-no {
            color: #991B1B;
        }

        .bas-loker-rusak .bas-loker-no {
            color: #6B7280;
        }

        .bas-loker-badge {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 99px;
            display: inline-block;
        }

        .bas-loker-kosong .bas-loker-badge {
            background: var(--bas-neutral-light);
            color: #6B7280;
            border: 1px solid var(--bas-border);
        }

        .bas-loker-terisi .bas-loker-badge {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #86EFAC;
        }

        .bas-loker-penuh .bas-loker-badge {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .bas-loker-rusak .bas-loker-badge {
            background: #F3F4F6;
            color: #6B7280;
            border: 1px solid #D1D5DB;
        }

        /* ---- EMPTY STATE ---- */
        .bas-empty-icon {
            width: 56px;
            height: 56px;
            background: var(--bas-neutral-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--bas-neutral);
            margin: 0 auto;
        }

        .bas-empty-text {
            font-size: 14px;
            color: var(--bas-neutral);
            margin: 0;
        }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 576px) {
            .bas-loker-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 8px;
            }

            .bas-stat-group {
                display: none !important;
            }

            .bas-tab-nav {
                width: 100%;
            }

            .bas-tab-link {
                flex: 1;
                justify-content: center;
                font-size: 12px;
                padding: 8px 10px;
            }
        }

        @media (max-width: 768px) {
            .bas-btn {
                font-size: 13px;
                padding: 0 14px;
                height: 40px;
            }
        }
    </style>

    <script>
        // Global State Management
        let state = {
            gender: 'L',
            lokerNo: '',
            defaultFoto: "{{ asset('assets/media/users/default.jpg') }}"
        };

        function refreshTooltips() {
            $('[data-toggle="tooltip"]').tooltip('dispose');
            $('[data-toggle="tooltip"]').tooltip({
                boundary: 'window',
                trigger: 'hover',
                html: true
            });
        }

        $(document).ready(function() {
            // Setup CSRF Token untuk semua request POST
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 1. Inisialisasi Tooltip saat halaman pertama dimuat
            refreshTooltips();

            // 2. Refresh Tooltip otomatis saat MODAL terbuka
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('[data-toggle="tooltip"]').tooltip({
                    boundary: 'window'
                });
            });

            // 3. Handling Input File Import (Update Label Nama File)
            $('body').on('change', '#customFile', function(e) {
                let fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // 4. TAB GENDER TRACKING
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                state.gender = $(e.target).attr("href").includes('_L') ? 'L' : 'P';

                $('#search_loker_input').val('');

                const container = $(`#tab_content_${state.gender}`);
                container.find('.loker-wrapper').show();
                container.find('.empty-state').addClass('d-none');
            });

            // 5. SEARCH UNIT LOKER (With Debounce, Tooltip Refresh & Enter to Open)
            let searchTimer;
            $('#search_loker_input').on('keyup', function(e) {
                clearTimeout(searchTimer);

                let value = $(this).val().trim();
                const container = $(`#tab_content_${state.gender}`);
                const items = container.find('.loker-wrapper');
                const emptyState = container.find('.empty-state');

                if (e.which === 13 && value.length > 0) {
                    items.show();
                    emptyState.addClass('d-none');
                    refreshTooltips();

                    if (!isNaN(value)) {
                        const card = container.find(`.bas-loker-card[data-no="${value}"]`);
                        if (card.length > 0) {
                            showDetail(state.gender, value);
                        } else {
                            cariDataGlobal(value);
                        }
                    } else {
                        cariDataGlobal(value);
                    }

                    $(this).val('');
                    return;
                }

                searchTimer = setTimeout(function() {
                    let keyword = value.toLowerCase();

                    if (value === '') {
                        items.show();
                        emptyState.addClass('d-none');
                        refreshTooltips();
                        return;
                    }

                    let found = 0;
                    items.each(function() {
                        const noLoker = $(this).find('.bas-loker-card').data('no')
                            .toString()
                            .toLowerCase();
                        const isMatch = noLoker.includes(keyword);

                        $(this).toggle(isMatch);
                        if (isMatch) found++;
                    });

                    if (found === 0) {
                        emptyState.removeClass('d-none');
                        emptyState.find('.empty-state-text').text(
                            `Loker nomor "${value}" tidak ditemukan`);
                    } else {
                        emptyState.addClass('d-none');
                    }

                    refreshTooltips();
                }, 250);
            });

            function cariDataGlobal(keyword) {
                KTApp.blockPage({
                    message: 'Mencari data penghuni...'
                });

                $.get("{{ route('loker.search-global') }}", {
                    q: keyword,
                    gender: state.gender
                }).done(function(res) {
                    KTApp.unblockPage();
                    if (res.success) {
                        showDetail(res.gender, res.no_loker);
                    } else {
                        Swal.fire('Tidak Ditemukan',
                            `Data "${keyword}" tidak terdaftar di loker ${state.gender === 'L' ? 'Pria' : 'Wanita'}`,
                            'info');
                    }
                }).fail(() => {
                    KTApp.unblockPage();
                    Swal.fire('Error', 'Gagal mencari data', 'error');
                })
            }

            // 6. MANUAL SELECT LOKER LISTENER (Form Plotting)
            $('#select_no_loker').on('change', function() {
                const val = $(this).val();
                const btn = $('#btnSimpanPlot');
                if (val && val !== '') {
                    btn.removeAttr('disabled').removeClass('btn-light').addClass('btn-primary shadow-sm');
                } else {
                    btn.attr('disabled', true).addClass('btn-light').removeClass('btn-primary shadow-sm');
                }
            });

            // 7. HANDLING SUBMIT IMPORT AJAX
            $('#formImport').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                KTApp.block('#modalImport .modal-content', {
                    message: 'Sedang memproses Excel & Sinkronisasi...'
                });

                $.ajax({
                    url: "{{ route('loker.import') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        KTApp.unblock('#modalImport .modal-content');
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                confirmButtonText: 'Mantap'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        KTApp.unblock('#modalImport .modal-content');
                        let msg = err.responseJSON ? err.responseJSON.message :
                            'Terjadi kesalahan saat import';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });

        // function showDetail(genderCode, no) {
        //     state.gender = genderCode;
        //     state.lokerNo = no;
        //     const label = genderCode === 'L' ? ' (Pria)' : ' (Wanita)';

        //     $('#detail_no_label').text(`#${no}${label}`);
        //     $('#detail_penghuni_list').html(
        //         '<tr><td colspan="6" class="text-center p-5"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat Data...</td></tr>'
        //     );

        //     $('#btn_rusak').off('click').on('click', () => updateStatusLoker('rusak', genderCode, no));
        //     $('#btn_aktif').off('click').on('click', () => updateStatusLoker('aktif', genderCode, no));

        //     $.get(`{{ url('loker/detail') }}/${genderCode}/${no}`)
        //         .done(function(data) {
        //             let html = '';
        //             if (data && data.length > 0) {
        //                 data.forEach(p => {
        //                     html += `<tr>
    //                     <td class="font-weight-bold text-primary">${p.nik}</td>
    //                     <td><span class="text-dark-75 font-weight-bolder d-block font-size-lg">${p.nama}</span></td>
    //                     <td><span class="label label-inline label-light-success font-weight-bold">${p.kategori.toUpperCase()}</span></td>
    //                     <td><span class="text-muted font-weight-bold">${p.divisi || '-'}</span></td>
    //                     <td>${p.tgl_masuk}</td>
    //                     <td class="text-right">
    //                         <button class="btn btn-icon btn-light-primary btn-xs mr-1" onclick="pindahLoker('${p.nik}')" data-toggle="tooltip" title="Relokasi">
    //                             <i class="flaticon-refresh"></i>
    //                         </button>
    //                         <button class="btn btn-icon btn-light-danger btn-xs" onclick="konfirmasiTarikKunci('${p.id}', '${p.nama}')" data-toggle="tooltip" title="Tarik Kunci">
    //                             <i class="flaticon2-logout-1"></i>
    //                         </button>
    //                     </td>
    //                 </tr>`;
        //                 });
        //             } else {
        //                 html =
        //                     '<tr><td colspan="6" class="text-center p-10 text-muted">Unit Kosong / Tidak Ada Penghuni</td></tr>';
        //             }
        //             $('#detail_penghuni_list').html(html);
        //             refreshTooltips();
        //             $('#modalDetail').modal('show');
        //         })
        //         .fail(() => Swal.fire('Error', 'Gagal memuat detail', 'error'));
        // }

        function showDetail(genderCode, no) {
            state.gender = genderCode;
            state.lokerNo = no;
            const label = genderCode === 'L' ? ' (Pria)' : ' (Wanita)';

            $('#detail_no_label').text(`#${no}${label}`);
            $('#detail_penghuni_list').html(
                '<tr><td colspan="6" class="text-center p-5"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat Data...</td></tr>'
            );

            // Reset tombol (hide dulu semua)
            $('#btn_rusak, #btn_aktif').hide();

            $.get(`{{ url('loker/detail') }}/${genderCode}/${no}`)
                .done(function(res) {
                    let html = '';

                    if (res.status_unit === 'rusak') {
                        $('#btn_aktif').show();
                        $('#btn_rusak').hide();
                    } else {
                        $('#btn_rusak').show();
                        $('#btn_aktif').hide();
                    }

                    // Loop data penghuni (res.data)
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(p => {
                            html += `<tr>
                        <td class="font-weight-bold text-primary">${p.nik}</td>
                        <td><span class="text-dark-75 font-weight-bolder d-block font-size-lg">${p.nama}</span></td>
                        <td><span class="label label-inline label-light-success font-weight-bold">${p.kategori.toUpperCase()}</span></td>
                        <td><span class="text-muted font-weight-bold">${p.divisi || '-'}</span></td>
                        <td>${p.tgl_masuk}</td>
                        <td class="text-right">
                            <button class="btn btn-icon btn-light-primary btn-xs mr-1" onclick="pindahLoker('${p.nik}')" data-toggle="tooltip" title="Relokasi">
                                <i class="flaticon-refresh"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger btn-xs" onclick="konfirmasiTarikKunci('${p.id}', '${p.nama}')" data-toggle="tooltip" title="Tarik Kunci">
                                <i class="flaticon2-logout-1"></i>
                            </button>
                        </td>
                    </tr>`;
                        });
                    } else {
                        html =
                            '<tr><td colspan="6" class="text-center p-10 text-muted">Unit Kosong / Tidak Ada Penghuni</td></tr>';
                    }

                    $('#detail_penghuni_list').html(html);

                    // Re-bind click event dengan data terbaru
                    $('#btn_rusak').off('click').on('click', () => updateStatusLoker('rusak', genderCode, no));
                    $('#btn_aktif').off('click').on('click', () => updateStatusLoker('aktif', genderCode, no));

                    refreshTooltips();
                    $('#modalDetail').modal('show');
                })
                .fail(() => Swal.fire('Error', 'Gagal memuat detail', 'error'));
        }

        function updateStatusLoker(status, gender, no) {
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: `Ubah status unit ${no} menjadi ${status.toUpperCase()}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Update!',
                confirmButtonColor: status === 'rusak' ? '#EF4444' : '#10B981'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post("{{ url('loker/update-status') }}", {
                        status,
                        gender,
                        no_loker: no
                    }).done(() => location.reload());
                }
            });
        }

        // --- MODAL PLOTTING ---

        function openModalPlotting(defaultNik = '') {
            $('#formPlotting')[0].reset();
            resetPlotFields();

            $('#plot_nik').prop('readonly', false).removeClass('bg-light');
            $('#plot_foto_img').attr('src', state.defaultFoto);

            if (defaultNik) {
                $('#plot_nik').val(defaultNik).prop('readonly', true).addClass('bg-light');
                $('#modalPlotting').modal('show');

                setTimeout(() => {
                    cariKaryawan();
                }, 500);
            } else {
                $('#modalPlotting').modal('show');
            }
        }

        function cariKaryawan() {
            const nik = $('#plot_nik').val();
            if (!nik) return;

            KTApp.block('#modalPlotting .modal-content', {
                message: 'Mengecek data...'
            });

            $.get(`{{ url('loker/search-karyawan') }}/${nik}`)
                .done(function(res) {
                    KTApp.unblock('#modalPlotting .modal-content');
                    if (res.success) {
                        const d = res.data;
                        $('#plot_nama').val(d.nama);
                        $('#plot_dept').val(d.divisi);
                        $('#plot_gender_label').val(d.gender === 'L' ? 'Laki-laki' : 'Perempuan');
                        $('#plot_gender_val').val(d.gender);
                        $('#plot_kategori_val').val(d.kategori);
                        $('#plot_kategori_label').val(d.kategori ? d.kategori.replace('_', ' ').toUpperCase() : '-');

                        if (d.no_loker) {
                            $('#plot_loker_lama').val("LOKER " + d.no_loker);
                            $('#modalPlottingTitle').text("Pindahkan Loker Karyawan");
                        } else {
                            $('#plot_loker_lama').val("Belum Memiliki Loker");
                            $('#modalPlottingTitle').text("Penempatan Karyawan Baru");
                        }

                        loadFoto(d.nik);
                        loadAvailableLockers(d.gender, d.kategori, function() {
                            getSuggestion(d.nik, d.gender, d.kategori);
                        });
                    } else {
                        resetPlotFields();
                        Swal.fire('Info', res.message, 'info');
                    }
                })
                .fail(() => {
                    KTApp.unblock('#modalPlotting .modal-content');
                    resetPlotFields();
                    Swal.fire('Error', 'Gagal memproses data karyawan.', 'error');
                });
        }

        function resetPlotFields() {
            $('#plot_nama, #plot_dept, #plot_loker_lama, #plot_gender_label, #plot_kategori_label').val('-');
            $('#plot_gender_val, #plot_kategori_val').val('');
            $('#plot_foto_img').attr('src', state.defaultFoto);
            $('#select_no_loker').empty().append('<option value="">-- Pilih Unit --</option>');
            $('#btnSimpanPlot').attr('disabled', true).addClass('btn-light').removeClass('btn-primary shadow-sm');
        }

        function loadAvailableLockers(gender, kategori, callback) {
            let dropdown = $('#select_no_loker');
            dropdown.empty().append('<option value="" selected disabled>⏳ Menyiapkan unit...</option>');

            $.get(`{{ url('loker/available') }}/${gender}/${kategori || 'non_staff'}`)
                .done(function(data) {
                    dropdown.empty().append('<option value="">-- Pilih Unit --</option>');
                    if (data.length > 0) {
                        data.forEach(item => dropdown.append(
                            `<option value="${item.no_loker}">Unit ${item.no_loker}</option>`));
                    } else {
                        dropdown.append('<option value="" selected disabled>❌ Tidak ada unit tersedia</option>');
                    }
                    if (callback) callback();
                });
        }

        function getSuggestion(nik, gender, kategori) {
            $.get("{{ route('loker.api-suggest-loker') }}", {
                    nik,
                    gender,
                    kategori
                })
                .done(function(res) {
                    if (res.rekomendasi_loker && res.rekomendasi_loker !== 'penuh') {
                        const recoValue = res.rekomendasi_loker.toString();
                        if ($(`#select_no_loker option[value='${recoValue}']`).length > 0) {
                            $('#select_no_loker').val(recoValue).trigger('change');
                        }
                    }
                });
        }

        function simpanPlotting() {
            const formData = $('#formPlotting').serialize();
            KTApp.block('#modalPlotting .modal-content', {
                message: 'Menyimpan...'
            });

            $.post("{{ route('loker.store') }}", formData)
                .done(function(res) {
                    KTApp.unblock('#modalPlotting .modal-content');
                    if (res.status === 'success') {
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }).fail(() => {
                    KTApp.unblock('#modalPlotting .modal-content');
                    Swal.fire('Error', 'Gagal menyimpan data', 'error');
                });
        }

        function loadFoto(nik) {
            $.get(`{{ url('loker/get-foto') }}/${nik}`)
                .done(function(f) {
                    $('#plot_foto_img').attr('src', (f.success && f.image) ? f.image : state.defaultFoto);
                }).fail(() => $('#plot_foto_img').attr('src', state.defaultFoto));
        }

        function konfirmasiTarikKunci(id, nama) {
            Swal.fire({
                title: 'Tarik Kunci?',
                text: `Keluarkan ${nama} dari unit ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluarkan',
                confirmButtonColor: '#EF4444'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post("{{ route('loker.tarik-kunci') }}", {
                        id
                    }).done(() => showDetail(state.gender, state.lokerNo));
                }
            });
        }

        function pindahLoker(nik) {
            $('#modalDetail').modal('hide');
            setTimeout(() => openModalPlotting(nik), 400);
        }

        function openModalImport(gender) {
            $('#formImport')[0].reset();
            $('#customFile').next('.custom-file-label').html('Pilih file Excel...');
            $('#importGenderLabel, #importGenderLabelSub').text(gender === 'L' ? 'Pria' : 'Wanita');
            $('#importGenderVal').val(gender);
            $('#modalImport').modal('show');
        }
    </script>
@endpush
