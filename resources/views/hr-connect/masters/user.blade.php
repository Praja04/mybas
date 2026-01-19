@extends('hr-connect.layouts.base')

@section('content')
    <div class="container-fluid">
        <button id="btnStore" class="btn btn-primary mb-3">Tambah</button>

        <div class="card p-4 table-responsive">

            <table id="tableAjax" class="table table-bordered">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Dept</th>
                        <th>Jenis Kelamin</th>
                        <th>Loker Baju</th>
                        <th>Loker Sepatu</th>
                        <th>Staff</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalData">
        <div class="modal-dialog">
            <form id="storeForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="modalTitle">Form Loker User</h5>
                    </div>
                    <div class="modal-body">

                        <div id="modalLoading" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2">Memuat data loker...</div>
                        </div>

                        <div class="modalFormContent">
                            <input type="hidden" id="editId">

                            <div class="mb-2">
                                <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nik" placeholder="Masukkan NIK"
                                    required>
                                {{-- disabled kl edit --}}
                            </div>

                            <div class="mb-2">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" placeholder="Masukkan Nama"
                                    required>
                            </div>

                            {{-- <div class="mb-2">
                                <label for="divisi" class="form-label">Departemen</label>
                                <input type="text" class="form-control" id="divisi" placeholder="Masukkan Divisi">
                            </div> --}}

                            <div class="mb-2">
                                <label for="divisi" class="form-label">Departemen <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="divisi" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                    <option value="__other__">Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-2 d-none" id="divisiLainnyaWrapper">
                                <label for="divisi_lainnya" class="form-label">Departemen Lainnya</label>
                                <input type="text" class="form-control" id="divisi_lainnya"
                                    placeholder="Masukkan nama departemen">
                            </div>


                            <div class="mb-2">
                                <label for="jk" class="form-label">Jenis Kelamin <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="jk" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">Pria</option>
                                    <option value="P">Wanita</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="no_loker" class="form-label">Nomor Loker <span
                                        class="text-danger">*</span></label>
                                {{-- <select class="form-control" id="no_loker">
                                    <option value="">Pilih No Loker</option>
                                </select> --}}
                                <input type="number" class="form-control" id="no_loker" placeholder="Masukkan Nomor Loker"
                                    required>

                            </div>

                            <div class="mb-2">
                                <label for="staff" class="form-label">Kategori Karyawan <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="staff" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="staff">Staff</option>
                                    <option value="non_staff">Non Staff</option>
                                    <option value="mitra_kerja">Mitra Kerja</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            /* ================= DATATABLE ================= */
            let table = $('#tableAjax').DataTable({
                serverSide: true,
                ajax: '/hr-connect/masters/loker-user/getData',
                columns: [{
                        data: 'nik'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'divisi'
                    },
                    {
                        data: 'jk',
                        render: function(data, type, row) {
                            return data === 'L' ? 'Pria' : 'Wanita';
                        }
                    },
                    {
                        data: 'loker_baju'
                    },
                    {
                        data: 'loker_sepatu'
                    },
                    {
                        data: 'staff',
                        render: function(data) {
                            return data
                                .replace(/_/g, ' ')
                                .replace(/\b\w/g, c => c.toUpperCase());
                        }
                    },
                    {
                        data: 'action',
                        orderable: false
                    }
                ]
            });

            /* ================= ADD ================= */
            $('#btnStore').on('click', function() {
                $('#storeForm')[0].reset();
                $('#modalData').modal('show');
            });

            /* ================= EDIT ================= */
            $(document).on('click', '.btnEdit', function() {
                // let id = $(this).data('id');
                let nik = $(this).data('nik');

                $('#storeForm')[0].reset();
                $('#modalData').modal('show');

                $.get('/hr-connect/masters/loker-user/get-by-nik/' + nik, function(res) {
                    let divisiOptions = $('#divisi option')
                        .map(function() {
                            return $(this).val();
                        })
                        .get();

                    $('#editId').val(res.nik);
                    $('#nik').val(res.nik);
                    $('#nama').val(res.nama);
                    $('#staff').val(res.staff);
                    $('#no_loker').val(res.no_loker);
                    $('#jk').val(res.jk);

                    if (divisiOptions.includes(res.divisi)) {
                        $('#divisi').val(res.divisi);
                        $('#divisiLainnyaWrapper').addClass('d-none');
                        $('#divisi_lainnya').val('');
                    } else {
                        $('#divisi').val('__other__').trigger('change');
                        $('#divisi_lainnya').val(res.divisi);
                    }

                });
            });


            $('#storeForm').submit(function(e) {
                e.preventDefault();

                let id = $('#editId').val();
                let isEdit = !!id;

                let url = isEdit ?
                    '/hr-connect/masters/loker-user/' + id :
                    '/hr-connect/masters/loker-user/store';

                let divisiFinal =
                    $('#divisi').val() === '__other__' ?
                    $('#divisi_lainnya').val() :
                    $('#divisi').val();

                divisi: divisiFinal,
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        nik: $('#nik').val(),
                        nama: $('#nama').val(),
                        divisi: divisiFinal,
                        jk: $('#jk').val(),
                        no_loker: $('#no_loker').val(),
                        staff: $('#staff').val(),
                        is_active: $('#is_active').val(),
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || (isEdit ? 'Data berhasil diperbarui' :
                                'Data berhasil ditambahkan'),
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#modalData').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).join('\n');
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg
                        });
                    }
                });
            });

            $(document).on('click', '.btnDelete', function() {
                let nik = $(this).data('nik');

                Swal.fire({
                    title: 'Yakin hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        type: 'DELETE',
                        url: '/hr-connect/masters/loker-user/delete-by-nik/' + nik,
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus',
                                text: res.message || 'Data berhasil dihapus',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat menghapus data'
                            });
                        }
                    });
                });
            });

            $(document).on('change', '#divisi', function() {
                if ($(this).val() === '__other__') {
                    $('#divisiLainnyaWrapper').removeClass('d-none');
                } else {
                    $('#divisiLainnyaWrapper').addClass('d-none');
                    $('#divisi_lainnya').val('');
                }
            });

        });
    </script>
@endpush
