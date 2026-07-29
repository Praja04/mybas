@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .sp-badge { font-weight: 600; letter-spacing: 0.5px; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-eye-line me-2"></i> Trace Surat Peringatan (SP)</h4>
            <a href="{{ route('sp_pelanggaran.index') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Input SP Baru
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" action="{{ route('sp_pelanggaran.trace') }}" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label text-muted small">Cari Nama Karyawan, NIK, atau Nomor SP</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" id="search" placeholder="Contoh: Budi, 20251101, SP-PRD/072026/001" value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="ri-search-line"></i> Cari</button>
                    @if(request('search'))
                        <a href="{{ route('sp_pelanggaran.trace') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header gradient-header py-3">
        <h5 class="card-title mb-0 text-white">
            <i class="ri-list-check me-2"></i> Riwayat SP Karyawan
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th>No</th>
                        <th>Karyawan</th>
                        <th>NIK</th>
                        <th>No SP</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Status Terkini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spRecords as $index => $sp)
                    <tr>
                        <td>{{ ($spRecords->currentPage() - 1) * $spRecords->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                        <td>{{ $sp->employee->nik ?? '-' }}</td>
                        <td>
                            @if($sp->nomor_sp_generated)
                                <span class="text-success fw-bold">{{ $sp->nomor_sp_generated }}</span>
                            @else
                                {{ $sp->no_sp ?? '-' }}
                            @endif
                        </td>
                        <td>
                            <span class="badge sp-badge {{ in_array($sp->jenis_pelanggaran, ['SP 1','SP 2','SP 3']) ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $sp->jenis_pelanggaran }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'DRAFT'            => ['bg-secondary', 'DRAFT'],
                                    'PENDING_DH'       => ['bg-primary', 'Pending Dept Head'],
                                    'PENDING_IR'       => ['bg-info', 'Pending IR Staff'],
                                    'PENDING_IR_HEAD'  => ['bg-warning text-dark', 'Pending IR Head'],
                                    'APPROVED'         => ['bg-success', 'APPROVED'],
                                    'REJECTED'         => ['bg-danger', 'REJECTED'],
                                ];
                                $cs = $sp->current_status ?? 'DRAFT';
                                [$color, $label] = $statusMap[$cs] ?? ['bg-secondary', $cs];
                            @endphp
                            <span class="badge {{ $color }} sp-badge">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(in_array($cs, ['DRAFT', 'REJECTED']))
                                <button class="btn btn-sm btn-success btnSubmitDh me-1" data-id="{{ $sp->id }}">
                                    <i class="ri-send-plane-fill me-1"></i> Submit ke Dept Head
                                </button>
                                <a href="{{ route('sp_pelanggaran.index', ['edit' => $sp->id]) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Draf">
                                    <i class="ri-pencil-line"></i>
                                </a>
                            @endif
                            <button class="btn btn-sm btn-outline-info btnDetailSp" data-id="{{ $sp->id }}" title="Lihat Detail & History">
                                <i class="ri-information-line me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            Belum ada data SP yang ditemukan.
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

<!-- Modal Detail & History Log -->
<div class="modal fade" id="modalDetailSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-header text-white">
                <h5 class="modal-title text-white"><i class="ri-information-line me-2"></i> Detail & Riwayat Log SP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handler Submit ke Dept Head
    $(document).on('click', '.btnSubmitDh', function() {
        let id = $(this).data('id');
        if (!confirm('Apakah Anda yakin ingin mengajukan SP ini ke Dept Head untuk mendapatkan persetujuan?')) return;

        let $btn = $(this);
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner me-1"></i> Mengirim...');

        $.post('/sp-pelanggaran/' + id + '/submit-to-depthead', {
            _token: '{{ csrf_token() }}'
        }, function(res) {
            alert(res.message);
            location.reload();
        }).fail(function(xhr) {
            let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengirim ke Dept Head.';
            alert('Gagal: ' + err);
            $btn.prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Submit ke Dept Head');
        });
    });

    // Handler Detail SP Modal
    $(document).on('click', '.btnDetailSp', function() {
        let id = $(this).data('id');
        let modal = new bootstrap.Modal(document.getElementById('modalDetailSp'));
        $('#modalDetailContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        modal.show();

        $.get('/sp-pelanggaran/' + id + '/detail', function(res) {
            if (res.status === 'success') {
                let sp = res.data;
                let emp = sp.employee || {};
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Karyawan</th><td>: <strong>${emp.nama || '-'}</strong></td></tr>
                                <tr><th>NIK</th><td>: ${emp.nik || '-'}</td></tr>
                                <tr><th>Tanggal</th><td>: ${sp.tanggal_pelanggaran || '-'}</td></tr>
                                <tr><th>Jenis SP</th><td>: <span class="badge bg-warning text-dark">${sp.jenis_pelanggaran || '-'}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Nomor SP</th><td>: <strong class="text-success">${sp.nomor_sp_generated || sp.no_sp || '-'}</strong></td></tr>
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
