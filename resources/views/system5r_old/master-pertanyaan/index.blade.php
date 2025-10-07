@extends('system5r.layouts.base')

@section('title', 'Master Pertanyaan')

@push('styles')
    <style>
        /* Breakline datatable  */
        table.dataTable tr td {
            word-break: break-all;
            white-space: normal;
        }

        table.dataTable tr td p {
            margin-bottom: 0
        }

        /* styling button download master excel */
        .master-excel {
            position: relative;
            padding: 10px 20px;
            border-radius: 7px;
            border: 1px solid rgb(61, 255, 168);
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 2px;
            background: transparent;
            color: #0aad7a;
            overflow: hidden;
            box-shadow: 0 0 0 0 transparent;
            -webkit-transition: all 0.2s ease-in;
            -moz-transition: all 0.2s ease-in;
            transition: all 0.2s ease-in;
        }

        .master-excel:hover {
                background: rgb(14, 173, 101);
                box-shadow: 0 0 30px 5px rgba(0, 236, 161, 0.815);
                color: white;
                -webkit-transition: all 0.2s ease-out;
                -moz-transition: all 0.2s ease-out;
                transition: all 0.2s ease-out;
            }

            .master-excel:hover::before {
                -webkit-animation: sh02 0.5s 0s linear;
                -moz-animation: sh02 0.5s 0s linear;
                animation: sh02 0.5s 0s linear;
            }

            .master-excel::before {
                content: '';
                display: block;
                width: 0px;
                height: 86%;
                position: absolute;
                top: 7%;
                left: 0%;
                opacity: 0;
                background: #fff;
                box-shadow: 0 0 50px 30px #fff;
                -webkit-transform: skewX(-20deg);
                -moz-transform: skewX(-20deg);
                -ms-transform: skewX(-20deg);
                -o-transform: skewX(-20deg);
                transform: skewX(-20deg);
            }

            @keyframes sh02 {
            from {
                opacity: 0;
                left: 0%;
            }

            50% {
                opacity: 1;
            }

            to {
                opacity: 0;
                left: 100%;
            }
            }

            .master-excel:active {
                box-shadow: 0 0 0 0 transparent;
                -webkit-transition: box-shadow 0.2s ease-in;
                -moz-transition: box-shadow 0.2s ease-in;
                transition: box-shadow 0.2s ease-in;
            }

    </style>
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <h3 style="margin-bottom: 13px">PERTANYAAN</h3>
                    <div class="card border">
                        <div class="card-body">
                            <div class="form-group mb-1">
                                <label class="mb-0">Filter Department</label>
                                <select name="department" id="filter_department" class="form-control" style="border: none; padding-left: 0">
                                    <option value="NO">-- Pilih Department --</option>
                                    @foreach ($department as $dept)
                                        <option value="{{ $dept->id_department }}">{{ $dept->nama_department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="mb-0">Filter Group</label>
                                <select name="group" id="filter_group" class="form-control" style="border: none; padding-left: 0">
                                    <option value="NO">-- Pilih Group --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button id="tombol-tambah-pertanyaan" type="button" class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal" data-bs-target="#modalCreatePertanyaan">
                        <i class="mdi mdi-plus"></i>
                        TAMBAH PERTANYAAN
                    </button>
                    <button id="tombol-clone-pertanyaan" type="button" class="btn btn-secondary waves-effect btn-block w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalClonePertanyaan">
                        <i class="mdi mdi-content-copy"></i>
                        CLONE PERTANYAAN
                    </button>
                    <button id="tombol-import-excel" type="button" class="btn btn-success waves-effect btn-block w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                        <i class="ri-file-excel-2-line"></i>
                        IMPORT EXCEL
                    </button>
                    <button id="tombol-archive-data-pertanyaan" type="button" class="btn btn-info waves-effect btn-block w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalArchiveDataPertanyaan">
                        <i class="ri-inbox-archive-fill"></i>
                        ARCHIVE PERTANYAAN
                    </button>
                </div>
                <div class="col-md-10">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped shadow" id="table-pertanyaan" style="width: 100% !important">
                            <thead>
                                <tr style="background-color: #a80000; color: #fff">
                                    <th style="width: 20px">JENIS</th>
                                    <th>ITEM PERIKSA</th>
                                    <th>KETERANGAN</th>
                                    <th style="width: 5%">AKSI</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalCreatePertanyaan" class="modal fade" tabindex="-1" aria-labelledby="modalCreatePertanyaanLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreatePertanyaanLabel">Tambah Pertanyaan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-pertanyaan.store') }}" method="POST" id="formCreatePertanyaan">
                    <div class="row" style="display: none">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label for="department">Department</label>
                                <input class="form-control" type="text" name="id_department" id="department-input">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label for="group">Group</label>
                                <input class="form-control" type="text" name="id_group" id="group-input">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis">Jenis</label>
                        <select required name="jenis" id="jenis" class="form-control">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="RINGKAS">RINGKAS</option>
                            <option value="RAPI">RAPI</option>
                            <option value="RESIK">RESIK</option>
                            <option value="RAWAT">RAWAT</option>
                            <option value="RAJIN">RAJIN</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="item_periksa">Item Periksa</label>
                        {{-- <textarea name="item_periksa" id="item_periksa" class="form-control"></textarea> --}}
                        <div class="bubble-editor" id="item_periksa"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="keterangan">Keterangan</label>
                        {{-- <textarea name="keterangan" id="keterangan" class="form-control"></textarea> --}}
                        <div class="bubble-editor" id="keterangan"></div>
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

{{-- modal clone pertanyaan --}}
<div id="modalClonePertanyaan" class="modal fade" tabindex="-1" aria-labelledby="modalClonePertanyaanLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClonePertanyaanLabel">Clone Pertanyaan Dari Group Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-pertanyaan.clone') }}" method="POST" id="formClonePertanyaan">
                    <div class="form-group">
                        <select required name="id_group_target" id="id_group" class="form-control">
                            <option value="">PILIH</option>
                            @foreach ($group as $_item)
                            @if(isset($_item->department) && isset($_item->department->nama_department))
                            <option value="{{ $_item->id_group }}">{{ $_item->department->nama_department }} - {{ $_item->nama_group }} - {{ $_item->persentase }} - {{ $_item->is_active }}</option>
                            @else
                            <option value=""> data tidak ada </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success mt-4">
                        <i class="mdi mdi-content-save"></i>
                        MULAI CLONE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- modal edit pertanyaan --}}
<div id="modalEditPertanyaan" class="modal fade" tabindex="-1" aria-labelledby="modalEditPertanyaanLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPertanyaanLabel">Edit Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('5r-system.master-pertanyaan.update') }}" method="POST" id="formUpdatePertanyaan">
                    <input type="hidden" id="id_pertanyaan" name="id_pertanyaan">
                    <div class="form-group mb-3">
                        <label for="jenis">Jenis</label>
                        <select required name="jenis" id="edit-jenis" class="form-control">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="RINGKAS">RINGKAS</option>
                            <option value="RAPI">RAPI</option>
                            <option value="RESIK">RESIK</option>
                            <option value="RAWAT">RAWAT</option>
                            <option value="RAJIN">RAJIN</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="item_periksa">Item Periksa</label>
                        {{-- <textarea name="item_periksa" id="item_periksa" class="form-control"></textarea> --}}
                        <div class="bubble-editor" id="edit-item_periksa"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="keterangan">Keterangan</label>
                        {{-- <textarea name="keterangan" id="keterangan" class="form-control"></textarea> --}}
                        <div class="bubble-editor" id="edit-keterangan"></div>
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

<!-- Modal for Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportExcelLabel">Import Excel File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for file upload -->
                {{-- jika berupa file wajib masukkan enctype lalu name untuk request file nya --}}
                <form id="excel-import-form" method="post" enctype="multipart/form-data" action="{{ route('5r-system.master-pertanyaan.import-master-pertanyaan') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="excel-file-upload" class="form-label">Upload Excel File</label>
                        <input type="file" name="excel" class="form-control" id="excel-file-upload" accept=".xlsx, .xls">
                    </div>
                    <input type="hidden" id="id-group-import" name="id_group_import"> 
                    <div class="mt-3">
                        <h5 class="mb-4">Download Master Excel</h5>
                        <a href="{{ url('/master_import/template_form_penilaian_5r.xlsx') }}" class="master-excel" download>Download Excel</a>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-success" style="font-size: larger;">
                            <i class="ri-send-plane-fill" style="margin-right: 5px;"></i>
                            Submit
                        </button>
                    </div>                    
                </form>
                <!-- Add the download link below -->
            </div>
        </div>
    </div>
</div>


{{-- modal success --}}
<div id="success-archive" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="hover" style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4">Data Pertanyaan Berhasil di Archive</h4>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
    <script src="{{ asset('assets/velzon/libs/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/js/pages/form-editor.init.js') }}"></script>
    <script>
        var table = $('#table-pertanyaan').DataTable({
        ajax: {
            url: "{{ route('5r-system.master-pertanyaan.data') }}?department=" + $('#filter_department').val() + "&group=" + $('#filter_group').val(),
        },
        columns: [
            {
                data: 'jenis', 
                name: 'jenis', 
                render: function (data, type, row) {
                        return data;
                }
            },
            {
                data: 'item_periksa', 
                name: 'item_periksa', 
                render: function (data, type, row) {
                        return data.replace('||--||', '&');
                }
            },
            {
                data: 'keterangan', 
                name: 'keterangan', 
                render: function (data, type, row) {
                        return data.replace('||--||', '&');
                }
            },
            {
                data: null, 
                sortable: false, 
                searchable: false, 
                render: function (data, type, row) {
                        return '<button class="btn btn-warning btn-sm btn-edit waves-effect" onClick="doEdit(\''+row.id_pertanyaan+'\')" data-id="'+row.id_pertanyaan+'"><i class="mdi mdi-pencil"></i></button><br /><button class="btn btn-danger btn-sm btn-delete waves-effect mt-2" onClick="doHapus(\''+row.id_pertanyaan+'\')" data-id="'+row.id_pertanyaan+'"><i class="mdi mdi-delete"></i></button><br /><button class="btn btn-primary btn-sm btn-archive waves-effect mt-2" onClick="showArchiveModal(\''+row.id_pertanyaan+'\')" data-id="'+row.id_pertanyaan+'"><i class="ri-inbox-archive-fill"></i></button>';
                }
            },
        ]
    });

        // send parameter for import excel
        $(document).ready(function() {
            $('#excel-import-form').submit(function(e) {
                e.preventDefault();
                
                var fileInput = $('#excel-file-upload')[0];
                
                if (fileInput.files.length === 0) {
                    alert('Pilih file Excel terlebih dahulu.');
                    return;
                }

                var id_group = $('#filter_group').val();
                $('#id-group-import').val(id_group); 
                var id_group_import = $('#id-group-import').val(); 

                console.log('id_group_import:', id_group_import); 

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route("5r-system.master-pertanyaan.import-master-pertanyaan") }}',
                    data: formData,
                    success: function (response) {
                        // Handle success
                        if (response.success) {
                            Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Import berhasil dilakukan.'
                        }).then(function() {
                            $('#table-pertanyaan').DataTable().ajax.reload();
                            $('#modalImportExcel').modal('hide');
                        });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Import gagal: ' + response.message
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengirim permintaan.'
                        });
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        });



        function showArchiveModal(idPertanyaan)
        {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi',
                    text: 'Apakah anda yakin ingin archive pertanyaan ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, archive!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('5r-system.master-pertanyaan.archiveDataPertanyaan') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id_pertanyaan: idPertanyaan
                            },
                            success: function (res) {
                                if (res.status == 1) {
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
                                // table.ajax.reload();
                                $('#table-pertanyaan').DataTable().ajax.reload();
                            }
                        })
                    }
                })
        }

        $('#tombol-archive-data-pertanyaan').on('click', function () {
            var id_department = $('#filter_department').val();
            var id_group = $('#filter_group').val();

            if (id_department && id_group) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin archive semua pertanyaan ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, archive!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var data = {
                            id_department: id_department,
                            id_group: id_group,
                        };

                        $.ajax({
                            url: "{{ route('5r-system.master-pertanyaan.all-pertanyaan') }}",
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                if (response.status == 1) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                    $('#table-pertanyaan').DataTable().ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });
                                }
                            },
                            error: function (error) {
                                console.error(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat menghubungi server.'
                                });
                            }
                        });
                    }
                });
            } else {
                alert('Pilih departemen dan grup terlebih dahulu.');
            }
        });

        function doHapus(idPertanyaan)
        {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi',
                text: 'Apakah anda yakin ingin menghapus pertanyaan ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('5r-system.master-pertanyaan.delete') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_pertanyaan: idPertanyaan
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

        function doEdit(idPertanyaan)
        {
            $('#id_pertanyaan').val(idPertanyaan)

            var url = "{{ route('5r-system.master-pertanyaan.get', ['id_pertanyaan' => '_id_pertanyaan']) }}";

            $.ajax({
                url: url.replace('_id_pertanyaan', idPertanyaan),
                type: 'GET',
                success: function (res) {
                    if (res.status == 'success') {
                        $('#edit-jenis').val(res.data.jenis)
                        $('#edit-item_periksa .ql-editor').html(res.data.item_periksa)
                        $('#edit-keterangan .ql-editor').html(res.data.keterangan)
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
            })
            $('#modalEditPertanyaan').modal('show')
        }

        $('#filter_department').on('change', function () {
            var department = $(this).val();
            var url = "{{ route('5r-system.master-group.by-department', ['id_department' => '_department']) }}";

            $('#department-input').val(department);

            $.ajax({
                url: url.replace('_department', department),
                success: function (res) {
                    var html = '<option value="NO">-- Pilih Group --</option>';
                    res.data.forEach(function (item) {
                        html += '<option value="'+item.id_group+'">'+item.nama_group+'</option>';
                    })
                    $('#filter_group').html(html);
                }
            })

            refreshTable();
        });

        $('#department').on('change', function () {
            var department = $(this).val();
            var url = "{{ route('5r-system.master-group.by-department', ['id_department' => '_department']) }}";

            $.ajax({
                url: url.replace('_department', department),
                success: function (res) {
                    var html = '<option value="NO">-- Pilih Group --</option>';
                    res.data.forEach(function (item) {
                        html += '<option value="'+item.id_group+'">'+item.nama_group+'</option>';
                    })
                    $('#group').html(html);
                }
            })

            refreshTable();
        });

        $('#filter_group').on('change', function () {
            var group = $(this).val();

            $('#group-input').val(group);

            refreshTable();
        });

        refreshTable();

        function refreshTable()
        {
            var department = $('#filter_department').val();
            var group = $('#filter_group').val();

            if(department == 'NO') {
                group = 'NO';
            }

            if(department == 'NO' || group == 'NO') {
                $('#tombol-tambah-pertanyaan').hide()
                $('#tombol-clone-pertanyaan').hide()
                $('#tombol-import-excel').hide()
                $('#tombol-archive-data-pertanyaan').hide()
            }else{
                $('#tombol-tambah-pertanyaan').show()
                $('#tombol-clone-pertanyaan').show()
                $('#tombol-import-excel').show()
                $('#tombol-archive-data-pertanyaan').show()
            }

            table.ajax.url("{{ route('5r-system.master-pertanyaan.data') }}?department=" + department + "&group=" + group).load();
        }


        $('#formCreatePertanyaan').on('submit', function (e) {
            e.preventDefault()

            var form = $(this);
            var data = form.serialize();

            // Add item_periksa
            var item_periksa = $('#item_periksa .ql-editor').html();
            // Clear &gt; and &lt;
            item_periksa = item_periksa.replace(/&gt;/g, '>');
            item_periksa = item_periksa.replace(/&lt;/g, '<');
            // Change & to &amp;
            item_periksa = item_periksa.replace(/&/g, '||--||');
            data += '&item_periksa=' + item_periksa.replace(/"/g, "'");

            // Add keterangan
            var keterangan = $('#keterangan .ql-editor').html();
            // Clear &gt; and &lt;
            keterangan = keterangan.replace(/&gt;/g, '>');
            keterangan = keterangan.replace(/&lt;/g, '<');
            // Change & to &amp;
            keterangan = keterangan.replace(/&/g, '||--||');
            data += '&keterangan=' + keterangan.replace(/"/g, "'");

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: data,
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
                    // form.find('input').val('');
                    // Clear editor
                    $('#item_periksa .ql-editor').html('');
                    $('#keterangan .ql-editor').html('');
                    $('#modalCreatePertanyaan').modal('hide');
                }
            })
        })

        $('#formUpdatePertanyaan').on('submit', function (e) {
            e.preventDefault()

            var form = $(this);
            var data = form.serialize();

            // Add item_periksa
            var item_periksa = $('#edit-item_periksa .ql-editor').html();
            // Clear &gt; and &lt;
            item_periksa = item_periksa.replace(/&gt;/g, '>');
            item_periksa = item_periksa.replace(/&lt;/g, '<');
            item_periksa = item_periksa.replace(/&/g, '||--||');
            data += '&item_periksa=' + item_periksa.replace(/"/g, "'");

            // Add keterangan
            var keterangan = $('#edit-keterangan .ql-editor').html();
            // Clear &gt; and &lt;
            keterangan = keterangan.replace(/&gt;/g, '>');
            keterangan = keterangan.replace(/&lt;/g, '<');

            // Change & to other character
            keterangan = keterangan.replace(/&/g, '||--||');
            data += '&keterangan=' + keterangan.replace(/"/g, "'");

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: data,
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
                    // Refresh data without reload pagination
                    table.ajax.reload(null, false);
                    // form.find('input').val('');
                    // Clear editor
                    $('#edit-item_periksa .ql-editor').html('');
                    $('#edit-keterangan .ql-editor').html('');
                    $('#modalEditPertanyaan').modal('hide');
                }
            })
        })

        $('#formClonePertanyaan').on('submit', function (e) {
            e.preventDefault()

            var form = $(this);
            var data = form.serialize();
            var data = data + '&id_department=' + $('#filter_department').val() + '&id_group=' + $('#filter_group').val();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: data,
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
                    $('#modalClonePertanyaan').modal('hide');
                }
            })
        })
    </script>
@endpush
