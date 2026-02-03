<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Monitoring Tamu/Izin Keluar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/bas_logo.jpg') }}" />

    <script src="{{ asset('assets/velzon/js/layout.js') }}"></script>
    <link href="{{ asset('assets/velzon/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/velzon/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .top-bar {
            background: #212529;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content {
            height: calc(100vh - 80px);
            padding: 2rem;
        }

        .photo-card {
            height: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .photo-card img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .biodata-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .text-red {
            color: #d32f2f;
        }

        .text-green {
            color: #2e7d32;
        }

        .info-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        .rfid-input {
            font-size: 0.9rem;
            padding: 6px 10px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            width: 180px;
            background: #f8f9fa;
            outline: none;
        }

        .rfid-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            flex: 1;
        }

        .info-box {
            background: #f1f3f5;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
        }

        .info-box.full {
            grid-column: span 2;
        }

        .label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .value {
            font-size: 1rem;
            font-weight: bold;
            color: #212529;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .badge-status {
            display: inline-block;
            font-weight: bold;
            font-size: 0.95rem;
            padding: 6px 16px;
            border-radius: 30px;
            text-transform: uppercase;
        }

        .green {
            background: linear-gradient(90deg, #00c853, #64dd17);
            color: white;
        }

        .orange {
            background: linear-gradient(90deg, #ff9100, #ff6d00);
            color: white;
        }

        .clock {
            font-size: 1.3rem;
        }

        @media (max-width: 768px) {
            .info-title {
                font-size: 1.1rem;
                flex-direction: column;
                gap: 8px;
            }

            .rfid-input {
                width: 100%;
                max-width: 200px;
                font-size: 0.85rem;
                padding: 5px 8px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .info-box {
                padding: 10px;
            }

            .label {
                font-size: 0.8rem;
            }

            .value {
                font-size: 0.95rem;
            }

            .biodata-card {
                padding: 1rem;
            }
        }

        @media (max-height: 600px) {
            .top-bar {
                font-size: 1rem;
                padding: 0.6rem 1rem;
            }

            .main-content {
                padding: 0.5rem;
            }

            .biodata-card {
                padding: 0.8rem;
            }

            .info-title {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }

            .info-grid {
                gap: 8px;
            }

            .info-box {
                padding: 8px;
            }

            .label,
            .value {
                font-size: 0.75rem;
            }

            .badge-status {
                font-size: 0.85rem;
                padding: 5px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <div class="d-flex align-items-center">
            <a href="{{ route('pos-security.formulir') }}" target="_blank">
                <img src="{{ asset('assets/media/logos/bas_logo.jpg') }}" alt="Logo" height="40" />
            </a>
            <span class="ms-3">Monitoring Absen Tamu/Izin Keluar</span>
        </div>
        <div id="clock" class="clock">--:--:--</div>
    </div>

    <div class="container-fluid main-content">
        <div class="row h-100 g-4">
            <div class="col-md-5">
                <div class="photo-card h-100">
                    <img id="foto-diri" src="{{ asset('assets/media/images/user-dummy-img.jpg') }}" alt="Foto Diri" />
                </div>
            </div>

            <div class="col-md-7">
                <div class="biodata-card">
                    <div class="info-title">
                        <span>INFORMASI</span>
                        <input type="text" id="rfidInput" class="rfid-input" placeholder="Scan RFID..."
                            autocomplete="off" autofocus />
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <p class="label">Nama</p>
                            <p class="value" id="nama-tamu">-</p>
                        </div>
                        <div class="info-box">
                            <p class="label">Perusahaan</p>
                            <p class="value" id="perusahaan">-</p>
                        </div>
                        <div class="info-box">
                            <p class="label">Sebagai Apa</p>
                            <p class="value" id="jenis-kunjungan">-</p>
                        </div>
                        <div class="info-box">
                            <p class="label">No. Polisi</p>
                            <p class="value" id="no-polisi">-</p>
                        </div>
                        <div class="info-box">
                            <p class="label">Status Kartu</p>
                            <span class="badge-status green" id="status-kartu">-</span>
                        </div>
                        <div class="info-box">
                            <p class="label">Status Lokasi</p>
                            <span class="badge-status orange" id="status-lokasi">-</span>
                        </div>
                        <div class="info-box full">
                            <p class="label">Keperluan Kunjungan</p>
                            <p class="value" id="keperluan">-</p>
                        </div>
                        <div class="info-box full">
                            <p class="label">Waktu Scan</p>
                            <p class="value" id="waktu-scan">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pos-security.routes.ajax')

    <script>
        // Ambil elemen-elemen yang dibutuhkan
        const rfidInput = document.getElementById('rfidInput');

        // Fokus ke input saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            rfidInput.focus();
        });

        // Fungsi untuk format waktu saat ini
        function nowFormatted() {
            return new Date().toLocaleTimeString('id-ID');
        }

        // Fungsi update jam
        function updateClock() {
            const clockEl = document.getElementById('clock');
            if (clockEl) clockEl.textContent = nowFormatted();
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Tambahkan event listener untuk input RFID
        rfidInput.addEventListener('keypress', function(e) {
            if (e.key !== 'Enter') return;

            const keyword = rfidInput.value.trim();
            if (!keyword) return;

            // Nonaktifkan input sementara
            rfidInput.disabled = true;

            // Ambil CSRF token dari meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Kirim request via fetch (gantian untuk $.ajax)
            fetch(API_ABSENSI_REST_LOG_SEARCH, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        keyword: keyword,
                        _token: csrfToken
                    })
                })
                .then(async response => {
                    const data = await response.json();

                    if (response.status === 403 || response.status === 422) {
                        Swal.fire({
                            icon: 'error',
                            title: response.status === 403 ? 'Akses Ditolak' : 'Data Tidak Valid',
                            text: data.message || 'Terjadi kesalahan.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        throw new Error('Stop execution');
                    }

                    if (!response.ok) throw new Error('Network response was not ok');
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        const d = data.data;
                        const detail = d.source_detail || {};

                        // Update konten teks
                        setText('nama-tamu', d.nama || '-');
                        setText('perusahaan', d.perusahaan || '-');
                        setText('jenis-kunjungan', d.jenis_kunjungan || '-');
                        setText('no-polisi', d.no_polisi || '-');
                        setText('keperluan', d.keperluan || '-');
                        setText('waktu-scan', nowFormatted());
                        setText('status-lokasi', "POS CHECK BODY");

                        // Status kartu
                        const statusKartuEl = document.getElementById('status-kartu');
                        if (d.status_kartu === 'dikembalikan') {
                            statusKartuEl.className = 'red';
                            statusKartuEl.textContent = 'DIKEMBALIKAN';
                        } else {
                            statusKartuEl.className = 'green';
                            statusKartuEl.textContent = 'AKTIF';
                        }

                        // Foto
                        if (d.foto_url) {
                            document.getElementById('foto-diri').src = d.foto_url;
                        }

                        // Tampilkan Swal sukses
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Berhasil',
                        //     text: data.message,
                        //     timer: 2000,
                        //     showConfirmButton: false
                        // });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Ditolak',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    if (err.message === 'Stop execution') return;

                    const statusKartuEl = document.getElementById('status-kartu');
                    statusKartuEl.className = 'red';
                    statusKartuEl.textContent = 'ERROR';

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal terhubung ke server.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .finally(() => {
                    // Reset input dan fokus kembali
                    rfidInput.value = '';
                    rfidInput.disabled = false;
                    rfidInput.focus();
                });
        });

        // Helper function untuk mengatur teks elemen
        function setText(id, text) {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        }

        // Auto refresh halaman setiap 10 menit
        const AUTO_REFRESH_MINUTES = 10;

        setTimeout(() => {
            window.location.reload(true);
        }, AUTO_REFRESH_MINUTES * 60 * 1000);
    </script>
</body>

</html>
