@extends('layouts.base-display')

@section('title', 'PENGAMBILAN ID CARD')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@push('styles')
    <style type="text/css">
        .hide {
            opacity: 0 !important;
        }
        #data-karyawan {
            opacity: 1;
            transition-duration: 0.5s;
        }
        #error {
            opacity: 1;
            transition-duration: 0.5s;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">PENAMBILAN ID CARD</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Pengambilan id card calon karyawan</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <form action="" id="scan-id-card-form">
                                            <input name="id_card" id="id-card" type="number" class="form-control" autofocus placeholder="Scan Id Card">  
                                        </form>
                                    </div>
                                    <div class="col-md-2">
                                        <button onClick="location.reload()" type="button" class="btn btn-outline-secondary"><i class="flaticon2-refresh"></i> Refresh Halaman</button>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="alert alert-danger py-2 mt-1 hide" id="error">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="data-karyawan" class="col-md-12 row hide">
                                <div class="col-md-4 mt-5">
                                    <div class="card card-custom card-fit card-border">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <span class="card-icon">
                                                    <i class="flaticon2-user"></i>
                                                </span>
                                                <h3 class="card-label">Informasi Karyawan</h3>
                                            </div>
                                        </div>
                                        <div class="card-body pt-2">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                                                    <div class="symbol-label" style="background-image:url('')" id="foto-diri"></div>
                                                </div>
                                                <div>
                                                    <span class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary" id="nama"></span>
                                                    <div class="text-muted"><span id="bagian"></span> - <span id="jabatan"></span></div>
                                                    <div class="mt-2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pt-8 pb-2">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="font-weight-bold mr-2">NIK :</span>
                                                    <span class="text-muted text-hover-primary" id="nik"></span>
                                                    <input type="hidden" name="nik" id="nik-input">
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="font-weight-bold mr-2">No KTP :</span>
                                                    <span class="text-muted" id="no-ktp"></span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="font-weight-bold mr-2">Jenis Kelamin :</span>
                                                    <span class="text-muted" id="jenis-kelamin"></span>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-5">
                                    <div class="card card-custom card-fit card-border">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <span class="card-icon">
                                                    <i class="fa fa-id-card"></i>
                                                </span>
                                                <h3 class="card-label">Detail KTP</h3>
                                            </div>
                                        </div>
                                        <div class="card-body pt-2">
                                            <div class="symbol symbol-150 symbol-2by3 flex-shrink-0 mb-2">
                                                <div class="symbol-label" style="background-image: url('')" id="foto-ktp"></div>
                                            </div>
                                            <div class="pt-1">
                                                <div class="row">
                                                    <div class="col-md-6" id="scan-ktp" style="display: none">
                                                        <form id="scan-ktp-form">
                                                            <input required id="ktp" type="number" class="form-control" placeholder="Scan KTP">
                                                        </form>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button id="scan-ktp-button" onClick="showScanKTP()" type="button" class="btn btn-sm btn-success mt-2">Scan KTP</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

@push('scripts')
    <script type="text/javascript">
        function showScanKTP()
        {
            $("#scan-ktp-button").hide();
            $("#scan-ktp").show();
            $("#scan-ktp input").focus();
        }

        $("#scan-id-card-form").on("submit", function(e) {
            e.preventDefault();

            if($("#nik-input").val() != "") {
                $("#scan-ktp-button").focus();
                return false;
            }

            var id_card = $("#id-card").val();
            $("#id-card").val("");
            $.ajax({
                url: "{{ url('display/pengambilan-id-card/scan') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    id_card : id_card
                },
                success: function ( response ) {
                    if(response.success == 1) {
                        var user = response.data;
                        $("#nama").text(user.nama);
                        $("#bagian").text(user.bagian.nama_bagian);
                        $("#jabatan").text(user.jabatan.nama_jabatan);
                        $("#nik").text(user.nik);
                        $("#nik-input").val(user.nik);
                        $("#no-ktp").text(user.nik_ktp);
                        $("#jenis-kelamin").text(user.jenis_kelamin == "L" ? "Laki-laki" : "Perempuan");
                        $("#foto-diri").css("background-image", "url('{{ asset('/images/foto') }}/"+user.foto_diri+"')");
                        $("#foto-ktp").css("background-image", "url('{{ asset('/images/ktp') }}/"+user.foto_ktp+"')");
                        $("#data-karyawan").removeClass("hide");
                    }else{
                        $("#error span").text(response.message);
                        $("#error").removeClass("hide");
                        setTimeout(function() {
                            $("#error").addClass("hide");
                        }, 2000);
                    }
                },
                error: function ( error ) {
                    console.log( error );
                }
            })
        });

        $("#scan-ktp-form").on("submit", function(e) {
            e.preventDefault();
            var rf = $("#ktp").val();
            $("#ktp").val("");
            $.ajax({
                url: "{{ url('display/pengambilan-id-card/submit') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    rf : rf,
                    nik : $("#nik-input").val()
                },
                success: function ( response ) {
                    if(response.success == 1) {
                        $("#nik-input").val("");
                        Swal.fire('Berhasil!', 'Pengambilan id card berhasil', 'success').then(function () {
                            location.reload();
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function ( error ) {
                    console.log( error );
                }
            })
        });
    </script>
@endpush