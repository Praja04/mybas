@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .status-badge { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-shield-check-line me-2"></i> Approval SP – Dept Head</h4>
        </div>
    </div>
</div>

{{-- Nav Pills Tab Filter (Pemisah Approve SP vs Approve Cancel SP) --}}
<ul class="nav nav-pills mb-3 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'approval' ? 'active bg-primary text-white' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'approval']) }}">
            <i class="ri-checkbox-circle-line me-1"></i> Persetujuan SP Baru
            <span class="badge {{ $activeTab === 'approval' ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill ms-1">
                {{ $countApproval }}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'cancel' ? 'active bg-warning text-dark' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'cancel']) }}">
            <i class="ri-alert-line me-1"></i> Pengajuan Pembatalan (Cancel SP)
            <span class="badge {{ $activeTab === 'cancel' ? 'bg-dark text-white' : 'bg-warning text-dark' }} rounded-pill ms-1">
                {{ $countCancel }}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'all' ? 'active bg-secondary text-white' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'all']) }}">
            <i class="ri-file-list-3-line me-1"></i> Semua Pengajuan
            <span class="badge bg-secondary text-white rounded-pill ms-1">
                {{ $countApproval + $countCancel }}
            </span>
        </a>
    </li>
</ul>

<div class="card shadow-sm border-0">
    <div class="card-header {{ $activeTab === 'cancel' ? 'bg-warning text-dark' : 'gradient-header' }} py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 {{ $activeTab === 'cancel' ? 'text-dark fw-bold' : 'text-white' }}">
            <i class="{{ $activeTab === 'cancel' ? 'ri-alert-line' : 'ri-list-check' }} me-2"></i> 
            @if($activeTab === 'cancel')
                Daftar Pengajuan Pembatalan SP (Cancel)
            @elseif($activeTab === 'approval')
                Daftar SP Baru Menunggu Persetujuan Anda
            @else
                Daftar Semua Pengajuan Menunggu Review Anda
            @endif
        </h5>
        @if($activeTab === 'cancel')
        <button id="btnMassApproveCancel" class="btn btn-dark text-white btn-sm shadow-sm">
            <i class="ri-check-double-line me-1"></i> Setujui Semua Pembatalan (<span id="selectedCount">{{ count($spRecords) }}</span>)
        </button>
        @else
        <button id="btnMassApprove" class="btn btn-success btn-sm shadow-sm">
            <i class="ri-check-double-line me-1"></i> Setujui Semua SP (<span id="selectedCount">{{ count($spRecords) }}</span>)
        </button>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th width="3%" class="text-center">
                            <input type="checkbox" id="checkAll" class="form-check-input">
                        </th>
                        <th>No</th>
                        <th>Penomoran</th>
                        <th>Tanggal Penerbit</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis SP</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spRecords as $index => $sp)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input checkSp" value="{{ $sp->id }}">
                        </td>
                        <td>{{ ($spRecords->currentPage() - 1) * $spRecords->perPage() + $index + 1 }}</td>
                        <td><strong class="text-primary">{{ $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT') }}</strong></td>
                        <td>
                            @if($sp->dates && $sp->dates->count() > 1)
                                <strong>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</strong>
                                <br><small class="badge bg-info text-dark" style="font-size: 10px;"><i class="ri-calendar-event-line me-1"></i>{{ $sp->dates->count() }} Tanggal</small>
                            @elseif($sp->tanggal_pelanggaran)
                                {{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                        <td><code>{{ $sp->employee->nik ?? '-' }}</code></td>
                        <td>
                            <span class="badge {{ in_array($sp->jenis_pelanggaran, ['SP 1','SP 2','SP 3']) ? 'bg-danger' : 'bg-info text-dark' }}">
                                {{ $sp->jenis_pelanggaran ?: ($sp->kode_admin ?: '-') }}
                            </span>
                        </td>
                        <td>
                            @if($sp->current_status === 'CANCEL_PENDING_DH')
                                <span class="badge bg-warning text-dark status-badge"><i class="ri-alert-line me-1"></i>PENGAJUAN CANCEL SP</span>
                            @else
                                <span class="badge bg-primary status-badge">PENDING DEPT HEAD</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info btnDetailSp me-1" data-id="{{ $sp->id }}">
                                <i class="ri-eye-line"></i> Detail
                            </button>
                            @if($sp->current_status === 'CANCEL_PENDING_DH')
                                <button class="btn btn-sm btn-warning btnApproveCancelSp me-1" data-id="{{ $sp->id }}">
                                    <i class="ri-check-double-line me-1"></i> Approve Cancel
                                </button>
                            @else
                                <button class="btn btn-sm btn-success btnApproveSp" data-id="{{ $sp->id }}">
                                    <i class="ri-check-line"></i> Approve
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            Tidak ada SP yang menunggu review Anda saat ini.
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
                                <td class="text-muted">Kode Admin:</td>
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
                    <label class="fw-bold text-muted small d-block">File Bukti / Lampiran:</label>
                    <div id="detail_lampiran_link">Tidak ada lampiran</div>
                </div>

                <div class="mb-0">
                    <h6 class="fw-bold text-primary"><i class="ri-history-line me-1"></i> Riwayat Approval:</h6>
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
                    ? '<a href="/' + data.lampiran + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-file-download-line me-1"></i> Bukti Pelanggaran (Saat Buat SP)</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran saat buat SP</span>';

                let lampiranCancel = data.lampiran_cancel
                    ? '<a href="/' + data.lampiran_cancel + '" target="_blank" class="btn btn-sm btn-outline-warning text-dark fw-bold"><i class="ri-file-download-line me-1"></i> Bukti Pembatalan (Saat Cancel SP)</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran pembatalan</span>';

                let lampiranHtml = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-primary mb-1"><i class="ri-file-text-line me-1"></i> 1. Lampiran Bukti Pelanggaran:</strong>
                                ${lampiranPelanggaran}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-dark mb-1"><i class="ri-file-warning-line me-1 text-warning"></i> 2. Lampiran Bukti Pembatalan:</strong>
                                ${lampiranCancel}
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
    // Check All handler
    $('#checkAll').on('change', function() {
        $('.checkSp').prop('checked', $(this).prop('checked'));
        updateMassApproveState();
    });

    // Checkbox row handler
    $(document).on('change', '.checkSp', function() {
        let total = $('.checkSp').length;
        let checked = $('.checkSp:checked').length;
        $('#checkAll').prop('checked', total > 0 && total === checked);
        updateMassApproveState();
    });

    function updateMassApproveState() {
        let count = $('.checkSp:checked').length;
        if (count > 0) {
            $('#selectedCount').text(count);
        } else {
            $('#selectedCount').text($('.checkSp').length);
        }
    }

    // Mass Approve Handler (Approve Semua SP langsung 1-Klik)
    $('#btnMassApprove').on('click', function() {
        let selectedIds = [];
        $('.checkSp:checked').each(function() {
            selectedIds.push($(this).val());
        });

        // Jika tidak ada yang dicentang manual, otomatis ambil SELURUH SP yang ada di halaman ini
        if (selectedIds.length === 0) {
            $('.checkSp').each(function() {
                selectedIds.push($(this).val());
            });
        }

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Tidak ada SP yang menunggu persetujuan.', 'info');
            return;
        }

        Swal.fire({
            title: `Setujui Semua ${selectedIds.length} SP Sekaligus?`,
            text: 'Seluruh SP di daftar ini akan langsung diapprove.',
            icon: 'question',
            input: 'textarea',
            inputLabel: 'Catatan Persetujuan (Opsional):',
            inputValue: 'Approved Semua SP by Dept Head',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-double-line me-1"></i> Ya, Setujui Semua',
            confirmButtonColor: '#28a745',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value || 'Approved Semua SP by Dept Head';
                Swal.fire({ title: 'Memproses Persetujuan Massal...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/depthead-mass-approve', {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds,
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menyetujui sekaligus.';
                    Swal.fire('Gagal!', err, 'error');
                });
            }
        });
    });

    // Mass Approve Cancel Handler (Dept Head)
    $('#btnMassApproveCancel').on('click', function() {
        let selectedIds = [];
        $('.checkSp:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            $('.checkSp').each(function() {
                selectedIds.push($(this).val());
            });
        }

        if (selectedIds.length === 0) {
            Swal.fire('Info', 'Tidak ada pengajuan pembatalan SP yang dapat disetujui.', 'info');
            return;
        }

        Swal.fire({
            title: `Setujui Pembatalan ${selectedIds.length} SP Sekaligus?`,
            text: 'Seluruh pengajuan pembatalan SP di daftar ini akan disetujui Dept Head dan diteruskan ke IR Staff.',
            icon: 'question',
            input: 'textarea',
            inputLabel: 'Catatan Persetujuan Cancel (Opsional):',
            inputValue: 'Persetujuan Pembatalan Massal oleh Dept Head',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-double-line me-1"></i> Ya, Setujui Semua Pembatalan',
            confirmButtonColor: '#ffc107',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value || 'Persetujuan Pembatalan Massal oleh Dept Head';
                Swal.fire({ title: 'Memproses Persetujuan Pembatalan Massal...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/dh-mass-approve-cancel', {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds,
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menyetujui pembatalan sekaligus.';
                    Swal.fire('Gagal!', err, 'error');
                });
            }
        });
    });

    // Single Approve Handler
    $('.btnApproveSp').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Setujui Pengajuan SP',
            input: 'textarea',
            inputLabel: 'Catatan Approval (Opsional):',
            inputPlaceholder: 'Masukkan catatan persetujuan jika ada...',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-line me-1"></i> Setujui SP',
            confirmButtonColor: '#28a745',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value || '';
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/depthead-approve', {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                });
            }
        });
    });

    // Single Reject Handler
    $('.btnRejectSp').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Tolak Pengajuan SP',
            input: 'textarea',
            inputLabel: 'Catatan Penolakan (Wajib):',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            inputValidator: (value) => {
                if (!value.trim()) {
                    return 'Catatan penolakan harus diisi!';
                }
            },
            showCancelButton: true,
            confirmButtonText: '<i class="ri-close-circle-line me-1"></i> Tolak SP',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value;
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/depthead-reject', {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                });
            }
        });
    });

    // Approve Cancel Handler (Persetujuan Pembatalan SP)
    $(document).on('click', '.btnApproveCancelSp', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Setujui Pembatalan (Cancel) SP',
            text: 'Apakah Anda yakin ingin menyetujui pembatalan SP ini dan mengarahkannya ke IR Staff?',
            input: 'textarea',
            inputLabel: 'Catatan Persetujuan Cancel (Opsional):',
            inputPlaceholder: 'Masukkan alasan / catatan persetujuan cancel...',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-double-line me-1"></i> Ya, Setujui Pembatalan',
            confirmButtonColor: '#ffc107',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value || '';
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/dh-approve-cancel', {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
