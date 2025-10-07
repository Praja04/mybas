@extends('system5r.layouts.base')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h3 style="margin-bottom: 13px">GROUP</h3>
                    <div class="card border">
                        <div class="card-body">
                            <h6>Filter Department</h6>
                            <select name="department" id="filter_department" class="form-control" style="border: none;">
                                <option value="NO">-- Pilih Department --</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal" data-bs-target="#modalCreateGroup">
                        <i class="mdi mdi-plus"></i>
                        TAMBAH GROUP
                    </button>
                </div>
                <div class="col-md-9">
                    <table class="table table-hover table-striped" id="table-group">
                        <thead>
                            <tr style="background-color: #a80000; color: #fff">
                                <th style="width: 220px">NAMA GROUP</th>
                                <th>PERSENTASE</th>
                                <th>DIGITALISASI</th>
                                <th style="width: 400px">AKSI</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalCreateGroup" class="modal fade" tabindex="-1" aria-labelledby="modalCreateGroupLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateGroupLabel">Tambah Group Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-group.store') }}" method="POST" id="formCreateGroup">
                    <div class="form-group mb-3">
                        <label for="">Department</label>
                        <select required name="id_department" id="filter_department" class="form-control">
                            <option value="">-- Pilih Department --</option>
                            @foreach ($department as $dept)
                                <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3 ">
                        <label for="">Nama Group</label>
                        <input required type="text" name="nama_group" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Persentase</label>
                        <input required type="number" name="persentase" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="is_digitalisasi">Digitalisasi</label>
                        <select required name="is_digitalisasi" id="is_digitalisasi" class="form-control">
                            <option value="Y">Ya</option>
                            <option value="N">Tidak</option>
                        </select>
                    </div>
                    <button class="btn btn-success">
                        <i class="mdi mdi-content-save"></i>
                        SIMPAN
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var table = $('#table-group').DataTable({
            ajax: {
                url: "{{ route('5r-system.master-group.data') }}",
                data: function (d) {
                    d.department = $('#filter_department').val();
                }
            },
            columns: [
                {data: 'nama_group', name: 'nama_group'},
                {data: 'persentase', name: 'persentase', render: function (data, type, row) {
                    return data + '%';
                }},
                {data: 'is_digitalisasi', name: 'Digitalisasi'},
                {data: null, name: 'action', 'orderable': false, 'searchable': false, render: function (data, type, row) {
                    return `
                    <div class="d-flex">
                        <button type="button" onClick="editPersentase('${row.id_group}', '${row.persentase}')" class="btn me-1 btn-sm waves-effect waves-light btn-warning">Edit Persentase</button>
                        <button type="button" onClick="deleteGroup('${row.id_group}')" class="btn me-1 btn-sm waves-effect waves-light btn-danger">Hapus</button>
                        <button type="button" onClick="nonaktifkanGroup('${row.id_group}')" class="btn btn-sm waves-effect waves-light btn-secondary">Nonaktifkan</button>
                    </div>
                    `;
                }},
            ]
        });

        function deleteGroup(id_group)
        {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Apakah anda yakin ingin menghapus data ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('5r-system.master-group.delete') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_group: id_group
                        },
                        success: function (res) {
                            if (res.status == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message,
                                })
                            }
                        },
                        error: function (err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: err.responseJSON.message,
                            })
                        },
                        complete: function () {
                            table.ajax.reload();
                        }
                    })
                }
            })
        }

        function nonaktifkanGroup(id_group)
        {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Apakah anda yakin ingin menonaktifkan data ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('5r-system.master-group.nonaktifkan') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_group: id_group
                        },
                        success: function (res) {
                            if (res.status == 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message,
                                })
                            }
                        },
                        error: function (err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: err.responseJSON.message,
                            })
                        },
                        complete: function () {
                            table.ajax.reload();
                        }
                    })
                }
            })
        }

        function editPersentase(id_group, persentase)
        {
            var persentase = prompt('Masukkan Persentase', persentase);

            if (persentase != null) {
                $.ajax({
                    url: "{{ route('5r-system.master-group.update-persentase') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_group: id_group,
                        persentase: persentase
                    },
                    success: function (res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message,
                            })
                        }
                    },
                    error: function (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.responseJSON.message,
                        })
                    },
                    complete: function () {
                        table.ajax.reload();
                    }
                })
            }
        }

        $('#filter_department').on('change', function () {
            var department = $(this).val();

            table.ajax.url("{{ route('5r-system.master-group.data') }}?department=" + department).load();
        });

        $('#formCreateGroup').on('submit', function (e) {
            e.preventDefault()

            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                beforeSend: function () {
                    form.find('button').attr('disabled', true);
                },
                success: function (res) {
                    if (res.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                        })
                    }
                },
                error: function (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.responseJSON.message,
                    })
                },
                complete: function () {
                    form.find('button').removeAttr('disabled');
                    table.ajax.reload();
                    form.find('input').val('');
                    $('#modalCreateGroup').modal('hide');
                }
            })
        })
    </script>
@endpush
