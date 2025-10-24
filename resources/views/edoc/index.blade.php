@extends('layouts.base-sidebar')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .fixTableHead {
            overflow-y: auto;
            height: 400px;
        }

        .fixTableHead thead th {
            position: sticky;
            top: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px 15px;
        }

        th {
            background: #dbdbdb;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card card-custom card-stretch bg-light-warning gutter-b Card_Kedatangan"
                        style="border-radius: 35px;">
                        <div class="card-body d-flex align-items-center py-0 mt-8">
                            <a href="javascript:void(0)" onclick="Menu('Kedatangan')"
                                class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">
                                Kedatangan Dokumen/Barang</a>
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                            </div>
                            <img src="{{ asset('assets/media/icons/kedatangan-barang.png') }}" alt=""
                                class="align-self-end h-150px" style="margin-bottom: 10%;" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card card-custom card-stretch bg-light-primary gutter-b Card_Pengambilan"
                        style="border-radius: 35px;">
                        <div class="card-body d-flex align-items-center py-0 mt-8">
                            <a href="javascript:void(0)" onclick="Menu('Pengambilan')"
                                class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">
                                Pengambilan Dokumen/Barang</a>
                            </a>
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                            </div>
                            <img src="{{ asset('assets/media/icons/pengambilan-barang.png') }}" alt=""
                                class="align-self-end h-200px mb-4" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card card-custom card-stretch bg-light-danger gutter-b Card_Pengiriman"
                        style="border-radius: 35px;">
                        <div class="card-body d-flex align-items-center py-0 mt-8">
                            <a href="javascript:void(0)" onclick="Menu('Pengiriman')"
                                class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">
                                Pengiriman Dokumen/Barang</a>
                            </a>
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                            </div>
                            <img src="{{ asset('assets/media/icons/barang-out.png') }}" alt=""
                                class="align-self-end h-200px mb-4" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card card-custom card-stretch bg-light-success gutter-b Card_SerahTerima"
                        style="border-radius: 35px;">
                        <div class="card-body d-flex align-items-center py-0 mt-8">
                            <a href="javascript:void(0)" onclick="Menu('SerahTerima')"
                                class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary">
                                Serah Terima Kurir</a>
                            </a>
                            <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                            </div>
                            <img src="{{ asset('assets/media/icons/serahterima.png') }}" alt=""
                                class="align-self-end h-200px mb-4" />
                        </div>
                    </div>
                </div>
            </div>
            <hr style="border: solid;">
            <div class="row">
                <div class="col-sm-12">
                    <div class="MenuAppend">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_pengambilan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" backdrop="static" keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form action="{{ url('edoc/post_pengambilan') }}" method="post" enctype="multipart/form-data"
                id="post_pengambilan">
                <div class="modal-content">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id_barang" class="IdBarangValue" id="">
                            <input type="hidden" name="nik" class="NikValue" id="">
                            <div class="col-sm-6">
                                <div id="my_camera"></div>
                                <input type="button" class="btn btn-info mt-2" onclick="take_snapshot()"
                                    value="Ambil Foto">
                            </div>
                            <div class="col-sm-6">
                                <div id="results"></div>
                                <input type="text" name="foto" class="ValuePicture" hidden required value="" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info btn-lg"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_konfirmasi" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" backdrop="static" keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ url('edoc/KonfirmasiPengiriman') }}" method="post" enctype="multipart/form-data"
                        id="KonfirmasiPengiriman">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id_barang" class="id_barang" id="">
                            <input type="hidden" name="nik" class="NikValue" id="">
                            <div class="col-sm-6">
                                <div id="cameraPengiriman"></div>
                                <input type="button" class="btn btn-info mt-2" onclick="snapshot_pengiriman()"
                                    value="Ambil Foto">
                            </div>
                            <div class="col-sm-6">
                                <div id="hasilFotoPengiriman"></div>
                                <input type="text" name="foto" class="fotoPengiriman" hidden required
                                    value="" />
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-info btn-lg KonfirmasiBtn"><i class="fas fa-save"></i>
                        Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_serahterima" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" backdrop="static" keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ url('edoc/PostSerahTerima') }}" method="post" enctype="multipart/form-data"
                        id="PostSerahTerima">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id_barang" class="id_barang" id="">
                            <input type="hidden" name="nik" class="NikValue" id="">
                            <div class="col-sm-6">
                                <div id="cameraSerahTerima"></div>
                                <input type="button" class="btn btn-info mt-2" onclick="snapshot_serahterima()"
                                    value="Ambil Foto">
                            </div>
                            <div class="col-sm-6">
                                <div id="hasilFotoserahterima"></div>
                                <input type="text" name="foto" class="fotoserahterima" hidden required
                                    value="" />
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-info btn-lg KonfirmasiBtn"><i class="fas fa-save"></i>
                        Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal-foto-ktp">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ambil Foto KTP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="foto-ktp-kamera"></div>
                    <input type="button" class="btn btn-info mt-2" onclick="ambilFotoKTP()" value="Ambil Foto">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/webcam/webcam.min.js') }}"></script>

    <script type="text/javascript">
        $('#dept-penerima').select2();
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        function openFotoKTPCamera() {
            Webcam.attach('#foto-ktp-kamera');
            $('#modal-foto-ktp').modal('show');
        }

        // update departemen penerima hanya yang memiliki status 1 yang aktif yg boleh muncul
        function Menu(menu) {
            if (menu == 'Kedatangan') {
                $('.gutter-b').toggle('slow')
                $('.Card_' + menu).toggle('slow')
                $('.MenuAppend').html('');
                $('.MenuAppend').append(`
<div class="card card-custom">
    <form action="{{ url('edoc/post_kedatangan') }}" method="POST" enctype="multipart/form-data" id="PostKedatanganForm">
        @csrf
        <div class="card-body">
            <div class="form-group mb-8">
                <div class="alert alert-custom alert-default" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                    <div class="alert-text">
                        <h3>
                            FORM KEDATANGAN BARANG
                        </h3>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="tanggal-kedatangan" class="col-sm-2 col-form-label text-right">Tanggal Kedatangan
                </label>
                <div class="col-sm-10">
                    <input class="form-control" type="date" name="tanggal_kedatangan" id="tanggal-kedatangan"
                        value="{{ date('Y-m-d') }}" required />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="dept-penerima" class="col-sm-2 col-form-label text-right">Departemen Penerima</label>
                <div class="col-sm-10">
                    <select class="form-control" name="dept_penerima" id="dept-penerima" style="width: 100%;" required>
                        <option value="" selected disabled>Pilih Departemen</option>
                        @foreach ($dept as $item)
                            @if ($item->status == 1)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-penerima" class="col-sm-2 col-form-label text-right">Nama Penerima</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_penerima" id="nama-penerima"
                        placeholder="Contoh : Muhammad Machbub Marzuqi" required />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-pt-pengirim" class="col-sm-2 col-form-label text-right">Nama PT Pengirim</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_pt_pengirim" id="nama-pt-pengirim" required
                        placeholder="Nama PT yang mengirimkan barang atau dokumen" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="no-identitas-kurir" class="col-sm-2 col-form-label text-right">No Identitas Pengantar
                    <br />( kurir )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="no_identitas_kurir" id="no-identitas-kurir"
                        required placeholder="Nomor KTP / Nomor SIM" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="foto-kartu-identitas-kurir" class="col-sm-2 col-form-label text-right">Foto Kartu Identitas
                    <br />( kurir )</label>
                <div class="col-sm-10">
                    <div id="foto-ktp-results"></div>
                    <input type="text" name="foto_kartu_identitas_kurir" class="ktpValuePicture" hidden value=""
                        id="foto-kartu-identitas-kurir" required />
                    <button class="btn btn-secondary btn-sm" type="button" onClick="openFotoKTPCamera()">Ambil
                        Foto</button>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-kurir" class="col-sm-2 col-form-label text-right">Nama Pengantar <br />( kurir
                    )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_kurir" id="nama-kurir" required
                        placeholder="Nama orang yang mengirimkan barang atau dokumen" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="no-hp-kurir" class="col-sm-2 col-form-label text-right">No. HP Pengantar <br />( kurir
                    )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="no_hp_kurir" id="no-hp-kurir" required
                        placeholder="Contoh : 081238273282" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class=" form-group row">
                <label for="jenis" class="col-sm-2 col-form-label text-right">Jenis Barang</label>
                <div class="col-sm-10">
                    <select class="form-control" name="jenis" id="jenis" required>
                        <option value="" selected disabled>Pilih Jenis Barang</option>
                        <option value="Barang">Barang</option>
                        <option value="Dokumen">Dokumen</option>
                    </select>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="keterangan" class="col-sm-2 col-form-label text-right">Keterangan</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="keterangan" id="keterangan" rows="5" placeholder="Masukan keterangan"></textarea>
                    <small class="form-text text-muted">*Opsional</small>
                </div>
            </div>
            <div class="float-right mb-4">
                <button type="submit" class="btn btn-info btn-lg mr-2 SaveKedatangan" style="border-radius: 10px;">
                    Simpan</button>
            </div>
        </div>
    </form>
</div>
`);
                Webcam.reset();
            } else if (menu == 'Pengambilan') {
                $('.gutter-b').toggle('slow')
                $('.Card_' + menu).toggle('slow')
                $('.MenuAppend').html('')
                $('.MenuAppend').append(`
                        <div class="card card-custom">
                              <div class="card-body">
                                  <div class="form-group validated">
                                      <div class="input-group">
                                          <input type="text" id="scanner" class="form-control is-active" placeholder="SILAHKAN SCAN IDCARD" style="zoom : 150%;" />
                                          <div class="input-group-append"><span class="input-group-text" ><i class="la la-user"></i></span></div>
                                        </div>
                                    </div>
                                    <div class="AppendScanPengambilan">
                                        
                                    </div>
                                </div>
                            </div>`)
                $('#scanner').focus()
            } else if (menu == 'Pengiriman') {
                Webcam.reset();
                var nik = sessionStorage.getItem('nik')
                $('.gutter-b').toggle('slow')
                $('.Card_' + menu).toggle('slow')
                $('.MenuAppend').html('')
                $('.MenuAppend').append(`
                        <div class="card card-custom">
                              <div class="card-body">
                                  <div class="form-group validated">
                                      <div class="input-group">
                                          <input type="text" id="scannerPengiriman" class="form-control is-active" placeholder="SILAHKAN SCAN IDCARD" style="zoom : 150%;" />
                                          <div class="input-group-append"><span class="input-group-text" ><i class="la la-user"></i></span></div>
                                        </div>
                                    </div>
                                    <div class="AppendScanPengiriman">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="list_pengiriman">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>#</th>
                                                        <th>Dept Pengirim</th>
                                                        <th>Nama Penerima</th>
                                                        <th>Nama PT Penerima</th>
                                                        <th>Tanggal Pengiriman</th>
                                                        <th>Jenis</th>
                                                        <th>Kurir</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                        </table>
                                        <div class="float-right">
                                            <a href="#modal_konfirmasi" data-toggle="modal" class="btn btn-lg btn-info hide  mb-4 btnKonfirmasi" style="border-radius-"10px;><i class="fas fa-check-circle"> </i> Konfirmasi</a>    
                                        </div>
                                    </div>
                                </div>
                            </div>`)
                $('#scannerPengiriman').focus()

                $('.btnKonfirmasi').on('click', function() {
                    getValue();
                });
            } else if (menu == 'SerahTerima') {
                $('.gutter-b').toggle('slow')
                Webcam.attach('#cameraSerahTerima');
                $('.Card_' + menu).toggle('slow')
                $('.MenuAppend').html('');
                $('.MenuAppend').append(`<div class="card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="table_serah_terima" style="zoom: 115%;">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>NO</th>
                                                                <th>#</th>
                                                                <th>JENIS</th>
                                                                <th>KURIR</th>
                                                                <th>FOTO</th>
                                                                <th>NAMA</th>
                                                                <th>DEPT</th>
                                                                <th>NAMA PENERIMA</th>
                                                                <th>NAMA PT PENERIMA</th>
                                                                <th>KETERANGAN</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                    <div class="float-right">
                                                        <a href="#modal_serahterima" data-toggle="modal" class="btn btn-lg btn-info hide  mb-4 btnSerahTerima" style="border-radius: 10px;"><i class="fas fa-check-circle"> </i> Lanjut</a>    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`);

                $('.btnSerahTerima').on('click', function() {
                    getValue();
                });

                $('#table_serah_terima').DataTable();
                $('#table_serah_terima tbody').html('');

                $.ajax({
                    url: "{{ url('edoc/ListSerahTerimaKurir') }}",
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(response) {
                        $('.btnSerahTerima').show();
                        var no = 1;
                        $.each(response.data.data, function(k, v) {
                            $('#table_serah_terima tbody').append(`
                                <tr class="text-center">
                                    <td>` + no + `</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="checkbox-list">
                                                <label class="checkbox">
                                                    <input type="checkbox" name="id_barang" value="${v.id_barang}" class="checks" />
                                                        <span></span>
                                                    Pilih
                                            </label>
                                        </div>
                                    </div>
                                    </td>
                                    <td>` + v.jenis + `</td>
                                    <td>` + v.kurir + `</td>
                                    <td><img class="img-sm" src="{{ url('e-doc/konfirmasi-pengiriman') }}/` + v.foto + `" width="100px;" height="100px;" style="border-radius: 25px;"></td>
                                    <td>` + response.data.nama[k] + `</td>
                                    <td>` + v.dept_pengirim + `</td>
                                    <td>` + v.nama_penerima + `</td>
                                    <td>` + v.nama_pt_penerima + `</td>
                                    <td>` + v.keterangan + `</td>
                                </tr>
                            `);
                            no++;
                        });
                    }
                });
            }

            $('#scanner').keypress(function(e) {
                if (e.which == 13) {
                    var rfid = $('#scanner').val();
                    if (rfid == '') {
                        alert('Silahkan Scan ID Card');
                        return false;
                    }
                    $.ajax({
                        url: "{{ url('edoc/ScanPengambilan') }}" + '/' + rfid,
                        type: "GET",
                        data: {
                            rfid: rfid,
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Webcam.attach('#my_camera');
                                sessionStorage.setItem('nik', response.data.pic.nik);
                                $('#scanner').val('')
                                $('.AppendScanPengambilan').html('');
                                $('.AppendScanPengambilan').append(
                                    '\
                                                                                                                                                <h2 class="text-center">\
                                                                                                                                                    <span class="badge badge-info"><i class="fas fa-user text-white mr-2"><b class="NamaPIC text-white ml-3">' +
                                    response.data.pic.nama +
                                    '</b></i></span>\
                                                                                                                                                    <span class="badge badge-info"><i class="fas fa-users mr-2 text-white"><b class="DeptPIC text-white ml-3">' +
                                    response.data.pic.dept +
                                    '</b></i></span>\
                                                                                                                                                        <input type="hidden" class="form-control DeptValue" value="' +
                                    response.data.pic.dept +
                                    '" />\
                                                                                                                                                    </div>\
                                                                                                                                                </h2>\
                                                                                                                                                <div class="card card-custom gutter-b">\
                                                                                                                                                    <div class="card-header card-header-tabs-line">\
                                                                                                                                                        <div class="card-toolbar">\
                                                                                                                                                            <ul class="nav nav-tabs nav-bold nav-tabs-line tabappend">\
                                                                                                                                                            </ul>\
                                                                                                                                                        </div>\
                                                                                                                                                    </div>\
                                                                                                                                                    <div class="card-body">\
                                                                                                                                                        <div class="tab-content tabappendkonten">\
                                                                                                                                                    </div>'
                                )

                                $.each(response.data.groupBy, function(key, value) {
                                    $('.tabappend').append(
                                        '\
                                                                                                                                            <li class="nav-item">\
                                                                                                                                                <a class="nav-link" onclick="ShowTable(\'' +
                                        key +
                                        '\')" data-toggle="tab" href="#tab_' + key +
                                        '">\
                                                                                                                                                    <span class="nav-icon"><i class="fas fa-list-alt"></i></span>\
                                                                                                                                                    <span class="nav-text">' +
                                        key +
                                        '</span>\
                                                                                                                                                </a>\
                                                                                                                                            </li>'
                                    )
                                });
                                $.each(response.data.groupBy, function(key, value) {
                                    $('.tabappendkonten').append(
                                        '\
                                                                                                                                                <div class="tab-pane fade show" id="tab_' +
                                        key +
                                        '" role="tabpanel" aria-labelledby="tab_' + key +
                                        '">\
                                                                                                                                                    <div class="table-responsive">\
                                                                                                                                                        <table class="table table-bordered">\
                                                                                                                                                            <thead>\
                                                                                                                                                                <tr class="text-center">\
                                                                                                                                                                    <th>No.</th>\
                                                                                                                                                                    <th>PILIH</th>\
                                                                                                                                                                    <th style="background-color: #DFF2E9">#</th>\
                                                                                                                                                                    <th>Dept Penerima</th>\
                                                                                                                                                                    <th>Nama PT Pengirim</th>\
                                                                                                                                                                    <th>Tanggal Kedatangan</th>\
                                                                                                                                                                    <th>Keterangan</th>\
                                                                                                                                                                    <th>Petugas</th>\
                                                                                                                                                                    <th>Status</th>\
                                                                                                                                                                </tr>\
                                                                                                                                                            </thead>\
                                                                                                                                                            <tbody id="table_' +
                                        key +
                                        '">\
                                                                                                                                                            </tbody>\
                                                                                                                                                        </table>\
                                                                                                                                                    </div>'
                                    )
                                });
                            }
                        },
                        error: function(error) {
                            alert('ID CARD tidak di kenali');
                        }
                    });
                }
            });

            var table_pengiriman = '';

            $('#scannerPengiriman').keypress(function(e) {
                if (e.which == 13) {
                    var rfid = $('#scannerPengiriman').val();
                    if (rfid == '') {
                        alert('Silahkan Scan ID Card');
                        return false;
                    }
                    $.ajax({
                        url: "{{ url('edoc/ScanPengiriman') }}" + '/' + rfid,
                        type: "GET",
                        data: {
                            rfid: rfid,
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('.NikValue').val(response.data.nik)
                                Webcam.attach('#cameraPengiriman');
                                $('#scannerPengiriman').val('')
                                $('.btnKonfirmasi').show()

                                if (table_pengiriman != '') {
                                    table_pengiriman.destroy();
                                    $('#list_pengiriman').empty();
                                }

                                //tinggal looping aja
                                table_pengiriman = $('#list_pengiriman').DataTable({
                                    processing: true,
                                    serverSide: true,
                                    searching: true,
                                    ajax: {
                                        url: "{{ url('edoc/ShowListPengiriman') }}/" + response
                                            .data.nik,
                                        type: 'GET',
                                    },
                                    columns: [{
                                            data: 'DT_RowIndex',
                                            name: 'DT_RowIndex',
                                            orderable: false,
                                            searchable: false
                                        },
                                        {
                                            data: null,
                                            name: null,
                                            sortable: false,
                                            render: function(data, type, row) {
                                                return '<div class="form-group">\
                                                                                                                                                            <div class="checkbox-list">\
                                                                                                                                                                <label class="checkbox">\
                                                                                                                                                                    <input type="checkbox" name="id_barang[]" value="' +
                                                    row.id_barang +
                                                    '" class="checks" />\
                                                                                                                                                                        <span></span>\
                                                                                                                                                                    Pilih\
                                                                                                                                                            </label>\
                                                                                                                                                        </div>\
                                                                                                                                                    </div>'
                                            }
                                        },
                                        {
                                            data: 'dept_pengirim',
                                            name: 'dept_pengirim'
                                        },
                                        {
                                            data: 'nama_penerima',
                                            name: 'nama_penerima'
                                        },
                                        {
                                            data: 'nama_pt_penerima',
                                            name: 'nama_pt_penerima'
                                        },
                                        {
                                            data: null,
                                            name: null,
                                            sortable: false,
                                            render: function(data, type, row) {
                                                return formatTanggalIndonesia2(row
                                                    .tanggal_pengiriman)
                                            }
                                        },
                                        {
                                            data: 'jenis',
                                            name: 'jenis'
                                        },
                                        {
                                            data: 'kurir',
                                            name: 'kurir'
                                        },
                                        {
                                            data: null,
                                            name: null,
                                            sortable: false,
                                            render: function(data, type, row) {
                                                if (row.status == '1') {
                                                    return '<span class="badge badge-dark">Belum Di Konfirmasi</span>'
                                                } else {
                                                    return '<span class="badge badge-info">Menunggu Kurir Datang</span>'
                                                }
                                            }
                                        }
                                    ],
                                });
                            }
                        },
                        error: function(error) {
                            alert('ID CARD tidak di kenali');
                        }
                    });
                }
            });

            $('#KonfirmasiPengiriman').on('submit', function() {
                $('.KonfirmasiBtn').addClass('disabled', true);
                $('.KonfirmasiBtn').addClass('spinner spinner-left pl-15');
            });

            $('#PostSerahTerima').on('submit', function() {
                $('.KonfirmasiBtn').addClass('disabled', true);
                $('.KonfirmasiBtn').addClass('spinner spinner-left pl-15');
            });
        }

        function ShowTable(jenis) {
            var dept = $('.DeptValue').val();
            $.ajax({
                url: "{{ url('edoc/GetListBarang') }}" + '/' + dept + '/' + jenis,
                type: "GET",
                data: {
                    jenis: jenis,
                    dept: dept,
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#table_' + jenis).html('');
                        var nik = sessionStorage.getItem('nik');
                        $.each(response.data, function(key, value) {
                            if (value.status == 1) {
                                var status = '<span class="badge badge-danger">Belum Diambil</span>';
                            } else {
                                var status = '<span class="badge badge-info">Sudah Diambil</span>';
                            }
                            $('#table_' + jenis).append(`
                                <tr class="text-center">
                                    <td>${key+1}</td>
                                    <td>
                                        <div class="form-group">
                                            <div class="checkbox-list">
                                                <label class="checkbox">
                                                    <input type="checkbox" name="id_barang" value="${value.id_barang}" class="ambil_${jenis}_checks" />
                                                        <span></span>
                                                    Pilih
                                            </label>
                                        </div>
                                    </td>
                                    <td style="background-color: #DFF2E9"><a class="btn btn-sm btn-dark" onclick="AmbilBarang('${value.id_barang}', '${nik}')"><i class="fas fa-check-double"></i> Ambil</a></td>
                                    <td>${value.dept_penerima}</td>
                                    <td>${value.nama_pt_pengirim}</td>
                                    <td>${formatTanggalIndonesia2(value.tanggal_kedatangan)}</td>
                                    <td>${value.keterangan}</td>
                                    <td>${value.created_by}</td>
                                    <td>${status}</td>
                                </tr>`)
                        });

                        $('.ambil-multiple').remove()

                        $('#tab_' + jenis + ' .table-responsive').append(`
                            <button type="button" class="btn btn-primary btn-sm ambil-multiple" onClick="getValuePenerimaan('${jenis}')">Ambil Barang Yang Dipilih</button>
                        `);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error get data from ajax, contact ite departement ');
                }
            })
        }

        function AmbilBarang(id_barang, nik) {
            $('#modal_pengambilan').modal('show');
            sessionStorage.setItem('id_barang', id_barang);
        }

        $('#post_pengambilan').on('submit', function(e) {
            e.preventDefault();
            $('.IdBarangValue').val(sessionStorage.getItem('id_barang'));
            $('.NikValue').val(sessionStorage.getItem('nik'));

            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#modal_pengambilan').modal('hide');
                        Swal.fire(
                            'Yeay!',
                            'Barang berhasil diambil',
                            'success'
                        )
                        ShowTable(response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error get data from ajax, contact ite departement ');
                }
            })
        });

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $('.ValuePicture').val(data_uri);
                document.getElementById('results').innerHTML =
                    '<img src="' + data_uri + '"/>';
            });
        }

        function ambilFotoKTP() {
            Webcam.snap(function(data_uri) {
                $('.ktpValuePicture').val(data_uri);
                document.getElementById('foto-ktp-results').innerHTML =
                    '<img src="' + data_uri + '"/>';
            });

            $('#modal-foto-ktp').modal('hide');
        }

        function snapshot_pengiriman() {
            Webcam.snap(function(data_uri) {
                $('.fotoPengiriman').val(data_uri);
                document.getElementById('hasilFotoPengiriman').innerHTML =
                    '<img src="' + data_uri + '"/>';
            });
        }

        function snapshot_serahterima() {
            Webcam.snap(function(data_uri) {
                $('.fotoserahterima').val(data_uri);
                document.getElementById('hasilFotoserahterima').innerHTML =
                    '<img src="' + data_uri + '"/>';
            });
        }

        function getValuePenerimaan(jenis) {
            var myArray = [];
            var checks = document.querySelectorAll('.ambil_' + jenis + '_checks:checked');

            if (checks.length == 0) {
                Swal.fire(
                    'Oops!',
                    'Silahkan pilih barang yang akan diambil',
                    'error'
                ).then((result) => {
                    if (result.value) {
                        $('#modal_pengambilan').modal('hide');
                    }
                })

                return;
            } // if none were checked return from the function

            for (i = 0; i < checks.length; i++) {
                myArray.push(checks[i].value);
                $('.IdBarangValue').val(myArray);
                sessionStorage.setItem('id_barang', myArray);
            }

            $('#modal_pengambilan').modal('show');
        }

        function getValue() {
            var myArray = [];
            var checks = document.querySelectorAll('.checks:checked');

            if (checks.length == 0) {
                Swal.fire(
                    'Oops!',
                    'Silahkan pilih barang yang akan dikirim',
                    'error'
                ).then((result) => {
                    if (result.value) {
                        $('#modal_konfirmasi').modal('hide');
                    }
                })

                return;
            } // if none were checked return from the function

            for (i = 0; i < checks.length; i++) {
                myArray.push(checks[i].value);
                $('.id_barang').val(myArray);
            }
        }

        $('#PostKedatanganForm').submit(function() {
            $('.SaveKedatangan').addClass('disabled', true);
            $('.SaveKedatangan').addClass('spinner spinner-left pl-15');
        });

        $(document).on('click', '.SaveKedatangan', function(e) {
            const foto = $('.ktpValuePicture').val();
            if (!foto || foto.trim() === '') {
                e.preventDefault();
                alert('Silakan ambil foto kartu identitas kurir terlebih dahulu.');
                return false;
            }
        });
    </script>
@endpush
