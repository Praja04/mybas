@extends('system5r.layouts.base')

@section('title', 'Master Area -')

@section('content')

    <div class="container-fluid">
        <h3>Master Area</h3>
        <p class="text-muted">Kelola area berdasarkan departemen.</p>

        <div id="alert-area" class="alert alert-info">
            Silakan pilih <strong>Departemen</strong> terlebih dahulu untuk menampilkan data Area.
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-9">
                        <div>
                            <label for="filter-department">Pilih Departemen <span class="text-danger">*</span></label>
                            <select name="department" id="filter_department" class="form-control">
                                <option value="" selected disabled>-- Pilih Departemen --</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="btn-pilih-department" class="btn btn-primary waves-effect w-100">
                            <i class="mdi mdi-magnify"></i>
                            Pilih
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card d-none" id="area-table-wrapper">
            <div class="card-body">
                <div class="row mb-4 align-items-end">
                    <div class="col-lg-10 col-md-6">
                        <h5 id="area-title">Area Departemen</h5>
                    </div>
                    <div class="col-lg-2 col-md-6 justify-content-end">
                        <button type="button" class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal"
                            data-bs-target="#modalCreateGroup">
                            <i class="mdi mdi-plus"></i>
                            Tambah Area
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped w-100" id="table-group">
                        <thead>
                            <tr>
                                <th style="width: 70%">NAMA AREA</th>
                                <th style="width: 30%">AKSI</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div id="modalCreateGroup" class="modal fade" tabindex="-1" aria-labelledby="modalCreateGroupLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateGroupLabel">Tambah Area Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('5r-system.master-area.store') }}" method="POST" id="formCreateGroup">
                        <div class="form-group mb-3">
                            <label for="">Departemen <span class="text-danger">*</span></label>
                            <select required name="id_department" id="create_department" class="form-control">
                                <option value="" disabled selected>-- Pilih Departemen --</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 ">
                            <label for="nama-area">Nama Area <span class="text-danger">*</span></label>
                            <input required type="text" name="nama_area" id="nama-area" placeholder="Masukkan nama area"
                                class="form-control">
                        </div>
                        <button class="btn btn-success">
                            Tambah
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- UPDATE MODAL --}}
    <div id="modalEditArea" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditArea">
                        @csrf
                        <input type="hidden" name="id_area" id="edit_id_area">

                        <div class="mb-3">
                            <label>Nama Area</label>
                            <input type="text" name="nama_area" id="edit_nama_area" class="form-control"
                                placeholder="Masukkan nama area" class="form-control">
                        </div>

                        <button class="btn btn-success">Edit</button>
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
                url: "{{ route('5r-system.master-area.data') }}",
                data: function(d) {
                    d.department = $('#filter_department').val();
                }
            },
            deferLoading: 0,
            autoWidth: false,
            responsive: true,
            columns: [{
                    data: 'nama_area',
                    name: 'nama_area'
                },
                {
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex">
                                <button onclick="editArea('${row.id_area}')" class="btn btn-sm btn-primary me-1">Edit</button>
                                <button onclick="deleteArea('${row.id_area}')" class="btn btn-sm btn-danger me-1">Hapus</button>
                                <button onclick="nonaktifkanArea('${row.id_area}')" class="btn btn-sm btn-warning">Nonaktifkan</button>
                            </div>
                        `;
                    }
                },
            ]
        });

        function deleteArea(id_area) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Apakah anda yakin ingin menghapus area ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('5r-system.master-area.delete') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_area: id_area
                        },
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                        },
                        error: function(err) {
                            console.log(err.responseJSON?.message);
                            Swal.fire(
                                'Error',
                                'Terjadi kesalahan saat menghapus data',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        function nonaktifkanArea(id_area) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Apakah anda yakin ingin menonaktifkan area ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('5r-system.master-area.nonaktifkan') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_area: id_area
                        },
                        success: function(res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                        },
                        error: function(err) {
                            console.log(err.responseJSON?.message);
                            Swal.fire(
                                'Error',
                                'Terjadi kesalahan saat menonaktifkan area',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        $('#btn-pilih-department').on('click', function() {
            let department = $('#filter_department').val();

            if (!department) {
                Swal.fire(
                    'Perhatian',
                    'Silakan pilih Departemen terlebih dahulu',
                    'info'
                );
                return;
            }

            let deptName = $('#filter_department option:selected').text();
            $('#area-title').text('Area Departemen ' + deptName);

            // Hide alert
            $('#alert-area').hide();

            // Show table
            $('#area-table-wrapper').removeClass('d-none');

            table.ajax.reload();
        });


        $('#formCreateGroup').on('submit', function(e) {
            e.preventDefault()

            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                beforeSend: function() {
                    form.find('button').attr('disabled', true);
                },
                success: function(res) {
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
                error: function(err) {
                    console.log(err.responseJSON?.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat membuat area',
                    })
                },
                complete: function() {
                    form.find('button').removeAttr('disabled');
                    table.ajax.reload();
                    form.find('input').val('');
                    $('#modalCreateGroup').modal('hide');
                }
            })
        })

        function editArea(id_area) {
            $.get("{{ route('5r-system.master-area.edit', ':id') }}".replace(':id', id_area), function(res) {
                if (res.status === 'success') {
                    $('#edit_id_area').val(res.data.id_area);
                    $('#edit_nama_area').val(res.data.nama_area);
                    $('#modalEditArea').modal('show');
                }
            }).fail(function(err) {
                console.log(err.responseJSON?.message);
                Swal.fire('Error', 'Terjadi kesalahan saat mengambil data area', 'error');
            });
        }

        $('#formEditArea').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('5r-system.master-area.update') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalEditArea').modal('hide');
                    table.ajax.reload();
                },
                error: function(err) {
                    console.log(err.responseJSON?.message);
                    Swal.fire('Error', 'Terjadi kesalahan saat edit data area', 'error');
                }
            });
        });

        $('#filter_department').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#btn-pilih-department').click();
            }
        });

        $('#modalCreateGroup').on('show.bs.modal', function() {
            let selectedDepartment = $('#filter_department').val();

            if (selectedDepartment) {
                $('#create_department').val(selectedDepartment);
            }
        });
    </script>
@endpush
