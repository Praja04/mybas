@extends('system5r.layouts.base')

@section('title', 'Master Increment -')

@section('content')
    <div class="container-fluid">
        <h3>Master Increment R1–R4</h3>
        <p class="text-muted">
            Nilai R1–R4 dari periode sebelumnya yang digunakan sebagai perhitungan R5 pada periode berjalan.
        </p>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Tahun <span class="text-danger">*</span></label>
                        <select id="filter_jadwal" class="form-control">
                            <option value="" disabled selected>-- Pilih Tahun --</option>
                            @foreach ($jadwal as $j)
                                <option value="{{ $j->id_jadwal }}">
                                    {{ $j->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Periode <span class="text-danger">*</span></label>
                        <select id="filter_periode" class="form-control" disabled>
                            <option value="" disabled selected>-- Pilih Periode --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- table --}}
        <div class="card d-none" id="table-wrapper">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <h4>Nilai Increment R1-R4 (Periode Sebelumnya)</h4>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-primary w-100" id="btnTambahIncrement" disabled>
                                Tambah Data
                            </button>
                        </div>
                    </div>

                    <table class="table table-striped w-100" id="table-increment">
                        <thead>
                            <tr>
                                <th>Departemen</th>
                                <th>Nilai Increment R1–R4 (Periode Sebelumnya)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL CREATE --}}
    <div class="modal fade" id="modalCreateIncrement" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Nilai Increment R1–R4</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formCreateIncrement">
                    @csrf

                    <input type="hidden" name="id_jadwal" id="create_jadwal">
                    <input type="hidden" name="id_periode" id="create_periode">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Departemen <span class="text-danger">*</span></label>
                            <select name="id_department" id="create_department" class="form-control" required>
                                <option value="" disabled selected>
                                    -- Pilih Departemen --
                                </option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id_department }}">
                                        {{ $dept->nama_department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Nilai Increment R1–R4 (Periode Sebelumnya) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" class="form-control" min="0" required
                                placeholder="Contoh: 35">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- MODAL UPDATE --}}
    <div class="modal fade" id="modalUpdateIncrement" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Nilai Increment R1–R4</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formUpdateIncrement">
                    @csrf
                    <input type="hidden" name="id" id="update_id">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Departemen</label>
                            <input type="text" id="update_department_name" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Nilai Increment R1–R4 <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" id="update_nilai" class="form-control" min="0"
                                required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection



@push('scripts')
    <script>
        let table;

        $('#filter_jadwal').on('change', function() {
            let jadwal = $(this).val();

            $('#filter_periode')
                .prop('disabled', true)
                .html('<option disabled selected>Loading...</option>');

            $('#table-wrapper').addClass('d-none');

            $.get(
                    "{{ route('5r-system.master-increment.periode.by-jadwal', ':id') }}"
                    .replace(':id', jadwal)
                )
                .done(function(res) {
                    let opt = '<option disabled selected>-- Pilih Periode --</option>';

                    if (res.data.length === 0) {
                        opt = '<option disabled selected>Tidak ada periode</option>';
                    } else {
                        res.data.forEach(p => {
                            opt += `<option value="${p.id_periode}">${p.nama_periode}</option>`;
                        });
                    }

                    $('#filter_periode')
                        .html(opt)
                        .prop('disabled', false);
                })
                .fail(function() {
                    $('#filter_periode')
                        .html('<option disabled selected>Gagal load periode</option>');
                });
        });


        $('#filter_periode').on('change', function() {
            $('#table-wrapper').removeClass('d-none');
            $('#btnTambahIncrement').prop('disabled', false);


            table = $('#table-increment').DataTable({
                destroy: true,
                ajax: {
                    url: "{{ route('5r-system.master-increment.data') }}",
                    data: {
                        id_jadwal: $('#filter_jadwal').val(),
                        id_periode: $('#filter_periode').val()
                    }
                },
                columns: [{
                        data: 'department.nama_department'
                    },
                    {
                        data: 'nilai',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(row) {
                            if (row.nilai === null) {
                                return `<span class="text-muted">Belum diisi</span>`;
                            }

                            return `
        <button class="btn btn-sm btn-warning me-1"
            onclick="openUpdate(
                '${row.id}',
                '${row.department.nama_department}',
                '${row.nilai}'
            )">
            Edit
        </button>

        <button class="btn btn-sm btn-danger"
            onclick="deleteIncrement('${row.id}')">
            Hapus
        </button>
    `;
                        }
                    }
                ]
            });
        });

        $('#formCreateIncrement').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);

            $.ajax({
                url: "{{ route('5r-system.master-increment.store') }}",
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    form.find('button').prop('disabled', true);
                },
                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');

                    form[0].reset();
                    $('#create_department').val('');

                    $('#modalCreateIncrement').modal('hide');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    let msg = 'Gagal menyimpan data';

                    if (xhr.status === 422 && xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    form.find('button').prop('disabled', false);
                }
            });
        });


        $('#btnTambahIncrement').on('click', function() {
            $('#create_jadwal').val($('#filter_jadwal').val());
            $('#create_periode').val($('#filter_periode').val());
            $('#create_department').val('');
            $('#modalCreateIncrement').modal('show');
        });

        function openUpdate(id, deptName, nilai) {
            $('#update_id').val(id);
            $('#update_department_name').val(deptName);
            $('#update_nilai').val(nilai);

            $('#modalUpdateIncrement').modal('show');
        }

        $('#formUpdateIncrement').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);

            $.ajax({
                url: "{{ route('5r-system.master-increment.update') }}",
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    form.find('button').prop('disabled', true);
                },
                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalUpdateIncrement').modal('hide');
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memperbarui data', 'error');
                },
                complete: function() {
                    form.find('button').prop('disabled', false);
                }
            });
        });


        function deleteIncrement(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Data',
                text: 'Data increment ini akan dihapus. Lanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ route('5r-system.master-increment.delete') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus data', 'error');
                    }
                });
            });
        }
    </script>
@endpush
