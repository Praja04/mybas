@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #0f172a, #1e293b); color: #ffffff; }
    .gradient-card-blue   { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
    .gradient-card-green  { background: linear-gradient(135deg, #059669, #047857); color: white; }
    .gradient-card-red    { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }
    .gradient-card-purple { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }

    .dropzone-box {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .dropzone-box:hover, .dropzone-box.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .status-badge-mismatch  { background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .status-badge-match     { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-badge-notfound  { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .filter-tab-active { background-color: #2563eb !important; color: white !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Title --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="fw-bold text-dark mb-1">
                <i class="ri-search-eye-line text-primary me-2"></i> Audit Mangkir
            </h3>
            <p class="text-muted mb-0">Cocokkan rekap absensi HR (Excel) dengan data SP Mangkir di sistem.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            @if($canInput)
                <a href="{{ route('sp_mangkir.index') }}" class="btn btn-outline-primary shadow-sm me-2">
                    <i class="ri-add-circle-line me-1"></i> Form Input SP Mangkir
                </a>
            @endif
            <a href="{{ route('sp_mangkir.trace') }}" class="btn btn-outline-info shadow-sm">
                <i class="ri-radar-line me-1"></i> Trace SP Mangkir
            </a>
        </div>
    </div>

    {{-- Read-only banner for non-IR-Staff --}}
    @if(!$canInput)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 rounded-3">
            <i class="ri-information-fill fs-3 me-3 text-warning"></i>
            <div>
                <strong class="d-block text-dark mb-1">Mode Lihat Hasil Audit (Read-Only)</strong>
                <span class="text-muted">Sebagai <strong>{{ $isDeptHead ? 'Dept Head' : 'IR Head' }}</strong>,
                    Anda dapat melihat hasil audit. Penginputan SP Mangkir hanya dilakukan oleh <strong>IR Staff</strong>.
                </span>
            </div>
        </div>
    @endif

    {{-- Upload Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header gradient-header py-3">
            <h5 class="card-title text-white mb-0 fs-6">
                <i class="ri-file-upload-line me-2"></i> Unggah File Rekap Absensi HR (.xls / .xlsx)
            </h5>
        </div>
        <div class="card-body p-4">
            <form id="auditUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-center">
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div class="dropzone-box p-4 text-center" id="dropzoneArea">
                            <i class="ri-file-excel-2-line text-success display-4 mb-2"></i>
                            <h6 class="fw-bold mb-1">Drag & Drop file Excel Absensi di sini</h6>
                            <p class="text-muted small mb-2">Atau klik untuk memilih file dari komputer (.xls, .xlsx, .csv)</p>
                            <span class="badge bg-light text-dark border px-3 py-2" id="selectedFileName">Belum ada file dipilih</span>
                        </div>
                        <input type="file" id="excelFile" name="file" class="d-none" accept=".xls,.xlsx,.csv">
                    </div>
                    <div class="col-lg-5">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="fw-semibold text-dark mb-2">
                                <i class="ri-filter-3-line me-1"></i> Kode Absensi yang Diaudit:
                            </label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_codes[]" value="A" id="codeA" checked>
                                    <label class="form-check-label fw-bold text-danger" for="codeA">A / ALFA (Mangkir)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_codes[]" value="KD" id="codeKD" checked>
                                    <label class="form-check-label fw-semibold text-warning" for="codeKD">KD (Kurang Disiplin)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_codes[]" value="L3" id="codeL3" checked>
                                    <label class="form-check-label fw-semibold text-secondary" for="codeL3">L3 (>30m)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_codes[]" value="L1" id="codeL1" checked>
                                    <label class="form-check-label fw-semibold text-secondary" for="codeL1">L1</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="target_codes[]" value="L2" id="codeL2" checked>
                                    <label class="form-check-label fw-semibold text-secondary" for="codeL2">L2</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="btnProsesAudit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                            <i class="ri-play-circle-line me-2"></i> Jalankan Audit Mangkir
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== DATABASE AUDIT RESULTS (Server-Rendered) ==================== --}}
    @if($kpiData)

        {{-- Period Selector --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="ri-bar-chart-line text-primary me-2"></i>
                    Hasil Audit: <span class="text-primary">{{ $periodLabel }}</span>
                </h5>
                @if($auditFilename)
                    <span class="badge bg-light text-muted border small">
                        <i class="ri-file-text-line me-1"></i>{{ $auditFilename }}
                        @if($auditUploadedAt) &nbsp;·&nbsp; {{ $auditUploadedAt }} @endif
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <form method="GET" action="{{ route('sp_mangkir.audit') }}" id="periodForm" class="d-flex gap-2 align-items-center">
                    <input type="hidden" name="filter" value="{{ $filterStatus }}">
                    <label class="text-muted small fw-semibold mb-0 me-1">Periode:</label>
                    <div class="input-group input-group-sm" style="width: 170px;">
                        <span class="input-group-text bg-light text-muted"><i class="ri-calendar-event-line"></i></span>
                        <input type="month" name="periode" class="form-control form-control-sm" value="{{ $selectedPeriod }}" onchange="this.form.submit()">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary fw-semibold">
                        <i class="ri-search-line me-1"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm gradient-card-blue rounded-3 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 text-uppercase small fw-bold mb-1">Total Absensi</p>
                                <h2 class="fw-bold mb-0 text-white">{{ $kpiData['total_excel_mangkir'] }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                                <i class="ri-file-list-3-line fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm gradient-card-green rounded-3 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 text-uppercase small fw-bold mb-1">Sudah Input SP</p>
                                <h2 class="fw-bold mb-0 text-white">{{ $kpiData['total_sudah_input'] }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                                <i class="ri-checkbox-circle-line fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm gradient-card-red rounded-3 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 text-uppercase small fw-bold mb-1">Belum Input</p>
                                <h2 class="fw-bold mb-0 text-white">{{ $kpiData['total_belum_input'] }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                                <i class="ri-alert-line fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm gradient-card-purple rounded-3 h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 text-uppercase small fw-bold mb-1">Total Karyawan</p>
                                <h2 class="fw-bold mb-0 text-white">{{ $kpiData['total_karyawan'] }}</h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white">
                                <i class="ri-user-unfollow-line fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Table Card --}}
        <div class="card border-0 shadow-sm">
            {{-- Filter Tabs (server-side links) & Search Form --}}
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <ul class="nav nav-pills card-header-pills mb-0">
                    <li class="nav-item">
                        <a href="{{ route('sp_mangkir.audit', array_merge(request()->query(), ['filter' => 'ALL'])) }}"
                           class="nav-link fw-bold {{ $filterStatus == 'ALL' ? 'active' : '' }}">
                            Semua <span class="badge bg-secondary ms-1">{{ $kpiData['total_excel_mangkir'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sp_mangkir.audit', array_merge(request()->query(), ['filter' => 'BELUM_INPUT'])) }}"
                           class="nav-link text-danger fw-bold {{ $filterStatus == 'BELUM_INPUT' ? 'active' : '' }}">
                            Belum Input <span class="badge bg-danger ms-1">{{ $kpiData['total_belum_input'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sp_mangkir.audit', array_merge(request()->query(), ['filter' => 'SUDAH_INPUT'])) }}"
                           class="nav-link text-success fw-bold {{ $filterStatus == 'SUDAH_INPUT' ? 'active' : '' }}">
                            Sudah Input <span class="badge bg-success ms-1">{{ $kpiData['total_sudah_input'] }}</span>
                        </a>
                    </li>
                </ul>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('sp_mangkir.audit') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="periode" value="{{ $selectedPeriod }}">
                    <input type="hidden" name="filter" value="{{ $filterStatus }}">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <span class="input-group-text bg-light text-muted"><i class="ri-search-line"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIK, Nama, Dept..." value="{{ $search ?? '' }}">
                        @if(!empty($search))
                            <a href="{{ route('sp_mangkir.audit', ['periode' => $selectedPeriod, 'filter' => $filterStatus]) }}" class="btn btn-outline-secondary btn-sm" title="Reset Pencarian">
                                <i class="ri-close-line"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-semibold">Cari</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="auditTable">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="text-center" style="width:50px">No</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Departemen / Section</th>
                            <th>Tanggal Mangkir</th>
                            <th class="text-center">Mangkir ke-</th>
                            <th class="text-center">Kode</th>
                            <th>Status Audit</th>
                            <th>Nomor / Status SP Mangkir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditRecordsPaginated as $idx => $rec)
                            @php
                                $rowNo = ($auditRecordsPaginated->currentPage() - 1) * $auditRecordsPaginated->perPage() + $loop->iteration;
                            @endphp
                            <tr>
                                <td class="text-center text-muted small fw-semibold">{{ $rowNo }}</td>
                                <td class="fw-bold text-dark">{{ $rec->nik }}</td>
                                <td class="fw-semibold text-dark">{{ $rec->nama }}</td>
                                <td>
                                    {{ $rec->department ?? '-' }}
                                    <small class="text-muted">({{ $rec->section ?? '-' }})</small>
                                </td>
                                <td>
                                    <i class="ri-calendar-line text-muted me-1"></i>
                                    {{ \Carbon\Carbon::parse($rec->tanggal)->isoFormat('D MMMM YYYY') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">Ke-{{ $rec->mangkir_ke }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $rec->kode_absensi }}</span>
                                </td>
                                <td>
                                    @if($rec->status_audit === 'SUDAH_INPUT')
                                        <span class="badge status-badge-match px-2 py-1">
                                            <i class="ri-checkbox-circle-line me-1"></i> Sudah Input SP
                                        </span>
                                    @elseif($rec->status_audit === 'BELUM_INPUT')
                                        <span class="badge status-badge-mismatch px-2 py-1">
                                            <i class="ri-alert-line me-1"></i> Belum Input SP
                                        </span>
                                    @else
                                        <span class="badge status-badge-notfound px-2 py-1">
                                            <i class="ri-user-unfollow-line me-1"></i> NIK Tidak Ada di DB
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($rec->sp_nomor)
                                        <span class="fw-semibold text-primary">{{ $rec->sp_nomor }}</span>
                                        @if($rec->sp_status)
                                            <br><small class="text-muted">{{ $rec->sp_status }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                                    Tidak ada data audit untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($auditRecordsPaginated->hasPages())
                <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                    <p class="text-muted small mb-0">
                        Menampilkan {{ $auditRecordsPaginated->firstItem() }}–{{ $auditRecordsPaginated->lastItem() }}
                        dari {{ $auditRecordsPaginated->total() }} data
                    </p>
                    {{ $auditRecordsPaginated->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    @elseif(count($availablePeriods) == 0)
        {{-- No data at all --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ri-file-upload-line text-muted display-4 d-block mb-3"></i>
                <h5 class="text-dark fw-bold">Belum Ada Data Audit Mangkir</h5>
                <p class="text-muted">Upload file Excel rekap absensi HR di atas untuk memulai audit.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
$(document.body).ready(function () {
    const canInputSp = @json($canInput ?? false);

    // ======== Dropzone ========
    const dropzone  = $('#dropzoneArea');
    const fileInput = $('#excelFile');

    dropzone.on('click', function (e) {
        if ($(e.target).is('#excelFile')) return;
        fileInput.trigger('click');
    });
    fileInput.on('click', function (e) { e.stopPropagation(); });
    fileInput.on('change', function () {
        if (this.files && this.files[0]) {
            $('#selectedFileName').text(this.files[0].name)
                .removeClass('bg-light text-dark').addClass('bg-primary text-white');
        }
    });
    dropzone.on('dragover dragenter', function (e) {
        e.preventDefault(); dropzone.addClass('dragover');
    });
    dropzone.on('dragleave drop', function (e) {
        e.preventDefault(); dropzone.removeClass('dragover');
    });
    dropzone.on('drop', function (e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files && files[0]) {
            fileInput[0].files = files;
            $('#selectedFileName').text(files[0].name)
                .removeClass('bg-light text-dark').addClass('bg-primary text-white');
        }
    });

    // ======== Upload & Audit Submit ========
    function submitAuditForm(formData) {
        const btn = $('#btnProsesAudit');
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-2"></i> Memproses Audit...');

        $.ajax({
            url: "{{ route('sp_mangkir.audit_process') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                btn.prop('disabled', false).html('<i class="ri-play-circle-line me-2"></i> Jalankan Audit Mangkir');

                if (res.status === 'confirm_overwrite') {
                    Swal.fire({
                        title: 'Data Audit Sudah Ada!',
                        text: res.message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ri-refresh-line me-1"></i> Ya, Timpa & Perbarui Data',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc2626'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            formData.set('overwrite', '1');
                            submitAuditForm(formData);
                        }
                    });
                    return;
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Audit Selesai & Tersimpan!',
                        text: res.message || 'Data audit berhasil disimpan ke database.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(function () {
                        // Reload page with the new period selected
                        if (res.data && res.data.period_key) {
                            window.location.href = "{{ route('sp_mangkir.audit') }}?periode=" + res.data.period_key;
                        } else {
                            window.location.reload();
                        }
                    });
                }
            },
            error: function (err) {
                btn.prop('disabled', false).html('<i class="ri-play-circle-line me-2"></i> Jalankan Audit Mangkir');
                const errMsg = err.responseJSON ? err.responseJSON.message : 'Gagal memproses file Excel.';
                Swal.fire('Gagal', errMsg, 'error');
            }
        });
    }

    $('#auditUploadForm').on('submit', function (e) {
        e.preventDefault();
        const file = fileInput[0].files[0];
        if (!file) {
            Swal.fire('Perhatian', 'Pilih file Excel rekap absensi terlebih dahulu!', 'warning');
            return;
        }
        submitAuditForm(new FormData(this));
    });
});
</script>
@endpush
