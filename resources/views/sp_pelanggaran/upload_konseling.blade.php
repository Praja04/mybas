@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .status-badge { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .stats-card { border: none; border-radius: 12px; transition: transform 0.2s ease; }
    .stats-card:hover { transform: translateY(-3px); }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-file-certificate-line me-2"></i> Upload & Data Hasil Konseling SP</h4>
            <div class="page-title-right">
                <span class="badge bg-soft-info text-info border border-info px-3 py-2">
                    <i class="ri-information-line me-1"></i> SP Terbit (Pelanggaran & Mangkir)
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row mb-3 g-3">
    <div class="col-md-4">
        <div class="card stats-card bg-primary text-white shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 fw-bold text-uppercase" style="font-size: 11px;">Total SP Terbit</h6>
                    <h3 class="text-white mb-0 fw-bold">{{ $totalTerbit }}</h3>
                </div>
                <div class="avatar-sm bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-checkbox-circle-line fs-20 text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card bg-success text-white shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 fw-bold text-uppercase" style="font-size: 11px;">Sudah Upload Konseling</h6>
                    <h3 class="text-white mb-0 fw-bold">{{ $countSudah }}</h3>
                </div>
                <div class="avatar-sm bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-file-pdf-line fs-20 text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card bg-warning text-dark shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-dark-50 mb-1 fw-bold text-uppercase" style="font-size: 11px;">Belum Upload Konseling</h6>
                    <h3 class="text-dark mb-0 fw-bold">{{ $countBelum }}</h3>
                </div>
                <div class="avatar-sm bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-alert-line fs-20 text-dark"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('sp_pelanggaran.upload_konseling') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama, No SP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="sumber" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Sumber SP --</option>
                    <option value="PELANGGARAN" {{ request('sumber') === 'PELANGGARAN' ? 'selected' : '' }}>SP Pelanggaran</option>
                    <option value="MANGKIR" {{ request('sumber') === 'MANGKIR' ? 'selected' : '' }}>SP Mangkir</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status_konseling" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Status Konseling --</option>
                    <option value="SUDAH" {{ request('status_konseling') === 'SUDAH' ? 'selected' : '' }}>Sudah Upload Konseling</option>
                    <option value="BELUM" {{ request('status_konseling') === 'BELUM' ? 'selected' : '' }}>Belum Upload Konseling</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-3-line me-1"></i> Filter</button>
                <a href="{{ route('sp_pelanggaran.upload_konseling') }}" class="btn btn-outline-secondary"><i class="ri-refresh-line"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header gradient-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 text-white">
            <i class="ri-file-list-3-line me-2"></i> Daftar SP Terbit & Dokumen Hasil Konseling
        </h5>
        @if(!$canUpload)
            <span class="badge bg-light text-dark fw-normal"><i class="ri-lock-line me-1"></i> Mode Read-Only (IR / Dept Head)</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th>Penomoran SP</th>
                        <th>Tanggal Penerbit</th>
                        <th>Karyawan</th>
                        <th>Jenis & Sumber SP</th>
                        <th class="text-center">Status Konseling</th>
                        <th class="text-center" width="22%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spRecords as $index => $sp)
                    <tr>
                        <td class="text-center">{{ ($spRecords->currentPage() - 1) * $spRecords->perPage() + $index + 1 }}</td>
                        <td>
                            <strong class="text-primary">{{ $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT') }}</strong>
                            @if($sp->kode_admin)
                                <br><small class="text-muted">{{ $sp->kode_admin }}</small>
                            @endif
                        </td>
                        <td>
                            @if($sp->dates && $sp->dates->count() > 1)
                                <strong>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</strong>
                                <br><small class="badge bg-info text-dark" style="font-size: 10px;"><i class="ri-calendar-event-line me-1"></i>{{ $sp->dates->count() }} Tanggal</small>
                            @elseif($sp->tanggal_pelanggaran)
                                {{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}
                            @else
                                -
                            @endif
                            <br><small class="text-muted">Terbit: {{ $sp->ir_head_approved_at ? \Carbon\Carbon::parse($sp->ir_head_approved_at)->format('d M Y') : '-' }}</small>
                        </td>
                        <td>
                            <strong>{{ $sp->employee->nama ?? '-' }}</strong>
                            <br><code>{{ $sp->employee->nik ?? '-' }}</code> <span class="text-muted">({{ $sp->employee->kode_divisi ?? $sp->employee->kode_bagian ?? '-' }})</span>
                        </td>
                        <td>
                            <span class="badge {{ in_array($sp->jenis_pelanggaran, ['SP 3','Surat Peringatan 3 (SP 3)']) ? 'bg-danger' : 'bg-info text-dark' }}">
                                {{ $sp->jenis_pelanggaran ?: ($sp->kode_admin ?: 'SP') }}
                            </span>
                            <br>
                            <small class="badge bg-light text-dark border mt-1">
                                <i class="{{ $sp->sumber_data === 'MANGKIR' ? 'ri-account-clock-line' : 'ri-shield-user-line' }} me-1"></i>
                                {{ $sp->sumber_data ?: 'PELANGGARAN' }}
                            </small>
                        </td>
                        <td class="text-center">
                            @if($sp->file_konseling)
                                <span class="badge bg-success status-badge mb-1"><i class="ri-checkbox-circle-line me-1"></i>Sudah Upload</span>
                                <br>
                                <small class="text-muted" style="font-size: 10px;">
                                    <i class="ri-time-line"></i> {{ \Carbon\Carbon::parse($sp->uploaded_konseling_at)->format('d/m/Y H:i') }}
                                    @if($sp->uploaderKonseling)
                                        <br>by {{ $sp->uploaderKonseling->name }}
                                    @endif
                                </small>
                            @else
                                <span class="badge bg-warning text-dark status-badge"><i class="ri-alert-line me-1"></i>Belum Ada File</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info btnDetailSp me-1" data-id="{{ $sp->id }}" title="Detail SP">
                                <i class="ri-eye-line me-1"></i> Detail
                            </button>

                            @if($sp->file_konseling)
                                <a href="{{ asset($sp->file_konseling) }}" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Lihat PDF Konseling">
                                    <i class="ri-file-pdf-line me-1"></i> Lihat PDF
                                </a>
                            @endif

                            @if($canUpload)
                                <button class="btn btn-sm {{ $sp->file_konseling ? 'btn-outline-primary' : 'btn-primary' }} btnUploadKonseling me-1" 
                                        data-id="{{ $sp->id }}" 
                                        data-nomor="{{ $sp->nomor_sp_generated ?: $sp->no_sp }}"
                                        data-nama="{{ $sp->employee->nama ?? '-' }}"
                                        data-hasfile="{{ $sp->file_konseling ? '1' : '0' }}"
                                        title="{{ $sp->file_konseling ? 'Ganti PDF Konseling' : 'Upload PDF Konseling' }}">
                                    <i class="{{ $sp->file_konseling ? 'ri-refresh-line' : 'ri-upload-2-line' }} me-1"></i> 
                                    {{ $sp->file_konseling ? 'Update PDF' : 'Upload PDF' }}
                                </button>

                                @if($sp->file_konseling)
                                    <button class="btn btn-sm btn-outline-danger btnDeleteKonseling" data-id="{{ $sp->id }}" title="Hapus PDF Konseling">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ri-file-search-line fs-24 d-block mb-2 text-muted"></i>
                            Tidak ada data SP terbit yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($spRecords->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $spRecords->links() }}
    </div>
    @endif
</div>

<!-- Modal Upload PDF Konseling -->
<div class="modal fade" id="modalUploadKonseling" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUploadKonseling" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="upload_sp_id" name="sp_id">
                <div class="modal-header gradient-header text-white">
                    <h5 class="modal-title text-white">
                        <i class="ri-upload-cloud-line me-2"></i> Upload PDF Hasil Konseling
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ri-information-line fs-20 me-2"></i>
                            <div>
                                <strong id="upload_karyawan_nama">-</strong>
                                <br><small id="upload_sp_nomor" class="text-muted">-</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File PDF Hasil Konseling <span class="text-danger">*</span></label>
                        <input type="file" name="file_konseling" id="file_konseling_input" class="form-control" accept=".pdf" required>
                        <small class="text-muted d-block mt-1"><i class="ri-file-warning-line me-1"></i> Format file harus PDF (Maksimal 2 MB).</small>
                    </div>

                    <div id="upload_progress_container" class="d-none mb-3">
                        <div class="progress" style="height: 10px;">
                            <div id="upload_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="upload_progress_text" class="text-muted d-block mt-1 text-center">Memproses unggahan file...</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitUpload">
                        <i class="ri-upload-2-line me-1"></i> Unggah File Konseling
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail SP -->
<div class="modal fade" id="modalDetailSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-header text-white">
                <h5 class="modal-title text-white">
                    <i class="ri-file-text-line me-2"></i> Detail Surat Peringatan (SP)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Karyawan:</td>
                                <td><strong id="detail_nama_karyawan">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK:</td>
                                <td><code id="detail_nik_karyawan">-</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Divisi / Bagian:</td>
                                <td id="detail_dept_karyawan">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Pelanggaran:</td>
                                <td id="detail_tanggal_pelanggaran">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nomor SP:</td>
                                <td><strong class="text-primary" id="detail_nomor_sp">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode Admin / Mangkir:</td>
                                <td><span class="badge bg-light text-dark border" id="detail_kode_admin">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode IR:</td>
                                <td><span class="badge bg-secondary" id="detail_kode_ir">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis / Tingkat SP:</td>
                                <td><span class="badge bg-danger" id="detail_jenis_sp">-</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="p-3 bg-light rounded mb-3">
                    <h6 class="fw-bold text-primary mb-1"><i class="ri-file-shield-line me-1"></i> Pasal / Aturan yang Dilanggar:</h6>
                    <p class="mb-2 text-dark" id="detail_pasal_dilanggar">-</p>

                    <h6 class="fw-bold text-primary mb-1"><i class="ri-align-left me-1"></i> Alasan / Bentuk Pelanggaran:</h6>
                    <p class="mb-0 text-dark" id="detail_alasan">-</p>
                </div>

                <div class="mb-3" id="container_lampiran">
                    <label class="fw-bold text-muted small d-block mb-1">Lampiran & Dokumen SP:</label>
                    <div id="detail_lampiran_link">Tidak ada lampiran</div>
                </div>

                <div class="mb-0">
                    <h6 class="fw-bold text-primary"><i class="ri-history-line me-1"></i> Riwayat Approval & Log System:</h6>
                    <div id="detail_approval_logs" class="small text-muted">Belum ada log approval.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let modalDetail = new bootstrap.Modal(document.getElementById('modalDetailSp'));
    let modalUpload = new bootstrap.Modal(document.getElementById('modalUploadKonseling'));

    // Open Upload Modal
    $(document).on('click', '.btnUploadKonseling', function() {
        let id = $(this).data('id');
        let nomor = $(this).data('nomor');
        let nama = $(this).data('nama');
        let hasFile = $(this).data('hasfile');

        $('#upload_sp_id').val(id);
        $('#upload_karyawan_nama').text(nama);
        $('#upload_sp_nomor').text('Nomor SP: ' + (nomor || 'PROSES'));
        $('#file_konseling_input').val('');
        $('#upload_progress_container').addClass('d-none');
        $('#btnSubmitUpload').prop('disabled', false).html('<i class="ri-upload-2-line me-1"></i> ' + (hasFile === 1 || hasFile === '1' ? 'Ganti File PDF Konseling' : 'Unggah File Konseling'));

        modalUpload.show();
    });

    // Handle Upload Submit via AJAX
    $('#formUploadKonseling').on('submit', function(e) {
        e.preventDefault();
        let spId = $('#upload_sp_id').val();
        let fileInput = $('#file_konseling_input')[0];

        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih file PDF konseling terlebih dahulu!', 'warning');
            return;
        }

        // Cek ukuran file di sisi client (Max 2MB = 2097152 bytes)
        if (fileInput.files[0].size > 2 * 1024 * 1024) {
            Swal.fire('Ukuran File Terlalu Besar!', 'Ukuran file PDF konseling maksimal 2 MB. Silakan kompres file Anda terlebih dahulu sebelum mengunggah.', 'warning');
            return;
        }

        let formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $('#upload_progress_container').removeClass('d-none');
        $('#btnSubmitUpload').prop('disabled', true).html('<i class="ri-loader-4-line spin me-1"></i> Mengunggah...');

        $.ajax({
            url: '/sp-pelanggaran/' + spId + '/upload-konseling',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#upload_progress_bar').css('width', percentComplete + '%');
                        $('#upload_progress_text').text('Mengunggah: ' + percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(res) {
                modalUpload.hide();
                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                $('#upload_progress_container').addClass('d-none');
                $('#btnSubmitUpload').prop('disabled', false).html('<i class="ri-upload-2-line me-1"></i> Unggah File Konseling');
                let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengunggah file konseling.';
                Swal.fire('Gagal!', err, 'error');
            }
        });
    });

    // Handle Delete Konseling PDF
    $(document).on('click', '.btnDeleteKonseling', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus File PDF Konseling?',
            text: 'File PDF hasil konseling yang diunggah akan dihapus secara permanen dari server.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus File',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: '/sp-pelanggaran/' + id + '/delete-konseling',
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Dihapus!', res.message, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menghapus file.';
                        Swal.fire('Gagal!', err, 'error');
                    }
                });
            }
        });
    });

    // Detail Action Handler
    $(document).on('click', '.btnDetailSp', function() {
        let id = $(this).data('id');
        $.get('/sp-pelanggaran/' + id + '/detail', function(res) {
            if (res.status === 'success') {
                let data = res.data;
                let emp = data.employee || {};

                $('#detail_nama_karyawan').text(emp.nama || '-');
                $('#detail_nik_karyawan').text(emp.nik || '-');
                $('#detail_dept_karyawan').text(emp.kode_divisi || emp.kode_bagian || '-');
                let dateDisplay = '-';
                let datesArr = (data.dates && data.dates.length > 0) ? data.dates.map(d => d.tanggal) : (data.tanggal_pelanggaran ? [data.tanggal_pelanggaran] : []);
                if (datesArr.length > 0) {
                    let formattedList = datesArr.map(d => {
                        let parts = d.split('-');
                        return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : d;
                    });
                    if (datesArr.length > 1) {
                        dateDisplay = `<strong>${formattedList[0]}</strong> <span class="badge bg-info text-dark">Multi-Date (${datesArr.length} Tanggal)</span><br><small class="text-primary"><i class="ri-calendar-event-line me-1"></i>Total ${datesArr.length} Tanggal Kejadian: ${formattedList.join(', ')}</small>`;
                    } else {
                        dateDisplay = formattedList[0];
                    }
                }
                $('#detail_tanggal_pelanggaran').html(dateDisplay);

                $('#detail_nomor_sp').text(data.nomor_sp_generated || data.no_sp || 'DRAFT');
                $('#detail_kode_admin').text(data.kode_admin || '-');
                $('#detail_kode_ir').text(data.kode_ir || '-');
                $('#detail_jenis_sp').text(data.jenis_pelanggaran || 'Belum Ditetapkan');

                $('#detail_pasal_dilanggar').text(data.pasal_dilanggar || '-');
                $('#detail_alasan').text(data.alasan || '-');

                let lampiranPelanggaran = data.lampiran
                    ? '<a href="/' + data.lampiran + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-file-download-line me-1"></i> Bukti SP</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran SP</span>';

                let lampiranKonseling = data.file_konseling
                    ? '<a href="/' + data.file_konseling + '" target="_blank" class="btn btn-sm btn-success fw-bold text-white"><i class="ri-file-pdf-line me-1"></i> PDF Hasil Konseling</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Belum ada file konseling</span>';

                let lampiranHtml = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-primary mb-1"><i class="ri-file-text-line me-1"></i> 1. Lampiran Bukti SP:</strong>
                                ${lampiranPelanggaran}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-success mb-1"><i class="ri-file-certificate-line me-1"></i> 2. File PDF Hasil Konseling:</strong>
                                ${lampiranKonseling}
                            </div>
                        </div>
                    </div>
                `;
                $('#detail_lampiran_link').html(lampiranHtml);

                // Render Approval Logs
                let logs = data.approval_logs || [];
                if (logs.length > 0) {
                    let html = '<ul class="list-group list-group-flush border-top mt-1">';
                    $.each(logs, function(i, log) {
                        html += '<li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">';
                        html += '<div><strong>' + log.action + '</strong> - <small>' + (log.notes || '-') + '</small></div>';
                        html += '<span class="badge bg-light text-dark">' + log.created_at + '</span>';
                        html += '</li>';
                    });
                    html += '</ul>';
                    $('#detail_approval_logs').html(html);
                } else {
                    $('#detail_approval_logs').html('<span class="text-muted">Belum ada riwayat approval.</span>');
                }

                modalDetail.show();
            }
        });
    });
});
</script>
@endpush
