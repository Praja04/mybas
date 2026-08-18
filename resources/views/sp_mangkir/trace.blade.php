@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .sp-badge { font-size: 0.8rem; padding: 0.45em 0.75em; border-radius: 6px; }
    .table-hover tbody tr:hover { background-color: rgba(30, 60, 114, 0.04); }
</style>
@endpush

@section('content')
@php
    $userPermissions = view()->shared('permissions') ?: [];
    $isAdmin = in_array('sp_pelanggaran_admin', $userPermissions);
    $isIrRole = in_array('sp_pelanggaran_ir_staff', $userPermissions) || in_array('sp_pelanggaran_ir_head', $userPermissions);
@endphp
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-user-unfollow-line me-2"></i> Trace & Tracking SP Mangkir Karyawan</h4>
            <div>
                <button class="btn btn-sm btn-success me-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExportSp">
                    <i class="ri-file-excel-2-line me-1"></i> Export Data SP
                </button>
                @if($isAdmin)
                <a href="{{ route('sp_mangkir.index') }}" class="btn btn-sm btn-primary">
                    <i class="ri-add-circle-line me-1"></i> Input SP Mangkir
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('sp_mangkir.trace') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIK, Nama, No SP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="AKTIF" {{ request('status') === 'AKTIF' ? 'selected' : '' }}>🟢 SP Mangkir Aktif (<= 6 Bln)</option>
                    <option value="EXPIRED" {{ request('status') === 'EXPIRED' ? 'selected' : '' }}>⚪ Tidak Aktif (> 6 Bln)</option>
                    <option value="SP3" {{ request('status') === 'SP3' ? 'selected' : '' }}>🔴 SP+3 (SP Berat)</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>⛔ SP Ditolak</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>⚠️ SP Cancel / Dibatalkan</option>
                    <option value="PROSES_CANCEL" {{ request('status') === 'PROSES_CANCEL' ? 'selected' : '' }}>⏳ Dalam Pengajuan Cancel</option>
                    <option value="PENDING_DH" {{ request('status') === 'PENDING_DH' ? 'selected' : '' }}>Pending Dept Head</option>
                    <option value="PENDING_IR" {{ request('status') === 'PENDING_IR' ? 'selected' : '' }}>Pending IR Staff</option>
                    <option value="PENDING_IR_HEAD" {{ request('status') === 'PENDING_IR_HEAD' ? 'selected' : '' }}>Pending IR Head</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="ri-search-line me-1"></i> Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th width="4%">No</th>
                        <th>Penomoran SP</th>
                        <th>Tgl Mangkir</th>
                        <th>Nama Karyawan</th>
                        <th>NIK</th>
                        <th>Kode Mangkir</th>
                        <th>Jenis / Tingkat SP</th>
                        <th>Status & Masa Berlaku</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sps as $index => $sp)
                    <tr>
                        <td>{{ $sps->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-primary">{{ $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT') }}</strong>
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
                        </td>
                        <td><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                        <td><code>{{ $sp->employee->nik ?? '-' }}</code></td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $sp->kode_admin ?: ('Mangkir ' . $sp->mangkir_ke) }}
                            </span>
                        </td>
                        <td>
                            @if($sp->jenis_pelanggaran)
                                <span class="badge sp-badge {{ in_array($sp->jenis_pelanggaran, ['SP 1','SP 2','SP 3']) ? 'bg-danger' : 'bg-info text-dark' }}">
                                    {{ $sp->jenis_pelanggaran }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border sp-badge">Belum Ditetapkan IR</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $cs = $sp->current_status ?? 'DRAFT';
                                $isExpired = $sp->isExpiredSp();
                            @endphp

                            @if($cs === 'CANCELLED')
                                <span class="badge bg-secondary sp-badge"><i class="ri-ban-line me-1"></i> CANCEL</span>
                            @elseif($cs === 'REJECTED')
                                <span class="badge bg-danger sp-badge"><i class="ri-close-circle-line me-1"></i> DITOLAK</span>
                            @elseif($cs === 'APPROVED')
                                @if($isExpired)
                                    <span class="badge bg-dark sp-badge" title="Masa berlaku 6 bulan telah habis"><i class="ri-history-line me-1"></i> TIDAK AKTIF (EXPIRED)</span>
                                @elseif(in_array($sp->jenis_pelanggaran, ['SP 3', 'SP III', 'SP III+']))
                                    <span class="badge bg-danger sp-badge"><i class="ri-alert-line me-1"></i> SP+3 (BERAT)</span>
                                @else
                                    <span class="badge bg-success sp-badge"><i class="ri-checkbox-circle-line me-1"></i> AKTIF (Berlaku 6 Bln)</span>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark sp-badge"><i class="ri-time-line me-1"></i> PROSES ({{ $cs }})</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1 flex-nowrap">
                                @if($isAdmin && in_array($cs, ['DRAFT', 'REJECTED']))
                                    <button class="btn btn-sm btn-success btnSubmitDh py-1 px-2 text-nowrap" data-id="{{ $sp->id }}" title="Submit ke Dept Head">
                                        <i class="ri-send-plane-fill me-1"></i> Submit
                                    </button>
                                @endif

                                {{-- Tombol Hapus: Untuk DRAFT / PENDING sebelum SP Terbit --}}
                                @if($cs !== 'APPROVED' && !in_array($cs, ['CANCELLED', 'CANCEL_PENDING_DH', 'CANCEL_PENDING_IR', 'CANCEL_PENDING_IR_HEAD']))
                                    @if($isIrRole || ($isAdmin && in_array($cs, ['DRAFT', 'PENDING_DH', 'REJECTED'])))
                                        <button class="btn btn-sm btn-outline-danger btnDeleteSp py-1 px-2 text-nowrap" data-id="{{ $sp->id }}" title="Hapus Data SP">
                                            <i class="ri-delete-bin-line me-1"></i> Hapus
                                        </button>
                                    @endif
                                @endif

                                {{-- Tombol Cancel: HANYA untuk SP yang sudah TERBIT (APPROVED) --}}
                                @if($cs === 'APPROVED')
                                    <button class="btn btn-sm btn-outline-warning btnCancelSp py-1 px-2 text-nowrap" data-id="{{ $sp->id }}" title="Ajukan Pembatalan (Cancel SP)">
                                        <i class="ri-ban-line me-1"></i> Ajukan Cancel
                                    </button>
                                @endif

                                <button class="btn btn-sm btn-outline-primary btnDetailSp py-1 px-2 text-nowrap" data-id="{{ $sp->id }}" title="Lihat Detail & Tracking">
                                    <i class="ri-information-line me-1"></i> Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data SP Mangkir yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <div>Menampilkan {{ $sps->firstItem() ?? 0 }} - {{ $sps->lastItem() ?? 0 }} dari {{ $sps->total() }} data</div>
        <div>{{ $sps->withQueryString()->links() }}</div>
    </div>
</div>

<!-- Modal Detail SP -->
<div class="modal fade" id="modalDetailSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <h5 class="modal-title fs-6 fw-bold"><i class="ri-file-search-line me-1"></i> Detail & Riwayat Tracking SP Mangkir</h5>
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
                <h5 class="modal-title fs-6 fw-bold"><i class="ri-ban-line me-1"></i> Batalkan SP Mangkir (Cancel)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancelSpId">
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan Pembatalan (Cancel): <span class="text-danger">*</span></label>
                    <textarea id="cancelNotes" class="form-control" rows="3" placeholder="Masukkan alasan kenapa SP Mangkir ini dibatalkan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btnConfirmCancel">Batalkan Sekarang (Cancel)</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export Data SP -->
<div class="modal fade" id="modalExportSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('sp_pelanggaran.export') }}" method="GET" target="_blank">
                <input type="hidden" name="sumber_data" value="MANGKIR">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fs-6 fw-bold"><i class="ri-download-2-line me-1"></i> Export Riwayat SP Mangkir</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Klasifikasi Data SP Mangkir:</label>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('.btnSubmitDh').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Submit ke Dept Head?',
            text: 'Apakah Anda yakin ingin mengajukan SP Mangkir ini ke Dept Head?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-send-plane-fill me-1"></i> Ya, Ajukan',
            confirmButtonColor: '#28a745',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Mengirimkan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/submit-to-depthead', {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengirim ke Dept Head.';
                    Swal.fire('Gagal!', err, 'error');
                });
            }
        });
    });

    $(document).on('click', '.btnCancelSp', function() {
        let id = $(this).data('id');
        $('#cancelSpId').val(id);
        $('#cancelNotes').val('');
        let modal = new bootstrap.Modal(document.getElementById('modalCancelSp'));
        modal.show();
    });

    $('#btnConfirmCancel').click(function() {
        let id = $('#cancelSpId').val();
        let notes = $('#cancelNotes').val().trim();
        if (!notes) {
            Swal.fire('Peringatan!', 'Alasan pembatalan (Cancel) wajib diisi!', 'warning');
            return;
        }

        let $btn = $(this);
        $btn.prop('disabled', true).text('Memproses...');

        $.post('/sp-pelanggaran/' + id + '/cancel', {
            _token: '{{ csrf_token() }}',
            notes: notes
        }, function(res) {
            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
        }).fail(function(xhr) {
            let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal membatalkan SP.';
            Swal.fire('Gagal!', err, 'error');
            $btn.prop('disabled', false).text('Batalkan Sekarang (Cancel)');
        });
    });

    $(document).on('click', '.btnDeleteSp', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus SP Mangkir?',
            text: 'Draft SP Mangkir ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: '/sp-mangkir/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Error', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btnDetailSp', function() {
        let id = $(this).data('id');
        let modal = new bootstrap.Modal(document.getElementById('modalDetailSp'));
        $('#modalDetailContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.show();

        $.get('/sp-pelanggaran/' + id + '/detail', function(res) {
            if (res.status === 'success') {
                let sp = res.data;
                let emp = sp.employee || {};
                let dateDisplay = '-';
                let datesArr = (sp.dates && sp.dates.length > 0) ? sp.dates.map(d => d.tanggal) : (sp.tanggal_pelanggaran ? [sp.tanggal_pelanggaran] : []);
                if (datesArr.length > 0) {
                    let formattedList = datesArr.map(d => {
                        let parts = d.split('-');
                        return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : d;
                    });
                    if (datesArr.length > 1) {
                        dateDisplay = `<strong>${formattedList[0]}</strong> <span class="badge bg-info text-dark">Multi-Date (${datesArr.length} Tanggal)</span><br><small class="text-primary"><i class="ri-calendar-event-line me-1"></i>Total ${datesArr.length} Tanggal Mangkir: ${formattedList.join(', ')}</small>`;
                    } else {
                        dateDisplay = formattedList[0];
                    }
                }

                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Karyawan</th><td>: <strong>${emp.nama || '-'}</strong></td></tr>
                                <tr><th>NIK</th><td>: ${emp.nik || '-'}</td></tr>
                                <tr><th>Tanggal Mangkir</th><td>: ${dateDisplay}</td></tr>
                                <tr><th>Kode Admin</th><td>: <span class="badge bg-primary">${sp.kode_admin || '-'}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Nomor SP</th><td>: <strong class="text-success">${sp.nomor_sp_generated || sp.no_sp || '-'}</strong></td></tr>
                                <tr><th>Status</th><td>: <span class="badge bg-primary">${sp.current_status || '-'}</span></td></tr>
                                <tr><th>Kode IR</th><td>: <span class="badge bg-secondary">${sp.kode_ir || '-'}</span></td></tr>
                                <tr><th>Jenis / Tingkat SP</th><td>: <span class="badge ${sp.jenis_pelanggaran ? 'bg-danger' : 'bg-light text-dark border'}">${sp.jenis_pelanggaran || 'Belum Ditetapkan IR'}</span></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Pasal / Aturan Yang Dilanggar:</label>
                        <p class="p-2 bg-light rounded">${sp.pasal_dilanggar || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Alasan / Detail Bentuk Mangkir:</label>
                        <p class="p-2 bg-light rounded">${sp.alasan || '-'}</p>
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
                    html += `<tr><td colspan="3" class="text-center text-muted">Belum ada riwayat log persetujuan.</td></tr>`;
                }
                html += `</tbody></table></div>`;
                $('#modalDetailContent').html(html);
            }
        }).fail(function() {
            $('#modalDetailContent').html('<div class="alert alert-danger">Gagal mengambil detail SP.</div>');
        });
    });
});
</script>
@endpush
