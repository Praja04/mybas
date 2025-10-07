@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
<style>
    #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
</style>
@endpush

@section('content')
{{-- <div id="loading-overlay">
    <div class="loader"></div>
</div> --}}
    <div id="main">
        @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-content">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengambilan <span style="color: red">Beras</span></h3>
                    <p class="text-subtitle text-muted"></p>
                </div>
            </div>
            <div class="form-group pt-1 pb-1 d-flex gap-3">
                <div class="stats-icon blue mb-2">
                    <div class="gambar-identifikasi">
                        <img src="{{ asset('assets/mazer/dist/assets/compiled/png/smiling.png') }}" alt="mobil truck"
                            width="30px">
                    </div>
                </div>
                @if ($records)
                @php
                    $formattedDate = date('d-m-Y', strtotime($records->tanggal));
                @endphp
                <h6 class="pt-3 text-success">{{ $totalJumlahPengambilan }} SAK Beras Sudah diambil pada bulan ini</h6>
                {{-- di tanggal {{ $formattedDate }} --}}
            @else
                <h6 class="pt-3">Stock beras belum diambil pada bulan ini</h6>
            @endif            
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-3">
                    <img src="{{ asset('assets/mazer/dist/assets/compiled/png/grab.png') }}" alt="mobil truck"
                        width="30px" class="mr-2">
                    <h4 class="card-title mb-0">Pengambilan Beras</h4>
                </div>


                <div class="card-body">
                    <div class="row">
                        <form method="POST" id="ambil-beras-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pilihJumlahPengambilan">Jumlah Pengambilan <span style="color: red;">*</span></label>
                                        <div class="input-group">
                                            <select name="jumlah_pengambilan" class="form-control" id="pilihJumlahPengambilan" required>
                                                <option value="">Pilih</option>
                                                <option value="1">1 sak</option>
                                                <option value="0.5">1/2 sak</option>
                                                <option value="0.33">1/3 sak</option>
                                            </select>
                                            <div class="input-group-append">
                                                <span class="input-group-text">SAK</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="display: none" for="jumlahPengambilanSesudah">Jumlah Pengambilan Sesudah</label>
                                        <input type="hidden" class="form-control" id="jumlahPengambilanSesudah" name="jumlah_pengambilan_sesudah" placeholder="Nilai Otomatis" readonly>
                                    </div>
                                    

                                    <div class="form-group">
                                        <label for="pilih-shift">Pilih Shift <span style="color: red;">*</span></label>
                                        <select name="shift" class="choices form-select" id="pilih-shift" required>
                                            <option value="">Pilih</option>
                                            <option value="1">Shift 1</option>
                                            <option value="2">Shift 2</option>
                                            <option value="3">Shift 3</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="keterangan-beras">Keterangan <span style="color: red;">*</span></label>
                                        <input name="keterangan" type="text" id="keterangan-beras" class="form-control"
                                            placeholder="Masukkan Keterangan" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="disabledInput">Jumlah Stock Saat Ini</label>
                                        @php
                                        $lastBerasJumlah = App\BerasJumlah::where('id_stock', '<>', '')
                                            ->orderBy('id_stock', 'desc')
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                        @endphp
                                    
                                        @if ($lastBerasJumlah)
                                            <input type="hidden" class="form-control" id="disabledInput" placeholder="350" disabled
                                                value="{{ $lastBerasJumlah->jumlah_stock }}" name="jumlah_stock">
                                            <input type="text" class="form-control" id="disabledInput" placeholder="350" disabled
                                                value="{{ $lastBerasJumlah->jumlah_stock_sesudah }}" id="jumlahStockSesudah">
                                        @else
                                            <input type="text" class="form-control" placeholder="350" disabled value="belum ada data">
                                        @endif
                                    </div>
                                    {{-- ga perlu di state store kedatangan beras --}}
                                    <div class="form-group" style="display: none;">
                                        <label for="id-stock">Kode Stock</label>
                                        @if ($pengambilanBeras->isNotEmpty())
                                            <input type="hidden" class="form-control" id="id-stock" placeholder="350"
                                                disabled value="{{ $pengambilanBeras->last()->id_stock }}" name="id_stock">
                                            <input type="hidden" name="id_stock"
                                                value="{{ $pengambilanBeras->last()->id_stock }}">
                                        @else
                                            <input type="hidden" class="form-control"placeholder="350" disabled
                                                value="belum ada data">
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="disabledInput">Tanggal</label>
                                        @if ($pengambilanBeras->isNotEmpty())
                                            <input type="text" class="form-control" id="disabledInput" placeholder="350"
                                                disabled value="{{ $pengambilanBeras->last()->tanggal }}" name="tanggal">
                                            <input type="hidden" name="tanggal"
                                                value="{{ $pengambilanBeras->last()->tanggal }}">
                                        @else
                                            <input type="text" class="form-control" placeholder="350" disabled
                                                value="belum ada data">
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-block" onclick="showConfirmation()">Ambil Beras</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('after-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function submitForm() {
            var formData = new FormData(document.getElementById('ambil-beras-form'));
            Swal.fire({
                title: 'Yakin ingin mengambil stock beras?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ambil!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Transaksi Pengambilan Beras',
                        text: 'Transaksi pengambilan beras sedang diproses. Mohon tunggu...',
                        icon: 'info',
                        showConfirmButton: false
                    });
    
                    $.ajax({
                        url: '{{ route('kedatangan-beras.ambil.stock') }}',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status === 1) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Berhasil mengambil stock beras.',
                                    icon: 'success'
                                }).then((result) => {
                                    if (result.isConfirmed || result.isDismissed) {
                                        window.location.reload(); 
                                    }
                                });
                            } else if (response.status === 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Stok tidak mencukupi untuk pengambilan ini.',
                                    icon: 'error'
                                });
                            } else if (response.status === 3) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Data stok tidak valid.',
                                    icon: 'error'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan: ' + response.error,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal mengambil stock beras.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    
        // Event listener untuk submit form
        document.getElementById('ambil-beras-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('pilihJumlahPengambilan').addEventListener('change', function() {
                var selectedValue = this.value;
                var calculatedValue = selectedValue * 60; 
                document.getElementById('jumlahPengambilanSesudah').value = calculatedValue; 
            });
        });

        $(document).ready(function() {
            $('#loading-overlay').show();
            $(window).on('load', function() {
                $('#loading-overlay').fadeOut('slow');
            });
        });
    </script>

@endpush
