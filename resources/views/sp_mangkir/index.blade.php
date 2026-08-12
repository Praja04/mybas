@extends('sp_pelanggaran.layouts.base')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .accumulation-card { border-left: 5px solid #0d6efd; background-color: #f8f9fa; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-user-unfollow-line me-2"></i> Input Surat Peringatan (SP) Mangkir Karyawan</h4>
            <div>
                <a href="{{ route('sp_mangkir.trace') }}" class="btn btn-sm btn-info text-white shadow-sm">
                    <i class="ri-radar-line me-1"></i> Lihat Trace & Riwayat SP Mangkir
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Input Form Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header gradient-header py-3">
        <h5 class="card-title mb-0 text-white"><i class="ri-add-circle-line me-2"></i> Form Penginputan Mangkir Harian</h5>
    </div>
    <div class="card-body">
        <form id="formSpMangkir" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="employee_id" class="form-label fw-bold">Pilih Karyawan Mangkir <span class="text-danger">*</span></label>
                    <select class="form-select select2-karyawan" id="employee_id" name="employee_id" required>
                        <option value="">-- Cari Nama / NIK Karyawan --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->nik }}) - {{ $emp->kode_divisi ?: ($emp->kode_bagian ?: '-') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tanggal_mangkir" class="form-label fw-bold">Tanggal Mangkir / Alpha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_mangkir" name="tanggal_mangkir" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <!-- Dynamic Monthly Accumulation Alert Banner -->
            <div class="p-3 mb-3 rounded accumulation-card" id="bannerAccumulation" style="display: none;">
                <div class="d-flex align-items-start">
                    <i class="ri-calendar-check-line ri-2x me-3 text-primary" id="iconAccumulation"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-primary" id="titleAccumulation">Akumulasi Mangkir Bulan Ini</h6>
                        <div id="descAccumulation" class="small text-muted">Memuat kalkulasi akumulasi...</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="lampiran" class="form-label fw-bold">File Bukti Mangkir / Absensi (Opsional)</label>
                <input type="file" class="form-control" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Upload scan form/presensi mangkir. Format: PDF, JPG, PNG (Maks 5MB).</small>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-outline-primary" id="btnSaveDraft">
                    <i class="ri-save-line me-1"></i> Simpan Draf
                </button>
                <button type="button" class="btn btn-primary" id="btnSubmitDirect">
                    <i class="ri-send-plane-fill me-1"></i> Simpan & Kirim ke Dept Head
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-karyawan').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Cari Nama / NIK Karyawan --'
    });

    function triggerAccumulationCheck() {
        let empId = $('#employee_id').val();
        let tgl = $('#tanggal_mangkir').val();

        if (empId && tgl) {
            $('#bannerAccumulation').slideDown();
            $('#titleAccumulation').text('Mengecek Akumulasi Mangkir...');
            $('#descAccumulation').html('<i class="ri-loader-4-line spinner me-1"></i> Menghitung riwayat mangkir bulan ini...');

            $.get('{{ route("sp_mangkir.check_accumulation") }}', {
                employee_id: empId,
                tanggal_mangkir: tgl
            }, function(res) {
                if (res.status === 'success') {
                    let d = res.data;
                    $('#titleAccumulation').html('<i class="ri-calendar-event-line me-1"></i> Deteksi Mangkir Bulan ' + d.bulan_formatted);
                    
                    let descText = 'Karyawan ini telah tercatat mangkir <strong>' + d.existing_count + 'x</strong> pada bulan ini. Penginputan pada tanggal ini akan otomatis dihitung sebagai <strong>Mangkir ke-' + d.next_mangkir_ke + ' (' + d.suggested_kode_admin + ')</strong>.';
                    $('#descAccumulation').html(descText);
                }
            });
        } else {
            $('#bannerAccumulation').slideUp();
        }
    }

    $('#employee_id, #tanggal_mangkir').on('change', function() {
        triggerAccumulationCheck();
    });

    $('#formSpMangkir').on('submit', function(e) {
        e.preventDefault();
        saveSpMangkir(false);
    });

    $('#btnSubmitDirect').on('click', function(e) {
        e.preventDefault();
        saveSpMangkir(true);
    });

    function saveSpMangkir(isSubmitDirect) {
        let formData = new FormData($('#formSpMangkir')[0]);
        if (isSubmitDirect) {
            formData.append('submit_direct', 1);
        }

        let $btn = isSubmitDirect ? $('#btnSubmitDirect') : $('#btnSaveDraft');
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner me-1"></i> Menyimpan...');

        $.ajax({
            url: '{{ route("sp_mangkir.store") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                    window.location.href = '{{ route("sp_mangkir.trace") }}';
                });
            },
            error: function(xhr) {
                let err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menyimpan SP Mangkir.';
                Swal.fire('Gagal!', err, 'error');
                $btn.prop('disabled', false);
                if (isSubmitDirect) {
                    $('#btnSubmitDirect').html('<i class="ri-send-plane-fill me-1"></i> Simpan & Kirim ke Dept Head');
                } else {
                    $('#btnSaveDraft').html('<i class="ri-save-line me-1"></i> Simpan Draf');
                }
            }
        });
    }
});
</script>
@endpush
