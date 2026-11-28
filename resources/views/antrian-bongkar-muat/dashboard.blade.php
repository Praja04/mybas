@extends('layouts.base')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Dashboard Antrian Bongkar Muat</h3>
                            </div>
                            <div class="card-toolbar">
                                <!-- Tombol Reset Manual -->
                                <form action="{{ route('antrian-bongkar-muat.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset antrian? Semua riwayat antrian hari ini akan dihapus.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger font-weight-bolder">
                                        <i class="flaticon2-reload"></i> Reset Antrian
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif

            <!-- Tambahkan ID pada baris utama untuk mempermudah auto-refresh -->
            <div id="refresh-container">
                <div class="row">
                    <!-- Bongkar Muat -->
                    <div class="col-xl-4">
                        <div class="card card-custom bg-light-primary card-stretch gutter-b shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-40 symbol-light-primary mr-5">
                                        <span class="symbol-label">
                                            <i class="flaticon-truck text-primary font-size-h4"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bolder text-dark-75 font-size-h4 d-block">Bongkar Muat</span>
                                    </div>
                                </div>

                                <!-- Antrian Aktif -->
                                <div class="mb-5 text-center p-5 bg-white rounded-xl shadow-sm border">
                                    <span class="text-muted font-weight-bold font-size-sm d-block text-uppercase mb-2">Sedang Dilayani</span>
                                    <h2 id="bmActiveTicket" class="display-4 font-weight-boldest text-primary mb-0">
                                        {{ $bmActive ? $bmActive->nomor_antrian : '---' }}
                                    </h2>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-5 px-2">
                                    <span class="font-weight-bold text-muted">Sisa Antrian</span>
                                    <span id="bmMenunggu" class="label label-xl label-warning label-inline font-weight-bolder">{{ $bmMenunggu }}</span>
                                </div>

                                <form action="{{ route('antrian-bongkar-muat.panggil') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="kategori" value="bongkar_muat">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bolder py-4 shadow-sm">
                                        <i class="flaticon2-speaker"></i> 
                                        <span id="bmBtnText">{{ $bmActive ? 'Panggil Berikutnya' : 'Panggil Antrian Pertama' }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tamu -->
                    <div class="col-xl-4">
                        <div class="card card-custom bg-light-info card-stretch gutter-b shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-40 symbol-light-info mr-5">
                                        <span class="symbol-label">
                                            <i class="flaticon-users text-info font-size-h4"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bolder text-dark-75 font-size-h4 d-block">Tamu</span>
                                    </div>
                                </div>

                                <!-- Antrian Aktif -->
                                <div class="mb-5 text-center p-5 bg-white rounded-xl shadow-sm border">
                                    <span class="text-muted font-weight-bold font-size-sm d-block text-uppercase mb-2">Sedang Dilayani</span>
                                    <h2 id="tamuActiveTicket" class="display-4 font-weight-boldest text-info mb-0">
                                        {{ $tamuActive ? $tamuActive->nomor_antrian : '---' }}
                                    </h2>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-5 px-2">
                                    <span class="font-weight-bold text-muted">Sisa Antrian</span>
                                    <span id="tamuMenunggu" class="label label-xl label-warning label-inline font-weight-bolder">{{ $tamuMenunggu }}</span>
                                </div>

                                <form action="{{ route('antrian-bongkar-muat.panggil') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="kategori" value="tamu">
                                    <button type="submit" class="btn btn-info btn-lg btn-block font-weight-bolder py-4 shadow-sm">
                                        <i class="flaticon2-speaker"></i> 
                                        <span id="tamuBtnText">{{ $tamuActive ? 'Panggil Berikutnya' : 'Panggil Antrian Pertama' }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- TKBM -->
                    <div class="col-xl-4">
                        <div class="card card-custom bg-light-success card-stretch gutter-b shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-40 symbol-light-success mr-5">
                                        <span class="symbol-label">
                                            <i class="flaticon-avatar text-success font-size-h4"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bolder text-dark-75 font-size-h4 d-block">TKBM</span>
                                    </div>
                                </div>

                                <!-- Antrian Aktif -->
                                <div class="mb-5 text-center p-5 bg-white rounded-xl shadow-sm border">
                                    <span class="text-muted font-weight-bold font-size-sm d-block text-uppercase mb-2">Sedang Dilayani</span>
                                    <h2 id="tkbmActiveTicket" class="display-4 font-weight-boldest text-success mb-0">
                                        {{ $tkbmActive ? $tkbmActive->nomor_antrian : '---' }}
                                    </h2>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-5 px-2">
                                    <span class="font-weight-bold text-muted">Sisa Antrian</span>
                                    <span id="tkbmMenunggu" class="label label-xl label-warning label-inline font-weight-bolder">{{ $tkbmMenunggu }}</span>
                                </div>

                                <form action="{{ route('antrian-bongkar-muat.panggil') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="kategori" value="tkbm">
                                    <button type="submit" class="btn btn-success btn-lg btn-block font-weight-bolder py-4 shadow-sm">
                                        <i class="flaticon2-speaker"></i> 
                                        <span id="tkbmBtnText">{{ $tkbmActive ? 'Panggil Berikutnya' : 'Panggil Antrian Pertama' }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Tabel Bongkar Muat -->
                    <div class="col-xl-4">
                        <div class="card card-custom gutter-b shadow-sm">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Riwayat Bongkar Muat</span>
                                </h3>
                            </div>
                            <div class="card-body pt-3 pb-5" id="bmTableContainer">
                                @include('antrian-bongkar-muat._table', ['items' => $bmList])
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Tamu -->
                    <div class="col-xl-4">
                        <div class="card card-custom gutter-b shadow-sm">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Riwayat Tamu</span>
                                </h3>
                            </div>
                            <div class="card-body pt-3 pb-5" id="tamuTableContainer">
                                @include('antrian-bongkar-muat._table', ['items' => $tamuList])
                            </div>
                        </div>
                    </div>

                    <!-- Tabel TKBM -->
                    <div class="col-xl-4">
                        <div class="card card-custom gutter-b shadow-sm">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Riwayat TKBM</span>
                                </h3>
                            </div>
                            <div class="card-body pt-3 pb-5" id="tkbmTableContainer">
                                @include('antrian-bongkar-muat._table', ['items' => $tkbmList])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@section('scripts')
<script>
    // Auto refresh dashboard setiap 5 detik
    function refreshDashboard() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            dataType: 'html',
            cache: false,
            success: function(data) {
                // Ambil seluruh konten refresh-container dari HTML respon
                const newContent = $(data).find('#refresh-container').html();
                if (newContent) {
                    $('#refresh-container').html(newContent);
                    console.log('Dashboard updated at ' + new Date().toLocaleTimeString());
                }
            },
            error: function() {
                console.error('Gagal memperbarui data dashboard.');
            }
        });
    }

    // Jalankan polling setiap 5 detik
    setInterval(refreshDashboard, 5000);
</script>
@endsection

