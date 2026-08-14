@extends('layouts.base-display')

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
            <div class="col-lg-8 col-md-12 mx-auto">
                <div class="card card-custom card-stretch gutter-b live-console-card">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Live Scanner Istirahat Karyawan</span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">Silakan tempelkan kartu RFID atau
                                masukkan NIK Anda</span>
                        </h3>
                    </div>

                    <div class="card-body pt-0">
                        <!-- Form Scanner -->
                        <form id="form-scan" autocomplete="off" class="mb-5">
                            <div class="form-group position-relative">
                                <input type="text" id="scanner" class="form-control scanner-input text-center"
                                    placeholder="SILAKAN TAP ID CARD ATAU MASUKKAN NIK DI SINI..." autofocus>
                                <span class="form-text text-muted text-center mt-2">
                                    <i class="fas fa-keyboard mr-1"></i> Sensor otomatis mendeteksi input kartu
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

                                <div class="col-sm-8 d-flex flex-column justify-content-center">
                                    <div class="bg-light-secondary rounded p-4 mb-3"
                                        style="border-right: 5px solid #3699FF; height: 100px;">
                                        <label class="text-muted font-weight-bolder mb-1">NAMA LENGKAP</label>
                                        <h3 id="karyawan-nama" class="font-weight-bolder text-dark-75 mb-0"
                                            style="font-size: 1.6rem; letter-spacing: 0.5px;">-</h3>
                                    </div>
                                    <div class="bg-light-secondary rounded p-4 mb-3"
                                        style="border-right: 5px solid #3699FF; height: 100px;">
                                        <label class="text-muted font-weight-bolder mb-1">NOMOR INDUK KARYAWAN (NIK)</label>
                                        <h3 id="karyawan-nik" class="font-weight-bolder text-dark-75 mb-0"
                                            style="font-size: 1.6rem; letter-spacing: 0.5px;">-</h3>
                                    </div>
                                    <div class="bg-light-secondary rounded p-4 mb-0"
                                        style="border-right: 5px solid #3699FF; height: 100px;">
                                        <label class="text-muted font-weight-bolder mb-1">DEPARTEMEN / DIVISI</label>
                                        <h3 id="karyawan-divisi" class="font-weight-bolder text-dark-75 mb-0"
                                            style="font-size: 1.6rem; letter-spacing: 0.5px;">-</h3>
                                    </div>
                                </div>

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
            $('#scanner').focus();

            $(document).click(function(e) {
                if (!$(e.target).is('#scanner') && !$(e.target).closest('form').length) {
                    $('#scanner').focus();
                }
            });

            $('#form-scan').submit(function(e) {
                e.preventDefault();

                var rfidOrNik = $('#scanner').val().trim();
                if (rfidOrNik === "") return;

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
                            var d = response.data;

                            $('#karyawan-nama').text(d.nama.toUpperCase());
                            $('#karyawan-nik').text(d.nik);
                            $('#karyawan-divisi').text(d.divisi.toUpperCase());

                            if (d.foto) {
                                $('#karyawan-foto').attr('src', d.foto);
                            } else {
                                $('#karyawan-foto').attr('src',
                                    "{{ asset('assets/media/images/no-image.jpg') }}");
                            }

                            if (response.action === 'keluar') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ABSEN KELUAR BERHASIL',
                                    text: 'Absen keluar pada jam istirahat.',
                                    timer: 1000,
                                    showConfirmButton: false
                                });
                            } else {
                                if (d.status === 'Tepat Waktu') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ABSEN MASUK BERHASIL',
                                        text: 'Kembali tepat waktu.',
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'TERLAMBAT MASUK',
                                        text: 'Terlambat ' + d.menit_terlambat +
                                            ' menit.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            }

                            setTimeout(function() {
                                $('#karyawan-foto').attr('src',
                                    "{{ asset('assets/media/images/no-image.jpg') }}"
                                );
                                $('#karyawan-nama').text('-');
                                $('#karyawan-nik').text('-');
                                $('#karyawan-divisi').text('-');
                            }, 7000);
                        }
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        $('#scan-result-panel').css('opacity', '1');

                        $('#karyawan-nama').text('-');
                        $('#karyawan-nik').text('-');
                        $('#karyawan-divisi').text('-');
                        $('#karyawan-foto').attr('src',
                            "{{ asset('assets/media/images/no-image.jpg') }}");

                        var errorMsg = "Terjadi kesalahan sistem, silakan coba lagi.";
                        if (xhr.status === 400 || xhr.status === 404) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'ABSENSI GAGAL',
                            text: errorMsg,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endpush
