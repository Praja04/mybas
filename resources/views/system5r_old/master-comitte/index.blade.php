@extends('system5r.layouts.base')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* style button submit */
        .submit-data-committee {
            display: inline-block;
            padding: 2px 20px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #0db53f;
            border: 2px solid #000;
            border-radius: 10px;
            box-shadow: 5px 5px 0px #000;
            transition: all 0.3s ease;
            cursor: pointer;
            }

            .submit-data-committee:hover {
            background-color: #fff;
            color: #63ff52;
            border: 2px solid #1dc547;
            box-shadow: 5px 5px 0px #0faf3c;
            }

            .submit-data-committee:active {
            background-color: #fcf414;
            box-shadow: none;
            transform: translateY(4px);
            }

            .submit-edit-committee {
                display: inline-block;
                padding: 4px 20px;
                font-size: 24px;
                font-weight: bold;
                text-align: center;
                text-decoration: none;
                color: #fff;
                background-color: #ff5252;
                border: 2px solid #000;
                border-radius: 10px;
                box-shadow: 5px 5px 0px #000;
                transition: all 0.3s ease;
                cursor: pointer;
            }

                .submit-edit-committee:hover {
                    background-color: #fff;
                    color: #ff5252;
                    border: 2px solid #ff5252;
                    box-shadow: 5px 5px 0px #ff5252;
                }

                .submit-edit-committee:active {
                    background-color: #fcf414;
                    box-shadow: none;
                    transform: translateY(4px);
                }

                /* styling checkbox */
                .switch {
                /* switch */
                --switch-width: 66px;
                --switch-height: 34px;
                --switch-bg: rgb(131, 131, 131);
                --switch-checked-bg: rgb(0, 218, 80);
                --switch-offset: calc((var(--switch-height) - var(--circle-diameter)) / 2);
                --switch-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
                /* circle */
                --circle-diameter: 18px;
                --circle-bg: #fff;
                --circle-shadow: 1px 1px 2px rgba(146, 146, 146, 0.45);
                --circle-checked-shadow: -1px 1px 2px rgba(163, 163, 163, 0.45);
                --circle-transition: var(--switch-transition);
                /* icon */
                --icon-transition: all .2s cubic-bezier(0.27, 0.2, 0.25, 1.51);
                --icon-cross-color: var(--switch-bg);
                --icon-cross-size: 6px;
                --icon-checkmark-color: var(--switch-checked-bg);
                --icon-checkmark-size: 10px;
                /* effect line */
                --effect-width: calc(var(--circle-diameter) / 2);
                --effect-height: calc(var(--effect-width) / 2 - 1px);
                --effect-bg: var(--circle-bg);
                --effect-border-radius: 1px;
                --effect-transition: all .2s ease-in-out;
                }

                .switch input {
                display: none;
                }

                .switch {
                display: inline-block;
                }

                .switch svg {
                -webkit-transition: var(--icon-transition);
                -o-transition: var(--icon-transition);
                transition: var(--icon-transition);
                position: absolute;
                height: auto;
                }

                .switch .checkmark {
                width: var(--icon-checkmark-size);
                color: var(--icon-checkmark-color);
                -webkit-transform: scale(0);
                -ms-transform: scale(0);
                transform: scale(0);
                }

                .switch .cross {
                width: var(--icon-cross-size);
                color: var(--icon-cross-color);
                }

                .slider {
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                width: var(--switch-width);
                height: var(--switch-height);
                background: var(--switch-bg);
                border-radius: 999px;
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                position: relative;
                -webkit-transition: var(--switch-transition);
                -o-transition: var(--switch-transition);
                transition: var(--switch-transition);
                cursor: pointer;
                }

                .circle {
                width: var(--circle-diameter);
                height: var(--circle-diameter);
                background: var(--circle-bg);
                border-radius: inherit;
                -webkit-box-shadow: var(--circle-shadow);
                box-shadow: var(--circle-shadow);
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                -webkit-box-pack: center;
                -ms-flex-pack: center;
                justify-content: center;
                -webkit-transition: var(--circle-transition);
                -o-transition: var(--circle-transition);
                transition: var(--circle-transition);
                z-index: 1;
                position: absolute;
                left: var(--switch-offset);
                }

                .slider::before {
                content: "";
                position: absolute;
                width: var(--effect-width);
                height: var(--effect-height);
                left: calc(var(--switch-offset) + (var(--effect-width) / 2));
                background: var(--effect-bg);
                border-radius: var(--effect-border-radius);
                -webkit-transition: var(--effect-transition);
                -o-transition: var(--effect-transition);
                transition: var(--effect-transition);
                }

                /* actions */

                .switch input:checked+.slider {
                background: var(--switch-checked-bg);
                }

                .switch input:checked+.slider .checkmark {
                -webkit-transform: scale(1);
                -ms-transform: scale(1);
                transform: scale(1);
                }

                .switch input:checked+.slider .cross {
                -webkit-transform: scale(0);
                -ms-transform: scale(0);
                transform: scale(0);
                }

                .switch input:checked+.slider::before {
                left: calc(100% - var(--effect-width) - (var(--effect-width) / 2) - var(--switch-offset));
                }

                .switch input:checked+.slider .circle {
                left: calc(100% - var(--circle-diameter) - var(--switch-offset));
                -webkit-box-shadow: var(--circle-checked-shadow);
                box-shadow: var(--circle-checked-shadow);
                }

                /* styling table */
                .card {
                box-shadow: none;
                border: 1px solid #ddd;
                }

                .table {
                border-collapse: collapse;
                }

                .table thead {
                background-color: #f8f9fa;
                }

                .table thead th {
                border-bottom: none;
                }

                .table tbody td {
                border-top: none;
                }
                .table th, 
                .table td {
                padding: 8px 16px; 
                }

                .action-button {
                margin: 0 5px; 
                padding: 5px 10px; 
                }

                @media screen and (max-width: 768px) {
                .table-responsive {
                    overflow-x: auto;
                    }
                }


    </style>
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')

{{-- modal create master committee --}}
    <div class="modal fade" id="modaltambahDataCommittee" tabindex="-1" aria-labelledby="modaltambahDataCommitteeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaltambahDataCommitteeLabel">Tambah Master Committee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="store-data-comittee" method="post" enctype="multipart/form-data" action="">
                        @csrf
                    
                        <!-- Pilih Department -->
                        <div class="mb-3">
                            <label for="select-department" class="form-label">Pilih Department</label><span style="color: red"> *</span>
                            <select name="department" id="filter_department" class="form-control" style="padding-left: 15px" required>
                                <option value="">-- Pilih Department --</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <!-- NIK Committee -->
                        <div class="mb-3">
                            <label for="nik-committee" class="form-label">NIK Committee</label><span style="color: red"> *</span>
                            <input type="number" name="nik_committee" class="form-control" id="nik-committee" placeholder="Silahkan masukkan NIK dalam bentuk angka" required>
                        </div>
                    
                        <!-- Nama Committee -->
                        <div class="mb-3">
                            <label for="nama-committee" class="form-label">Nama Committee</label><span style="color: red"> *</span>
                            <input type="text" name="nama_committee" class="form-control" id="nama-committee" placeholder="Masukkan nama committee" required>
                        </div>
                    
                        <!-- Button Submit -->
                        <div class="d-grid">
                            <button type="submit" class="submit-data-committee">
                                <i class="ri-send-plane-fill"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{{-- modal edit master comittee --}}
    <div class="modal fade" id="modalEditDataCommittee" tabindex="-1" aria-labelledby="modalEditDataCommitteeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditDataCommitteeLabel">Edit Master Committee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-data-comittee" method="post" enctype="multipart/form-data" action="">
                        @csrf

                        <!-- Input Hidden untuk menyimpan ID -->
                        <input type="hidden" name="id" id="edit-id">
                        
                        <!-- Pilih Department -->
                        <div class="mb-3">
                            <label for="edit-select-department" class="form-label">Pilih Department</label><span style="color: red"> *</span>
                            <select name="department" id="edit_filter_department" class="form-control" style="padding-left: 15px" required>
                                <option value="">-- Pilih Department --</option>
                                @foreach ($department as $dept)
                                    <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <!-- NIK Committee -->
                        <div class="mb-3">
                            <label for="edit-nik-committee" class="form-label">NIK Committee</label><span style="color: red"> *</span>
                            <input type="number" name="nik_committee" class="form-control" id="edit-nik-committee" placeholder="Silahkan masukkan NIK dalam bentuk angka" required>
                        </div>
                    
                        <!-- Nama Committee -->
                        <div class="mb-3">
                            <label for="edit-nama-committee" class="form-label">Nama Committee</label><span style="color: red"> *</span>
                            <input type="text" name="nama_committee" class="form-control" id="edit-nama-committee" placeholder="Masukkan nama committee" required>
                        </div>
                    
                        <!-- Button Submit -->
                        <div class="d-grid">
                            <button type="submit" class="submit-edit-committee">
                                <i class="fas fa-pencil-alt"></i> Update
                            </button>                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

{{-- kode data table master committee --}}
<div class="col-lg-12" id="container">
    <div class="card">
        <div class="card-header">
            <h2>Data Master Committe</h2>
        </div>
        <div class="modal-section p-4">
            <button id="tombol-tambah-data-committee" type="button" class="btn btn-info waves-effect w-40 mt-2" data-bs-toggle="modal" data-bs-target="#modaltambahDataCommittee">
                <i class="ri-group-2-fill"></i>
                Tambah Committee
            </button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <table id="dataComittee" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col-lg-2" style="display: none">id</th>
                                        <th scope="col-lg-2">id_department</th>
                                        <th scope="col-lg-2">nik committee</th>
                                        <th scope="col-lg-2">nama committee</th>
                                        <th scope="col-lg-2">status</th>
                                        <th scope="col-lg-2">action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- append or query data disini --}}
                                </tbody>
                            </table>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- kode modal --}}
<div id="statusModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title" id="statusModalLabel">Aktifasi Comittee?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="form-group">
                    <label class="switch">
                        <input id="toggleStatus" type="checkbox" checked="" data-toggle="toggle" data-on="Aktif" data-off="Tidak Aktif" data-onstyle="success" data-offstyle="danger" data-width="100">
                        <div class="slider">
                            <div class="circle">
                                <svg class="cross" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path data-original="#000000" fill="currentColor" d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path>
                                    </g>
                                </svg>
                                <svg class="checkmark" xml:space="preserve" style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0" x="0" height="10" width="10" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path class="" data-original="#000000" fill="currentColor" d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/velzon/libs/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/js/pages/form-editor.init.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://kit.fontawesome.com/e1f618f385.js" crossorigin="anonymous"></script>
    {{-- data table --}}
    <script>
        // get data table
        $(document).ready(function() {
            var table = $('#dataComittee').DataTable({
                paging: true, 
                responsive: true,
                autoWidth: true,
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6 text-right"p>>',
                order: [
                    [4, "desc"]
                ],
                columnDefs: [{
                    targets: 1,
                    type: "date-eu"
                }],
                ajax: {
                    url: '{{ route('5r-system.get-data.comittee') }}',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id', visible: false },
                    { data: 'id_department' },
                    { data: 'nik_committee' },
                    { data: 'nama_committee' },
                    { 
                        data: 'is_active',
                        render: function(data, type, row) {
                            if (data === 'N') {
                                return '<span class="badge badge-soft-danger badge-border">Tidak Aktif</span>';
                            } else if (data === 'Y') {
                                return '<span class="badge badge-soft-success badge-border">Aktif</span>';
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'id', 
                        render: function(data, type, row) {
                            var editButton = '<button type="button" class="btn btn-warning btn-icon waves-effect waves-light editCommittee" data-id="' + data + '" data-department="' + row.id_department + '" data-nik="' + row.nik_committee + '" data-nama="' + row.nama_committee + '"><i class="ri-pencil-line"></i></button>';
                            var deleteButton = '<button type="button" class="btn btn-danger btn-icon waves-effect waves-light deleteDepartment" data-id="' + data + '"><i class="ri-delete-bin-5-line"></i></button>';
                            var changeStatusButton = '<button type="button" class="btn btn-primary btn-icon waves-effect waves-light changeStatus" data-id="' + data + '" data-status="' + row.is_active + '"><i class="ri-eye-line"></i></button>';

                            return editButton + '&nbsp;&nbsp;' + deleteButton + '&nbsp;&nbsp;' + changeStatusButton;
                        }
                    }
                ],
                pageLength: 8, 
                lengthMenu: [8, 25, 50, 100] 
            });
            
            $("div.toolbar").html(
                '<div class="dataTables_length" style="display:inline-block;margin-right:10px;">Show entries <select class="form-control input-sm" id="pageLength"><option value="8">8</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></div>'
            );
        });
    
        function deleteDepartment(id) {
            console.log('Delete department with ID:', id);
        }

        // store group master comittee
        $(document).ready(function() {
            $('#store-data-comittee').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Ingin menambahkan data comittee?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tambahkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("5r-system.store-data.comittee") }}',
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                if(response.status === 1) {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    );
                                    $('#dataComittee').DataTable().ajax.reload();
                                    $('#modaltambahDataCommittee').modal('hide');
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat mengirim data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

        // edit master comittee
        $(document).ready(function() {
            $('#edit-data-comittee').on('submit', function(e) {
                e.preventDefault();

                // Mengumpulkan data dari form
                var formData = $(this).serialize();

                // Konfirmasi sebelum melakukan edit
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Ingin mengedit user committee ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, edit!'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("5r-system.update-data.comittee") }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                console.log('Success Response:', response);

                                Swal.fire(
                                    'Berhasil!',
                                    'Data committee telah diperbarui.',
                                    'success'
                                ).then(function() {
                                    // Reload DataTable dan tutup modal
                                    $('#dataComittee').DataTable().ajax.reload();
                                    $('#modalEditDataCommittee').modal('hide');
                                });
                            },
                            error: function(response) {
                                console.log('Error Response:', response);

                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat memperbarui data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

        $(document).on('click', '.editCommittee', function() {
            var id = $(this).data('id');
            var department = $(this).data('department');
            var nik = $(this).data('nik');
            var nama = $(this).data('nama');

            // Isi form modal dengan data
            $('#edit-id').val(id);
            $('#edit_filter_department').val(department);
            $('#edit-nik-committee').val(nik);
            $('#edit-nama-committee').val(nama);

            // Tampilkan modal
            $('#modalEditDataCommittee').modal('show');
        });

        // change status comittee
        $(document).on('click', '.changeStatus', function() {
            var id = $(this).data('id');
            var currentStatus = $(this).data('status');
            $('#toggleStatus').prop('checked', currentStatus === 'Y');
            $('#statusModal').data('id', id).data('currentStatus', currentStatus);
            $('#statusModal').modal('show');
        });

        $('#statusModal').on('shown.bs.modal', function() {
            var id = $(this).data('id');
            console.log("ID comittee ini adalah:", id);
        });

        $('#toggleStatus').change(function() {
            var id = $('#statusModal').data('id');
            var newStatus = $(this).is(':checked') ? 'Y' : 'N';

            $.ajax({
                url: '{{ route("5r-system.ubah-status.comittee") }}',
                type: 'POST',
                data: { 
                    id: id, 
                    is_active: newStatus
                },
                success: function(response) {
                    Swal.fire(
                        'Berhasil diubah!',
                        'Status committee telah diubah.',
                        'success'
                    ).then(function() {
                        $('#dataComittee').DataTable().ajax.reload();
                        $('#statusModal').modal('hide');
                    });
                },
                error: function(xhr, status, error) {
                    console.log("Error:", xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat mengubah status.',
                        'error'
                    );
                }
            });
        });


        // delete master comittee
        $('#dataComittee').on('click', 'button.deleteDepartment', function() {
            var id = $(this).data('id'); 
            console.log(id);
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Ingin menghapus user committee ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("5r-system.delete-data.comittee") }}',
                        type: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Dihapus!',
                                'User committee telah dihapus.',
                                'success'
                            ).then(function() {
                                $('#dataComittee').DataTable().ajax.reload();
                            });
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush