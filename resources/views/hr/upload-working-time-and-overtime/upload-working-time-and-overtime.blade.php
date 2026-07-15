@extends('layouts.base')

@section('content')

<style>
    .wt-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .wt-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .wt-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .wt-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .wt-badge-plan { background:#ffe0b2; color:#e65100; }
    .wt-badge-created { background:#c8e6c9; color:#1b5e20; }
    .wt-badge-updated { background:#fff9c4; color:#827717; }
    .wt-badge-pending { background:#ffe0b2; color:#e65100; }
    .wt-badge-confirmed { background:#bbdefb; color:#0d47a1; }
    .wt-tab-btn { border:1px solid #ddd; background:#fff; padding:.4rem 1rem; font-weight:600; }
    .wt-tab-btn.active { background:#4a148c; color:#fff; border-color:#4a148c; }
    .wt-modal-lg .modal-dialog { max-width: 95%; margin: 1.75rem auto; }
    .wt-table th { font-size:.85rem; }
    .wt-table td { font-size:.85rem; vertical-align: middle; }
    .wt-pagination .page-link { padding:.2rem .5rem; font-size:.85rem; }
    .wt-review-wrapper { max-height: 500px; overflow: auto; border: 1px solid #dee2e6; border-radius: 4px; }
    .wt-review-wrapper table { margin-bottom: 0; }
    .wt-review-wrapper thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
    .wt-review-wrapper td, .wt-review-wrapper th { white-space: nowrap; }
    .wt-confirm-progress { display:none; margin-top:.5rem; font-size:.85rem; color:#4a148c; }
</style>

<div class="container-fluid">
    <h1 class="wt-page-title">Upload Working Time &amp; Overtime</h1>

    @if(!empty($isMitraKerja) && $isMitraKerja)
        <div class="alert alert-warning" style="font-size:.85rem; padding:.5rem .75rem;">
            <i class="fas fa-info-circle"></i>
            <strong>Mode Mitra Kerja aktif.</strong>
            Gunakan template CSV compact dengan kolom: NIK, Nama, Company, Dept, Section, Tgl In, Jam SPKL, Jam HOVT, No SPKL.
        </div>
    @endif

    {{-- IMPORT SECTION --}}
    <div class="wt-card">
        <h5>Import Working Time &amp; Overtime CSV</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            <strong class="text-danger">Maksimum ukuran file: 128MB.</strong> Jika file lebih besar, pecah menjadi beberapa file atau hubungi admin untuk menaikkan batas upload.
        </p>

        <form id="formUpload" enctype="multipart/form-data">
            @csrf
            @if(!empty($isMitraKerja) && $isMitraKerja)
                <input type="hidden" name="type_karyawan" value="mitra_kerja">
            @endif
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

        @if(!empty($isMitraKerja) && $isMitraKerja)
            <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
                <strong>Template CSV Mitra Kerja:</strong><br>
                <strong>Baris 1 (Judul):</strong> Judul bebas<br>
                <strong>Baris 2 (Header):</strong> NIK, Nama, Company, Dept, Section, Tgl In, Jam SPKL, Jam HOVT, No SPKL<br>
                <strong>Baris 3+ (Value):</strong> isi data sesuai header. Kolom <em>No SPKL</em> boleh kosong (akan diisi <code>null</code>).
            </div>
        @else
            <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
                <strong>Kolom CSV (baris 1):</strong>
                NIK, Nama, Company, Dept, Section, Business Area, Tgl In, Jam In, Tgl Out, Jam Out, Target Kerja, Terlambat, Pulang Awal, Durasi Raw, Durasi Round, Durasi Dibayar, Lembur Awal Actual, Lembur Akhir Actual, Total Lembur Actual, Tipe Lembur, Jam SPKL, Jam HOVT, HOVT Actual, HOVT Dibayar, No SPKL
            </div>
        @endif
    </div>

    {{-- RIWAYAT IMPORT --}}
    <div class="wt-card">
        <h5>Riwayat Import</h5>
        <div id="historyContainer">
            <p class="text-muted">Memuat...</p>
        </div>
        <div class="d-flex justify-content-center mt-2" id="historyPagination"></div>
    </div>

    {{-- RECORDS LIST --}}
    <div class="wt-card">
        <h5>Working Time &amp; Overtime Records</h5>

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
            <table class="table table-bordered wt-table">
                <thead class="thead-light">
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Dept</th>
                        <th>Sub Departmen</th>
                        <th>Section</th>
                        <th>Tgl In</th>
                        <th>Jam SPKL</th>
                        <th>Jam HOVT</th>
                        <th>No SPKL</th>
                        <th>Update Oleh</th>
                        <th>Update Pada</th>
                    </tr>
                </thead>
                <tbody id="recordTbody">
                    <tr><td colspan="11" class="text-center text-muted">Belum ada data.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2" id="recPagination"></div>
    </div>
</div>

{{-- REVIEW MODAL --}}
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg wt-modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Hasil Import - Review Data</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="reviewHeader" class="mb-3" style="font-size:.85rem;"></div>
                <div class="d-flex align-items-center mb-2" style="gap:1rem; flex-wrap: wrap;">
                    <label style="font-weight:600;">
                        <input type="checkbox" id="checkAll"> Pilih Semua Data
                    </label>
                    <span class="text-muted" style="font-size:.85rem;">Terpilih: <span id="selectedCount">0</span></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-auto" id="btnRefreshReview">
                        <i class="la la-refresh"></i> Refresh
                    </button>
                </div>
                <div class="wt-review-wrapper">
                    <table class="table table-bordered table-sm wt-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:32px;"><input type="checkbox" id="checkPage" title="Pilih halaman ini saja"></th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Dept</th>
                                <th>Section</th>
                                <th>Tgl In</th>
                                <th>Jam SPKL</th>
                                <th>Jam HOVT</th>
                                <th>No SPKL</th>
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

@endsection

@push('scripts')
<script>
    let currentBatchId = null;
    let currentPage = 1;
    let historyPage = 1;
    let recCurrentPage = 1;
    let selectedIds = new Set();
    let totalBatchRows = 0;
    let confirmPollTimer = null;

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

    // === Upload ===
    $('#formUpload').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#btnUpload').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-working-time-and-overtime/upload') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#formUpload')[0].reset();
                currentBatchId = res.batch_id;
                loadHistory();
                pollBatch();
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
        $.get("{{ url('/hr/upload-working-time-and-overtime/review') }}/" + currentBatchId + "?per_page=1&page=1",
            function (res) {
                let total = res.meta?.total || 0;
                if (total > 0) {
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

    // === History ===
    function loadHistory() {
        $.get("{{ url('/hr/upload-working-time-and-overtime/history') }}", { page: historyPage, per_page: 5 }, function (res) {
            let html = '';
            if (!res.data || res.data.length === 0) {
                html = '<p class="text-muted">Belum ada riwayat import.</p>';
            } else {
                res.data.forEach(b => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center p-2 mb-1"
                             style="border:1px solid #eee; border-radius:4px;">
                            <div>
                                <a href="javascript:;" class="open-review" data-batch="${b.batch_id}" style="font-weight:600;">
                                    ${escapeHtml(b.filename)}
                                </a>
                                <div class="mt-1" style="font-size:.8rem;">
                                    <span class="wt-badge wt-badge-created">Data Baru: ${b.created_count}</span>
                                    <span class="wt-badge wt-badge-updated">Update: ${b.updated_count}</span>
                                    <span class="wt-badge wt-badge-confirmed">Terkonfirmasi: ${b.confirmed_count}</span>
                                    <span class="wt-badge wt-badge-pending">Belum dikonfirmasi: ${b.unconfirmed}</span>
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

        let html = '<nav><ul class="pagination wt-pagination">';
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
        currentPage = 1;
        selectedIds = new Set();
        loadReview();
        $('#reviewModal').modal('show');
    });

    $('#btnRefreshReview').on('click', function () {
        loadReview();
    });

    // === Review ===
    function loadReview() {
        if (!currentBatchId) return;
        $.get("{{ url('/hr/upload-working-time-and-overtime/review') }}/" + currentBatchId,
            { page: currentPage, per_page: 50 },
            function (res) {
                let meta = res.meta;
                totalBatchRows = meta.total;
                $('#reviewHeader').html(`
                    <strong>File:</strong> ${escapeHtml(meta.filename || '-')} |
                    <strong>Data Baru:</strong> <span class="wt-badge wt-badge-created">${meta.created}</span>
                    <strong>Data Update:</strong> <span class="wt-badge wt-badge-updated">${meta.updated}</span>
                    <strong>Total:</strong> ${meta.total}
                    <strong>Sudah dikonfirmasi:</strong> <span class="wt-badge wt-badge-confirmed">${meta.confirmed}</span>
                `);

                let rows = '';
                res.data.forEach(r => {
                    let badge = r.status === 'created'
                        ? '<span class="wt-badge wt-badge-created">Created</span>'
                        : (r.status === 'updated'
                            ? '<span class="wt-badge wt-badge-updated">Updated</span>'
                            : '<span class="wt-badge wt-badge-confirmed">Confirmed</span>');
                    let isChecked = selectedIds.has(r.id) ? 'checked' : '';
                    rows += `
                        <tr>
                            <td><input type="checkbox" class="row-check" value="${r.id}" data-nik="${escapeHtml(r.nik)}" ${isChecked}></td>
                            <td>${escapeHtml(r.nik)}</td>
                            <td>${escapeHtml(r.nama || '')}</td>
                            <td>${escapeHtml(r.dept || '')}</td>
                            <td>${escapeHtml(r.section || '')}</td>
                            <td>${fmtDate(r.tgl_in)}</td>
                            <td>${r.jam_spkl ?? ''}</td>
                            <td>${r.jam_hovt ?? ''}</td>
                            <td>${escapeHtml(r.no_spkl || '')}</td>
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

        let html = '<nav><ul class="pagination wt-pagination">';
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
        $('#checkAll').off('change.wt');
        $('#checkPage').off('change.wt');
        $(document).off('change.wt', '.row-check');

        $('#checkAll').on('change.wt', function () {
            if (!currentBatchId) return;
            if (this.checked) {
                $.get("{{ url('/hr/upload-working-time-and-overtime/review') }}/" + currentBatchId,
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

        $('#checkPage').on('change.wt', function () {
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

        $(document).on('change.wt', '.row-check', function () {
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
        $.get("{{ url('/hr/upload-working-time-and-overtime/confirm-status') }}/" + currentBatchId,
            function (res) {
                let status = res.confirm_status;
                let processed = res.confirm_processed;
                let total = res.confirm_total;
                if (status === 'done') {
                    stopConfirmPolling();
                    Swal.fire('Berhasil', `Konfirmasi selesai. ${processed} data telah dipindahkan ke tabel utama.`, 'success');
                    selectedIds.clear();
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
        Swal.fire({
            title: 'Konfirmasi ' + ids.length + ' data?',
            text: 'Data akan dipindahkan ke tabel utama oleh queue worker.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Konfirmasi',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/upload-working-time-and-overtime/confirm') }}/" + currentBatchId,
                type: 'POST',
                data: { ids: ids, _token: csrf() },
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
    });

    // === Records ===
    function loadRecords() {
        $.get("{{ url('/hr/upload-working-time-and-overtime/records') }}",
            {
                search:   $('#searchInput').val(),
                page:     recCurrentPage,
                per_page: $('#recPerPage').val()
            },
            function (res) {
                let rows = '';
                if (!res.data || res.data.length === 0) {
                    rows = '<tr><td colspan="11" class="text-center text-muted">Belum ada data.</td></tr>';
                } else {
                    res.data.forEach(r => {
                        rows += `
                            <tr>
                                <td>${escapeHtml(r.nik)}</td>
                                <td>${escapeHtml(r.nama || '')}</td>
                                <td>${escapeHtml(r.dept || '')}</td>
                                <td>${escapeHtml(r.sub_departmen || '')}</td>
                                <td>${escapeHtml(r.section || '')}</td>
                                <td>${fmtDate(r.tgl_in)}</td>
                                <td>${r.jam_spkl ?? ''}</td>
                                <td>${r.jam_hovt ?? ''}</td>
                                <td>${escapeHtml(r.no_spkl || '')}</td>
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
        $('#recInfo').text(`Menampilkan ${from}–${to} dari ${total} data`);

        let html = '<nav><ul class="pagination wt-pagination">';
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

    // initial load
    loadHistory();
    loadRecords();

    // auto refresh history every 30s
    setInterval(function() { loadHistory(); }, 30000);

    // cleanup polling saat modal ditutup
    $('#reviewModal').on('hidden.bs.modal', function () {
        stopConfirmPolling();
    });
</script>
@endpush
