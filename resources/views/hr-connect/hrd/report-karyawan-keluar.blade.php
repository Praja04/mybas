@extends('hr-connect.layouts.base')

@push('styles')
<style>
input[type="checkbox"]:disabled {
    opacity: 1; 
    border-width: 2px; 
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2">
            <label>Tanggal:</label>
            <input type="date" class="form-control mb-3" id="tanggalFilter">
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Karyawan Keluar</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false" style="width: 20%;">NIK</th>
                                <th data-ordering="false" style="width: 30%;">Nama</th>
                                <th data-ordering="false">Dept</th>
                                <th data-ordering="false">Alasan Keluar</th>
                                <th data-ordering="false">Tgl Keluar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
<script>
$(document).ready(function(){
    let defaultTanggal = moment().format("YYYY-MM-DD");

    $("#tanggalFilter").val(defaultTanggal);

    $(document).on("change", "#tanggalFilter", function(){
        defaultTanggal = $(this).val();
        $("#tableAjax").DataTable().draw();
    });

    let table = $("#tableAjax").dataTable({
        processing: false,
        serverSide: true,
        ordering: false,
        dom: "<'row'<'col-sm-12 text-right'Bf>>\
            <'row'<'col-sm-12'tr>>\
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
        buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                filename: 'HRD IR - Report Karyawan Keluar',
                exportOptions: {
                    columns: [0,1,2,3,4]
                }
            },
        ],
        ajax: {
            type: "GET",
            url: "/hr-connect/dept-hrd/report-karyawan-keluar/getDataReport",
            data: function(d){
                d.tanggal = defaultTanggal
            }
        },
        columns: [
            { data: 'nik' },
            { data: 'nama' },
            { data: 'kode_bagian' },
            { data: 'alasan_keluar' },
            { data: 'tanggal_keluar' },
        ]
    });
});
</script>
@endpush