@extends('pages.halo-security.layout.base')

@section('title', 'BA Laporan Kejadian')

@section('content')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
    <link href="{{ asset('assets/velzon/libs/quill/quill.snow.css') }}" rel="stylesheet" />
@endpush

<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Berita Acara Laporan Kejadian</h4>
        </div>

        <div class="card-body">
            <form id="validate" action="{{route('balaporankejadian.editlaporankejadian',[$balaporankejadian->lk_id])}}" enctype="multipart/form-data" method="POST" autocomplete="off">
                {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="danru" class="form-label">Danru</label>
                                <input type="text" name="danru" id="danru" value="{{old('danru', $balaporankejadian->danru)}}" class="form-control" placeholder="Masukan Danru">
                                <p class="text-danger ml-2">
                                    @error('danru')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="jenis_kejadian" class="form-label">Jenis Kejadian</label>
                                <div id="jenis_kejadian_container">
                                    <select class="form-select mb-3" name="jenis_kejadian" id="jenis_kejadian">
                                        <option value="">Pilih Jenis Kejadian</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='kecelakaan lalu lintas') selected @endif value="kecelakaan lalu lintas">Kecelakaan Lalu lintas</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='penemuan barang') selected @endif value="penemuan barang">Penemuan Barang</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='kecelakaan kerja') selected @endif value="kecelakaan kerja">Kecelakaan Kerja</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='pencurian') selected @endif value="pencurian">Pencurian</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='perkelahian') selected @endif value="perkelahian">Perkelahian</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='tindak kekerasan') selected @endif value="tindak kekerasan">Tindak Kekerasan</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='kebakaran') selected @endif value="kebakaran">Kebakaran</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='demonstrasi') selected @endif value="demonstrasi">Demonstrasi</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='tindakan asusila') selected @endif value="tindakan asusila">Tindakan Asusila</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='pengerusakan') selected @endif value="pengerusakan">Pengerusakan</option>
                                        <option @if($balaporankejadian['jenis_kejadian']=='tindakan indispliner') selected @endif value="tindakan indispliner">Tindakan Indispliner</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <p class="text-danger ml-2">
                                    @error('jenis_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_korban" class="form-label">Nama Korban</label>
                                <input type="text" class="form-control" name="nama_korban" id="nama_korban" value="{{old('nama_korban', $balaporankejadian->nama_korban)}}" placeholder="Masukan Nama Korban">
                                <p class="text-danger ml-2">
                                    @error('nama_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nik_korban" class="form-label">Nik (Nomor Induk Karyawan) Korban</label>
                                <input type="text" name="nik_korban" id="nik_korban" value="{{old('nik_korban', $balaporankejadian->nik_korban)}}" class="form-control" placeholder="Masukan Nomor NIK (Nomor Induk Karyawan) Korban">
                                <p class="text-danger ml-2">
                                    @error('nik_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="perusahaan_korban" class="form-label">Perusahaan</label>
                                <input type="text" name="perusahaan_korban" id="perusahaan_korban" value="{{old('perusahaan_korban', $balaporankejadian->perusahaan_korban)}}" class="form-control" placeholder="Masukan perusahaan">
                                <p class="text-danger ml-2">
                                    @error('perusahaan_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagian_korban" class="form-label">Bagian Korban</label>
                                <input type="text" name="bagian_korban" id="bagian_korban" value="{{old('bagian_korban', $balaporankejadian->bagian_korban)}}" class="form-control" placeholder="Masukan Bagian Korban">
                                <p class="text-danger ml-2">
                                    @error('bagian_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="lokasi_kejadian" class="form-label">Lokasi Kejadian</label>
                                <textarea class="form-control" name="lokasi_kejadian" id="lokasi_kejadian" placeholder="Masukan Lokasi Kejadian" rows="3">{{old('lokasi_kejadian', $balaporankejadian->lokasi_kejadian)}}</textarea>
                                <p class="text-danger ml-2">
                                    @error('lokasi_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="test">
                                <div class="d-flex justify-content-between mb-2">
                                    <label for="fakta_kejadian" class="form-label">Fakta Kejadian</label>
                                </div>
                                <table class="table table-bordered" id="tablefk">
                                    <thead>
                                        <tr>
                                            <th>Keterangan Fakta Kejadian</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($balaporankejadian->faktas as $item)
                                        <tr>
                                            <td><textarea class="form-control" name="fakta_kejadian[{{ $item->id }}]" id="fakta_kejadian" placeholder="Masukan Fakta Kejadian" rows="3">{{$item->keterangan_fakta }}</textarea></td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    @if($loop->iteration == 1)
                                                    <button type="button" name="addfkedit" id="addfkedit" class="btn btn-md btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                                    @else
                                                    <button type="button" class="btn btn-md btn-danger remove-table-row-fk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="yang_terjadi" class="form-label">Kejadian yang terjadi</label>
                                <textarea class="form-control" name="yang_terjadi" id="yang_terjadi" placeholder="Masukan kejadian yang terjadi" rows="3">{{old('yang_terjadi', $balaporankejadian->yang_terjadi)}}</textarea>
                                <p class="text-danger ml-2">
                                    @error('yang_terjadi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_terlapor" class="form-label">Nama Terlapor</label>
                                <input type="text" name="nama_terlapor" id="nama_terlapor" value="{{old('nama_terlapor', $balaporankejadian->nama_terlapor)}}" class="form-control" placeholder="Masukan Nama Terlapor">
                                <p class="text-danger ml-2">
                                    @error('nama_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="umur_terlapor" class="form-label">Umur Terlapor</label>
                                <input type="text" name="umur_terlapor" id="umur_terlapor" value="{{old('umur_terlapor', $balaporankejadian->umur_terlapor)}}" class="form-control" placeholder="Masukan Umur Terlapor">
                                <p class="text-danger ml-2">
                                    @error('umur_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="ttl_terlapor" class="form-label">Tempat Tanggal Lahir Terlapor</label>
                                <input type="text" name="ttl_terlapor" id="ttl_terlapor" value="{{old('ttl_terlapor', $balaporankejadian->ttl_terlapor)}}" class="form-control" placeholder="Masukan Tempat Tanggal Lahir Terlapor">
                                <p class="text-danger ml-2">
                                    @error('ttl_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pekerjaan_terlapor" class="form-label">Pekerjaan Terlapor</label>
                                <input type="text" name="pekerjaan_terlapor" id="pekerjaan_terlapor" value="{{old('pekerjaan_terlapor', $balaporankejadian->pekerjaan_terlapor)}}" class="form-control" placeholder="Masukan Pekerjaan Terlapor">
                                <p class="text-danger ml-2">
                                    @error('pekerjaan_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="alamat_terlapor" class="form-label">Alamat Terlapor</label>
                                <input type="text" name="alamat_terlapor" id="alamat_terlapor" value="{{old('alamat_terlapor', $balaporankejadian->alamat_terlapor)}}" class="form-control" placeholder="Masukan Alamat Terlapor">
                                <p class="text-danger ml-2">
                                    @error('alamat_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="kelurahan_terlapor" class="form-label">Kelurahan Terlapor</label>
                                <input type="text" name="kelurahan_terlapor" id="kelurahan_terlapor" value="{{old('kelurahan_terlapor', $balaporankejadian->kelurahan_terlapor)}}" class="form-control" placeholder="Masukan Kelurahan Terlapor">
                                <p class="text-danger ml-2">
                                    @error('kelurahan_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="kecamatan_terlapor" class="form-label">Kecamatan Terlapor</label>
                                <input type="text" name="kecamatan_terlapor" id="kecamatan_terlapor" value="{{old('kecamatan_terlapor', $balaporankejadian->kecamatan_terlapor)}}" class="form-control" placeholder="Masukan Kecamatan Terlapor">
                                <p class="text-danger ml-2">
                                    @error('kecamatan_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="provinsi_terlapor" class="form-label">Provinsi Terlapor</label>
                                <input type="text" name="provinsi_terlapor" id="provinsi_terlapor" value="{{old('provinsi_terlapor', $balaporankejadian->provinsi_terlapor)}}" class="form-control" placeholder="Masukan Provinsi Terlapor">
                                <p class="text-danger ml-2">
                                    @error('provinsi_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status_terlapor" class="form-label">Status Terlapor</label>
                                <select class="form-select mb-3" name="status_terlapor" id="status_terlapor">
                                    <option value="">Pilih Status Terlapor</option>
                                    <option @if($balaporankejadian['status_terlapor']=='belum kawin') selected @endif value="belum kawin">Belum Kawin</option>
                                    <option @if($balaporankejadian['status_terlapor']=='sudah kawin') selected @endif value="sudah kawin">Sudah Kawin</option>
                                    <option @if($balaporankejadian['status_terlapor']=='janda/duda') selected @endif value="janda/duda">Janda/Duda</option>
                                </select>
                                <p class="text-danger ml-2">
                                    @error('status_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="agama_terlapor" class="form-label">Agama Terlapor</label>
                                <input type="text" name="agama_terlapor" id="agama_terlapor" value="{{old('agama_terlapor', $balaporankejadian->agama_terlapor)}}" class="form-control" placeholder="Masukan Agama Terlapor">
                                <p class="text-danger ml-2">
                                    @error('agama_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="kebangsaan_terlapor" class="form-label">Kebangsaan Terlapor</label>
                                <input type="text" name="kebangsaan_terlapor" id="kebangsaan_terlapor" value="{{old('kebangsaan_terlapor', $balaporankejadian->kebangsaan_terlapor)}}" class="form-control" placeholder="Masukan Kebangsaan Terlapor">
                                <p class="text-danger ml-2">
                                    @error('kebangsaan_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_ktp_terlapor" class="form-label">Nomor KTP Terlapor</label>
                                <input type="text" name="no_ktp_terlapor" id="no_ktp_terlapor" value="{{old('no_ktp_terlapor', $balaporankejadian->no_ktp_terlapor)}}" class="form-control" placeholder="Masukan Nomor KTP Terlapor">
                                <p class="text-danger ml-2">
                                    @error('no_ktp_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_simc_terlapor" class="form-label">Nomor SIM Terlapor</label>
                                <input type="text" name="no_simc_terlapor" id="no_simc_terlapor" value="{{old('no_simc_terlapor', $balaporankejadian->no_simc_terlapor)}}" class="form-control" placeholder="Masukan Nomor SIM C Terlapor">
                                <p class="text-danger ml-2">
                                    @error('no_simc_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_hp_terlapor" class="form-label">Nomor Handphone Terlapor</label>
                                <input type="text" name="no_hp_terlapor" id="no_hp_terlapor" value="{{old('no_hp_terlapor', $balaporankejadian->no_hp_terlapor)}}" class="form-control" placeholder="Masukan Nomor Handphone Terlapor">
                                <p class="text-danger ml-2">
                                    @error('no_hp_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagaimana_terjadi" class="form-label">Bagiamana Terjadi</label>
                                <textarea class="form-control" name="bagaimana_terjadi" id="bagaimana_terjadi" placeholder="Masukan Bagiamana Terjadi" rows="3">{{old('bagaimana_terjadi', $balaporankejadian->bagaimana_terjadi)}}</textarea>
                                <p class="text-danger ml-2">
                                    @error('bagaimana_terjadi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="mengapa_terjadi" class="form-label">Mengapa Terjadi</label>
                                <textarea class="form-control" name="mengapa_terjadi" id="mengapa_terjadi" placeholder="Masukan Mengapa Terjadi" rows="3">{{old('mengapa_terjadi', $balaporankejadian->mengapa_terjadi)}}</textarea>
                                <p class="text-danger ml-2">
                                    @error('mengapa_terjadi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="saksi_kejadian" class="form-label">Saksi Kejadian</label>
                                <table class="table table-bordered" id="tablesk">
                                    <tr>
                                        <th>Nama Saksi</th>
                                        <th>Nik Saksi</th>
                                        <th>Departemen Saksi</th>
                                        <th>Keterangan Saksi</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    @foreach ($balaporankejadian->saksis as $item)
                                    <tr>
                                        <td><input type="text" name="nama_saksi[{{ $item->id }}]" id="nama_saksi" value="{{ $item->nama_saksi }}" class="form-control" placeholder="Masukan Nama Saksi"></td>
                                        <td><input type="text" name="nik_saksi[{{ $item->id }}]" id="nik_saksi" value="{{ $item->nik_saksi }}" class="form-control" placeholder="Masukan Nik Saksi"></td>
                                        <td><input type="text" name="departement_saksi[{{ $item->id }}]" id="departement_saksi" value="{{ $item->departement_saksi }}" class="form-control" placeholder="Masukan Departemen Saksi"></td>
                                        <td><input type="text" name="keterangan_saksi[{{ $item->id }}]" id="keterangan_saksi" value="{{ $item->keterangan_saksi }}" class="form-control" placeholder="Masukan Keterangan Saksi"></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @if($loop->iteration == 1)
                                                <button type="button" name="addskedit" id="addskedit" class="btn btn-md btn-success"><i class="ri-add-box-line" style="font-size: 15px;"></i></button>
                                                @else
                                                <button type="button" class="btn btn-md btn-danger remove-table-row-sk"><i class="ri-delete-bin-2-line" style="font-size: 15px;"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                                {{-- <p class="text-danger ml-2">
                                    @error('saksi_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p> --}}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="uraian_kejadian" class="form-label">Uraian Kejadian</label>
                                <textarea class="form-control" style="display: none; color: white;" name="uraian_kejadian" id="uraian_kejadian-textarea" placeholder="Masukan Uraian Kejadian" rows="3">{!! old('uraian_kejadian', $balaporankejadian->uraian_kejadian) !!}</textarea>
                                <div id="uraian_kejadian">{!! old('uraian_kejadian', $balaporankejadian->uraian_kejadian) !!}</div>
                                <p class="text-danger ml-2">
                                    @error('uraian_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="tindakan_pengamanan" class="form-label">Tindakan Pengamanan</label>
                                <textarea class="form-control" style="display: none; color: white;" name="tindakan_pengamanan" id="tindakan_pengamanan-textarea" placeholder="Masukan Tindakan Pengamanan" rows="3">{!! old('tindakan_pengamanan', $balaporankejadian->tindakan_pengamanan) !!}</textarea>
                                <div id="tindakan_pengamanan">{!! old('tindakan_pengamanan', $balaporankejadian->tindakan_pengamanan) !!}</div>
                                <p class="text-danger ml-2">
                                    @error('tindakan_pengamanan')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="hasil_daritindakan" class="form-label">Hasil Dari Tindakan</label>
                                <textarea class="form-control" style="display: none; color: white;" name="hasil_daritindakan" id="hasil_daritindakan-textarea" placeholder="Masukan Hasil Dari Tindakan" rows="3">{!! old('hasil_daritindakan', $balaporankejadian->hasil_daritindakan) !!}</textarea>
                                <div id="hasil_daritindakan">{!! old('hasil_daritindakan', $balaporankejadian->hasil_daritindakan) !!}</div>
                                <p class="text-danger ml-2">
                                    @error('hasil_daritindakan')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="saran" class="form-label">Saran</label>
                                {{-- <textarea class="form-control" name="saran" placeholder="Masukan Saran" rows="3">{{old('saran', $balaporankejadian->saran)}}</textarea> --}}
                                {{-- <div name="saran" id="saran"></div> --}}
                                <textarea class="form-control" style="display: none; color: white;" name="saran" id="saran-textarea" placeholder="Masukan Saran" rows="3">{!! old('saran', $balaporankejadian->saran) !!}</textarea>
                                <div id="saran">{!! old('saran', $balaporankejadian->saran) !!}</div>
                                <p class="text-danger ml-2">
                                    @error('saran')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="dokumentasi_kejadian" class="form-label">Dokumentasi Kejadian</label>
                                <table class="table table-bordered" id="tabledk">
                                    <tr>
                                        <th>Foto Kejadian</th>
                                        <th>Keterangan Kejadian</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    @foreach ($balaporankejadian->dokumentasis as $item)
                                    <tr>
                                        <td style="max-width: 250px">
                                            <input type="hidden" name="dokumentasi_id[{{ $item->id }}]" value="{{ $item->id }}">
                                            <input accept="image/png, image/jpeg" class="form-control foto-introgasi_0" type="file" name="dokumentasi_kejadian[{{ $item->id }}][]" data-key="0" id="images0" multiple>
                                            <p class="text-center mt-3">Foto Lama Kejadian</p>
                                            <div class="mb-4 mt-4 g-10">
                                                @foreach (explode(',', $item->foto_kejadian) as $image)
                                                    <img src="/master_laporan_kejadian_gambar/{{ $image }}" style="height: 200px; width: 200px;">
                                                @endforeach
                                            </div>
                                            <p class="text-center mt-3">Foto Baru Kejadian</p>
                                            <div class="mt-4">
                                                <div class="preview-images_0" style="columns: 2; margin-left: -12px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="keterangan_kejadian[{{ $item->id }}]" placeholder="Masukan Keterangan Kejadian" rows="3">{{$item->keterangan_kejadian }}</textarea>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @if($loop->iteration == 1)
                                                <button type="button" name="adddkedit" id="adddkedit" class="btn btn-sm btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                                @else
                                                <button type="button" class="btn btn-sm btn-danger remove-table-row-dk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                                {{-- <p class="text-danger ml-2">
                                    @error('dokumentasi_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p> --}}
                            </div>
                        </div>
                        <div class="col-lg-12">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            <a href="{{ route('ba-list-laporankejadian') }}" class="btn btn-dark btn-md">Kembali</a>
                                        </div>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-md btn-primary">
                                                Ubah
                                            </button>
                                        </div>
                                    </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/velzon/js/pages/form-editor.init.js') }}"></script>
<script>

    $(document).ready(function () {

    setInterval(keepTokenAlive, 1000 * 60 * 15); // every 15 mins

    function keepTokenAlive() {
        $.ajax({
            url: "{{ url('halo-security/keep-token-alive') }}", //https://stackoverflow.com/q/31449434/470749
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).then(function (result) {
            console.log(new Date() + ' ' + result + ' ' + $('meta[name="csrf-token"]').attr('content'));
        });
    }

    });

    var i = 0;

    // Untuk textarea uraian kejadian
    var quill = new Quill('#uraian_kejadian', {
        modules: {
            toolbar: false
        },
        placeholder: 'Data Hasil Dari Tindakan',
        theme: 'snow',
    });

    quill.on('text-change', function(delta, source) {
        $('#uraian_kejadian-textarea').val($('#uraian_kejadian .ql-editor').html())
    });

    // Untuk tindakan pengamanan
    var quill = new Quill('#tindakan_pengamanan', {
        modules: {
            toolbar: false
        },
        placeholder: 'Data Hasil Dari Tindakan',
        theme: 'snow',
    });

    quill.on('text-change', function(delta, source) {
        $('#tindakan_pengamanan-textarea').val($('#tindakan_pengamanan .ql-editor').html())
    });

    // Untuk textarea hasil dari tindakan
    var quill = new Quill('#hasil_daritindakan', {
        modules: {
            toolbar: false
        },
        placeholder: 'Data Hasil Dari Tindakan',
        theme: 'snow',
    });

    quill.on('text-change', function(delta, source) {
        $('#hasil_daritindakan-textarea').val($('#hasil_daritindakan .ql-editor').html())
    });

    // Untuk textarea saran
    var quill = new Quill('#saran', {
        modules: {
            toolbar: false
        },
        placeholder: 'Data Saran',
        theme: 'snow',
    });

    quill.on('text-change', function(delta, source) {
        $('#saran-textarea').val($('#saran .ql-editor').html())
    });

    $('#jenis_kejadian').on('change', function() {
        if($(this).val() == 'lainnya') {
            $('#jenis_kejadian_container').html('')
            $('#jenis_kejadian_container').html(`
                <input class="form-control mb-3" name="jenis_kejadian" id="jenis_kejadian" value="{{old('jenis_kejadian', $balaporankejadian->jenis_kejadian)}}" placeholder="Jenis Kejadian lainnya" />
            `);
        }
    });

    // Untuk Fakta Kejadian
    $('#addfkedit').click(function(){
        ++i;
        $('#tablefk').append(
            `
            <tbody>
            <tr>
                <td>
                    <textarea class="form-control" name="fakta_kejadian[]" placeholder="Masukan Fakta Kejadian" rows="3"></textarea>
                </td>
                <td>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-md btn-danger remove-table-row-fk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                    </div>
                </td>
            </tr>
            </tbody>
            `);
    });

    $(document).on('click','.remove-table-row-fk', function(){
        $(this).parents('tr').remove();
    })

    // Untuk Saksi Kejadian
    $('#addskedit').click(function(){
        ++i;
        $('#tablesk').append(
            `<tr>
                <td><input type="text" name="nama_saksi[]" class="form-control" placeholder="Masukan Nama Saksi"></td>
                <td><input type="text" name="nik_saksi[]" class="form-control" placeholder="Masukan Nik Saksi"></td>
                <td><input type="text" name="departement_saksi[]" class="form-control" placeholder="Masukan Departemen Saksi"></td>
                <td><input type="text" name="keterangan_saksi[]" class="form-control" placeholder="Masukan Keterangan Saksi"></td>
                <td>
                    <button type="button" class="btn btn-md btn-danger remove-table-row-sk"><i class="ri-delete-bin-2-line" style="font-size: 15px;"></i></button>
                </td>

            </tr>`);
    });

    $(document).on('click','.remove-table-row-sk', function(){
        $(this).parents('tr').remove();
    })

    // Untuk Dokumentasi Kejadian
    $('#adddkedit').click(function(){
        ++i;
        $('#tabledk').append(
            `<tr>
                <td style="max-width: 250px">
                    <input accept="image/png, image/jpeg" class="form-control foto-introgasi_${i}" type="file" name="dokumentasi_kejadian[new${i}][]" id="images${i}" data-key="${i}" multiple>
                    <div class="m-4">
                        <div class="preview-images_${i}" style="columns: 2; margin-left: -15px;"></div>
                    </div>
                </td>
                <td><textarea class="form-control" name="keterangan_kejadian[new${i}]" placeholder="Masukan Keterangan Kejadian" rows="3"></textarea></td>
                <td>
                    <div class="d-flex justify-content-center mx-auto">
                        <button type="button" class="btn btn-sm btn-danger remove-table-row-dk"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                    </div>
                </td>

            </tr>`);
        
            // Preview Multiple Gambar
            $(`.foto-introgasi_${i}`).on("change", function () {
                var key = $(this).data('key');
                previewFoto(this, key);
            });
    });

    // Preview Multiple Gambar
    function previewFoto(input, key) {
        var previewContainer = document.querySelector(`.preview-images_${key}`);
        
        previewContainer.innerHTML = "";
        
        if (input.files) {
            Array.from(input.files).forEach(function (file) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var img = document.createElement("img");
                    img.src = e.target.result;
                    img.style.width = "200px";
                    img.style.height = "150px";
                    img.style.display = "flex";
                    img.style.margin = "auto auto 20px 20px";
                    img.className = `preview-images_${key}`;

                    previewContainer.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        }
        
    }

    // Untuk memanggil function preview multiple foto
    $(`.foto-introgasi_${i}`).on("change", function () {
        var key = $(this).data('key');
        previewFoto(this, key);
    });

    $(document).on('click','.remove-table-row-dk', function(){
        $(this).parents('tr').remove();
    })

    // Validasi Data Input
    $("#validate").submit(function() {
            // Mengambil data
            var jenis_kejadian = $("#jenis_kejadian").val();
            var nama_korban = $("#nama_korban").val();
            var nik_korban = $("#nik_korban").val();
            var perusahaan_korban = $("#perusahaan_korban").val();
            var bagian_korban = $("#bagian_korban").val();
            var lokasi_kejadian = $("#lokasi_kejadian").val();
            var fakta_kejadian = $("#fakta_kejadian").val();
            var yang_terjadi = $("#yang_terjadi").val();
            var nama_terlapor = $("#nama_terlapor").val();
            var umur_terlapor = $("#umur_terlapor").val();
            var ttl_terlapor = $("#ttl_terlapor").val();
            var pekerjaan_terlapor = $("#pekerjaan_terlapor").val();
            var alamat_terlapor = $("#alamat_terlapor").val();
            var kelurahan_terlapor = $("#kelurahan_terlapor").val();
            var kecamatan_terlapor = $("#kecamatan_terlapor").val();
            var provinsi_terlapor = $("#provinsi_terlapor").val();
            var status_terlapor = $("#status_terlapor").val();
            var agama_terlapor = $("#agama_terlapor").val();
            var kebangsaan_terlapor = $("#kebangsaan_terlapor").val();
            var no_ktp_terlapor = $("#no_ktp_terlapor").val();
            var no_simc_terlapor = $("#no_simc_terlapor").val();
            var no_hp_terlapor = $("#no_hp_terlapor").val();
            var bagaimana_terjadi = $("#bagaimana_terjadi").val();
            var mengapa_terjadi = $("#mengapa_terjadi").val();
            var nama_saksi = $("#nama_saksi").val();
            var nik_saksi = $("#nik_saksi").val();
            var departement_saksi = $("#departement_saksi").val();
            var keterangan_saksi = $("#keterangan_saksi").val();

            // Validasi
            if(jenis_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Jenis Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nama_korban == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nama Korban Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nik_korban == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nik Korban Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(perusahaan_korban == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Perusahaan Korban Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(bagian_korban == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Bagian Korban Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(lokasi_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Lokasi Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(fakta_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Fakta Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(yang_terjadi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Yang Terjadi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nama_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nama Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(umur_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Umur Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(ttl_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Tempat Tanggal Lahir Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(pekerjaan_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Pekerjaan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(alamat_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Alamat Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(kelurahan_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Kelurahan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(kecamatan_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Kecamatan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(provinsi_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Provinsi Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(status_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Status Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(agama_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Agama Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(kebangsaan_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Kebangsaan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(no_ktp_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nomor KTP Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(no_simc_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nomor SIM C Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(no_hp_terlapor == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nomor HP Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(bagaimana_terjadi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Bagaimana Terjadi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(mengapa_terjadi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Mengapa Terjadi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nama_saksi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nama Saksi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nik_saksi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nik Saksi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(departement_saksi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Departement Saksi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(keterangan_saksi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Keterangan Saksi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }
    })
</script>
@endpush