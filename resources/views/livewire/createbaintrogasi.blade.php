@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<style>
    .loading-button {
        position: relative;
        padding: 8px 17px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        }

        .loading-button:hover {
        background-color: #45CB85;
        }

        .loading-button .button-text {
        display: block;
        opacity: 1;
        transition: opacity 0.3s ease;
        }

        .loading-button.loading .button-text {
        opacity: 0;
        }

        .loading-button.loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border: 2px solid #fff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        }

        @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
        }
</style>
@endpush

<div class="container-fluid">
    <div class="card">
        <div class="card-header justify-content-between d-flex">
            <h4 class="card-title">Buat Berita Acara Introgasi</h4>
            <button class="btn btn-md btn-danger" onclick="myFunction(this)">Clear</button>
        </div>
        <div class="card-body">
            {{-- {{ route('simpan-psq') }} --}}
            {{-- <form id="simpan_draft" method="POST">
                @csrf --}}
                <form id="validate" action="{{ route('create-introgasi') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            <div class="col-md-12" style="display: none;">
                                <div class="mb-3">
                                    <label for="lk_id" class="form-label">ID Laporan Kejadian</label>
                                    <input type="text" class="form-control" onchange="changeNilai('lk_id')" name="lk_id" id="lk_id" value="{{old('lk_id')}}" placeholder="Masukan nama introgasi">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="jenis_kejadian" class="form-label">Jenis Kejadian</label>
                                    {{-- <div id="jenis_kejadian_container">
                                        <select class="form-select mb-3" onchange="changeNilai('jenis_kejadian')" name="jenis_kejadian" id="jenis_kejadian" value="{{old('jenis_kejadian')}}">
                                            <option value="">Pilih Laporan Kejadian</option>
                                            <option value="kecelakaan lalu lintas">Kecelakaan Lalu lintas</option>
                                            <option value="penemuan barang">Penemuan Barang</option>
                                            <option value="kecelakaan kerja">Kecelakaan Kerja</option>
                                            <option value="pencurian">Pencurian</option>
                                            <option value="perkelahian">Perkelahian</option>
                                            <option value="tindak kekerasan">Tindak Kekerasan</option>
                                            <option value="kebakaran">Kebakaran</option>
                                            <option value="demonstrasi">Demonstrasi</option>
                                            <option value="tindakan asusila">Tindakan Asusila</option>
                                            <option value="pengerusakan">Pengerusakan</option>
                                            <option value="tindakan indispliner">Tindakan Indispliner</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div> --}}
                                    <select class="form-select mb-3" onchange="changeNilai('jenis_kejadian')" name="jenis_kejadian" id="jenis_kejadian">
                                        <option value="">Pilih Laporan Kejadian</option>
                                        <option value="kecelakaan lalu lintas">Kecelakaan Lalu lintas</option>
                                        <option value="penemuan barang">Penemuan Barang</option>
                                        <option value="kecelakaan kerja">Kecelakaan Kerja</option>
                                        <option value="pencurian">Pencurian</option>
                                        <option value="perkelahian">Perkelahian</option>
                                        <option value="tindak kekerasan">Tindak Kekerasan</option>
                                        <option value="kebakaran">Kebakaran</option>
                                        <option value="demonstrasi">Demonstrasi</option>
                                        <option value="tindakan asusila">Tindakan Asusila</option>
                                        <option value="pengerusakan">Pengerusakan</option>
                                        <option value="tindakan indispliner">Tindakan Indispliner</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                    <div id="jenis_kejadian_container"></div>
                                    <p class="text-danger ml-2">
                                        @error('jenis_kejadian')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" onClick="openModalKejadianSelectTemplate('0')" class="btn btn-sm btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#myKejadian"><i class="ri-search-line" style="font-size: 20px;"></i></button>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama_introgasi" class="form-label">Nama Introgasi</label>
                                    <input type="text" class="form-control" onkeyup="changeNilai('nama_introgasi')" name="nama_introgasi" id="nama_introgasi" value="{{old('nama_introgasi')}}" placeholder="Masukan nama introgasi">
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
                                    <input type="text" onkeyup="changeNilai('umur_introgasi')" name="umur_introgasi" id="umur_introgasi" value="{{old('umur_introgasi')}}" class="form-control" placeholder="Masukan umur introgasi">
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
                                    <input type="text" onkeyup="changeNilai('pekerjaan_introgasi')" name="pekerjaan_introgasi" id="pekerjaan_introgasi" value="{{old('pekerjaan_introgasi')}}" class="form-control" placeholder="Masukan perusahaan">
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
                                    <input type="text" onkeyup="changeNilai('bagian_introgasi')" name="bagian_introgasi" id="bagian_introgasi" value="{{old('bagian_introgasi')}}" class="form-control" placeholder="Masukan bagian introgasi">
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
                                    <input type="text" onchange="changeNilai('nama_pelapor')" name="nama_pelapor" id="nama_pelapor" value="{{old('nama_pelapor')}}" class="form-control" placeholder="Masukan nama pelapor">
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
                                    <input type="text" onchange="changeNilai('detail_barang_kejadian')" name="detail_barang_kejadian" id="detail_barang_kejadian" value="{{old('detail_barang_kejadian')}}" class="form-control" placeholder="Masukan jenis kejadian dan barang">
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
                                    <input type="text" onchange="changeNilai('tempat_kejadian')" name="tempat_kejadian" id="tempat_kejadian" value="{{old('tempat_kejadian')}}" class="form-control" placeholder="Masukan tempat kejadian">
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
                                    <input type="text" onchange="changeNilai('nama_korban')" name="nama_korban" id="nama_korban" value="{{old('nama_korban')}}" class="form-control" placeholder="Masukan nama korban">
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
                                    <input type="text" onchange="changeNilai('nik_korban')" name="nik_korban" id="nik_korban" value="{{old('nik_korban')}}" class="form-control" placeholder="Masukan nik (nomor induk karyawan) korban">
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
                                    <input type="text" onchange="changeNilai('bagian_korban')" name="bagian_korban" id="bagian_korban" value="{{old('bagian_korban')}}" class="form-control" placeholder="Masukan bagian korban">
                                    <p class="text-danger ml-2">
                                        @error('bagian_korban')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama_pelaku" class="form-label">Nama Terlapor</label>
                                    <input type="text" onchange="changeNilai('nama_pelaku')" name="nama_pelaku" id="nama_pelaku" value="{{old('nama_pelaku')}}" class="form-control" placeholder="Masukan nama terlapor">
                                    <p class="text-danger ml-2">
                                        @error('nama_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="umur_pelaku" class="form-label">Umur Terlapor</label>
                                    <input type="text" onchange="changeNilai('umur_pelaku')" name="umur_pelaku" id="umur_pelaku" value="{{old('umur_pelaku')}}" class="form-control" placeholder="Masukan umur terlapor">
                                    <p class="text-danger ml-2">
                                        @error('umur_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ttl_pelaku" class="form-label">Tempat Tanggal Lahir Terlapor</label>
                                    <input type="text" onchange="changeNilai('ttl_pelaku')" name="ttl_pelaku" id="ttl_pelaku" value="{{old('ttl_pelaku')}}" class="form-control" placeholder="Masukan tempat tanggal lahir terlapor">
                                    <p class="text-danger ml-2">
                                        @error('ttl_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="pekerjaan_pelaku" class="form-label">Pekerjaan Terlapor</label>
                                    <input type="text" onchange="changeNilai('pekerjaan_pelaku')" name="pekerjaan_pelaku" id="pekerjaan_pelaku" value="{{old('pekerjaan_pelaku')}}" class="form-control" placeholder="Masukan pekerjaan terlapor">
                                    <p class="text-danger ml-2">
                                        @error('pekerjaan_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nik_pelaku" class="form-label">Nik Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('nik_pelaku')" name="nik_pelaku" id="nik_pelaku" value="{{old('nik_pelaku')}}" class="form-control" placeholder="Masukan nik pelaku">
                                    <p class="text-danger ml-2">
                                        @error('nik_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="bagian_pelaku" class="form-label">Bagian Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('bagian_pelaku')" name="bagian_pelaku" id="bagian_pelaku" value="{{old('bagian_pelaku')}}" class="form-control" placeholder="Masukan bagian pelaku">
                                    <p class="text-danger ml-2">
                                        @error('bagian_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="alamat_pelaku" class="form-label">Alamat Terlapor</label>
                                    <textarea class="form-control" onchange="changeNilai('alamat_pelaku')" name="alamat_pelaku" id="alamat_pelaku" value="{{old('alamat_pelaku')}}" placeholder="Masukan alamat pelaku" rows="3"></textarea>
                                    <p class="text-danger ml-2">
                                        @error('alamat_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="agama_pelaku" class="form-label">Agama Terlapor</label>
                                    <input type="text" onchange="changeNilai('agama_pelaku')" name="agama_pelaku" id="agama_pelaku" value="{{old('agama_pelaku')}}" class="form-control" placeholder="Masukan agama pelaku">
                                    <p class="text-danger ml-2">
                                        @error('agama_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="suku_pelaku" class="form-label">Suku Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('suku_pelaku')" name="suku_pelaku" id="suku_pelaku" value="{{old('suku_pelaku')}}" class="form-control" placeholder="Masukan suku pelaku">
                                    <p class="text-danger ml-2">
                                        @error('suku_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="status_pelaku" class="form-label">Status Terlapor</label>
                                    <select class="form-select mb-3" onchange="changeNilai('status_pelaku')" value="{{old('status_pelaku')}}" name="status_pelaku" id="status_pelaku">
                                        <option value="">Pilih Status Terlapor</option>
                                        <option value="belum kawin">Belum Kawin</option>
                                        <option value="sudah kawin">Sudah Kawin</option>
                                        <option value="janda/duda">Janda/Duda</option>
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
                                    <select class="form-select mb-3" onchange="changeNilai('shift')" value="{{old('shift')}}" name="shift" id="shift">
                                        <option value="">Pilih Shift</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
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
                                    <label for="pendidikan_pelaku" class="form-label">Pendidikan Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('pendidikan_pelaku')" name="pendidikan_pelaku" id="pendidikan_pelaku" value="{{old('pendidikan_pelaku')}}" class="form-control" placeholder="Masukan pendidikan pelaku">
                                    <p class="text-danger ml-2">
                                        @error('pendidikan_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nik_ktp_pelaku" class="form-label">Nik KTP Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('nik_ktp_pelaku')" name="nik_ktp_pelaku" id="nik_ktp_pelaku" value="{{old('nik_ktp_pelaku')}}" class="form-control" placeholder="Masukan nik ktp pelaku">
                                    <p class="text-danger ml-2">
                                        @error('nik_ktp_pelaku')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_hp_pelaku" class="form-label">Nomor HP Terlapor</label>
                                    <input type="text" onkeyup="changeNilai('no_hp_pelaku')" name="no_hp_pelaku" id="no_hp_pelaku" value="{{old('no_hp_pelaku')}}" class="form-control" placeholder="Masukan nomor hp pelaku">
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
                                    <input type="text" onkeyup="changeNilai('tempat_introgasi')" name="tempat_introgasi" id="tempat_introgasi" value="{{old('tempat_introgasi')}}" class="form-control" placeholder="Masukan tempat introgasi">
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
                                    <input type="text" onchange="changeNilai('keterangan_kejadian')" name="keterangan_kejadian" id="keterangan_kejadian" value="{{old('keterangan_kejadian')}}" class="form-control" placeholder="Masukan keterangan kejadian">
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
                                        <tr>
                                            <th>Pertanyaan Introgasi</th>
                                            <th>Jawaban Introgasi</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        <tr>
                                            {{-- <input type="hidden" name="item_id" id="item_id"> --}}
                                            <td>
                                                <div id="passwordHelpBlock" class="form-text mb-2" style="margin-top: -2px;">
                                                    1. Ditanyakan kepada Sdr. <span class='nama__'>Nama</span> (Tidak perlu ditulis ulang)
                                                </div>
                                                <textarea class="form-control" onchange="changeNilai('pertanyaan_introgasi-0')" name="pertanyaan_introgasi[]" id="pertanyaan_introgasi-0" placeholder="Masukan pertanyaan introgasi" rows="3"></textarea>
                                            </td>
                                            <td><textarea class="form-control mt-4" onchange="changeNilai('jawaban_introgasi-0')" name="jawaban_introgasi[]" id="jawaban_introgasi-0" placeholder="Masukan jawaban introgasi" rows="3"></textarea></td>
                                            <td class="d-flex justify-content-center mx-auto" style="margin-top: 20px;">
                                                <button type="button" name="addtji" id="addtji" class="btn btn-sm btn-success mt-4" style="margin-right: 10px;"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                                <button type="button" onClick="openModalSelectTemplate('0')" class="btn btn-sm btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-search-line" style="font-size: 20px;"></i></button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="foto_introgasi" class="form-label">Dokumentasi Introgasi</label>
                                    <table class="table table-bordered" id="tabledi">
                                        <tr>
                                            <th>Foto Introgasi</th>
                                            <th>Keterangan Introgasi</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        <tr>
                                            <td style="max-width: 250px">
                                                <input accept="image/png, image/jpeg" onchange="previewGambar('foto_introgasi-0')" class="form-control foto-introgasi_0" type="file" data-key="0" id="foto_introgasi-0" multiple>
                                                <div class="mt-4">
                                                    <div class="row gambar-bai" id="gambar-preview-0">
                                                        {{-- <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 400px; max-height: 300px;"> --}}
                                                    </div>
                                                    <div class="textarea_image_container" id="gambar_value-0" style="display: none;"></div>
                                                    {{-- <div class="preview-images_0" style="columns: 2;">
                                                    </div> --}}
                                                </div>
                                            </td>
                                            <td><textarea class="form-control" onkeyup="changeNilai('keterangan_introgasi-0')" name="keterangan_introgasi[]" id="keterangan_introgasi-0" placeholder="Masukan keterangan introgasi" rows="3" required></textarea></td>
                                            <td class="d-flex justify-content-center mx-auto"><button type="button" name="adddi" id="adddi" class="btn btn-sm btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="bai_oneimage" class="form-label">Foto Dokumentasi Kejadian</label>
                                    <input type="file" id="bai_oneimage" onchange="previewImage('bai_oneimage')" accept="image/jpeg" class="form-control mb-4">
                                    <div class="d-flex justify-content-center image-bai" id="image-preview">
                                        {{-- <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 400px; max-height: 300px;"> --}}
                                    </div>
                                    <div class="textarea_image_container" id="image_value"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="foto_introgasi" class="form-label">Foto Dokumentasi Proses BAI</label>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="text-center">Kamera</th>
                                            <th class="text-center">Hasil</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div id="my_camera" style="margin: auto;"></div>
                                                <div class="text-center mt-2">
                                                    <input type="button" onchange="previewKamera('image')" id="image" accept="image/jpeg" value="Ambil Foto" class="btn btn-sm btn-secondary" onClick="take_snapshot()">
                                                    <input type="hidden" name="image" id="image" class="image-tag">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div id="results">Foto yang anda ambil akan muncul di sini...</div>
                                                <div class="d-flex justify-content-center image-bai" id="foto-preview">
                                                </div>
                                                <div class="textarea_image_container" id="foto_value"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-4 mb-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <span class="button-text"><i class="ri-save-3-line align-middle mr-2"></i> Simpan</span>
                                    </button>
                                    <button class="loading-button" style="margin-left: 20px;" type="button" id="validatePreview">
                                        <span class="button-text"><i class="ri-eye-line align-middle mr-2"></i> Preview</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            {{-- </form> --}}
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
                                <table id="tanya" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th class="text-center" style="width: 10px !important;">Action</th>
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

<!-- Default Modals -->
<div id="myKejadian" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Template Berita Acara Laporan Kejadian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="product-content">
                    <nav>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success" id="nav-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="nav-speci-tab" data-bs-toggle="tab" href="#nav-speci" role="tab" aria-controls="nav-speci" aria-selected="true">Data Template Berita Acara Laporan Kejadian</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="nav-speci" role="tabpanel" aria-labelledby="nav-speci-tab">
                            <div class="table-responsive">
                                <table id="kejadian" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID Laporan Kejadian</th>
                                            <th>Jenis Kejadian</th>
                                            <th>Nama Korban</th>
                                            <th class="text-center" style="width: 10px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
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

{{-- Tambah Data Template BAI Items --}}
<div id="AddTemplateModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addTag">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Tambah Data Template BA Items Introgasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                        <ul id="saveform_errList"></ul>
                        <div>
                            <label for="pertanyaan_introgasi" class="form-label">Pertanyaan</label>
                            <textarea class="form-control pertanyaan_introgasi" id="pertanyaan_introgasi" name="pertanyaan_introgasi" rows="3"></textarea>
                            <p class="text-danger ml-2">
                                @error('pertanyaan_introgasi')
                                    {{ $message }}
                                @enderror
                            </p>
                        </div>
                        <div>
                            <label for="jawaban_introgasi" class="form-label">Jawaban</label>
                            <textarea class="form-control jawaban_introgasi" id="jawaban_introgasi" name="jawaban_introgasi" rows="3"></textarea>
                            <small id="jawaban_introgasi_help" class="text-danger"></small>
                        </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" style="margin-right: 10px;" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" id="submit-data" class="btn btn-primary add_template">Simpan</button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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

@push('scripts')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="{{ url('/assets/plugins/custom/webcam/webcam.min.js') }}"></script>

<script>
    // $.ajaxSetup({
	// 	headers: {
	// 	    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
	// 	}
    // });

        var database = window.indexedDB.open("halo_security", 20);

        // Create object store
        database.onupgradeneeded = function(event) {
            let db = event.target.result;
            // Delete old object store
            if (db.objectStoreNames.contains("baintrogasi")) {
                db.deleteObjectStore("baintrogasi");
                console.log('Delete because upgrade needed')
            }

            let objectStore = db.createObjectStore("baintrogasi", { keyPath: "id" });
        };

        // database.onsuccess = function(event) {
            // let db = event.target.result;
            // console.log('testing.......')
            // if (!db.objectStoreNames.contains("baintrogasi")) {
            //     let objectStore = db.createObjectStore("baintrogasi", { keyPath: "id" });
            // }
        // }

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
    
    // function previewImage(input) {

    //     if (input.files && input.files[0]) {
    //         var reader = new FileReader();

    //         reader.onload = function(e) {
    //             $('#image-preview').attr('src', e.target.result).show();
    //         }

    //         reader.readAsDataURL(input.files[0]);
    //     }
    // }
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
        // if($(this).val() == 'lainnya') {
        //     $('#jenis_kejadian_container').html('')
        //     $('#jenis_kejadian_container').html(`
        //         <input class="form-control mb-3" name="jenis_kejadian" id="jenis_kejadian" value="{{old('jenis_kejadian')}}" placeholder="Jenis Kejadian lainnya" />
        //     `);
        // }
        
        if ($(this).val() == 'lainnya') {
            $('#jenis_kejadian_container').html(`
                <input class="form-control mb-3" onchange="changeNilai('jenis_kejadian_lainnya')" name="jenis_kejadian_lainnya" id="jenis_kejadian_lainnya" placeholder="Jenis Kejadian lainnya" />
            `);
        } else {
            $('#jenis_kejadian_container').html('');
        }
    });

    var i = 0;
    var i_dokumentasi = 0;

    // Untuk BAI Items
    $('#addtji').click(function(){
        ++i;
        $('#tabletji').append(
            `<tr>
                <td>
                    <div id="passwordHelpBlock" class="form-text mb-2" style="margin-top: -2px;">
                        ${i+1}. Ditanyakan kepada Sdr. <span class='nama__'>Nama</span> (Tidak perlu ditulis ulang)
                    </div>
                    <textarea class="form-control" onchange="changeQuestion('${i}')" name="pertanyaan_introgasi[]" id="pertanyaan_introgasi-${i}" placeholder="Masukan pertanyaan introgasi" rows="3"></textarea>
                </td>
                <td><textarea class="form-control mt-4" onchange="changeAnswer('${i}')" name="jawaban_introgasi[]" id="jawaban_introgasi-${i}" placeholder="Masukan jawaban introgasi" rows="3"></textarea></td>
                <td class="d-flex justify-content-center mx-auto">
                    <button type="button" data-id_qna="${i}" class="btn btn-sm btn-danger remove-table-row-tji mt-5" style="margin-right: 10px;"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                    <button type="button" class="btn btn-sm btn-primary mt-5" onClick="openModalSelectTemplate('${i}')" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-search-line" style="font-size: 20px;"></i></button>
                </td>

            </tr>`);

            // Untuk mengambil data nama pelaku yang di isi di form input dengan memasukan ke class nama__
            setTimeout(function () {
            var nama_pelaku__ = document.getElementById('nama_pelaku').value;
            $('.nama__').text(nama_pelaku__)
        }, 2000)

        var database = window.indexedDB.open("halo_security", 20);
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("baintrogasi", "readwrite");
            let objectStore = transaction.objectStore("baintrogasi");

            let request = objectStore.get('question_answer');

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    var _nilai = data.nilai;
                    _nilai.push({
                        id: i,
                        question: '',
                        answer:''
                    })
                    data.nilai = _nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    var nilai = [];
                    nilai.push({
                        id: i,
                        question: '',
                        answer:''
                    })
                    objectStore.put({ id: 'question_answer', nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    });

    var database = window.indexedDB.open("halo_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'question_answer';
        var db = event.target.result;
        var transaction = db.transaction("baintrogasi", "readonly");
        var objectStore = transaction.objectStore("baintrogasi");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                data.nilai.forEach((item, key) => {
                    i = key+1
                    $('#tabletji').append(
                    `<tr>
                        <td>
                            <div id="passwordHelpBlock" class="form-text mb-2" style="margin-top: -2px;">
                                ${key+2}. Ditanyakan kepada Sdr. <span class='nama__'>Nama</span> (Tidak perlu ditulis ulang)
                            </div>
                            <textarea onchange="changeQuestion('${item.id}')" class="form-control" name="pertanyaan_introgasi[]" id="pertanyaan_introgasi-${item.id}" placeholder="Masukan pertanyaan introgasi" rows="3">${item.question}</textarea>
                        </td>
                        <td><textarea onchange="changeAnswer('${item.id}')" class="form-control mt-4" name="jawaban_introgasi[]" id="jawaban_introgasi-${item.id}" placeholder="Masukan jawaban introgasi" rows="3">${item.answer}</textarea></td>
                        <td class="d-flex justify-content-center mx-auto">
                            <button type="button" data-id_qna="${item.id}" class="btn btn-sm btn-danger remove-table-row-tji mt-5" style="margin-right: 10px;"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                            <button type="button" class="btn btn-sm btn-primary mt-5" onClick="openModalSelectTemplate('${item.id}')" data-bs-toggle="modal" data-bs-target="#myModal"><i class="ri-search-line" style="font-size: 20px;"></i></button>
                        </td>

                    </tr>`);

                    setTimeout(() => {
                        var nama_pelaku__ = document.getElementById('nama_pelaku').value;
                        $('.nama__').text(nama_pelaku__)
                    }, 2000);
                })
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    function changeQuestion(id_question){
        var question = $('#pertanyaan_introgasi-'+id_question).val()

        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'question_answer';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readwrite");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_question) {
                            item.question = question
                        }
                    })

                    objectStore.put(data)
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };
    }

    function changeAnswer(id_question){
        var answer = $('#jawaban_introgasi-'+id_question).val()

        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'question_answer';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readwrite");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_question) {
                            item.answer = answer
                        }
                    })

                    objectStore.put(data)
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };
    }

    $(document).on('click','.remove-table-row-tji', function(){

        var id_qna = $(this).data('id_qna')

        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'question_answer';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readwrite");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    var _nilai = data.nilai.filter(item => {
                        if(item.id != id_qna) {
                            return item
                        }
                    })

                    data.nilai = _nilai

                    objectStore.put(data)
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };

        $(this).parents('tr').remove();
    })

    // Untuk Dokumentasi introgasi
    $('#adddi').click(function(){
        ++i_dokumentasi;
        $('#tabledi').append(
            `<tr>
                <td style="max-width: 250px">
                    <input accept="image/png, image/jpeg" onchange="previewIntrogasi('${i_dokumentasi}')" class="form-control foto-introgasi_${i_dokumentasi}" type="file" data-key="${i_dokumentasi}" name="foto_introgasi[${i_dokumentasi}][]" id="foto_introgasi-${i_dokumentasi}" multiple>
                    <div class="mt-4">
                        <div class="row justify-content-center gambar-bai" id="gambar-preview-${i_dokumentasi}"></div>
                        <div class="textarea_image_container" id="gambar_value-${i_dokumentasi}" style="display: none;"></div>
                    </div>
                </td>
                <td><textarea class="form-control" onKeyup="changeKeterangan('${i_dokumentasi}')" name="keterangan_introgasi[]" id="keterangan_introgasi-${i_dokumentasi}" placeholder="Masukan keterangan introgasi" rows="3" required></textarea></td>
                <td class="d-flex justify-content-center mx-auto">
                    <button type="button" data-id_doc="${i_dokumentasi}" class="btn btn-sm btn-danger remove-table-row-di mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                </td>

            </tr>`);

            var database = window.indexedDB.open("halo_security", 20);
            
            // Save data to indexeddb
            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("baintrogasi", "readwrite");
                let objectStore = transaction.objectStore("baintrogasi");

                let request = objectStore.get('dokumentasi_introgasi');

                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var _isi = data.nilai;
                        _isi.push({
                            id: i_dokumentasi,
                            foto: [],
                            keterangan:''
                        })
                        data.nilai = _isi;
                        objectStore.put(data);
                        console.log("Data berhasil diubah");
                    } else {
                        var nilai = [];
                        nilai.push({
                            id: i_dokumentasi,
                            foto: [],
                            keterangan:''
                        })
                        objectStore.put({ id: 'dokumentasi_introgasi', nilai: nilai });
                        console.log("Data berhasil dibuat");
                    }

                };

                request.onerror = function(event) {
                    console.log("Data gagal disimpan");
                };
            };
    });

    var database = window.indexedDB.open("halo_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'dokumentasi_introgasi';
        var db = event.target.result;
        var transaction = db.transaction("baintrogasi", "readonly");
        var objectStore = transaction.objectStore("baintrogasi");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                data.nilai.forEach((item) => {
                    i_dokumentasi = parseInt(item.id)
                    $('#tabledi').append(
                        `<tr>
                            <td style="max-width: 250px">
                                <input accept="image/png, image/jpeg" onchange="previewIntrogasi('${item.id}')" class="form-control foto-introgasi_${item.id}" type="file" data-key="${item.id}" name="foto_introgasi[${item.id}][]" id="foto_introgasi-${item.id}" multiple>
                                <div class="mt-4">
                                    <div class="row gambar-bai" id="gambar-preview-${item.id}"></div>
                                    <div class="textarea_image_container" id="gambar_value-${item.id}" style="display: none;"></div>
                                </div>
                            </td>
                            <td><textarea class="form-control" onKeyup="changeKeterangan('${item.id}')" name="keterangan_introgasi[]" id="keterangan_introgasi-${item.id}" placeholder="Masukan keterangan introgasi" rows="3">${item.keterangan}</textarea></td>
                            <td class="d-flex justify-content-center mx-auto">
                                <button type="button" data-id_doc="${item.id}" class="btn btn-sm btn-danger remove-table-row-di mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                            </td>

                    </tr>`);

                    item.foto.forEach(fotoItem => {
                        let image = document.createElement('img');
                        image.src = fotoItem;
                        image.style.width = '100%';
                        image.style.height = '100%';
                        image.style.objectFit = 'contain';

                        var imageInner = document.createElement('div');
                        imageInner.style.marginBottom = '2px';
                        // Position relative
                        imageInner.style.position = 'relative';
                        imageInner.classList.add('col-6')
                        imageInner.appendChild(image);

                        // Create delete button
                        var deleteButton = document.createElement('button');
                        deleteButton.style.position = 'absolute';
                        deleteButton.style.top = '0';
                        deleteButton.style.right = '0';
                        deleteButton.style.margin = '2px';
                        deleteButton.style.padding = '2px 5px';
                        deleteButton.style.fontSize = '10px';
                        deleteButton.style.cursor = 'pointer';
                        deleteButton.innerHTML = 'Hapus';
                        deleteButton.type = 'button';
                        // Add css class
                        deleteButton.classList.add('btn');
                        deleteButton.classList.add('btn-xs');
                        deleteButton.classList.add('btn-danger');
                        // Add onclick event
                        deleteButton.onclick = function () {

                            var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                            if (!confirm) return;

                            // Delete image from indexeddb
                            var database = window.indexedDB.open("halo_security", 20);

                            // Save data to indexeddb
                            database.onsuccess = function(event) {
                                let db = event.target.result;
                                let transaction = db.transaction("baintrogasi", "readwrite");
                                let objectStore = transaction.objectStore("baintrogasi");
                                // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                // Change only image
                                let request = objectStore.get('dokumentasi_introgasi');
                                request.onsuccess = function(event) {
                                    let data = event.target.result;
                                    if (data) {
                                        data.nilai.map((__item) => {
                                            if(__item.id == item.id) {
                                                var foto = __item.foto;
                                                if (foto == null) {
                                                    foto = [];
                                                }

                                                var index = foto.indexOf(fotoItem);
                                                if (index > -1) {
                                                    foto.splice(index, 1);
                                                }

                                                __item.foto = foto;
                                            }

                                            return __item;
                                        })

                                        objectStore.put(data);
                                    }
                                };

                                request.onerror = function(event) {
                                    console.log("Error retrieving data:", event.target.error);
                                };
                            };

                            $('#gambar_foto_'+item.id).remove();

                            // Delete image from DOM
                            this.parentElement.remove();
                        }
                        imageInner.appendChild(deleteButton);

                        document.getElementById('gambar-preview-'+item.id).appendChild(imageInner);

                        // var textareaImage = document.createElement('input');
                        // textareaImage.setAttribute("type", "hidden");
                        // textareaImage.name = 'foto_introgasi['+ item.id +'][]';
                        // textareaImage.setAttribute("value", fotoItem);
                        var textareaImage = document.createElement('textarea');
                        textareaImage.name = 'foto_introgasi['+ item.id +'][]';
                        textareaImage.id = 'gambar_foto_'+item.id;
                        textareaImage.value = fotoItem;

                        document.getElementById('gambar_value-'+item.id).appendChild(textareaImage);
                    })
                })
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    function changeKeterangan(id_keterangan){
        var keterangan = $('#keterangan_introgasi-'+id_keterangan).val()

        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'dokumentasi_introgasi';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readwrite");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_keterangan) {
                            item.keterangan = keterangan
                        }
                    })

                    objectStore.put(data)
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };
    }

    // Untuk menampilkan gambar Foto Introgasi
    var database = window.indexedDB.open("halo_security", 20);

    const compressGambar = async (file, { quality = 1, type = file.type }) => {
        // Get as image data
        const imageBitmap = await createImageBitmap(file);

        // Resize image to width 100px
        const canvas = document.createElement('canvas');
        canvas.width = 450;
        canvas.height = imageBitmap.height * (450 / imageBitmap.width);
        const ctx = canvas.getContext('2d');

        ctx.drawImage(imageBitmap, 0, 0, canvas.width, canvas.height);

        // Turn into Blob
        const blob = await new Promise((resolve) =>
            canvas.toBlob(resolve, type, quality)
        );

        // Turn Blob into File
        return new File([blob], file.name, {
            type: blob.type,
        });
    };

    // Untuk Foto Introgasi
    const previewIntrogasi = async (id_keterangan) =>
    {
        var files = document.getElementById('foto_introgasi-'+id_keterangan).files;

        // No files selected
        if (!files.length) return;

        // We'll store the files in this data transfer object
        const dataTransfer = new DataTransfer();

        // console.log(files)
        // return

        // let files = document.getElementById('foto_introgasi-'+id_keterangan).files;
        Array.from(files).forEach(async file => {
            if (!file.type.startsWith('image')) {
                // Ignore this file, but do add it to our result
                dataTransfer.items.add(file);
                return;
            }

            // We compress the file by 50%
            const compressedFile = await compressGambar(file, {
                quality: 0.4,
                type: 'image/jpeg',
            });

            // Save back the compressed file instead of the original file
            dataTransfer.items.add(compressedFile);

            file = dataTransfer.files[0];
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                let base64 = reader.result;
                let image = document.createElement('img');
                image.src = base64;
                image.style.width = '100%';
                image.style.height = '100%';
                image.style.objectFit = 'contain';

                var imageInner = document.createElement('div');
                imageInner.style.marginBottom = '2px';
                // Position relative
                imageInner.style.position = 'relative';
                imageInner.classList.add('col-6')
                imageInner.appendChild(image);

                // Create delete button
                var deleteButton = document.createElement('button');
                deleteButton.style.position = 'absolute';
                deleteButton.style.top = '0';
                deleteButton.style.right = '0';
                deleteButton.style.margin = '2px';
                deleteButton.style.padding = '2px 5px';
                deleteButton.style.fontSize = '10px';
                deleteButton.style.cursor = 'pointer';
                deleteButton.innerHTML = 'Hapus';
                deleteButton.type = 'button';
                // Add css class
                deleteButton.classList.add('btn');
                deleteButton.classList.add('btn-xs');
                deleteButton.classList.add('btn-danger');
                // Add onclick event
                deleteButton.onclick = function () {

                    var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                    if (!confirm) return;

                    // Delete image from indexeddb
                    var database = window.indexedDB.open("halo_security", 20);

                    // Save data to indexeddb
                    database.onsuccess = function(event) {
                        let db = event.target.result;
                        let transaction = db.transaction("baintrogasi", "readwrite");
                        let objectStore = transaction.objectStore("baintrogasi");
                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                        // Change only image
                        let request = objectStore.get(id_keterangan);
                        request.onsuccess = function(event) {
                            let data = event.target.result;
                            // if (data) {
                            //     var images = data.images;
                            //     if (images == null) {
                            //         images = [];
                            //     }

                            //     var index = images.indexOf(base64);
                            //     if (index > -1) {
                            //         images.splice(index, 1);
                            //     }

                            //     data.images = images;
                            //     objectStore.put(data);
                            // }

                            if (data) {
                                var images = data.images;
                                if (images == null) {
                                    images = [];
                                }

                                images.push(base64);

                                data.images = images;
                                objectStore.put(data);
                            } else {
                                objectStore.put({ id: id_keterangan, images: [base64] });
                            }
                        };

                        request.onerror = function(event) {
                            console.log("Error retrieving data:", event.target.error);
                        };
                    };

                    $('#gambar_foto_'+id_keterangan).remove();

                    // Delete image from DOM
                    this.parentElement.remove();
                }
                imageInner.appendChild(deleteButton);

                document.getElementById('gambar-preview-'+id_keterangan).appendChild(imageInner);

                // var textareaImage = document.createElement('input');
                // textareaImage.setAttribute("type", "hidden");
                // textareaImage.name = 'foto_introgasi['+ id_keterangan +'][]';
                // textareaImage.setAttribute("value", base64);

                var textareaImage = document.createElement('textarea');
                textareaImage.name = 'foto_introgasi['+ id_keterangan +'][]';
                textareaImage.id = 'gambar_foto_'+id_keterangan;
                textareaImage.value = base64;

                document.getElementById('gambar_value-'+id_keterangan).appendChild(textareaImage);

                // Clear input file
                document.getElementById('foto_introgasi-'+id_keterangan).value = '';

                // Save image to indexeddb
                var database = window.indexedDB.open("halo_security", 20);

                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("baintrogasi", "readwrite");
                    let objectStore = transaction.objectStore("baintrogasi");
                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                    // Change only image
                    let request = objectStore.get('dokumentasi_introgasi');
                    request.onsuccess = function(event) {
                        let data = event.target.result;
                        if (data) {
                            var _isi = data.nilai;
                            // _isi.push({
                            //     id: i_dokumentasi,
                            //     foto: [],
                            //     keterangan:''
                            // })
                            // data.nilai = _isi;
                            // objectStore.put(data);
                            data.nilai.map((item) => {
                                if(item.id == id_keterangan) {
                                    var _foto = item.foto;
                                    _foto.push(base64) 
                                    item.foto = _foto;
                                }
                            })

                            objectStore.put(data)
                            console.log("Data berhasil diubah");
                        } else {
                            // var nilai = [];
                            // nilai.push({
                            //     id: i_dokumentasi,
                            //     foto: [],
                            //     keterangan:''
                            // })
                            // objectStore.put({ id: 'dokumentasi_introgasi', nilai: nilai });
                            // console.log("Data berhasil dibuat");
                        }
                    };

                    request.onerror = function(event) {
                        console.log("Error retrieving data:", event.target.error);
                    };
                };
                
            };
        })
    }

    $(document).on('click','.remove-table-row-di', function(){
        var id_doc = $(this).data('id_doc')

        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'dokumentasi_introgasi';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readwrite");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    var _isi = data.nilai.filter(item => {
                        if(item.id != id_doc) {
                            return item
                        }
                    })

                    data.nilai = _isi

                    objectStore.put(data)
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };

        $(this).parents('tr').remove();
    })

    // Mengambil id nomor untuk modal AUTOFILL
    function openModalSelectTemplate(id)
    {
        window.localStorage.setItem('current_bai_id', id)
    }

    function openModalKejadianSelectTemplate(id)
    {
        window.localStorage.setItem('kejadian_bai_id', id)
    }

    // Untuk AUTOFILL Tanya Jawab Introgasi
    $(document).ready(function(){
        $(document).on('click', '#select', function(){
            var current_bai_id = window.localStorage.getItem('current_bai_id')

            var pertanyaan_introgasi = $(this).data('pertanyaan_introgasi');
            var jawaban_introgasi = $(this).data('jawaban_introgasi');
            
            $('#pertanyaan_introgasi-'+ current_bai_id).val(pertanyaan_introgasi);
            $('#jawaban_introgasi-'+ current_bai_id).val(jawaban_introgasi);
            $('#myModal').modal('hide');

            if(current_bai_id == 0) {
                Swal.fire({            
                    icon: 'success',                   
                    title: 'Berhasil',    
                    text: 'Template Tanya Jawab Introgasi Berhasil Digunakan',                        
                    timer: 2000,                                
                    showConfirmButton: false
                })

                setTimeout(function () {
                    changeNilai('pertanyaan_introgasi-0')
                    changeNilai('jawaban_introgasi-0')
                }, 100)
            }else{
                Swal.fire({            
                    icon: 'success',                   
                    title: 'Berhasil',    
                    text: 'Template Tanya Jawab Introgasi Berhasil Digunakan',                        
                    timer: 2000,                                
                    showConfirmButton: false
                })

                setTimeout(function() {
                    changeQuestion(current_bai_id);
                    changeAnswer(current_bai_id);
                }, 100)
            }

            
        })
    })

    // Untuk AUTOFILL Tanya Jawab Introgasi
    $(document).ready(function(){
        $(document).on('click', '#pilih', function(){
            var kejadian_bai_id = window.localStorage.getItem('kejadian_bai_id')

            var lk_id = $(this).data('lk_id');
            var jenis_kejadian = $(this).data('jenis_kejadian');
            var nama_korban = $(this).data('nama_korban');
            var nik_korban = $(this).data('nik_korban');
            var bagian_korban = $(this).data('bagian_korban');
            var nama_pelaku = $(this).data('nama_pelaku');
            var umur_pelaku = $(this).data('umur_pelaku');
            var ttl_pelaku = $(this).data('ttl_pelaku');
            var pekerjaan_pelaku = $(this).data('pekerjaan_pelaku');
            var alamat_pelaku = $(this).data('alamat_pelaku');
            var status_pelaku = $(this).data('status_pelaku');
            var agama_pelaku = $(this).data('agama_pelaku');
            var detail_barang_kejadian = $(this).data('detail_barang_kejadian');
            var tempat_kejadian = $(this).data('tempat_kejadian');
            var keterangan_kejadian = $(this).data('keterangan_kejadian');
            var nama_pelapor = $(this).data('nama_pelapor');

            $('#lk_id').val(lk_id);
            $('#jenis_kejadian').val(jenis_kejadian);
            $('#nama_korban').val(nama_korban);
            $('#nik_korban').val(nik_korban);
            $('#bagian_korban').val(bagian_korban);
            $('#nama_pelaku').val(nama_pelaku);
            $('#umur_pelaku').val(umur_pelaku);
            $('#ttl_pelaku').val(ttl_pelaku);
            $('#pekerjaan_pelaku').val(pekerjaan_pelaku);
            $('#alamat_pelaku').val(alamat_pelaku);
            $('#status_pelaku').val(status_pelaku);
            $('#agama_pelaku').val(agama_pelaku);
            $('#detail_barang_kejadian').val(detail_barang_kejadian);
            $('#tempat_kejadian').val(tempat_kejadian);
            $('#keterangan_kejadian').val(keterangan_kejadian);
            $('#nama_pelapor').val(nama_pelapor);
            $('#myKejadian').modal('hide');

            if(kejadian_bai_id == 0) {
                Swal.fire({            
                    icon: 'success',                   
                    title: 'Berhasil',    
                    text: 'Laporan Kejadian Berhasil Digunakan',                        
                    timer: 2000,                                
                    showConfirmButton: false
                })

                setTimeout(function () {
                    changeNilai('lk_id')
                    changeNilai('jenis_kejadian')
                    changeNilai('nama_korban')
                    changeNilai('nik_korban')
                    changeNilai('bagian_korban')
                    changeNilai('nama_pelaku')
                    changeNilai('umur_pelaku')
                    changeNilai('ttl_pelaku')
                    changeNilai('pekerjaan_pelaku')
                    changeNilai('alamat_pelaku')
                    changeNilai('status_pelaku')
                    changeNilai('agama_pelaku')
                    changeNilai('detail_barang_kejadian')
                    changeNilai('tempat_kejadian')
                    changeNilai('keterangan_kejadian')
                    changeNilai('nama_pelapor')
                }, 100)
            }

        })
    })

    // Validasi Data Input
    $("#validate").submit(function() {
            // Mengambil data
            var jenis_kejadian = $("#jenis_kejadian").val();
            var jenis_kejadian_lainnya = $("#jenis_kejadian_lainnya").val();
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
            var image = $("#image").val();

            // Validasi
            if(jenis_kejadian == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Jenis Kejadian Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }else if(jenis_kejadian_lainnya == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Jenis Kejadian Lainnya Harus Diisi',
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
            }else if(image == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Foto Dokumentasi Proses BAI Harus Diisi',
                    icon: 'warning',
                });
                return false;
            }
    })
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

    $(document).ready(function() {

        //Data Insert Code
        $('#submit-data').click(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: "{{ url('halo-security/addtemplate') }}",
                type: "post",
                dataType: "json",
                // headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // },
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

        // Template Data Laporan Kejadian
        var table = $('#kejadian').DataTable( {
            ajax: "{{ url('halo-security/getKejadian') }}",
            columns: [
                { 
                        "data": "lk_id",
                        render: function(data, type, row) {
                            return `
                            <div style="white-space: normal">${row.lk_id}
                            </div>
                            `;
                        }
                },
                {       "data": "jenis_kejadian", 
                        render: function(data, type, row) {
                            return `
                            <div style="white-space: normal">${row.jenis_kejadian}
                            </div>
                            `;
                        } 
                },
                {       "data": "nama_korban", 
                        render: function(data, type, row) {
                            return `
                            <div style="white-space: normal">${row.nama_korban}
                            </div>
                            `;
                        } 
                },
                { 
                        "data": null,
                        render: function(data, type, row) {

                            var saksi = row.saksis.map(function (object) {
                                return `data-nama_pelapor="${object.nama_saksi}"`;
                            })

                            return `
                            <div class="text-center">
                                <button class="btn btn-info btn-md" id="pilih" data-lk_id="${row.lk_id}" ${saksi} data-detail_barang_kejadian="${row.yang_terjadi}" data-tempat_kejadian="${row.lokasi_kejadian}" data-keterangan_kejadian="${row.bagaimana_terjadi}" data-jenis_kejadian="${row.jenis_kejadian}" data-nama_korban="${row.nama_korban}" data-nik_korban="${row.nik_korban}" data-bagian_korban="${row.bagian_korban}" data-nama_pelaku="${row.nama_terlapor}" data-umur_pelaku="${row.umur_terlapor}" data-ttl_pelaku="${row.ttl_terlapor}" data-pekerjaan_pelaku="${row.pekerjaan_terlapor}" data-alamat_pelaku="${row.alamat_terlapor}" data-status_pelaku="${row.status_terlapor}" data-agama_pelaku="${row.agama_terlapor}"><i class="ri-check-line"></i> Pilih</button>
                            </div>
                            `;
                        }
                }
            ]
        } );


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
                // headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // },
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
                    // headers: {
                    //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // },
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
                    // headers: {
                    //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    // },
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

<script language="JavaScript">

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

    Webcam.set({
        width: 300,
        height: 200,
        image_format: 'jpg',
        jpg_quality: 90
    });
    
    Webcam.attach( '#my_camera' );
    
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }
</script>

<script>
    function myFunction() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin menghapus semua isi form input data berita acara introgasi ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Tidak, Batalkan',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                if (result.isConfirmed){

                    swalWithBootstrapButtons.fire(
                        'Dihapus!',
                        'Semua isi form input data berita acara introgasi ini berhasil dihapus.',
                        "success"
                    );
                    const request = window.indexedDB.deleteDatabase('halo_security');
                    window.location.reload()

                }

            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Dibatalkan!',
                    'Proses menghapus semua isi form input data berita acara introgasi ini',
                    'error'
                );
            }
        });
    }
</script>

<script>

    function changeNilai(_id)
    {
        let nilai = document.getElementById(_id).value;

        var database = window.indexedDB.open("halo_security", 20);

        if(_id == 'nama_pelaku') {
            $('.nama__').text(nilai)
        }
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("baintrogasi", "readwrite");
            let objectStore = transaction.objectStore("baintrogasi");

            let request = objectStore.get(_id);

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: _id, nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    }

    const compressImage = async (file, { quality = 1, type = file.type }) => {
        // Get as image data
        const imageBitmap = await createImageBitmap(file);

        // Resize image to width 100px
        const canvas = document.createElement('canvas');
        canvas.width = 450;
        canvas.height = imageBitmap.height * (450 / imageBitmap.width);
        const ctx = canvas.getContext('2d');

        ctx.drawImage(imageBitmap, 0, 0, canvas.width, canvas.height);

        // Turn into Blob
        const blob = await new Promise((resolve) =>
            canvas.toBlob(resolve, type, quality)
        );

        // Turn Blob into File
        return new File([blob], file.name, {
            type: blob.type,
        });
    };

    // Untuk Foto Dokumentasi Kejadian
    const previewImage = async (_id) =>
    {
        var files = document.getElementById('bai_oneimage').files;

        // No files selected
        if (!files.length) return;

        // We'll store the files in this data transfer object
        const dataTransfer = new DataTransfer();

        let file = document.getElementById('bai_oneimage').files[0];
        if (!file.type.startsWith('image')) {
            // Ignore this file, but do add it to our result
            dataTransfer.items.add(file);
            return;
        }

        // We compress the file by 50%
        const compressedFile = await compressImage(file, {
            quality: 0.4,
            type: 'image/jpeg',
        });

        // Save back the compressed file instead of the original file
        dataTransfer.items.add(compressedFile);

        file = dataTransfer.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            let base64 = reader.result;
            let image = document.createElement('img');
            image.src = base64;
            image.style.width = '100%';
            image.style.height = '100%';
            image.style.objectFit = 'contain';

            var imageInner = document.createElement('div');
            imageInner.style.marginBottom = '2px';
            // Position relative
            imageInner.style.position = 'relative';
            imageInner.appendChild(image);

            // Store to textarea
            // document.getElementById(_id + '_image').value = base64;
            // var textareaImage = document.createElement('textarea');
            // textareaImage.name = 'image[' + _id.split('-')[1] + '][]';
            // textareaImage.value = base64;

            // document.getElementById(_id + '_image_container');

            // Clear input file
            document.getElementById('bai_oneimage').value = '';

            // Save image to indexeddb
            var database = window.indexedDB.open("halo_security", 20);

            // Save data to indexeddb
            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("baintrogasi", "readwrite");
                let objectStore = transaction.objectStore("baintrogasi");
                // let request = objectStore.put({ id: _id, image: base64 });
                // Change only image
                let request = objectStore.get(_id);
                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var images = data.images;
                        if (images == null) {
                            images = [];
                        }

                        images = base64;

                        data.images = images;
                        objectStore.put(data);
                        if (data.images) {
                            document.getElementById('image-preview').innerHTML = '';
                            var _image = data.images
                            // data.images.forEach(_image => {
                                let image = document.createElement('img');
                                image.src = _image;
                                image.style.width = '100%';
                                image.style.height = '100%';
                                image.style.objectFit = 'contain';

                                var imageInner = document.createElement('div');
                                imageInner.style.marginBottom = '2px';
                                // Position relative
                                imageInner.style.position = 'relative';
                                imageInner.appendChild(image);

                                // Create delete button
                                var deleteButton = document.createElement('button');
                                deleteButton.style.position = 'absolute';
                                deleteButton.style.top = '0';
                                deleteButton.style.right = '0';
                                deleteButton.style.margin = '2px';
                                deleteButton.style.padding = '2px 5px';
                                deleteButton.style.fontSize = '10px';
                                deleteButton.style.cursor = 'pointer';
                                deleteButton.innerHTML = 'Hapus';
                                deleteButton.type = 'button';
                                // Add css class
                                deleteButton.classList.add('btn');
                                deleteButton.classList.add('btn-xs');
                                deleteButton.classList.add('btn-danger');
                                // Add onclick event
                                deleteButton.onclick = function () {

                                    var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                                    if (!confirm) return;

                                    // Delete image from indexeddb
                                    var database = window.indexedDB.open("halo_security", 20);

                                    // Save data to indexeddb
                                    database.onsuccess = function(event) {
                                        let db = event.target.result;
                                        let transaction = db.transaction("baintrogasi", "readwrite");
                                        let objectStore = transaction.objectStore("baintrogasi");
                                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                        // Change only image
                                        let request = objectStore.get(_id);
                                        request.onsuccess = function(event) {
                                            let data = event.target.result;
                                            if (data) {
                                                var images = data.images;
                                                if (images == null) {
                                                    images = [];
                                                }

                                                var index = images.indexOf(_image);
                                                if (index > -1) {
                                                    images.splice(index, 1);
                                                }

                                                data.images = images;
                                                objectStore.put(data);
                                            }
                                        };

                                        request.onerror = function(event) {
                                            console.log("Error retrieving data:", event.target.error);
                                        };
                                    };

                                    // Delete image from DOM
                                    this.parentElement.remove();
                                }
                                imageInner.appendChild(deleteButton);

                                document.getElementById('image-preview').appendChild(imageInner);

                                // console.log('test', imageInner);

                                var textareaImage = document.createElement('input');
                                textareaImage.setAttribute("type", "hidden");
                                textareaImage.name = 'bai_oneimage';
                                textareaImage.setAttribute("value", _image);
                                // textareaImage.value = _image;

                                document.getElementById('image_value').appendChild(textareaImage);
                            // });
                        }
                    } else {
                        objectStore.put({ id: _id, images: base64 });

                        // if (data.images) {
                            document.getElementById('image-preview').innerHTML = '';
                            var _image = base64
                            // data.images.forEach(_image => {
                                let image = document.createElement('img');
                                image.src = _image;
                                image.style.width = '100%';
                                image.style.height = '100%';
                                image.style.objectFit = 'contain';

                                var imageInner = document.createElement('div');
                                imageInner.style.marginBottom = '2px';
                                // Position relative
                                imageInner.style.position = 'relative';
                                imageInner.appendChild(image);

                                // Create delete button
                                var deleteButton = document.createElement('button');
                                deleteButton.style.position = 'absolute';
                                deleteButton.style.top = '0';
                                deleteButton.style.right = '0';
                                deleteButton.style.margin = '2px';
                                deleteButton.style.padding = '2px 5px';
                                deleteButton.style.fontSize = '10px';
                                deleteButton.style.cursor = 'pointer';
                                deleteButton.innerHTML = 'Hapus';
                                deleteButton.type = 'button';
                                // Add css class
                                deleteButton.classList.add('btn');
                                deleteButton.classList.add('btn-xs');
                                deleteButton.classList.add('btn-danger');
                                // Add onclick event
                                deleteButton.onclick = function () {

                                    var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                                    if (!confirm) return;

                                    // Delete image from indexeddb
                                    var database = window.indexedDB.open("halo_security", 20);

                                    // Save data to indexeddb
                                    database.onsuccess = function(event) {
                                        let db = event.target.result;
                                        let transaction = db.transaction("baintrogasi", "readwrite");
                                        let objectStore = transaction.objectStore("baintrogasi");
                                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                        // Change only image
                                        let request = objectStore.get(_id);
                                        request.onsuccess = function(event) {
                                            let data = event.target.result;
                                            if (data) {
                                                var images = data.images;
                                                if (images == null) {
                                                    images = [];
                                                }

                                                var index = images.indexOf(_image);
                                                if (index > -1) {
                                                    images.splice(index, 1);
                                                }

                                                data.images = images;
                                                objectStore.put(data);
                                            }
                                        };

                                        request.onerror = function(event) {
                                            console.log("Error retrieving data:", event.target.error);
                                        };
                                    };

                                    // Delete image from DOM
                                    this.parentElement.remove();
                                }
                                imageInner.appendChild(deleteButton);

                                document.getElementById('image-preview').appendChild(imageInner);

                                // console.log('test', imageInner);

                                // Store to textarea
                                // document.getElementById(idPertanyaan + '_image').value = data.image;
                                var textareaImage = document.createElement('input');
                                textareaImage.setAttribute("type", "hidden");
                                textareaImage.name = 'bai_oneimage';
                                textareaImage.setAttribute("value", _image);
                                // textareaImage.value = _image;

                                document.getElementById('image_value').appendChild(textareaImage);
                            // });
                        // }
                    }
                };

                request.onerror = function(event) {
                    console.log("Error retrieving data:", event.target.error);
                };
            };
            
        };
    }

    // Untuk menampilkan gambar Foto Dokumentasi Kejadian
    var database = window.indexedDB.open("halo_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'bai_oneimage';
        var db = event.target.result;
        var transaction = db.transaction("baintrogasi", "readonly");
        var objectStore = transaction.objectStore("baintrogasi");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                if (data.images) {
                            document.getElementById('image-preview').innerHTML = '';
                            var _image = data.images
                            // data.images.forEach(_image => {
                                let image = document.createElement('img');
                                image.src = _image;
                                image.style.width = '100%';
                                image.style.height = '100%';
                                image.style.objectFit = 'contain';

                                var imageInner = document.createElement('div');
                                imageInner.style.marginBottom = '2px';
                                // Position relative
                                imageInner.style.position = 'relative';
                                imageInner.appendChild(image);

                                // Create delete button
                                var deleteButton = document.createElement('button');
                                deleteButton.style.position = 'absolute';
                                deleteButton.style.top = '0';
                                deleteButton.style.right = '0';
                                deleteButton.style.margin = '2px';
                                deleteButton.style.padding = '2px 5px';
                                deleteButton.style.fontSize = '10px';
                                deleteButton.style.cursor = 'pointer';
                                deleteButton.innerHTML = 'Hapus';
                                deleteButton.type = 'button';
                                // Add css class
                                deleteButton.classList.add('btn');
                                deleteButton.classList.add('btn-xs');
                                deleteButton.classList.add('btn-danger');
                                // Add onclick event
                                deleteButton.onclick = function () {

                                    var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                                    if (!confirm) return;

                                    // Delete image from indexeddb
                                    var database = window.indexedDB.open("halo_security", 20);

                                    // Save data to indexeddb
                                    database.onsuccess = function(event) {
                                        let db = event.target.result;
                                        let transaction = db.transaction("baintrogasi", "readwrite");
                                        let objectStore = transaction.objectStore("baintrogasi");
                                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                        // Change only image
                                        let request = objectStore.get(_id);
                                        request.onsuccess = function(event) {
                                            let data = event.target.result;
                                            if (data) {
                                                var images = data.images;
                                                if (images == null) {
                                                    images = [];
                                                }

                                                var index = images.indexOf(_image);
                                                if (index > -1) {
                                                    images.splice(index, 1);
                                                }

                                                data.images = images;
                                                objectStore.put(data);
                                            }
                                        };

                                        request.onerror = function(event) {
                                            console.log("Error retrieving data:", event.target.error);
                                        };
                                    };

                                    // Delete image from DOM
                                    this.parentElement.remove();
                                }
                                imageInner.appendChild(deleteButton);

                                document.getElementById('image-preview').appendChild(imageInner);

                                var textareaImage = document.createElement('input');
                                textareaImage.setAttribute("type", "hidden");
                                textareaImage.name = 'bai_oneimage';
                                textareaImage.setAttribute("value", _image);
                                // textareaImage.value = _image;

                                document.getElementById('image_value').appendChild(textareaImage);
                            // });
                        }
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    // Untuk Foto Introgasi
    const previewGambar = async (_id) =>
    {
        var files = document.getElementById('foto_introgasi-0').files;

        // No files selected
        if (!files.length) return;

        // We'll store the files in this data transfer object
        const dataTransfer = new DataTransfer();

        let file = document.getElementById('foto_introgasi-0').files[0];
        if (!file.type.startsWith('image')) {
            // Ignore this file, but do add it to our result
            dataTransfer.items.add(file);
            return;
        }

        // We compress the file by 50%
        const compressedFile = await compressImage(file, {
            quality: 0.4,
            type: 'image/jpeg',
        });

        // Save back the compressed file instead of the original file
        dataTransfer.items.add(compressedFile);

        file = dataTransfer.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            let base64 = reader.result;
            let image = document.createElement('img');
            image.src = base64;
            image.style.width = '100%';
            image.style.height = '100%';
            image.style.objectFit = 'contain';

            var imageInner = document.createElement('div');
            imageInner.style.marginBottom = '2px';
            // Position relative
            imageInner.style.position = 'relative';
            imageInner.classList.add('col-6')
            imageInner.appendChild(image);

            // Create delete button
            var deleteButton = document.createElement('button');
            deleteButton.style.position = 'absolute';
            deleteButton.style.top = '0';
            deleteButton.style.right = '0';
            deleteButton.style.margin = '2px';
            deleteButton.style.padding = '2px 5px';
            deleteButton.style.fontSize = '10px';
            deleteButton.style.cursor = 'pointer';
            deleteButton.innerHTML = 'Hapus';
            deleteButton.type = 'button';
            // Add css class
            deleteButton.classList.add('btn');
            deleteButton.classList.add('btn-xs');
            deleteButton.classList.add('btn-danger');
            // Add onclick event
            deleteButton.onclick = function () {

                var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                if (!confirm) return;

                // Delete image from indexeddb
                var database = window.indexedDB.open("halo_security", 20);

                // Save data to indexeddb
                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("baintrogasi", "readwrite");
                    let objectStore = transaction.objectStore("baintrogasi");
                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                    // Change only image
                    let request = objectStore.get(_id);
                    request.onsuccess = function(event) {
                        let data = event.target.result;
                        if (data) {
                            var images = data.images;
                            if (images == null) {
                                images = [];
                            }

                            var index = images.indexOf(base64);
                            if (index > -1) {
                                images.splice(index, 1);
                            }

                            data.images = images;
                            objectStore.put(data);
                        }

                        // if (data) {
                        //     var images = data.images;
                        //     if (images == null) {
                        //         images = [];
                        //     }

                        //     images.push(base64);

                        //     data.images = images;
                        //     objectStore.put(data);
                        // } else {
                        //     objectStore.put({ id: _id, images: [base64] });
                        // }
                    };

                    request.onerror = function(event) {
                        console.log("Error retrieving data:", event.target.error);
                    };
                };

                $('#gambar_foto_0').remove();

                // Delete image from DOM
                this.parentElement.remove();
            }

            imageInner.appendChild(deleteButton);

            document.getElementById('gambar-preview-0').appendChild(imageInner);

            // var textareaImage = document.createElement('input');
            // textareaImage.setAttribute("type", "text");
            // textareaImage.name = 'foto_introgasi[0][]';
            // textareaImage.setAttribute("value", base64);

            var textareaImage = document.createElement('textarea');
            // textareaImage.setAttribute("type", "text");
            textareaImage.name = 'foto_introgasi[0][]';
            textareaImage.id = 'gambar_foto_0';
            textareaImage.value = base64;

            document.getElementById('gambar_value-0').appendChild(textareaImage);

            // Clear input file
            document.getElementById('foto_introgasi-0').value = '';

            // Save image to indexeddb
            var database = window.indexedDB.open("halo_security", 20);

            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("baintrogasi", "readwrite");
                let objectStore = transaction.objectStore("baintrogasi");
                // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                // Change only image
                let request = objectStore.get(_id);
                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var images = data.images;
                        if (images == null) {
                            images = [];
                        }

                        images.push(base64);

                        data.images = images;
                        objectStore.put(data);
                    } else {
                        objectStore.put({ id: _id, images: [base64] });
                    }
                };

                request.onerror = function(event) {
                    console.log("Error retrieving data:", event.target.error);
                };
            };
            
        };
    }

    // Untuk menampilkan gambar Foto Introgasi
    var database = window.indexedDB.open("halo_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'foto_introgasi-0';
        var db = event.target.result;
        var transaction = db.transaction("baintrogasi", "readonly");
        var objectStore = transaction.objectStore("baintrogasi");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                if (data.images) {
                        document.getElementById('gambar-preview-0').innerHTML = '';
                        data.images.forEach(_image => {
                            let image = document.createElement('img');
                            image.src = _image;
                            image.style.width = '100%';
                            image.style.height = '100%';
                            image.style.objectFit = 'contain';

                            var imageInner = document.createElement('div');
                            imageInner.style.marginBottom = '2px';
                            // Position relative
                            imageInner.style.position = 'relative';
                            imageInner.classList.add('col-6')
                            imageInner.appendChild(image);

                            // Create delete button
                            var deleteButton = document.createElement('button');
                            deleteButton.style.position = 'absolute';
                            deleteButton.style.top = '0';
                            deleteButton.style.right = '0';
                            deleteButton.style.margin = '2px';
                            deleteButton.style.padding = '2px 5px';
                            deleteButton.style.fontSize = '10px';
                            deleteButton.style.cursor = 'pointer';
                            deleteButton.innerHTML = 'Hapus';
                            deleteButton.type = 'button';
                            // Add css class
                            deleteButton.classList.add('btn');
                            deleteButton.classList.add('btn-xs');
                            deleteButton.classList.add('btn-danger');
                            // Add onclick event
                            deleteButton.onclick = function () {

                                var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                                if (!confirm) return;

                                // Delete image from indexeddb
                                var database = window.indexedDB.open("halo_security", 20);

                                // Save data to indexeddb
                                database.onsuccess = function(event) {
                                    let db = event.target.result;
                                    let transaction = db.transaction("baintrogasi", "readwrite");
                                    let objectStore = transaction.objectStore("baintrogasi");
                                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                    // Change only image
                                    let request = objectStore.get(_id);
                                    request.onsuccess = function(event) {
                                        let data = event.target.result;
                                        if (data) {
                                            var images = data.images;
                                            if (images == null) {
                                                images = [];
                                            }

                                            var index = images.indexOf(_image);
                                            if (index > -1) {
                                                images.splice(index, 1);
                                            }

                                            data.images = images;
                                            objectStore.put(data);
                                        }
                                    };

                                    request.onerror = function(event) {
                                        console.log("Error retrieving data:", event.target.error);
                                    };
                                };

                                $('#gambar_foto_0').remove();

                                // Delete image from DOM
                                this.parentElement.remove();
                            }
                            imageInner.appendChild(deleteButton);

                            document.getElementById('gambar-preview-0').appendChild(imageInner);

                            var textareaImage = document.createElement('textarea');
                            textareaImage.name = 'foto_introgasi[0][]';
                            textareaImage.id = 'gambar_foto_0';
                            textareaImage.value = _image;

                            // var textareaImage = document.createElement('textarea');
                            // textareaImage.name = 'image[' + idPertanyaan.split('-')[1] + '][]';
                            // textareaImage.value = _image;

                            document.getElementById('gambar_value-0').appendChild(textareaImage);
                        });
                    }
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    // Hanya untuk input tipe text
    @foreach(['jenis_kejadian', 'lk_id', 'nama_introgasi', 'umur_introgasi', 'pekerjaan_introgasi', 'bagian_introgasi', 'nama_pelapor', 'detail_barang_kejadian', 'tempat_kejadian', 'nama_korban', 'nik_korban', 'bagian_korban', 'nama_pelaku', 'umur_pelaku', 'ttl_pelaku', 'pekerjaan_pelaku', 'nik_pelaku', 'bagian_pelaku', 'alamat_pelaku', 'agama_pelaku', 'suku_pelaku', 'status_pelaku', 'shift', 'pendidikan_pelaku', 'nik_ktp_pelaku', 'no_hp_pelaku', 'tempat_introgasi', 'keterangan_kejadian', 'pertanyaan_introgasi-0', 'jawaban_introgasi-0', 'keterangan_introgasi-0', 'jenis_kejadian_lainnya'] as $id)
        var database = window.indexedDB.open("halo_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = '{{ $id }}';
            var db = event.target.result;
            var transaction = db.transaction("baintrogasi", "readonly");
            var objectStore = transaction.objectStore("baintrogasi");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    document.getElementById(_id).value = data.nilai;
                    if (_id == 'jenis_kejadian') {
                        if (data.nilai === 'lainnya') {
                            document.querySelector('#jenis_kejadian_container').innerHTML = `<input class="form-control mb-3" onchange="changeNilai('jenis_kejadian_lainnya')" name="jenis_kejadian_lainnya" id="jenis_kejadian_lainnya" placeholder="Jenis Kejadian lainnya" />`;
                        }
                    }
                } else {
                    console.log(_id, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };
    @endforeach

</script>

{{-- Proses simpan data ke data introgasi dengan status draft --}}
<script>
    $(document).ready(function () {
        $('#validatePreview').on('click', function (e) {
            e.preventDefault()

            // Ambil data dari semua form input untuk disimpan
            var data = {
                laporan_kejadian_id: $('#lk_id').val(),
                jenis_kejadian: $('#jenis_kejadian').val(),
                jenis_kejadian_lainnya: $('#jenis_kejadian_lainnya').val(),
                nama_introgasi: $('#nama_introgasi').val(),
                umur_introgasi: $('#umur_introgasi').val(),
                pekerjaan_introgasi: $('#pekerjaan_introgasi').val(),
                bagian_introgasi: $('#bagian_introgasi').val(),
                nama_pelapor: $('#nama_pelapor').val(),
                detail_barang_kejadian: $('#detail_barang_kejadian').val(),
                tempat_kejadian: $('#tempat_kejadian').val(),
                nama_korban: $('#nama_korban').val(),
                nik_korban: $('#nik_korban').val(),
                bagian_korban: $('#bagian_korban').val(),
                nama_pelaku: $('#nama_pelaku').val(),
                umur_pelaku: $('#umur_pelaku').val(),
                ttl_pelaku: $('#ttl_pelaku').val(),
                pekerjaan_pelaku: $('#pekerjaan_pelaku').val(),
                nik_pelaku: $('#nik_pelaku').val(),
                bagian_pelaku: $('#bagian_pelaku').val(),
                alamat_pelaku: $('#alamat_pelaku').val(),
                agama_pelaku: $('#agama_pelaku').val(),
                suku_pelaku: $('#suku_pelaku').val(),
                status_pelaku: $('#status_pelaku').val(),
                shift: $('#shift').val(),
                pendidikan_pelaku: $('#pendidikan_pelaku').val(),
                nik_ktp_pelaku: $('#nik_ktp_pelaku').val(),
                no_hp_pelaku: $('#no_hp_pelaku').val(),
                tempat_introgasi: $('#tempat_introgasi').val(),
                keterangan_kejadian: $('#keterangan_kejadian').val(),
                pertanyaan: [],
                jawaban: [],
                foto: [],
                keterangan: [],
                bai_oneimage: $('[name="bai_oneimage"]').val(),
                image: $('.image-tag').val(),
            };

            // Mengambil nilai pertanyaan dan jawaban introgasi dari tabel
            $('#tabletji tr:not(:first)').each(function (index) {
                var pertanyaan = $(this).find('textarea[name="pertanyaan_introgasi[]"]').val();
                var jawaban = $(this).find('textarea[name="jawaban_introgasi[]"]').val();

                // Buat objek untuk setiap baris dan tambahkan ke dalam array data.pertanyaan_jawaban
                var row_data = {
                    pertanyaan_introgasi: pertanyaan,
                    jawaban_introgasi: jawaban
                };

                data.pertanyaan[index] = row_data;
                data.jawaban[index] = row_data;
            });

            data.pertanyaan = JSON.stringify(data.pertanyaan);
            data.jawaban = JSON.stringify(data.jawaban);

            // Mengambil nilai foto introgasi dan keterangan introgasi dari tabel
            $('#tabledi tr:not(:first)').each(function (index) {
                var foto_introgasi = $(this).find('textarea[id^="gambar_foto_"]').val();
                var keterangan_introgasi = $(this).find('textarea[name="keterangan_introgasi[]"]').val();

                // Buat objek untuk setiap baris dan tambahkan ke dalam array data.foto_keterangan
                var row_data = {
                    foto_introgasi: foto_introgasi,
                    keterangan_introgasi: keterangan_introgasi
                };

                data.foto[index] = row_data;
                data.keterangan[index] = row_data;
            });

            data.foto = JSON.stringify(data.foto);
            data.keterangan = JSON.stringify(data.keterangan);

            $.ajax({
                url: '/halo-security/bai/prosessimpanlk',
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.status == 'success') {
                        console.log('berhasil simpan introgasi', response.bai_id);
                        var previewIntrogasi = "{{ url('/halo-security/bai/prosespreviewlk') }}/" + response.bai_id;
        
                        window.open(previewIntrogasi, '_blank');
                    }
                },
                error: function (xhr) {
                    var res = xhr.responseJSON;
                    if ($.isEmptyObject(res) == false) {
                        var errorMessage = '<ul style="list-style-type: none; padding: 0; text-decoration: none;">';
                        $.each(res.errors, function (key, value) {
                            errorMessage += '<li style="text-decoration: none; margin-bottom: 10px;">' + value + '</li>';
                        });
                        errorMessage += '</ul>';
                        errorMessage += '<p style="margin-top: 10px; color: red; font-weight: bold;">!!! SEGERA CEK ULANG !!!</p>';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            html: errorMessage,
                        });
                    } else {
                        toastr.error('An error occurred while processing the request', 'Error !');
                    }
                }
            })
        });

        const validatePreview = document.getElementById("validatePreview");
    
        validatePreview.addEventListener("click", () => {
            validatePreview.classList.add("loading");
            // Simulate an asynchronous action, e.g., an API call
            setTimeout(() => {
                validatePreview.classList.remove("loading");
            }, 3000); // Replace with your desired loading time
        });
    });
</script>
@endpush