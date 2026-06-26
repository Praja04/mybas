@extends('layouts.base')

@push('styles')
    <style>
        .live-photo {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            border: 4px solid #ebedf3;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .scanner-input {
            font-size: 20px !important;
            font-weight: bold !important;
            height: auto !important;
            padding: 12px 20px !important;
            border: 2px solid #3699FF !important;
            background-color: #f3f6f9 !important;
            color: #3f4254 !important;
            transition: all 0.3s ease;
        }
        .scanner-input:focus {
            background-color: #ffffff !important;
            box-shadow: 0 0 10px rgba(54, 153, 255, 0.25) !important;
        }
        .status-badge {
            font-size: 11px !important;
            font-weight: 700;
            padding: 5px 10px !important;
            text-transform: uppercase;
        }
        .live-console-card {
            border-left: 5px solid #3699FF !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- ========================================== -->
        <!-- KOLOM KIRI: KONSOL LIVE SCANNER (TABLET)   -->
        <!-- ========================================== -->
        <div class="col-lg-5 col-md-12 mb-5">
            <div class="card card-custom card-stretch gutter-b live-console-card">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Live Scanner Izin Istirahat</span>
                        <span class="text-muted mt-2 font-weight-bold font-size-sm">Tap kartu RFID atau input NIK</span>
                    </h3>
                </div>

                <div class="card-body pt-0">
                    <!-- Form Scanner -->
                    <form id="form-scan" autocomplete="off" class="mb-5">
                        <div class="form-group position-relative">
                            <input type="text" id="scanner" class="form-control scanner-input text-center" 
                                placeholder="TAP ID CARD ATAU KETIK NIK DI SINI..." autofocus>
                            <span class="form-text text-muted text-center mt-2">
                                <i class="fas fa-keyboard mr-1"></i> Input otomatis terfokus setelah scan
                            </span>
                        </div>
                    </form>

                    <!-- Indikator Loading -->
                    <div id="loading" class="text-center py-5" style="display: none;">
                        <div class="spinner spinner-primary spinner-lg mr-15"></div>
                        <span class="font-weight-bolder text-primary">Memverifikasi Data Karyawan...</span>
                    </div>

                    <!-- Panel Detail Karyawan & Hasil Scan -->
                    <div id="scan-result-panel">
                        <div class="row">
                            <!-- Foto Karyawan -->
                            <div class="col-sm-4 text-center mb-4">
                                <img id="karyawan-foto" class="live-photo" 
                                    src="{{ asset('assets/media/images/no-image.jpg') }}" alt="Foto Karyawan">
                            </div>
                            
                            <!-- Biodata -->
                            <div class="col-sm-8">
                                <div class="form-group mb-3">
                                    <label class="text-muted font-weight-bold mb-0">NAMA LENGKAP</label>
                                    <h4 id="karyawan-nama" class="font-weight-bolder text-dark-75">-</h4>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-muted font-weight-bold mb-0">NOMOR INDUK KARYAWAN (NIK)</label>
                                    <h4 id="karyawan-nik" class="font-weight-bolder text-dark-75">-</h4>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="text-muted font-weight-bold mb-0">DEPARTEMEN / DIVISI</label>
                                    <h4 id="karyawan-divisi" class="font-weight-bolder text-dark-75">-</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Notifikasi Status Aksi -->
                        <div id="status-alert" class="alert alert-custom alert-light-primary fade show mt-4 py-4" role="alert" style="display: none;">
                            <div class="alert-icon">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <!-- Icon placeholder -->
                                    <i id="status-alert-icon" class="fas fa-check-circle fa-2x"></i>
                                </span>
                            </div>
                            <div class="alert-text font-weight-bolder" id="status-alert-message" style="font-size: 15px;">
                                Tempel kartu untuk memulai scan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: MONITORING SCAN HARI INI      -->
        <!-- ========================================== -->
        <div class="col-lg-7 col-md-12 mb-5">
            <div class="card card-custom card-stretch gutter-b">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Monitoring Riwayat Hari Ini</span>
                        <span class="text-muted mt-2 font-weight-bold font-size-sm">Menampilkan 10 data scan teratas</span>
                    </h3>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center table-hover" id="table-log">
                            <thead>
                                <tr class="text-left text-uppercase">
                                    <th style="width: 80px;">NIK</th>
                                    <th>Nama</th>
                                    <th>Divisi</th>
                                    <th>Keluar</th>
                                    <th>Kembali</th>
                                    <th class="text-center">Telat</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="table-log-body">
                                @forelse ($today as $item)
                                    <tr id="row-{{ $item['nik'] }}">
                                        <td>{{ $item['nik'] }}</td>
                                        <td class="font-weight-bolder">{{ $item['nama'] }}</td>
                                        <td>{{ $item['divisi'] }}</td>
                                        <td>
                                            <span class="label label-inline font-weight-bold py-3">{{ date('H:i:s', strtotime($item['jam_keluar'])) }}</span>
                                        </td>
                                        <td id="in-time-{{ $item['nik'] }}">
                                            @if ($item['jam_masuk'])
                                                <span class="label label-inline font-weight-bold py-3">{{ date('H:i:s', strtotime($item['jam_masuk'])) }}</span>
                                            @else
                                                <span class="text-muted italic">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold text-danger" id="late-{{ $item['nik'] }}">
                                            @if ($item['menit_terlambat'] > 0)
                                                {{ $item['menit_terlambat'] }} m
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center" id="badge-{{ $item['nik'] }}">
                                            @if ($item['status'] === 'Belum Kembali')
                                                <span class="label label-light-warning label-inline status-badge">Belum Kembali</span>
                                            @elseif ($item['status'] === 'Tepat Waktu')
                                                <span class="label label-light-success label-inline status-badge">Tepat Waktu</span>
                                            @else
                                                <span class="label label-light-danger label-inline status-badge">Terlambat</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-data-row">
                                        <td colspan="7" class="text-center text-muted py-5">Belum ada riwayat tap istirahat siang hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                        <div class="d-flex align-items-center py-3">
                            {!! $today->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fokuskan kursor ke input scanner saat halaman dimuat
        $('#scanner').focus();

        // Jaga agar input tetap fokus jika user tidak sengaja klik di luar input
        $(document).click(function(e) {
            if (!$(e.target).is('#scanner') && !$(e.target).closest('form').length) {
                $('#scanner').focus();
            }
        });

        // ==========================================
        // FITUR SOUND BEEP (Web Audio API - Offline)
        // ==========================================
        function playBeep(type) {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                
                if (type === 'success') {
                    // Beep nada tinggi bersih (Berhasil)
                    osc.frequency.setValueAtTime(880, ctx.currentTime); 
                    gain.gain.setValueAtTime(0.08, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.005, ctx.currentTime + 0.15);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.15);
                } else {
                    // Beep nada rendah berdengung (Gagal/Error)
                    osc.frequency.setValueAtTime(220, ctx.currentTime); 
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.005, ctx.currentTime + 0.35);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.35);
                }
            } catch (e) {
                console.error("AudioContext error: ", e);
            }
        }

        // ==========================================
        // FITUR TEXT-TO-SPEECH (TTS Bahasa Indonesia)
        // ==========================================
        function speakWelcome(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel(); // Batalkan antrean suara aktif sebelumnya
                var utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID'; // Menggunakan suara lokal Indonesia
                utterance.rate = 1.0;     // Kecepatan normal
                window.speechSynthesis.speak(utterance);
            }
        }

        // ==========================================
        // EVENT HANDLER SCANNING FORM SUBMIT
        // ==========================================
        $('#form-scan').submit(function(e) {
            e.preventDefault();

            var rfidOrNik = $('#scanner').val().trim();
            if (rfidOrNik === "") return;

            // Kosongkan kolom input scanner dan tampilkan spinner loading
            $('#scanner').val('');
            $('#loading').show();
            $('#scan-result-panel').css('opacity', '0.5');

            $.ajax({
                url: "{{ route('izin-keluar.check-karyawan') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    rfidOrNik: rfidOrNik,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#loading').hide();
                    $('#scan-result-panel').css('opacity', '1');

                    if (response.success) {
                        // 1. Play success beep
                        playBeep('success');

                        var d = response.data;
                        
                        // 2. Tampilkan Biodata Karyawan di Screen
                        $('#karyawan-nama').text(d.nama.toUpperCase());
                        $('#karyawan-nik').text(d.nik);
                        $('#karyawan-divisi').text(d.divisi.toUpperCase());

                        // 3. Tampilkan Foto Karyawan
                        if (d.foto) {
                            $('#karyawan-foto').attr('src', d.foto);
                        } else {
                            $('#karyawan-foto').attr('src', "{{ asset('assets/media/images/no-image.jpg') }}");
                        }

                        // 4. Update Alert Status & Mainkan TTS
                        $('#status-alert').removeClass('alert-light-primary alert-light-danger alert-light-success alert-light-warning');
                        var alertMsg = "";
                        var voiceText = "";

                        if (response.action === 'keluar') {
                            // Skenario: Keluar Makan Siang
                            $('#status-alert').addClass('alert-light-success');
                            $('#status-alert-icon').attr('class', 'fas fa-sign-out-alt fa-2x text-success');
                            alertMsg = "<strong>Scan Berhasil (OUT)</strong><br />Selamat beristirahat, " + d.nama;
                            voiceText = d.nama + ", selamat beristirahat.";

                            // Masukkan baris baru ke tabel monitoring (Real-time Prepend)
                            $('#no-data-row').remove();
                            
                            // Hapus baris lama jika NIK tersebut sudah ada di tabel sebelumnya (biar tidak double baris hari ini)
                            $('#row-' + d.nik).remove();

                            var jamKeluarFix = d.jam_keluar.split(' ')[1]; // ambil format HH:MM:SS saja
                            var newRow = `
                                <tr id="row-${d.nik}">
                                    <td>${d.nik}</td>
                                    <td class="font-weight-bolder">${d.nama}</td>
                                    <td>${d.divisi}</td>
                                    <td>
                                        <span class="label label-inline font-weight-bold py-3">${jamKeluarFix}</span>
                                    </td>
                                    <td id="in-time-${d.nik}">
                                        <span class="text-muted italic">-</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-danger" id="late-${d.nik}">-</td>
                                    <td class="text-center" id="badge-${d.nik}">
                                        <span class="label label-light-warning label-inline status-badge">Belum Kembali</span>
                                    </td>
                                </tr>`;
                            $('#table-log-body').prepend(newRow);

                        } else {
                            // Skenario: Masuk Kembali
                            var jamMasukFix = d.jam_masuk.split(' ')[1];

                            if (d.status === 'Tepat Waktu') {
                                $('#status-alert').addClass('alert-light-success');
                                $('#status-alert-icon').attr('class', 'fas fa-check-circle fa-2x text-success');
                                alertMsg = "<strong>Scan Berhasil (IN)</strong><br />Tepat waktu. Selamat bekerja kembali.";
                                voiceText = d.nama + ", selamat bekerja kembali.";
                            } else {
                                $('#status-alert').addClass('alert-light-danger');
                                $('#status-alert-icon').attr('class', 'fas fa-exclamation-triangle fa-2x text-danger');
                                alertMsg = "<strong>Scan Berhasil (IN - TERLAMBAT)</strong><br />Terlambat " + d.menit_terlambat + " menit. Selamat bekerja kembali.";
                                voiceText = d.nama + ", Anda terlambat " + d.menit_terlambat + " menit.";
                            }

                            // Update data di baris tabel monitoring secara dinamis
                            if ($('#row-' + d.nik).length) {
                                $('#in-time-' + d.nik).html(`<span class="label label-inline font-weight-bold py-3">${jamMasukFix}</span>`);
                                
                                if (d.menit_terlambat > 0) {
                                    $('#late-' + d.nik).text(d.menit_terlambat + ' m');
                                    $('#badge-' + d.nik).html(`<span class="label label-light-danger label-inline status-badge">Terlambat</span>`);
                                } else {
                                    $('#late-' + d.nik).text('-');
                                    $('#badge-' + d.nik).html(`<span class="label label-light-success label-inline status-badge">Tepat Waktu</span>`);
                                }
                            }
                        }

                        $('#status-alert-message').html(alertMsg);
                        $('#status-alert').show();

                        // Jalankan Text-to-Speech
                        speakWelcome(voiceText);
                    }
                },
                error: function(xhr) {
                    $('#loading').hide();
                    $('#scan-result-panel').css('opacity', '1');

                    // 1. Play error beep
                    playBeep('error');

                    // 2. Reset tampilan data
                    $('#karyawan-nama').text('-');
                    $('#karyawan-nik').text('-');
                    $('#karyawan-divisi').text('-');
                    $('#karyawan-foto').attr('src', "{{ asset('assets/media/images/no-image.jpg') }}");

                    // 3. Tampilkan pesan error di alert panel
                    $('#status-alert').removeClass('alert-light-primary alert-light-success alert-light-warning alert-light-danger')
                                      .addClass('alert-light-danger');
                    $('#status-alert-icon').attr('class', 'fas fa-times-circle fa-2x text-danger');
                    
                    var errorMsg = "Terjadi kesalahan sistem, silakan coba lagi.";
                    var voiceError = "Terjadi kesalahan.";

                    if (xhr.status === 400 || xhr.status === 404) {
                        errorMsg = xhr.responseJSON.message;
                        voiceError = xhr.responseJSON.message;
                    }
                    
                    $('#status-alert-message').html("<strong>Gagal Mengabsen</strong><br />" + errorMsg);
                    $('#status-alert').show();

                    // Jalankan suara error
                    speakWelcome(voiceError);
                }
            });
        });
    });
</script>
@endpush
