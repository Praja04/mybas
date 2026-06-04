@extends('hr-connect.layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            height: 36px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            vertical-align: middle !important;
            text-align: center;
        }

        #tableAjax tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="font-weight: 600;">
                            <i class="ri-file-list-3-line text-primary me-2"></i> Master Data - Alasan Keluar
                        </h5>
                        <div>
                            <button id="btnUploadModal" class="btn btn-sm btn-success fw-bold shadow-sm me-2">
                                <i class="ri-file-excel-2-line align-bottom me-1"></i> Upload Excel
                            </button>
                            <button id="btnStore" class="btn btn-sm btn-primary fw-bold shadow-sm">
                                <i class="ri-add-line align-bottom me-1"></i> Tambah Data
                            </button>
                        </div>
                    </div>

                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 15%;">Tipe Action</th>
                                        <th style="width: 15%;">Kode Reason</th>
                                        <th style="width: 40%;">Nama Reason</th>
                                        <th style="width: 15%;">Status</th>
                                        <th style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalData" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalDataLabel">Tambah Data Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="storeForm">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label class="form-label fw-medium">Tipe Action <span class="text-danger">*</span></label>
                                <select id="tipe" name="tipe" class="js-example-basic-single form-control shadow-sm"
                                    required>
                                    <option value="">-- Pilih Tipe --</option>

                                    @foreach ($list_tipe as $item)
                                        <option value="{{ $item->tipe }}">{{ $item->tipe }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label fw-medium">Kode Reason <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" id="kode_reason" name="kode_reason"
                                    required>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label fw-medium">Nama Reason <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" id="nama_reason" name="nama_reason"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <input type="hidden" id="editId" name="id">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitForm">
                            <i class="ri-save-3-line align-bottom me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUpload" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="formUpload" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white fw-bold">Upload Master Data (Excel)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-success bg-success-subtle text-success border-0 shadow-sm mb-4">
                            <h6 class="fw-bold mb-1">Format Excel:</h6>
                            <ul class="mb-0 ps-3">
                                <li>Kolom A: Action Type (Tipe)</li>
                                <li>Kolom B: Kode Reason</li>
                                <li>Kolom C: Nama Reason</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Pilih File (.xlsx / .xls) <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control shadow-sm" id="excel_file" name="excel_file"
                                accept=".xlsx, .xls" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitUpload">
                            <i class="ri-upload-cloud-2-line align-bottom me-1"></i> Mulai Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                paging: true,
                pageLength: 10,
                dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
                ajax: {
                    type: "GET",
                    url: "{{ route('master-reason.getData') }}"
                },
                columns: [{
                        data: "tipe",
                        render: data => data // Teks biasa polosan
                    },
                    {
                        data: "kode_reason",
                        render: data => data // Teks biasa polosan
                    },
                    {
                        data: "nama_reason",
                        render: data => data // Teks biasa polosan
                    },
                    {
                        data: "is_active",
                        // Status tetep pakai badge biar enak dilihat mata
                        render: data => data === 'Y' ?
                            `<center><span class="badge bg-success-subtle text-success border">Aktif</span></center>` :
                            `<center><span class="badge bg-danger-subtle text-danger border">Nonaktif</span></center>`
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            let statusBtn = row.is_active === 'Y' ?
                                `<button class="btn btn-sm btn-soft-danger btnStatus" data-id="${row.id}" data-status="N" data-bs-toggle="tooltip" title="Nonaktifkan"><i class="ri-close-circle-line"></i></button>` :
                                `<button class="btn btn-sm btn-soft-success btnStatus" data-id="${row.id}" data-status="Y" data-bs-toggle="tooltip" title="Aktifkan"><i class="ri-checkbox-circle-line"></i></button>`;

                            return `
                                <center>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-soft-primary btnEdit" data-id="${row.id}" data-tipe="${row.tipe}" data-kode="${row.kode_reason}" data-nama="${row.nama_reason}" data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="ri-edit-2-line"></i>
                                        </button>
                                        ${statusBtn}
                                    </div>
                                </center>
                            `;
                        }
                    }
                ]
            });

            table.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $('.js-example-basic-single').select2({
                dropdownParent: $('#modalData'),
                width: '100%',
                tags: true
            });

            $("#btnStore").click(function() {
                $("#modalDataLabel").text('Tambah Data Reason');
                $("#storeForm")[0].reset();
                $("#tipe").val('').trigger('change');
                $("#editId").val('');
                $("#modalData").modal("show");
            });

            $("#btnUploadModal").click(function() {
                $("#formUpload")[0].reset();
                $("#modalUpload").modal("show");
            });

            $(document).on("click", ".btnEdit", function() {
                let id = $(this).data("id");
                let tipe = $(this).data("tipe");
                let kode = $(this).data("kode");
                let nama = $(this).data("nama");

                $("#modalDataLabel").text('Edit Data Reason');
                $("#editId").val(id);
                $("#tipe").val(tipe).trigger('change');
                $("#kode_reason").val(kode);
                $("#nama_reason").val(nama);

                $("#modalData").modal("show");
            });

            $("#storeForm").submit(function(e) {
                e.preventDefault();
                let btn = $("#btnSubmitForm");
                let originalHtml = btn.html();

                btn.html('<i class="spinner-border spinner-border-sm align-bottom me-1"></i> Menyimpan...')
                    .prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/masters/reason/store') }}",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            Toastify({
                                text: res.message,
                                duration: 3000,
                                gravity: "top",
                                position: 'right',
                                backgroundColor: "#0ab39c"
                            }).showToast();
                        }
                        $("#modalData").modal("hide");
                        table.draw(false);
                        btn.html(originalHtml).prop('disabled', false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan!',
                            html: xhr.responseJSON?.message ||
                                "Terjadi kesalahan sistem.",
                        });
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            $(document).on("click", ".btnStatus", function() {
                let id = $(this).data("id");
                let status = $(this).data("status");
                let textConfirm = status === 'Y' ? "mengaktifkan" : "menonaktifkan";

                Swal.fire({
                    title: "Ubah Status?",
                    text: `Anda yakin ingin ${textConfirm} data ini?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3577f1",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "Ya, Ubah!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/hr-connect/masters/reason/status') }}",
                            data: {
                                id: id,
                                status: status
                            },
                            success: function(res) {
                                Toastify({
                                    text: res.message,
                                    duration: 3000,
                                    gravity: "top",
                                    position: 'right',
                                    backgroundColor: "#0ab39c"
                                }).showToast();
                                table.draw(false);
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal",
                                    "Terjadi kesalahan saat mengubah status.",
                                    "error");
                            }
                        });
                    }
                });
            });

            $('#formUpload').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSubmitUpload');
                let originalHtml = btn.html();

                let formData = new FormData(this);

                btn.html('<i class="spinner-border spinner-border-sm align-bottom me-1"></i> Mengimpor...')
                    .prop('disabled', true);

                $.ajax({
                    url: "{{ route('master-reason.uploadExcel') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#modalUpload').modal('hide');
                        table.draw(false);
                        Toastify({
                            text: res.message,
                            duration: 3000,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "#0ab39c"
                        }).showToast();
                        btn.html(originalHtml).prop('disabled', false);
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Gagal!',
                            html: err.responseJSON?.message ||
                                'Terjadi kesalahan sistem.',
                        });
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
