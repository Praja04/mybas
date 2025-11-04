@extends('pos-security.layouts.base')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h5 class="mb-3">Dashboard Akses Pengunjung</h5>

        {{-- Filter --}}
        <div class="card mb-4">
            <div class="card-body row g-3">
                <div class="col-md-4 col-12">
                    <label class="form-label">Jenis Kartu</label>
                    <select id="jenis_kartu" class="form-select">
                        <option value="">Semua</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Tamu">Tamu</option>
                        <option value="Transporter">Transporter</option>
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">POS</label>
                    <select id="pos" class="form-select">
                        <option value="POS 1">POS 1</option>
                        <option value="POS 2">POS 2</option>
                    </select>
                </div>
                <div class="col-md-4 col-12 d-flex flex-column flex-md-row gap-2 align-items-md-end">
                    <button id="btn-filter" class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
                        type="submit">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <button type="button"
                        class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center"
                        onclick="hotReload()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh halaman
                    </button>
                </div>
            </div>
        </div>
        {{-- Info Kartu --}}
        <div class="row g-4" id="dashboard-cards">
            @php
                $data = [
                    ['icon' => 'bi-person-check', 'label' => 'Kartu Aktif', 'value' => '#', 'color' => 'primary'],
                    [
                        'icon' => 'bi-clock-history',
                        'label' => 'Belum Dikembalikan',
                        'value' => '#',
                        'color' => 'warning',
                    ],
                    [
                        'icon' => 'bi-check-circle', // GANTI ICON
                        'label' => 'Sudah Dikembalikan', // GANTI LABEL
                        'value' => '#', // GANTI VALUE
                        'color' => 'success', // GANTI WARNA
                    ],
                    ['icon' => 'bi-people', 'label' => 'Total Pengunjung', 'value' => '#', 'color' => 'info'], // info untuk total
                ];
            @endphp
            @foreach ($data as $item)
                <div class="col-md-3">
                    <div class="card dashboard-card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body text-start d-flex align-items-center gap-3">
                            <div class="icon-wrapper bg-{{ $item['color'] }}-subtle text-{{ $item['color'] }}">
                                <i class="bi {{ $item['icon'] }} fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted">{{ $item['label'] }}</small>
                                <h4 class="fw-semibold mb-0">{{ $item['value'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Chart --}}
        {{-- <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h6 class="mb-0">Distribusi Pengunjung Hari Ini</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="visitorChart" height="180"></canvas>
                        </div>
                    </div>
                </div> --}}

        {{-- Activity Log --}}
        {{-- <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h6 class="mb-0">Aktivitas Terakhir</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><strong>Rudi</strong> masuk lewat POS 1</span>
                                    <small class="text-muted">10:21</small>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><strong>Andi</strong> keluar dari POS 2</span>
                                    <small class="text-muted">09:45</small>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><strong>Siti</strong> check-in tanpa kartu</span>
                                    <small class="text-muted">08:55</small>
                                </li>
                                <!-- Add more if needed -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div> --}}

        {{-- Ringkasan Laporan --}}
        {{-- <div class="card mt-4 border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0">Ringkasan Laporan Hari Ini</h6>
                </div>
                <div class="card-body row">
                    <div class="col-md-3">
                        <div class="text-muted small">Total Pengunjung Hari Ini</div>
                        <h5 class="fw-bold">75</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Transporter Terlambat</div>
                        <h5 class="fw-bold">5</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Kartu Belum Kembali</div>
                        <h5 class="fw-bold">12</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Check-in Tanpa RFID</div>
                        <h5 class="fw-bold">2</h5>
                    </div>
                </div>
            </div> --}}

        {{-- Statistik Perusahaan & Departemen --}}
        <div class="card mt-4 border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0">Statistik Perusahaan & Departemen Favorit</h6>
                    <div class="d-flex gap-2">
                        <select id="periode_statistik_select" class="form-select form-select-sm" style="width: auto;">
                            <option value="today">Hari Ini</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="all" selected>Semua Waktu</option>
                        </select>
                        <select id="jenis_kartu_statistik_select" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Jenis Kartu</option>
                            <option value="Vendor">Vendor</option>
                            <option value="Tamu">Tamu</option>
                            <option value="Transporter">Transporter</option>
                        </select>
                        <select id="pos_statistik_select" class="form-select form-select-sm" style="width: auto;">
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2" selected>POS 2</option> {{-- DEFAULT POS 2 --}}
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Perusahaan Teratas --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0">Perusahaan yang Sering Berkunjung</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush" id="perusahaan_teratas_container">
                                    <li class="list-group-item text-center text-muted">Memuat data...</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Departemen Favorit --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-transparent border-bottom">
                                <h6 class="mb-0">Departemen yang Sering Dikunjungi</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush" id="departemen_favorit_container">
                                    <li class="list-group-item text-center text-muted">Memuat data...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('visitorChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Vendor', 'Tamu', 'Transporter'],
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: [50, 30, 15], // <- ganti dengan data dinamis
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(255, 99, 132, 0.5)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#btn-filter').on('click', function() {
                const jenisKartu = $('#jenis_kartu').val();
                const pos = $('#pos').val();

                // Tampilkan loading
                Swal.fire({
                    title: 'Memuat...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // AJAX POST request dengan form data
                $.ajax({
                    url: API_DASHBOARD_FILTER,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        jenis_kartu: jenisKartu,
                        pos: pos
                    },
                    success: function(response) {
                        Swal.close();
                        renderDashboardCards(response);
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.log('Error response:', xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
                    }
                });
            });

            function renderDashboardCards(data) {
                const container = $('#dashboard-cards');

                // Check if container exists
                if (container.length === 0) {
                    console.error('Container #dashboard-cards not found');
                    return;
                }

                container.empty();

                if (!Array.isArray(data)) {
                    console.error('Invalid data format:', data);
                    return;
                }

                data.forEach(function(item) {
                    const cardHtml = `
            <div class="col-md-3">
                <div class="card dashboard-card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body text-start d-flex align-items-center gap-3">
                        <div class="icon-wrapper bg-${item.color}-subtle text-${item.color}">
                            <i class="bi ${item.icon} fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted">${item.label}</small>
                            <h4 class="fw-semibold mb-0">${item.value}</h4>
                        </div>
                    </div>
                </div>
            </div>
        `;
                    container.append(cardHtml);
                });
            }
        });
    </script>
    <script>
        function hotReload() {
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url);
        }
        $(document).ready(function() {
            // Filter statistik perusahaan & departemen
            $('#periode_statistik_select, #jenis_kartu_statistik_select, #pos_statistik_select').on('change',
                function() {
                    loadStatistikPerusahaanDepartemen();
                });

            // Set default POS ke POS 2
            $('#pos_statistik_select').val('POS 2');

            // Load pertama kali dengan POS 2
            loadStatistikPerusahaanDepartemen();
        });

        function loadStatistikPerusahaanDepartemen() {
            const periode = $('#periode_statistik_select').val() || 'all';
            const jenisKartu = $('#jenis_kartu_statistik_select').val() || '';
            const pos = $('#pos_statistik_select').val() || 'POS 2'; // Default POS 2

            // Tampilkan loading indicator
            $('#perusahaan_teratas_container').html(
                '<li class="list-group-item text-center text-muted">Memuat data...</li>');
            $('#departemen_favorit_container').html(
                '<li class="list-group-item text-center text-muted">Memuat data...</li>');

            $.ajax({
                url: API_DASHBOARD_FILTER_STATISTIK,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    periode: periode,
                    jenis_kartu: jenisKartu,
                    pos: pos
                },
                success: function(response) {
                    renderStatistikPerusahaanDepartemen(response);
                },
                error: function(xhr, status, error) {
                    console.log('Error loading statistik:', error);
                    $('#perusahaan_teratas_container').html(
                        '<li class="list-group-item text-center text-danger">Error memuat data</li>');
                    $('#departemen_favorit_container').html(
                        '<li class="list-group-item text-center text-danger">Error memuat data</li>');
                }
            });
        }

        function renderStatistikPerusahaanDepartemen(data) {
            // Render perusahaan teratas
            const perusahaanList = $('#perusahaan_teratas_container');
            perusahaanList.empty();

            if (data.perusahaan && Array.isArray(data.perusahaan) && data.perusahaan.length > 0) {
                data.perusahaan.forEach(function(item) {
                    const listItem = `
                <li class="list-group-item d-flex justify-content-between">
                    <span>${item.nama || '-'}</span>
                    <span class="badge bg-primary rounded-pill">${item.jumlah}x</span>
                </li>
            `;
                    perusahaanList.append(listItem);
                });
            } else {
                perusahaanList.append('<li class="list-group-item text-center text-muted">Tidak ada data</li>');
            }

            // Render departemen favorit
            const departemenList = $('#departemen_favorit_container');
            departemenList.empty();

            if (data.departemen && Array.isArray(data.departemen) && data.departemen.length > 0) {
                data.departemen.forEach(function(item) {
                    const listItem = `
                <li class="list-group-item d-flex justify-content-between">
                    <span>${item.nama || '-'}</span>
                    <span class="badge bg-success rounded-pill">${item.jumlah}x</span>
                </li>
            `;
                    departemenList.append(listItem);
                });
            } else {
                departemenList.append('<li class="list-group-item text-center text-muted">Tidak ada data</li>');
            }
        }
    </script>
@endpush
