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
            <h4 class="mb-0 text-primary"><i class="ri-user-search-line me-2"></i> Review SP – IR Staff</h4>
        </div>
    </div>
</div>

        <div class="card shadow-sm border-0">
            <div class="card-header gradient-header py-3">
                <h5 class="card-title mb-0 text-white">
                    <i class="ri-list-check me-2"></i> Daftar SP Menunggu Review IR Staff
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
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Status</th>
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
                                    <span class="badge {{ in_array($sp->jenis_pelanggaran, ['SP 1','SP 2','SP 3']) ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $sp->jenis_pelanggaran }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-info status-badge">PENDING IR STAFF</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary btnSubmitIr" data-id="{{ $sp->id }}">
                                        <i class="ri-send-plane-line"></i> Ajukan ke IR Head
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Tidak ada SP yang menunggu verifikasi IR Staff saat ini.
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btnSubmitIr').on('click', function() {
        let id = $(this).data('id');
        let notes = prompt('Masukkan Catatan Review (Opsional):');
        if (notes === null) return;

        $.post('/sp-pelanggaran/' + id + '/irstaff-submit', {
            _token: '{{ csrf_token() }}',
            notes: notes
        }, function(res) {
            alert(res.message);
            location.reload();
        }).fail(function(xhr) {
            alert('Gagal: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Error'));
        });
    });
});
</script>
@endpush
