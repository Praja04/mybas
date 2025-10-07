@extends('layouts.base')

@push('styles')
    <style type="text/css">
        .hide {
            display: none !important
        }

        ._zoom {
            margin-top: -7px;
            margin-bottom: -7px;
            margin-left: -7px;
        }

        .item-obat {
            /* border: 1px solid #ccc; */
            width: 100%;
            min-height: 100px;
            cursor: pointer;
            border-radius: 10px;
            transition-duration: .2s;
            padding: 10px;
            border: 1px;
            border-color: transparent;
            border-style: solid;
        }

        .item-obat:hover {
            -webkit-box-shadow: 4px 7px 15px -11px rgba(0, 0, 0, 0.51);
            -moz-box-shadow: 4px 7px 15px -11px rgba(0, 0, 0, 0.51);
            box-shadow: 4px 7px 15px -11px rgba(0, 0, 0, 0.51);
            border-color: #eee;
            transform: scale(1.1)
        }

        .outer-cart-obat {
            padding-right: 5px;
            max-height: 300px;
            overflow: hidden;
            overflow-y: auto;
        }

        .item-cart-obat {
            /* position: relative; */
            border: 1px solid #eee;
            border-radius: 5px;
            padding-top: 5px;
            padding-bottom: 5px
        }

        .btn-xs {
            padding: 0;
            height: 30px;
            width: 100%;
        }

        .form-control-xs {
            /* padding: 0 !important; */
            padding-left: 5px;
            height: 30px;
            text-align: center
        }

        .delete-icon:hover i {
            color: #333
        }

        .judul {
            position: relative;
            margin-top: -10px;
        }

        .judul::after {
            background: #fff linear-gradient(90deg, #850000 0, #a80000) repeat scroll 0 0;
            border-radius: 12px;
            content: "";
            height: 3px;
            left: 1px;
            position: absolute;
            bottom: -4px;
            width: 70px;
        }

        .form-label-group-select select {
            padding-left: .5rem;
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group-select .select2-selection__rendered {
            padding-bottom: .25rem !important;
            padding-left: .75rem !important;
            padding-top: 1.25rem !important;
        }

        .form-label-group-select {
            position: relative;
        }

        .form-label-group-select label {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            margin-bottom: 0;
            /* Override default `<label>` margin */
            line-height: 1.5;
            color: #495057;
            pointer-events: none;
            cursor: text;
            /* Match the input under the label */
            border: 1px solid transparent;
            border-radius: .25rem;
            transition: all .1s ease-in-out;
            height: 3.125rem;
            padding: .75rem;
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .form-label-group input,
        .form-label-group label {
            height: 3.125rem;
            padding: .75rem;
        }

        .form-label-group label {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            margin-bottom: 0;
            /* Override default `<label>` margin */
            line-height: 1.5;
            color: #495057;
            pointer-events: none;
            cursor: text;
            /* Match the input under the label */
            border: 1px solid transparent;
            border-radius: .25rem;
            transition: all .1s ease-in-out;
        }

        .form-label-group input::-webkit-input-placeholder {
            color: transparent;
        }

        .form-label-group input::-moz-placeholder {
            color: transparent;
        }

        .form-label-group input:-ms-input-placeholder {
            color: transparent;
        }

        .form-label-group input::-ms-input-placeholder {
            color: transparent;
        }

        .form-label-group input::placeholder {
            color: transparent;
        }

        .form-label-group input:not(:-moz-placeholder-shown) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:-ms-input-placeholder) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:placeholder-shown) {
            padding-top: 1.25rem;
            padding-bottom: .25rem;
        }

        .form-label-group input:not(:-moz-placeholder-shown)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:not(:-ms-input-placeholder)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:not(:placeholder-shown)~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }

        .form-label-group input:-webkit-autofill~label {
            padding-top: .25rem;
            padding-bottom: .25rem;
            font-size: 12px;
            color: #777;
        }
    </style>

    <style type="text/css">
        .option {
            transition: .2s;
            cursor: pointer;
        }

        .option:hover {
            transform: scale(0.9)
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-root">
        <div class="login login-2 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login"
            style="max-height: 440px">
            <div
                class="login-aside order-2 order-lg-1 d-flex flex-column-fluid flex-lg-row-auto bgi-size-cover bgi-no-repeat p-7 p-lg-10">
                <div class="d-flex flex-row-fluid flex-column justify-content-between">
                    <div class="d-flex flex-column-fluid flex-column flex-center mt-3 mt-lg-0">
                        <a class="mb-15 text-center">
                            <img id="foto" style="width: 100px" class="opacity-90"
                                src="{{ asset('/assets/media/icons/id-card.jpg') }}" alt="Icon id card">
                        </a>
                        <div id="user-info" class="text-left hide">
                            <div class="p-1" style="border:1px solid #eee; width: 200px; border-radius: 5px">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td><strong>NIK</strong></td>
                                            <td class="px-2">:</td>
                                            <td><span id="view-nik"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>NAMA</strong></td>
                                            <td class="px-2">:</td>
                                            <td><span id="view-nama"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>BAGIAN</strong></td>
                                            <td class="px-2">:</td>
                                            <td><span id="view-bagian"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button onClick="getRekamMedis()" class="btn btn-primary btn-sm mt-3" type="button"
                                style="width: 100%"> <i class="fa fa-clock"></i> Rekam Medis</button>
                            <button onClick="window.location.reload()" class="btn btn-sm btn-danger btn-full mt-3"
                                style="width: 100%">Cancel</button>
                        </div>
                        <div id="scanner-container" class="login-form login-signin" style="width: 200px">
                            <div class="text-center mb-10 mb-lg-20">
                                <h2 class="font-weight-bold">Pendaftaran</h2>
                                <p class="text-muted font-weight-bold">Silahkan Scan IDCard Karyawan</p>
                            </div>
                            <div class="form-group py-3 m-0">
                                <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text"
                                    placeholder="Scan Kartu" id="scanner" autocomplete="off" autofocus />
                            </div>

                            <input type="hidden" id="temp_rfid" name="temp_rfid">
                            <input id="nik" style="width: 50px" type="text" class="nik" hidden>
                        </div>
                    </div>
                </div>
            </div>
            <div id="banner" class="order-1 order-lg-2 flex-column-auto flex-lg-row-fluid d-flex flex-column p-7"
                style="background: url({{ url('/assets/media/logos/bg_klinik.jpg') }}) no-repeat; background-size: cover; max-height: 460px">
                <div class="d-flex flex-column-fluid flex-lg">
                    <div class="d-flex flex-column justify-content">
                        <h3 class="display-3 font-weight-bold mt-5 mb-2 text-dark">E-KLINIK BAS</h3>
                        <p class="pl-2 rounded bg-white font-weight-bold font-size-lg text-dark opacity-80">Administrasi Log
                            Kedatangan Pasien, Jenis
                            <br /> Obat Dan Diagnosa Karyawan.
                        </p>
                    </div>
                </div>
            </div>
            <div id="transaction"
                class="hide order-1 order-lg-2 flex-column-auto flex-lg-row-fluid d-flex flex-column p-7 bg-white shadow-sm"
                style="border-radius: 20px; min-height: 460px">
                <div class="row" style="height: 90%">
                    <div class="col-8">
                        <div class="row" id="obat-container">
                            <div class="col-7">
                                <h5 class="judul mb-5">Obat</h5>
                                <input id="search-obat" type="text" class="form-control form-control-sm"
                                    placeholder="Cari Obat..">
                                <div class="row mt-2 px-4 scrollbar py-3" style="max-height:300px; overflow-y: auto"
                                    id="container-obat">
                                    @foreach ($obat as $o)
                                        <div onClick="addToCart({{ $o }})" class="col-3 mb-3 item-obat-container"
                                            data-search="{{ strtolower($o->nama_obat) }}">
                                            <div class="item-obat">
                                                <img style="width: 100%"
                                                    src="{{ asset('/assets/media/icons/obat-no-image.jpg') }}"
                                                    alt="{{ $o->nama_obat }}">
                                                <span style="font-size: 10px">{{ $o->nama_obat }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-5" style="border-left: 1px solid #eee">
                                <div class="outer-cart-obat scrollbar pb-3">
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row" id="validasi-container" style="display: none">
                            <div class="col-12">
                                <h5 class="judul mb-5">Validasi SKD</h5>
                                <div class="row mt-2 px-4 scrollbar py-3" style="max-height:300px; overflow-y: auto">
                                    <table id="table-pemeriksaan" class="table table-bordered">
                                        <thead>
                                            <div class="card-body">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="exampleSelectl">Upload Bukti SKD</label>
                                                        <label for=""></label>
                                                        <img class="img-preview img-fluid mb-3 col-sm-2" alt="">
                                                        <input disabled type="file" class="form-control-file" name="fileskd"
                                                            id="fileskd" placeholder="Masukan"
                                                            aria-describedby="fileHelpId" required
                                                            onchange="previewImage()">
                                                    </div>
                                                </div>
                                                <tr>
                                                    {{-- <th>Tgl Pemeriksaan</th>
                                                <th>Waktu Pemeriksaan</th>
                                                <th>Keluhan</th>
                                                <th>Diagnosa</th>
                                                <th>Dokter</th>
                                                <th></th> --}}
                                                </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-4">
                        <h5 class="judul mb-5">Detail Pemeriksaan <div class="btn btn-danger font-bold"
                                id="jenis-pemeriksaan-info"
                                style="padding-top: 1px !important; padding-bottom: 1px !important;"></div>
                        </h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-label-group">
                                    <input type="text" id="keluhan" class="form-control" placeholder="Keluhan"
                                        required="" autofocus="">
                                    <label for="keluhan">Keluhan</label>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="form-label-group-select mb-4 w-100">
                                        <select name="diagnosa" id="diagnosa" class="form-control form-control"
                                            style="width: 100%">
                                            <option value="">Pilih</option>
                                            @foreach ($diagnosa as $d)
                                                <option value="{{ $d->id }}">{{ $d->nama_diagnosa }}</option>
                                            @endforeach
                                        </select>
                                        <label for="diagnosa">Diagnosa</label>
                                    </div>
                                    <div class="ml-1">
                                        <button onClick="tambahDiagnosa()" title="Tambah Diagnosa"
                                            class="btn btn-primary">+</button>
                                    </div>
                                </div>
                                <div class="form-group mb-2 row">
                                    <div class="col-6">
                                        <div class="form-label-group">
                                            <input type="number" id="suhu" class="form-control" placeholder="Suhu"
                                                required="" autofocus="">
                                            <label for="suhu">Suhu</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-label-group">
                                            <input type="text" id="tensi" class="form-control"
                                                placeholder="Tensi" required="" autofocus="">
                                            <label for="tensi">Tensi</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-label-group-select mb-2">
                                    <select name="tindakan" id="tindakan" class="form-control form-control"
                                        style="width: 100%">
                                        {{-- dipulangkan di update nama nya --}}
                                        <option value="Dipulangkan dan berobat">Dipulangkan dan berobat</option>
                                        <option value="Dirujuk">Dirujuk</option>
                                        {{-- baru kotak p3k --}}
                                        @foreach ($departments as $department)
                                            @if ($department->name === 'HSE')
                                                <option value="Mengambil kotak p3k">Mengambil kotak p3k</option>
                                            @endif
                                        @endforeach
                                        <option value="Kembali Bekerja" selected>Kembali Bekerja</option>
                                    </select>
                                    <label for="tindakan">Tindakan</label>
                                </div>
                                <div class="form-label-group mt-4">
                                    <input type="text" id="keterangan" class="form-control" placeholder="Keterangan"
                                        required="" autofocus="">
                                    <label for="keterangan">Keterangan</label>
                                </div>
                                <div class="form-label-group-select mb-2">
                                    <select name="komorbid" id="komorbid" class="form-control form-control"
                                        style="width: 100%">
                                        <option value="Bukan">Bukan</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                    <label for="komorbid">Komorbid</label>
                                </div>
                            </div>
                            <div class="col-12 form-group mb-2 mt-1 row" id="tanggal-skd">
                                <div class="col-12">
                                    <span>Tanggal Surat Dokter</span>
                                </div>
                                <div class="col-6">
                                    <div class="form-label-group">
                                        <input type="date" id="tanggal-skd-mulai" class="form-control"
                                            placeholder="Tanggal Mulai">
                                        <label for="tanggal-skd-mulai">Mulai</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-label-group">
                                        <input type="date" id="tanggal-skd-selesai" class="form-control"
                                            placeholder="Tanggal Selesai">
                                        <label for="tanggal-skd-selesai">Sampai</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 form-group mb-2" id="asal-faskes">
                                <div class="d-flex justify-content-between">
                                    <div class="form-label-group-select mb-4 w-100">
                                        <select name="faskes" id="faskes" class="form-control form-control"
                                            style="width: 100%">
                                            <option value="">Pilih</option>
                                            @foreach ($faskes as $f)
                                                <option value="{{ $f->kode_faskes }}">{{ $f->nama_faskes }}</option>
                                            @endforeach
                                        </select>
                                        <label for="faskes-asal">Faskes Asal</label>
                                    </div>
                                    <div class="ml-1">
                                        <button onClick="tambahFaskes()" title="Tambah Faskes"
                                            class="btn btn-primary">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            style="background-color: #fff; border-top: 1px solid #eee; bottom: 0; left: 5px; right: 0; position: absolute">
                            <button id="save-button" type="button"
                                class="btn btn-sm btn-success mt-2 ml-2">Save</button>
                            <button id="dokter" class="btn btn-outline-secondary btn-sm mt-2 font-italic">Dokter :
                                <span></span></button>
                            <input type="hidden" id="dokter-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-rekam-medis" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formApproval">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">REKAM MEDIS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="timeline timeline-5">
                                    <div class="timeline-items">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default font-weight-bold"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal jenis pemeriksaan-->
    <div class="modal fade" id="jenis-pemeriksaan-modal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="jenis-pemeriksaan-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="container">
                    <div>
                        <form class="form py-5" id="form-jenis-pemeriksaan">
                            <div class="form-group mb-0">
                                <label>Jenis Pemeriksaan :</label>
                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <label class="option option-plain shadow-sm p-2">
                                            <span class="option-control">
                                                <span class="radio">
                                                    <input type="radio" name="jenis_pemeriksaan"
                                                        value="permintaan_obat" checked="checked" />
                                                    <span></span>
                                                </span>
                                            </span>
                                            <span class="option-label">
                                                <span class="option-head">
                                                    <span class="option-title">
                                                        PERMINTAAN OBAT
                                                    </span>
                                                </span>
                                                <span class="option-body">
                                                    ...
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="option option-plain shadow-sm p-2">
                                            <span class="option-control">
                                                <span class="radio">
                                                    <input type="radio" name="jenis_pemeriksaan" value="skd" />
                                                    <span></span>
                                                </span>
                                            </span>
                                            <span class="option-label">
                                                <span class="option-head">
                                                    <span class="option-title">
                                                        SKD
                                                    </span>
                                                </span>
                                                <span class="option-body">
                                                    ...
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // $(document).on("keypress", function (e) {
        //     if(e.key == "Enter")
        //     {
        //         $("#scanner").focus()
        //     }
        // })

        function tambahFaskes() {
            var nama_faskes = prompt("Nama Faskes ");
            if (nama_faskes == "" || nama_faskes == null) {
                return
            }

            $.ajax({
                url: "{{ url('/klinik/create-faskes') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    nama_faskes: nama_faskes
                },
                success: function(response) {
                    if (response.success == 0) {
                        toastr.error(response.message)
                    }

                    if (response.success == 1) {
                        $('#faskes').append('<option value="' + response.data.kode_faskes + '">' + response.data
                            .nama_faskes + '</option>')
                        $('#faskes').select2()
                        toastr.success(response.message)
                    }
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }



        function tambahDiagnosa() {
            var diagnosa = prompt("Nama Diagnosa ");
            if (diagnosa == "" || diagnosa == null) {
                return
            }

            $.ajax({
                url: "{{ url('/klinik/create-diagnosa') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    diagnosa: diagnosa
                },
                success: function(response) {
                    if (response.success == 0) {
                        toastr.error(response.message)
                    }

                    if (response.success == 1) {
                        $('#diagnosa').append('<option value="' + response.data.id + '">' + response.data
                            .nama_diagnosa + '</option>')
                        $('#diagnosa').select2()
                        toastr.success(response.message)
                    }
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }

        $('input[name=jenis_pemeriksaan]').on("click", function(e) {
            e.preventDefault()
            var jenisPemeriksaan = $('input[name=jenis_pemeriksaan]:checked').val()

            if (jenisPemeriksaan == 'skd') {
                $('#tanggal-skd').show()
                $('#obat-container').hide()
                $('#validasi-container').show()
                $('#asal-faskes').show()
            } else {
                $('#tanggal-skd').hide()
                $('#obat-container').show()
                $('#validasi-container').hide()
                $('#asal-faskes').hide()
            }

            localStorage.setItem('klinik_jenis_pemeriksaan', jenisPemeriksaan)
            $("#jenis-pemeriksaan-info").text(jenisPemeriksaan)
            $("#jenis-pemeriksaan-modal").modal('hide')
        })

        function doValidasi(id) {
            if (!confirm('Yakin validasi data ini?')) {
                return false
            }

            $.ajax({
                url: "{{ url('/klinik/validate') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                },
                success: function(response) {
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                    toastr.success("Validasi Berhasil")
                    $("#keluhan").val("")
                    $("#diagnosa").val("")
                    $("#diagnosa").select2()
                    $("#faskes").val("")
                    $("#faskes").select2()
                    $("#suhu").val("")
                    $("#tindakan").val("Kembali Bekerja")
                    $("#keterangan").val("")
                    $("#tensi").val("")
                    $("#tanggal-skd-mulai").val("")
                    $("#tanggal-skd-selesai").val("")
                    // $("#bukti_skd").val("")
                    clearCart()

                    $('#foto').attr('src', "{{ asset('assets/media/icons/id-card.jpg') }}");
                    $("#nik").val("")
                    $("#view-nik").text("")
                    $("#view-nama").text("")
                    $("#view-bagian").text("")

                    $("#transaction").fadeOut("fast", function() {
                        $("#transaction").addClass("hide")
                        $("#transaction").removeClass("_zoom")
                    })

                    $("#banner").fadeIn("fast", function() {
                        $("#banner").removeClass("hide")
                    })

                    $("#user-info").addClass("hide")
                    $("#scanner-container").removeClass("hide")

                    $("#scanner").focus()
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }

        function addNeedValidasi(data) {
            var table = $('#table-pemeriksaan tbody')
            if (data.jenis_pemeriksaan == 'skd' && data.tanggal_validasi == null && data.tindakan ==
                'Dipulangkan dan berobat') {
                table.append(
                    '<tr>\
                        <td>' +
                    data
                    .tanggal_pemeriksaan +
                    '</td>\
                        <td>' +
                    data
                    .waktu_pemeriksaan +
                    '</td>\
                        <td>' +
                    data
                    .keluhan +
                    '</td>\
                        <td>' +
                    data
                    .data_diagnosa
                    .nama_diagnosa +
                    '</td>\
                        <td>' +
                    data
                    .dokter +
                    '</td>\
                        <td><button onClick="doValidasi(\'' +
                    data
                    .id +
                    '\')" class="btn btn-warning text-dark btn-sm">Validasi</button></td>\
                    </tr>'
                )
            }
        }

        function getDataPemeriksaan() {
            var nik = $("#nik").val()
            $.ajax({
                url: "{{ url('/klinik/get-rekam-medis') }}/" + nik,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    $(".timeline-items").html("")
                    var table = $('#table-pemeriksaan tbody')
                    table.html('')
                    // console.log(response)
                    if (response.data.length == 0) {
                        $(".timeline-items").html(
                            "<div class='alert alert-warning'>Belum ada riwayat pemeriksaan</div>");
                        return
                    }
                    response.data.forEach(function(data) {
                        // Jika skd dan tanggal validasi nya masih kosong
                        addNeedValidasi(data)
                        addRekamMedis(data)
                    });
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }

        function getRekamMedis() {
            $("#modal-rekam-medis").modal("show")
        }

        function addRekamMedis(data) {
            var list_obat = "";
            data.obat.forEach(function(obat, index) {
                list_obat +=
                    "\
                                                                                            <tr>\
                                                                                                <td>" +
                    (
                        parseInt(
                            index) +
                        1) +
                    "</td>\
                                                                                                <td>" +
                    obat
                    .nama_obat +
                    "</td>\
                                                                                                <td class='text-center'>" +
                    obat
                    .pivot
                    .quantity + "</td>\
                                                                                            </tr>\
                                                                                            ";
            });

            $(".timeline-items").append(
                "\
                                                                                        <div class='timeline-item'>\
                                                                                            <div class='timeline-desc timeline-desc-light-success'>\
                                                                                                <i class='font-weight-bolder text-success' style='background-color: #eee; padding-left: 2px; padding-right: 5px; border-radius: 5px;'>" +
                formatTanggalIndonesia2(data.tanggal_pemeriksaan) + " " + data.waktu_pemeriksaan +
                "</i>\
                                                                                            <div class='font-weight-normal text-dark-50 mt-2'>\
                                                                                                <ul style='margin: 0; padding: 0; padding-left: 80px'>\
                                                                                                    <li>Keluhan : " +
                data
                .keluhan +
                "</li>\
                                                                                            <li>Diagnosa : " +
                data
                .data_diagnosa
                .nama_diagnosa +
                "</li>\
                                                                                            <li>Suhu : " +
                data
                .suhu +
                "</li>\
                                                                                            <li>Tensi : " +
                data
                .tensi +
                "</li>\
                                                                                            <li>Dokter : " +
                data
                .dokter +
                "</li>\
                                                                                            </ul>\
                                                                                            <div style='margin-top: 10px; width: 100%; margin-left: 70px; padding: 5px; border: 1px solid #eee; border-radius: 10px '>\
                                                                                                <h6>Obat : </h6>\
                                                                                                <table class='table table-bordered table-hover'>\
                                                                                                    <thead>\
                                                                                                        <tr>\
                                                                                                            <th class='text-center' style='width: 2px'>No</th>\
                                                                                                            <th>Nama Obat</th>\
                                                                                                        <th class='text-center'>Qty</th>\
                                                                                                        </tr>\
                                                                                                    </thead>\
                                                                                                    <tbody>\
                                                                                                    " +
                list_obat + "\
                                                                                            </tbody>\
                                                                                            </table>\
                                                                                            </div>\
                                                                                            </div>\
                                                                                            </div>\
                                                                                            </div>\
                                                                                            ")
        }

        function previewImage() {
            const fileskd = document.querySelector('#fileskd');
            const imgPreview = document.querySelector('#.img-priview');
            imgPreview.style.display = 'block';

            const oFReader = new FileReader();

            oFReader.readAsDataURL(fileskd.files[0]);
            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }

        }
        getDokter()

        function getDokter() {
            $("#dokter span").text(localStorage.getItem('nama_dokter'))
            $("#dokter-input").val(localStorage.getItem('nama_dokter'))
        }

        $("#dokter").on("click", function() {
            var nama_dokter = prompt("Nama Dokter", localStorage.getItem('nama_dokter'));
            if (nama_dokter == "" || nama_dokter == null) {
                return
            }

            localStorage.setItem('nama_dokter', nama_dokter)
            getDokter()
        })

        $("#diagnosa").select2()
        $("#faskes").select2()

        $("#save-button").on("click", function() {
            $("#save-button").attr("disabled", true)
            $("#save-button").text("Saving..")
            if ($("#keluhan").val() == '') {
                $("#keluhan").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Mohon isi keluhan")
                return
            }

            if ($("#diagnosa").val() == '') {
                $("#diagnosa").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Mohon isi diagnosa")
                return
            }

            if ($("#suhu").val() == '') {
                $("#suhu").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Mohon isi suhu")
                return
            }

            if ($("#tindakan").val() == '') {
                $("#tindakan").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Mohon isi tindakan")
                return
            }

            if ($("#keterangan").val() == '') {
                $("#keterangan").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Mohon isi keterangan")
                return
            }

            if (parseInt($("#suhu").val()) > 60) {
                $("#suhu").focus()
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                toastr.error("Nilai suhu tidak valid")
                return
            }

            if ($("#dokter-input").val() == '') {
                alert('Mohon isi nama dokter')
                $("#save-button").removeAttr("disabled")
                $("#save-button").text("Save")
                return
            }

            var jenisPemeriksaan = localStorage.getItem('klinik_jenis_pemeriksaan')

            if (!jenisPemeriksaan) {
                alert('Tidak ada jenis pemeriksaan, mulai transaksi dari awal')
            }

            var cart_obat = getCartObat()

            if (jenisPemeriksaan != 'skd' && cart_obat.length == 0) {
                if (!confirm("Yakin tidak diberi obat?")) {
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                    $("#search-obat").focus()
                    return
                }
            }

            if (jenisPemeriksaan == 'skd') {
                if ($("#tanggal-skd-mulai").val() == '' || $("#tanggal-skd-selesai").val() == '') {
                    alert('Mohon isi tanggal skd')
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                    return
                }

                if ($("#faskes").val() == '') {
                    alert('Mohon pilih salah satu faskes')
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                    return
                }
            }

            // Oke ini mantap
            //var files = $('#fileskd')[0].files[0];
            var form = new FormData();
            //form.append('fileskd', files);
            form.append('keluhan', $("#keluhan").val());
            form.append('diagnosa', $("#diagnosa").val());
            form.append('suhu', $("#suhu").val());
            form.append('tensi', $("#tensi").val());
            form.append('nik', $("#nik").val());
            form.append('obat', JSON.stringify(cart_obat));
            form.append('dokter', $("#dokter-input").val());
            form.append('tindakan', $("#tindakan").val());
            form.append('keterangan', $("#keterangan").val());
            form.append('komorbid', $("#komorbid").val());
            form.append('jenis_pemeriksaan', jenisPemeriksaan);
            form.append('tanggal_skd_mulai', $("#tanggal-skd-mulai").val());
            form.append('tanggal_skd_selesai', $("#tanggal-skd-selesai").val());
            form.append('faskes', $("#faskes").val());
            form.append('nama', $("#view-nama").text());
            form.append('bagian', $("#view-bagian").text());
            // console.log(form);


            $.ajax({
                url: "{{ route('klinik.save') }}",
                type: "POST",
                dataType: "JSON",
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function(response) {
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                    toastr.success("Save data succeed")
                    $("#keluhan").val("")
                    $("#diagnosa").val("")
                    $("#diagnosa").select2()
                    $("#faskes").val("")
                    $("#faskes").select2()
                    $("#suhu").val("")
                    $("#tindakan").val("Kembali Bekerja")
                    $("#keterangan").val("")
                    $("#tensi").val("")
                    $("#tanggal-skd-mulai").val("")
                    $("#tanggal-skd-selesai").val("")
                    clearCart()

                    $('#foto').attr('src', "{{ asset('assets/media/icons/id-card.jpg') }}");
                    $("#nik").val("")
                    $("#view-nik").text("")
                    $("#view-nama").text("")
                    $("#view-bagian").text("")

                    $("#transaction").fadeOut("fast", function() {
                        $("#transaction").addClass("hide")
                        $("#transaction").removeClass("_zoom")
                    })

                    $("#banner").fadeIn("fast", function() {
                        $("#banner").removeClass("hide")
                    })

                    $("#user-info").addClass("hide")
                    $("#scanner-container").removeClass("hide")

                    $("#scanner").focus()

                    // window.location.reload()
                },
                error: function(e) {
                    toastr.error("Error. Coba lagi")
                    $("#save-button").removeAttr("disabled")
                    $("#save-button").text("Save")
                }
            })


        })

        function sudahDibukaChange(obat) {
            console.log($('#check-dibuka-' + obat.id).is(':checked'))
            var cart_obat = getCartObat()
            var cart_index = findCartIndex(obat, cart_obat)

            cart_obat[cart_index].sudah_dibuka = 0
            if ($('#check-dibuka-' + obat.id).is(':checked')) {
                cart_obat[cart_index].sudah_dibuka = 1
            }

            setCartObat(cart_obat)
            updateCart()
        }

        function deleteCartItem(obat) {
            var cart_obat = getCartObat()
            var cart_index = findCartIndex(obat, cart_obat)
            cart_obat.splice(cart_index, 1)

            setCartObat(cart_obat)
            updateCart()
        }

        function updateQty(obat, qty) {
            var cart_obat = getCartObat()
            var cart_index = findCartIndex(obat, cart_obat)

            var current_qty = cart_obat[cart_index].qty + qty
            cart_obat[cart_index].qty += qty

            if (current_qty <= 0) {
                cart_obat.splice(cart_index, 1)
            }

            setCartObat(cart_obat)
            updateCart()

        }

        function updateCart() {
            var cart_obat = getCartObat();
            $(".outer-cart-obat").html("");
            $.each(cart_obat, function(key, value) {
                var isSudahDibuka = value.sudah_dibuka === '1' ? 'checked' : '';

                $(".outer-cart-obat").append(
                    '<div class="item-cart-obat mb-1">' +
                    '<div class="row">' +
                    '<div class="col-3">' +
                    '<img src=\'{{ asset('/assets/media/icons/obat-no-image.jpg') }}\' style="height: 50px" alt="Obat">' +
                    '</div>' +
                    '<div class="col-7">' +
                    '<div class="row">' +
                    '<div class="col-12">' +
                    '<span style="font-size: 9px">' + value.nama_obat + '</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row mt-1">' +
                    '<div class="col-9">' +
                    '<div class="row">' +
                    '<div class="col-4">' +
                    '<button onClick="updateQty(' + JSON.stringify(value).replace(/\"/g, "'") +
                    ', -1)" class="btn btn-default btn-xs">-</button>' +
                    '</div>' +
                    '<div class="col-4 px-0">' +
                    '<input readonly value="' + value.qty + '" class="form-control form-control-xs">' +
                    '</div>' +
                    '<div class="col-4">' +
                    '<button onClick="updateQty(' + JSON.stringify(value).replace(/\"/g, "'") +
                    ', 1)" class="btn btn-default btn-xs">+</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-2 text-right pr-8 pt-1">' +
                    '<a title="Remove" class="delete-icon" href="javascript:" onClick="deleteCartItem(' + JSON
                    .stringify(value).replace(/\"/g, "'") + ')">' +
                    '<i class="fa fa-trash mt-1"></i></a>' +
                    '</div>' +
                    '</div>' +
                    '<div class="ml-2">' +
                    '<div class="checkbox-inline">' +
                    '<label class="checkbox checkbox-success">' +
                    // '<input id="check-dibuka-' + value.id + '" ' + isSudahDibuka +
                    // ' onChange="sudahDibukaChange(' + JSON.stringify(value).replace(/\"/g, "'") +
                    // ')" type="checkbox" name=""/>' +
                    '<input type="checkbox" id="check-dibuka-' + value.id +
                    '" class="form-control" name="check-' +
                    value.id + '">' +
                    '<span></span>' +
                    'Sudah Terbuka' +
                    '</label>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            });
        }

        clearCart()

        function clearCart() {
            setCartObat([])
            updateCart()
        }

        function addToCart(obat) {
            // Get all cart content from localstorage+
            var cart_obat = getCartObat()

            if (cart_obat == null) {
                cart_obat = []
            }

            var cart_index = findCartIndex(obat, cart_obat)

            if (cart_index == -1) {
                obat.qty = 1
                obat.sudah_dibuka = 0;
                cart_obat.push(obat)
            } else {
                cart_obat[cart_index].qty += 1
            }

            setCartObat(cart_obat)
            updateCart()
        }

        function getCartObat() {
            return JSON.parse(localStorage.getItem('cart_obat'));
        }

        function setCartObat(cart_obat) {
            localStorage.setItem('cart_obat', JSON.stringify(cart_obat))
        }

        function findCartIndex(obat, cart_obat) {
            return cart_obat.findIndex(function(p) {
                return p.id == obat.id
            })
        }

        $("#search-obat").on("keyup", function() {
            var keyword = $(this).val().toLowerCase()

            $("#container-obat .item-obat-container").each(function() {
                if (keyword == "") {
                    $(this).fadeIn("medium", function() {
                        $(this).show();
                    })
                } else {
                    if ($(this).filter('[data-search *= ' + keyword + ']').length > 0) {
                        $(this).fadeIn("medium", function() {
                            $(this).show();
                        })
                    } else {
                        $(this).fadeOut("medium", function() {
                            $(this).hide();
                        })
                    }
                }
            })
        })

        $("#scanner").on("keypress", function(e) {
            if (e.key == "Enter") {

                if ($("#scanner").val() == "") {
                    return false;
                }

                var rfid = $("#scanner").val();
                $("#scanner").val("");
                $.ajax({
                    url: "{{ url('/klinik/scan') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        rfid: rfid
                    },

                    // kondisi jika data belum terdaftar di secure access
                    success: function(response) {
                        if (response.success == '0') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data belum terdaftar',
                                text: 'Silahkan hubungi Team GA',
                            });
                            return;
                        }

                        // // kondisi jika data sudah pernah mengajukan berobat di tanggal yang sama
                        // if (response.error === "3") {
                        //     Swal.fire({
                        //         icon: 'warning',
                        //         title: 'Tidak diizinkan',
                        //         text: 'Karyawan dengan nik ini sudah pernah berobat hari ini.',
                        //     });
                        //     return;
                        // }

                        $('#foto').attr('src', 'data:image/jpg;base64,' + response.data.FOTOBLOB);
                        $("#nik").val(response.data.NIK);
                        $("#view-nik").text(response.data.NIK);
                        $("#view-nama").text(response.data.EMPNM);
                        $("#view-bagian").text(response.data.DEPTID);
                        $("#banner").fadeOut("fast", function() {
                            $("#banner").addClass("hide");
                        });

                        $("#transaction").fadeIn("fast", function() {
                            $("#transaction").removeClass("hide");
                            $("#transaction").addClass("_zoom");
                        });

                        $("#user-info").removeClass("hide");
                        $("#scanner-container").addClass("hide");

                        $("#jenis-pemeriksaan-modal").modal('show');

                        getDataPemeriksaan();
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            }
        });
    </script>
@endpush
