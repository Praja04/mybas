@extends('layouts.base')

@section('content')

<style>
    .em-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .em-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .em-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .em-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .em-badge-plan { background:#ffe0b2; color:#e65100; }
    .em-badge-created { background:#c8e6c9; color:#1b5e20; }
    .em-badge-updated { background:#fff9c4; color:#827717; }
    .em-badge-pending { background:#ffe0b2; color:#e65100; }
    .em-tab-btn { border:1px solid #ddd; background:#fff; padding:.4rem 1rem; font-weight:600; }
    .em-tab-btn.active { background:#4a148c; color:#fff; border-color:#4a148c; }
    .em-modal-lg .modal-dialog { max-width: 95%; margin: 1.75rem auto; }
    .em-table th { font-size:.85rem; }
    .em-table td { font-size:.85rem; vertical-align: middle; }
    .em-pagination .page-link { padding:.2rem .5rem; font-size:.85rem; }
    .review-table-wrapper { max-height: 500px; overflow: auto; border: 1px solid #dee2e6; border-radius: 4px; }
    .review-table-wrapper table { margin-bottom: 0; }
    .review-table-wrapper thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
    .review-table-wrapper td, .review-table-wrapper th { white-space: nowrap; }
</style>

<div class="container-fluid">
    <h1 class="em-page-title">Employee Management</h1>

    {{-- IMPORT SECTION --}}
    <div class="em-card">
        <h5>Import Employee CSV</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
                Tanggal (Tgl Lahir, Tgl Masuk, Valid From) akan dinormalisasi otomatis.<br>
            <strong class="text-danger">Maksimum ukuran file: 128MB.</strong> Jika file lebih besar, pecah menjadi beberapa file atau hubungi admin untuk menaikkan batas upload.
        </p>

        <form id="formUpload" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;">File CSV:</label>
                    <input type="file" name="file" id="fileInput" class="form-control-file" accept=".csv,.xls,.xlsx,.txt" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success" id="btnUpload">
                        Upload &amp; Import
                    </button>
                </div>
            </div>
        </form>
        <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
            <strong>Kolom CSV (baris 1):</strong>
            Judul
        </div>
        <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
            <strong>Kolom CSV (baris 2):</strong>
            Company, NIK, Nama, Tempat Lahir, Tgl Lahir, Tgl Masuk, Divisi, Bus Area, Sales Office, Departmen, Section,
            Tipe Karyawan, Jabatan, Group, Sub Group, Level, Payroll Type, Jenis Kelamin, Alamat KTP, Jumlah Anak, Work Status, Status Nikah, Aktif, Valid From, Valid To, View
            <br><span class="text-muted"><strong>Catatan:</strong> Kolom <em>Sub Departmen</em> tidak ada di template, tapi akan di-resolve otomatis dari <em>Section</em> via mapping di <code>HrEmployeeNormalizer::SECTION_TO_SUB_DEPT</code>. Departmen dipakai langsung dari CSV tanpa normalisasi PT.</span>
        </div>
        <div class="mt-3 p-2" style="background:#f5f5f5; font-size:.8rem;">
            <strong>Kolom CSV (baris 3):</strong>
            isi / value
        </div>

    {{-- RIWAYAT IMPORT --}}
    <div class="em-card">
        <h5>Riwayat Import</h5>
        <div id="historyContainer">
            <p class="text-muted">Memuat...</p>
        </div>
        <div class="d-flex justify-content-center mt-2" id="historyPagination"></div>
    </div>

    {{-- EMPLOYEE LIST --}}
    <div class="em-card">
        <h5>Employee List</h5>

        <form id="formSearch" class="d-flex mb-3" style="gap:.5rem;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Berdasarkan NIK...">
            <button type="submit" class="btn btn-primary">Cari Data</button>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.85rem;">
            <div>
                <span class="text-muted">Tampilkan</span>
                <select id="empPerPage" class="form-control form-control-sm d-inline-block" style="width:auto;">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-muted">data per halaman</span>
            </div>
            <div class="text-muted" id="empInfo"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered em-table">
                <thead class="thead-light">
                    <tr>
                        <th>NIK</th>
                        <th>Tipe</th>
                        <th>Nama</th>
                        <th>Tgl Lahir</th>
                        <th>Tgl Masuk</th>
                        <th>Departmen</th>
                        <th>Sub Departmen</th>
                        <th>Section</th>
                        <th>Jabatan</th>
                        <th>Jenis Kelamin</th>
                        <th>Work Status</th>
                        <th>Status Nikah</th>
                        <th>Aktif</th>
                        <th>Valid From</th>
                        <th>Update Oleh</th>
                        <th>Update Pada</th>
                    </tr>
                </thead>
                <tbody id="employeeTbody">
                    <tr><td colspan="16" class="text-center text-muted">Belum ada data employee untuk unit ini.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-2" id="empPagination"></div>
    </div>
</div>

{{-- REVIEW MODAL --}}
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg em-modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Hasil Import - Review Data</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="reviewHeader" class="mb-3" style="font-size:.85rem;"></div>
                <div class="d-flex align-items-center mb-2" style="gap:1rem;">
                    <label style="font-weight:600;">
                        <input type="checkbox" id="checkAll"> Pilih Semua Data
                    </label>
                    <span class="text-muted" style="font-size:.85rem;">Terpilih: <span id="selectedCount">0</span></span>
                </div>
                <div class="review-table-wrapper">
                    <table class="table table-bordered table-sm em-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:32px;"><input type="checkbox" id="checkPage" title="Pilih halaman ini saja"></th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tgl Lahir</th>
                                <th>Tgl Masuk</th>
                                <th>Departmen</th>
                                <th>Sub Departmen</th>
                                <th>Section</th>
                                <th>Tipe Karyawan</th>
                                <th>Jabatan</th>
                                <th>Jenis Kelamin</th>
                                <th>Work Status</th>
                                <th>Status Nikah</th>
                                <th>Aktif</th>
                                <th>Valid From</th>
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
    let empCurrentPage = 1;
    let selectedNiks = new Set();
    let currentPageNiks = new Set();
    let totalBatchRows = 0;

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, c => (
            { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]
        ));
    }

    // === Upload ===
    $('#formUpload').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#btnUpload').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-file-mp/upload') }}",
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
        $.get("{{ url('/hr/upload-file-mp/review') }}/" + currentBatchId + "?per_page=1&page=1",
            function (res) {
                let total = res.meta?.total || 0;
                if (total > 0) {
                    loadHistory();
                    return;
                }
                setTimeout(pollBatch, 2000);
            }
        ).fail(() => setTimeout(pollBatch, 3000));
    }

    // === History ===
    function loadHistory() {
        $.get("{{ url('/hr/upload-file-mp/history') }}", { page: historyPage, per_page: 5 }, function (res) {
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
                                    <span class="em-badge em-badge-created">Data Baru: ${b.created_count}</span>
                                    <span class="em-badge em-badge-updated">Update: ${b.updated_count}</span>
                                    <span class="em-badge em-badge-pending">Belum dikonfirmasi: ${b.unconfirmed}</span>
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
        let perPage  = meta.per_page || 5;

        if (total === 0 || lastPage <= 1) {
            $('#historyPagination').html('');
            return;
        }

        let html = '<nav><ul class="pagination em-pagination">';

        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="1">&laquo;</a>
                </li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page - 1}">&lsaquo;</a>
                </li>`;

        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}">
                        <a class="page-link" href="javascript:;" data-page="${i}">${i}</a>
                    </li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="${lastPage}">${lastPage}</a></li>`;
        }

        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page + 1}">&rsaquo;</a>
                </li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${lastPage}">&raquo;</a>
                </li>`;
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
        selectedNiks = new Set();
        loadReview();
        $('#reviewModal').modal('show');
    });

    // === Review ===
    function loadReview() {
        if (!currentBatchId) return;
        $.get("{{ url('/hr/upload-file-mp/review') }}/" + currentBatchId,
            { page: currentPage, per_page: 50 },
            function (res) {
                let meta = res.meta;
                totalBatchRows = meta.total;
                $('#reviewHeader').html(`
                    <strong>File:</strong> ${escapeHtml(meta.filename || '-')} |
                    <strong>Data Baru (Created):</strong> <span class="em-badge em-badge-created">${meta.created}</span>
                    <strong>Data Update (Updated):</strong> <span class="em-badge em-badge-updated">${meta.updated}</span>
                    <strong>Total:</strong> ${meta.total}
                    <strong>Terkonfirmasi:</strong> ${meta.confirmed}
                `);

                let rows = '';
                res.data.forEach(r => {
                    let badge = r.status === 'created'
                        ? '<span class="em-badge em-badge-created">Created</span>'
                        : (r.status === 'updated'
                            ? '<span class="em-badge em-badge-updated">Updated</span>'
                            : '<span class="em-badge em-badge-pending">Confirmed</span>');
                    let disabled = r.status === 'confirmed' ? 'disabled' : '';
                    let isChecked = selectedNiks.has(r.NIK) ? 'checked' : '';
                    rows += `
                        <tr>
                            <td><input type="checkbox" class="row-check" value="${r.NIK}" ${disabled} ${isChecked}></td>
                            <td>${escapeHtml(r.NIK)}</td>
                            <td>${escapeHtml(r.Nama)}</td>
                            <td>${escapeHtml(r['Tgl Lahir'] || '')}</td>
                            <td>${escapeHtml(r['Tgl Masuk'] || '')}</td>
                            <td>${escapeHtml(r['Departmen'] || '')}</td>
                            <td>${escapeHtml(r['Sub Departmen'] || '')}</td>
                            <td>${escapeHtml(r['Section'] || '')}</td>
                            <td>${escapeHtml(r['Tipe Karyawan'] || '')}</td>
                            <td>${escapeHtml(r['Jabatan'] || '')}</td>
                            <td>${escapeHtml(r['Jenis Kelamin'] || '')}</td>
                            <td>${escapeHtml(r['Work Status'] || '')}</td>
                            <td>${escapeHtml(r['Status Nikah'] || '')}</td>
                            <td>${escapeHtml(r['Aktif'] || '')}</td>
                            <td>${escapeHtml(r['Valid From'] || '')}</td>
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

        let html = '<nav><ul class="pagination em-pagination">';

        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="1">&laquo;</a>
                </li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page - 1}">&lsaquo;</a>
                </li>`;

        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}">
                        <a class="page-link" href="javascript:;" data-page="${i}">${i}</a>
                    </li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="${lastPage}">${lastPage}</a></li>`;
        }

        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page + 1}">&rsaquo;</a>
                </li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${lastPage}">&raquo;</a>
                </li>`;
        html += '</ul></nav>';
        $('#reviewPagination').html(html);
    }

    $(document).on('click', '#reviewPagination .page-link', function () {
        currentPage = parseInt($(this).data('page'));
        loadReview();
    });

    // === Checkbox handlers (re-bind setiap load) ===
    function bindCheckboxEvents() {
        // Unbind dulu untuk hindari double binding
        $('#checkAll').off('change.em');
        $('#checkPage').off('change.em');
        $(document).off('change.em', '.row-check');

        // Header "Pilih Semua Data" → select SEMUA eligible di semua halaman
        $('#checkAll').on('change.em', function () {
            if (!currentBatchId) return;
            if (this.checked) {
                $.get("{{ url('/hr/upload-file-mp/review') }}/" + currentBatchId,
                    { page: 1, per_page: 100000 },
                    function (res) {
                        res.data.forEach(r => {
                            if (r.status !== 'confirmed') {
                                selectedNiks.add(r.NIK);
                            }
                        });
                        $('.row-check:not(:disabled)').each(function () {
                            this.checked = true;
                        });
                        $('#checkPage').prop('checked', true);
                        updateSelectedCount();
                    }
                );
            } else {
                selectedNiks.clear();
                $('.row-check').each(function () {
                    this.checked = false;
                });
                $('#checkPage').prop('checked', false);
                updateSelectedCount();
            }
        });

        // Checkbox header kolom NIK → select/deselect halaman ini saja
        $('#checkPage').on('change.em', function () {
            $('.row-check:not(:disabled)').each(function () {
                let nik = this.value;
                this.checked = $('#checkPage').prop('checked');
                if (this.checked) {
                    selectedNiks.add(nik);
                } else {
                    selectedNiks.delete(nik);
                }
            });
            updateCheckAllState(totalBatchRows);
            updateSelectedCount();
        });

        // Checkbox per-row
        $(document).on('change.em', '.row-check', function () {
            let nik = this.value;
            if (this.checked) {
                selectedNiks.add(nik);
            } else {
                selectedNiks.delete(nik);
            }
            updateCheckAllState(totalBatchRows);
            updateSelectedCount();
        });
    }

    function updateCheckAllState(total) {
        // Header "pilih semua" = checked kalau selectedNiks sudah mencakup semua eligible rows
        $('#checkAll').prop('checked', selectedNiks.size >= total && total > 0);
        // Header per-halaman = checked kalau semua row di halaman ini ada di selected
        let pageRows = $('.row-check:not(:disabled)');
        if (pageRows.length === 0) {
            $('#checkPage').prop('checked', false);
            return;
        }
        let allChecked = pageRows.toArray().every(el => selectedNiks.has(el.value));
        $('#checkPage').prop('checked', allChecked);
    }

    function updateSelectedCount() {
        $('#selectedCount').text(selectedNiks.size);
        $('#totalCount').text(totalBatchRows);
    }

    $('#btnConfirm').on('click', function () {
        let niks = Array.from(selectedNiks);
        if (niks.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal 1 data.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Konfirmasi ' + niks.length + ' data?',
            text: 'Data akan dipindahkan ke tabel utama.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Konfirmasi',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-mp/confirm') }}/" + currentBatchId,
                type: 'POST',
                data: { niks: niks, _token: csrf() },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    selectedNiks.clear();
                    loadReview();
                    loadHistory();
                    loadEmployees();
                },
                error: function () {
                    Swal.fire('Error', 'Gagal konfirmasi data.', 'error');
                }
            });
        });
    });

    // === Employees ===
    function loadEmployees() {
        $.get("{{ url('/hr/upload-file-mp/employees') }}",
            {
                search:   $('#searchInput').val(),
                page:     empCurrentPage,
                per_page: $('#empPerPage').val()
            },
            function (res) {
                let rows = '';
                if (!res.data || res.data.length === 0) {
                    rows = '<tr><td colspan="16" class="text-center text-muted">Belum ada data employee untuk unit ini.</td></tr>';
                } else {
                    res.data.forEach(r => {
                        rows += `
                            <tr>
                                <td>${escapeHtml(r.NIK)}</td>
                                <td>${escapeHtml(r['Tipe Karyawan'])}</td>
                                <td>${escapeHtml(r.Nama)}</td>
                                <td>${escapeHtml(r['Tgl Lahir'])}</td>
                                <td>${escapeHtml(r['Tgl Masuk'])}</td>
                                <td>${escapeHtml(r.Departmen)}</td>
                                <td>${escapeHtml(r['Sub Departmen'])}</td>
                                <td>${escapeHtml(r.Section)}</td>
                                <td>${escapeHtml(r.Jabatan)}</td>
                                <td>${escapeHtml(r['Jenis Kelamin'])}</td>
                                <td>${escapeHtml(r['Work Status'])}</td>
                                <td>${escapeHtml(r['Status Nikah'])}</td>
                                <td>${escapeHtml(r.Aktif)}</td>
                                <td>${escapeHtml(r['Valid From'])}</td>
                                <td>${escapeHtml(r.updated_by_name || r.send_by_username || '')}</td>
                                <td>${r.updated_at ? new Date(r.updated_at).toISOString().slice(0, 19).replace('T', ' ') : ''}</td>
                            </tr>
                        `;
                    });
                }
                $('#employeeTbody').html(rows);
                renderEmpPagination(res.meta || {});
            }
        );
    }

    function renderEmpPagination(meta) {
        let total    = meta.total || 0;
        let page     = meta.page || 1;
        let lastPage = meta.last_page || 1;
        let perPage  = meta.per_page || 25;

        if (total === 0) {
            $('#empInfo').text('Menampilkan 0 dari 0 data');
            $('#empPagination').html('');
            return;
        }

        let from = (page - 1) * perPage + 1;
        let to   = Math.min(page * perPage, total);
        $('#empInfo').text(`Menampilkan ${from}–${to} dari ${total} data`);

        let html = '<nav><ul class="pagination em-pagination">';

        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="1">&laquo;</a>
                </li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page - 1}">&lsaquo;</a>
                </li>`;

        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}">
                        <a class="page-link" href="javascript:;" data-page="${i}">${i}</a>
                    </li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="${lastPage}">${lastPage}</a></li>`;
        }

        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page + 1}">&rsaquo;</a>
                </li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${lastPage}">&raquo;</a>
                </li>`;
        html += '</ul></nav>';

        $('#empPagination').html(html);
    }

    $(document).on('click', '#empPagination .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        empCurrentPage = parseInt($(this).data('page'));
        loadEmployees();
    });

    $('#empPerPage').on('change', function () {
        empCurrentPage = 1;
        loadEmployees();
    });

    $('#formSearch').on('submit', function (e) {
        e.preventDefault();
        empCurrentPage = 1;
        loadEmployees();
    });

    // initial load
    loadHistory();
    loadEmployees();

    // auto refresh history every 30s (preserve current page)
    setInterval(function() { loadHistory(); }, 30000);
</script>
@endpush
