@extends('sp_pelanggaran.layouts.base')

@push('styles')
    <style>
        .sp-badge {
            font-size: 0.8rem;
            padding: 0.45em 0.75em;
            border-radius: 6px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(30, 60, 114, 0.04);
        }
    </style>
@endpush

@section('content')
    @php
        $userPermissions = view()->shared('permissions') ?: [];
        $isAdmin = in_array('sp_pelanggaran_admin', $userPermissions);
        $isIrRole =
            in_array('sp_pelanggaran_ir_staff', $userPermissions) ||
            in_array('sp_pelanggaran_ir_head', $userPermissions);
    @endphp
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-primary"><i class="ri-radar-line me-2"></i> Trace & Tracking SP Karyawan</h4>
                <div>
                    <button class="btn btn-sm btn-success me-1 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#modalExportSp">
                        <i class="ri-file-excel-2-line me-1"></i> Export Data SP
                    </button>
                    @if ($isAdmin)
                        <a href="{{ route('sp_pelanggaran.index') }}" class="btn btn-sm btn-primary">
                            <i class="ri-add-circle-line me-1"></i> Input SP Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <form id="filterTraceForm" class="row g-2" onsubmit="event.preventDefault(); loadTraceData(1);">
                <div class="col-md-4">
                    <input type="text" id="traceSearchInput" name="search" class="form-control form-control-sm"
                        placeholder="Cari NIK, Nama, No SP, Kode Pelanggaran...">
                </div>
                <div class="col-md-5">
                    <select id="traceStatusSelect" name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="AKTIF">🟢 SP Aktif (<= 6 Bln)</option>
                        <option value="EXPIRED">⚪ Tidak Aktif (> 6 Bln)</option>
                        <option value="SP3">🔴 SP+3 (SP Berat)</option>
                        <option value="REJECTED">⛔ SP Ditolak</option>
                        <option value="CANCELLED">⚠️ SP Cancel / Dibatalkan</option>
                        <option value="PROSES_CANCEL">⏳ Dalam Pengajuan Cancel</option>
                        <option value="PENDING_DH">Pending Dept Head</option>
                        <option value="PENDING_IR">Pending IR Staff</option>
                        <option value="PENDING_IR_HEAD">Pending IR Head</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-secondary w-100">
                        <i class="ri-search-line me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th>No</th>
                            <th>Penomoran SP</th>
                            <th>Tgl Kejadian</th>
                            <th>Nama Karyawan</th>
                            <th>NIK</th>
                            <th>Kode Pelanggaran</th>
                            <th>Tingkat SP</th>
                            <th>Status & Masa Berlaku</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="traceTableBody">
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                                Memuat data SP...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <div id="tracePaginationInfo" class="text-muted small">Memuat informasi data...</div>
            <div id="tracePaginationLinks"></div>
        </div>
    </div>div>
    </div>

    <!-- Modal Detail SP -->
    <div class="modal fade" id="modalDetailSp" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light py-2">
                    <h5 class="modal-title fs-6 fw-bold"><i class="ri-file-search-line me-1"></i> Detail & Riwayat
                        Tracking SP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cancel SP -->
    <div class="modal fade" id="modalCancelSp" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h5 class="modal-title fs-6 fw-bold"><i class="ri-ban-line me-1"></i> Batalkan SP (Cancel)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cancelSpId">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Pembatalan (Cancel): <span
                                class="text-danger">*</span></label>
                        <textarea id="cancelNotes" class="form-control" rows="3"
                            placeholder="Masukkan alasan kenapa SP ini dibatalkan..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="ri-attachment-2"></i> Lampiran Bukti Pembatalan /
                            Cancel (Opsional):</label>
                        <input type="file" id="cancelLampiranFile" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <div class="form-text small">Upload file bukti pendukung pembatalan (Max 5MB: PDF, JPG, PNG,
                            DOCX).
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmCancel">Batalkan Sekarang
                        (Cancel)</button>
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
                        <h5 class="modal-title fs-6 fw-bold"><i class="ri-download-2-line me-1"></i> Export Riwayat &
                            Klasifikasi SP</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Klasifikasi Data SP:</label>
                            <select name="kategori" class="form-select">
                                <option value="ALL">Semua Klasifikasi Status (Aktif, Expired, SP3, Ditolak, Cancel)
                                </option>
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
                        <button type="submit" class="btn btn-success"><i class="ri-download-line me-1"></i> Download
                            File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentTracePage = 1;
        let traceSearchTimer = null;

        function loadTraceData(page = 1) {
            currentTracePage = page;
            let search = $('#traceSearchInput').val();
            let status = $('#traceStatusSelect').val();

            $('#traceTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        Memuat data SP...
                    </td>
                </tr>
            `);

            $.ajax({
                url: '{{ route("sp_pelanggaran.trace") }}',
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    status: status
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        renderTable(res.data, res.is_admin, res.is_ir_role);
                        renderPagination(res.data);
                    }
                },
                error: function(xhr) {
                    $('#traceTableBody').html(`
                        <tr>
                            <td colspan="9" class="text-center py-4 text-danger">
                                <i class="ri-error-warning-line me-1"></i> Gagal memuat data SP. Silakan coba lagi.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        function renderTable(paginator, isAdmin, isIrRole) {
            let list = paginator.data || [];
            if (list.length === 0) {
                $('#traceTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="ri-search-eye-line fs-3 d-block mb-1 text-secondary"></i>
                            Belum ada data SP yang ditemukan.
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            list.forEach((sp, index) => {
                let no = (paginator.current_page - 1) * paginator.per_page + index + 1;
                let emp = sp.employee || {};
                let cs = sp.current_status || 'DRAFT';
                let isExpired = sp.is_expired;

                // Date formatting
                let dateDisplay = '-';
                if (sp.tanggal_pelanggaran) {
                    let d = new Date(sp.tanggal_pelanggaran);
                    if (!isNaN(d.getTime())) {
                        let day = String(d.getDate()).padStart(2, '0');
                        let months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        dateDisplay = `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
                    }
                }
                if (sp.dates && sp.dates.length > 1) {
                    dateDisplay += `<br><small class="text-primary fw-bold"><i class="ri-calendar-event-line"></i> +${sp.dates.length - 1} tgl</small>`;
                }

                // Status Badge
                let statusBadge = '';
                if (cs === 'CANCELLED') {
                    statusBadge = '<span class="badge bg-secondary sp-badge"><i class="ri-ban-line me-1"></i> CANCEL (DIBATALKAN)</span>';
                } else if (['CANCEL_PENDING_DH', 'CANCEL_PENDING_IR', 'CANCEL_PENDING_IR_HEAD'].includes(cs)) {
                    statusBadge = `<span class="badge bg-warning text-dark sp-badge"><i class="ri-alert-line me-1"></i> PROSES CANCEL (${cs})</span>`;
                } else if (cs === 'REJECTED') {
                    statusBadge = '<span class="badge bg-danger sp-badge"><i class="ri-close-circle-line me-1"></i> DITOLAK</span>';
                } else if (cs === 'APPROVED') {
                    if (isExpired) {
                        statusBadge = '<span class="badge bg-dark sp-badge" title="Masa berlaku 6 bulan telah habis"><i class="ri-history-line me-1"></i> TIDAK AKTIF (EXPIRED > 6 Bln)</span>';
                    } else if (['SP 3', 'Surat Peringatan 3 (SP 3)'].includes(sp.jenis_pelanggaran)) {
                        statusBadge = '<span class="badge bg-danger sp-badge"><i class="ri-alert-line me-1"></i> SP+3 (BERAT)</span>';
                    } else {
                        statusBadge = '<span class="badge bg-success sp-badge"><i class="ri-checkbox-circle-line me-1"></i> AKTIF (Berlaku 6 Bln)</span>';
                    }
                } else {
                    statusBadge = `<span class="badge bg-warning text-dark sp-badge"><i class="ri-time-line me-1"></i> PROSES (${cs})</span>`;
                }

                // Actions
                let actions = '<div class="d-flex align-items-center justify-content-center gap-1 flex-nowrap">';
                if (isAdmin && cs === 'DRAFT') {
                    actions += `
                        <button class="btn btn-sm btn-success btnSubmitDh py-1 px-2 text-nowrap" data-id="${sp.id}" title="Submit ke Dept Head">
                            <i class="ri-send-plane-fill me-1"></i> Submit
                        </button>
                        <a href="/sp-pelanggaran?edit=${sp.id}" class="btn btn-sm btn-warning text-dark py-1 px-2" title="Edit Draf SP">
                            <i class="ri-pencil-line"></i> Edit
                        </a>
                    `;
                }

                if (cs !== 'APPROVED' && !['CANCELLED', 'CANCEL_PENDING_DH', 'CANCEL_PENDING_IR', 'CANCEL_PENDING_IR_HEAD'].includes(cs)) {
                    if (isIrRole || (isAdmin && ['DRAFT', 'PENDING_DH'].includes(cs))) {
                        actions += `
                            <button class="btn btn-sm btn-outline-danger btnDeleteSp py-1 px-2 text-nowrap" data-id="${sp.id}" title="Hapus Data SP">
                                <i class="ri-delete-bin-line me-1"></i> Hapus
                            </button>
                        `;
                    }
                }

                if (cs === 'APPROVED') {
                    actions += `
                        <button class="btn btn-sm btn-outline-warning btnCancelSp py-1 px-2 text-nowrap" data-id="${sp.id}" title="Ajukan Pembatalan (Cancel SP)">
                            <i class="ri-ban-line me-1"></i> Ajukan Cancel
                        </button>
                        <a href="/sp-pelanggaran/export-sp-pdf/${sp.id}" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap" title="Download Surat Peringatan (PDF)" target="_blank">
                            <i class="ri-file-pdf-line me-1"></i> PDF
                        </a>
                    `;
                }

                actions += `
                    <button class="btn btn-sm btn-outline-primary btnDetailSp py-1 px-2 text-nowrap" data-id="${sp.id}" title="Lihat Detail & Tracking">
                        <i class="ri-information-line me-1"></i> Detail
                    </button>
                </div>`;

                let kodeBadge = (sp.kode_admin || sp.kode_ir) ?
                    `<span class="badge bg-info text-dark sp-badge" title="Kode Pelanggaran">${sp.kode_admin || sp.kode_ir}</span>` :
                    '<span class="text-muted fw-bold">-</span>';

                let jenisBadge = sp.jenis_pelanggaran ?
                    `<span class="badge bg-danger sp-badge">${sp.jenis_pelanggaran}</span>` :
                    '<span class="text-muted fw-bold">-</span>';

                html += `
                    <tr>
                        <td>${no}</td>
                        <td><strong class="text-primary">${sp.nomor_sp_generated || 'DRAFT'}</strong></td>
                        <td>${dateDisplay}</td>
                        <td><strong>${emp.nama || '-'}</strong></td>
                        <td><code>${emp.nik || '-'}</code></td>
                        <td>${kodeBadge}</td>
                        <td>${jenisBadge}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">${actions}</td>
                    </tr>
                `;
            });

            $('#traceTableBody').html(html);
        }

        function renderPagination(paginator) {
            let from = paginator.from || 0;
            let to = paginator.to || 0;
            let total = paginator.total || 0;
            let lastPage = paginator.last_page || 1;
            let current = paginator.current_page || 1;

            $('#tracePaginationInfo').html(`Menampilkan ${from} - ${to} dari ${total} data`);

            if (lastPage <= 1) {
                $('#tracePaginationLinks').html('');
                return;
            }

            let linksHtml = '<ul class="pagination pagination-sm mb-0">';

            // Previous
            if (current > 1) {
                linksHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadTraceData(${current - 1})">&laquo;</a></li>`;
            } else {
                linksHtml += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
            }

            // Page numbers
            let startPage = Math.max(1, current - 2);
            let endPage = Math.min(lastPage, current + 2);

            if (startPage > 1) {
                linksHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadTraceData(1)">1</a></li>`;
                if (startPage > 2) linksHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === current) {
                    linksHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    linksHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadTraceData(${i})">${i}</a></li>`;
                }
            }

            if (endPage < lastPage) {
                if (endPage < lastPage - 1) linksHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                linksHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadTraceData(${lastPage})">${lastPage}</a></li>`;
            }

            // Next
            if (current < lastPage) {
                linksHtml += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadTraceData(${current + 1})">&raquo;</a></li>`;
            } else {
                linksHtml += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
            }

            linksHtml += '</ul>';
            $('#tracePaginationLinks').html(linksHtml);
        }

        $(document).ready(function() {
            // Initial load
            loadTraceData(1);

            // Live Search Debounce
            $('#traceSearchInput').on('keyup', function() {
                clearTimeout(traceSearchTimer);
                traceSearchTimer = setTimeout(function() {
                    loadTraceData(1);
                }, 300);
            });

            // Filter Status Change
            $('#traceStatusSelect').on('change', function() {
                loadTraceData(1);
            });

            // Handler Submit ke Dept Head
            $(document).on('click', '.btnSubmitDh', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Submit ke Dept Head?',
                    text: 'Apakah Anda yakin ingin mengajukan SP ini ke Dept Head?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ri-send-plane-fill me-1"></i> Ya, Ajukan Now',
                    confirmButtonColor: '#28a745',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengirimkan...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        $.post('/sp-pelanggaran/' + id + '/submit-to-depthead', {
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => loadTraceData(currentTracePage));
                        }).fail(function(xhr) {
                            let err = xhr.responseJSON ? xhr.responseJSON.message :
                                'Gagal mengirim ke Dept Head.';
                            Swal.fire('Gagal!', err, 'error');
                        });
                    }
                });
            });

            // Handler Hapus SP (Draf / Proses)
            $(document).on('click', '.btnDeleteSp', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data SP?',
                    text: "Data SP draf/proses ini akan dihapus secara permanen dari sistem.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/sp-pelanggaran/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Berhasil!', res.message, 'success').then(() => loadTraceData(currentTracePage));
                            },
                            error: function(xhr) {
                                let err = xhr.responseJSON ? xhr.responseJSON.message :
                                    'Gagal menghapus data SP.';
                                Swal.fire('Gagal!', err, 'error');
                            }
                        });
                    }
                });
            });

            // Handler Cancel SP Modal
            $(document).on('click', '.btnCancelSp', function() {
                let id = $(this).data('id');
                $('#cancelSpId').val(id);
                $('#cancelNotes').val('');
                let modal = new bootstrap.Modal(document.getElementById('modalCancelSp'));
                modal.show();
            });

            // Handler Confirm Cancel
            $('#btnConfirmCancel').click(function() {
                let id = $('#cancelSpId').val();
                let notes = $('#cancelNotes').val().trim();
                if (!notes) {
                    Swal.fire('Peringatan!', 'Alasan pembatalan (Cancel) wajib diisi!', 'warning');
                    return;
                }

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('notes', notes);

                let fileInput = $('#cancelLampiranFile')[0];
                if (fileInput && fileInput.files.length > 0) {
                    formData.append('lampiran_cancel', fileInput.files[0]);
                }

                let $btn = $(this);
                $btn.prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '/sp-pelanggaran/' + id + '/cancel',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        let modalEl = document.getElementById('modalCancelSp');
                        let modalInst = bootstrap.Modal.getInstance(modalEl);
                        if (modalInst) modalInst.hide();

                        Swal.fire('Berhasil!', res.message, 'success').then(() => loadTraceData(currentTracePage));
                        $btn.prop('disabled', false).text('Batalkan Sekarang (Cancel)');
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON ? xhr.responseJSON.message :
                            'Gagal membatalkan SP.';
                        Swal.fire('Gagal!', err, 'error');
                        $btn.prop('disabled', false).text('Batalkan Sekarang (Cancel)');
                    }
                });
            });

            // Handler Detail SP Modal
            $(document).on('click', '.btnDetailSp', function() {
                let id = $(this).data('id');
                let modal = new bootstrap.Modal(document.getElementById('modalDetailSp'));
                $('#modalDetailContent').html(
                    '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>'
                );
                modal.show();

                $.get('/sp-pelanggaran/' + id + '/detail', function(res) {
                    if (res.status === 'success') {
                        let sp = res.data;
                        let emp = sp.employee || {};
                        let dateDisplay = '-';
                        let datesArr = (sp.dates && sp.dates.length > 0) ? sp.dates.map(d => d
                            .tanggal) : (sp.tanggal_pelanggaran ? [sp.tanggal_pelanggaran] : []);
                        if (datesArr.length > 0) {
                            let formattedList = datesArr.map(d => {
                                let parts = d.split('-');
                                return parts.length === 3 ?
                                    `${parts[2]}/${parts[1]}/${parts[0]}` : d;
                            });
                            if (datesArr.length > 1) {
                                dateDisplay =
                                    `<strong>${formattedList[0]}</strong> <span class="badge bg-info text-dark">Multi-Date (${datesArr.length} Tanggal)</span><br><small class="text-primary"><i class="ri-calendar-event-line me-1"></i>Total ${datesArr.length} Tanggal Kejadian: ${formattedList.join(', ')}</small>`;
                            } else {
                                dateDisplay = formattedList[0];
                            }
                        }

                        let lampiranPelanggaran = sp.lampiran ?
                            `<a href="/${sp.lampiran}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-file-download-line me-1"></i> Bukti Pelanggaran (Saat Buat SP)</a>` :
                            `<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran saat buat SP</span>`;

                        let lampiranCancel = sp.lampiran_cancel ?
                            `<a href="/${sp.lampiran_cancel}" target="_blank" class="btn btn-sm btn-outline-warning text-dark fw-bold"><i class="ri-file-download-line me-1"></i> Bukti Pembatalan (Saat Cancel SP)</a>` :
                            `<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran pembatalan</span>`;

                        let lampiranKonseling = sp.file_konseling ?
                            `<a href="/${sp.file_konseling}" target="_blank" class="btn btn-sm btn-success text-white fw-bold"><i class="ri-file-pdf-line me-1"></i> PDF Hasil Konseling</a>` :
                            `<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Belum ada file konseling</span>`;

                        let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Karyawan</th><td>: <strong>${emp.nama || '-'}</strong></td></tr>
                                <tr><th>NIK</th><td>: ${emp.nik || '-'}</td></tr>
                                <tr><th>Tanggal</th><td>: ${dateDisplay}</td></tr>
                                <tr><th>Jenis SP</th><td>: <span class="badge bg-warning text-dark">${sp.jenis_pelanggaran || '-'}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Nomor SP</th><td>: <strong class="text-success">${sp.nomor_sp_generated || '-'}</strong></td></tr>
                                <tr><th>Status</th><td>: <span class="badge bg-primary">${sp.current_status || '-'}</span></td></tr>
                                <tr><th>Sumber Data</th><td>: ${sp.sumber_data || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Pasal / Aturan Yang Dilanggar:</label>
                        <p class="p-2 bg-light rounded">${sp.pasal_dilanggar || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Alasan / Detail Pelanggaran:</label>
                        <p class="p-2 bg-light rounded">${sp.alasan || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold d-block mb-2"><i class="ri-attachment-2 me-1"></i> File Bukti & Dokumen (Pelanggaran, Pembatalan & Konseling):</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="p-2 border rounded bg-light">
                                     <strong class="d-block small text-primary mb-1"><i class="ri-file-text-line me-1"></i> 1. Bukti Pelanggaran:</strong>
                                    ${lampiranPelanggaran}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded bg-light">
                                    <strong class="d-block small text-dark mb-1"><i class="ri-file-warning-line me-1 text-warning"></i> 2. Bukti Pembatalan:</strong>
                                    ${lampiranCancel}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded bg-light">
                                    <strong class="d-block small text-success mb-1"><i class="ri-file-certificate-line me-1"></i> 3. PDF Hasil Konseling:</strong>
                                    ${lampiranKonseling}
                                </div>
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold mt-4"><i class="ri-history-line me-1"></i> Riwayat Approval Log</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr><th>Waktu</th><th>Aksi</th><th>Catatan</th></tr>
                            </thead>
                            <tbody>
                `;
                        if (sp.approval_logs && sp.approval_logs.length > 0) {
                            sp.approval_logs.forEach(log => {
                                html += `<tr>
                            <td><small>${log.created_at || '-'}</small></td>
                            <td><span class="badge bg-secondary">${log.action}</span></td>
                            <td>${log.notes || '-'}</td>
                        </tr>`;
                            });
                        } else {
                            html +=
                                `<tr><td colspan="3" class="text-center text-muted">Belum ada riwayat log persetujuan.</td></tr>`;
                        }
                        html += `</tbody></table></div>`;
                        $('#modalDetailContent').html(html);
                    }
                }).fail(function() {
                    $('#modalDetailContent').html(
                        '<div class="alert alert-danger">Gagal mengambil detail SP.</div>');
                });
            });
        });
    </script>
@endpush
