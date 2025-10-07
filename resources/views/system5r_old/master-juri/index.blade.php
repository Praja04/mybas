@extends('system5r.layouts.base')
@section('title', 'Dashboard')
@section('content')
    <style>
        .badge-success {
            background-color: green;
            color: white;
        }
        .badge-danger {
            background-color: red;
            color: white;
        }
        th.no-column {
            padding-left: 20px;
            padding-right: 20px;
        }
        #juriTable td {
            font-size: 15px;
        }
        .badge {
            font-size: 14px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <div class="col-lg-12">
        <div class="px-8">
            <div class="card mx-auto">
                <div class="card-header">
                    <h1 class="card-title pb-4 display-3">DATA MASTER JURI</h1>
                </div>
                <div class="card-body">
                    <button id="createGroupButton" class="btn btn-dark bg-gradient waves-effect waves-light mb-3"><i class="fas fa-user"></i> Tambah Group
                        Juri</button>
                    <table id="juriTable" class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>ID Group</th>
                                <th>Departemen</th>
                                <th>Nama Group</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Isi tabel -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk Membuat Group Juri baru -->
    <div id="modalCreateGroup" class="modal fade" tabindex="-1" aria-labelledby="modalCreateGroupLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateGroupLabel">Tambah Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="formCreateGroup">
                        @csrf
                        <div class="form-group mb-3">
                            <label style="display: none" for="">Id Group Juri</label>
                            <input required type="hidden" name="id_group_juri" class="form-control"
                            <input required type="hidden" name="id_group_juri" class="form-control"
                                placeholder="Nama Group">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Nama Group</label>
                            <input required type="text" name="nama_group" class="form-control"
                                placeholder="masukkan nama group">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Keterangan</label>
                            <input required type="text" name="keterangan" class="form-control"
                                placeholder="masukkan nama">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Is Active</label>
                            <select required name="is_active" class="form-control">
                                <option value="Y">Aktif</option>
                                <option value="N">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-content-save"></i>
                            SIMPAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk Membuat Group Anggota Juri baru -->
    <div id="modalcreateAnggota" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Group Anggota Juri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <button type="button" class="btn btn-danger" id="anggotaJuribtn">
                            <i class="mdi mdi-account-multiple-plus"></i>
                            + tampilkan form tambah juri
                        </button>
                    </div>
                    <form id="formjuriDepartment" action="" method="POST">
                        <div class="form-group mb-3">
                            <label for="id_group_juri">ID Group Juri:</label>
                            <input type="text" class="form-control" id="id_group_juri" name="id_group_juri">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nik_juri">NIK Juri:</label>
                            <input type="text" class="form-control" id="nik_juri" name="nik_juri">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_juri">Nama Juri:</label>
                            <input type="text" class="form-control" id="nama_juri" name="nama_juri">
                        </div>
                        <div class="form-group mb-3">
                            <label for="is_active">Status:</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="Y">Aktif</option>
                                <option value="N">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-success" id="submitForm">
                            <i class="mdi mdi-content-save"></i>
                            SIMPAN
                        </button>
                    </form>
                </div>
                <div class="p-4">
                    <table class="table table-responsive" id="groupAnggotajuri">
                        <thead>
                            <tr>
                                <th>ID Group Juri</th>
                                <th>NIK Juri</th>
                                <th>Nama Juri</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk membuat Group Juri departemen baru -->
    <div id="modalGroupJuriDepartment" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Group Juri Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGroupJuriDepartment" action="" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="id_group_juri">ID Group Juri:</label>
                            <input type="text" class="form-control" id="id_group_juri" name="id_group_juri">
                        </div>
                        {{-- looping dari departments --}}
                        <div class="form-group mb-3">
                            <label for="id_department">Pilih Department:</label>
                            <select class="form-control" id="id_department" name="id_department">
                                <option value="">Pilih</option>
                                @foreach ($departments as $departemen)
                                    <option value="{{ $departemen->id_department }}">{{ $departemen->id_department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- looping dari jadwal --}}
                        <div class="form-group mb-3">
                            <label for="id_periode">ID Periode:</label>
                            <select class="form-control" id="id_periode" name="id_periode">
                                <option value="">pilih</option>
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->id_periode }}">
                                        {{ $periode->nama_periode . ' | ' . $periode->jadwal->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-success" id="submitDept">
                            <i class="mdi mdi-content-save"></i>
                            SIMPAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk set status -->
    <div id="statusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="statusModalLabel">Aktifasi Juri?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="form-group">
                        <input type="checkbox" id="toggleStatus" data-toggle="toggle" data-on="Aktif"
                            data-off="Tidak Aktif" data-onstyle="success" data-offstyle="danger" data-width="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://kit.fontawesome.com/e1f618f385.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // get data master juri
            var table = $('#juriTable').DataTable({
                paging: false,
                responsive: true,
                dom: '<"toolbar">frtip',
                order: [
                    [1, "desc"]
                ],
                columnDefs: [{
                    targets: 4,
                    type: "date-eu"
                }],
                ajax: {
                    url: '{{ route('5r-system.data.juri') }}',
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            var pageNum = table.page.info().page;
                            var index = meta.row + (pageNum * table.page.info().length) + 1;
                            return index;
                        },
                        visible: false 
                    },
                    {
                        data: 'id_group_juri',
                        visible: false
                    },
                    {
                        data: 'id_department',
                        render: function(data, type, row) {
                            if (data.length === 0) {
                                var groupJuriInfo = 'Group Juri: ' + row.id_group_juri + ' (' + row
                                    .id_department + ')';
                                return '<a href="#" class="group-juri-departemen" data-id="' + row
                                    .id_group_juri + '">' +
                                    '<i class="fas fa-folder-open" style="color: orange; padding-right: 5px;"></i>' +
                                    '<span style="color: blue;">' +
                                    ' - Silahkan lengkapi group juri dahulu</span>' +
                                    '</a>';
                            } else {
                                console.log(row);
                                return data;
                            }
                        }
                    },
                    {
                        data: 'nama_group',
                    },
                    {
                        data: 'keterangan',
                    },
                    {
                        data: 'is_active',
                        render: function(data, type, row) {
                            var badgeClass = (data === 'Y') ? 'badge badge-success' :
                                'badge badge-danger';
                            var badgeText = (data === 'Y') ? 'Aktif' : 'Tidak Aktif';
                            return '<span class="' + badgeClass +
                                ' clickable-badge toggle-status" data-id="' + row
                                .id_group_juri +
                                '" data-status="' + data + '" style="cursor: pointer;">' +
                                badgeText + '</span>';
                        }
                    },
                    {
                        data: 'nama_juri',
                        render: function(data, type, row) {
                            var juriNames = data ? data.split(', ') : [];
                            var orderedList =
                                '<ol style="list-style-type: none; padding: 10px 0;">';
                            if (juriNames.length === 0) {
                                orderedList +=
                                    '<li style="display: flex; align-items: center; padding: 5px 0;"><span class="group-juri-anggota" data-id="' +
                                    row.id_group_juri +
                                    '" style="color: black; cursor: pointer;"><i class="fas fa-user" style="color: black; padding-right: 5px; font-size: 20px;"></i>Tambahkan Anggota Juri +</span></li>';
                            } else {
                                for (var i = 0; i < juriNames.length; i++) {
                                    orderedList +=
                                        '<li style="display: flex; align-items: center; padding: 5px 0;"><a href="#" class="group-juri-anggota" data-id="' +
                                        row.id_group_juri +
                                        '" style="color: black;"><i class="fas fa-user" style="color: black; padding-right: 5px; font-size: 20px;"></i>' +
                                        juriNames[i] +
                                        '</a></li>';
                                }
                            }
                            orderedList += '</ol>';
                            return orderedList;
                        }
                    },
                ],
                lengthMenu: [10, 25, 50, 100]
            });
            $("div.toolbar").html(
                '<div class="dataTables_length" style="display:inline-block;margin-right:10px;">Show entries <select class="form-control input-sm" id="pageLength"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></div>'
            );
            $('#createGroupButton').click(function() {
                $('#modalCreateGroup').modal('show');
            });
            // create anggota juri
            $('#formCreateGroup').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '{{ route('5r-system.store.group') }}',
                    data: $('#formCreateGroup').serialize(),
                    success: function(response) {
                        if (response.success === 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(function() {
                                $('#modalCreateGroup').modal(
                                    'hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim permintaan.',
                        });
                    }
                });
            });
            // buat group juri anggota
            $(document).ready(function() {
                var groupAnggotajuriTable;
                $('#juriTable').on('click', '.group-juri-anggota', function(e) {
                    e.preventDefault();
                    var id_group_juri = $(this).data('id');
                    $('input[name=id_group_juri]').val(id_group_juri);
                    if ($.fn.DataTable.isDataTable('#groupAnggotajuri')) {
                        $('#groupAnggotajuri').DataTable().destroy();
                    }
                    //ajax
                    groupAnggotajuriTable = $('#groupAnggotajuri').DataTable({
                        ajax: {
                            url: '/5r-system/master-juri/group-juri/get/' + id_group_juri,
                            dataSrc: ''
                        },
                        paging: false,
                        responsive: true,
                        columns: [{
                                data: 'id_group_juri',
                                title: 'Group Juri'
                            },
                            {
                                data: 'nik_juri',
                                title: 'NIK Juri'
                            },
                            {
                                data: 'nama_juri',
                                title: 'Nama Juri'
                            },
                            {
                                data: 'is_active',
                                title: 'Status',
                                render: function(data, type, row) {
                                    var badgeClass = (data === 'Y') ?
                                        'badge badge-success' :
                                        'badge badge-danger';
                                    var badgeText = (data === 'Y') ? 'Aktif' :
                                        'Tidak Aktif';
                                    return '<span class="' + badgeClass +
                                        ' clickable-badge" data-id="' +
                                        row
                                        .id_group_juri +
                                        '" data-status="' + data +
                                        '" style="cursor: pointer;">' +
                                        badgeText + '</span>';
                                }
                            },
                            {
                                data: null,
                                title: 'Action',
                                render: function(data, type, full, meta) {
                                    var deleteButton =
                                        '<a href="#" data-nik="' + data.nik_juri +
                                        '" class="delete-juri" data-id="' +
                                        data.id_group_juri +
                                        '"><i class="fa fa-trash-o" style="font-size:48px;color:red"></i></a>';
                                    return deleteButton;
                                }
                            }
                        ]
                    });
                    $('#modalcreateAnggota').modal('show');
                });
                // delete juri
                $("#submitForm").click(function() {
                    var formjuriDepartment = $("#formjuriDepartment").serialize();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('5r-system.juri.anggota') }}',
                        data: formjuriDepartment,
                        success: function(response) {
                            if (response.success === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                }).then(function() {
                                    $('#modalcreateAnggota').modal('hide');
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim permintaan.',
                            });
                        }
                    });
                });
                // delete juri
                $('#groupAnggotajuri').on('click', '.delete-juri', function(e) {
                    e.preventDefault();
                    var id_group_juri = $(this).data('id');
                    var nik = $(this).data('nik')
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda ingin menonaktifkan juri ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "DELETE",
                                url: '/5r-system/master-juri/anggota-juri/delete',
                                data: {
                                    id_group_juri: id_group_juri,
                                    nik: nik
                                },
                                success: function(response) {
                                    if (response.success === 1) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Sukses',
                                            text: response.message,
                                        }).then(function() {
                                            groupAnggotajuriTable.ajax
                                                .reload();
                                            table.ajax.reload()
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message,
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan saat mengirim permintaan.',
                                    });
                                }
                            });
                        }
                    });
                });
            });
            // create group juri depeartemen anggota
            $(document).ready(function() {
                $('#juriTable').on('click', '.group-juri-departemen', function(e) {
                    e.preventDefault();
                    var id_group_juri = $(this).data('id');
                    $('input[name=id_group_juri]').val(id_group_juri);
                    $('#modalGroupJuriDepartment').modal('show');
                });
                $("#submitDept").click(function() {
                    var formGroupJuriDepartment = $("#formGroupJuriDepartment").serialize();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('5r-system.juri.departemen') }}',
                        data: $('#formGroupJuriDepartment').serialize(),
                        success: function(response) {
                            if (response.success === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                }).then(function() {
                                    $('#modalGroupJuriDepartment').modal(
                                        'hide');
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim permintaan.',
                            });
                        }
                    });
                });
            });
            // change status anggota group
            $(document).ready(function() {
                $('#statusModal').on('change', '#toggleStatus', function() {
                    console.log('Halos')
                    var id_group_juri = $('#statusModal').data('id');
                    var currentStatus = $('#statusModal').data('currentStatus');
                    var newStatus = $(this).prop('checked') ? 'Y' : 'N';
                    if (newStatus !== currentStatus) {
                        $.ajax({
                            type: "POST",
                            url: '{{ route('5r-system.juri.status') }}',
                            data: {
                                id: id_group_juri,
                                is_active: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload()
                                    $('#statusModal').modal('hide');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat mengirim permintaan.',
                                });
                            }
                        });
                    }
                });
                $('#juriTable').on('click', '.toggle-status', function() {
                    var id_group_juri = $(this).data('id');
                    var currentStatus = $(this).data('status');
                    if (currentStatus == 'Y') {
                        $('#statusModalLabel').text('Nonaktifkan Status');
                        // $('#').bootstrapToggle('on');
                        $('#toggleStatus').prop('checked', true).bootstrapToggle('destroy')
                            .bootstrapToggle();
                    } else {
                        // $('#').bootstrapToggle('off');
                        $('#toggleStatus').prop('checked', false).bootstrapToggle('destroy')
                            .bootstrapToggle();
                        $('#statusModalLabel').text('Aktifkan Status');
                    }
                    $('#statusModal').data('id', id_group_juri);
                    $('#statusModal').data('currentStatus', currentStatus);
                    $('#statusModal').modal('show');
                });
            });
            // get modal
            $('#juriTable').on('click', '.edit-link', function() {
                var juriId = $(this).data('id');
                //    get data juri by id nanti disini
                $('#modalEditJuri').modal('show');
            });
            $(document).ready(function() {
                var isFormVisible = false;
                $("#formjuriDepartment").hide();
                $("#anggotaJuribtn").click(function() {
                    if (isFormVisible) {
                        $("#formjuriDepartment").slideUp();
                    } else {
                        $("#formjuriDepartment").slideDown();
                    }
                    isFormVisible = !isFormVisible;
                });
            });
            // get anggota juri
        });
    </script>
@endpush