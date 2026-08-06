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

<div class="card shadow-sm border-0">
    <div class="card-header gradient-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 text-white">
            <i class="ri-list-check me-2"></i> Daftar SP Menunggu Persetujuan Anda
        </h5>
        <button id="btnMassApprove" class="btn btn-success btn-sm shadow-sm">
            <i class="ri-check-double-line me-1"></i> Setujui Semua SP (<span id="selectedCount">{{ count($spRecords) }}</span>)
        </button>
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
                        <td>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</td>
                        <td><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                        <td><code>{{ $sp->employee->nik ?? '-' }}</code></td>
                        <td>
                            <span class="badge {{ in_array($sp->jenis_pelanggaran, ['SP 1','SP 2','SP 3']) ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $sp->jenis_pelanggaran }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary status-badge">PENDING DEPT HEAD</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success btnApproveSp" data-id="{{ $sp->id }}">
                                <i class="ri-check-line"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger btnRejectSp" data-id="{{ $sp->id }}">
                                <i class="ri-close-line"></i> Reject
                            </button>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
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
});
</script>
@endpush
