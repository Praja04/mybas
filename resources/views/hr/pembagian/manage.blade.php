@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    <style>
        .btn-shine {
            position: relative;
            margin: 0;
            padding: 17px 35px;
            outline: none;
            text-decoration: none;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            text-transform: uppercase;
            background-color: #fff;
            border: 1px solid rgba(22, 76, 167, 0.6);
            border-radius: 10px;
            color: #1d89ff;
            font-weight: 400;
            font-family: inherit;
            z-index: 0;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.02, 0.01, 0.47, 1);
        }

        .btn-shine span {
            color: #164ca7;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.7px;
        }

        .btn-shine:hover {
            animation: rotate624 0.7s ease-in-out both;
        }

        .btn-shine:hover span {
            animation: storm1261 0.7s ease-in-out both;
            animation-delay: 0.06s;
        }

        @keyframes rotate624 {
            0% {
                transform: rotate(0deg) translate3d(0, 0, 0);
            }

            25% {
                transform: rotate(3deg) translate3d(0, 0, 0);
            }

            50% {
                transform: rotate(-3deg) translate3d(0, 0, 0);
            }

            75% {
                transform: rotate(1deg) translate3d(0, 0, 0);
            }

            100% {
                transform: rotate(0deg) translate3d(0, 0, 0);
            }
        }

        @keyframes storm1261 {
            0% {
                transform: translate3d(0, 0, 0) translateZ(0);
            }

            25% {
                transform: translate3d(4px, 0, 0) translateZ(0);
            }

            50% {
                transform: translate3d(-3px, 0, 0) translateZ(0);
            }

            75% {
                transform: translate3d(2px, 0, 0) translateZ(0);
            }

            100% {
                transform: translate3d(0, 0, 0) translateZ(0);
            }
        }

        .btn-shine {
            border: 1px solid;
            overflow: hidden;
            position: relative;
        }

        .btn-shine span {
            z-index: 20;
        }

        .btn-shine:after {
            background: #38ef7d;
            content: "";
            height: 155px;
            left: -75px;
            opacity: 0.4;
            position: absolute;
            top: -50px;
            transform: rotate(35deg);
            transition: all 550ms cubic-bezier(0.19, 1, 0.22, 1);
            width: 50px;
            z-index: -10;
        }

        .btn-shine:hover:after {
            left: 120%;
            transition: all 550ms cubic-bezier(0.19, 1, 0.22, 1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">
                                Pembagian <span class="d-block text-muted pt-2 font-size-sm">Pembagian produk karyawan</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Create New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="navi navi-bold navi-hover navi-active navi-link-rounded">
                            @foreach ($pembagians as $pembagian)
                                <div class="navi-item mb-2 position-relative">
                                    <a onClick="showPembagian('{{ $pembagian->id }}')" href="javascript:"
                                        class="navi-link py-4"
                                        style="border: 1px solid #ddd !important; border-radius: 5px">
                                        <span class="navi-icon mr-2">
                                            <span class="svg-icon">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Text/Article.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <rect fill="#000000" x="4" y="5" width="16" height="3"
                                                            rx="1.5"></rect>
                                                        <path
                                                            d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L12.5,10 C13.3284271,10 14,10.6715729 14,11.5 C14,12.3284271 13.3284271,13 12.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z"
                                                            fill="#000000" opacity="0.3"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </span>
                                        <span class="navi-text">{{ $pembagian->keterangan }}</span>
                                        <small>{{ formatTanggalIndonesia2($pembagian->tanggal_pembagian) }}</small>
                                    </a>
                                    <button onClick="deleteItem('{{ $pembagian->id }}')"
                                        class="btn btn-danger btn-xs btn-icon ml-5 position-absolute"
                                        style="right: -5px; top: -9px"><i class="fa fa-trash"></i></button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="modal-create-pembagian" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">Create Pembagian</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="form-create-pembagian">
                        <div class="row">
                            <div class="col-md-12">
                                <input required name="keterangan" type="text" class="form-control"
                                    placeholder="Nama pembagian">
                            </div>
                            <div class="col-md-12 mt-5"><label for="">Tanggal Pembagian</label></div>
                            <div class="col-md-12">
                                <input required name="tanggal" type="date" class="form-control"
                                    placeholder="Tanggal Pembagian">
                            </div>
                            <div class="col-md-12 mt-5">
                                <button id="button-submit-create" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pembagian-karyawan" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">List pembagian karyawan</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="current-id-pembagian">
                    <div class="card card-custom card-fit card-border mb-5">
                        <div class="card-body">
                            <form action="" id="form-import-data" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="file" class="form-control" name="file">
                                        <a href="{{ url('/templates/template-import-pembagian.xls') }}">Download
                                            template</a>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="upload-button" type="submit" class="btn btn-success">Upload</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div>
                        <button class="btn-shine p-4 mb-4" id="export-button">
                            <span>Export Excel</span>
                        </button>
                    </div>
                    <table id="table-pembagian-karyawan" class="table table-bordered table-hover table-responsive">
                        <thead>
                            <tr>
                                <th width="10">No</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Department</th>
                                <th>Lokasi Pembagian</th>
                                <th>Status Ambil</th>
                                <th>Waktu Ambil</th>
                                <th>Waktu Checkout</th>
                                <th>PIC</th>
                                <th>Ket.</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">
        $("#form-import-data").on("submit", function(e) {
            e.preventDefault();
            var data = new FormData(this);
            $("#upload-button").attr("disabled", true);
            $("#upload-button").text("Uploading...");
            data.append('id_pembagian', $("#current-id-pembagian").val());
            $.ajax({
                url: "{{ url('/hr/pembagian-karyawan/upload') }}",
                data: data,
                type: "POST",
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success == 1) {
                        var message = "Data berhasil di upload!";
                        // cek data yang di ignored ada berapa lalu kasuh keterangan nik nya
                        if (response.ignored_count > 0) {
                            message += " " + response.ignored_count +
                                " data diabaikan (nik sudah ada).";
                        }
                        Swal.fire("Oke!", message, "success")
                            .then((value) => {
                                $('#upload-button').removeAttr('disabled');
                                $('#upload-button').text('Upload');
                                // datatable.reload();
                                $("#form-import-data").get(0).reset();
                                showPembagian($("#current-id-pembagian").val());
                            });
                    }
                },
                error: function(e) {
                    console.log(e);
                    Swal.fire("Hmmm!", "Gagal upload, coba lagi!", "error")
                        .then((value) => {
                            $('#upload-button').removeAttr('disabled');
                            $('#upload-button').text('Upload');
                        });
                }
            });
        });

        $("#form-create-pembagian").on("submit", function(e) {
            e.preventDefault();
            $("#button-submit-create").text("Submiting...");
            $("#button-submit-create").attr("disabled", true);
            $.ajax({
                url: "{{ url('/hr/pembagian/create') }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(response) {
                    $("#button-submit-create").text("Submit");
                    $("#button-submit-create").removeAttr("disabled");
                    Swal.fire('Berhasil!', 'Create pembagian berhasil', 'success').then(function() {
                        location.reload();
                    });
                    // console.log( response );
                },
                error: function(error) {
                    $("#button-submit-create").text("Submit");
                    $("#button-submit-create").removeAttr("disabled");
                }
            })
        });

        function deleteItem(id) {
            if (confirm("Yakin mau menghapus data?")) {
                $.ajax({
                    url: "{{ url('/hr/pembagian/delete') }}/" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    success: function(response) {
                        if (response.success == 1) {
                            Swal.fire('Berhasil!', 'Data berhasil dihapus', 'success').then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Gagal menghapus data', 'error').then(function() {
                                location.reload();
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire('Gagal!', 'Gagal menghapus data', 'error').then(function() {
                            location.reload();
                        });
                    }
                });
            }
        }

        $(document).ready(function() {
            $("#export-button").click(function() {
                var pembagianID = $("#current-id-pembagian").val();
                exportToExcel(pembagianID);
            });

            function exportToExcel(id) {
                window.location.href = "/hr/pembagian-karyawan-data/export/" + id;
            }
        });

        function showPembagian(id) {
            $("#current-id-pembagian").val(id);
            var table = $("#table-pembagian-karyawan tbody");
            table.html("");
            $("#modal-pembagian-karyawan").modal("show");
            $.ajax({
                url: "{{ url('/hr/pembagian-karyawan/get') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    $.each(response.data, function(key, item) {
                        var waktu_ambil = item.waktu_ambil == null ? '' : item.waktu_ambil;
                        var pic = item.pic == null ? '' : item.pic;
                        var keterangan = item.keterangan == null ? '' : item.keterangan;
                        var checkout_time = item.checkout_time == null ? '' : item.checkout_time;
                        var status_ambil_label = item.status_ambil == 'belum' ? 'warning' : 'success';
                        var row = "<tr>";
                        row += "<td>" + (key + 1) + "</td>";
                        row += "<td>" + item.nik + "</td>";
                        row += "<td>" + item.nama + "</td>";
                        row += "<td>" + item.department + "</td>";
                        row += "<td>" + item.lokasi_pembagian + "</td>";
                        row += "<td><span class='label label-inline label-outline-" +
                            status_ambil_label + "'>" + item.status_ambil + "</span></td>";
                        row += "<td>" + waktu_ambil + "</td>";
                        row += "<td>" + checkout_time + "</td>";
                        row += "<td>" + pic + "</td>";
                        row += "<td>" + keterangan + "</td>";
                        row += "</tr>"
                        table.append(row);
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        // $('.custom-file-input').on('change', function() {
        //     var fileName = $(this).val();
        //     $(this).next('.custom-file-label').addClass("selected").html(fileName);
        // });

        // $('.table').DataTable();

        function showModalCreateNew() {
            $('#modal-create-pembagian').modal('show');
        }

        // $('#submitButton').on('click', function () {
        //     $(this).html('<i class="fa fa-spinner fa-spin"></i> Submiting...');
        //     setTimeout(function () {
        //         $('#submitButton').html('<i class="fa fa-paper-plane"></i> Submit');
        //     }, 1000)
        //     // $(this).attr('disabled', true);
        // })
    </script>
@endpush
