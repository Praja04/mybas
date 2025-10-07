@extends('layouts.base')

@push('styles')
    <style type="text/css">
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
                        <span class="d-block text-muted pt-2 font-size-sm">Data Karyawan</span></h3>
                    </div>
                    <div class="card-toolbar">
                        <!--begin::Dropdown-->
                        <div class="dropdown dropdown-inline mr-2">
                            <button type="button" class="btn btn-outline-secondary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                        <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>Export</button>
                            <!--begin::Dropdown Menu-->
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <!--begin::Navigation-->
                                <ul class="navi flex-column navi-hover py-2">
                                    <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-dark-50 pb-2">Choose an option:</li>
                                    <li class="navi-item">
                                        <a onClick="exportPayroll()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">PAYROLL</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportBPJS()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">BPJS</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportBPJSTK()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">BPJS TK</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportBankMandiri()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">Bank Mandiri</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportIris()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">Master Iris</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportMasterAll()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-excel"></i>
                                            </span>
                                            <span class="navi-text">Master All</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a onClick="exportPKWTT()" href="javascript:" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-pdf"></i>
                                            </span>
                                            <span class="navi-text">PKWTT</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a href="#" class="navi-link">
                                            <span class="navi-icon">
                                                <i class="la la-file-pdf"></i>
                                            </span>
                                            <span class="navi-text">PKWT</span>
                                        </a>
                                    </li>
                                </ul>
                                <!--end::Navigation-->
                            </div>
                            <!--end::Dropdown Menu-->
                        </div>
                        <!--end::Dropdown-->
                        <!--begin::Button-->

                        <!--end::Button-->
                    </div>
                </div>
                <div class="card-body">
                    <!--begin: Search Form-->
                    <!--begin::Search Form-->
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-xl-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3 my-2 my-md-0">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="datatable_search_query" />
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 my-2 my-md-0">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Jenis Kelamin:</label>
                                            <select class="form-control" id="datatable_search_jenis_kelamin">
                                                <option value="">All</option>
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 my-2 my-md-0">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-3 mb-0 d-none d-md-block">Divisi: </label>
                                            <select class="form-control" id="datatable_search_divisi">
                                                <option value="">Semua Divisi</option>
                                                @foreach($divisis as $divisi)
                                                    <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 my-2 my-md-0">
                                        <div class="d-flex align-items-center">
                                            <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Tgl Masuk: </label>
                                            <input type="date" class="form-control" id="datatable_search_tgl_masuk">
                                        </div>
                                    </div>
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
                                        <label class="mr-3 mb-0 d-none d-md-block">Group: </label>
                                        <select class="form-control" id="pilih-group" style="width: 250px">
                                            <option value="">Pilih Kode Group</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->kode_group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown mr-3">
                                <div class="col-md-12 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Admin: </label>
                                        <select class="form-control" id="pilih-admin" style="width: 250px">
                                            <option value="">Pilih Kode Admin</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}">{{ $admin->kode_admin }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-success" type="button" id="submitButton">Submit</button>
                            <button class="ml-10 btn btn-sm btn-danger" type="button" id="sendToRecruitment"><i class="fa fa-reply"></i> Kembalikan Ke Recruitment</button>
                        </div>
                    </div>
                    <!--begin: Datatable-->
                    <div class="table-responsive">
                    <div class="table-bordered table-hover datatable datatable-bordered datatable-head-custom" id="karyawan-datatable"></div>
                    </div>
                    <!--end: Datatable-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script type="text/javascript">

$('#pilih-group,#pilih-admin').select2();

"use strict";
// Class definition

var datatable;

function showFoto(jenis, foto)
{
    $("#foto-viewer").attr("src", "{{ url('images') }}/"+jenis+"/"+foto);
    $("#modal-foto-viewer").modal('show');
}

function clearlocalStorage()
{
    localStorage.setItem('selectedId', JSON.stringify([]));
}
clearlocalStorage();

function exportPayroll()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-payroll') }}/"+ids.join(),
            "_blank"
        )
    }
}

function exportIris()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-iris') }}/"+ids.join(),
            "_blank"
        )
    }
}

function uploadToSecureAccess(id)
{
    // alert(nik);
    $.ajax({
    url: "{{ url('pkw/karyawan/upload-secure-access') }}",
    type: "POST",
    dataType: "JSON",
    data: {
            id : id
    },
    success: function( response ) {
        if(response.success == 1) {
                datatable.reload();
        }
    },
    error: function ( error ) {
        console.log( error );
    }
    });
}

function exportBPJS()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-bpjs') }}/"+ids.join(),
            "_blank"
        )
    }
}

function exportBPJSTK()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-bpjs-tk') }}/"+ids.join(),
            "_blank"
        )
    }
}

function exportBankMandiri()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-bank-mandiri') }}/"+ids.join(),
            "_blank"
        )
    }
}

function exportPKWTT()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-pkwtt') }}/"+ids.join(),
            "_blank"
        )
    }
}

function exportMasterAll()
{
    var ids = JSON.parse(localStorage.getItem('selectedId'));
    if(ids.length == 0) {
        alert('Mohon pilih salah satu item');
    }else{
        // console.log(ids.join());
        window.open(
            "{{ url('/pkw/karyawan/export-master-all') }}/"+ids.join(),
            "_blank"
        )
    }
}


var Datatable_ = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: HOST_URL + '/pkw/karyawan/karyawan',
                    map: function(raw) {
                            // sample data mapping
                        raw = raw.data;
                        // console.log(raw);
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
                    field: 'nik',
                    title: 'NIK',
                    width: 100,
                    sortable: false
                }, {
                    field: 'nama',
                    title: 'Nama',
                    width: 150,
                },{
                    field: 'divisi',
                    title: 'Divisi',
                    width: 60,
                },{
                    field: 'bagian',
                    title: 'Bagian',
                    width: 140,
                },{
                    field: 'jabatan',
                    title: 'Jabatan',
                    width: 180,
                },{
                    field: 'group',
                    title: 'Group',
                    width: 100,
                },{
                    field: 'admin',
                    title: 'Admin',
                    width: 150,
                },{
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
                            <a onClick="showFoto(\'foto\', \''+row.foto_diri+'\')" href="javascript:;" class="btn btn-xs '+status_diri+' py-0 px-1 pl-2" title="Delete">\
                                <i class="fa fa-user"></i>\
                            </a>\
                            <a onClick="showFoto(\'ktp\', \''+row.foto_ktp+'\')" href="javascript:;" class="btn btn-xs '+status_ktp+' py-0 px-1" title="Delete">\
                                <span>ktp</span>\
                            </a>\
                            <a onClick="showFoto(\'npwp\', \''+row.foto_npwp+'\')" href="javascript:;" class="btn btn-xs '+status_npwp+' py-0 px-1" title="Delete">\
                                <span>npwp</span>\
                            </a>\
                            <a onClick="showFoto(\'kk\', \''+row.foto_kk+'\')" href="javascript:;" class="btn btn-xs '+status_kk+' py-0 px-1" title="Delete">\
                                <span>kk</span>\
                            </a>\
                        ';
                    },
                },
                {
                    field: 'Action',
                    title: 'Action',
                    sortable: false,
                    width: 80,
                    overflow: 'visible',
                    autoHide: false,
                    template: function(row) {
                        if(row.upload_secure_access == "sudah")
                        {
                            return '\
                                <span class="btn btn-xs btn-clean py-0 px-1 mr-2" title="Foto sudah di upload">\
                                    <i class="la la-picture-o"></i> <i class="fa fa-check text-success"></i>\
                                </span>\
                            ';
                        }else{
                            return '\
                                <a onClick="uploadToSecureAccess(\''+row.id+'\')" href="javascript:;" class="btn btn-xs btn-outline-primary py-0 px-1 mr-2" title="Upload foto ke secure access">\
                                    <i class="la la-picture-o"></i> <i class="la la-upload"></i>\
                                </a>\
                            ';
                        }
                        
                    },
                },{
                    field: 'jenis_kelamin',
                    title: 'Jenis Kelamin',
                    width: 110,
                },{
                    field: 'tempat_lahir',
                    title: 'Tempat Lahir',
                    width: 120,
                }, {
                    field: 'tanggal_lahir',
                    title: 'Tgl Lahir',
                    type: 'date',
                    format: 'DD/MM/YYYY',
                    width: 90,
                },
                {
                    field: 'agama',
                    title: 'Agama',
                    sortable: false,
                },{
                    field: 'tanggal_masuk',
                    title: 'Tanggal Masuk',
                    width: 150
                },{
                    field: 'status_perdata',
                    title: 'Status Perdata',
                    sortable: false,
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

        datatable = $('#karyawan-datatable').KTDatatable(options);

        $('#datatable_search_jenis_kelamin').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'jenis_kelamin');
        });

        $('#datatable_search_divisi').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'divisi');
        });

        $('#datatable_search_tgl_masuk').val("{!! date('Y-m-d') !!}");

        $('#datatable_search_tgl_masuk').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'tanggal_masuk');
        });

        $('#datatable_search_jenis_kelamin, #datatable_search_divisi').selectpicker();

        datatable.on(
            'datatable-on-click-checkbox',
            function(e) {
                // datatable.checkbox() access to extension methods
                var ids = datatable.checkbox().getSelectedId();
                var count = ids.length;

                localStorage.setItem('selectedId', JSON.stringify(ids));

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
            if($('#pilih-group').val() == '' || $('#pilih-admin').val() == '') {
                Swal.fire("Hmm!", "Mohon pilih group & admin!", "info")
                $('#submitButton').removeAttr('disabled');
                $('#submitButton').text('Submit');
                return;
            }
            var data = {
                id : datatable.checkbox().getSelectedId(),
                group : $('#pilih-group').val(),
                admin : $('#pilih-admin').val()
            }
            $.ajax({
                url: "{{ url('/') }}/pkw/karyawan/set-group-admin",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    Swal.fire("Oke!", "Set Group dan admin berhasil!", "success")
                    .then((value) => {
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').text('Submit');
                        datatable.reload();
                    });
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                    $('#submitButton').removeAttr('disabled');
                    $('#submitButton').text('Submit');
                }
            });
        });

        $("#sendToRecruitment").on("click", function() {
            $('#sendToRecruitment').attr('disabled', true);
            $('#sendToRecruitment').text('Processing..');
            var data = {
                id : datatable.checkbox().getSelectedId(),
            }

            $.ajax({
                url: "{{ url('/') }}/pkw/karyawan/send-to-recruitment",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    Swal.fire("Oke!", "Berhasil dikembalikan ke recruitment!", "success")
                    .then((value) => {
                        $('#sendToRecruitment').removeAttr('disabled');
                        $('#sendToRecruitment').text('Kembalikan ke recruitment');
                        datatable.reload();
                    });
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                    $('#sendToRecruitment').removeAttr('disabled');
                    $('#sendToRecruitment').text('Kembalikan ke recruitment');
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
    Datatable_.init();
});

</script>

@endpush
