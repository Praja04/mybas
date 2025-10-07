@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    <style>
        table tbody tr td {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        .swal2-icon {
            margin: auto !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label mb-0">Master User</h3>
                </div>
                <div class="card-toolbar">
                    <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#createUserModal">
                        <i class="fas fa-plus-circle mr-2"></i>
                        TAMBAH USER
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover table-striped" id="table-user">
                    <thead>
                        <tr>
                            <th>USERNAME</th>
                            <th>NAMA</th>
                            <th>EMAIL</th>
                            <th>GROUP PERMISSION</th>
                            <th>DEPARTMENT</th>
                            <th>STATUS</th>
                            <th style="width: 5%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Create User -->
    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- User creation form -->
                    <form id="form-create" action="{{ route('master.user.store') }}" method="POST">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input name="username" type="text" required class="form-control" id="username"
                                        placeholder="Enter username">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" required class="form-control" id="name"
                                        placeholder="Enter name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" type="email" class="form-control" id="email"
                                placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <input name="password" type="password" class="form-control" id="password" placeholder="Enter password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                        <i id="password-eye-icon" class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="auth_group_id">Auth Group</label>
                                    <select required name="auth_group_id" id="auth_group_id" class="form-control">
                                        <option value="">-- Select Auth Group --</option>
                                        @foreach ($authGroup as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="department_id">Department</label>
                                    <select required name="department_id" id="department_id" class="form-control">
                                        <option value="">-- Select Department --</option>
                                        @foreach ($department as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button form="form-create" type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end modal create user --}}

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- url/route dibedakan untuk show modal dan create disini pakai yg ubah --}}
                <form id="form-edit" action="{{ url('master/user/prosesUbah/') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Form fields for edit user -->
                        <input type="hidden" name="id" id="editUserId">

                        <div class="form-group">
                            <label for="editUsername">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email">
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <div class="input-group">
                                <input name="edit_password" type="password" class="form-control" id="editPasword" placeholder="Enter password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleEditPasswordVisibility()">
                                        <i id="password-eye-icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>                          

                        <div class="form-group">
                            <label for="editAuthGroupId">Group</label>
                            <select class="form-control" id="editAuthGroupId" name="auth_group_id" required>
                                <option value="">Select Group</option>
                                @foreach ($authGroup as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editDepartmentId">Department</label>
                            <select class="form-control" id="editDepartmentId" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button form="form-edit" type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--Modal change user --}}
    <div class="modal" id="modal-change-permission">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal" action="" role="form" id="change-permission-form" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" class="group_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">AUTH PERMISSIONS</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="auth-permissions">
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary pull-left submit-button">OK</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    {{-- end modal edit user --}}
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // mapping data tables
        $(document).ready(function() {
            var table = $('#table-user').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.user.data') }}",
                columns: [{
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'group.name', name: 'group.name', render: function (data, type, row) {
                        return `<a onClick="groupPermissions('${row.group.id}')" href="javascript:;">${data}</a>`;
                    }},
                    {
                        data: 'department.name',
                        name: 'department.name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data == 1 ? '<span class="badge badge-success">Aktif</span>' :
                                '<span class="badge badge-danger">Tidak Aktif</span>';
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<div class="btn-group">' +
                                '<button type="button" class="btn btn-sm btn-primary dropdown-toggle py-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>' +
                                '<div class="dropdown-menu">' +
                                '<a class="dropdown-item edit-user" href="#" data-id="' + row.id +
                                '">Edit</a>' +
                                '<a class="dropdown-item nonaktifkan-user" href="#" data-id="' + row
                                .id + '" data-status="' + row.status + '">' + (row.status == 1 ?
                                    'Nonaktifkan' : 'Aktifkan') + '</a>' +
                                '</div>' +
                                '</div>';
                        }
                    }
                ]
            });
            // handle function nonaktifkan-user
            $(document).on('click', '.nonaktifkan-user', function(e) {
                e.preventDefault();
                var button = $(this);
                var userId = button.data('id');
                var status = button.data('status');
                var nonaktifkanUrl = "{{ url('/master/user/nonaktifkan') }}/" + userId;

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah status user ini?',
                    icon: 'warning',
                    customClass: {
                        popup: 'swal-wide',
                        icon: 'icon-class'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: nonaktifkanUrl,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    table.ajax.reload();
                                    var newStatus = response.new_status;
                                    var buttonText = newStatus == 1 ? 'Nonaktifkan' :
                                        'Aktifkan';
                                    var buttonClass = newStatus == 1 ? 'badge-danger' :
                                        'badge-success';
                                    var buttonLabel = newStatus == 1 ? 'Tidak Aktif' :
                                        'Aktif';

                                    button.text(buttonText);
                                    button.data('status', newStatus);
                                    button.removeClass('badge-danger badge-success')
                                        .addClass(buttonClass);
                                    $('.status-user[data-id="' + userId + '"]').html(
                                        '<span class="badge ' + buttonClass + '">' +
                                        buttonLabel + '</span>');

                                    Swal.fire('Berhasil', response.message, 'success');
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Terjadi kesalahan saat mengubah status user.',
                                    'error');
                            }
                        });
                    }
                });
            });
            // handle form edit 1
            // kondisi satu get user id untuk diubah
            $(document).on('click', '.edit-user', function(e) {
            e.preventDefault();
            var button = $(this);
            var userId = button.data('id');
            var editUrl = "{{ url('/master/user/ubah') }}/" + userId;

            $('#form-edit')[0].reset();

            $.ajax({
                url: editUrl,
                type: 'PUT',
                success: function(response) {
                    if (response.status == 'success') {
                        var user = response.user;

                        // Set values from the response to the form fields
                        $('#editUserId').val(user.id);
                        $('#editUsername').val(user.username);
                        $('#editName').val(user.name);
                        $('#editEmail').val(user.email);

                        console.log('editPasword:', user.password);

                        $('#editPasword').val(user.password); // Set password here
                        $('#editAuthGroupId').val(user.auth_group_id).trigger('change');
                        $('#editDepartmentId').val(user.department_id).trigger('change');

                        $('#editUserModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengambil data pengguna.', 'error');
                }
            });
        });

            // handle form edit 2
            // kondisi 2 untuk button submit create dari get id sebelum nya
            $('#form-edit').on('submit', function(e) {
                e.preventDefault();

                var data = $(this).serialize();
                var url = $(this).attr('action');
                var idUpdate = $('#editUserId').val();
                url = url + '/' + idUpdate;


                $.ajax({
                    url: url,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#editUserModal').modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message, 'Success!');
                            // ambil response dari parameter request
                            console.log(response.request);
                        } else {
                            toastr.error(response.message, 'Error!');
                            console.log('Gagal 1');
                        }
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON;
                        if ($.isEmptyObject(res) == false) {
                            $.each(res.errors, function(key, value) {
                                toastr.error(value, 'Error!');
                            });
                        }
                        console.log('Gagal 2');
                    }
                });
            });
            // 

            // handle form create
            $('#form-create').on('submit', function(e) {
                e.preventDefault();

                var data = $(this).serialize();
                var url = $(this).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#createUserModal').modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message, 'Success!');
                        } else {
                            toastr.error(response.message, 'Error!');
                        }
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON;
                        if ($.isEmptyObject(res) == false) {
                            $.each(res.errors, function(key, value) {
                                toastr.error(value, 'Error!');
                            });
                        }
                    }
                });
            });
        });

        // toggle buat create
        function togglePasswordVisibility() {
            let passwordInput = document.getElementById("password");
            let passwordIcon = document.getElementById("password-eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            }
        }

        // toggle buat edit
        function toggleEditPasswordVisibility() {
        let passwordInput = document.getElementById('editPasword');
        let toggleIcon = document.getElementById('password-eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function groupPermissions(id)
    {
            // alert(id);
            $(".group_id").val(id);
            $.ajax({
                url: '{{ URL::to('/permission/auth-group/get-permissions') }}',
                type: 'POST',
                data: {
                    id: id
                },
                success: function ( response )
                {
                    $(".auth-permissions").html("");
                    $.each(response.auth_permissions, (key, data) => {
                        $(".auth-permissions").append(`
                        <li>
                            <div class="checkbox">
                            <label>
                                <input class="mr-1" name="permissions[]" type="checkbox" checked value="`+data.id+`">`+data.codename+`
                            </label>
                            </div>
                        </li>
                        `);
                    });
                    $(".auth-permissions").append(`
                        <li>---------------------</li>
                    `);
                    $.each(response.permissions_left, (key, data) => {
                        $(".auth-permissions").append(`
                        <li>
                            <div class="checkbox">
                            <label>
                                <input class="mr-1" name="permissions[]" type="checkbox" value="`+data.id+`">`+data.codename+`
                            </label>
                            </div>
                        </li>
                        `);
                    });
                    $("#modal-change-permission").modal("show");
                    // console.log( response );
                    },
                error: function ( error) {
                    console.log( error );
                }
            })
    }

    $(document).ready( function () {
        $('#change-permission-form').submit( function (e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    url: '{{ URL::to('/permission/auth-group/change-permissions') }}',
                    type: 'POST',
                    data: data,
                    success: function ( response ) {
                        if (response.success == 1) {
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                        }else{
                        alert("Tidak bisa menyimpan data, silahkan periksa inputan anda");
                        }
                    },
                    error : function ( error ) {
                        if (error.status == 422) {
                        $('.help-block').text('');
                        $.each(error.responseJSON.errors, (index, item) => {
                            $('._'+index+' .help-block').text(item);
                        });
                        }
                    }
                })
        });
    })

    </script>

    {{-- buat password --}}
@endpush
