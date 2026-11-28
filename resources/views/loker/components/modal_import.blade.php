<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formImport" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="gender" id="importGenderVal">

            <div class="modal-content rounded-xl shadow-sm border-0">

                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-3" data-toggle="tooltip"
                            title="Gunakan dokumen Excel untuk memperbarui data alokasi loker secara massal.">
                            <i class="fa fa-file-excel text-white"></i>
                        </div>
                        <h5 class="font-weight-bolder mb-0">
                            Unggah (Import) Data Loker <span id="importGenderLabel"></span>
                        </h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body pt-4 pb-6 px-6">

                    <div class="alert alert-light-warning rounded-lg mb-5">
                        <strong data-toggle="tooltip"
                            title="Sistem akan mencocokkan Nomor Loker pada dokumen Excel dengan database utama dan memperbarui informasi alokasi secara otomatis.">Perhatian:</strong>
                        Seluruh data alokasi lama pada area loker
                        <span class="font-weight-bolder" id="importGenderLabelSub"></span>
                        akan diperbarui (ditimpa) secara otomatis mengikuti data dari dokumen Excel.
                    </div>

                    <p class="text-muted font-size-sm mb-4">
                        Silakan unggah dokumen Excel (.xlsx) sesuai dengan format templat master yang telah ditentukan.
                    </p>

                    <div class="form-group mb-0">
                        <label class="font-weight-bolder mb-2">Pilih Dokumen Excel</label>
                        <div class="custom-file" data-toggle="tooltip" data-placement="top"
                            title="Pastikan struktur kolom (NIK, Nama, No Loker) sesuai dengan templat master.">
                            <input type="file" name="file" class="custom-file-input" id="customFile" required
                                accept=".xlsx">
                            <label class="custom-file-label" for="customFile">Pilih dokumen...</label>
                        </div>
                        <span class="form-text text-muted font-size-xs mt-2">
                            Maksimal ukuran dokumen: 2MB
                        </span>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0 pb-6 px-6">
                    <button type="button" class="bas-btn bas-btn-secondary" data-dismiss="modal" data-toggle="tooltip"
                        title="Batalkan proses dan tutup jendela">
                        Batal
                    </button>
                    <button type="submit" class="bas-btn bas-btn-primary" id="btnSubmitImport" data-toggle="tooltip"
                        title="Klik untuk memproses dokumen. Pastikan data sudah tervalidasi sebelum melanjutkan.">
                        <i class="fas fa-upload mr-2"></i> Proses Unggah Data
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('scripts')
    <style>
        .bas-modal {
            border-radius: var(--bas-radius-lg);
            border: 1.5px solid var(--bas-border);
            overflow: hidden;
        }

        .bas-modal-header {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bas-modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .bas-modal-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        .bas-modal-close {
            background: rgba(255, 255, 255, 0.08);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .bas-modal-close:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .bas-modal-body {
            padding: 20px 24px;
        }

        .bas-table-wrap {
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-md);
            overflow: hidden;
        }

        .bas-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bas-table thead {
            background: var(--bas-neutral-light);
        }

        .bas-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--bas-neutral);
            padding: 12px;
            text-align: left;
        }

        .bas-table td {
            padding: 14px 12px;
            border-top: 1px solid var(--bas-border);
            font-size: 13px;
            color: var(--bas-dark);
        }

        .bas-table tbody tr:hover {
            background: #FAFAFA;
        }

        .bas-modal-footer {
            padding: 16px 24px;
            border-top: 1.5px solid var(--bas-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('body').on('change', '#customFile', function(e) {
                let fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            $('#formImport').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                KTApp.block('#modalImport .modal-content', {
                    message: 'Sedang memproses dokumen & Sinkronisasi...'
                });

                $.ajax({
                    url: "{{ route('loker.import') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        KTApp.unblock('#modalImport .modal-content');
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Proses Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Proses Gagal', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        KTApp.unblock('#modalImport .modal-content');
                        let msg = err.responseJSON ? err.responseJSON.message :
                            'Terjadi kesalahan sistem saat pemrosesan data.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });

        function openModalImport(gender) {
            $('#formImport')[0].reset();
            $('#customFile').next('.custom-file-label').html('Pilih dokumen Excel...');
            $('#importGenderLabel, #importGenderLabelSub').text(gender === 'L' ? 'Pria' : 'Wanita');
            $('#importGenderVal').val(gender);
            $('#modalImport').modal('show');
        }
    </script>
@endpush
