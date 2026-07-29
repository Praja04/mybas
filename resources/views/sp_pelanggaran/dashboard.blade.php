@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .metric-card { border-radius: 12px; transition: transform 0.2s; }
    .metric-card:hover { transform: translateY(-3px); }
    .bg-gradient-blue { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #fff; }
    .bg-gradient-amber { background: linear-gradient(135deg, #ff9800, #f57c00); color: #fff; }
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-dashboard-line me-2"></i> Dashboard Analitik SP Karyawan</h4>
        </div>
    </div>
</div>

        <div class="row mb-4">
            <div class="col-md-6 col-xl-6">
                <div class="card metric-card bg-gradient-blue border-0 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 text-uppercase fw-bold mb-1">Total SP Aktif ({{ $currentYear }})</h6>
                                <h2 class="text-white mb-0 fw-bold">{{ $totalSpActive }}</h2>
                            </div>
                            <div class="avatar-md bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-file-shield-2-line fs-1 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-6">
                <div class="card metric-card bg-gradient-amber border-0 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 text-uppercase fw-bold mb-1">SP Sedang Diproses</h6>
                                <h2 class="text-white mb-0 fw-bold">{{ $totalSpProcess }}</h2>
                            </div>
                            <div class="avatar-md bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-time-line fs-1 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header gradient-header py-3">
                        <h5 class="card-title mb-0 text-white"><i class="ri-bar-chart-fill me-2"></i> Top Departemen Penyumbang SP</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>No</th>
                                        <th>Departemen / Divisi</th>
                                        <th>Total SP Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topDepartments as $index => $dept)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $dept['dept'] }}</strong></td>
                                        <td><span class="badge bg-danger fs-6">{{ $dept['total'] }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data SP yang disetujui.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
@endsection
