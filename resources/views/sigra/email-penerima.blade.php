@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">

                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">SIGRA - Email Penerima
                                <span class="d-block text-muted pt-2 font-size-sm">Mengelola daftar email yang akan menerima
                                    notifikasi dari sistem SIGRA
                                </span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Tambah</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="email-penerima-datatable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th>NAMA PENERIMA</th>
                                    <th>EMAIL</th>
                                    <th>JENIS</th>
                                    <th>STATUS</th>
                                    <th width="5%"><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div class="modal fade" id="create-new-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i> Tambah Email Penerima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="create-email-form">
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="email-penerima">Email Penerima <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="email_penerima" required placeholder="Email Penerima" class="form-control"
                                    type="email" id="email-penerima">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="keterangan">Nama Penerima <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="keterangan" required placeholder="Nama Penerima" class="form-control"
                                    type="text" id="keterangan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="jenis">Jenis <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <select required name="jenis" id="jenis" class="form-control">
                                    <option value="" selected disabled>-- Pilih Jenis --</option>
                                    <option value="kontrak_vendor">Kontrak Vendor</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="legalitas">Legalitas</option>
                                    <option value="sio">SIO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="createButton" type="submit" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Tambah</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- UPDATE MODAL --}}
    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><i class="fa fa-tools text-dark-75"></i> Edit Email Penerima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-email-form">
                        <input type="hidden" name="id" id="edit-id">

                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="edit-email-penerima">Email Penerima <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="email_penerima" required placeholder="Email Penerima" class="form-control"
                                    type="email" id="edit-email-penerima">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="edit-keterangan">Nama Penerima <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="keterangan" required placeholder="Nama Penerima" class="form-control"
                                    type="text" id="edit-keterangan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="edit-jenis">Jenis <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <select required name="jenis" id="edit-jenis" class="form-control">
                                    <option value="" selected disabled>-- Pilih Jenis --</option>
                                    <option value="kontrak_vendor">Kontrak Vendor</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="legalitas">Legalitas</option>
                                    <option value="sio">SIO</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="editButton" type="submit" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Edit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#email-penerima-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('sigra.email.penerima.getAll') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'email_penerima',
                        name: 'email_penerima'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        function showModalCreateNew() {
            $('#create-new-modal').modal('show');
        }

        $('#create-email-form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $('#createButton').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ route('sigra.email.penerima.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#create-new-modal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                        });

                        $('#email-penerima-datatable').DataTable().ajax.reload(null, false);
                        $('#create-email-form')[0].reset();
                    }
                },
                error: function(err) {
                    $('#createButton').prop('disabled', false).text('Tambah');

                    if (err.status === 422) {
                        const message = err.responseJSON.message;

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: message
                        });

                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server.'
                    });
                },
                complete: function() {
                    $('#createButton').prop('disabled', false).text('Tambah');
                }
            });
        });

        // get data
        function editData(id) {
            $.ajax({
                url: "/sigra/email/" + id,
                method: "GET",
                success: function(res) {
                    if (res.success) {
                        $('#edit-id').val(res.data.id);
                        $('#edit-email-penerima').val(res.data.email_penerima);
                        $('#edit-keterangan').val(res.data.keterangan);
                        $('#edit-jenis').val(res.data.jenis);

                        $('#edit-modal').modal('show');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak bisa memuat data.'
                    });
                }
            });
        }

        $('#edit-email-form').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let id = $('#edit-id').val();
            $('#editButton').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ url('/sigra/email/update') }}/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        $('#edit-modal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        });

                        $('#email-penerima-datatable').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(err) {
                    $('#editButton').prop('disabled', false).text('Edit');

                    if (err.status === 422) {
                        const message = err.responseJSON.message;

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: message
                        });

                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan server.'
                    });
                },
                complete: function() {
                    $('#editButton').prop('disabled', false).text('Edit');
                }
            });
        });

        function toggleStatus(id) {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin mengubah status data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.post("{{ url('/sigra/email/toggle') }}/" + id)
                    .done(res => {
                        Swal.fire("Berhasil", res.message, "success");
                        $('#email-penerima-datatable').DataTable().ajax.reload(null, false);
                    })
                    .fail(err => {
                        Swal.fire(
                            "Error",
                            err.responseJSON?.message || "Terjadi kesalahan.",
                            "error"
                        );
                    });
            });
        }

        function deleteData(id) {
            Swal.fire({
                title: "Hapus Permanen?",
                text: "Data akan hilang dan tidak dapat dikembalikan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal"
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "/sigra/email/delete/" + id,
                    method: "DELETE",
                    success: function(res) {
                        Swal.fire("Berhasil", res.message, "success");
                        $('#email-penerima-datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire(
                            "Error",
                            "Gagal menghapus data.",
                            "error"
                        );
                    }
                });
            });
        }
    </script>
@endpush
