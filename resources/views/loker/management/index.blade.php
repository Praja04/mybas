@extends('layouts.base')

@push('styles')
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

        /* ---- HEADER & STATS ---- */
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
        }

        .bas-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 0;
        }

        .bas-header-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
        }

        .bas-stat-group {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .bas-stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
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

        /* ---- SEARCH BAR ---- */
        .bas-search-wrap {
            position: relative;
            width: 100%;
            max-width: 300px;
        }

        .bas-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bas-neutral);
            z-index: 2;
        }

        .bas-search-input {
            width: 100%;
            height: 44px;
            padding: 0 14px 0 40px;
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-md);
            transition: var(--bas-transition);
            outline: none;
        }

        .bas-search-input:focus {
            border-color: var(--bas-primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        /* ---- LOKER GRID & CARD ---- */
        .bas-loker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
            gap: 15px;
        }

        .bas-loker-card {
            position: relative;
            border-radius: var(--bas-radius-lg);
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1.5px solid var(--bas-border);
            background: var(--bas-surface);
            transition: all 0.22s ease;
            min-height: 120px;
            justify-content: center;
        }

        .bas-loker-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
        }

        /* Status Colors */
        .bas-loker-kosong {
            background: var(--bas-surface);
        }

        .bas-loker-terisi {
            background: #F0FDF4;
            border-color: #86EFAC;
        }

        .bas-loker-penuh {
            background: #FFF5F5;
            border-color: #FCA5A5;
        }

        .bas-loker-rusak {
            background: #F9FAFB;
            border-color: #D1D5DB;
            opacity: 0.75;
        }

        /* Inner Card Elements */
        .bas-loker-no {
            font-size: 18px;
            font-weight: 700;
            color: var(--bas-dark);
            margin-bottom: 5px;
        }

        .bas-loker-terisi .bas-loker-no {
            color: #065F46;
        }

        .bas-loker-penuh .bas-loker-no {
            color: #991B1B;
        }

        .bas-loker-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .bas-loker-kosong .bas-loker-badge {
            background: var(--bas-neutral-light);
            color: #6B7280;
        }

        .bas-loker-terisi .bas-loker-badge {
            background: #DCFCE7;
            color: #166534;
        }

        .bas-loker-penuh .bas-loker-badge {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* ---- LEGEND BAR ---- */
        .bas-legend-bar {
            background: var(--bas-surface);
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-md);
            padding: 12px 20px;
        }

        .bas-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        /* ---- BUTTONS ---- */
        .bas-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            padding: 0 24px;
            border-radius: var(--bas-radius-md);
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: var(--bas-transition);
            white-space: nowrap;
            text-decoration: none;
            letter-spacing: 0.5px;
        }

        .bas-btn-primary {
            background: var(--bas-primary) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
        }

        .bas-btn-primary:hover {
            background: var(--bas-primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.4);
            color: #FFFFFF !important;
        }

        .bas-btn-primary:active {
            transform: translateY(0);
        }

        /* Biar icon di dalem tombol rapi */
        .bas-btn i {
            font-size: 16px;
        }

        /* Warna Aktif pada Tab Navigasi */
        .bas-tab-nav .nav-link.active,
        .bas-tab-link.active {
            background: var(--bas-surface) !important;
            color: var(--bas-primary-dark) !important;
            /* Warna teks kuning gelap */
            border: 1.5px solid var(--bas-primary) !important;
            /* Border kuning */
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15) !important;
        }

        /* Styling Pagination agar sesuai warna Amber */
        .pagination .page-item.active .page-link {
            background-color: var(--bas-primary) !important;
            border-color: var(--bas-primary) !important;
            color: #ffffff !important;
        }

        .pagination .page-link {
            color: var(--bas-dark);
            border-radius: 8px;
            margin: 0 3px;
            border: 1px solid var(--bas-border);
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background-color: var(--bas-primary-light);
            color: var(--bas-primary-dark);
            border-color: var(--bas-primary);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .bas-stat-group {
                display: none !important;
            }

            .bas-loker-grid {
                grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
                gap: 10px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-8 py-6">

        {{-- ========== HEADER ========== --}}
        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header rounded-xl d-flex align-items-center justify-content-between flex-wrap p-7">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title mb-1">Manajemen Unit Loker</h2>
                            <div class="bas-header-sub">Pengaturan Inventaris & Master Data &bull; PT Bumi Alam Segar</div>
                        </div>
                    </div>

                    <div class="bas-stat-group d-flex align-items-center mt-4 mt-md-0 rounded-lg px-5 py-3">
                        <div class="bas-stat-item text-center px-5">
                            <div class="bas-stat-label">Total Pria</div>
                            <div class="bas-stat-value">
                                <span id="total-pria">{{ number_format($lokerPria->total()) }}</span>
                            </div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5">
                            <div class="bas-stat-label">Total Wanita</div>
                            <div class="bas-stat-value">
                                <span id="total-wanita">{{ number_format($lokerWanita->total()) }}</span>
                            </div>
                        </div>
                        <div class="bas-stat-divider"></div>
                        <div class="bas-stat-item text-center px-5">
                            <div class="bas-stat-label">Status Sistem</div>
                            <div class="bas-stat-value bas-stat-success">TERSEDIA</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 1: FORM TAMBAH UNIT --}}
        <div class="card card-custom gutter-b shadow-sm overflow-hidden"
            style="border-radius: var(--bas-radius-lg); border: 1.5px solid var(--bas-border);">
            <div class="card-header border-0 py-5"
                style="background: #1F2937; border-bottom: 3px solid var(--bas-primary) !important;">
                <div class="card-title m-0">
                    <h3 class="font-weight-bolder text-white">
                        <i class="fas fa-plus-circle text-warning mr-3"></i>
                        <span class="card-label text-white">Tambah Unit Massal</span>
                        <span class="text-muted font-size-sm d-block mt-1 font-weight-normal">Generate nomor loker secara
                            otomatis ke sistem</span>
                    </h3>
                </div>
            </div>

            <form id="formBulkAdd">
                @csrf
                <div class="card-body p-8">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label class="font-weight-bolder text-dark mb-2">
                                    <i class="fas fa-venus-mars mr-1 text-muted"></i> Tipe Loker
                                </label>
                                <div class="bas-search-wrap">
                                    <select name="kode_rak" class="form-control bas-search-input pl-5"
                                        style="appearance: auto;">
                                        <option value="LP">Loker Pria (P)</option>
                                        <option value="LW">Loker Wanita (W)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label class="font-weight-bolder text-dark mb-2">
                                    <i class="fas fa-layer-group mr-1 text-muted"></i> Jumlah Unit
                                </label>
                                <div class="bas-search-wrap">
                                    <i class="fas fa-sort-numeric-up bas-search-icon"></i>
                                    <input type="number" name="jumlah" class="form-control bas-search-input"
                                        placeholder="Contoh: 20" required min="1" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- PERBAIKAN: Tag form ganda di sini sudah dihapus --}}
                            <div class="form-group mb-md-0">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="bas-btn bas-btn-primary w-100">
                                    <i class="fas fa-rocket mr-2"></i> PROSES
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- SECTION 2: DAFTAR UNIT --}}
        <div class="card card-custom shadow-sm" style="border-radius: 18px; border: 1.5px solid #E5E7EB;">
            <div class="card-header border-0 pt-6 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bolder text-dark">
                    <i class="fas fa-list text-warning mr-3"></i> Inventaris Loker Kosong
                </h3>

                <ul class="nav nav-pills bas-tab-nav">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab_pria">
                            <i class="fas fa-male mr-2"></i> LOKER PRIA
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab_wanita">
                            <i class="fas fa-female mr-2"></i> LOKER WANITA
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="table-container">
                    <div class="tab-pane fade show active" id="tab_pria">
                        @include('loker.management.partials.table_management', [
                            'data' => $lokerPria,
                            'gender' => 'tab_pria',
                        ])
                    </div>
                    <div class="tab-pane fade" id="tab_wanita">
                        @include('loker.management.partials.table_management', [
                            'data' => $lokerWanita,
                            'gender' => 'tab_wanita',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function fetch_data(page, gender, pushToHistory = true) {
            let container = (gender === 'tab_pria') ? '#tab_pria' : '#tab_wanita';
            let counterElement = (gender === 'tab_pria') ? '#total-pria' : '#total-wanita';

            $('.nav-pills a[href="#' + gender + '"]').tab('show');
            $(container).css('opacity', '0.5');

            $.ajax({
                url: window.location.pathname,
                method: "GET",
                data: {
                    page: page,
                    gender: gender
                },
                success: function(response) {
                    let $html = $(`<div>${response}</div>`);
                    let isEmpty = $html.find('.bas-empty-text').length > 0;

                    if (isEmpty && page > 1) {
                        fetch_data(page - 1, gender, pushToHistory);
                        return;
                    }

                    $(container).html(response);
                    $(container).css('opacity', '1');

                    let newTotal = $html.find('#data-total-count').val();
                    if (newTotal !== undefined) {
                        $(counterElement).text(newTotal);
                    }

                    if (pushToHistory) {
                        const newUrl = window.location.pathname + "?page=" + page + "&gender=" + gender;
                        window.history.pushState({
                            page: page,
                            gender: gender
                        }, "", newUrl);
                    }

                    $('[data-toggle="tooltip"]').tooltip();
                },
                error: function() {
                    $(container).css('opacity', '1');
                    Swal.fire('Error', 'Gagal memuat data.', 'error');
                }
            });
        }

        function hapusLoker(id, label) {
            let button = $(`button[onclick*="'${id}'"]`);
            let wrapper = button.closest('[id^="wrapper-"]')

            if (wrapper.length === 0) {
                console.error("Wrapper tidak ditemukan!");
                return;
            }

            let genderTab = button.closest('.tab-pane').attr('id');
            let activePage = wrapper.find('.pagination .page-item.active').text().trim() || 1;

            Swal.fire({
                title: 'Hapus Loker?',
                text: `Yakin mau hapus loker nomor ${label}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, Hapus!',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();

                    $.ajax({
                        url: "/loker/delete/" + id,
                        method: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}',
                            page: activePage,
                            gender: genderTab
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                let container = (genderTab === 'tab_pria') ? '#tab_pria' :
                                    '#tab_wanita';
                                let counterElement = (genderTab === 'tab_pria') ? '#total-pria' :
                                    '#total-wanita';

                                $(container).html(res.html);
                                $(counterElement).text(res.newTotal);

                                $('[data-toggle="tooltip"]').tooltip();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                let $html = $(`<div>${res.html}</div>`);
                                if ($html.find('.bas-empty-text').length > 0 && activePage > 1) {
                                    fetch_data(activePage - 1, genderTab, true);
                                }
                            }
                        },
                        error: function(err) {
                            let msg = err.responseJSON ? err.responseJSON.message :
                                'Gagal menghapus data';
                            Swal.fire('Gagal!', msg, 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            // Fix: Update URL saat ganti tab dengan selector yang benar
            $('.nav-pills a').on('shown.bs.tab', function(e) {
                let gender = $(e.target).attr('href').replace('#', '');
                // Selector diperbaiki dari .page-item-active ke .page-item.active
                let activePage = parseInt($(`#${gender} .page-item.active`).text().trim()) || 1;

                const newUrl = window.location.pathname + "?page=" + activePage + "&gender=" + gender;
                window.history.pushState({
                    page: activePage,
                    gender: gender
                }, "", newUrl);
            });

            // Handle pagination clicks
            $(document).on('click', '.tab-pane .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let page = url.split('page=')[1];
                let gender = $(this).closest('.tab-pane').attr('id');
                fetch_data(page, gender, true);
            });

            // Handle Browser Back/Forward
            window.onpopstate = function(event) {
                const params = new URLSearchParams(window.location.search);
                let page = params.get('page') || 1;
                let gender = params.get('gender') || 'tab_pria';
                fetch_data(page, gender, false);
            };

            // Form Submit
            $('#formBulkAdd').on('submit', function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                let selectedKode = $('select[name="kode_rak"]').val();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Tambah unit massal?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.addClass('spinner spinner-white spinner-right disabled').prop(
                            'disabled', true);
                        $.ajax({
                            url: "{{ route('loker.bulk-add') }}",
                            method: "POST",
                            data: $(this).serialize(),
                            success: function(res) {
                                btn.removeClass(
                                        'spinner spinner-white spinner-right disabled')
                                    .prop('disabled', false);
                                Swal.fire('Berhasil!', res.message, 'success').then(
                                    () => {
                                        let targetTab = (selectedKode === 'LP') ?
                                            'tab_pria' : 'tab_wanita';
                                        fetch_data(1, targetTab, true);
                                    });
                            },
                            error: function(err) {
                                btn.removeClass(
                                        'spinner spinner-white spinner-right disabled')
                                    .prop('disabled', false);
                                Swal.fire('Gagal!', err.responseJSON.message, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
