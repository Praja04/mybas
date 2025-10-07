@extends('system5r.layouts.base')

@section('title', 'Master Department')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between">
        <h2 class="font-weight-bolder"><i class="mdi mdi-office-building-cog"></i> MASTER DEPARTMENT</h2>
        <div>
            @if(!$data->isEmpty())
            <button type="button" class="btn btn-outline-primary waves-effect waves-light btn-block btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCreateWorkspace" style="display: none;">
                <i class="mdi mdi-plus"></i>
                TAMBAH WORKSPACE
                </button>
            @else
                <button type="button" class="btn btn-outline-primary waves-effect waves-light btn-block btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCreateWorkspace">
                    <i class="mdi mdi-plus"></i>
                    TAMBAH WORKSPACE
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        @foreach ($data as $workspace)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 style="margin-bottom: 13px">{{ $workspace->name }}</h3>
                            <div>
                                <button onClick="openCreateDepartmentModal('{{ $workspace->id_workspace }}')" type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light btn-block w-100" data-bs-toggle="modal" data-bs-target="#modalCreateDepartment">
                                    <i class="mdi mdi-plus"></i>
                                    TAMBAH DEPARTMENT
                                </button>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                                <tr class="pas-background-color">
                                    <th class="text-white">ID DEPT</th>
                                    <th class="text-white">DEPT NAME</th>
                                    <th class="text-white">STATUS</th>
                                    <th class="text-white">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workspace->departments as $department)
                                <tr>
                                    <td class="py-1">{{ $department->id_department }}</td>
                                    <td class="py-1">{{ $department->nama_department }}</td>
                                    <td class="py-1">
                                        @if($department->is_active == 'Y')
                                            <button onClick="nonaktifkan('{{ $department->id_department }}')" class="btn p-1 py-0 btn-success waves-effect waves-light" style="font-size: 10px">Aktif</button>
                                        @else
                                            <button onClick="aktifkan('{{ $department->id_department }}')" class="btn p-1 py-0 btn-danger waves-effect waves-light" style="font-size: 10px">Tidak Aktif</button>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        <!-- Tombol Edit -->
                                        <button type="button" onClick="editDepartment('{{ $department->id_department }}', '{{ $department->nama_department }}', '{{ $department->is_active }}')" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-pencil-fill"></i></button>
                        
                                        <!-- Tombol Delete -->
                                        <button type="button" onClick="deleteDepartment('{{ $department->id_department }}', '{{ $department->nama_department }}')" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="modalCreateWorkspace" class="modal fade" tabindex="-1" aria-labelledby="modalCreateWorkspaceLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateWorkspaceLabel">Buat Workspace Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-department.create-workspace') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama-workspace">Nama Workspace</label>
                        <input required type="text" id="nama-workspace" name="name" class="form-control" placeholder="Masukkan nama workspace">
                    </div>
                    <button class="mt-4 shadow-none btn btn-success waves-effect waves-light">
                        <i class="mdi mdi-send"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalCreateDepartment" class="modal fade" tabindex="-1" aria-labelledby="modalCreateDepartmentLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateDepartmentLabel">Buat Department Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-department.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3" style="display: none">
                        <label for="id-workspace">ID Workspace</label>
                        <input required type="text" id="id-workspace" name="id_workspace" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="id-department">ID Department</label>
                        <input required type="text" id="id-department" name="id_department" class="form-control" placeholder="Masukkan id department">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama-department">Nama Department</label>
                        <input required type="text" id="nama-department" name="nama_department" class="form-control" placeholder="Masukkan nama department">
                    </div>
                    <button class="mt-2 shadow-none btn btn-success waves-effect waves-light">
                        <i class="mdi mdi-send"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- modal edit department --}}
<div id="editDepartmentModal" class="modal fade" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Update Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                @csrf
                <form id="editDepartmentForm" action="{{ route('5r-system.master-department.edit') }}" method="post">                    <div class="form-group mb-3">
                    {{-- <label for="id_department">ID Department</label> --}}
                        <input type="hidden" class="form-control" id="edit_id_department" name="edit_department">
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_department">Nama Department</label>
                        <input type="text" class="form-control" id="nama_department" name="nama_department"><br>
                        <small class="form-text text-muted"><em><b>Note:</b>Ketika Anda mengubah nama departemen, ID Departemen akan otomatis mengikuti perubahan pada nama departemen.</em></small>
                    </div>
                    <button type="submit" class="mt-2 shadow-none btn btn-success waves-effect waves-light">
                        <i class="ri-send-plane-fill"></i> Ubah data
                    </button>                    
                </form>
            </div>
        </div>
    </div>
</div>



<form style="display: none" action="{{ route('5r-system.master-department.aktifkan') }}" id="form-aktifkan" method="POST">
    @csrf
    <input type="text" id="id-department-aktifkan" name="id_department">
</form>

<form style="display: none" action="{{ route('5r-system.master-department.nonaktifkan') }}" id="form-nonaktifkan" method="POST">
    @csrf
    <input type="text" id="id-department-nonaktifkan" name="id_department">
</form>

<form style="display: none" action="{{ route('5r-system.master-department.hapus') }}" id="form-hapus" method="POST">
    @csrf
    <input type="text" id="id-department-hapus" name="id_department">
</form>

@endsection

@push('scripts')
    <script>
        function openCreateDepartmentModal(id_workspace)
        {
            $('#id-workspace').val(id_workspace);
        }

        function aktifkan(id_department)
        {
            $('#id-department-aktifkan').val(id_department)

            var _confirm = window.confirm('Yakin akan mengaktifkan department ini?')

            if(!_confirm) {
                return
            }

            $('#form-aktifkan').submit()
        }

        function nonaktifkan(id_department)
        {
            $('#id-department-nonaktifkan').val(id_department)

            var _confirm = window.confirm('Yakin akan menonaktifkan department ini?')

            if(!_confirm) {
                return
            }

            $('#form-nonaktifkan').submit()
        }

        function deleteDepartment(id_department, nama_department) {
            $('#id-department-hapus').val(id_department);

            var confirmation = window.confirm("Apakah Anda yakin ingin menghapus data berikut?\n\nDepartment ID: " + id_department + "\nDepartment Name: " + nama_department);

            if(!confirmation) {
                return;
            }

            $('#form-hapus').submit();
        }

        $(document).ready(function() {
            $("#editDepartmentForm").submit(function(event) {
                event.preventDefault();

                var id = $('#edit_id_department').val();
                var name = $('#nama_department').val();

                $.ajax({
                    type: "POST",
                    url: $(this).attr('action'),
                    data: {
                        _token: $('input[name="_token"]').val(),
                        edit_id_department: id,
                        nama_department: name
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
        });

        function editDepartment(id, name) {
            document.getElementById('edit_id_department').value = id;
            document.getElementById('nama_department').value = name;

            // Tampilkan modal
            $('#editDepartmentModal').modal('show');
        }

    </script>
@endpush
