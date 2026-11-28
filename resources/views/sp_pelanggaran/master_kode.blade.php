@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 3px solid #1e3c72;
        color: #1e3c72;
    }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-book-read-line me-2"></i> Master Data Kode Pelanggaran (IR Staff)</h4>
            <div>
                <button class="btn btn-success me-1" data-bs-toggle="modal" data-bs-target="#modalImportMaster">
                    <i class="ri-file-excel-line me-1"></i> Import Master Excel (kode_admin & kode_ir)
                </button>
                <button class="btn btn-primary" id="btnTambahKode">
                    <i class="ri-add-line me-1"></i> Tambah Master Kode
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Nav Tabs Kategori Kode -->
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-tabs bg-white px-3 pt-2 rounded shadow-sm">
            <li class="nav-item">
                <a class="nav-link {{ ($kategori ?? 'ALL') === 'ALL' ? 'active' : '' }}" 
                   href="{{ route('sp_pelanggaran.master_kode', ['kategori' => 'ALL', 'search' => request('search')]) }}">
                    <i class="ri-node-tree me-1"></i> Semua Master Kode
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($kategori ?? '') === 'ADMIN' ? 'active' : '' }}" 
                   href="{{ route('sp_pelanggaran.master_kode', ['kategori' => 'ADMIN', 'search' => request('search')]) }}">
                    <i class="ri-user-settings-line me-1"></i> Kode Admin (Form Input)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($kategori ?? '') === 'IR' ? 'active' : '' }}" 
                   href="{{ route('sp_pelanggaran.master_kode', ['kategori' => 'IR', 'search' => request('search')]) }}">
                    <i class="ri-shield-user-line me-1"></i> Kode IR (Penetapan IR Staff)
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" action="{{ route('sp_pelanggaran.master_kode') }}" class="row g-3 align-items-end">
            <input type="hidden" name="kategori" value="{{ $kategori ?? 'ALL' }}">
            <div class="col-md-9">
                <label for="search" class="form-label text-muted small">Cari Kode, Bentuk Pelanggaran, atau Dasar Pertimbangan</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" id="search" placeholder="Contoh: Teguran Lisan 2x, SOP, Terlambat..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit"><i class="ri-search-line"></i> Cari</button>
                    @if(request('search'))
                        <a href="{{ route('sp_pelanggaran.master_kode', ['kategori' => $kategori ?? 'ALL']) }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header gradient-header py-3">
        <h5 class="card-title mb-0 text-white">
            <i class="ri-list-check me-2"></i> Daftar Master Kode Pelanggaran ({{ $kategori ?? 'ALL' }})
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th width="4%">No</th>
                        <th width="10%">Kategori</th>
                        <th width="20%">Kode Pelanggaran</th>
                        <th width="30%">Bentuk Pelanggaran</th>
                        <th width="22%">Dasar Pertimbangan / Pasal</th>
                        <th width="8%">Tingkat SP</th>
                        <th width="14%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterKodes as $index => $mk)
                    <tr>
                        <td>{{ ($masterKodes->currentPage() - 1) * $masterKodes->perPage() + $index + 1 }}</td>
                        <td>
                            @if(($mk->kategori_kode ?? 'ADMIN') === 'IR')
                                <span class="badge bg-success">KODE IR</span>
                            @else
                                <span class="badge bg-primary">KODE ADMIN</span>
                            @endif
                        </td>
                        <td><strong class="text-dark">{{ $mk->kode }}</strong></td>
                        <td>{{ $mk->bentuk_pelanggaran ?: $mk->nama_pelanggaran }}</td>
                        <td><small class="text-muted">{{ $mk->dasar_pertimbangan ?: ($mk->pasal_dilanggar ?: '-') }}</small></td>
                        <td>
                            <span class="badge {{ in_array($mk->jenis_sp, ['SP 1','SP 2','SP 3','SP II','SP III','SP III+']) ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $mk->jenis_sp }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning btnEditKode me-1" data-id="{{ $mk->id }}">
                                <i class="ri-pencil-line"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger btnDeleteKode" data-id="{{ $mk->id }}">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Belum ada data master kode pelanggaran untuk kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($masterKodes->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $masterKodes->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Modal Form Master Kode -->
<div class="modal fade" id="modalFormKode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formKode">
                @csrf
                <input type="hidden" id="kode_id" name="kode_id">
                <div class="modal-header gradient-header text-white">
                    <h5 class="modal-title text-white" id="modalTitle"><i class="ri-edit-line me-2"></i> Master Kode Pelanggaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kategori_kode" class="form-label fw-bold">Kategori Master Kode <span class="text-danger">*</span></label>
                            <select class="form-select" id="kategori_kode" name="kategori_kode" required>
                                <option value="ADMIN">KODE ADMIN (Sheet kode_admin - Form Admin)</option>
                                <option value="IR">KODE IR (Sheet kode_ir - Penetapan IR)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_sp" class="form-label fw-bold">Tingkat SP <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_sp" name="jenis_sp" required>
                                <option value="SP I">SP I</option>
                                <option value="SP II">SP II</option>
                                <option value="SP III">SP III</option>
                                <option value="SP III+">SP III+ / SP Berat</option>
                                <option value="Teguran Lisan">Teguran Lisan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="kode" class="form-label fw-bold">Kode / Judul Pelanggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode" name="kode" placeholder="Contoh: Teguran Lisan 2x, SP I + SOP" required>
                    </div>

                    <div class="mb-3">
                        <label for="bentuk_pelanggaran" class="form-label fw-bold">Bentuk Pelanggaran (Uraian)</label>
                        <textarea class="form-control" id="bentuk_pelanggaran" name="bentuk_pelanggaran" rows="3" placeholder="Penjelasan mengenai bentuk pelanggaran..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="dasar_pertimbangan" class="form-label fw-bold">Dasar Pertimbangan / Pasal Peraturan Perusahaan</label>
                        <textarea class="form-control" id="dasar_pertimbangan" name="dasar_pertimbangan" rows="3" placeholder="Rujukan pasal atau ketentuan Peraturan Perusahaan..."></textarea>
                    </div>
                    
                    <input type="hidden" id="nama_pelanggaran" name="nama_pelanggaran">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveKode">Simpan Master Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel Master Kode -->
<div class="modal fade" id="modalImportMaster" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="importMasterForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fs-6 fw-bold"><i class="ri-file-excel-2-line me-1"></i> Import Master Kode SP (.xlsx)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload File Excel Kode Master SP:</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                        <small class="text-muted d-block mt-1">Mengimpor otomatis dari sheet `kode_admin` dan `kode_ir` pada file "kode master sp.xlsx".</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitImport"><i class="ri-upload-2-line me-1"></i> Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let modalForm = new bootstrap.Modal(document.getElementById('modalFormKode'));

    $('#btnTambahKode').on('click', function() {
        $('#formKode')[0].reset();
        $('#kode_id').val('');
        $('#kategori_kode').val('ADMIN');
        $('#modalTitle').html('<i class="ri-add-circle-line me-2"></i> Tambah Master Kode Pelanggaran');
        modalForm.show();
    });

    $(document).on('click', '.btnEditKode', function() {
        let id = $(this).data('id');
        $.get('/sp-pelanggaran/master-kode/' + id + '/detail', function(res) {
            if (res.status === 'success') {
                let data = res.data;
                $('#kode_id').val(data.id);
                $('#kategori_kode').val(data.kategori_kode || 'ADMIN');
                $('#kode').val(data.kode);
                $('#nama_pelanggaran').val(data.nama_pelanggaran || data.kode);
                $('#jenis_sp').val(data.jenis_sp);
                $('#bentuk_pelanggaran').val(data.bentuk_pelanggaran || data.deskripsi);
                $('#dasar_pertimbangan').val(data.dasar_pertimbangan || data.pasal_dilanggar);
                $('#modalTitle').html('<i class="ri-pencil-line me-2"></i> Edit Master Kode Pelanggaran');
                modalForm.show();
            }
        });
    });

    $('#formKode').on('submit', function(e) {
        e.preventDefault();
        let id = $('#kode_id').val();
        let url = id ? ('/sp-pelanggaran/master-kode/' + id + '/update') : '/sp-pelanggaran/master-kode';
        
        $('#nama_pelanggaran').val($('#kode').val());

        let $btn = $('#btnSaveKode');
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner me-1"></i> Menyimpan...');

        $.post(url, $(this).serialize(), function(res) {
            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
        }).fail(function(xhr) {
            let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menyimpan master kode.';
            Swal.fire('Gagal!', err, 'error');
            $btn.prop('disabled', false).html('Simpan Master Kode');
        });
    });

    $('#importMasterForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let $btn = $('#btnSubmitImport');
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner me-1"></i> Mengimpor...');

        $.ajax({
            url: '{{ route("sp_pelanggaran.master_kode_import") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengimpor master kode.';
                Swal.fire('Gagal!', err, 'error');
                $btn.prop('disabled', false).html('<i class="ri-upload-2-line me-1"></i> Import Sekarang');
            }
        });
    });

    $(document).on('click', '.btnDeleteKode', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Kode Pelanggaran?',
            text: 'Apakah Anda yakin ingin menghapus master kode pelanggaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Ya, Hapus',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: '/sp-pelanggaran/master-kode/' + id,
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
});
</script>
@endpush
