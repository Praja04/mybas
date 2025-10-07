@extends('layouts.base')

@push('styles')

{{--    <link href="{{ url('/') }}/assets/css/bootstrap-editable.css" rel="stylesheet">--}}
    <style type="text/css">
        .editable-container {
            position: absolute;
            /*top: 15px;*/
            margin-top: -20px;
        }
        #datatable .datatable-cell {
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            padding-right: 5px !important;
            padding-left: 5px !important;
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
                        <h3 class="card-label">Data PKW
                        <span class="d-block text-muted pt-2 font-size-sm">Data Calon Karyawan</span></h3>
                    </div>
                    <div class="card-toolbar">
                        <button onClick="showImportData()" class="btn btn-primary" type="button"><i class="la la-file-download"></i>Import Data</button>
                        <button onClick="showImportImages()" class="btn btn-primary ml-5" type="button"><i class="la la-picture-o"></i>Import Image</button>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin: Search Form-->
                    <!--begin::Search Form-->
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-lg-9 col-xl-8">
                                <div class="row align-items-center">
                                    <div class="col-md-4 my-2 my-md-0">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="datatable_search_query" />
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 my-2 my-md-0">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Jenis Kelamin:</label>
                                            <select class="form-control" id="datatable_search_jenis_kelamin">
                                                <option value="">All</option>
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- div class="col-md-4 my-2 my-md-0">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-3 mb-0 d-none d-md-block">Divisi: </label>
                                            <select class="form-control" id="datatable_search_divisi">
                                                <option value="">Tidak Punya Divisi</option>
                                                @foreach($divisis as $divisi)
                                                    <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 mb-5 collapse" id="datatable_group_action_form">
                        <div class="d-flex align-items-center">
                            <div class="font-weight-bold text-danger mr-3">Selected
                            (<span id="datatable_selected_records"></span>) records:</div>
                            <div class="dropdown mr-2">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Divisi: </label>
                                        <select class="form-control" id="pilih-divisi">
                                            <option value="">Pilih Divisi</option>
                                            @foreach($divisis as $divisi)
                                                <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-3">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Bagian: </label>
                                        <select disabled class="form-control" id="pilih-bagian">
                                            <option value="">Pilih Bagian</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-3">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Jabatan: </label>
                                        <select disabled class="form-control" id="pilih-jabatan">
                                            <option value="">Pilih Jabatan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4 mb-4">
                            <div class="dropdown mr-2">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Jenis: </label>
                                        <select class="form-control" id="pilih-jenis">
                                            <option value="">Pilih Jenis</option>
                                            <option value="pkwt">PKWT</option>
                                            <option value="pkwtt">PKWTT</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-2">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Periode: </label>
                                        <select class="form-control" id="pilih-periode">
                                            <option value="">Pilih Periode</option>
                                            <option value="3">3 Bulan</option>
                                            <option value="6">6 Bulan</option>
                                            <option value="12">12 Bulan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-2">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Mulai: </label>
                                        <input id="start-date" type="text" class="form-control" readonly="readonly" placeholder="Pilih Tanggal" />
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-2">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Selesai: </label>
                                        <input id="end-date" type="text" class="form-control" readonly="readonly" placeholder="Pilih Tanggal" />
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-success" type="button" id="submitButton">Submit</button>
                        </div>

                    </div>
                    <!--begin: Datatable-->
                    <div class="table-responsive">
                    <div class="table-bordered table-hover datatable datatable-bordered datatable-head-custom" id="calon-karyawan-datatable"></div>
                    </div>
                    <!--end: Datatable-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
</div>

<div id="modal-import-data" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="form-import-data" enctype="multipart/form-data">
                    <div class="custom-file">
                        <input required name="file" type="file" class="custom-file-input" id="input-import-data" />
                        <label class="custom-file-label" for="input-import-data" id="label-import-data">Pilih File Excel</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="upload-button" type="submit" class="btn btn-primary" form="form-import-data">Upload</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-import-images" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Images</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="form-import-images" enctype="multipart/form-data">
                    <div class="custom-file">
                        <input required name="file" type="file" class="custom-file-input" id="input-import-images" />
                        <label class="custom-file-label" for="input-import-images" id="label-import-images">Pilih File Excel</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary upload-button" form="form-import-images">Upload</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-foto-viewer" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <img id="foto-viewer" src="" alt="Foto" width="100%">
                <div id="foto-uploader" style="display: none">
                    <form id="form-foto-uploader" action="" enctype="multipart/form-data">
                        <div class="custom-file mb-2">
                            <input name="file_foto" type="file" class="custom-file-input" id="input-upload-foto" />
                            <label class="custom-file-label" for="input-upload-foto" id="label-upload-foto">Upload Foto Pribadi</label>
                        </div>
                        <div class="custom-file mb-2">
                            <input name="file_ktp" type="file" class="custom-file-input" id="input-upload-ktp" />
                            <label class="custom-file-label" for="input-upload-ktp" id="label-upload-ktp">Upload Foto KTP</label>
                        </div>
                        <div class="custom-file mb-2">
                            <input name="file_npwp" type="file" class="custom-file-input" id="input-upload-npwp" />
                            <label class="custom-file-label" for="input-upload-npwp" id="label-upload-npwp">Upload Foto NPWP</label>
                        </div>
                        <div class="custom-file mb-2">
                            <input name="file_kk" type="file" class="custom-file-input" id="input-upload-kk" />
                            <label class="custom-file-label" for="input-upload-kk" id="label-upload-kk">Upload Foto KK</label>
                        </div>
                        <input type="hidden" id="foto-uploader-id-user" name="id_user">
                        <button type="submit" class="btn btn-success" id="button-foto-uploader">Upload</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ url('/') }}/assets/js/bootstrap-editable.js"></script>

<script type="text/javascript">
var datatable;

function deleteCalonKaryawan(id)
{
    if(!confirm('Yakin hapus data ini?. Data yang sudah dihapus tidak dapat dikembalikan'))
    {
        return false;
    }

    $.ajax({
        url: "{{ url('/pkw/karyawan/delete') }}/"+id,
        type: "DELETE",
        dataType: "JSON",
        success: function( response ) {
            Swal.fire("Oke!", "Data berhasil di hapus!", "success")
            .then((value) => {
                datatable.reload();
            })
        },
        error: function ( e ) {
            console.log( e )
        }
    })
}

function showFoto(id_user, jenis, foto)
{
    $("#foto-uploader-id-user").val(id_user);

    if(foto == "") {
        $("#foto-viewer").hide();
        $("#foto-uploader").show();
    }else{
        $("#foto-viewer").show();
        $("#foto-uploader").hide();
        $("#foto-viewer").attr("src", "{{ url('images') }}/"+jenis+"/"+foto);
    }

    $("#modal-foto-viewer").modal('show');
}

$("#form-foto-uploader").on("submit", function (e) {
   e.preventDefault();
   var data = new FormData(this);
    
    $("#button-foto-uploader").attr("disabled", true);
    $("#button-foto-uploader").text("Uploading...");


   $.ajax({
        url: "{{ url('/pkw/karyawan/upload-image') }}",
        data: data,
        type: "POST",
        processData: false,
        contentType: false,
        success: function ( response ) {
            if(response.success == 1) {
                Swal.fire("Oke!", "Foto berhasil di upload!", "success")
                .then((value) => {
                    $('#button-foto-uploader').removeAttr('disabled');
                    $('#button-foto-uploader').text('Submit');
                    $('#form-foto-uploader').trigger("reset");
                    $("#modal-foto-viewer").modal('hide');
                    $("#label-upload-foto").text("Upload Foto Pribadi");
                    $("#label-upload-ktp").text("Upload Foto KTP");
                    $("#label-upload-npwp").text("Upload Foto NPWP");
                    $("#label-upload-kk").text("Upload Foto KK");
                    datatable.reload();
                })
            }
        }, error: function ( e ) {
            console.log( e );
            Swal.fire("Hmmm!", "Gagal upload, coba lagi!", "error")
            .then((value) => {
                $('#button-foto-uploader').removeAttr('disabled');
                $('#button-foto-uploader').text('Submit');
            })
        }
    })
});

$('#form-import-data').on('submit', function(e) {
    e.preventDefault();
    var data = new FormData(this);
    $("#upload-button").attr("disabled", true);
    $("#upload-button").text("Uploading...");
    // data.append('file', $('#input-import-data'));
    $.ajax({
        url: "{{ url('/pkw/karyawan/upload') }}",
        data: data,
        type: "POST",
        processData: false,
        contentType: false,
        success: function ( response ) {
            if(response.success == 1) {
                Swal.fire("Oke!", "Data berhasil di upload!", "success")
                .then((value) => {
                    $('#upload-button').removeAttr('disabled');
                    $('#upload-button').text('Submit');
                    $('#datatable_group_action_form').collapse('hide');
                    $('#form-import-data').trigger("reset");
                    $("#modal-import-data").modal("hide");
                    $("#label-import-data").text("Pilih File Excel")
                    datatable.reload();
                })
            }
        }, error: function ( e ) {
            console.log( e );
            Swal.fire("Hmmm!", "Gagal upload, coba lagi!", "error")
            .then((value) => {
                $('#upload-button').removeAttr('disabled');
                $('#upload-button').text('Submit');
            })
        }
    })
});

$('#form-import-images').on('submit', function(e) {
    e.preventDefault();
    var data = new FormData(this);
    $("#modal-import-images .upload-button").attr("disabled", true);
    $("#modal-import-images .upload-button").text("Uploading...");
    // data.append('file', $('#input-import-data'));
    $.ajax({
        url: "{{ url('/pkw/karyawan/upload-images') }}",
        data: data,
        type: "POST",
        processData: false,
        contentType: false,
        success: function ( response ) {
            if(response.success == 1) {
                Swal.fire("Oke!", "Gambar berhasil di upload!", "success")
                .then((value) => {
                    $('#modal-import-images .upload-button').removeAttr('disabled');
                    $('#modal-import-images .upload-button').text('Submit');
                    $('#datatable_group_action_form').collapse('hide');
                    $('#form-import-images').trigger("reset");
                    $("#modal-import-images").modal("hide");
                    $("#label-import-images").text("Pilih File Excel")
                    datatable.reload();
                })
            }else{
                $('#modal-import-images .upload-button').removeAttr('disabled');
                $('#modal-import-images .upload-button').text('Submit');
                $('#datatable_group_action_form').collapse('hide');
                $('#form-import-images').trigger("reset");
                $("##form-import-images").modal("hide");
                $("#label-import-images").text("Pilih File Excel")
                datatable.reload();
            }
        }, error: function ( e ) {
            // console.log( e );
            Swal.fire("Hmmm!", "Gagal upload, coba lagi!", "error")
            .then((value) => {
                $('#modal-import-images .upload-button').removeAttr('disabled');
                $('#modal-import-images .upload-button').text('Submit');
                $('#datatable_group_action_form').collapse('hide');
                $('#form-import-images').trigger("reset");
                $("##form-import-images").modal("hide");
                $("#label-import-images").text("Pilih File Excel")
                datatable.reload();
            })
        }
    })
});

$('#input-import-data').on('change', function(e) {
    $('#label-import-data').text(e.target.files[0].name);
});

$('#input-import-images').on('change', function(e) {
    $('#label-import-images').text(e.target.files[0].name);
});

$('#input-upload-foto').on('change', function(e) {
    $('#label-upload-foto').text(e.target.files[0].name);
});

$('#input-upload-ktp').on('change', function(e) {
    $('#label-upload-ktp').text(e.target.files[0].name);
});

$('#input-upload-npwp').on('change', function(e) {
    $('#label-upload-npwp').text(e.target.files[0].name);
});

$('#input-upload-kk').on('change', function(e) {
    $('#label-upload-kk').text(e.target.files[0].name);
});

$('#pilih-divisi,#pilih-bagian,#pilih-jabatan').select2();

$('#start-date, #end-date').datepicker({
    todayHighlight: true,
    orientation: "bottom left",
    autoclose: true,
    format: 'yyyy-mm-dd'
});

$('#start-date').on('changeDate', function () {
    getEndDate();
});
$('#pilih-periode').on('change', function () {
    getEndDate();
});

function showImportData()
{
    $("#modal-import-data").modal("show");
}

function showImportImages()
{
    $("#modal-import-images").modal("show");
}


function getEndDate()
{
    if($('#pilih-periode').val() != '' && $('#pilih-periode').val() != '') {
        var periode = $('#pilih-periode').val();
        // console.log(periode);
        var date = new Date($('#start-date').val());
        var newDate = new Date(date.setMonth(date.getMonth()+parseInt(periode)));
        newDate = new Date(newDate.setDate(newDate.getDate()-1));
        var newDateYear = newDate.getFullYear();
        var newDateMonth = newDate.getMonth()+1 < 10 ? '0' + (newDate.getMonth()+1) : newDate.getMonth()+1;
        var newDateDate = newDate.getDate() < 10 ? '0' + newDate.getDate() : newDate.getDate();
        $('#end-date').val(newDateYear + '-' + newDateMonth + '-' +newDateDate);
    }
}

$(document).ajaxComplete(function () {
    $.fn.editable.defaults.mode = 'inline';
    $('.editable').editable({
        toggle: 'dblclick',
        type: 'text',
        rows: 2,
        name: $(this).attr('data-name'),
        pk: $(this).attr('data-pk'),
        url: '{{ url('/pkw/karyawan/update') }}',
        title: 'Enter username'
    });
})

$('#pilih-divisi').on('change', function () {
    var divisiId = $(this).val();
    $('#pilih-bagian').attr('disabled', true);
    $.ajax({
        url: "{{ url('/') }}/bagian/get-by-divisi/"+divisiId,
        type: "GET",
        dataType: "JSON",
        success: function ( response ) {
            $('#pilih-bagian').html('<option value="">Pilih Bagian</option>');
            $('#pilih-bagian').removeAttr('disabled');
            $.each(response.bagians, function (key,val) {
                $('#pilih-bagian').append('<option value="' + val.id + '">' + val.nama_bagian + '</option>');
            });
        },
        error: function ( error ) {
            console.log( error );
        }
    });
});

$('#pilih-bagian').on('change', function () {
    var bagianId = $(this).val();
    $('#pilih-jabatan').attr('disabled', true);
    $.ajax({
        url: "{{ url('/') }}/jabatan/get-by-bagian/"+bagianId,
        type: "GET",
        dataType: "JSON",
        success: function ( response ) {
            $('#pilih-jabatan').html('<option value="">Pilih Jabatan</option>');
            $('#pilih-jabatan').removeAttr('disabled');
            $.each(response.jabatans, function (key,val) {
                $('#pilih-jabatan').append('<option value="' + val.id + '">' + val.nama_jabatan + '</option>');
            });
        },
        error: function ( error ) {
            console.log( error );
        }
    });
});

"use strict";
// Class definition

var KTDatatableRecordSelectionDemo = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: HOST_URL + '/pkw/karyawan/recruitment',
                    map: function(raw) {
                            // sample data mapping
                        raw = raw.data;
                        var dataSet = raw;
                        if (typeof raw.data !== 'undefined') {
                            dataSet = raw.data;
                        }
                        return dataSet;
                    },
                },
            },
            pageSize: 10,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
        },

        // layout definition
        layout: {
            scroll: false, // enable/disable datatable scroll both horizontal and
            footer: false // display/hide footer
        },

        // column sorting
        sortable: true,

        pagination: true,

        // columns definition
        columns: [
                {
                    field: 'id',
                    title: 'ID',
                    sortable: false,
                    width: 40,
                    selector: {
                        class: ''
                    },
                    textAlign: 'center',
                }, {
                    field: 'nik_ktp',
                    title: 'No KTP',
                    width: 130,
                    template: function ( row ) {
                        return '<a data-name="nik_ktp" data-pk="'+ row.id +'" class="editable">'+ row.nik_ktp +'</a>';
                    }
                }, {
                    field: 'nama',
                    title: 'Nama',
                    width: 150,
                    template: function ( row ) {
                        return '<a data-name="nama" data-pk="'+ row.id +'" class="editable">'+ row.nama +'</a>';
                    }
                },{
                    field: 'jenis_kelamin',
                    title: 'Jenis Kelamin',
                },{
                    field: 'tempat_lahir',
                    title: 'Tempat Lahir',
                    template: function ( row ) {
                        return '<a data-name="tempat_lahir" data-pk="'+ row.id +'" class="editable">'+ row.tempat_lahir +'</a>';
                    }
                }, {
                    field: 'tanggal_lahir',
                    title: 'Tgl Lahir',
                    type: 'date',
                    format: 'DD/MM/YYYY',
                },{
                    field: 'divisi',
                    title: 'Divisi',
                    width: 50
                },{
                    field: 'bagian',
                    title: 'Bagian',
                    width: 60
                },{
                    field: 'jabatan',
                    title: 'Jabatan',
                    width: 150,
                },
                {
                    field: 'agama',
                    title: 'Agama',
                    sortable: false,
                    template: function ( row ) {
                        return '<a data-name="agama" data-pk="'+ row.id +'" class="editable">'+ row.agama +'</a>';
                    }
                },
                {
                    field: 'Foto',
                    title: 'Foto',
                    sortable: false,
                    width: 140,
                    overflow: 'visible',
                    autoHide: false,
                    template: function( row ) {
                        var status_diri = row.foto_diri == '' ? 'btn-outline-danger' : 'btn-outline-success';
                        var status_ktp = row.foto_ktp == '' ? 'btn-outline-danger' : 'btn-outline-success';
                        var status_npwp = row.foto_npwp == '' ? 'btn-outline-danger' : 'btn-outline-success';
                        var status_kk = row.foto_kk == '' ? 'btn-outline-danger' : 'btn-outline-success';
                        return '\
                            <a onClick="showFoto(\''+row.id+'\',\'foto\', \''+row.foto_diri+'\')" href="javascript:;" class="btn btn-xs '+status_diri+' py-0 px-1 pl-2" title="Delete">\
                                <i class="fa fa-user"></i>\
                            </a>\
                            <a onClick="showFoto(\''+row.id+'\',\'ktp\', \''+row.foto_ktp+'\')" href="javascript:;" class="btn btn-xs '+status_ktp+' py-0 px-1" title="Delete">\
                                <span>ktp</span>\
                            </a>\
                            <a onClick="showFoto(\''+row.id+'\',\'npwp\', \''+row.foto_npwp+'\')" href="javascript:;" class="btn btn-xs '+status_npwp+' py-0 px-1" title="Delete">\
                                <span>npwp</span>\
                            </a>\
                            <a onClick="showFoto(\''+row.id+'\',\'kk\', \''+row.foto_kk+'\')" href="javascript:;" class="btn btn-xs '+status_kk+' py-0 px-1" title="Delete">\
                                <span>kk</span>\
                            </a>\
                        ';
                    },
                },
                {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    overflow: 'visible',
                    autoHide: false,
                    template: function(item) {
                        return '\
                            <a onClick="deleteCalonKaryawan('+ item.id +')" href="javascript:;" class="btn btn-xs btn-clean btn-icon" title="Delete">\
                                <i class="fa fa-trash"></i>\
                            </a>\
                        ';
                    },
                },
                {
                    field: 'alamat_rumah_luar_kota',
                    title: 'Alamat Rumah Luar Kota',
                    sortable: false,
                    template: function ( row ) {
                        return '<a data-name="alamat_rumah_luar_kota" data-pk="'+ row.id +'" class="editable">'+ row.alamat_rumah_luar_kota +'</a>';
                    }
                },{
                    field: 'tanggal_masuk',
                    title: 'Tanggal Masuk',
                    sortable: false,
                },{
                    field: 'status_perdata',
                    title: 'Status Perdata',
                    sortable: false,
                    template: function ( row ) {
                        return '<a data-name="status_perdata" data-pk="'+ row.id +'" class="editable">'+ row.status_perdata +'</a>';
                    }
                },{
                    field: 'nama_pasangan',
                    title: 'Nama Pasangan',
                    sortable: false,
                },{
                    field: 'tempat_pernikahan',
                    title: 'Tempat Pernikahan',
                    sortable: false,
                },{
                    field: 'tanggal_pernikahan',
                    title: 'Tanggal Pernikahan',
                    sortable: false,
                },{
                    field: 'tempat_lahir_pasangan',
                    title: 'Tempat Lahir Pasangan',
                    sortable: false,
                },{
                    field: 'tanggal_lahir_pasangan',
                    title: 'Tanggal Lahir Pasangan',
                    sortable: false,
                },{
                    field: 'pekerjaan_pasangan',
                    title: 'Pekerjaan Pasangan',
                    sortable: false,
                },{
                    field: 'tempat_pasangan_bekerja',
                    title: 'Tempat Pasangan Bekerja',
                    sortable: false,
                },{
                    field: 'nama_ayah',
                    title: 'Nama Ayah',
                    sortable: false,
                },{
                    field: 'tempat_lahir_ayah',
                    title: 'Tempat Lahir Ayah',
                    sortable: false,
                },{
                    field: 'tanggal_lahir_ayah',
                    title: 'Tanggal Lahir Ayah',
                    sortable: false,
                },{
                    field: 'nama_ibu',
                    title: 'Nama Ibu',
                    sortable: false,
                },{
                    field: 'tempat_lahir_ibu',
                    title: 'Tempat Lahir Ibu',
                    sortable: false,
                },{
                    field: 'tanggal_lahir_ibu',
                    title: 'Tanggal Lahir Ibu',
                    sortable: false,
                },{
                    field: 'nama_ayah_mertua',
                    title: 'Nama Ayah Mertua',
                    sortable: false,
                },
                {
                    field: 'tempat_lahir_ayah_mertua',
                    title: 'Tempat Lahir Ayah Mertua',
                    sortable: false,
                },
                {
                    field: 'tanggal_lahir_ayah_mertua',
                    title: 'Tanggal Lahir Ayah Mertua',
                    sortable: false,
                },{
                    field: 'nama_ibu_mertua',
                    title: 'Nama Ibu Mertua',
                    sortable: false,
                },{
                    field: 'tempat_lahir_ibu_mertua',
                    title: 'Tempat Lahir Ibu Mertua',
                    sortable: false,
                },
                {
                    field: 'tangal_lahir_ibu_mertua',
                    title: 'Tanggal Lahir Ibu Mertua',
                    sortable: false,
                },
                {
                    field: 'nama_kontak_darurat',
                    title: 'Nama Kontak Darurat',
                    sortable: false,
                },
                {
                    field: 'hubungan_kontak_darurat',
                    title: 'Hubungan Kontak Darurat',
                    sortable: false,
                },
                {
                    field: 'no_telepon_kontak_darurat',
                    title: 'No Telepon Kontak Darurat',
                    sortable: false,
                },
                {
                    field: 'nomor_rekening_bank',
                    title: 'Nomor Rekening Bank',
                    sortable: false,
                },
                {
                    field: 'nomor_kartu_bpjs_ketenagakerjaan',
                    title: 'Nomor Kartu BPJS Ketenagakerjaan',
                    sortable: false,
                },{
                    field: 'keterangan_kartu_bpjs_ketenagakerjaan',
                    title: 'Keterangan',
                    sortable: false,
                },{
                    field: 'nomor_kartu_bpjs_kesehatan',
                    title: 'Nomor Kartu BPJS Kesehatan',
                    sortable: false,
                },{
                    field: 'keterangan_kartu_bpjs_kesehatan',
                    title: 'Keterangan',
                    sortable: false,
                },
            ],
    };
    var serverSelectorDemo = function() {
        // enable extension
        options.extensions = {
            // boolean or object (extension options)
            checkbox: true,
        };
        options.search = {
            input: $('#datatable_search_query'),
            key: 'generalSearch'
        };

        datatable = $('#calon-karyawan-datatable').KTDatatable(options);

        $('#datatable_search_jenis_kelamin').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'jenis_kelamin');
        });

        $('#datatable_search_divisi').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'divisi');
        });

        $('#datatable_search_jenis_kelamin, #datatable_search_divisi').selectpicker();

        datatable.on(
            'datatable-on-click-checkbox',
            function(e) {
                // datatable.checkbox() access to extension methods
                var ids = datatable.checkbox().getSelectedId();
                var count = ids.length;

                $('#datatable_selected_records').html(count);

                if (count > 0) {
                    $('#datatable_group_action_form').collapse('show');
                } else {
                    $('#datatable_group_action_form').collapse('hide');
                }
            });

        // $('#datatable_fetch_modal').on('show.bs.modal', function(e) {
        //     var ids = datatable.checkbox().getSelectedId();
        //     var c = document.createDocumentFragment();
        //     for (var i = 0; i < ids.length; i++) {
        //         var li = document.createElement('li');
        //         li.setAttribute('data-id', ids[i]);
        //         li.innerHTML = 'Selected record ID: ' + ids[i];
        //         c.appendChild(li);
        //     }
        //     $('#datatable_fetch_display').append(c);
        // }).on('hide.bs.modal', function(e) {
        //     $('#datatable_fetch_display').empty();
        // });

        $('#submitButton').on('click', function() {
            $('#submitButton').attr('disabled', true);
            $('#submitButton').text('Processing..');
            var data = {
                id : datatable.checkbox().getSelectedId(),
                divisi : $('#pilih-divisi').val(),
                bagian : $('#pilih-bagian').val(),
                jabatan : $('#pilih-jabatan').val(),
                jenis : $('#pilih-jenis').val(),
                periode : $('#pilih-periode').val(),
                start_date : $('#start-date').val(),
                end_date : $('#end-date').val()
            }
            $.ajax({
                url: "{{ url('/') }}/pkw/start",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    if(response.success == 1) {
                        Swal.fire("Oke!", "Data berhasil di konfirmasi!", "success")
                            .then((value) => {
                                $('#submitButton').removeAttr('disabled');
                                $('#submitButton').text('Submit');
                                $('#datatable_group_action_form').collapse('hide');
                                $('#pilih-divisi').val('');
                                $('#pilih-divisi').trigger('change');
                                $('#pilih-bagian').val('');
                                $('#pilih-bagian').trigger('change');
                                $('#pilih-jabatan').val('');
                                $('#pilih-jabatan').trigger('change');
                                $('#pilih-jenis').val('');
                                $('#pilih-periode').val('');
                                $('#start-date').val('');
                                $('#end-date').val('');

                                datatable.reload();
                            })
                    }else{
                        Swal.fire("Hmmm!", response.message, "error");
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').text('Submit');
                    }
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                    $('#submitButton').removeAttr('disabled');
                    $('#submitButton').text('Submit');
                }
            });
        });
    };

    return {
        // public functions
        init: function() {
            serverSelectorDemo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRecordSelectionDemo.init();
});

</script>

@endpush
