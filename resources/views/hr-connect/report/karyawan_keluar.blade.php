@extends('hr-connect.layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<style>
    /* Style untuk input date */
    .custom-date-input {
        height: 30px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .th-inner {
    display: block;
}

.th-inner span {
    display: inline-block;
}

.th-inner span:before {
    content: " ";
}
#tableAjax thead th {
    text-align: center; /* Membuat teks menjadi di tengah */
    vertical-align: middle; /* Mengatur posisi vertikal menjadi di tengah */
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Report Karyawan Keluar</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <h5>Filter Data</h5>
                        <div class="col-lg-2">
                            <!-- [x] Membuat filter divisi !-->
                            <select class="js-example-basic-single form-control" id="pilihDivisi">
                                <option value="">Pilih Divisi</option>
                                @foreach($kodeDivisi as $divisi)
                                    <option value="{{ $divisi }}">{{ $divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <!-- [x] Membuat filter bagian !-->
                            <select class="js-example-basic-single form-control" id="pilihBagian">
                                <option value="">Kode Bagian</option>
                                @foreach($kodeBagian as $bagian)
                                    <option value="{{ $bagian }}">{{ $bagian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <!-- [x] Membuat filter kode group !-->
                            <select class="js-example-basic-single form-control" id="pilihKodeGroup">
                                <option value="">Kode Group</option>
                                @foreach($kodeGroup as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <!-- [x] Membuat filter loker !-->
                            <select class="js-example-basic-single form-control" id="pilihLoker">
                                <option value="">Pilih Loker</option>
                                @foreach($lokers as $loker)
                                <option value="{{ $loker->kode_blok.'-'.$loker->no_loker }}">{{ $loker->kode_blok.' '.$loker->no_loker }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-lg-2">
                            <!-- [x] Membuat filter tanggal masuk !-->
                            <input type="date" class="custom-date-input" id="pilihTanggalMasuk">
                        </div>
                    </div>
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Nik</th>
                                <th rowspan="2">Kode Divisi</th> 
                                <th rowspan="2">Kode Bagian</th> 
                                <th rowspan="2">Kode Group</th> 
                                <th colspan="2">Loker</th>
                                <th rowspan="2">Tanggal Keluar</th> 
                            </tr>
                            <tr>
                                <th width="10%">Kode Blok</th>
                                <th width="10%">No. Loker</th>
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
<script>
let table = $("#tableAjax").dataTable({
    processing: false,
    serverSide: true,
    paging: false,
    // dom: 'Bfrtip',
    // buttons: [
    //     'excel', 'pdf', 'print'
    // ],
    ajax: {
        type: "GET",
        url: "/hr-connect/report/getDataKaryawanKeluar"
    },
    columns: [
        { data: 'nama' },
        { data: 'nik' },
        { data: 'kode_divisi' },
        { data: 'kode_bagian' },
        { data: 'kode_group' },
        { data: 'kode_blok' },
        { data: 'no_loker' },
        { 
            data: 'tanggal_keluar',
            render: function(data, type, row){
                let entryDate = new Date(row.tanggal_keluar);

                let tgl_keluar = entryDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

                return `${tgl_keluar}`;
            }
        },
    ]
});

table.on('draw.dt', function() {
    $('.js-example-basic-single').select2();
});

$("#pilihDivisi").change(function(){
    let search = $(this).val();
    // Ubah bila urutan kolom diubah
    table.api().columns(2).search(search).draw();
});

$("#pilihBagian").change(function(){
    let search = $(this).val();
    // Ubah bila urutan kolom diubah
    table.api().columns(3).search(search).draw();
});

$("#pilihKodeGroup").change(function(){
    let search = $(this).val();
    // Ubah bila urutan kolom diubah
    table.api().columns(4).search(search).draw();
});

$("#pilihLoker").change(function(){
    let search = $(this).val();

    let parts = search.split('-');
    let kodeBlok = parts[0];
    let noLoker = parts[1];
    
    // Ubah bila urutan kolom diubah
    table.api().columns(5).search(kodeBlok).draw();
    table.api().columns(6).search(noLoker).draw();
});

$("#pilihTanggalMasuk").change(function(){
    let search = $(this).val();
    // Ubah bila urutan kolom diubah
    table.api().columns(7).search(search).draw();
});
</script>
@endpush