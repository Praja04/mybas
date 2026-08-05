@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .metric-card { border-radius: 12px; transition: transform 0.2s; }
    .metric-card:hover { transform: translateY(-3px); }
    .bg-gradient-green { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
    .bg-gradient-secondary { background: linear-gradient(135deg, #606c88, #3f4c6b); color: #fff; }
    .bg-gradient-danger { background: linear-gradient(135deg, #eb3b5a, #fa8231); color: #fff; }
    .bg-gradient-rejected { background: linear-gradient(135deg, #cb2d3e, #ef473a); color: #fff; }
    .bg-gradient-cancel { background: linear-gradient(135deg, #8e9eab, #eef2f3); color: #333; }
    .bg-gradient-amber { background: linear-gradient(135deg, #ff9800, #f57c00); color: #fff; }
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-dashboard-line me-2"></i> Dashboard Analitik & Klasifikasi SP Karyawan</h4>
            <div>
                <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExportSp">
                    <i class="ri-file-excel-2-line me-1"></i> Export Data SP (Excel / PDF)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 5 Klasifikasi Utama SP + SP Diproses -->
<div class="row mb-4">
    <!-- 1. SP Aktif -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-green border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">SP Aktif (<= 6 Bln)</h6>
                        <h3 class="text-white mb-0 fw-bold">{{ $totalSpActive }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-checkbox-circle-line fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SP Tidak Aktif / Expired -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-secondary border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">Tidak Aktif (> 6 Bln)</h6>
                        <h3 class="text-white mb-0 fw-bold">{{ $totalSpExpired }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-history-line fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SP+3 / SP Berat -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-danger border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">SP+3 / SP Berat</h6>
                        <h3 class="text-white mb-0 fw-bold">{{ $totalSpBerat }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-alert-line fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. SP Ditolak -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-rejected border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">SP Ditolak</h6>
                        <h3 class="text-white mb-0 fw-bold">{{ $totalSpRejected }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-close-circle-line fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. SP Cancel -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-cancel border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">SP Cancel</h6>
                        <h3 class="text-dark mb-0 fw-bold">{{ $totalSpCancelled }}</h3>
                    </div>
                    <div class="avatar-sm bg-secondary bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-ban-line fs-3 text-dark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. SP Diproses -->
    <div class="col-md-4 col-xl-2 mb-3">
        <div class="card metric-card bg-gradient-amber border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">Sedang Diproses</h6>
                        <h3 class="text-white mb-0 fw-bold">{{ $totalSpProcess }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-time-line fs-3 text-white"></i>
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
</div>

<!-- Modal Export Data SP -->
<div class="modal fade" id="modalExportSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('sp_pelanggaran.export') }}" method="GET" target="_blank">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fs-6 fw-bold"><i class="ri-download-2-line me-1"></i> Export Riwayat & Klasifikasi SP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Klasifikasi Data SP:</label>
                        <select name="kategori" class="form-select">
                            <option value="ALL">Semua Klasifikasi Status (Aktif, Expired, SP3, Ditolak, Cancel)</option>
                            <option value="AKTIF">🟢 SP Aktif (Berlaku <= 6 Bulan)</option>
                            <option value="EXPIRED">⚪ Tidak Aktif (Expired > 6 Bulan)</option>
                            <option value="SP3">🔴 SP+3 / SP Berat</option>
                            <option value="DITOLAK">⛔ SP Ditolak</option>
                            <option value="CANCEL">⚠️ SP Cancel / Dibatalkan</option>
                            <option value="PROSES">⏳ SP Sedang Diproses</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dari Tanggal:</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sampai Tanggal:</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Format Output Export:</label>
                        <select name="format" class="form-select">
                            <option value="excel">📊 Excel Spreadsheet (.xlsx)</option>
                            <option value="pdf">📄 Dokumen Resmi PDF (.pdf)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="ri-download-line me-1"></i> Download File</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
