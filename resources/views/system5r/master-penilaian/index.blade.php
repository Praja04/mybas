@extends('system5r.layouts.base')

@section('title', 'Master Penilaian')

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

        #periodePenilaian td {
            font-size: 15px;
        }

        .btn-tambah-tahun {
        --color: #560bad;
        font-family: inherit;
        display: inline-block;
        width: 10em;
        height: 2.6em;
        line-height: 2.5em;
        margin: 20px;
        position: relative;
        overflow: hidden;
        border: 2px solid var(--color);
        transition: color .5s;
        z-index: 1;
        font-size: 17px;
        border-radius: 6px;
        font-weight: 500;
        color: var(--color);
        }

        .btn-tambah-tahun:before {
        content: "";
        position: absolute;
        z-index: -1;
        background: var(--color);
        height: 150px;
        width: 210px;
        border-radius: 50%;
        }

        .btn-tambah-tahun:hover {
        color: #fff;
        }

        .btn-tambah-tahun:before {
        top: 100%;
        left: 100%;
        transition: all .7s;
        }

        .btn-tambah-tahun:hover:before {
        top: -30px;
        left: -18.8px;
        }

        .btn-tambah-tahun:active:before {
        background: #3a0ca3;
        transition: background 0s;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="button-show-penilaian">
            <button id="showScheduleButton" class="btn-tambah-tahun">Tambahkan Tahun</button>
        </div>
        <div id="scheduleContent" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                            <div class="col-md-4">
                                <img id="scheduleImage" src="{{ asset('/assets/velzon/images/ilustrasi_kalender.png') }}" alt="Schedule Image"
                                    style="width: 100%; max-width: 300px;" class="shake-image">
                            </div>
                            <div class="col-md-6">
                            <div class="title mt-4">
                                <h2>Input Tahun</h2>
                            </div>
                            <form method="POST" id="formTambahTahun" class="needs-validation" novalidate>
                                @csrf
                                <div class="form-group">
                                    <input required type="number" class="form-control" id="tahun" name="tahun"
                                        min="{{ date('Y') }}" max="{{ date('Y') + 10 }}" oninvalid="this.setCustomValidity('Tahun Periode Penilaian tidak boleh kosong')" oninput="this.setCustomValidity('')">
                                    <div class="invalid-feedback">Tahun Periode Penilaian tidak boleh kosong</div>
                                </div>                            
                                <div class="mt-4">
                                    <button type="submit" id="simpanButton" class="btn btn-success">
                                        <i class="mdi mdi-content-save ml-2"></i>Simpan
                                    </button>
                                </div>
                            </form>
                    </div>                        
                </div>
                </div>
            </div>
    </div>

    {{-- buat modal untuk create periode penilaian --}}
    <div id="modalPeriodePenilaian" class="modal fade" tabindex="-1" aria-labelledby="modalPenilaianLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateGroupLabel">Tambah Periode Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="formPeriodePenilaian">
                        @csrf

                        <div class="mb-3">
                            <label for="id_jadwal" class="form-label">ID Jadwal</label>
                            <input type="text" class="form-control" id="id_jadwal" name="id_jadwal" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_periode" class="form-label">Nama Periode</label>
                            <input type="text" class="form-control" id="nama_periode" name="nama_periode" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                        </div>

                        <button type="button" class="btn btn-success" id="submitPeriode">
                            <i class="mdi mdi-content-save"></i>
                            SIMPAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- buat modal untuk delete --}}


    
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-4">
                <!-- Pembungkus tabel dengan .table-responsive -->
                <div class="table-responsive">
                    <table class="table table-bordered border-secondary table-nowrap" id="periodePenilaian">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tahun</th>
                                <th>Periode</th>
                                <th>Keterangan</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- isi data periode penilaian disini --}}
                        </tbody>
                    </table>
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
    <script>
        // hide show tambah tahun
        $(document).ready(function() {
            $('#showScheduleButton').click(function() {
                var isContentVisible = $('#scheduleContent').is(":visible");
                $('#scheduleContent').slideToggle('slow');
                if (!isContentVisible) {
                    $(this).html('<i class="fas fa-times" style="padding-right: 5px;"></i>Tutup Form');
                } else {
                    $(this).html('Tambahkan Tahun');
                }
            });
        });

        // submit btn
        $(document).ready(function() {
            $('#formTambahTahun').submit(function(e) {
                e.preventDefault();

                // validasi form nya
                if (this.checkValidity() === false) {
                    e.stopPropagation();
                    $(this).addClass('was-validated'); 
                    return; 
                }

                $.ajax({
                    type: "POST",
                    url: '{{ route('5r-system.store.tahun') }}',
                    data: $(this)
                        .serialize(),
                    success: function(response) {
                        if (response.success === 1) {
                            // Reload tabel DataTable
                            var table = $('#periodePenilaian').DataTable();
                            table.ajax.reload();

                            Swal.fire({
                                title: 'Pesan dari bangjoy',
                                text: 'Mantap Anda berhasil membuat jadwal penilaian.',
                                iconHtml: '<img src="{{ asset('/assets/velzon/images/otaku.png') }}" style="width: 48px;">',
                                iconColor: 'green',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    shakeImage();
                                }
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
                            title: 'Pesan dari bangjoy',
                            text: 'Waduh Jadwal penilaian nya udah ada bro.',
                            iconHtml: '<img src="{{ asset('/assets/velzon/images/otakusedih.png') }}" style="width: 48px;">',
                            iconColor: 'red',
                        });
                    }
                });
            });

            // Fungsi untuk membuat gambar bergoyang
            function shakeImage() {
                const image = $('#scheduleImage');
                let isLeft = true;
                let left = 0;
                const interval = setInterval(function() {
                    if (isLeft) {
                        left -= 5;
                    } else {
                        left += 5;
                    }
                    image.css('margin-left', left + 'px');
                    isLeft = !isLeft;
                }, 100);

                setTimeout(function() {
                    clearInterval(interval);
                    image.css('margin-left', '0px');
                }, 3000);
            }

            // get data all periode
            $(document).ready(function() {
                var table = $('#periodePenilaian').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('5r-system.data.periode') }}",
                        type: 'GET',
                    },
                    columns: [{
                            data: 'id_jadwal',
                        },
                        {
                            data: 'tahun',
                        },
                        {
                            data: 'nama_periode',
                            render: function(data, type, row) {
                                if (!data) {
                                    return '<a href="#" class="group-periode-penilaian" data-id="' +
                                        row.id_jadwal + '">' +
                                        '<i class="fas fa-folder-open" style="color: orange; padding-right: 5px;"></i>' +
                                        '<span style="color: blue;">Silahkan tambah periode penilaian</span>' +
                                        '</a>';
                                } else {
                                    var GroupPeriode = row
                                        .nama_periode;
                                    return '<a href="#" class="group-periode-penilaian" data-id="' +
                                        row.id_jadwal + '">' +
                                        '<i class="fas fa-folder-open" style="color: orange; padding-right: 5px;"></i>' +
                                        '<span style="color: blue;">' + GroupPeriode +
                                        '</span>' +
                                        '</a>';
                                }
                            }
                        },
                        {
                            data: 'keterangan',
                            render: function(data, type, row) {
                                if (!data || data === '') {
                                    return '-';
                                } else {
                                    var keteranganItems = data.split(',');
                                    var listHTML =
                                        '<ul style="list-style: none; padding-left: 0;">';
                                    for (var i = 0; i < keteranganItems.length; i++) {
                                        listHTML +=
                                            '<li><i class="fas fa-file-alt" style="color: orange;"></i> ' +
                                            keteranganItems[i] + '</li>';
                                    }
                                    listHTML += '</ul>';

                                    return listHTML;
                                }
                            }
                        },
                        // {
                        //     data: null,
                        //     render: function(data) {
                        //         return '<button class="btn btn-sm btn-primary">Edit</button> ' +
                        //             '<button class="btn btn-sm btn-danger">Delete</button>';
                        //     },
                        //     orderable: false,
                        //     searchable: false,
                        // },
                    ],
                });
            });

            $(document).ready(function() {
                $('#periodePenilaian').on('click', '.group-periode-penilaian', function(e) {
                    e.preventDefault();
                    var id_jadwal = $(this).data('id');
                    $('input[name=id_jadwal]').val(id_jadwal);
                    $('#modalPeriodePenilaian').modal('show');
                });

                $("#submitPeriode").click(function() {
                    var formPeriodePenilaian = $("#formPeriodePenilaian").serialize();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('5r-system.tambah.periode') }}',
                        data: $('#formPeriodePenilaian').serialize(),
                        success: function(response) {
                            console.log("Response Success:", response);
                            if (response.success === 1) {
                                var table = $('#periodePenilaian').DataTable();
                                table.ajax.reload();
                                Swal.fire({
                                    title: 'Pesan dari bangjoy',
                                    text: 'Mantap Anda berhasil membuat jadwal penilaian.',
                                    iconHtml: '<img src="{{ asset('/assets/velzon/images/otaku.png') }}" style="width: 48px;">',
                                    iconColor: 'green',
                                    text: response.message,
                                }).then(function() {
                                    $('#modalPeriodePenilaian').modal(
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
                            console.log("Response Error:", xhr.responseText);
                            Swal.fire({
                                title: 'Pesan dari bangjoy',
                                text: 'Waduh Jadwal penilaian nya udah ada bro.',
                                iconHtml: '<img src="{{ asset('/assets/velzon/images/otakusedih.png') }}" style="width: 48px;">',
                                iconColor: 'red',
                            });
                        }
                    });
                });
            });
        });
    </script>
@endpush
