@extends('pages.halo-security.layout.base')

@section('title', 'BA Introgasi')

@section('content')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@endpush

<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Berita Acara Introgasi</h4>
        </div>

        <div class="card-body">
            <form id="validate" action="{{route('baintrogasi.editintrogasi',[$baintrogasi->bai_id])}}" enctype="multipart/form-data" method="POST" autocomplete="off">
                {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="jenis_kejadian" class="form-label">Jenis Kejadian</label>
                                <div id="jenis_kejadian_container">
                                    <select class="form-select mb-3" name="jenis_kejadian" id="jenis_kejadian">
                                        <option value="">Pilih Jenis Kejadian</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='kecelakaan lalu lintas') selected @endif value="kecelakaan lalu lintas">Kecelakaan Lalu lintas</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='penemuan barang') selected @endif value="penemuan barang">Penemuan Barang</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='kecelakaan kerja') selected @endif value="kecelakaan kerja">Kecelakaan Kerja</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='pencurian') selected @endif value="pencurian">Pencurian</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='perkelahian') selected @endif value="perkelahian">Perkelahian</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='tindak kekerasan') selected @endif value="tindak kekerasan">Tindak Kekerasan</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='kebakaran') selected @endif value="kebakaran">Kebakaran</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='demonstrasi') selected @endif value="demonstrasi">Demonstrasi</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='tindakan asusila') selected @endif value="tindakan asusila">Tindakan Asusila</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='pengerusakan') selected @endif value="pengerusakan">Pengerusakan</option>
                                        <option @if($baintrogasi['jenis_kejadian']=='tindakan indispliner') selected @endif value="tindakan indispliner">Tindakan Indispliner</option>
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
                                <label for="nama_introgasi" class="form-label">Nama Introgasi</label>
                                <input type="text" class="form-control" name="nama_introgasi" id="nama_introgasi" value="{{old('nama_introgasi', $baintrogasi->nama_introgasi)}}" placeholder="Masukan nama introgasi">
                                <p class="text-danger ml-2">
                                    @error('nama_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="umur_introgasi" class="form-label">Umur Introgasi</label>
                                <input type="text" name="umur_introgasi" id="umur_introgasi" value="{{old('umur_introgasi', $baintrogasi->umur_introgasi)}}" class="form-control" placeholder="Masukan umur introgasi">
                                <p class="text-danger ml-2">
                                    @error('umur_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pekerjaan_introgasi" class="form-label">Pekerjaan Introgasi</label>
                                <input type="text" name="pekerjaan_introgasi" id="pekerjaan_introgasi" value="{{old('pekerjaan_introgasi', $baintrogasi->pekerjaan_introgasi)}}" class="form-control" placeholder="Masukan perusahaan">
                                <p class="text-danger ml-2">
                                    @error('pekerjaan_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagian_introgasi" class="form-label">Bagian Introgasi</label>
                                <input type="text" name="bagian_introgasi" id="bagian_introgasi" value="{{old('bagian_introgasi', $baintrogasi->bagian_introgasi)}}" class="form-control" placeholder="Masukan bagian introgasi">
                                <p class="text-danger ml-2">
                                    @error('bagian_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_pelapor" class="form-label">Nama Pelapor</label>
                                <input type="text" name="nama_pelapor" id="nama_pelapor" value="{{old('nama_pelapor', $baintrogasi->nama_pelapor)}}" class="form-control" placeholder="Masukan nama pelapor">
                                <p class="text-danger ml-2">
                                    @error('nama_pelapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="detail_barang_kejadian" class="form-label">Detail Kejadian / Motif Kejadian</label>
                                <input type="text" name="detail_barang_kejadian" id="detail_barang_kejadian" value="{{old('detail_barang_kejadian', $baintrogasi->detail_barang_kejadian)}}" class="form-control" placeholder="Masukan jenis kejadian dan barang">
                                <p class="text-danger ml-2">
                                    @error('detail_barang_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="tempat_kejadian" class="form-label">Tempat Kejadian</label>
                                <input type="text" name="tempat_kejadian" id="tempat_kejadian" value="{{old('tempat_kejadian', $baintrogasi->tempat_kejadian)}}" class="form-control" placeholder="Masukan tempat kejadian">
                                <p class="text-danger ml-2">
                                    @error('tempat_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_korban" class="form-label">Nama Korban</label>
                                <input type="text" name="nama_korban" id="nama_korban" value="{{old('nama_korban', $baintrogasi->nama_korban)}}" class="form-control" placeholder="Masukan nama korban">
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
                                <input type="text" name="nik_korban" id="nik_korban" value="{{old('nik_korban', $baintrogasi->nik_korban)}}" class="form-control" placeholder="Masukan nik korban">
                                <p class="text-danger ml-2">
                                    @error('nik_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagian_korban" class="form-label">Bagian Korban</label>
                                <input type="text" name="bagian_korban" id="bagian_korban" value="{{old('bagian_korban', $baintrogasi->bagian_korban)}}" class="form-control" placeholder="Masukan bagian korban">
                                <p class="text-danger ml-2">
                                    @error('bagian_korban')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_pelaku" class="form-label">Nama Pelaku</label>
                                <input type="text" name="nama_pelaku" id="nama_pelaku" value="{{old('nama_pelaku', $baintrogasi->nama_pelaku)}}" class="form-control" placeholder="Masukan nama pelaku">
                                <p class="text-danger ml-2">
                                    @error('nama_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="umur_pelaku" class="form-label">Umur Pelaku</label>
                                <input type="text" name="umur_pelaku" id="umur_pelaku" value="{{old('umur_pelaku', $baintrogasi->umur_pelaku)}}" class="form-control" placeholder="Masukan nama korban">
                                <p class="text-danger ml-2">
                                    @error('umur_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="ttl_pelaku" class="form-label">Tempat Tanggal Lahir Pelaku</label>
                                <input type="text" name="ttl_pelaku" id="ttl_pelaku" value="{{old('ttl_pelaku', $baintrogasi->ttl_pelaku)}}" class="form-control" placeholder="Masukan tempat tanggal lahir pelaku">
                                <p class="text-danger ml-2">
                                    @error('ttl_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pekerjaan_pelaku" class="form-label">Pekerjaan Pelaku</label>
                                <input type="text" name="pekerjaan_pelaku" id="pekerjaan_pelaku" value="{{old('pekerjaan_pelaku', $baintrogasi->pekerjaan_pelaku)}}" class="form-control" placeholder="Masukan pekerjaan pelaku">
                                <p class="text-danger ml-2">
                                    @error('pekerjaan_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nik_pelaku" class="form-label">Nik (Nomor Induk Karyawan) Pelaku</label>
                                <input type="text" name="nik_pelaku" id="nik_pelaku" value="{{old('nik_pelaku', $baintrogasi->nik_pelaku)}}" class="form-control" placeholder="Masukan nik pelaku">
                                <p class="text-danger ml-2">
                                    @error('nik_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagian_pelaku" class="form-label">Bagian Pelaku</label>
                                <input type="text" name="bagian_pelaku" id="bagian_pelaku" value="{{old('bagian_pelaku', $baintrogasi->bagian_pelaku)}}" class="form-control" placeholder="Masukan bagian pelaku">
                                <p class="text-danger ml-2">
                                    @error('bagian_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="alamat_pelaku" class="form-label">Alamat Pelaku</label>
                                <textarea class="form-control" name="alamat_pelaku" id="alamat_pelaku" placeholder="Masukan alamat pelaku" rows="3">{{old('alamat_pelaku', $baintrogasi->alamat_pelaku)}}</textarea>
                                <p class="text-danger ml-2">
                                    @error('alamat_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="agama_pelaku" class="form-label">Agama Pelaku</label>
                                <input type="text" name="agama_pelaku" id="agama_pelaku" value="{{old('agama_pelaku', $baintrogasi->agama_pelaku)}}" class="form-control" placeholder="Masukan agama pelaku">
                                <p class="text-danger ml-2">
                                    @error('agama_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="suku_pelaku" class="form-label">Suku Pelaku</label>
                                <input type="text" name="suku_pelaku" id="suku_pelaku" value="{{old('suku_pelaku', $baintrogasi->suku_pelaku)}}" class="form-control" placeholder="Masukan suku pelaku">
                                <p class="text-danger ml-2">
                                    @error('suku_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="status_pelaku" class="form-label">Status Pelaku</label>
                                <select class="form-select mb-3" name="status_pelaku" id="status_pelaku">
                                    <option value="">Pilih Status Terlapor</option>
                                    <option @if($baintrogasi['status_pelaku']=='belum kawin') selected @endif value="belum kawin">Belum Kawin</option>
                                    <option @if($baintrogasi['status_pelaku']=='sudah kawin') selected @endif value="sudah kawin">Sudah Kawin</option>
                                    <option @if($baintrogasi['status_pelaku']=='janda/duda') selected @endif value="janda/duda">Janda/Duda</option>
                                </select>
                                <p class="text-danger ml-2">
                                    @error('status_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="shift" class="form-label">Shift</label>
                                <select class="form-select mb-3" name="shift" id="shift">
                                    <option value="">Pilih Shift</option>
                                    <option @if($baintrogasi['shift']=='1') selected @endif value="1">1</option>
									<option @if($baintrogasi['shift']=='2') selected @endif value="2">2</option>
                                    <option @if($baintrogasi['shift']=='3') selected @endif value="3">3</option>
                                </select>
                                <p class="text-danger ml-2">
                                    @error('shift')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="pendidikan_pelaku" class="form-label">Pendidikan Pelaku</label>
                                <input type="text" name="pendidikan_pelaku" id="pendidikan_pelaku" value="{{old('pendidikan_pelaku', $baintrogasi->pendidikan_pelaku)}}" class="form-control" placeholder="Masukan pendidikan pelaku">
                                <p class="text-danger ml-2">
                                    @error('pendidikan_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nik_ktp_pelaku" class="form-label">Nik KTP Pelaku</label>
                                <input type="text" name="nik_ktp_pelaku" id="nik_ktp_pelaku" value="{{old('nik_ktp_pelaku', $baintrogasi->nik_ktp_pelaku)}}" class="form-control" placeholder="Masukan nik ktp pelaku">
                                <p class="text-danger ml-2">
                                    @error('nik_ktp_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_hp_pelaku" class="form-label">Nomor HP Pelaku</label>
                                <input type="text" name="no_hp_pelaku" id="no_hp_pelaku" value="{{old('no_hp_pelaku', $baintrogasi->no_hp_pelaku)}}" class="form-control" placeholder="Masukan nomor hp pelaku">
                                <p class="text-danger ml-2">
                                    @error('no_hp_pelaku')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="tempat_introgasi" class="form-label">Tempat Introgasi</label>
                                <input type="text" name="tempat_introgasi" id="tempat_introgasi" value="{{old('tempat_introgasi', $baintrogasi->tempat_introgasi)}}" class="form-control" placeholder="Masukan tempat introgasi">
                                <p class="text-danger ml-2">
                                    @error('tempat_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="keterangan_kejadian" class="form-label">Keterangan Kejadian</label>
                                <input type="text" name="keterangan_kejadian" id="keterangan_kejadian" value="{{old('keterangan_kejadian', $baintrogasi->keterangan_kejadian)}}" class="form-control" placeholder="Masukan keterangan kejadian">
                                <p class="text-danger ml-2">
                                    @error('keterangan_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div id="main">

                            </div>
                            <div class="mb-3">
                                <label for="pertanyaan_introgasi" class="form-label">Tanya Jawab Introgasi</label>
                                <table class="table table-bordered" id="tabletji">
                                    <thead>
                                        <tr>
                                            <th>Pertanyaan Introgasi</th>
                                            <th>Jawaban Introgasi</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($baintrogasi->baiitems as $item)
                                        <tr>
                                            <input type="hidden" name="item_id" id="item_id">
                                            <td><textarea class="form-control pertanyaan_introgasi" name="pertanyaan_introgasi[{{ $item->id }}]" id="pertanyaan_introgasi-{{ $item->id }}" placeholder="Masukan pertanyaan introgasi" rows="3">{{ $item->pertanyaan_introgasi }}</textarea></td>
                                            <td><textarea class="form-control jawaban_introgasi" name="jawaban_introgasi[{{ $item->id }}]" id="jawaban_introgasi-{{ $item->id }}" placeholder="Masukan jawaban introgasi" rows="3">{{ $item->jawaban_introgasi }}</textarea></td>
                                            <td>
                                                <div class="d-flex justify-content-center mx-auto" style="margin-top: 20px;">
                                                    @if($loop->iteration == 1)
                                                    <button type="button" name="addtji" id="addtji" class="btn btn-sm btn-success" style="margin-right: 10px;"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                                    {{-- <button type="button" class="btn btn-sm btn-primary" onClick="openModalSelectTemplate({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-search-line" style="font-size: 20px;"></i></button> --}}
                                                    @else
                                                    <button type="button" class="btn btn-md btn-danger remove-table-row-tji mt-3" style="margin-right: 10px;"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                                                    {{-- <button type="button" class="btn btn-sm btn-primary" onClick="openModalSelectTemplate({{ $item->id }})" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-search-line" style="font-size: 20px;"></i></button> --}}
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
                                <label for="foto_introgasi" class="form-label">Dokumentasi Introgasi</label>
                                <table class="table table-bordered" id="tabledi">
                                    <tr>
                                        <th>Foto Introgasi</th>
                                        <th>Keterangan Introgasi</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    @foreach ($baintrogasi->dokumentasibais as $item)
                                    <tr>
                                        <td style="max-width: 250px">
                                            <input type="hidden" name="dokumentasi_id[{{ $item->id }}]" value="{{ $item->id }}">
                                            <input accept="image/png, image/jpeg" class="form-control foto-introgasi_0" type="file" name="foto_introgasi[{{ $item->id }}][]" data-key="0" id="images0" multiple>
                                            <p class="text-center mt-3">Foto Lama Introgasi</p>
                                            <div class="mb-4 mt-3 g-10">
                                                @foreach (explode(',', $item->foto_introgasi) as $image)
                                                    <img src="/master_introgasi_gambar/{{ $image }}" style="height: 150px; width: 200px; margin-left: 5px;">
                                                @endforeach
                                            </div>
                                            <p class="text-center mt-3">Foto Baru Introgasi</p>
                                            <div class="mt-4">
                                                <div class="preview-images_0" style="columns: 2; margin-left: -12px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="keterangan_introgasi[{{ $item->id }}]" id="keterangan_introgasi" placeholder="Masukan Keterangan Kejadian" rows="3">{{$item->keterangan_introgasi }}</textarea>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @if($loop->iteration == 1)
                                                <button type="button" name="adddiedit" id="adddiedit" class="btn btn-sm btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                                @else
                                                <button type="button" class="btn btn-sm btn-danger remove-table-row-di mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                                <p class="text-danger ml-2">
                                    @error('foto_introgasi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bai_oneimage" class="form-label">Foto Dokumentasi Kejadian</label>
                                <input type="file" name="bai_oneimage" id="bai_oneimage" class="form-control" onchange="previewImage(this)">
                                <div class="mb-4 mt-4 row d-flex justify-space-around">
                                    <div class="col-md-6">
                                        <label for="bai_oneimage" class="text-center">Foto Lama Dokumentasi Kejadian</label>
                                        <img src="/baioneimage-halosecurity/{{ $baintrogasi->bai_oneimage }}" width="350px" height="250px" alt="Image">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bai_oneimage" class="form-label">Foto Baru Dokumentasi Kejadian</label>
                                        <img src="/baioneimage-halosecurity/{{ $baintrogasi->bai_oneimage }}" width="350px" height="250px" alt="Image" id="image-preview">
                                    </div>
                                </div>
                                <p class="text-danger ml-2">
                                    @error('bai_oneimage')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <a href="{{ route('ba-list-introgasi') }}" class="btn btn-dark btn-md">Kembali</a>
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

<!-- Default Modals -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Template Tanya Jawab BA Introgasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="product-content">
                    <div id="hapustemplate"></div>
                    <nav>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success" id="nav-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="nav-speci-tab" data-bs-toggle="tab" href="#nav-speci" role="tab" aria-controls="nav-speci" aria-selected="true">Data Template Tanya Jawab BA Introgasi</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="nav-additional-tab" data-bs-toggle="tab" href="#nav-additional" role="tab" aria-controls="nav-additional" aria-selected="false" tabindex="-1">Tambah Data Template Tanya Jawab BA Introgasi</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="nav-speci" role="tabpanel" aria-labelledby="nav-speci-tab">
                            <div class="table-responsive">
                                <table id="tanya" class="table table-bordered dt-responsive nowrap table-striped align-middle" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-additional" role="tabpanel" aria-labelledby="nav-additional-tab">
                            <form id="addTag">
                                @csrf
                                    <div>
                                        <label for="pertanyaan_introgasi" class="form-label">Pertanyaan Introgasi</label>
                                        <textarea class="form-control pertanyaan_introgasi" id="pertanyaan_introgasi" name="pertanyaan_introgasi" placeholder="Masukan pertanyaan introgasi" rows="3"></textarea>
                                    </div>
                                    <div>
                                        <label for="jawaban_introgasi" class="form-label mt-3">Jawaban Introgasi</label>
                                        <textarea class="form-control jawaban_introgasi" id="jawaban_introgasi" name="jawaban_introgasi" placeholder="Masukan jawaban introgasi" rows="3"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" id="submit-data" class="btn btn-primary add_template">Simpan</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- Ubah Data Template BAI Items --}}
<div id="EditTemplateModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ubah Data Template Tanya Jawab BA Introgasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            
                <div class="modal-body">
                    <form id="templateEdit">
                        {{-- {{csrf_field()}} --}}
                        @csrf
                        <input type="hidden" id="id" name="id" />
                        <div class="mb-2">
                            <label for="pertanyaan_introgasi" class="form-label">Pertanyaan</label>
                            <textarea class="form-control pertanyaan_introgasi" id="edit_pertanyaan_introgasi" name="edit_pertanyaan_introgasi" placeholder="Masukan pertanyaan introgasi" rows="3"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="jawaban_introgasi" class="form-label">Jawaban</label>
                            <textarea class="form-control jawaban_introgasi" id="edit_jawaban_introgasi" name="edit_jawaban_introgasi" placeholder="Masukan jawaban introgasi" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="modal-footer">
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" style="margin-right: 10px;" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" id="update" class="btn btn-primary">Ubah</button>
                    </div>
                </div>
            
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@push('scripts')
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

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
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

    $('#jenis_kejadian').on('change', function() {
        if($(this).val() == 'lainnya') {
            $('#jenis_kejadian_container').html('')
            $('#jenis_kejadian_container').html(`
                <input class="form-control mb-3" name="jenis_kejadian" id="jenis_kejadian" value="{{old('jenis_kejadian', $baintrogasi->jenis_kejadian)}}" placeholder="Jenis Kejadian lainnya" />
            `);
        }
    });

    var i = 0;

    // Untuk BAI Items
    $('#addtji').click(function(){
        ++i;
        $('#tabletji').append(
            `
            <tbody>
            <tr>
                <td><textarea class="form-control" name="pertanyaan_introgasi[]" id="pertanyaan_introgasi-${i}" placeholder="Masukan pertanyaan introgasi" rows="3"></textarea></td>
                <td><textarea class="form-control" name="jawaban_introgasi[]" id="jawaban_introgasi-${i}" placeholder="Masukan jawaban introgasi" rows="3"></textarea></td>
                <td>
                    <div class="d-flex justify-content-center mx-auto">
                        <button type="button" class="btn btn-md btn-danger remove-table-row-tji mt-3" style="margin-right: 10px;"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                    </div>
                </td>
            </tr>
            </tbody>
            `);
    });

    $(document).on('click','.remove-table-row-tji', function(){
        $(this).parents('tr').remove();
    })

    // Untuk Dokumentasi Introgasi
    $('#adddiedit').click(function(){
        ++i;
        $('#tabledi').append(
            `<tr>
                <td style="max-width: 250px">
                    <input accept="image/png, image/jpeg" class="form-control foto-introgasi_${i}" type="file" name="foto_introgasi[new${i}][]" id="images${i}" data-key="${i}" multiple>
                    <div class="m-4">
                        <div class="preview-images_${i}" style="columns: 2; margin-left: -15px;"></div>
                    </div>
                </td>
                <td><textarea class="form-control" name="keterangan_introgasi[new${i}]" placeholder="Masukan Keterangan Introgasi" rows="3"></textarea></td>
                <td class="d-flex justify-content-center mx-auto">
                    <button type="button" class="btn btn-sm btn-danger remove-table-row-di"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
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

    $(document).on('click','.remove-table-row-di', function(){
        $(this).parents('tr').remove();
    })

    // Mengambil id nomor untuk modal AUTOFILL
    // function openModalSelectTemplate(id)
    // {
    //     window.localStorage.setItem('current_bai_id', id)
    // }

    // Untuk AUTOFILL Tanya Jawab Introgasi
    // $(document).ready(function(){
    //     $(document).on('click', '#select', function(){

    //         var current_bai_id = window.localStorage.getItem('current_bai_id')

    //         // var item_id = $(this).data('id');
    //         var pertanyaan_introgasi = $(this).data('pertanyaan_introgasi');
    //         var jawaban_introgasi = $(this).data('jawaban_introgasi');
    //         // $('#item_id').val(item_id);
    //         $('#pertanyaan_introgasi-'+ current_bai_id).val(pertanyaan_introgasi);
    //         $('#jawaban_introgasi-'+ current_bai_id).val(jawaban_introgasi);
    //         $('#myModal').modal('hide');
    //     })
    // })

    // Validasi Data Input
    $("#validate").submit(function() {
            // Mengambil data
            var jenis_kejadian = $("#jenis_kejadian").val();
            var nama_introgasi = $("#nama_introgasi").val();
            var umur_introgasi = $("#umur_introgasi").val();
            var pekerjaan_introgasi = $("#pekerjaan_introgasi").val();
            var bagian_introgasi = $("#bagian_introgasi").val();
            var detail_barang_kejadian = $("#detail_barang_kejadian").val();
            var tempat_kejadian = $("#tempat_kejadian").val();
            var nama_korban = $("#nama_korban").val();
            var nik_korban = $("#nik_korban").val();
            var bagian_korban = $("#bagian_korban").val();
            var nama_pelaku = $("#nama_pelaku").val();
            var umur_pelaku = $("#umur_pelaku").val();
            var ttl_pelaku = $("#ttl_pelaku").val();
            var pekerjaan_pelaku = $("#pekerjaan_pelaku").val();
            var nik_pelaku = $("#nik_pelaku").val();
            var alamat_pelaku = $("#alamat_pelaku").val();
            var agama_pelaku = $("#agama_pelaku").val();
            var suku_pelaku = $("#suku_pelaku").val();
            var status_pelaku = $("#status_pelaku").val();
            var shift = $("#shift").val();
            var pendidikan_pelaku = $("#pendidikan_pelaku").val();
            var nik_ktp_pelaku = $("#nik_ktp_pelaku").val();
            var no_hp_pelaku = $("#no_hp_pelaku").val();
            var tempat_introgasi = $("#tempat_introgasi").val();
            var keterangan_kejadian = $("#keterangan_kejadian").val();
            var pertanyaan_introgasi = $("#pertanyaan_introgasi-0").val();
            var jawaban_introgasi = $("#jawaban_introgasi-0").val();
            var formFileMultiple = $("#formFileMultiple").val();
            var keterangan_introgasi = $("#keterangan_introgasi").val();
            // var bai_oneimage = $("#bai_oneimage").val();

            // Validasi
            if(jenis_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Jenis Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nama_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nama Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(umur_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Umur Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(pekerjaan_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Pekerjaan Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(bagian_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Bagian Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(detail_barang_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Detail Barang Kejadian / Motif Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(tempat_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Tempat Kejadian Harus Diisi',
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
            }else if(bagian_korban == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Bagian Korban Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nama_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nama Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(umur_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Umur Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(ttl_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Tempat Tanggal Lahir Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(pekerjaan_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Pekerjaan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nik_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nik Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(alamat_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Alamat Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(agama_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Agama Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(suku_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Suku Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(status_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Status Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(shift == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Shift Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(pendidikan_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Pendidikan Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(nik_ktp_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nik KTP Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(no_hp_pelaku == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Nomor HP Terlapor Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(tempat_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Tempat Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(keterangan_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Keterangan Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(pertanyaan_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Pertanyaan Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(jawaban_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Jawaban Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(formFileMultiple == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Foto Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(keterangan_introgasi == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Keterangan Introgasi Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }
    })

</script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
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

    $(document).ready(function() {

        //Data Insert Code
        $('#submit-data').click(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: "{{ url('halo-security/addtemplate') }}",
                type: "post",
                dataType: "json",
                data: $('#addTag').serialize(),
                success: function(response) {
                    $('#addTag')[0].reset();
                    console.log(response);
                    $('#main').html('<div class="alert alert-success alert-dismissible alert-solid alert-label-icon shadow fade show col-ssm-12" role="alert"><i class="ri-checkbox-circle-fill label-icon"></i><strong>Success</strong> - ' + response.message + '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    table.ajax.reload();
                    $('#myModal').modal('hide')
                }
            });
        });

        // Template Data Tanya Jawab
        var table = $('#tanya').DataTable( {
            ajax: "{{ url('halo-security/getTemplates') }}",
            columns: [
                { 
                    "data": "pertanyaan_introgasi",
                        render: function(data, type, row) {
                            return `
                            <div style="white-space: normal">${row.pertanyaan_introgasi}
                            </div>
                            `;
                        }
                },
                { "data": "jawaban_introgasi", 
                render: function(data, type, row) {
                            return `
                            <div style="white-space: normal">${row.jawaban_introgasi}
                            </div>
                            `;
                        } },
                { 
                        "data": null,
                        render: function(data, type, row) {
                            return `
                            <div class="text-center">
                                <button class="btn btn-info btn-md" id="select" data-pertanyaan_introgasi="${row.pertanyaan_introgasi}" data-jawaban_introgasi="${row.jawaban_introgasi}"><i class="ri-check-line"></i> Pilih</button>
                                <button data-id="${row.id}" type="button" class="btn btn-warning btn-md" data-bs-toggle="modal" data-bs-target="#EditTemplateModal" id="edit"><i class="ri-pencil-fill"></i></i> Ubah</button>
                                <button data-id="${row.id}" type="button" class="btn btn-danger btn-md" id="delete"><i class="ri-delete-bin-2-line"></i> Hapus</button>
                            </div>
                            `;
                        }
                }
            ]
        } );

        $(document).on('shown.bs.modal', function (e) {
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        });

        // edit here
        $(document).on('click', '#edit', function() {
            $.ajax({
                url: "{{ url('halo-security/getTemplateById') }}",
                type: "post",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": $(this).data('id')
                },
                success: function(response) {
                    $('input[name="id"]').val(response.data.id);
                    $('textarea[name="edit_pertanyaan_introgasi"]').val(response.data.pertanyaan_introgasi);
                    $('textarea[name="edit_jawaban_introgasi"]').val(response.data.jawaban_introgasi);
                }
            })
        })

        // Update here
        $(document).on('click', '#update', function() {
            if(confirm('Apakah anda yakin ingin mengubah data template tanya jawab BA Introgasi ini ?')) {
                $.ajax({
                    url: '{{ url("halo-security/updateTemplate") }}',
                    type: 'post',
                    dataType: 'json',
                    data: $('#templateEdit').serialize(),
                    success: function(response) {
                        // console.log(response);
                        $('#templateEdit')[0].reset();
                        table.ajax.reload();
                        $('#EditTemplateModal').modal('hide');
                        $('#main').html('<div class="alert alert-success alert-dismissible alert-solid alert-label-icon shadow fade show col-ssm-12" role="alert"><i class="ri-pencil-fill label-icon"></i><strong>Success</strong> - ' + response.message + '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                })
            }
        })

        // delete here
        $(document).on('click', '#delete', function() {
            if(confirm('Apakah anda yakin ingin menghapus data template tanya jawab BA Introgasi ini ?')){
                $.ajax({
                    url: "{{ url('halo-security/deleteTemplateById') }}",
                    type: "post",
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": $(this).data('id')
                    },
                    success: function(response) {
                        $('#hapustemplate').html('<div class="alert alert-success alert-dismissible alert-solid alert-label-icon shadow fade show col-ssm-12" role="alert"><i class="ri-delete-bin-2-line label-icon"></i><strong>Success</strong> - ' + response.message + '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        table.ajax.reload();
                    }
                })
            }
        })
    });
</script>
@endpush