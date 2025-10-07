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
                        <span class="d-block text-muted pt-2 font-size-sm">Data Karyawan PKWTT</span></h3>
                    </div>
                    <div class="card-toolbar">
                        <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                            <li class="nav-item">
                                <a class="nav-link py-2 px-4" href="{{ url('/pkw/pkwt') }}">PKWT</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 px-4 active" href="{{ url('/pkw/pkwtt') }}">PKWTT</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin: Search Form-->
                    <!--begin::Search Form-->
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-xl-12">
                                <div class="row align-items-center">
                                    <div class="col-md-2 my-2 my-md-0">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="datatable_search_query" />
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 my-2 my-md-0">
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
                                            <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Percobaan/Kontak Ke: </label>
                                            <input type="number" class="form-control" id="datatable_search_kontrak_ke">
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
                            <button class="btn btn-sm btn-success" type="button" id="submitButton"><i class="la la-paper-plane"></i> Kirim Form PA</button>
                        </div>
                    </div>
                    <!--begin: Datatable-->
                    <div class="table-responsive">
                    <div class="table-bordered table-hover datatable datatable-bordered datatable-head-custom" id="pkwtt-datatable"></div>
                    </div>
                    <!--end: Datatable-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
</div>

@endsection

@push('scripts')

<script type="text/javascript">

const _MS_PER_DAY = 1000 * 60 * 60 * 24;

// a and b are javascript Date objects
function dateDiffInDays(a, b) {
  // Discard the time and time-zone information.
  const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
  const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

  return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}


"use strict";
// Class definition

var pkwttDatatableData = function() {
    // Private functions

    var options = {
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: HOST_URL + '/pkw/get-all/pkwtt',
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
                    width: 20,
                    selector: {
                        class: ''
                    },
                    textAlign: 'center',
                }, {
                    field: 'nik',
                    title: 'NIK',
                    width: 100,
                }, {
                    field: 'nama',
                    title: 'Nama',
                    width: 150,
                },{
                    field: 'jenis_kelamin',
                    title: 'JK',
                    width: 20,
                },{
                    field: 'divisi',
                    title: 'Divisi',
                    width: 60,
                },{
                    field: 'bagian',
                    title: 'Bagian',
                    width: 100,
                },{
                    field: 'jenis',
                    title: 'Jenis',
                    width: 60,
                    template: function(row) {
		                return '<span class="label label-lg font-weight-bold label-light-info label-inline">' + row.jenis + '</span>';
                    }
                },{
                    field: 'kontrak_ke',
                    title: 'Kontrak',
                    width: 70,
                    template: function(row) {
		                return '<span class="label label-lg font-weight-bold label-light-info label-inline">' + row.kontrak_ke + '</span>';
                    }
                },{
                    field: 'tanggal_mulai',
                    title: 'Mulai',
                    width: 90,
                    template: function(row) {
                    	var date = moment(row.tanggal_mulai);
		                return '<span class="label label-lg font-weight-bold label-light-info label-inline">' + row.tanggal_mulai + '</span>';
                    }
                },{
                    field: 'tanggal_selesai',
                    title: 'Selesai',
                    width: 90,
                    template: function(row) {
                        const a = moment();
                        const b = moment(row.pure_tanggal_selesai);
                        const difference = b.diff(a, 'days');
                        if(parseInt(difference) < 30) {
                            var color_class = 'label-light-danger';
                        }else{
                            var color_class = 'label-light-success';
                        }
		                return '<span class="label label-lg font-weight-bold ' + color_class + ' label-inline">' + row.tanggal_selesai + '</span>';
                    }
                },{
                    field: 'form_pa',
                    title: 'Form PA',
                    width: 90,
                    template: function(row) {
                        var text = row.form_pa;
                        if(row.form_pa == 'created' || row.form_pa == 'filled') {
                            var color_class = 'label-light-secondary text-black-50';
                            var text = 'created';
                        }else if(row.form_pa == 'approve1'){
                            var color_class = 'label-light-warning';
                        }else if(row.form_pa == 'approve2'){
                            var color_class = 'label-light-warning';
                        }else if(row.form_pa == 'done'){
                            var color_class = 'label-light-success';
                        }else{
                            var color_class = 'label-light-info';
                        }
                        return '<span class="label label-lg font-weight-bold ' + color_class + ' label-inline">' + text + '</span>';
                    }
                },{
                    field: 'kesimpulan',
                    title: 'Kesimpulan',
                    width: 150,
                    template: function(row) {
                        if(row.kesimpulan == 'lulus') {
                            var color_class = 'label-success';
                        }else if(row.kesimpulan == 'lulus dengan catatan'){
                            var color_class = 'label-light-success';
                        }else if(row.kesimpulan == 'tidak lulus') {
                            var color_class = 'label-light-danger';
                        }else{
                            var color_class = 'label-light-secondary text-black-50';
                        }
                        return '<span class="label label-lg font-weight-bold ' + color_class + ' label-inline">' + row.kesimpulan + '</span>';
                    }
                }
            ],
    };

    var pkwttDatatable = function() {
        // enable extension
        options.extensions = {
            // boolean or object (extension options)
            checkbox: true,
        };
        options.search = {
            input: $('#datatable_search_query'),
            key: 'generalSearch'
        };

        var datatable = $('#pkwtt-datatable').KTDatatable(options);

        $('#datatable_search_divisi').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'divisi');
        });

        $('#datatable_search_jenis_pkw, #datatable_search_divisi').selectpicker();

        $('#start-date, #end-date').datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

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
            }
        );

        $('#submitButton').on('click', function() {
            $('#submitButton').attr('disabled', true);
            $('#submitButton').text('Sending..');
            var data = {
                id : datatable.checkbox().getSelectedId()
            }
            $.ajax({
                url: "{{ url('/') }}/pkw/form-pa/create",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    if(response.success == 1) {
                        Swal.fire("Oke!", "Form PA berhasil di kirim!", "success")
                        .then((value) => {
                            $('#submitButton').removeAttr('disabled');
                            $('#submitButton').text('Submit');
                            location.reload();
                        });
                    }else if(response.success == 0) {
                        Swal.fire("Oke!", response.message, "error")
                        .then((value) => {
                            $('#submitButton').removeAttr('disabled');
                            $('#submitButton').text('Submit');
                        });
                    }else{
                        Swal.fire("Gagal!", "Mohon coba lagi!", "error")
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').text('Submit');
                    }
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error")
                    $('#submitButton').removeAttr('disabled');
                    $('#submitButton').text('Submit');
                }
            });
        });
    };

    return {
        // public functions
        init: function() {
            pkwttDatatable();
        },
    };
}();

jQuery(document).ready(function() {
    pkwttDatatableData.init();
});

</script>

@endpush
