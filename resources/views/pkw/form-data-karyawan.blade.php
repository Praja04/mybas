<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Data Karyawan</title>
{{--    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap4/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap4/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frest/colors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frest/components.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frest/vertical-menu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/frest/vendors.min.css') }}">
    <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/logo-pas.jpg" />
    <style type="text/css">
        table.table-no-padding td, table.table-no-padding th {
            padding: 5px !important;
        }
        table.table-no-padding .form-control {
            /*padding: 2px !important;*/
        }
        table.table-no-padding thead tr {
            white-space: nowrap;
        }
        table.table-no-padding thead tr th {
            text-align: left !important;
        }
        .wajib-diisi {
            position: absolute;
            margin-top: -8px;
        }
        .hide {
            display: none;
        }
    </style>
</head>
<body>
<nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top" style="background-color: rgb(255, 255, 255); box-shadow: rgba(25, 42, 70, 0.13) -8px 12px 18px 0px;left:0">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <div class="navbar-header">
                        <ul class="nav navbar-nav flex-row">
                            <li class="nav-item mr-auto">
                                <a class="navbar-brand" href="javascript:">
                                    <div class="brand-logo">
                                        <img width="100px" class="logo" src="{{ asset('assets/media/logos/logo-pas-with-text.png') }}">
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav flex-column text-center">
                        <li class="nav-item">
                            <h3 class="m-0 font-medium-4">PT. PRAKARSA ALAM SEGAR</h3>
                        </li>
                        <li class="nav-item">
                            <h4 class="m-0 font-small-3">Human Resource Department</h4>
                        </li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    {{--                        <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon bx bx-search"></i></a>--}}
                    {{--                            <div class="search-input">--}}
                    {{--                                <div class="search-input-icon" wfd-invisible="true"><i class="bx bx-search primary"></i></div>--}}
                    {{--                                <input class="input" type="text" placeholder="Explore Frest..." tabindex="-1" data-search="template-search">--}}
                    {{--                                <div class="search-input-close" wfd-invisible="true"><i class="bx bx-x"></i></div>--}}
                    {{--                                <ul class="search-list" wfd-invisible="true"></ul>--}}
                    {{--                            </div>--}}
                    {{--                        </li>--}}
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="javascript:" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name">-</span>
                                <span class="user-status text-muted">-</span>
                            </div>
                            <span>
                                <img class="round" src="{{ asset('/assets/media/users/default.jpg') }}" alt="avatar" height="40" width="40">
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid" style="margin-top: 100px">
    <section id="floating-label-layouts">
        <div class="row match-height">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">FORM DATA KARTU POKOK KARYAWAN</h4>
                        <hr>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" id="formDataKaryawan">
                                <div class="form-body">
                                    <div class="alert bg-rgba-secondary alert-dismissible mb-2" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-info-circle"></i>
                                            <span> Setiap kolom dengan tanda (<span class="text-danger">*</span>) wajib di isi</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" style="margin-bottom: 5px">
                                            <h4><u>Info Pribadi</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input maxlength="16" required type="number" id="nomor_ktp" class="form-control" name="nomor_ktp" placeholder="Nomor KTP">
                                                <div class="form-control-position">
                                                    <i class="bx bx-id-card"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nomor_ktp">Nomor KTP</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div  class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="nama" class="form-control" name="nama" placeholder="Nama Lengkap">
                                                <div class="form-control-position">
                                                    <i class="bx bx-id-card"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nama">Nama Lengkap</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input maxlength="16" required type="number" id="nomor_kk" class="form-control" name="nomor_kk" placeholder="Nomor KK">
                                                <div class="form-control-position">
                                                    <i class="bx bx-id-card"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nomor_kk">Nomor KK</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input maxlength="16" required type="number" id="nomor_npwp" class="form-control" name="nomor_npwp" placeholder="Nomor NPWP">
                                                <small class="text-muted">Tanpa titik dan strip.</small>
                                                <div class="form-control-position">
                                                    <i class="bx bx-id-card"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nomor_npwp">Nomor NPWP</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="d-block">Agama <span class="text-danger wajib-diisi">*</span></label>
                                                <div class="custom-control-inline">
                                                    <div class="radio mr-1">
                                                        <input checked required value="islam" type="radio" name="agama" id="islam">
                                                        <label for="islam">Islam</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="budha" type="radio" name="agama" id="budha">
                                                        <label for="budha">Budha</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="hindu" type="radio" name="agama" id="hindu">
                                                        <label for="hindu">Hindu</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="khatolik" type="radio" name="agama" id="khatolik">
                                                        <label for="khatolik">Khatolik</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="kristen" type="radio" name="agama" id="kristen">
                                                        <label for="kristen">Kristen</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="d-block">Jenis Kelamin <span class="text-danger wajib-diisi">*</span></label>
                                                <div class="custom-control-inline">
                                                    <div class="radio mr-1">
                                                        <input checked required value="L" type="radio" name="jenis_kelamin" id="pria">
                                                        <label for="pria">Laki-laki</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="P" type="radio" name="jenis_kelamin" id="wanita">
                                                        <label for="wanita">Perempuan</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="tempat-lahir" class="form-control" name="tempat_lahir" placeholder="Tempat Lahir">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="tempat-lahir">Tempat Lahir</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required value="1998-03-20" type="date" id="tanggal-lahir" class="form-control" name="tanggal_lahir" placeholder="Tanggal Lahir">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="tanggal-lahir">Tanggal Lahir</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label class="d-block">Alamat Sesuai KTP<span class="text-danger wajib-diisi">*</span></label>
                                            </div>
                                            <div class="form-group">
                                                <select required class="form-control select2" name="alamat_ktp_provinsi" id="provinsi">
                                                    <option value="">Pilih Provinsi</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{ $province->id }}-{{ $province->name }}">{{ $province->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select required disabled class="form-control select2" name="alamat_ktp_kota" id="kota">
                                                    <option value="">Pilih Kabupaten / Kota</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select required disabled class="form-control select2" name="alamat_ktp_kecamatan" id="kecamatan">
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select required disabled class="form-control select2" name="alamat_ktp_desa" id="desa">
                                                    <option value="">Pilih Desa</option>
                                                </select>
                                            </div>
                                            <div class="form-label-group mt-2">
                                                <textarea disabled required name="alamat_ktp_alamat_rumah" class="form-control" id="alamat-rumah" rows="3" placeholder="Alamat" spellcheck="false"></textarea>
                                                <label for="alamat-rumah">Alamat</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <label class="d-block">Alamat Rumah Sekarang <span class="text-danger wajib-diisi">*</span></label>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <div class="checkbox">
                                                        <input value="alamat_sekarang_sesuai_ktp" type="checkbox" class="checkbox__input" id="alamat-sesuai-ktp" name="alamat_sekarang_sesuai_ktp">
                                                        <label for="alamat-sesuai-ktp">Alamat sekarang sesuai alamat KTP</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="alamat-luar-kota-sesuai-ktp" class="hide">
                                                <ul>
                                                    <li>Provinsi : <span id="alamat-sesuai-ktp-provinsi"></span></li>
                                                    <li>Kabupaten / Kota : <span id="alamat-sesuai-ktp-kota"></span></li>
                                                    <li>Kecamatan : <span id="alamat-sesuai-ktp-kecamatan"></span></li>
                                                    <li>Desa : <span id="alamat-sesuai-ktp-desa"></span></li>
                                                    <li>Alamat : <span id="alamat-sesuai-ktp-alamat"></span></li>
                                                </ul>
                                            </div>
                                            <div id="alamat-luar-kota-container">
                                                <div class="form-group">
                                                    <select required class="alamat-rumah-sekarang form-control select2" name="alamat_sekarang_provinsi" id="alamat-sekarang-provinsi">
                                                        <option value="">Pilih Provinsi</option>
                                                        @foreach($provinces as $province)
                                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <select required disabled class="alamat-rumah-sekarang form-control select2" name="alamat_sekarang_kota" id="alamat-sekarang-kota">
                                                        <option value="">Pilih Kabupaten / Kota</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <select required disabled class="form-control select2" name="alamat_sekarang_kecamatan" id="alamat-sekarang-kecamatan">
                                                        <option value="">Pilih Kecamatan</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <select required disabled class="form-control select2" name="alamat_sekarang_desa" id="alamat-sekarang-desa">
                                                        <option value="">Pilih Desa</option>
                                                    </select>
                                                </div>
                                                <div class="form-label-group mt-2">
                                                <textarea required disabled name="alamat_sekarang" class="form-control" id="alamat-sekarang-alamat-rumah" rows="3" placeholder="Alamat Rumah" spellcheck="false"></textarea>
                                                <label for="alamat-luar-kota-rumah">Alamat</label>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input value="1998-03-20" type="date" required id="tanggal-masuk" class="form-control" name="tanggal_masuk" placeholder="Tanggal Masuk">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="tanggal-masuk">Tanggal Masuk</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12"></div>
                                        <div class="col-12">
                                            <br>
                                            <h4><u>Info Perdata</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="d-block">Status Perdata <span class="text-danger wajib-diisi">*</span></label>
                                                <div class="custom-control-inline" style="flex-wrap: wrap;">
                                                    <div class="radio mr-1">
                                                        <input checked required value="belum_menikah" type="radio" name="status_perdata" id="belum-menikah">
                                                        <label for="belum-menikah" style="white-space: nowrap">Belum Menikah</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="menikah" type="radio" name="status_perdata" id="menikah">
                                                        <label for="menikah">Menikah</label>
                                                    </div>
                                                    <div class="radio mr-1">
                                                        <input value="janda" type="radio" name="status_perdata" id="janda">
                                                        <label for="janda">Janda</label>
                                                    </div>
                                                    <div class="radio">
                                                        <input value="duda" type="radio" name="status_perdata" id="duda">
                                                        <label for="duda">Duda</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="text" id="nama-pasangan" class="menikah form-control" name="nama_pasangan" placeholder="Nama Pasangan">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i>
                                                </div>
                                                <label for="nama-pasangan">Nama Pasangan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="text" id="tempat-pernikahan" class="menikah form-control" name="tempat_pernikahan" placeholder="Tpt Pernikahan">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="tempat-pernikahan">Tempat Pernikahan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="date" id="tanggal-pernikahan" class="menikah form-control" name="tanggal_pernikahan" placeholder="Tgl Pernikahan">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="tanggal-pernikahan">Tanggal Pernikahan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="text" id="tempat-lahir-pasangan" class="menikah form-control" name="tempat_lahir_pasangan" placeholder="Tpt Lahir Pasangan">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="tempat-lahir-pasangan">Tempat Lahir Pasangan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="date" id="tanggal-lahir-pasangan" class="menikah form-control" name="tanggal_lahir_pasangan" placeholder="Tgl Lahir Pasangan">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="tanggal-lahir-pasangan">Tanggal Lahir Pasangan</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="text" id="pekerjaan-pasangan" class="menikah form-control" name="pekerjaan_pasangan" placeholder="Pekerjaan Pasangan">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i>
                                                </div>
                                                <label for="pekerjaan-pasangan">Pekerjaan Pasangan</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input disabled type="text" id="tempat-pasangan-bekerja" class="menikah form-control" name="tempat_pasangan_bekerja" placeholder="Tempat Pasangan Bekerja">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map-pin"></i>
                                                </div>
                                                <label for="tempat-pasangan-bekerja">Tempat Pasangan Bekerja</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <h4><u>Pengalaman Kerja</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table style="min-width: 1200px" class="table hover table-no-padding">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 300px">NAMA PERUSAHAAN</th>
                                                        <th style="width: 400px">JABATAN</th>
                                                        <th style="width: 400px">LAMA BEKERJA</th>
                                                        <th style="width: 300px">KOTA</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @for($i=1;$i<=4;$i++)
                                                        <tr>
                                                            <td><textarea name="pengalaman_kerja_perusahaan_{{ $i }}" rows='1' class="form-control"></textarea></td>
                                                            <td><input name="pengalaman_kerja_jabatan_{{ $i }}" type="text" class="form-control"></td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-sm-6 col-xs-12">
                                                                        <input name="pengalaman_kerja_tanggal_mulai_{{ $i }}" type="date" class="form-control" placeholder="Tanggal Mulai">
                                                                    </div>
                                                                    <div class="col-sm-6 col-xs-12">
                                                                        <input name="pengalaman_kerja_tanggal_selesai_{{ $i }}" type="date" class="form-control" placeholder="Tanggal Selesai">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><input name="pengalaman_kerja_kota_{{ $i }}" type="text" class="form-control"></td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <br>
                                            <h4><u>Anak-anak</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table style="min-width: 1200px" class="table hover table-no-padding">
                                                    <thead>
                                                    <tr>
                                                        <th>NAMA</th>
                                                        <th>JENIS KELAMIN</th>
                                                        <th>TEMPAT LAHIR</th>
                                                        <th>TANGGAL LAHIR</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @for($i=1;$i<=4;$i++)
                                                        <tr>
                                                            <td><input name="anak_anak_nama_{{ $i }}" type="text" class="form-control"></td>
                                                            <td>
                                                                <div class="custom-control-inline">
                                                                    <div class="radio mr-1">
                                                                        <input value="L" type="radio" name="anak_anak_jenis_kelamin_{{ $i }}" id="anak-pria-{{ $i }}">
                                                                        <label for="anak-pria-{{ $i }}">Laki-laki</label>
                                                                    </div>
                                                                    <div class="radio mr-1">
                                                                        <input value="P" type="radio" name="anak_anak_jenis_kelamin_{{ $i }}" id="anak-wanita-{{ $i }}">
                                                                        <label for="anak-wanita-{{ $i }}">Perempuan</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><input name="anak_anak_tempat_lahir_{{ $i }}" type="text" class="form-control"></td>
                                                            <td><input name="anak_anak_tanggal_lahir_{{ $i }}" type="date" class="form-control"></td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <br>
                                            <h4><u>Orang Tua</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="nama-ayah" class="form-control" name="orang_tua_nama_ayah" placeholder="Nama Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nama-ayah">Nama Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="tempat-lahir-ayah" class="form-control" name="orang_tua_tempat_lahir_ayah" placeholder="Tpt Lahir Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="tempat-lahir-ayah">Tempat Lahir Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="date" id="tanggal-lahir-ayah" class="form-control" name="orang_tua_tanggal_lahir_ayah" placeholder="Tgl Lahir Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="tanggal-lahir-ayah">Tanggal Lahir Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="nama-ibu" class="form-control" name="orang_tua_nama_ibu" placeholder="Nama Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="nama-ibu">Nama Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="tempat-lahir-ibu" class="form-control" name="orang_tua_tempat_lahir_ibu" placeholder="Tpt Lahir Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="tempat-lahir-ibu">Tempat Lahir Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="date" id="tanggal-lahir-ibu" class="form-control" name="orang_tua_tanggal_lahir_ibu" placeholder="Tgl Lahir Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="tanggal-lahir-ibu">Tanggal Lahir Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <h4><u>Saudara Kandung</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table style="min-width: 1200px" class="table hover table-no-padding">
                                                    <thead>
                                                    <tr>
                                                        <th>NAMA</th>
                                                        <th>JENIS KELAMIN</th>
                                                        <th>TEMPAT LAHIR</th>
                                                        <th>TANGGAL LAHIR</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @for($i=1;$i<=4;$i++)
                                                        <tr>
                                                            <td><input name="saudara_kandung_nama_{{ $i }}" type="text" class="form-control"></td>
                                                            <td>
                                                                <div class="custom-control-inline">
                                                                    <div class="radio mr-1">
                                                                        <input value="L" type="radio" name="saudara_kandung_jenis_kelamin_{{ $i }}" id="saudara-kandung-pria-{{ $i }}">
                                                                        <label for="saudara-kandung-pria-{{ $i }}">Laki-laki</label>
                                                                    </div>
                                                                    <div class="radio mr-1">
                                                                        <input value="P" type="radio" name="saudara_kandung_jenis_kelamin_{{ $i }}" id="saudara-kandung-wanita-{{ $i }}">
                                                                        <label for="saudara-kandung-wanita-{{ $i }}">Perempuan</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><input name="saudara_kandung_tempat_lahir_{{ $i }}" type="text" class="form-control"></td>
                                                            <td><input name="saudara_kandung_tanggal_lahir_{{ $i }}" type="date" class="form-control"></td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12" style="margin-bottom: 5px;margin-top: 5px">
                                            <h4><u>Mertua</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="mertua-nama-ayah" class="form-control" name="mertua_nama_ayah" placeholder="Nama Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i>
                                                </div>
                                                <label for="mertua-nama-ayah">Nama Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="mertua-tempat-lahir-ayah" class="form-control" name="mertua_tempat_lahir_ayah" placeholder="Tpt Lahir Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="mertua-tempat-lahir-ayah">Tempat Lahir Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="date" id="mertua-tanggal-lahir-ayah" class="form-control" name="mertua_tanggal_lahir_ayah" placeholder="Tgl Lahir Ayah">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="mertua-tanggal-lahir-ayah">Tanggal Lahir Ayah</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="mertua-nama-ibu" class="form-control" name="mertua_nama_ibu" placeholder="Nama Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-user-detail"></i>
                                                </div>
                                                <label for="mertua-nama-ibu">Nama Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="mertua-tempat-lahir-ibu" class="form-control" name="mertua_tempat_lahir_ibu" placeholder="Tpt Lahir Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bx-map"></i>
                                                </div>
                                                <label for="mertua-tempat-lahir-ibu">Tempat Lahir Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="date" id="mertua-tanggal-lahir-ibu" class="form-control" name="mertua_tanggal_lahir_ibu" placeholder="Tgl Lahir Ibu">
                                                <div class="form-control-position">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                                <label for="mertua-tanggal-lahir-ibu">Tanggal Lahir Ibu</label>
                                            </div>
                                        </div>
                                        <div class="col-12" style="margin-bottom: 5px">
                                            <h4><u>Kontak Darurat</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="kontak-darurat-nama" class="form-control" name="kontak_darurat_nama" placeholder="Nama">
                                                <div class="form-control-position">
                                                    <i class="bx bx-user-minus"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="kontak-darurat-nama">Nama</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="kontak-darurat-hubungan" class="form-control" name="kontak_darurat_hubungan" placeholder="Hubungan">
                                                <div class="form-control-position">
                                                    <i class="bx bx-user-plus"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="kontak-darurat-hubungan">Hubungan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input required type="text" id="kontak-darurat-no-telepon" class="form-control" name="kontak_darurat_no_telepon" placeholder="No. Telpon">
                                                <div class="form-control-position">
                                                    <i class="bx bx-phone"></i><span class="text-danger wajib-diisi">*</span>
                                                </div>
                                                <label for="kontak-darurat-no-telepon">No. Telepon</label>
                                            </div>
                                        </div>
                                        <div class="col-12" style="margin-bottom: 5px">
                                            <h4><u>Lain-lain</u></h4>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="nomor-rekening-bank" class="form-control" name="nomor_rekening_bank" placeholder="Nomor Rekening Bank">
                                                <small class="form-text text-muted"><img width="50px" src="{{ asset('assets/media/logos/bank-mandiri.png') }}" alt="Icon bank mandiri"> Hanya boleh nomor rekening mandiri</small>
                                                <div class="form-control-position">
                                                    <i class="bx bx-money"></i>
                                                </div>
                                                <label for="nomor-rekening-bank">Nomor Rekening Bank</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="nomor-kartu-bpjs-ketenagakerjaan" class="form-control" name="nomor_kartu_bpjs_ketenagakerjaan" placeholder="No. Kartu BPJS Ketenagakerjaan">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-id-card"></i>
                                                </div>
                                                <label for="nomor-kartu-bpjs-ketenagakerjaan">No. Kartu BPJS Ketenagakerjaan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text bg-white" for="keterangan-bpjs-ketenagakerjaan">KET.</label>
                                                </div>
                                                <select name="keterangan_kartu_bpjs_ketenagakerjaan" class="form-control" id="keterangan-bpjs-ketenagakerjaan">
                                                    <option value="Dapa">Dapat</option>
                                                    <option selected value="Belum Dapat">Belum Dapat</option>
                                                    <option value="Hilang">Hilang</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-9">
                                            <div class="form-label-group position-relative has-icon-left">
                                                <input type="text" id="nomor-kartu-bpjs-kesehatan" class="form-control" name="nomor_kartu_bpjs_kesehatan" placeholder="No. Kartu BPJS Kesehatan">
                                                <div class="form-control-position">
                                                    <i class="bx bxs-id-card"></i>
                                                </div>
                                                <label for="nomor-kartu-bpjs-kesehatan">No. Kartu BPJS Kesehatan</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text bg-white" for="keterangan-bpjs-kesehatan">KET.</label>
                                                </div>
                                                <select name="keterangan_kartu_bpjs_kesehatan" class="form-control" id="keterangan-bpjs-kesehatan">
                                                    <option value="Dapat">Dapat</option>
                                                    <option selected value="Belum Dapat">Belum Dapat</option>
                                                    <option value="Hilang">Hilang</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert border-secondary mb-2" role="alert">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="checkbox">
                                                            <input name="setuju_disclaimer" type="checkbox" class="checkbox__input" id="disclaimer">
                                                            <label for="disclaimer">Dengan ini saya menyatakan bahwa semua data yang saya isi di atas adalah benar ada nya data milik saya, dan tapat dipertanggung jawab kan.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                        <div class="col-12 d-flex justify-content-center">
                                            <button type="submit" id="submitButton" class="btn btn-primary mr-1 mb-1">Submit</button>
                                            {{--                                                <button type="reset" class="btn btn-light-secondary mr-1 mb-1">Reset</button>--}}
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="{{ asset('assets/js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $('.select2').select2();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('input[type=radio][name=status_perdata]').on('change', function() {
        if (this.value == 'menikah') {
            $('.menikah').removeAttr('disabled');
        }else{
            $('.menikah').attr('disabled', true);
        }
    });

    $('#alamat-sesuai-ktp').on('change', function () {
        if($(this).prop('checked')) {
            // Jika di ceklis
            $('#alamat-luar-kota-container').                                                                                addClass('hide');
            $('#alamat-luar-kota-sesuai-ktp').removeClass('hide');

            $('.alamat-rumah-sekarang').removeAttr('required');
        }else{
            $('#alamat-luar-kota-sesuai-ktp').addClass('hide');
            $('#alamat-luar-kota-container').removeClass('hide');
            $('.alamat-rumah-sekarang').attr('required', true);
        }
    })

    $('#provinsi').on('change', function () {
        $('#kota').attr('disabled', true);
        $('#kecamatan').val('');
        $('#kecamatan').attr('disabled', true);
        $('#desa').val('');
        $('#desa').attr('disabled', true);
        $('#alamat-rumah').attr('disabled', true);

        var id = $(this).val().split('-');

        // Untuk alamat sekarang sesuai ktp
        $('#alamat-sesuai-ktp-provinsi').text(id[1]);
        $.ajax({
            url: '{{ url('/indoregion/get-regencies-by-province') }}/'+id[0],
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                if(response.success == 1) {
                    $('#kota').removeAttr('disabled');
                    $('#kota').html('<option value="">Pilih Kabupaten / Kota</option>')
                    $.each(response.regencies, function (key, regency) {
                        $('#kota').append('<option value="' + regency.id + '-' + regency.name + '">' + regency.name + '</option>')
                    });
                }
            }
        })
    });

    $('#kota').on('change', function () {
        $('#kecamatan').attr('disabled', true);
        $('#desa').val('');
        $('#desa').attr('disabled', true);
        $('#alamat-rumah').attr('disabled', true);

        var id = $(this).val().split('-');

        // Untuk alamat sesuai ktp
        $('#alamat-sesuai-ktp-kota').text(id[1]);

        $.ajax({
            url: '{{ url('/indoregion/get-districts-by-regency') }}/'+id[0],
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                // console.log(response)
                if(response.success == 1) {
                    $('#kecamatan').removeAttr('disabled');
                    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>')
                    $.each(response.districts, function (key, district) {
                        $('#kecamatan').append('<option value="' + district.id + '-' + district.name + '">' + district.name + '</option>')
                    });
                }
            }
        })
    });

    $('#kecamatan').on('change', function () {
        $('#desa').attr('disabled', true);
        $('#alamat-rumah').attr('disabled', true);

        var id = $(this).val().split('-');

        // Untuk alamat sesuai ktp
        $('#alamat-sesuai-ktp-kecamatan').text(id[1]);

        $.ajax({
            url: '{{ url('/indoregion/get-villages-by-district') }}/'+id[0],
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                // console.log(response)
                if(response.success == 1) {
                    $('#desa').removeAttr('disabled');
                    $('#desa').html('<option value="">Pilih Desa</option>')
                    $.each(response.villages, function (key, village) {
                        $('#desa').append('<option value="' + village.id + '-' + village.name + '">' + village.name + '</option>')
                    });
                }
            }
        })
    });
    $('#desa').on('change', function () {
        var id = $(this).val().split('-');

        // Untuk alamat sesuai ktp
        $('#alamat-sesuai-ktp-desa').text(id[1]);

        if($(this).val() != '') {
            $('#alamat-rumah').removeAttr('disabled');
        }else{
            $('#alamat-rumah').attr('disabled', true);
        }
    });

    $('#alamat-rumah').keyup(function () {
       $('#alamat-sesuai-ktp-alamat').text($(this).val());
    });

    $('#alamat-sekarang-provinsi').on('change', function () {
        $('#alamat-sekarang-kota').attr('disabled', true);
        $('#alamat-sekarang-kecamatan').val('');
        $('#alamat-sekarang-kecamatan').attr('disabled', true);
        $('#alamat-sekarang-desa').val('');
        $('#alamat-sekarang-desa').attr('disabled', true);
        $('#alamat-sekarang-alamat-rumah').attr('disabled', true);
        // console.log('test')

        $.ajax({
            url: '{{ url('/indoregion/get-regencies-by-province') }}/'+$(this).val(),
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                if(response.success == 1) {
                    $('#alamat-sekarang-kota').removeAttr('disabled');
                    $('#alamat-sekarang-kota').html('<option value="">Pilih Kabupaten / Kota</option>')
                    $.each(response.regencies, function (key, regency) {
                        $('#alamat-sekarang-kota').append('<option value="' + regency.id + '">' + regency.name + '</option>')
                    });
                }
            }
        })
    });

    $('#alamat-sekarang-kota').on('change', function () {
        $('#alamat-sekarang-kecamatan').attr('disabled', true);
        $('#alamat-sekarang-desa').val('');
        $('#alamat-sekarang-desa').attr('disabled', true);
        $('#alamat-sekarang-alamat-rumah').attr('disabled', true);

        $.ajax({
            url: '{{ url('/indoregion/get-districts-by-regency') }}/'+$(this).val(),
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                // console.log(response)
                if(response.success == 1) {
                    $('#alamat-sekarang-kecamatan').removeAttr('disabled');
                    $('#alamat-sekarang-kecamatan').html('<option value="">Pilih Kecamatan</option>')
                    $.each(response.districts, function (key, district) {
                        $('#alamat-sekarang-kecamatan').append('<option value="' + district.id + '">' + district.name + '</option>')
                    });
                }
            }
        })
    });

    $('#alamat-sekarang-kecamatan').on('change', function () {
        $('#alamat-sekarang-desa').attr('disabled', true);
        $('#alamat-sekarang-alamat-rumah').attr('disabled', true);

        $.ajax({
            url: '{{ url('/indoregion/get-villages-by-district') }}/'+$(this).val(),
            type: 'GET',
            dataType: 'JSON',
            success: function ( response ) {
                // console.log(response)
                if(response.success == 1) {
                    $('#alamat-sekarang-desa').removeAttr('disabled');
                    $('#alamat-sekarang-desa').html('<option value="">Pilih Desa</option>')
                    $.each(response.villages, function (key, village) {
                        $('#alamat-sekarang-desa').append('<option value="' + village.id + '">' + village.name + '</option>')
                    });
                }
            }
        })
    });
    $('#alamat-sekarang-desa').on('change', function () {

        if($(this).val() != '') {
            $('#alamat-sekarang-alamat-rumah').removeAttr('disabled');
        }else{
            $('#alamat-sekarang-alamat-rumah').attr('disabled', true);
        }
    });

    $('textarea').keyup(function () {
        if($(this).val().length >= 30 && $(this).val().length < 60) {
            $(this).attr('rows', 2);
        }else if($(this).val().length >= 60 && $(this).val().length < 90) {
            $(this).attr('rows', 3);
        }else if($(this).val().length >= 90) {
            $(this).attr('rows', 4);
        }else{
            $(this).attr('rows', 1);
        }
    });

    $("#formDataKaryawan").submit(function(event) {
        event.preventDefault();
        if(!$('#disclaimer').prop('checked')) {
            swal("Hmm!", "Kamu harus menyetujui kalau data yang kamu masukan itu benar!", "info");
            return false;
        }
        $('#submitButton').attr('disabled', 'true');
        $('#submitButton').html("<i class='bx bx-loader bx-spin'></i>")
        $.ajax({
            url: "{{ url('form-data-karyawan/store') }}",
            type: "POST",
            dataTye: "JSON",
            data: $(this).serialize(),
            success: function ( response ) {
                $('#submitButton').removeAttr('disabled');
                $('#submitButton').html("Submit");
                if(response.success == '1') {
                    swal("Oke!", "Data kamu berhasil disimpan!", "success")
                        .then((value) => {
                            location.reload();
                        });
                }
                console.log( response );
            },
            error: function ( exception ) {
                alert('Belum bisa submit, coba lagi!');
                $('#submitButton').removeAttr('disabled');
                $('#submitButton').html("Submit");
                console.log( exception );
            }
        })
    });

</script>
</body>
</html>
