@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/velzon/libs/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .swal2-icon { display:flex !important; align-items:center !important; justify-content:center !important; }
    .swal2-icon .swal2-icon-content { display:flex !important; align-items:center !important; justify-content:center !important; }
</style>
@endpush

@section('content')

<style>
    .mk-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .mk-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .mk-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .mk-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .mk-badge-created { background:#c8e6c9; color:#1b5e20; }
    .mk-badge-updated { background:#fff9c4; color:#827717; }
    .mk-badge-pending { background:#ffe0b2; color:#e65100; }
    .mk-badge-confirmed { background:#bbdefb; color:#0d47a1; }
    .mk-badge-overlap { background:#ffcdd2; color:#b71c1c; }
    .mk-modal-lg .modal-dialog { max-width: 95%; margin: 1.75rem auto; }
    .mk-table th { font-size:.85rem; }
    .mk-table td { font-size:.85rem; vertical-align: middle; }
    .mk-pagination .page-link { padding:.2rem .5rem; font-size:.85rem; }
    .mk-review-wrapper { max-height: 500px; overflow: auto; border: 1px solid #dee2e6; border-radius: 4px; }
    .mk-review-wrapper table { margin-bottom: 0; }
    .mk-review-wrapper thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
    .mk-review-wrapper td, .mk-review-wrapper th { white-space: nowrap; }
    .mk-confirm-progress { display:none; margin-top:.5rem; font-size:.85rem; color:#4a148c; }

    .mk-overlap-wrapper { max-height: 400px; overflow: auto; border: 1px solid #ffe0b2; border-radius: 4px; background: #fff8e1; padding: .5rem; }
    .mk-overlap-wrapper table { margin-bottom: 0; }
    .mk-orphan-wrapper { max-height: 420px; overflow: auto; border: 1px solid #ffe0b2; border-radius: 4px; padding: .5rem; }
    .mk-orphan-wrapper table { margin-bottom: 0; }
    .mk-orphan-info-box { background: #fff8e1; border: 1px solid #ffe0b2; border-radius: 4px; padding: .65rem .85rem; font-size: .85rem; }
</style>

<div class="container-fluid">
    <h1 class="mk-page-title">Upload File Mangkir HRDASH</h1>

    <div class="mk-card">
        <h5>Import Mangkir HRDASH CSV</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            <strong class="text-danger">Maksimum ukuran file: 128MB.</strong>
            Jika file lebih besar, pecah menjadi beberapa file atau hubungi admin untuk menaikkan batas upload.
        </p>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            <strong class="text-warning">Kode Ijin otomatis di-set "A" (Mangkir).</strong>
            No SPI dan Keterangan dikosongkan otomatis.
        </p>

        <form id="formUpload" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;">File CSV:</label>
                    <input type="file" name="file" id="fileInput" class="form-control-file" accept=".csv,.txt" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success" id="btnUpload">
                        Upload &amp; Import
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
            <strong>Kolom CSV (baris 1) — 12 kolom:</strong>
            NIK, Nama, Company, Dept, Section, Business Area, Tgl, Kode Shift, Shift Time, Break Time, No Tukar Shift, Keterangan
            <br><br>
            <strong>Kolom yang diambil (5):</strong>
            NIK, Nama, Dept, Section, Tgl
            <br>
            <strong>Kolom diabaikan:</strong>
            Company, Business Area, Kode Shift, Shift Time, Break Time, No Tukar Shift, Keterangan
            <br>
            <strong>Kode Ijin otomatis:</strong> A (Mangkir) &middot;
            <strong>No SPI:</strong> NULL &middot;
            <strong>Keterangan:</strong> NULL
        </div>
    </div>

    <div class="mk-card">
        <h5>Cek Data Mangkir Berdasarkan Tanggal</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            Pilih rentang tanggal untuk membandingkan data mangkir yang sudah ada di database
            dengan data dari batch import terbaru. NIK + Tgl yang ada di database tetapi
            tidak ada di data baru akan ditampilkan sebagai <strong>data orphan</strong>.
        </p>
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-weight:600;">Tanggal</label>
                <input type="text" class="form-control form-control-sm flatpickr-range"
                       id="orphanDateRange" placeholder="Pilih rentang tanggal..." autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-weight:600;">Tipe Karyawan</label>
                <select class="form-control form-control-sm" id="orphanTipe">
                    <option value="">-- Pilih --</option>
                    <option value="Staff">Staff</option>
                    <option value="Non Staff">Non Staff</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" id="btnCheckOrphans" disabled>
                    <i class="la la-search"></i> Cek Data
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnResetOrphans">
                    <i class="la la-undo"></i> Reset
                </button>
            </div>
            <div class="col-md-4">
                <div class="text-muted" id="orphanBatchInfo" style="font-size:.85rem;">
                    Batch aktif: <strong>-</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mk-card">
        <h5>Riwayat Import</h5>
        <div id="historyContainer">
            <p class="text-muted">Memuat...</p>
        </div>
        <div class="d-flex justify-content-center mt-2" id="historyPagination"></div>
    </div>

    <div class="mk-card">
        <h5>Mangkir HRDASH Records</h5>

        <form id="formSearch" class="d-flex mb-3" style="gap:.5rem;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Berdasarkan NIK...">
            <button type="submit" class="btn btn-primary">Cari Data</button>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.85rem;">
            <div>
                <span class="text-muted">Tampilkan</span>
                <select id="recPerPage" class="form-control form-control-sm d-inline-block" style="width:auto;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-muted">data per halaman</span>
            </div>
            <div class="text-muted" id="recInfo"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mk-table">
                <thead class="thead-light">
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Dept</th>
                        <th>Sub Departmen</th>
                        <th>Section</th>
                        <th>Tgl</th>
                        <th>No SPI</th>
                        <th>Kode Ijin</th>
                        <th>Ijin</th>
                        <th>Keterangan</th>
                        <th>Update Oleh</th>
                        <th>Update Pada</th>
                    </tr>
                </thead>
                <tbody id="recordTbody">
                    <tr><td colspan="12" class="text-center text-muted">Belum ada data.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2" id="recPagination"></div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg mk-modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Hasil Import - Review Data Mangkir</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="reviewHeader" class="mb-3" style="font-size:.85rem;"></div>
                <div id="reviewOverlapAlert" class="alert alert-warning" style="display:none; font-size:.85rem;">
                    <i class="la la-exclamation-triangle"></i>
                    <strong><span id="overlapCountText">0</span> baris</strong> overlap dengan data lembur
                    (NIK + Tgl yang sama). Akan diminta keputusan saat konfirmasi.
                </div>
                <div class="d-flex align-items-center mb-2" style="gap:1rem; flex-wrap: wrap;">
                    <label style="font-weight:600;">
                        <input type="checkbox" id="checkAll"> Pilih Semua Data
                    </label>
                    <span class="text-muted" style="font-size:.85rem;">Terpilih: <span id="selectedCount">0</span></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-auto" id="btnRefreshReview">
                        <i class="la la-refresh"></i> Refresh
                    </button>
                </div>
                <div class="mk-review-wrapper">
                    <table class="table table-bordered table-sm mk-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:32px;"><input type="checkbox" id="checkPage" title="Pilih halaman ini saja"></th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Dept</th>
                                <th>Section</th>
                                <th>Tgl</th>
                                <th>Kode Ijin</th>
                                <th>Ijin</th>
                                <th>Overlap</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="reviewTbody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-2" id="reviewPagination"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnConfirm">Konfirmasi Data Terpilih</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="overlapModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg mk-modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff3e0;">
                <h5 class="modal-title" style="color:#b71c1c; font-weight:700;">
                    <i class="la la-exclamation-triangle"></i> Konflik dengan Data Lembur
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size:.9rem;">
                    Ditemukan <strong><span id="overlapModalCount">0</span> baris mangkir</strong> yang memiliki
                    <strong>NIK + Tgl</strong> yang sama dengan data lembur (Working Time &amp; Overtime).
                    Pilih tindakan untuk setiap baris:
                </p>
                <div class="mk-overlap-wrapper">
                    <table class="table table-bordered table-sm mk-table">
                        <thead>
                            <tr style="background:#ffe0b2;">
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tgl</th>
                                <th>No SPKL (Lembur)</th>
                                <th style="min-width:260px;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="overlapTbody"></tbody>
                    </table>
                </div>
                <p class="text-muted mt-2" style="font-size:.8rem;">
                    <strong>Catatan:</strong> Pilih "Pakai Mangkir" akan otomatis menghapus data lembur yang konflik.
                    Pilih "Pakai Lembur" / "Lewati" akan melewati (skip) data mangkir tsb.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnOverlapCancel">Batal</button>
                <button type="button" class="btn btn-success" id="btnOverlapSubmit">Konfirmasi dengan Pilihan Ini</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orphanModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg mk-modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff3e0;">
                <h5 class="modal-title" style="color:#b71c1c; font-weight:700;">
                    <i class="la la-exclamation-triangle"></i> Data Orphan (Tidak Ada di Data Baru)
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="mk-orphan-info-box mb-2">
                    <div><strong>Rentang Tanggal:</strong> <span id="orphanRangeInfo">-</span></div>
                    <div class="mt-1">
                        <span class="mk-badge mk-badge-confirmed">Existing: <span id="orphanExistingCount">0</span></span>
                        <span class="mk-badge mk-badge-created">Data Baru: <span id="orphanNewCount">0</span></span>
                        <span class="mk-badge mk-badge-overlap">Hilang (Orphan): <span id="orphanMissingCount">0</span></span>
                    </div>
                </div>

                <div id="orphanEmptyAlert" class="alert alert-success" style="display:none; font-size:.85rem;">
                    <i class="la la-check-circle"></i> Tidak ada data orphan. Semua data existing sudah tercakup di data baru.
                </div>

                <div id="orphanContent" style="display:none;">
                    <p style="font-size:.85rem;">
                        Berikut adalah data mangkir yang <strong>sudah ada</strong> di database
                        (dalam rentang tanggal di atas) tetapi <strong>tidak ditemukan</strong>
                        di data batch baru. Centang baris yang ingin dihapus, atau klik
                        <strong>"Biarkan / Tutup"</strong> untuk membiarkan semua data.
                    </p>
                    <div class="d-flex align-items-center mb-2" style="gap:1rem; flex-wrap: wrap;">
                        <label style="font-weight:600;">
                            <input type="checkbox" id="orphanCheckAll"> Pilih Semua
                        </label>
                        <span class="text-muted" style="font-size:.85rem;">
                            Terpilih: <span id="orphanSelectedCount">0</span> dari <span id="orphanTotalCount">0</span>
                        </span>
                    </div>
                    <div class="mk-orphan-wrapper">
                        <table class="table table-bordered table-sm mk-table">
                            <thead>
                                <tr style="background:#ffe0b2;">
                                    <th style="width:32px;"><input type="checkbox" id="orphanCheckPage" title="Pilih halaman ini saja"></th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Dept</th>
                                    <th>Section</th>
                                    <th>Tgl</th>
                                </tr>
                            </thead>
                            <tbody id="orphanTbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnOrphanKeep">Biarkan / Tutup</button>
                <button type="button" class="btn btn-danger" id="btnOrphanDelete" disabled>
                    <i class="la la-trash"></i> Hapus Data Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    let currentBatchId = null;
    let currentBatchFileName = null;
    let currentPage = 1;
    let historyPage = 1;
    let recCurrentPage = 1;
    let selectedIds = new Set();
    let totalBatchRows = 0;
    let confirmPollTimer = null;
    let overlapStagingIds = [];
    let overlapDecisions = {};

    const KODE_IJIN_MAP = {
        'Cuti':     ['CB', 'CDC1', 'CDC2', 'CDC3', 'CIM', 'CK', 'CKT', 'CH', 'CM', 'CNA', 'CHJ', 'C2', 'C', 'CUT'],
        'Sakit':    ['CHD', 'IM', 'KD', 'S'],
        'Sakit KK': ['SKK'],
        'Mangkir':  ['A'],
    };
    function getKategoriIjin(kode) {
        if (!kode) return '';
        const upper = String(kode).toUpperCase().trim();
        for (const [kategori, kodes] of Object.entries(KODE_IJIN_MAP)) {
            if (kodes.includes(upper)) return kategori;
        }
        return upper;
    }

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, c => (
            { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]
        ));
    }

    function fmtDate(s) {
        if (!s) return '';
        return String(s).substring(0, 10);
    }

    $('#formUpload').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#btnUpload').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-file-mangkir-hrdash/upload') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#formUpload')[0].reset();
                currentBatchId = res.batch_id;
                currentBatchFileName = res.filename;
                loadHistory();
                pollBatch();
                refreshOrphanBatchInfo();
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Gagal upload file.';
                Swal.fire('Error', msg, 'error');
            },
            complete: function () {
                $('#btnUpload').prop('disabled', false).text('Upload & Import');
            }
        });
    });

    function pollBatch() {
        if (!currentBatchId) return;
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/review') }}/" + currentBatchId + "?per_page=1&page=1",
            function (res) {
                let total = res.meta?.total || 0;
                if (total > 0 || res.meta?.filename) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Staging selesai diproses',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    loadHistory();
                    return;
                }
                setTimeout(pollBatch, 2000);
            }
        ).fail(() => setTimeout(pollBatch, 3000));
    }

    function loadHistory() {
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/history') }}", { page: historyPage, per_page: 5 }, function (res) {
            let html = '';
            if (!res.data || res.data.length === 0) {
                html = '<p class="text-muted">Belum ada riwayat import.</p>';
            } else {
                res.data.forEach(b => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center p-2 mb-1"
                             style="border:1px solid #eee; border-radius:4px;">
                            <div>
                                <a href="javascript:;" class="open-review" data-batch="${b.batch_id}" data-filename="${escapeHtml(b.filename)}" style="font-weight:600;">
                                    ${escapeHtml(b.filename)}
                                </a>
                                <div class="mt-1" style="font-size:.8rem;">
                                    <span class="mk-badge mk-badge-created">Data Baru: ${b.created_count}</span>
                                    <span class="mk-badge mk-badge-updated">Update: ${b.updated_count}</span>
                                    <span class="mk-badge mk-badge-confirmed">Terkonfirmasi: ${b.confirmed_count}</span>
                                    <span class="mk-badge mk-badge-pending">Belum dikonfirmasi: ${b.unconfirmed}</span>
                                    ${b.overlap_count > 0 ? `<span class="mk-badge mk-badge-overlap">Overlap: ${b.overlap_count}</span>` : ''}
                                    ${b.deleted_overtime_count > 0 ? `<span class="mk-badge mk-badge-overlap">Lembur dihapus: ${b.deleted_overtime_count}</span>` : ''}
                                    ${b.deleted_mangkir_count > 0 ? `<span class="mk-badge mk-badge-overlap">Mangkir dihapus: ${b.deleted_mangkir_count}</span>` : ''}
                                    ${b.deleted_orphan_count > 0 ? `<span class="mk-badge mk-badge-overlap">Orphan Dihapus: ${b.deleted_orphan_count}</span>` : ''}
                                </div>
                            </div>
                            <div class="text-muted" style="font-size:.85rem;">${escapeHtml(b.created_at || '')}</div>
                        </div>
                    `;
                });
            }
            $('#historyContainer').html(html);
            renderHistoryPagination(res.meta || {});
        });
    }

    function renderHistoryPagination(meta) {
        let total    = meta.total || 0;
        let page     = meta.page || 1;
        let lastPage = meta.last_page || 1;

        if (total === 0 || lastPage <= 1) {
            $('#historyPagination').html('');
            return;
        }

        let html = '<nav><ul class="pagination mk-pagination">';
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="1">&laquo;</a></li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="${page - 1}">&lsaquo;</a></li>`;
        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#historyPagination').html(html);
    }

    $(document).on('click', '#historyPagination .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        historyPage = parseInt($(this).data('page'));
        loadHistory();
    });

    $(document).on('click', '.open-review', function () {
        currentBatchId = $(this).data('batch');
        currentBatchFileName = $(this).data('filename');
        currentPage = 1;
        selectedIds = new Set();
        overlapDecisions = {};
        loadReview();
        $('#reviewModal').modal('show');
        refreshOrphanBatchInfo();
    });

    $('#btnRefreshReview').on('click', function () {
        loadReview();
    });

    function loadReview() {
        if (!currentBatchId) return;
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/review') }}/" + currentBatchId,
            { page: currentPage, per_page: 50 },
            function (res) {
                let meta = res.meta;
                totalBatchRows = meta.total;
                let overlapRows = res.overlap_rows || [];
                let overlapStagingSet = new Set(overlapRows.map(o => o.staging_id));

                $('#reviewHeader').html(`
                    <strong>File:</strong> ${escapeHtml(meta.filename || '-')} |
                    <strong>Data Baru:</strong> <span class="mk-badge mk-badge-created">${meta.created}</span>
                    <strong>Data Update:</strong> <span class="mk-badge mk-badge-updated">${meta.updated}</span>
                    <strong>Total:</strong> ${meta.total}
                    <strong>Sudah dikonfirmasi:</strong> <span class="mk-badge mk-badge-confirmed">${meta.confirmed}</span>
                    <strong>Overlap dengan Lembur:</strong> <span class="mk-badge mk-badge-overlap">${meta.overlap_count || 0}</span>
                `);

                if ((meta.overlap_count || 0) > 0) {
                    $('#reviewOverlapAlert').show();
                    $('#overlapCountText').text(meta.overlap_count);
                } else {
                    $('#reviewOverlapAlert').hide();
                }

                overlapStagingIds = Array.from(overlapStagingSet);

                let rows = '';
                res.data.forEach(r => {
                    let badge = r.status === 'created'
                        ? '<span class="mk-badge mk-badge-created">Created</span>'
                        : (r.status === 'updated'
                            ? '<span class="mk-badge mk-badge-updated">Updated</span>'
                            : '<span class="mk-badge mk-badge-confirmed">Confirmed</span>');
                    let isChecked = selectedIds.has(r.id) ? 'checked' : '';
                    let overlapBadge = overlapStagingSet.has(r.id)
                        ? '<span class="mk-badge mk-badge-overlap" title="NIK + Tgl sama dengan data lembur">⚠ Overlap</span>'
                        : '<span class="text-muted" style="font-size:.75rem;">-</span>';
                    rows += `
                        <tr>
                            <td><input type="checkbox" class="row-check" value="${r.id}" data-nik="${escapeHtml(r.nik)}" ${isChecked}></td>
                            <td>${escapeHtml(r.nik)}</td>
                            <td>${escapeHtml(r.nama || '')}</td>
                            <td>${escapeHtml(r.dept || '')}</td>
                            <td>${escapeHtml(r.section || '')}</td>
                            <td>${fmtDate(r.tgl)}</td>
                            <td>${escapeHtml(r.kode_ijin || '')}</td>
                            <td>${escapeHtml(getKategoriIjin(r.kode_ijin))}</td>
                            <td>${overlapBadge}</td>
                            <td>${badge}</td>
                        </tr>
                    `;
                });
                $('#reviewTbody').html(rows);
                bindCheckboxEvents();
                updateCheckAllState(meta.total);
                updateSelectedCount();
                renderPagination(meta);
            }
        );
    }

    function renderPagination(meta) {
        let page     = meta.page || 1;
        let lastPage = meta.last_page || 1;

        let html = '<nav><ul class="pagination mk-pagination">';
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="1">&laquo;</a></li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="${page - 1}">&lsaquo;</a></li>`;
        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#reviewPagination').html(html);
    }

    $(document).on('click', '#reviewPagination .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        currentPage = parseInt($(this).data('page'));
        loadReview();
    });

    function bindCheckboxEvents() {
        $('#checkAll').off('change.mk');
        $('#checkPage').off('change.mk');
        $(document).off('change.mk', '.row-check');

        $('#checkAll').on('change.mk', function () {
            if (!currentBatchId) return;
            if (this.checked) {
                $.get("{{ url('/hr/upload-file-mangkir-hrdash/review') }}/" + currentBatchId,
                    { page: 1, per_page: 100000 },
                    function (res) {
                        res.data.forEach(r => {
                            selectedIds.add(r.id);
                        });
                        $('.row-check').each(function () {
                            this.checked = true;
                        });
                        $('#checkPage').prop('checked', true);
                        updateSelectedCount();
                    }
                );
            } else {
                selectedIds.clear();
                $('.row-check').each(function () {
                    this.checked = false;
                });
                $('#checkPage').prop('checked', false);
                updateSelectedCount();
            }
        });

        $('#checkPage').on('change.mk', function () {
            $('.row-check').each(function () {
                let id = parseInt(this.value);
                this.checked = $('#checkPage').prop('checked');
                if (this.checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
            });
            updateCheckAllState(totalBatchRows);
            updateSelectedCount();
        });

        $(document).on('change.mk', '.row-check', function () {
            let id = parseInt(this.value);
            if (this.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
            updateCheckAllState(totalBatchRows);
            updateSelectedCount();
        });
    }

    function updateCheckAllState(total) {
        $('#checkAll').prop('checked', selectedIds.size >= total && total > 0);
        let pageRows = $('.row-check');
        if (pageRows.length === 0) {
            $('#checkPage').prop('checked', false);
            return;
        }
        let allChecked = pageRows.toArray().every(el => selectedIds.has(parseInt(el.value)));
        $('#checkPage').prop('checked', allChecked);
    }

    function updateSelectedCount() {
        $('#selectedCount').text(selectedIds.size);
    }

    function stopConfirmPolling() {
        if (confirmPollTimer) {
            clearInterval(confirmPollTimer);
            confirmPollTimer = null;
        }
    }

    function pollConfirmStatus() {
        if (!currentBatchId) return;
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/confirm-status') }}/" + currentBatchId,
            function (res) {
                let status = res.confirm_status;
                let processed = res.confirm_processed;
                if (status === 'done') {
                    stopConfirmPolling();
                    let msg = `Konfirmasi selesai. ${processed} data telah diproses.`;
                    if (res.deleted_overtime_count > 0) {
                        msg += ` ${res.deleted_overtime_count} data lembur dihapus karena overlap.`;
                    }
                    if (res.deleted_mangkir_count > 0) {
                        msg += ` ${res.deleted_mangkir_count} data mangkir dihapus (izin prioritas).`;
                    }
                    Swal.fire('Berhasil', msg, 'success');
                    selectedIds.clear();
                    overlapDecisions = {};
                    $('#overlapModal').modal('hide');
                    loadReview();
                    loadHistory();
                    loadRecords();
                } else if (status === 'failed') {
                    stopConfirmPolling();
                    Swal.fire('Error', 'Konfirmasi gagal: ' + (res.confirm_error || 'unknown error'), 'error');
                    loadReview();
                    loadHistory();
                }
            }
        ).fail(() => { /* keep polling */ });
    }

    $('#btnConfirm').on('click', function () {
        let ids = Array.from(selectedIds);
        if (ids.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal 1 data.', 'warning');
            return;
        }

        let overlapSelected = overlapStagingIds.filter(id => selectedIds.has(id));

        if (overlapSelected.length > 0) {
            showOverlapModal(overlapSelected);
        } else {
            submitConfirm(ids, {});
        }
    });

    function showOverlapModal(overlapSelectedIds) {
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/review') }}/" + currentBatchId,
            { page: 1, per_page: 100000 },
            function (res) {
                let overlapRows = (res.overlap_rows || []).filter(o => overlapSelectedIds.includes(o.staging_id));
                let html = '';
                overlapRows.forEach(o => {
                    let sid = o.staging_id;
                    if (!overlapDecisions[sid]) {
                        overlapDecisions[sid] = 'keep_izin';
                    }
                    let def = overlapDecisions[sid];
                    html += `
                        <tr data-staging-id="${sid}">
                            <td>${escapeHtml(o.nik)}</td>
                            <td>${escapeHtml(o.nama || '')}</td>
                            <td>${fmtDate(o.tgl)}</td>
                            <td>${escapeHtml(o.overtime?.no_spkl || '')}</td>
                            <td>
                                <label style="display:block; font-size:.8rem; margin-bottom:.2rem;">
                                    <input type="radio" name="overlap-decision-${sid}" value="keep_izin" ${def === 'keep_izin' ? 'checked' : ''}>
                                    <span class="text-success" style="font-weight:600;">Pakai Mangkir</span>
                                    <span class="text-muted">(hapus lembur)</span>
                                </label>
                                <label style="display:block; font-size:.8rem;">
                                    <input type="radio" name="overlap-decision-${sid}" value="keep_lembur" ${def === 'keep_lembur' ? 'checked' : ''}>
                                    <span class="text-danger" style="font-weight:600;">Pakai Lembur</span>
                                    <span class="text-muted">(skip mangkir)</span>
                                </label>
                            </td>
                        </tr>
                    `;
                });
                $('#overlapTbody').html(html);
                $('#overlapModalCount').text(overlapRows.length);
                $('#overlapModal').modal('show');

                $('#overlapTbody input[type=radio]').on('change', function () {
                    let tr = $(this).closest('tr');
                    let sid = parseInt(tr.data('staging-id'));
                    overlapDecisions[sid] = $(this).val();
                });
            }
        );
    }

    $('#btnOverlapSubmit').on('click', function () {
        let ids = Array.from(selectedIds);
        submitConfirm(ids, overlapDecisions);
    });

    function submitConfirm(ids, decisions) {
        Swal.fire({
            title: 'Konfirmasi ' + ids.length + ' data mangkir?',
            text: 'Data akan diproses oleh queue worker.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Konfirmasi',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-mangkir-hrdash/confirm') }}/" + currentBatchId,
                type: 'POST',
                data: { ids: ids, decisions: decisions, _token: csrf() },
                success: function (res) {
                    Swal.fire({
                        title: 'Diproses',
                        text: res.message,
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    stopConfirmPolling();
                    confirmPollTimer = setInterval(pollConfirmStatus, 2000);
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Gagal konfirmasi data.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    }

    function loadRecords() {
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/records') }}",
            {
                search:   $('#searchInput').val(),
                page:     recCurrentPage,
                per_page: $('#recPerPage').val()
            },
            function (res) {
                let rows = '';
                if (!res.data || res.data.length === 0) {
                    rows = '<tr><td colspan="12" class="text-center text-muted">Belum ada data.</td></tr>';
                } else {
                    res.data.forEach(r => {
                        rows += `
                            <tr>
                                <td>${escapeHtml(r.nik)}</td>
                                <td>${escapeHtml(r.nama || '')}</td>
                                <td>${escapeHtml(r.dept || '')}</td>
                                <td>${escapeHtml(r.sub_departmen || '')}</td>
                                <td>${escapeHtml(r.section || '')}</td>
                                <td>${fmtDate(r.tgl)}</td>
                                <td>${escapeHtml(r.no_spi || '')}</td>
                                <td>${escapeHtml(r.kode_ijin || '')}</td>
                                <td>${escapeHtml(getKategoriIjin(r.kode_ijin))}</td>
                                <td>${escapeHtml(r.keterangan || '')}</td>
                                <td>${escapeHtml(r.updated_by_name || r.send_by_username || '')}</td>
                                <td>${r.updated_at ? new Date(r.updated_at).toISOString().slice(0, 19).replace('T', ' ') : ''}</td>
                            </tr>
                        `;
                    });
                }
                $('#recordTbody').html(rows);
                renderRecPagination(res.meta || {});
            }
        );
    }

    function renderRecPagination(meta) {
        let total    = meta.total || 0;
        let page     = meta.page || 1;
        let lastPage = meta.last_page || 1;
        let perPage  = meta.per_page || 25;

        if (total === 0) {
            $('#recInfo').text('Menampilkan 0 dari 0 data');
            $('#recPagination').html('');
            return;
        }

        let from = (page - 1) * perPage + 1;
        let to   = Math.min(page * perPage, total);
        $('#recInfo').text(`Menampilkan ${from}\u2013${to} dari ${total} data`);

        let html = '<nav><ul class="pagination mk-pagination">';
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="1">&laquo;</a></li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="${page - 1}">&lsaquo;</a></li>`;
        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#recPagination').html(html);
    }

    $(document).on('click', '#recPagination .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        recCurrentPage = parseInt($(this).data('page'));
        loadRecords();
    });

    $('#recPerPage').on('change', function () {
        recCurrentPage = 1;
        loadRecords();
    });

    $('#formSearch').on('submit', function (e) {
        e.preventDefault();
        recCurrentPage = 1;
        loadRecords();
    });

    loadHistory();
    loadRecords();

    setInterval(function() { loadHistory(); }, 30000);

    $('#reviewModal').on('hidden.bs.modal', function () {
        stopConfirmPolling();
    });

    // === ORPHAN CHECK ===
    const orphanDateRange = flatpickr('#orphanDateRange', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });
    let orphanCurrentData = [];
    let orphanSelectedKeys = new Set();
    let orphanTglFrom = null;
    let orphanTglTo = null;
    let orphanTipe = null;

    function parseOrphanRange(val) {
        if (!val) return null;
        const parts = val.split(/\s+to\s+|\s+-\s+/);
        if (parts.length === 2) {
            return { from: parts[0].trim(), to: parts[1].trim() };
        }
        if (parts.length === 1 && val.trim()) {
            return { from: val.trim(), to: val.trim() };
        }
        return null;
    }

    function refreshOrphanBatchInfo() {
        if (currentBatchId) {
            const fname = currentBatchFileName ? escapeHtml(currentBatchFileName) : '-';
            $('#orphanBatchInfo').html('File: ' + fname);
        } else {
            $('#orphanBatchInfo').html('Batch aktif: <strong>-</strong> <span class="text-muted">(upload file dulu)</span>');
        }
        refreshOrphanButtonState();
    }

    function refreshOrphanButtonState() {
        const range = parseOrphanRange($('#orphanDateRange').val());
        const tipe = $('#orphanTipe').val();
        const canCheck = currentBatchId && range && range.from && range.to && tipe;
        $('#btnCheckOrphans').prop('disabled', !canCheck);
    }

    $('#orphanDateRange').on('change', function () {
        refreshOrphanButtonState();
    });

    $('#orphanTipe').on('change', function () {
        refreshOrphanButtonState();
    });

    $('#btnResetOrphans').on('click', function () {
        if (orphanDateRange && orphanDateRange.clear) {
            orphanDateRange.clear();
        }
        $('#orphanTipe').val('');
        orphanTipe = null;
        refreshOrphanButtonState();
    });

    $('#btnCheckOrphans').on('click', function () {
        if (!currentBatchId) {
            Swal.fire('Peringatan', 'Tidak ada batch aktif. Upload file terlebih dahulu.', 'warning');
            return;
        }
        const range = parseOrphanRange($('#orphanDateRange').val());
        if (!range || !range.from || !range.to) {
            Swal.fire('Peringatan', 'Pilih rentang tanggal terlebih dahulu.', 'warning');
            return;
        }
        const tipe = $('#orphanTipe').val();
        if (!tipe) {
            Swal.fire('Peringatan', 'Pilih Tipe Karyawan (Staff / Non Staff) terlebih dahulu.', 'warning');
            return;
        }
        orphanTglFrom = range.from;
        orphanTglTo   = range.to;
        orphanTipe    = tipe;

        $('#btnCheckOrphans').prop('disabled', true).html('<i class="la la-spinner la-spin"></i> Memuat...');
        $.get("{{ url('/hr/upload-file-mangkir-hrdash/check-orphans') }}/" + currentBatchId,
            { tgl_from: orphanTglFrom, tgl_to: orphanTglTo, tipe: orphanTipe },
            function (res) {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Gagal mengecek data.', 'error');
                    return;
                }
                showOrphanModal(res);
            }
        ).fail(function (xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal mengecek data orphan.';
            Swal.fire('Error', msg, 'error');
        }).always(function () {
            $('#btnCheckOrphans').prop('disabled', false).html('<i class="la la-search"></i> Cek Data');
            refreshOrphanButtonState();
        });
    });

    function showOrphanModal(res) {
        orphanCurrentData  = res.missing || [];
        orphanSelectedKeys = new Set();
        orphanTglFrom      = res.tgl_from;
        orphanTglTo        = res.tgl_to;

        $('#orphanRangeInfo').text(orphanTglFrom + ' s/d ' + orphanTglTo + ' (' + orphanTipe + ')');
        $('#orphanExistingCount').text(res.existing_total);
        $('#orphanNewCount').text(res.new_total);
        $('#orphanMissingCount').text(res.missing_count);
        $('#orphanTotalCount').text(orphanCurrentData.length);
        $('#orphanSelectedCount').text(0);

        if (orphanCurrentData.length === 0) {
            $('#orphanContent').hide();
            $('#orphanEmptyAlert').show();
            $('#btnOrphanDelete').prop('disabled', true);
        } else {
            $('#orphanContent').show();
            $('#orphanEmptyAlert').hide();
            renderOrphanTbody();
            $('#btnOrphanDelete').prop('disabled', false);
        }

        $('#orphanCheckAll').prop('checked', false);
        $('#orphanCheckPage').prop('checked', false);
        $('#orphanModal').modal('show');
    }

    function renderOrphanTbody() {
        let rows = '';
        orphanCurrentData.forEach((r, idx) => {
            const key = r.nik + '|' + r.tgl;
            const checked = orphanSelectedKeys.has(key) ? 'checked' : '';
            rows += `
                <tr>
                    <td><input type="checkbox" class="orphan-row-check" data-key="${escapeHtml(key)}" ${checked}></td>
                    <td>${escapeHtml(r.nik)}</td>
                    <td>${escapeHtml(r.nama || '')}</td>
                    <td>${escapeHtml(r.dept || '')}</td>
                    <td>${escapeHtml(r.section || '')}</td>
                    <td>${fmtDate(r.tgl)}</td>
                </tr>
            `;
        });
        $('#orphanTbody').html(rows);
    }

    $(document).on('change', '.orphan-row-check', function () {
        const key = $(this).data('key');
        if (this.checked) {
            orphanSelectedKeys.add(key);
        } else {
            orphanSelectedKeys.delete(key);
        }
        updateOrphanSelectedCount();
        updateOrphanPageAllState();
    });

    $('#orphanCheckPage').on('change', function () {
        $('.orphan-row-check').each(function () {
            const key = $(this).data('key');
            this.checked = $('#orphanCheckPage').prop('checked');
            if (this.checked) {
                orphanSelectedKeys.add(key);
            } else {
                orphanSelectedKeys.delete(key);
            }
        });
        updateOrphanSelectedCount();
        updateOrphanCheckAllState();
    });

    $('#orphanCheckAll').on('change', function () {
        if (this.checked) {
            orphanCurrentData.forEach(r => {
                orphanSelectedKeys.add(r.nik + '|' + r.tgl);
            });
            $('.orphan-row-check').prop('checked', true);
            $('#orphanCheckPage').prop('checked', true);
        } else {
            orphanSelectedKeys.clear();
            $('.orphan-row-check').prop('checked', false);
            $('#orphanCheckPage').prop('checked', false);
        }
        updateOrphanSelectedCount();
    });

    function updateOrphanSelectedCount() {
        $('#orphanSelectedCount').text(orphanSelectedKeys.size);
        $('#btnOrphanDelete').prop('disabled', orphanSelectedKeys.size === 0);
    }

    function updateOrphanCheckAllState() {
        const total = orphanCurrentData.length;
        $('#orphanCheckAll').prop('checked', total > 0 && orphanSelectedKeys.size >= total);
    }

    function updateOrphanPageAllState() {
        const all = $('.orphan-row-check');
        if (all.length === 0) {
            $('#orphanCheckPage').prop('checked', false);
            return;
        }
        const allChecked = all.toArray().every(el => $(el).prop('checked'));
        $('#orphanCheckPage').prop('checked', allChecked);
    }

    $('#btnOrphanDelete').on('click', function () {
        if (orphanSelectedKeys.size === 0) {
            Swal.fire('Peringatan', 'Pilih minimal 1 data untuk dihapus.', 'warning');
            return;
        }
        const orphans = [];
        orphanSelectedKeys.forEach(key => {
            const [nik, tgl] = key.split('|');
            orphans.push({ nik: nik, tgl: tgl });
        });

        Swal.fire({
            title: 'Hapus ' + orphans.length + ' data orphan?',
            text: 'Data yang dipilih akan dihapus permanen dari database hr_izin.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
        }).then((r) => {
            if (!r.isConfirmed) return;

            $('#btnOrphanDelete').prop('disabled', true).html('<i class="la la-spinner la-spin"></i> Menghapus...');
            $.ajax({
                url: "{{ url('/hr/upload-file-mangkir-hrdash/delete-orphans') }}/" + currentBatchId,
                type: 'POST',
                data: { orphans: orphans, _token: csrf() },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        orphanSelectedKeys.clear();
                        loadRecords();
                        loadHistory();
                        if ($('#reviewModal').hasClass('show')) {
                            loadReview();
                        }
                        $('#orphanModal').modal('hide');
                    } else {
                        Swal.fire('Error', res.message || 'Gagal menghapus data.', 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Gagal menghapus data orphan.';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    $('#btnOrphanDelete').prop('disabled', false).html('<i class="la la-trash"></i> Hapus Data Terpilih');
                }
            });
        });
    });

    refreshOrphanBatchInfo();
</script>
@endpush
