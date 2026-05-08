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

    table tbody tr td {
        padding-top: 4px !important;
        padding-bottom: 4px !important;
    }

    .swal2-icon {
        margin: auto !important;
    }

    /* Override Body Background agar kartu terlihat pop-out */
    .content {
        background-color: #F9FAFB !important;
    }

    /* ---- HEADER STYLE (Matching Loker) ---- */
    .bas-header {
        background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: var(--bas-radius-lg);
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

    /* ---- TABLE CARD & DATATABLE ---- */
    .bas-tab-card {
        background: var(--bas-surface) !important;
        border: 1.5px solid var(--bas-border);
        border-radius: var(--bas-radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* PERBAIKAN: Selector diubah dari #table_permission menjadi #table-user */
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
        padding: 18px 15px;
        border: none;
    }

    #table-user td {
        padding: 16px 15px;
        border-bottom: 1px solid var(--bas-border);
        color: var(--bas-dark);
        vertical-align: middle;
    }

    #table-user tbody tr:hover {
        background-color: #FFFBEB !important;
        /* Soft yellow hover */
    }

    /* ---- CUSTOM BUTTONS ---- */
    .bas-btn-primary {
        background: var(--bas-primary);
        border: none;
        color: #FFFFFF;
        font-weight: 600;
        border-radius: var(--bas-radius-md);
        transition: var(--bas-transition);
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

    .permission-item.selected::before {
        content: '✓';
        position: absolute;
        top: 5px;
        right: 8px;
        font-size: 10px;
        color: var(--bas-primary);
        font-weight: bold;
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

    #modal-change-permission .modal-body {
        background-color: #FDFDFD;
        max-height: 65vh;
        overflow-y: auto;
        padding: 25px !important;
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
                        <a onClick="groupPermissions('${row.group.id}', '${data}')" href="javascript:;"
                            class="label label-lg label-light-primary label-inline font-weight-bolder cursor-pointer text-hover-primary"
                            data-toggle="tooltip" title="Lihat detail permission">
                            <i class="fas fa-shield-alt icon-sm text-primary mr-2"></i> ${data}
                        </a>`;
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
                            `<i class="fas fa-ban text-danger"></i>` : `<i class="fas fa-check-circle text-success"></i>`;
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

        // Function groupPermissions dan change permission tetap sama
        function groupPermissions(id, groupName) {
        // 1. Tampilkan Loading State yang rapi di tengah
        $('#title-group-name').text('Detail Hak Akses Group (' + groupName + ')');

        $(".auth-permissions").html(`
        <div class="d-flex justify-content-center align-items-center w-100" style="grid-column: 1 / -1; min-height: 250px;">
            <span class="spinner spinner-primary mr-5"></span>
            <span class="text-muted font-weight-bold font-size-lg">Memuat Detail Permission...</span>
        </div>
        `);

        $("#modal-view-permission").modal("show");

        $.ajax({
        url: '{{ URL::to('/permission/auth-group/get-permissions') }}',
        type: 'POST',
        data: {
        id: id,
        _token: '{{ csrf_token() }}'
        },
        success: function(res) {
        let html = '';

        if (res.auth_permissions.length > 0) {
        res.auth_permissions.forEach(data => {
        // Ceklis dan style tambahan dihapus, murni pakai class .selected dari CSS lu
        html += `
        <li class="permission-item selected shadow-sm" style="cursor: default;">
            <div class="text-dark-75 font-weight-bolder" style="word-break: break-all;">
                ${data.codename}
            </div>
        </li>`;
        });
        } else {
        // 2. Tampilan Empty State (Jika belum ada permission)
        html = `
        <div class="text-center py-10 w-100" style="grid-column: 1 / -1;">
            <div class="symbol symbol-light-secondary symbol-75 mb-4">
                <span class="symbol-label">
                    <i class="fas fa-folder-open text-muted font-size-h1"></i>
                </span>
            </div>
            <h5 class="text-muted font-weight-bold">Belum Ada Hak Akses</h5>
            <p class="text-muted font-size-sm mb-0">Group ini belum memiliki permission yang terhubung.</p>
        </div>`;
        }

        $(".auth-permissions").html(html);
        },
        error: function(err) {
        // 3. Tampilan Error State (Jika koneksi/query gagal)
        $(".auth-permissions").html(`
        <div class="text-center text-danger py-10 w-100" style="grid-column: 1 / -1;">
            <i class="fas fa-exclamation-triangle font-size-h1 text-danger mb-3"></i>
            <h6 class="font-weight-bolder">Oopss... Gagal Memuat Data!</h6>
            <span class="text-muted font-size-sm">Silakan tutup modal dan coba lagi.</span>
        </div>
        `);
        }
        });
        }
        // function renderPermissionItem(data, isChecked) {
        //     return `
        //     <li class="permission-item ${isChecked ? 'selected' : ''}">
        //         <label class="checkbox checkbox-primary m-0 w-100 cursor-pointer">
        //             <input type="checkbox" name="permissions[]" ${isChecked ? 'checked' : '' } value="${data.id}"
        //                 class="perm-checkbox">
        //             <span></span>
        //             <div class="ml-3 text-dark-75 font-weight-bolder">${data.codename}</div>
        //         </label>
        //     </li>`;
        // }

        // $(document).on('change', '.perm-checkbox', function () {
        //     if($(this).is(':checked')){
        //         $(this).closest('.permission-item').addClass('selected');
        //     } else {
        //         $(this).closest('.permission-item').removeClass('selected');
        //     }
        // })

        // $('#change-permission-form').submit(function(e) {
        //     e.preventDefault();
        //     var data = $(this).serialize();
        //     $.ajax({
        //         url: '{{ URL::to('/permission/auth-group/change-permissions') }}',
        //         type: 'POST',
        //         data: data,
        //         success: function(response) {
        //             if (response.success == 1) {
        //                 setTimeout(function() {
        //                     location.reload();
        //                 }, 500);
        //             } else {
        //                 alert("Tidak bisa menyimpan data, silahkan periksa inputan anda");
        //             }
        //         },
        //         error: function(error) {
        //             if (error.status == 422) {
        //                 $('.help-block').text('');
        //                 $.each(error.responseJSON.errors, (index, item) => {
        //                     $('._' + index + ' .help-block').text(item);
        //                 });
        //             }
        //         }
        //     })
        // });
</script>
@endpush
