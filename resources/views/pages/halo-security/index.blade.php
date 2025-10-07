@extends('pages.halo-security.layout.base')

@section('title', 'Dashboard')

@section('content')

    <div class="container-fluid">
        <h1>Welcome To Halo Security</h1>

        <div class="col-md-12 mt-4">
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #F2BD12;">Total Laporan Kejadian</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #F2BD12;">{{ $jumlahKejadian }}</h4>
                                    <a href="{{ route('ba-list-laporankejadian') }}" style="text-decoration: none;">Kunjungi Menu Laporan Kejadian</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning rounded fs-3">
                                        <i class="bx bxs-edit-location"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #4B38B3;">Total BA S.O.P Karyawan</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #4B38B3;">{{ $jumlahKaryawan }}</h4>
                                    <a href="{{ route('ba-sop-list-karyawan') }}" style="text-decoration: none;">Kunjungi Menu BA S.O.P Karyawan</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary rounded fs-3">
                                        <i class="bx bx-buildings"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #4E9CDC;"> Total BA S.O.P Supir</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #4E9CDC;">{{ $jumlahSupir }}</h4>
                                    <a href="{{ route('ba-sop-list-supir') }}" style="text-decoration: none;">Kunjungi Menu BA S.O.P Supir</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info rounded fs-3">
                                        <i class="bx bx-car"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #E96447;"> Total BA Introgasi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #E96447;">{{ $jumlahIntrogasi }}</h4>
                                    <a href="{{ route('ba-list-introgasi') }}" style="text-decoration: none;">Kunjungi Menu BA Introgasi</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger rounded fs-3">
                                        <i class="bx bxs-user-detail"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #3F77F1;"> Total Template Tanya Jawab BA Introgasi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #3F77F1;">{{ $jumlahTemplate }}</h4>
                                    <a href="{{ route('tambah-introgasi') }}" style="text-decoration: none;">Kunjungi Menu Template Tanya Jawab BA Introgasi</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-secondary rounded fs-3">
                                        <i class="bx bx-pie-chart-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
                <div class="col-xl-4 col-md-6">
                    <!-- card -->
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium mb-0" style="color: #63CC86;"> Total Security User GA</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4" style="color: #63CC86;">{{ $jumlahSug }}</h4>
                                    <a href="{{ route('security') }}" style="text-decoration: none;">Kunjungi Menu Security User GA</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success rounded fs-3">
                                        <i class="bx bxs-user-pin"></i>
                                    </span>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div>
        </div>
    </div>

@endsection