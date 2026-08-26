@extends('sp_pelanggaran.layouts.base')

@push('styles')
    <style>
        .warning-card {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            border-left: 5px solid #d32f2f;
            color: #c62828;
        }

        .warning-icon {
            font-size: 24px;
            margin-right: 15px;
        }

        .gradient-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #ffffff;
        }

        .form-group label {
            font-weight: 550;
            color: #495057;
            margin-bottom: 5px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-primary">
                    <i class="ri-file-edit-line me-2"></i>
                    {{ isset($editSp) ? 'Edit Data SP / Pelanggaran' : 'Input SP / Pelanggaran Baru' }}
                </h4>
                <a href="{{ route('sp_pelanggaran.trace') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Trace SP
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header gradient-header py-3">
                    <h5 class="card-title mb-0 text-white">
                        <i class="ri-survey-line me-2"></i> Form SP / Pelanggaran
                    </h5>
                </div>
                <div class="card-body">
                    <form id="spForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="sp_id" value="{{ isset($editSp) ? $editSp->id : '' }}">

                        <div class="mb-4 p-3 bg-light border rounded">
                            <label for="select_kode_pelanggaran" class="form-label fw-bold text-primary mb-1">
                                <i class="ri-magic-line me-1"></i> Auto-Suggest Kode Admin Pelanggaran (Pilih untuk
                                Auto-Fill)
                            </label>
                            <select id="select_kode_pelanggaran" class="form-select select2">
                                <option value="">-- Ketik Nama/Kode Pelanggaran (misal: Teguran Lisan 2x, SOP,
                                    Terlambat 2x) --</option>
                                @if (isset($masterKodes))
                                    @foreach ($masterKodes as $mk)
                                        <option value="{{ $mk->kode }}" data-kode="{{ $mk->kode }}"
                                            data-jenis="{{ $mk->jenis_sp }}"
                                            data-dasar="{{ $mk->dasar_pertimbangan ?: $mk->pasal_dilanggar }}"
                                            data-bentuk="{{ $mk->bentuk_pelanggaran ?: $mk->deskripsi }}">
                                            {{ $mk->kode }} - {{ $mk->bentuk_pelanggaran ?: $mk->nama_pelanggaran }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text text-muted small">Memilih kode akan mengisi Kode Admin, Pasal, dan Detail
                                Alasan secara otomatis.</div>
                            <input type="hidden" name="kode_admin" id="kode_admin"
                                value="{{ isset($editSp) ? $editSp->kode_admin : '' }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="employee_id">Pilih Karyawan <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select select2" required>
                                        <option value="">Ketik Nama atau NIK untuk mencari...</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                {{ isset($editSp) && $editSp->employee_id == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->nama }} - {{ $emp->nik }}
                                                ({{ $emp->kode_divisi ?? ($emp->kode_bagian ?? '-') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="tanggal_pelanggaran">Tanggal Pelanggaran / Kejadian Utama <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pelanggaran" id="tanggal_pelanggaran"
                                        class="form-control"
                                        value="{{ isset($editSp) ? $editSp->tanggal_pelanggaran : date('Y-m-d') }}"
                                        required>
                                </div>

                                {{-- Tanggal Pelanggaran Tambahan (Case Multi-Hari / Multi-Tanggal) --}}
                                <div class="form-group mb-3 p-3 bg-light rounded border border-secondary border-opacity-25">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label mb-0 fw-bold text-dark small">
                                            <i class="ri-calendar-event-line me-1 text-primary"></i> Tanggal Kejadian
                                            Tambahan (Untuk Kasus Multi-Hari / Multi-Date)
                                        </label>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2"
                                            id="btnAddDateRow">
                                            <i class="ri-add-line"></i> Tambah Tanggal
                                        </button>
                                    </div>
                                    <div id="additionalDatesContainer">
                                        @if (isset($editSp) && $editSp->dates->count() > 1)
                                            @foreach ($editSp->dates as $idx => $d)
                                                @if ($idx > 0)
                                                    <div class="input-group input-group-sm mb-2 date-row-item">
                                                        <span class="input-group-text"><i
                                                                class="ri-calendar-line"></i></span>
                                                        <input type="date" name="tanggal_terlambat_list[]"
                                                            class="form-control" value="{{ $d->tanggal }}" required>
                                                        <button type="button"
                                                            class="btn btn-outline-danger btnRemoveDateRow">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size:0.75rem">
                                        Klik <strong>+ Tambah Tanggal</strong> jika kasus pelanggaran/terlambat/mangkir
                                        terjadi lebih dari 1 hari dalam 1 pengajuan SP.
                                    </small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="sumber_data">Sumber Data</label>
                                    <input type="text" name="sumber_data" id="sumber_data" class="form-control"
                                        placeholder="Contoh: Laporan Temuan Security, Laporan Absensi"
                                        value="{{ isset($editSp) ? $editSp->sumber_data : '' }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="pasal_dilanggar">Pasal / Aturan yang Dilanggar</label>
                                    <input type="text" name="pasal_dilanggar" id="pasal_dilanggar" class="form-control"
                                        placeholder="Contoh: Pasal 5 Ayat 2 Peraturan Perusahaan"
                                        value="{{ isset($editSp) ? $editSp->pasal_dilanggar : '' }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="alasan">Alasan / Detail Pelanggaran</label>
                                    <textarea name="alasan" id="alasan" class="form-control" rows="3"
                                        placeholder="Jelaskankronologi atau alasan tindak pelanggaran...">{{ isset($editSp) ? $editSp->alasan : '' }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="lampiran">File Lampiran / Bukti (PDF, JPG, PNG)</label>
                                    <input type="file" name="lampiran" id="lampiran" class="form-control">
                                    @if (isset($editSp) && $editSp->lampiran)
                                        <div class="mt-1 small">
                                            <a href="{{ asset($editSp->lampiran) }}" target="_blank"
                                                class="text-primary">
                                                <i class="ri-file-download-line me-1"></i> Lihat Lampiran Saat Ini
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <input type="hidden" name="status" value="DRAFT">
                                <input type="hidden" name="sesuai_ketentuan" value="1">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" onclick="history.back()">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSaveSp">
                                <i class="ri-save-line me-1"></i> {{ isset($editSp) ? 'Perbarui SP' : 'Simpan SP' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Dynamic Multi-Date Handler (Case Terlambat)
            $('#btnAddDateRow').on('click', function() {
                let dateRow = `
            <div class="input-group input-group-sm mb-2 date-row-item">
                <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                <input type="date" name="tanggal_terlambat_list[]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger btnRemoveDateRow">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
                $('#additionalDatesContainer').append(dateRow);
            });

            $(document).on('click', '.btnRemoveDateRow', function() {
                $(this).closest('.date-row-item').remove();
            });

            $('#select_kode_pelanggaran').on('change', function() {
                let $opt = $(this).find(':selected');
                if ($opt.val()) {
                    let kode = $opt.data('kode');
                    let dasar = $opt.data('dasar');
                    let bentuk = $opt.data('bentuk');

                    $('#kode_admin').val(kode);
                    if (dasar) {
                        $('#pasal_dilanggar').val(dasar);
                    }
                    if (bentuk) {
                        $('#alasan').val(bentuk);
                    }
                }
            });

            function validateDuplicateDates() {
                let mainDate = $('#tanggal_pelanggaran').val();
                let datesList = [];
                if (mainDate) datesList.push(mainDate);

                let isDuplicate = false;
                let dupValue = '';

                $('input[name="tanggal_terlambat_list[]"]').each(function() {
                    let val = $(this).val();
                    if (val) {
                        if (datesList.includes(val)) {
                            isDuplicate = true;
                            dupValue = val;
                            return false;
                        }
                        datesList.push(val);
                    }
                });

                if (isDuplicate) {
                    let parts = dupValue.split('-');
                    let formattedDup = parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : dupValue;
                    Swal.fire({
                        title: 'Tanggal Ganda (Duplicate)!',
                        text: `Tanggal ${formattedDup} dimasukkan lebih dari 1 kali dalam pengajuan ini. Mohon ubah atau hapus tanggal yang sama.`,
                        icon: 'warning'
                    });
                    return false;
                }
                return true;
            }

            $('#spForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateDuplicateDates()) {
                    return false;
                }

                let formData = new FormData(this);
                let id = $('#sp_id').val();
                let url = id ? '/sp-pelanggaran/' + id + '/update' : '/sp-pelanggaran';

                $('#btnSaveSp').prop('disabled', true).html(
                    '<i class="ri-loader-4-line spinner me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        Swal.fire('Berhasil!', res.message, 'success').then(function() {
                            window.location.href =
                                "{{ route('sp_pelanggaran.trace') }}";
                        });
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON ? xhr.responseJSON.message :
                            'Terjadi kesalahan sistem.';
                        Swal.fire('Gagal!', err, 'error');
                        $('#btnSaveSp').prop('disabled', false).html(
                            '<i class="ri-save-line me-1"></i> Simpan SP');
                    }
                });
            });
        });
    </script>
@endpush
