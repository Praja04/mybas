@extends('layouts.base')

@section('content')
    <div class="container-fluid px-8 py-6">

        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header rounded-xl d-flex align-items-center justify-content-between flex-wrap p-7">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title mb-1">Dasbor Manajemen Fasilitas Loker</h2>
                            <div class="bas-header-sub">Sistem Pemantauan Area Loker Terpadu &bull; PT Bumi Alam Segar</div>
                        </div>
                    </div>

                    <div class="bas-stat-group d-flex align-items-center mt-4 mt-md-0 rounded-lg px-5 py-3">
                        <div class="bas-stat-item text-center px-5">
                            <div class="bas-stat-label">Total Loker</div>
                            <div class="bas-stat-value">{{ number_format($grandTotal['total']) }}</div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5" data-toggle="tooltip"
                            title="Jumlah loker yang berstatus tersedia untuk dialokasikan kepada karyawan.">
                            <div class="bas-stat-label">Tersedia</div>
                            <div class="bas-stat-value bas-stat-success">{{ number_format($grandTotal['tersedia']) }}</div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5" data-toggle="tooltip"
                            title="Jumlah loker yang sedang dalam proses pemeliharaan (maintenance) dan tidak dapat dialokasikan.">
                            <div class="bas-stat-label">Dlm. Pemeliharaan</div>
                            <div class="bas-stat-value bas-stat-danger">{{ number_format($grandTotal['rusak']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-6 align-items-center">
            <div class="col-md-5 col-lg-4 mb-3 mb-md-0">
                <div class="bas-search-wrap">
                    <span class="bas-search-icon"><i class="flaticon2-search-1"></i></span>
                    <input type="text" id="search_loker_input" class="bas-search-input"
                        placeholder="Cari nomor loker, NIK, atau nama..." data-toggle="tooltip" data-placement="top"
                        title="Pencarian cepat berdasarkan nomor loker, NIK, atau nama karyawan pada tab yang aktif.">
                </div>
            </div>
            <div class="col-md-7 col-lg-8 text-right">
                @if (in_array('loker_operator', $permissions))
                    <button type="button" onclick="openModalPlotting()" class="bas-btn bas-btn-primary mr-2"
                        data-toggle="tooltip" title="Daftarkan alokasi penempatan karyawan baru ke dalam loker.">
                        <i class="fas fa-plus-circle mr-2"></i> Penempatan Baru
                    </button>
                @endif

                @if (in_array('loker_master', $permissions))
                    <div class="dropdown d-inline-block">
                        <button class="bas-btn bas-btn-outline" data-toggle="dropdown">
                            <i class="fas fa-file-export mr-2"></i> Manajemen Data <i
                                class="fas fa-chevron-down ml-2 font-size-xs"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right bas-dropdown shadow border-0 p-3"
                            style="min-width: 220px;">
                            <div class="bas-dropdown-section-label">Unduh Laporan (Excel)</div>
                            <a class="bas-dropdown-item" href="{{ route('loker.export', 'L') }}" data-toggle="tooltip"
                                data-placement="left" title="Unduh data laporan alokasi loker area Pria.">
                                <span class="bas-dropdown-icon bas-icon-success"><i class="far fa-file-excel"></i></span>
                                Loker Area Pria
                            </a>
                            <a class="bas-dropdown-item" href="{{ route('loker.export', 'P') }}" data-toggle="tooltip"
                                data-placement="left" title="Unduh data laporan alokasi loker area Wanita.">
                                <span class="bas-dropdown-icon bas-icon-danger"><i class="far fa-file-excel"></i></span>
                                Loker Area Wanita
                            </a>
                            <div class="bas-dropdown-divider"></div>
                            <div class="bas-dropdown-section-label">Unggah Data Loker</div>
                            <a class="bas-dropdown-item" href="javascript:void(0)" onclick="openModalImport('L')"
                                data-toggle="tooltip" data-placement="left"
                                title="Unggah file pembaruan master loker area Pria.">
                                <span class="bas-dropdown-icon bas-icon-primary"><i class="fas fa-upload"></i></span>
                                Unggah (Import) Loker Pria
                            </a>
                            <a class="bas-dropdown-item" href="javascript:void(0)" onclick="openModalImport('P')"
                                data-toggle="tooltip" data-placement="left"
                                title="Unggah file pembaruan master loker area Wanita.">
                                <span class="bas-dropdown-icon bas-icon-primary"><i class="fas fa-upload"></i></span>
                                Unggah (Import) Loker Wanita
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bas-legend-bar d-flex align-items-center flex-wrap mb-5 px-5 py-3 rounded-lg">
            <span class="bas-legend-title mr-4">Status:</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-kosong"></span> Tersedia</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-terisi"></span> Terisi Sebagian</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-penuh"></span> Kapasitas Penuh</span>
            <span class="bas-legend-item"><span class="bas-dot bas-dot-rusak"></span> Dalam Pemeliharaan</span>
        </div>

        <div class="bas-tab-card rounded-xl p-6">

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

        .bas-tab-card {
            background: var(--bas-surface);
            border: 1.5px solid var(--bas-border);
        }

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

        @media (max-width: 576px) {
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
        const canOperator = @json(in_array('loker_operator', $permissions));

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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            refreshTooltips();

            $('.modal').on('shown.bs.modal', function() {
                $(this).find('[data-toggle="tooltip"]').tooltip({
                    boundary: 'window'
                });
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                state.gender = $(e.target).attr("href").includes('_L') ? 'L' : 'P';
                $('#search_loker_input').val('');
                const container = $(`#tab_content_${state.gender}`);
                container.find('.loker-wrapper').show();
                container.find('.empty-state').addClass('d-none');
                refreshTooltips();
            });

            let searchTimer;
            $('#search_loker_input').on('keyup', function(e) {
                clearTimeout(searchTimer);
                let value = $(this).val().trim();
                const container = $(`#tab_content_${state.gender}`);
                const items = container.find('.loker-wrapper');
                const emptyState = container.find('.empty-state');

                if (e.which === 13 && value.length > 0) {
                    const card = container.find(`.bas-loker-card[data-no="${value}"]`);

                    if (card.length > 0) {
                        showDetail(state.gender, value);
                        items.show();
                        emptyState.addClass('d-none');
                    } else {
                        items.show();
                        emptyState.addClass('d-none');
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
                        const card = $(this).find('.bas-loker-card');
                        const noLoker = card.data('no') ? card.data('no').toString()
                            .toLowerCase() : '';
                        const isMatch = noLoker.includes(keyword);

                        $(this).toggle(isMatch);
                        if (isMatch) found++;
                    });

                    if (found === 0) {
                        emptyState.removeClass('d-none');
                        emptyState.find('.empty-state-text').text(
                            `Nomor loker "${value}" tidak ditemukan. Tekan Enter untuk pencarian NIK/Nama secara global.`
                        );
                    } else {
                        emptyState.addClass('d-none');
                    }

                    refreshTooltips();
                }, 250);
            });
        });

        function cariDataGlobal(keyword) {
            const cleanKeyword = keyword.trim();

            if (!cleanKeyword) return;

            KTApp.blockPage({
                message: 'Memeriksa data penghuni...'
            });

            $.get("{{ route('loker.search-global') }}", {
                q: cleanKeyword,
                gender: state.gender
            }).done(function(res) {
                KTApp.unblockPage();

                if (res.success) {
                    if (res.is_wrong_tab) {
                        Swal.fire({
                            title: 'Informasi Penempatan',
                            text: res.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Beralih ke Loker ' + (res.gender === 'L' ? 'Pria' :
                                'Wanita')
                        }).then((result) => {
                            if (result.isConfirmed) {
                                state.gender = res.gender;
                                let targetTab = `#tab_content_${res.gender}`;
                                $(`a[href="${targetTab}"]`).tab('show');
                                setTimeout(() => {
                                    showDetail(res.gender, res.no_loker);
                                }, 400);
                            }
                        });
                    } else {
                        showDetail(res.gender, res.no_loker);
                    }
                } else {
                    const msg = res.message ||
                        `Informasi "${cleanKeyword}" tidak ditemukan pada area loker ${state.gender === 'L' ? 'Pria' : 'Wanita'}`;
                    Swal.fire({
                        title: 'Data Tidak Ditemukan',
                        text: msg,
                        icon: 'info',
                        confirmButtonText: 'Tutup'
                    });
                }
            }).fail(() => {
                KTApp.unblockPage();
                Swal.fire('Error Koneksi', 'Gagal terhubung ke server untuk pencarian global.', 'error');
            });
        }
    </script>
@endpush
