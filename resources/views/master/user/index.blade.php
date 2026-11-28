@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">

    <style>
        :root {
            --bas-primary: #F59E0B;
            --bas-primary-dark: #D97706;
            --bas-primary-light: #FEF3C7;
            --bas-neutral: #6B7280;
            --bas-neutral-light: #F3F4F6;
            --bas-dark: #374151;
            --bas-border: #E5E7EB;
            --bas-surface: #FFFFFF;
            --bas-radius-md: 12px;
            --bas-radius-lg: 18px;
            --bas-transition: all 0.2s ease;
        }

        /* 1. PERBAIKAN STRUKTUR LAYER (Z-INDEX) */
        #kt_header {
            z-index: 1100 !important;
        }

        .modal {
            z-index: 1070 !important;
        }

        .modal-backdrop {
            z-index: 1060 !important;
        }

        .popover {
            z-index: 1040 !important;
        }

        .swal2-container {
            z-index: 2000 !important;
        }

        .swal2-icon {
            margin: auto !important;
        }

        /* 2. LAYOUT & HEADER */
        .content {
            background-color: #F9FAFB !important;
            padding-top: 20px;
            /* Jarak aman dari navbar */
        }

        .bas-header {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--bas-radius-lg);
            position: relative;
            z-index: 1;
            margin-bottom: 25px;
        }

        .bas-header-icon {
            width: 56px;
            height: 56px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: var(--bas-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--bas-primary);
            flex-shrink: 0;
        }

        .bas-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            margin-bottom: 0;
        }

        .bas-header-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        /* 3. TABLE CARD STYLE */
        .bas-tab-card {
            background: var(--bas-surface) !important;
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Styling Spesifik Tabel User */
        #table-user {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
        }

        #table-user thead th {
            background-color: var(--bas-neutral-light);
            color: var(--bas-neutral);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 20px 25px;
            border: none;
        }

        #table-user tbody td {
            padding: 18px 25px;
            vertical-align: middle;
            border-bottom: 1px solid var(--bas-border);
            color: var(--bas-dark);
        }

        #table-user tbody tr:hover {
            background-color: #FFFBEB !important;
            transition: var(--bas-transition);
        }

        /* Area Pagination & Search Datatables */
        .dataTables_wrapper .row:last-child {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--bas-border);
            background-color: var(--bas-neutral-light);
        }

        .dataTables_filter,
        .dataTables_length {
            padding: 1.5rem;
        }

        /* 4. UTILITY & BUTTONS */
        .bas-btn-primary {
            background: var(--bas-primary);
            border: none;
            color: #FFFFFF;
            font-weight: 600;
            border-radius: var(--bas-radius-md);
            transition: var(--bas-transition);
            height: 45px;
        }

        .bas-btn-primary:hover {
            background: var(--bas-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: #FFFFFF;
        }

        /* 5. PERMISSION GRID & MODAL */
        .auth-permissions {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            width: 100%;
        }

        .permission-item {
            padding: 15px;
            border-radius: var(--bas-radius-md);
            border: 1.5px solid var(--bas-border);
            background-color: var(--bas-surface);
            transition: var(--bas-transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            position: relative;
        }

        .permission-item.selected {
            border-color: var(--bas-primary);
            background-color: var(--bas-primary-light);
        }

        .separator-text {
            grid-column: 1 / -1;
            padding: 20px 0 10px 0;
            font-weight: 800;
            color: var(--bas-dark);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
        }

        .separator-text::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--bas-border);
            margin-left: 15px;
        }

        /* Styling Spesifik Modal View Permission (User) */
        #modal-view-permission .modal-content {
            margin-top: 5vh;
        }

        #modal-view-permission form {
            width: 100%;
        }

        #modal-view-permission .modal-body {
            background-color: #FDFDFD;
            max-height: 55vh !important;
            overflow-y: auto;
            padding: 25px !important;
        }

        /* 6. BADGE STYLING */
        .label.label-light-warning {
            background-color: #FFFBEB !important;
            color: #D97706 !important;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-8 py-6">
        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header d-flex align-items-center justify-content-between p-7 shadow-lg">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-user-astronaut"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title">Manajemen User</h2>
                            <div class="bas-header-sub">Kontrol Hak Akses User &bull; Wings Food (BAS)</div>
                        </div>
                    </div>
                    <div>
                        <button type="button" data-toggle="modal" data-target="#createUserModal"
                            class="bas-btn bas-btn-primary px-6 h-45px d-flex align-items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah User
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('master.user.partials._table_user')
    </div>

    @include('master.user.partials._modal_create')

    @include('master.user.partials._modal_edit')

    @include('master.user.partials._modal_ubah')

    <!-- Modal Permission Tambahan per User -->
    <div class="modal fade" id="modal-user-permission" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="change-user-permission-form" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="user_permission_user_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-key text-warning mr-2"></i>
                            Permission Tambahan - <span id="modal-user-permission-name" class="text-primary"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-user-permission-body">
                        <div class="text-center py-10">
                            <span class="spinner spinner-primary mb-2"></span>
                            <div class="text-muted font-weight-bold">Memuat permission...</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-save mr-1"></i> Simpan Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var table = $('#table-user').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.user.data') }}",
                /* PERBAIKAN: Menambahkan translasi bahasa Indonesia dari halaman Permission */
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ada data yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Data kosong",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                columns: [{
                    data: 'username',
                    name: 'username',
                    render: function(data, type, row) {
                        return `
                    <div class="d-flex flex-column">
                        <span class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">${data}</span>
                        <span class="text-muted font-weight-bold font-size-sm">ID: ${row.id}</span>
                    </div>
                </div>`;
                    }
                }, {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        return `<span class="text-dark-75 font-weight-bolder d-block font-size-lg">${data}</span>`;
                    }
                }, {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, row) {
                        let emailText = data ? data :
                            '<i class="text-muted">Tidak ada email</i>';
                        return `
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope text-muted mr-2 font-size-sm"></i>
                    <span class="text-dark-50 font-weight-bold">${emailText}</span>
                </div>`;
                    }
                }, {
                    data: 'group.name',
                    name: 'group.name',
                    render: function(data, type, row) {
                        return `
        <a onClick="groupPermissions('${row.group_id || row.group.id}', '${data}')" href="javascript:;"
            class="label label-lg label-light-primary label-inline font-weight-bolder cursor-pointer text-hover-primary"
            data-toggle="tooltip" title="Lihat detail permission">
            <i class="fas fa-shield-alt icon-sm text-primary mr-2"></i> ${data}
        </a>`;
                    }
                }, {
                    data: 'direct_permissions_count',
                    name: 'direct_permissions_count',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var count = parseInt(data) || 0;
                        if (count > 0) {
                            var safeName = (row.name || '').replace(/"/g, '&quot;');
                            return `<a href="javascript:;" class="user-permission-badge label label-lg label-light-danger label-inline font-weight-bolder cursor-pointer" data-user-id="${row.id}" data-user-name="${safeName}" title="Kelola permission tambahan"><i class="fas fa-plus-circle icon-sm text-danger mr-1"></i> ${count}</a>`;
                        }
                        return '<span class="text-muted font-weight-bold">-</span>';
                    }
                }, {
                    data: 'department.name',
                    name: 'department.name',
                    render: function(data, type, row) {
                        return `
                <div class="d-flex align-items-center">
                    <span class="bullet bullet-bar bg-warning align-self-stretch mr-3"></span>
                    <span class="font-weight-bold text-dark-75">${data}</span>
                </div>`;
                    }
                }, {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return `<span class="label label-lg label-light-success label-inline font-weight-bold"><i class="fas fa-check-circle text-success icon-sm mr-2"></i>Aktif</span>`;
                        } else {
                            return `<span class="label label-lg label-light-danger label-inline font-weight-bold"><i class="fas fa-times-circle text-danger icon-sm mr-2"></i>Nonaktif</span>`;
                        }
                    }
                }, {
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let statusIcon = row.status == 1 ?
                            `<i class="fas fa-ban text-danger"></i>` :
                            `<i class="fas fa-check-circle text-success"></i>`;
                        let statusText = row.status == 1 ? 'Nonaktifkan User' : 'Aktifkan User';
                        let statusColor = row.status == 1 ? 'text-danger' : 'text-success';

                        // Menggunakan struktur dropdown Nav menu bawaan Metronic
                        return `
                <div class="dropdown dropdown-inline">
                    <button type="button" class="btn btn-sm btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog icon-sm"></i> Opsi
                    </button>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right shadow-sm border-0">
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-xs text-primary pb-2">
                                Pilih Aksi:
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link edit-user" data-id="${row.id}">
                                    <span class="navi-icon"><i class="fas fa-pen text-warning"></i></span>
                                    <span class="navi-text font-weight-bold">Edit User</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link kelola-permission-user" data-id="${row.id}" data-name="${(row.name || '').replace(/"/g, '&quot;')}">
                                    <span class="navi-icon"><i class="fas fa-key text-info"></i></span>
                                    <span class="navi-text font-weight-bold">Kelola Permission</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link nonaktifkan-user" data-id="${row.id}" data-status="${row.status}">
                                    <span class="navi-icon">${statusIcon}</span>
                                    <span class="navi-text font-weight-bold ${statusColor}">${statusText}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>`;
                    }
                }]
            });

            $('#createUserModal').on('show.bs.modal', function() {
                $('#form-create')[0].reset();
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
                                    table.ajax.reload(null,
                                        false); // Reload tanpa reset pagination
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

            // handle form edit 1 - GET DATA
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

                            $('#editUserId').val(user.id);
                            $('#editUsername').val(user.username);
                            $('#editName').val(user.name);
                            $('#editEmail').val(user.email);
                            $('#editPasword').val(user.password);
                            $('#editAuthGroupId').val(user.auth_group_id).trigger('change');
                            $('#editDepartmentId').val(user.dept_id).trigger('change');

                            $('#editUserModal').modal('show');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan saat mengambil data pengguna.',
                            'error');
                    }
                });
            });

            // handle form edit 2 - SUBMIT DATA
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
                            table.ajax.reload(null, false);
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

            // handle form create
            $('#form-create').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize();

                $.ajax({
                    url: "{{ route('master.user.store') }}",
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

        // ============================================
        // PERMISSION TAMBAHAN PER USER
        // ============================================

        // Buka modal & load data
        function userPermissions(userId, userName) {
            userName = userName || 'User';
            $("#user_permission_user_id").val(userId);
            $("#modal-user-permission-name").text(userName);

            // Reset modal body ke loading state
            $("#modal-user-permission-body").html(`
                <div class="text-center py-10">
                    <span class="spinner spinner-primary mb-2"></span>
                    <div class="text-muted font-weight-bold">Memuat permission...</div>
                </div>
            `);
            $("#modal-user-permission").modal("show");

            $.ajax({
                url: "{{ url('/master/user') }}/" + userId + "/show-permissions-modal",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId
                },
                success: function(response) {
                    if (typeof response === 'object' && response.success === 0) {
                        toastr.error(response.message || 'User tidak ditemukan', 'Error');
                        $("#modal-user-permission").modal("hide");
                        return;
                    }
                    $("#modal-user-permission-body").html(response);
                },
                error: function(xhr) {
                    var msg = 'Terjadi kesalahan saat memuat data permissions';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg, 'Error');
                    $("#modal-user-permission").modal("hide");
                }
            });
        }

        // Trigger buka modal: dari badge di kolom atau dari menu dropdown
        $(document).on('click', '.kelola-permission-user, .user-permission-badge', function(e) {
            e.preventDefault();
            var userId = $(this).data('user-id') || $(this).data('id');
            var userName = $(this).data('user-name') || $(this).data('name') || 'User';
            if (!userId) return;
            userPermissions(userId, userName);
        });

        // Search filter pada modal permission
        $(document).on('input', '#searchUserPermission', function() {
            var q = $(this).val().toLowerCase();
            $('.user-auth-permissions .permission-row').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(q) !== -1);
            });
        });

        // Submit form permission tambahan
        $(document).on('submit', '#change-user-permission-form', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner spinner-sm spinner-white mr-1"></span> Menyimpan...');

            $.ajax({
                url: "{{ url('/master/user/change-user-permissions') }}",
                type: 'POST',
                data: data,
                success: function(response) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Permission');
                    if (response.success == 1) {
                        toastr.success(response.message || 'Permission berhasil disimpan', 'Berhasil');
                        $("#modal-user-permission").modal("hide");
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error('Tidak bisa menyimpan data, periksa inputan Anda', 'Error');
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Permission');
                    if (xhr.status == 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(idx, msg) {
                            toastr.error(msg, 'Validasi Gagal');
                        });
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan saat menyimpan';
                        toastr.error(msg, 'Error');
                    }
                }
            });
        });

        // toggle buat edit
        function toggleEditPasswordVisibility() {
            let passwordInput = document.getElementById('editPasword');
            let toggleIcon = document.getElementById('edit-password-eye-icon');

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

        // Function groupPermissions
        function groupPermissions(id, groupName) {
            // 1. Set ID dan Title
            $("#hidden_group_id").val(id);
            $('#title-group-name').text('Mapping Hak Akses (' + groupName + ')');

            // 2. Loading State
            $(".auth-permissions").html(`
        <div class="d-flex flex-column justify-content-center align-items-center w-100" style="grid-column: 1 / -1; min-height: 200px;">
            <span class="spinner spinner-primary mb-3"></span>
            <span class="text-muted font-weight-bold">Memuat Detail Permission...</span>
        </div>
    `);

            $("#modal-view-permission").modal("show");

            // 3. Tembak AJAX
            $.ajax({
                url: '{{ URL::to('/permission/auth-group/get-permissions') }}',
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    let html = '';

                    // RENDER PERMISSION AKTIF
                    if (res.auth_permissions.length > 0) {
                        html += `
                <div class="separator-text text-primary mt-2 mb-2" style="grid-column: 1 / -1; font-weight: 800; font-size: 14px; border-bottom: 2px solid #E5E7EB; padding-bottom: 8px;">
                    <i class="fas fa-check-circle text-primary mr-2"></i> Permission Aktif
                </div>`;

                        res.auth_permissions.forEach(data => {
                            html += renderPermissionItem(data, true);
                        });
                    }

                    // RENDER PERMISSION TERSEDIA (BELUM AKTIF)
                    if (res.permissions_left.length > 0) {
                        html += `
                <div class="separator-text text-muted mt-6 mb-2" style="grid-column: 1 / -1; font-weight: 800; font-size: 14px; border-bottom: 2px solid #E5E7EB; padding-bottom: 8px;">
                    <i class="fas fa-box-open text-muted mr-2"></i> Tersedia (Belum Diaktifkan)
                </div>`;

                        res.permissions_left.forEach(data => {
                            html += renderPermissionItem(data, false);
                        });
                    }

                    // Kalau kosong melompong
                    if (html === '') {
                        html = `
                <div class="text-center py-10 w-100" style="grid-column: 1 / -1;">
                    <h5 class="text-muted font-weight-bold">Belum Ada Hak Akses Master</h5>
                </div>`;
                    }

                    $(".auth-permissions").html(html);
                },
                error: function(err) {
                    $(".auth-permissions").html(`
                <div class="text-center text-danger py-10 w-100" style="grid-column: 1 / -1;">
                    <h6 class="font-weight-bolder">Oopss... Gagal Memuat Data!</h6>
                </div>
            `);
                }
            });
        }

        // 1. Fungsi render template kotak Checkbox (Kembalikan ke aslinya)
        function renderPermissionItem(data, isChecked) {
            return `
    <li class="permission-item ${isChecked ? 'selected' : ''}">
        <label class="checkbox checkbox-primary m-0 w-100 cursor-pointer d-flex align-items-center">
            <input type="checkbox" name="permissions[]" ${isChecked ? 'checked' : ''} value="${data.id}" class="perm-checkbox">
            <span></span>
            <div class="ml-3 text-dark-75 font-weight-bolder" style="font-size: 13px;">${data.codename}</div>
        </label>
    </li>`;
        }

        // 2. Efek highlight ketika dicentang / di-uncheck (Hanya pakai class 'selected')
        $(document).on('change', '.perm-checkbox', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.permission-item').addClass('selected');
            } else {
                $(this).closest('.permission-item').removeClass('selected');
            }
        });

        // Aksi Submit Form
        $('#change-permission-form').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();

            Swal.fire({
                title: 'Menyimpan Perubahan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ URL::to('/permission/auth-group/change-permissions') }}',
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Permission Group berhasil diperbarui!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modal-view-permission').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', 'Tidak bisa menyimpan data', 'error');
                    }
                },
                error: function(error) {
                    Swal.close();
                    if (error.status == 422) {
                        Swal.fire('Validasi Gagal', 'Silakan periksa kembali inputan Anda', 'warning');
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan data', 'error');
                    }
                }
            });
        });
    </script>
@endpush
