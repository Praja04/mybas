@extends('hr-connect.layouts.base')

@push('styles')
    <!-- CSS Select2 Biar Dropdownnya Elegan -->
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
                    <!-- HEADER CARD + TOMBOL TAMBAH -->
                    <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="font-weight: 600;">
                            <i class="ri-user-settings-line text-primary me-2"></i> Data Masters Admin
                        </h5>
                        <button id="btnStore" class="btn btn-sm btn-primary fw-bold shadow-sm">
                            <i class="ri-add-line align-bottom me-1"></i> Tambah Data
                        </button>
                    </div>

                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 15%;">Kode Bagian</th>
                                        <th style="width: 15%;">Kode Admin</th>
                                        <th style="width: 15%;">NIK</th>
                                        <th style="width: 40%;">Nama Admin / User</th>
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

    <!-- Modal Store Data -->
    <div class="modal fade" id="modalData" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalDataLabel">Tambah Data Masters Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="storeForm">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="form-label fw-medium">Kode Bagian <span class="text-danger">*</span></label>
                                <select id="kode_bagian" class="js-example-basic-single form-control shadow-sm" required>
                                    <option value="">-- Pilih Kode Bagian --</option>
                                    @foreach ($kode_bagian as $bag)
                                        <option value="{{ $bag->kode_bagian }}">{{ $bag->kode_bagian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label fw-medium">Kode Admin <span class="text-danger">*</span></label>
                                <select id="kode_admin" class="js-example-basic-single form-control shadow-sm" required>
                                    <option value="">-- Pilih Kode Admin --</option>
                                    @foreach ($kode_admin as $adm)
                                        <option value="{{ $adm->kode_admin }}">{{ $adm->kode_admin }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label fw-medium">Nama Admin <span class="text-danger">*</span></label>
                                <select id="nama_admin" class="js-example-basic-single form-control shadow-sm" required>
                                    <option value="">-- Pilih Nama Admin --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->name }}">{{ $user->username . ' - ' . $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <input type="hidden" id="editId">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitForm">
                            <i class="ri-save-3-line align-bottom me-1"></i> Simpan Data
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
            // SETUP AJAX CSRF TOKEN (Penting untuk keamananan POST/DELETE)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // INISIALISASI DATATABLES DENGAN UI GACOR
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
                    url: "{{ url('/hr-connect/masters/admin/getData') }}"
                },
                columns: [{
                        data: "kode_bagian",
                        render: data =>
                            `<center><span class="badge bg-light text-dark border">${data}</span></center>`
                    },
                    {
                        data: "kode_admin",
                        render: data =>
                            `<center><span class="badge bg-primary-subtle text-primary border">${data}</span></center>`
                    },
                    {
                        data: "nik_admin",
                        render: data => `<span class="fw-bold text-secondary">${data}</span>`
                    },
                    {
                        data: "nama_admin",
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'kode_admin',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <center>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-soft-success btnEdit" data-id="${row.id}" data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="ri-edit-2-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger btnDelete" data-id="${row.id}" data-bs-toggle="tooltip" title="Hapus Data">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </center>
                            `;
                        }
                    }
                ]
            });

            // Redraw Select2 dan Tooltip saat tabel ganti halaman
            table.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // INISIALISASI SELECT2 DI DALAM MODAL
            $('.js-example-basic-single').select2({
                dropdownParent: $('#modalData'),
                width: '100%'
            });

            // TOMBOL TAMBAH DATA
            $("#btnStore").click(function() {
                $("#modalDataLabel").text('Tambah Data Masters Admin');
                $("#storeForm")[0].reset();
                $("#kode_bagian").val('').trigger('change');
                $("#kode_admin").val('').trigger('change');
                $("#nama_admin").val('').trigger('change');
                $("#editId").val('');
                $("#modalData").modal("show");
            });

            // TOMBOL EDIT DATA
            $(document).on("click", ".btnEdit", function() {
                let id = $(this).data("id");
                let btn = $(this);
                let originalHtml = btn.html();

                btn.html('<i class="spinner-border spinner-border-sm"></i>').prop('disabled', true);

                $("#modalDataLabel").text('Edit Data Masters Admin');
                $("#editId").val(id);

                $.ajax({
                    type: "GET",
                    url: "{{ url('/hr-connect/masters/admin/show/') }}/" + id,
                    success: function(response) {
                        $("#kode_bagian").val(response.kode_bagian).trigger('change');
                        $("#kode_admin").val(response.kode_admin).trigger('change');
                        $("#nama_admin").val(response.nama_admin).trigger('change');
                        $("#modalData").modal("show");
                        btn.html(originalHtml).prop('disabled', false);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal mengambil data!', 'error');
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // SUBMIT FORM (TAMBAH / EDIT)
            $("#storeForm").submit(function(e) {
                e.preventDefault();
                let id = $("#editId").val();
                let url = id ? "{{ url('/hr-connect/masters/admin/') }}/" + id :
                    "{{ url('/hr-connect/masters/admin/store') }}";
                let btn = $("#btnSubmitForm");
                let originalHtml = btn.html();

                let formData = {
                    "kode_bagian": $("#kode_bagian").val(),
                    "kode_admin": $("#kode_admin").val(),
                    "nama_admin": $("#nama_admin").val(),
                    "_token": "{{ csrf_token() }}" // Pengaman tambahan
                };

                btn.html('<i class="spinner-border spinner-border-sm align-bottom me-1"></i> Menyimpan...')
                    .prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function(res) {
                        if (res.status == 'success' || res.success) {
                            Toastify({
                                text: res.message || "Data berhasil disimpan!",
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
                        let errors = xhr.responseJSON.errors;
                        let message = '';
                        if (errors) {
                            $.each(errors, function(key, value) {
                                message += value[0] + '<br>';
                            });
                        } else {
                            message = xhr.responseJSON.message || "Terjadi kesalahan sistem.";
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan!',
                            html: message,
                        });
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // TOMBOL DELETE DATA
            $(document).on("click", ".btnDelete", function() {
                let id = $(this).data("id");

                Swal.fire({
                    title: "Hapus Data?",
                    text: "Data master admin ini akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('/hr-connect/masters/admin/') }}/" + id,
                            data: {
                                "_token": "{{ csrf_token() }}" // Wajib ada untuk tipe DELETE di Laravel
                            },
                            success: function() {
                                Toastify({
                                    text: "Data berhasil dihapus!",
                                    duration: 3000,
                                    gravity: "top",
                                    position: 'right',
                                    backgroundColor: "#f06548"
                                }).showToast();
                                table.draw(false);
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal",
                                    "Terjadi kesalahan saat menghapus data.",
                                    "error");
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
