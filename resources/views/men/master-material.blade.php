@extends('layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">

<style type="text/css">
    #table-material > thead > tr > th, #table-material > tbody > tr > td, .table > thead > tr > th, .table > tbody > tr > td {
        white-space: nowrap;
        padding: 5px;
        /* padding-right: 20px; */
    }
</style>

@endpush

@push('scripts')
<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endpush


@section('content')

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">MEN
                                <span class="d-block text-muted pt-2 font-size-sm">Master Material</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a id="upload-button" href="javascript:" class="btn btn-primary font-weight-bolder"><i class="fa fa-plus-circle"></i> Upload Master</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if( session('status') )
                        <div class="alert alert-custom alert-light-success fade show mb-5" role="alert">
                            <div class="alert-text">{{ session('status') }}</div>
                            <div class="alert-close">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                </button>
                            </div>
                        </div>
                        @endif

                        <div class="table-responsive pb-5 d-none">
                            <table class="table table-bordered" id="table-material">
                                <thead>
                                    <tr>
                                        <th>PLANT</th>
                                        <th>SLOC</th>
                                        <th>MATERIAL</th>
                                        <th>MATERIAL DESC</th>
                                        <th>BATCH</th>
                                        <th>UOM</th>
                                        <th>QTY</th>
                                        <th>PRODUCTION DATE</th>
                                        <th>EXPIRED DATE</th>
                                        <th>SHELF LIFE</th>
                                        <th>MATERIAL TYPE</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="expired-table-summary" style="display: none">
                            <table class="table table-bordered table-dark">
                                <thead>
                                    <tr class="bg-white text-dark">
                                        <th style="width: 10px">SLOC</th>
                                        <th style="width: 10px">MATERIAL TYPE</th>
                                        <th style="width: 10px">BUn</th>
                                        <th style="width: 10px">SUM OF QUANTITY</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="bg-diagonal bg-diagonal-primary h-30px">
                            <h3 class="position-relative text-white mt-1 ml-5">SUMMARY</h3>
                        </div>

                        <div id="expired-data-summary" class="mt-5">
                            
                        </div>

                        <div id="expired-table" style="display: none">
                            <table class="table table-bordered table-dark">
                                <thead>
                                    <tr class="bg-white text-dark">
                                        <th style="width: 10px">PLANT</th>
                                        <th style="width: 10px">SLOC</th>
                                        <th style="width: 10px">MATERIAL</th>
                                        <th>MATERIAL DESCRIPTION</th>
                                        <th style="width: 10px">BATCH</th>
                                        <th style="width: 10px">BUn</th>
                                        <th style="width: 10px">QTY</th>
                                        <th style="width: 10px">PRODUCTION</th>
                                        <th style="width: 10px">EXPIRED</th>
                                        <th style="width: 10px">SHELF LIFE</th>
                                        <th style="width: 10px">DUE DATE</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="bg-diagonal bg-diagonal-primary h-30px">
                            <h3 class="position-relative text-white mt-1 ml-5">DETAILS</h3>
                        </div>

                        <div id="loading-expired-data" class="alert alert-custom alert-light-secondary fade show mt-5" role="alert">
                            <div class="spinner spinner-primary mr-10"></div> Loading Expired Data...
                        </div>

                        <div id="expired-data-detail" class="mt-5">
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">Upload Master</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="{{ url('/templates/template-upload-material-expired.xls') }}" class="btn btn-sm btn-secondary"><i class="fa fa-file-excel"></i> Download template</a>
                    <hr />
                    <form action="{{ url('/men/master-material/upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input name="file" type="file" accept=".csv, .xls">
                        <br />
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $("#upload-button").click(function () {
            $("#modal").modal("show");
        })

        function getStatusColor(status)
        {
            var statusArray = {
                expired   : '#f03030',
                warning2  : '#f09030',
                warning1  : '#f0d848',
                standard  : '#ffffff'
            }

            return statusArray[status];
        }

        $("#loading-expired-data").show();
        $("#expired-data-detail").hide();
        $.ajax({
            url: "{{ url('/men/master-material/get-expired') }}",
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                $("#loading-expired-data").hide();
                $("#expired-data-detail").show();

                var el = $("#expired-data-detail");
                var summary_el = $("#expired-data-summary");
                var number = 1;

                $.each(response.data, function (key, value) {
                    var header = "<h3>"+ number +". "+ key + "</h3>";

                    var expired_table_detail_el = $("#expired-table tbody");
                    expired_table_detail_el.html("");
                    var rows;

                    var expired_table_summary_el = $("#expired-table-summary tbody");
                    expired_table_summary_el.html("");
                    var summaryRows

                    $.each(value, function(_key, _value) {

                        var category        = _value.category;
                        var status          = _value.status;
                        var status_color    = getStatusColor(status)
                        rows = '';

                        $.each(_value.materials, function (__key, __value) {
                            var row = "<tr style='color: #222; background-color: "+ status_color +"'>";
                                var column = "<td>"+ __value.plant +"</td>";
                                    column += "<td>"+ __value.sloc +"</td>";
                                    column += "<td>"+ __value.material +"</td>";
                                    column += "<td>"+ __value.material_description +"</td>";
                                    column += "<td>"+ __value.batch +"</td>";
                                    column += "<td>"+ __value.uom +"</td>";
                                    column += "<td class='text-right'>"+ numberFormat(__value.qty) +"</td>";
                                    column += "<td>"+ formatTanggalIndonesia2(__value.production_date) +"</td>";
                                    column += "<td>"+ formatTanggalIndonesia2(__value.expired_date) +"</td>";
                                    column += "<td>"+ __value.shelf_life +"</td>";
                                    column += "<td>"+ __value.due_date +"</td>";
                            row += column;
                            row += "</tr>";
                            rows += row;
                        })

                        expired_table_detail_el.append(rows);

                        summaryRows = '';
                        $.each(_value.grouped_materials, function (__key, __value) {
                            var sloc = __key;

                            $.each(__value, function (___key, ___value) {
                                $.each(___value, function (____key, ____value) {
                                    var row = "<tr style='color: #222; background-color: "+ status_color +"'>";
                                    var column = "<td>"+ sloc +"</td>";
                                        column += "<td>"+ ____value.type +"</td>";
                                        column += "<td>"+ ____value.uom +"</td>";
                                        column += "<td class='text-right'>"+ numberFormat(____value.sum) +"</td>";
                                    row += column;
                                    row += "</tr>";
                                    summaryRows += row;
                                })
                            })
                        })

                        expired_table_summary_el.append(summaryRows);

                    })

                    var expired_table_summary_finish = $("#expired-table-summary").html();
                    var expired_table_finish = $("#expired-table").html();

                    summary_el.append(header, expired_table_summary_finish);
                    el.append(header, expired_table_finish);

                    number++;
                });
                
            },
            error: function (e) {
                $("#loading-expired-data").hide();
                $("#expired-data-detail").show();
                console.log( e )
            }
        })

        var table = $('#table-material').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/men/master-material/all') }}",
                type: "POST"
            },
            columns: [
                { data : 'plant' },
                { data : 'sloc' },
                { data : 'material' },
                { data : 'material_description' },
                { data : 'batch' },
                { data : 'uom' },
                { data : 'qty' },
                { data : 'production_date', render: function (data) {
                    return formatTanggalIndonesia2(data)
                }},
                { data : 'expired_date', render: function (data) {
                    return formatTanggalIndonesia2(data)
                }},
                { data : 'shelf_life' },
                { data : 'material_type' },
            ],
            // initComplete: function () {
            //     this.api().columns().every(function () {
            //         var column = this;
            //         var input = document.createElement("input");
            //         input.classList.add('form-control')
            //         input.classList.add('input-search')
            //         $(input).appendTo($(column.footer()).empty())
            //         .on('change', function () {
            //             column.search($(this).val(), false, false, true).draw();
            //         });
            //     });
            // }
        });
    </script>
@endpush