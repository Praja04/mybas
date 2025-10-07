@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
    <link href="{{ asset('assets/velzon/libs/quill/quill.snow.css') }}" rel="stylesheet" />
@endpush
<div class="container-fluid">
    <div class="card">
        <div class="card-header justify-content-between d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Buat Berita Acara Laporan Kejadian</h4>
            <button class="btn btn-md btn-danger" onclick="myFunction(this)">Clear</button>
        </div>

        <div class="card-body">
            <form id="validate" action="{{ route('create-laporan-kejadian') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="danru" class="form-label">Danru</label>
                                <input type="text" class="form-control" name="danru" id="danru" onkeyup="changeKejadian('danru')" value="{{old('danru')}}" placeholder="Masukan Danru">
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
                                <div>
                                    <select class="form-select mb-3" onchange="changeKejadian('jenis_kejadian')" name="jenis_kejadian" id="jenis_kejadian" value="{{old('jenis_kejadian')}}">
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
                                </div>
                                <div id="jenis_kejadian_container"></div>
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
                                <input type="text" class="form-control" name="nama_korban" id="nama_korban" onkeyup="changeKejadian('nama_korban')" value="{{old('nama_korban')}}" placeholder="Masukan Nama Korban">
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
                                <input type="text" name="nik_korban" id="nik_korban" onkeyup="changeKejadian('nik_korban')" value="{{old('nik_korban')}}" class="form-control" placeholder="Masukan Nomor NIK (Nomor Induk Karyawan) Korban">
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
                                <input type="text" name="perusahaan_korban" id="perusahaan_korban" onkeyup="changeKejadian('perusahaan_korban')" value="{{old('perusahaan_korban')}}" class="form-control" placeholder="Masukan perusahaan">
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
                                <input type="text" name="bagian_korban" id="bagian_korban" onkeyup="changeKejadian('bagian_korban')" value="{{old('bagian_korban')}}" class="form-control" placeholder="Masukan Bagian Korban">
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
                                <textarea class="form-control" name="lokasi_kejadian" id="lokasi_kejadian" onkeyup="changeKejadian('lokasi_kejadian')" value="{{old('lokasi_kejadian')}}" placeholder="Masukan Lokasi Kejadian" rows="3"></textarea>
                                <p class="text-danger ml-2">
                                    @error('lokasi_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="fakta_kejadian" class="form-label">Fakta Kejadian</label>
                                <table class="table table-bordered" id="tablefk">
                                    <thead>
                                        <tr>
                                            <th>Fakta Kejadian</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div id="passwordHelpBlock" class="form-text mb-2" style="margin-top: -2px;">
                                                    Nomor Fakta Kejadian (Tidak perlu ditulis ulang)
                                                </div>
                                                <textarea class="form-control" name="fakta_kejadian[]" onkeyup="changeKejadian('fakta_kejadian-0')" id="fakta_kejadian-0" placeholder="Masukan Fakta Kejadian" rows="3"></textarea>
                                                <p class="text-danger ml-2">
                                                    @error('fakta_kejadian')
                                                        {{ $message }}
                                                    @enderror
                                                </p>
                                            </td>
                                            <td class="d-flex justify-content-center mx-auto">
                                                <button type="button" name="addfk" id="addfk" class="btn btn-md btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="yang_terjadi" class="form-label">Kejadian yang terjadi</label>
                                <textarea class="form-control" name="yang_terjadi" id="yang_terjadi" onkeyup="changeKejadian('yang_terjadi')" value="{{old('yang_terjadi')}}" placeholder="Masukan kejadian yang terjadi" rows="3"></textarea>
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
                                <input type="text" name="nama_terlapor" id="nama_terlapor" onkeyup="changeKejadian('nama_terlapor')" value="{{old('nama_terlapor')}}" class="form-control" placeholder="Masukan Nama Terlapor">
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
                                <input type="text" name="umur_terlapor" id="umur_terlapor" onkeyup="changeKejadian('umur_terlapor')" value="{{old('umur_terlapor')}}" class="form-control" placeholder="Masukan Umur Terlapor">
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
                                <input type="text" name="ttl_terlapor" id="ttl_terlapor" onkeyup="changeKejadian('ttl_terlapor')" value="{{old('ttl_terlapor')}}" class="form-control" placeholder="Masukan Tempat Tanggal Lahir Terlapor">
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
                                <input type="text" name="pekerjaan_terlapor" id="pekerjaan_terlapor" onkeyup="changeKejadian('pekerjaan_terlapor')" value="{{old('pekerjaan_terlapor')}}" class="form-control" placeholder="Masukan Pekerjaan Terlapor">
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
                                <input type="text" name="alamat_terlapor" id="alamat_terlapor" onkeyup="changeKejadian('alamat_terlapor')" value="{{old('alamat_terlapor')}}" class="form-control" placeholder="Masukan Alamat Terlapor">
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
                                <input type="text" name="kelurahan_terlapor" id="kelurahan_terlapor" onkeyup="changeKejadian('kelurahan_terlapor')" value="{{old('kelurahan_terlapor')}}" class="form-control" placeholder="Masukan Kelurahan Terlapor">
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
                                <input type="text" name="kecamatan_terlapor" id="kecamatan_terlapor" onkeyup="changeKejadian('kecamatan_terlapor')" value="{{old('kecamatan_terlapor')}}" class="form-control" placeholder="Masukan Kecamatan Terlapor">
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
                                <input type="text" name="provinsi_terlapor" id="provinsi_terlapor" onkeyup="changeKejadian('provinsi_terlapor')" value="{{old('provinsi_terlapor')}}" class="form-control" placeholder="Masukan Provinsi Terlapor">
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
                                <select class="form-select mb-3" value="{{old('status_terlapor')}}" onchange="changeKejadian('status_terlapor')" name="status_terlapor" id="status_terlapor">
                                    <option value="">Pilih Status Terlapor</option>
                                    <option value="belum kawin">Belum Kawin</option>
                                    <option value="sudah kawin">Sudah Kawin</option>
                                    <option value="janda/duda">Janda/Duda</option>
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
                                <input type="text" name="agama_terlapor" id="agama_terlapor" onkeyup="changeKejadian('agama_terlapor')" value="{{old('agama_terlapor')}}" class="form-control" placeholder="Masukan Agama Terlapor">
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
                                <input type="text" name="kebangsaan_terlapor" id="kebangsaan_terlapor" onkeyup="changeKejadian('kebangsaan_terlapor')" value="{{old('kebangsaan_terlapor')}}" class="form-control" placeholder="Masukan Kebangsaan Terlapor">
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
                                <input type="text" name="no_ktp_terlapor" id="no_ktp_terlapor" onkeyup="changeKejadian('no_ktp_terlapor')" value="{{old('no_ktp_terlapor')}}" class="form-control" placeholder="Masukan Nomor KTP Terlapor">
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
                                <input type="text" name="no_simc_terlapor" id="no_simc_terlapor" onkeyup="changeKejadian('no_simc_terlapor')" value="{{old('no_simc_terlapor')}}" class="form-control" placeholder="Masukan Nomor SIM C Terlapor">
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
                                <input type="text" name="no_hp_terlapor" id="no_hp_terlapor" onkeyup="changeKejadian('no_hp_terlapor')" value="{{old('no_hp_terlapor')}}" class="form-control" placeholder="Masukan Nomor Handphone Terlapor">
                                <p class="text-danger ml-2">
                                    @error('no_hp_terlapor')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="bagaimana_terjadi" class="form-label">Bagaimana Terjadi</label>
                                <textarea class="form-control" name="bagaimana_terjadi" id="bagaimana_terjadi" onkeyup="changeKejadian('bagaimana_terjadi')" value="{{old('bagaimana_terjadi')}}" placeholder="Masukan Bagaimana Terjadi" rows="3"></textarea>
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
                                <textarea class="form-control" name="mengapa_terjadi" id="mengapa_terjadi" onkeyup="changeKejadian('mengapa_terjadi')" value="{{old('mengapa_terjadi')}}" placeholder="Masukan Mengapa Terjadi" rows="3"></textarea>
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
                                    <thead>
                                        <tr>
                                            <th>Nama Saksi</th>
                                            <th>Nik Saksi</th>
                                            <th>Departemen Saksi</th>
                                            <th>Keterangan Saksi</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="nama_saksi[]" onkeyup="changeKejadian('nama_saksi-0')" id="nama_saksi-0" class="form-control" placeholder="Masukan Nama Saksi" required></td>
                                            <td><input type="text" name="nik_saksi[]" onkeyup="changeKejadian('nik_saksi-0')" id="nik_saksi-0" class="form-control" placeholder="Masukan Nik Saksi" required></td>
                                            <td><input type="text" name="departement_saksi[]" onkeyup="changeKejadian('departement_saksi-0')" id="departement_saksi-0" class="form-control" placeholder="Masukan Departemen Saksi" required></td>
                                            <td><input type="text" name="keterangan_saksi[]" onkeyup="changeKejadian('keterangan_saksi-0')" id="keterangan_saksi-0" class="form-control" placeholder="Masukan Keterangan Saksi" required></td>
                                            <td class="d-flex justify-content-center mx-auto"><button type="button" name="addsk" id="addsk" class="btn btn-sm btn-success"><i class="ri-add-box-line" style="font-size: 20px;"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="uraian_kejadian" class="form-label">Uraian Kejadian</label>
                                <textarea class="form-control" style="display: none;" name="uraian_kejadian" id="uraian_kejadian" rows="3"></textarea>
                                <div id="uraian"></div>
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
                                <textarea class="form-control" style="display: none" name="tindakan_pengamanan" id="tindakan_pengamanan" rows="3"></textarea>
                                <div id="tindakan"></div>
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
                                <textarea class="form-control" style="display: none" name="hasil_daritindakan" id="hasil_daritindakan" rows="3"></textarea>
                                <div id="hasil"></div>
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
                                <textarea class="form-control" style="display: none" name="saran" id="saran" placeholder="Masukan Saran" rows="3"></textarea>
                                <div id="saran-text-area"></div>
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
                                    <tr>
                                        <td style="max-width: 250px">
                                            <input accept="image/png, image/jpeg" onchange="previewKejadian('dokumentasi_kejadian-0')" class="form-control foto-introgasi_0" data-key="0" type="file" id="dokumentasi_kejadian-0" multiple>
                                            <div class="mt-4">
                                                <div class="row gambar-laporan" id="kejadian-preview-0">
                                                </div>
                                                <div class="textarea_image_container" id="kejadian_value-0" style="display: none;"></div>
                                            </div>
                                        </td>
                                        <td><textarea class="form-control" onkeyup="changeKejadian('keterangan_kejadian-0')" name="keterangan_kejadian[]" id="keterangan_kejadian-0" placeholder="Masukan Keterangan Kejadian" rows="3"></textarea></td>
                                        <td class="d-flex justify-content-center mx-auto"><button type="button" name="adddk" id="adddk" class="btn btn-sm btn-success mt-3"><i class="ri-add-box-line" style="font-size: 20px;"></i></button></td>
                                    </tr>
                                </table>
                                <p class="text-danger ml-2">
                                    @error('dokumentasi_kejadian')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex">
                                <div class="flex">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="submit" class="btn btn-md btn-primary">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/velzon/libs/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/velzon/js/pages/form-editor.init.js') }}"></script>
<script>
    var database = window.indexedDB.open("kejadian_security", 20);

    // Create object store
    database.onupgradeneeded = function(event) {
        let db = event.target.result;
        // Delete old object store
        if (db.objectStoreNames.contains("laporankejadian")) {
            db.deleteObjectStore("laporankejadian");
            console.log('Delete because upgrade needed')
        }

        let objectStore = db.createObjectStore("laporankejadian", { keyPath: "id" });
    };

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

    // Untuk textarea uraian kejadian
    var quillUraianKejadian = new Quill('#uraian', {
        modules: {
            toolbar: false
        },

        placeholder: 'Masukan Uraian Kejadian',
        theme: 'snow'
    });

    quillUraianKejadian.on('text-change', function(delta, oldDelta, source) {
        $('#uraian_kejadian').val(quillUraianKejadian.root.innerHTML)

        var database = window.indexedDB.open("kejadian_security", 20);

        var nilai = quillUraianKejadian.root.innerHTML
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("laporankejadian", "readwrite");
            let objectStore = transaction.objectStore("laporankejadian");

            let request = objectStore.get('uraian_kejadian');

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: 'uraian_kejadian', nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    });

    // Untuk textarea tindakan pengamanan
    var quillTindakanPengamanan = new Quill('#tindakan', {
        modules: {
            toolbar: false
        },
        placeholder: 'Masukan Tindakan Pengamanan',
        theme: 'snow'
    });

    quillTindakanPengamanan.on('text-change', function(delta, oldDelta, source) {
        $('#tindakan_pengamanan').val(quillTindakanPengamanan.root.innerHTML)

        var database = window.indexedDB.open("kejadian_security", 20);

        var nilai = quillTindakanPengamanan.root.innerHTML
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("laporankejadian", "readwrite");
            let objectStore = transaction.objectStore("laporankejadian");

            let request = objectStore.get('tindakan_pengamanan');

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: 'tindakan_pengamanan', nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    });

    // Untuk textarea hasil dari tindakan
    var quillHasilDariTindakan = new Quill('#hasil', {
        modules: {
            toolbar: false
        },
        placeholder: 'Masukan Hasil Dari Tindakan',
        theme: 'snow'
    });

    quillHasilDariTindakan.on('text-change', function(delta, oldDelta, source) {
        $('#hasil_daritindakan').val(quillHasilDariTindakan.root.innerHTML)

        var database = window.indexedDB.open("kejadian_security", 20);

        var nilai = quillHasilDariTindakan.root.innerHTML
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("laporankejadian", "readwrite");
            let objectStore = transaction.objectStore("laporankejadian");

            let request = objectStore.get('hasil_daritindakan');

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: 'hasil_daritindakan', nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    });

    // Untuk textarea saran
    var quillSaran = new Quill('#saran-text-area', {
        modules: {
            toolbar: false
        },
        placeholder: 'Masukan Saran',
        theme: 'snow'
    });

    quillSaran.on('text-change', function(delta, oldDelta, source) {
        $('#saran').val(quillSaran.root.innerHTML)

        var database = window.indexedDB.open("kejadian_security", 20);

        var nilai = quillSaran.root.innerHTML
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("laporankejadian", "readwrite");
            let objectStore = transaction.objectStore("laporankejadian");

            let request = objectStore.get('saran');

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: 'saran', nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    });
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
                <input class="form-control mb-3" onchange="changeKejadian('jenis_kejadian_lainnya')" name="jenis_kejadian_lainnya" id="jenis_kejadian_lainnya" placeholder="Jenis Kejadian lainnya" required />
            `);
        } else {
            $('#jenis_kejadian_container').html('');
        }
    });

    var i = 0;
    var i_dokumentasi = 0;
    var increment = 1;

    // Untuk Fakta Kejadian
    $('#addfk').click(function(){
        ++i;
        ++increment;
        $('#tablefk').append(
            `
            <tbody>
            <tr>
                <td>
                    <div id="passwordHelpBlock" class="form-text mb-2" style="margin-top: -2px;">
                        Nomor Fakta Kejadian (Tidak perlu ditulis ulang)
                    </div>
                    <textarea class="form-control" name="fakta_kejadian[]" onKeyup="changeFakta('${i}')" id="fakta_kejadian-${i}" placeholder="Masukan Fakta Kejadian" rows="3"></textarea>
                </td>
                <td class="d-flex justify-content-center">
                    <button type="button" data-id_fakta="${i}" class="btn btn-md btn-danger remove-table-row-fk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                </td>

            </tr>
            </tbody>
            `);

            var database = window.indexedDB.open("kejadian_security", 20);
        
            // Save data to indexeddb
            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("laporankejadian", "readwrite");
                let objectStore = transaction.objectStore("laporankejadian");

                let request = objectStore.get('fact_tragedi');

                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var _nilai = data.nilai;
                        _nilai.push({
                            id: i,
                            fakta: '',
                        })
                        data.nilai = _nilai;
                        objectStore.put(data);
                        console.log("Data berhasil diubah");
                    } else {
                        var nilai = [];
                        nilai.push({
                            id: i,
                            fakta: '',
                        })
                        objectStore.put({ id: 'fact_tragedi', nilai: nilai });
                        console.log("Data berhasil dibuat");
                    }

                };

                request.onerror = function(event) {
                    console.log("Data gagal disimpan");
                };
            };
    });

    var database = window.indexedDB.open("kejadian_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'fact_tragedi';
        var db = event.target.result;
        var transaction = db.transaction("laporankejadian", "readonly");
        var objectStore = transaction.objectStore("laporankejadian");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                data.nilai.forEach((item) => {
                    i = parseInt(item.id)
                    $('#tablefk').append(
                    `<tbody>
                        <tr>
                            <td>
                                <textarea class="form-control" name="fakta_kejadian[]" onKeyup="changeFakta('${item.id}')" id="fakta_kejadian-${item.id}" placeholder="Masukan Fakta Kejadian" rows="3">${item.fakta}</textarea>
                            </td>
                            <td class="d-flex justify-content-center">
                                <button type="button" data-id_fakta="${i}" class="btn btn-md btn-danger remove-table-row-fk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                            </td>

                        </tr>
                    </tbody>`);
                })
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    function changeFakta(id_fakta){
        var fakta = $('#fakta_kejadian-'+id_fakta).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'fact_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_fakta) {
                            item.fakta = fakta
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

    $(document).on('click','.remove-table-row-fk', function(){
        var id_fakta = $(this).data('id_fakta')

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'fact_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    var _nilai = data.nilai.filter(item => {
                        if(item.id != id_fakta) {
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

    // Untuk Saksi Kejadian
    $('#addsk').click(function(){
        ++i;
        $('#tablesk').append(
            `<tr>
                <td><input type="text" name="nama_saksi[]" onkeyup="changeNamaSaksi('${i}')" id="nama_saksi-${i}" class="form-control" placeholder="Masukan Nama Saksi" required></td>
                <td><input type="text" name="nik_saksi[]" onkeyup="changeNikSaksi('${i}')" id="nik_saksi-${i}" class="form-control" placeholder="Masukan Nik Saksi" required></td>
                <td><input type="text" name="departement_saksi[]" onkeyup="changeDepartementSaksi('${i}')" id="departement_saksi-${i}" class="form-control" placeholder="Masukan Departemen Saksi" required></td>
                <td><input type="text" name="keterangan_saksi[]" onkeyup="changeKeteranganSaksi('${i}')" id="keterangan_saksi-${i}" class="form-control" placeholder="Masukan Keterangan Saksi" required></td>
                <td class="d-flex justify-content-center mx-auto">
                    <button type="button" data-id_saksi_tragedi="${i}" class="btn btn-sm btn-danger remove-table-row-sk"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                </td>

            </tr>`);

            var database = window.indexedDB.open("kejadian_security", 20);
        
            // Save data to indexeddb
            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("laporankejadian", "readwrite");
                let objectStore = transaction.objectStore("laporankejadian");

                let request = objectStore.get('saksi_tragedi');

                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var _nilai = data.nilai;
                        _nilai.push({
                            id: i,
                            nama_saksi: "",
                            nik_saksi: '',
                            departement_saksi: '',
                            keterangan_saksi: '',
                        })
                        data.nilai = _nilai;
                        objectStore.put(data);
                        console.log("Data berhasil diubah");
                    } else {
                        var nilai = [];
                        nilai.push({
                            id: i,
                            nama_saksi: '',
                            nik_saksi: '',
                            departement_saksi: '',
                            keterangan_saksi: '',
                        })
                        objectStore.put({ id: 'saksi_tragedi', nilai: nilai });
                        console.log("Data berhasil dibuat");
                    }

                };

                request.onerror = function(event) {
                    console.log("Data gagal disimpan");
                };
            };
    });

    var database = window.indexedDB.open("kejadian_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'saksi_tragedi';
        var db = event.target.result;
        var transaction = db.transaction("laporankejadian", "readonly");
        var objectStore = transaction.objectStore("laporankejadian");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                data.nilai.forEach((item) => {
                    i = parseInt(item.id)
                    $('#tablesk').append(
                    `<tr>
                            <td><input type="text" name="nama_saksi[]" onKeyup="changeNamaSaksi('${item.id}')" id="nama_saksi-${item.id}" value="${item.nama_saksi}" class="form-control" placeholder="Masukan Nama Saksi"></td>
                            <td><input type="text" name="nik_saksi[]" onkeyup="changeNikSaksi('${item.id}')" id="nik_saksi-${item.id}" value="${item.nik_saksi}" class="form-control" placeholder="Masukan Nik Saksi"></td>
                            <td><input type="text" name="departement_saksi[]" onkeyup="changeDepartementSaksi('${item.id}')" value="${item.departement_saksi}" id="departement_saksi-${item.id}" class="form-control" placeholder="Masukan Departemen Saksi"></td>
                            <td><input type="text" name="keterangan_saksi[]" onkeyup="changeKeteranganSaksi('${item.id}')" value="${item.keterangan_saksi}" id="keterangan_saksi-${item.id}" class="form-control" placeholder="Masukan Keterangan Saksi"></td>
                            <td class="d-flex justify-content-center mx-auto">
                                <button type="button" data-id_saksi_tragedi="${i}" class="btn btn-sm btn-danger remove-table-row-sk"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                            </td>
                        </tr>`);
                })
            } else {
                console.log(_id, "Data not found");
            }
        };

        request.onerror = function(event) {
            console.log("Error retrieving data:", event.target.error);
        };
    };

    function changeNamaSaksi(id_saksi){
        var nama_saksi = $('#nama_saksi-'+id_saksi).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'saksi_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_saksi) {
                            item.nama_saksi = nama_saksi
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
    
    function changeNikSaksi(id_saksi){
        var nik_saksi = $('#nik_saksi-'+id_saksi).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'saksi_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_saksi) {
                            item.nik_saksi = nik_saksi
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

    function changeDepartementSaksi(id_saksi){
        var departement_saksi = $('#departement_saksi-'+id_saksi).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'saksi_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_saksi) {
                            item.departement_saksi = departement_saksi
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

    function changeKeteranganSaksi(id_saksi){
        var keterangan_saksi = $('#keterangan_saksi-'+id_saksi).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'saksi_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_saksi) {
                            item.keterangan_saksi = keterangan_saksi
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

    $(document).on('click','.remove-table-row-sk', function(){

        var id_saksi_tragedi = $(this).data('id_saksi_tragedi')

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'saksi_tragedi';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    var _nilai = data.nilai.filter(item => {
                        if(item.id != id_saksi_tragedi) {
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

    // Untuk Dokumentasi Kejadian
    $('#adddk').click(function(){
        ++i_dokumentasi;
        $('#tabledk').append(
            `<tr>
                <td style="max-width: 250px">
                    <input accept="image/png, image/jpeg" class="form-control foto-kejadian_${i_dokumentasi}" onchange="previewLaporanKejadian('${i_dokumentasi}')" type="file" data-key="${i_dokumentasi}" id="dokumentasi_kejadian-${i_dokumentasi}" multiple>
                    <div class="mt-4">
                        <div class="row gambar-laporan" id="kejadian-preview-${i_dokumentasi}">
                        </div>
                        <div class="textarea_image_container" id="kejadian_value-${i_dokumentasi}" style="display: none;"></div>
                    </div>
                </td>
                <td><textarea class="form-control" onKeyup="changeKeteranganKejadian('${i_dokumentasi}')" id="keterangan_kejadian-${i_dokumentasi}" name="keterangan_kejadian[]" placeholder="Masukan Keterangan Kejadian" rows="3"></textarea></td>
                <td class="d-flex justify-content-center mx-auto">
                    <button type="button" data-id_kejadian="${i_dokumentasi}" class="btn btn-sm btn-danger remove-table-row-dk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                </td>

            </tr>`);

            // Preview Multiple Gambar
            // $(`.foto-introgasi_${i_dokumentasi}`).on("change", function () {
            //     var key = $(this).data('key');
            //     previewFoto(this, key);
            // });

            var database = window.indexedDB.open("kejadian_security", 20);
            
            // Save data to indexeddb
            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("laporankejadian", "readwrite");
                let objectStore = transaction.objectStore("laporankejadian");

                let request = objectStore.get('dokumentasi_kejadian');

                request.onsuccess = function(event) {
                    let data = event.target.result;
                    if (data) {
                        var _isi = data.nilai;
                        _isi.push({
                            id: i_dokumentasi,
                            foto_kejadian: [],
                            keterangan:''
                        })
                        data.nilai = _isi;
                        objectStore.put(data);
                        console.log("Data berhasil diubah");
                    } else {
                        var nilai = [];
                        nilai.push({
                            id: i_dokumentasi,
                            foto_kejadian: [],
                            keterangan:''
                        })
                        objectStore.put({ id: 'dokumentasi_kejadian', nilai: nilai });
                        console.log("Data berhasil dibuat");
                    }

                };

                request.onerror = function(event) {
                    console.log("Data gagal disimpan");
                };
            };
    });

    // Preview Multiple Gambar
    // function previewFoto(input, key) {
    //     var previewContainer = document.querySelector(`.preview-images_${key}`);
        
    //     previewContainer.innerHTML = "";
        
    //     if (input.files) {
    //         Array.from(input.files).forEach(function (file) {
    //             var reader = new FileReader();

    //             reader.onload = function (e) {
    //                 var img = document.createElement("img");
    //                 img.src = e.target.result;
    //                 img.style.width = "200px";
    //                 img.style.height = "150px";
    //                 img.style.display = "flex";
    //                 img.style.margin = "auto auto 20px 20px";
    //                 img.className = `preview-images_${key}`;

    //                 previewContainer.appendChild(img);
    //             };

    //             reader.readAsDataURL(file);
    //         });
    //     }
        
    // }

    // Untuk memanggil function preview multiple foto
    // $(`.foto-introgasi_${i_dokumentasi}`).on("change", function () {
    //     var key = $(this).data('key');
    //     previewFoto(this, key);
    // });

    var database = window.indexedDB.open("kejadian_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'dokumentasi_kejadian';
        var db = event.target.result;
        var transaction = db.transaction("laporankejadian", "readonly");
        var objectStore = transaction.objectStore("laporankejadian");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                data.nilai.forEach((item) => {
                    i_dokumentasi = parseInt(item.id)
                    $('#tabledk').append(
                        `<tr>
                            <td style="max-width: 250px">
                                <input accept="image/png, image/jpeg" class="form-control foto-kejadian_${item.id}" onchange="previewLaporanKejadian('${item.id}')" type="file" data-key="${item.id}" id="dokumentasi_kejadian-${item.id}" multiple>
                                <div class="mt-4">
                                    <div class="row gambar-laporan" id="kejadian-preview-${item.id}">
                                    </div>
                                    <div class="textarea_image_container" id="kejadian_value-${item.id}" style="display: none;"></div>
                                </div>
                            </td>
                            <td><textarea class="form-control" onKeyup="changeKeteranganKejadian('${item.id}')" id="keterangan_kejadian-${item.id}" name="keterangan_kejadian[]" placeholder="Masukan Keterangan Kejadian" rows="3">${item.keterangan}</textarea></td>
                            <td class="d-flex justify-content-center mx-auto">
                                <button type="button" data-id_kejadian="${item.id}" class="btn btn-sm btn-danger remove-table-row-dk mt-3"><i class="ri-delete-bin-2-line" style="font-size: 20px;"></i></button>
                            </td>
                        </tr>`);

                        item.foto_kejadian.forEach(fotoItem => {
                            let image = document.createElement('img');
                            image.src = fotoItem;
                            image.style.width = '100%';
                            image.style.height = '100%';
                            image.style.objectFit = 'contain';

                            var imageInner = document.createElement('div');
                            imageInner.style.marginBottom = '2px';
                            // Position relative
                            imageInner.style.position = 'relative';
                            imageInner.classList.add('col-6');
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
                                var database = window.indexedDB.open("kejadian_security", 20);

                                // Save data to indexeddb
                                database.onsuccess = function(event) {
                                    let db = event.target.result;
                                    let transaction = db.transaction("laporankejadian", "readwrite");
                                    let objectStore = transaction.objectStore("laporankejadian");
                                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                    // Change only image
                                    let request = objectStore.get('dokumentasi_kejadian');
                                    request.onsuccess = function(event) {
                                        let data = event.target.result;
                                        // if (data) {
                                        //     var foto_kejadian = data.foto_kejadian;
                                        //     if (foto_kejadian == null) {
                                        //         foto_kejadian = [];
                                        //     }

                                        //     var index = foto_kejadian.indexOf(fotoItem);
                                        //     if (index > -1) {
                                        //         foto_kejadian.splice(index, 1);
                                        //     }

                                        //     data.foto_kejadian = foto_kejadian;
                                        //     objectStore.put(data);
                                        // }

                                        if (data) {
                                            data.nilai.map((__item) => {
                                                if(__item.id == item.id) {
                                                    var foto_kejadian = __item.foto_kejadian;
                                                    if (foto_kejadian == null) {
                                                        foto_kejadian = [];
                                                    }

                                                    var index = foto_kejadian.indexOf(fotoItem);
                                                    if (index > -1) {
                                                        foto_kejadian.splice(index, 1);
                                                    }

                                                    __item.foto_kejadian = foto_kejadian;
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

                                $('#gambar_kejadian_'+item.id).remove();

                                // Delete image from DOM
                                this.parentElement.remove();
                            }
                            imageInner.appendChild(deleteButton);

                            document.getElementById('kejadian-preview-'+item.id).appendChild(imageInner);

                            // var textareaImage = document.createElement('input');
                            // textareaImage.setAttribute("type", "hidden");
                            // textareaImage.name = 'dokumentasi_kejadian['+ item.id +'][]';
                            // textareaImage.setAttribute("value", fotoItem);

                            var textareaImage = document.createElement('textarea');
                            textareaImage.name = 'dokumentasi_kejadian['+ item.id +'][]';
                            textareaImage.id = 'gambar_kejadian_'+item.id;
                            textareaImage.value = fotoItem;

                            document.getElementById('kejadian_value-'+item.id).appendChild(textareaImage);
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

    function changeKeteranganKejadian(id_kejadian){
        var keterangan = $('#keterangan_kejadian-'+id_kejadian).val()

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'dokumentasi_kejadian';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    data.nilai.map((item) => {
                        if(item.id == id_kejadian) {
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
    var database = window.indexedDB.open("kejadian_security", 20);

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

    // Untuk Foto Kejadian
    const previewLaporanKejadian = async (id_kejadian) =>
    {
        var files = document.getElementById('dokumentasi_kejadian-'+id_kejadian).files;

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
                    var database = window.indexedDB.open("kejadian_security", 20);

                    // Save data to indexeddb
                    database.onsuccess = function(event) {
                        let db = event.target.result;
                        let transaction = db.transaction("laporankejadian", "readwrite");
                        let objectStore = transaction.objectStore("laporankejadian");
                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                        // Change only image
                        let request = objectStore.get(id_kejadian);
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

                    $('#gambar_kejadian_'+id_kejadian).remove();

                    // Delete image from DOM
                    this.parentElement.remove();
                }

                imageInner.appendChild(deleteButton);

                document.getElementById('kejadian-preview-'+id_kejadian).appendChild(imageInner);

                // var textareaImage = document.createElement('input');
                // textareaImage.setAttribute("type", "hidden");
                // textareaImage.name = 'dokumentasi_kejadian['+ id_kejadian +'][]';
                // textareaImage.setAttribute("value", base64);

                var textareaImage = document.createElement('textarea');
                textareaImage.name = 'dokumentasi_kejadian['+ id_kejadian +'][]';
                textareaImage.id = 'gambar_kejadian_'+id_kejadian;
                textareaImage.value = base64;

                document.getElementById('kejadian_value-'+id_kejadian).appendChild(textareaImage);

                // Clear input file
                document.getElementById('dokumentasi_kejadian-'+id_kejadian).value = '';

                // Save image to indexeddb
                var database = window.indexedDB.open("kejadian_security", 20);

                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("laporankejadian", "readwrite");
                    let objectStore = transaction.objectStore("laporankejadian");
                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                    // Change only image
                    let request = objectStore.get('dokumentasi_kejadian');
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
                                if(item.id == id_kejadian) {
                                    var _foto_kejadian = item.foto_kejadian;
                                    _foto_kejadian.push(base64) 
                                    item.foto_kejadian = _foto_kejadian;
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

    $(document).on('click','.remove-table-row-dk', function(){

        var id_kejadian = $(this).data('id_kejadian')

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = 'dokumentasi_kejadian';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readwrite");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    // document.getElementById(_id).value = data.nilai;
                    var _isi = data.nilai.filter(item => {
                        if(item.id != id_kejadian) {
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
            var formFileMultiple = $("#formFileMultiple").val();
            var keterangan_kejadian = $("#keterangan_kejadian").val();

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
            }else if(formFileMultiple == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'Foto Kejadian Harus Diisi',
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
            text: "Ingin menghapus semua isi form input data berita acara laporan kejadian ini!",
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
                        'Semua isi form input data berita acara laporan kejadian ini berhasil dihapus.',
                        "success"
                    );
                    const request = window.indexedDB.deleteDatabase('kejadian_security');
                    window.location.reload()

                }

            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Dibatalkan!',
                    'Proses menghapus semua isi form input data berita acara laporan kejadian ini',
                    'error'
                );
            }
        });
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

    function changeKejadian(_id)
    {
        let nilai = document.getElementById(_id).value;

        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("laporankejadian", "readwrite");
            let objectStore = transaction.objectStore("laporankejadian");

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

    // Untuk Foto Kejadian
    const previewKejadian = async (_id) =>
    {
        var files = document.getElementById('dokumentasi_kejadian-0').files;

        // No files selected
        if (!files.length) return;

        // We'll store the files in this data transfer object
        const dataTransfer = new DataTransfer();

        let file = document.getElementById('dokumentasi_kejadian-0').files[0];
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
            imageInner.classList.add('col-6');
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
                var database = window.indexedDB.open("kejadian_security", 20);

                // Save data to indexeddb
                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("laporankejadian", "readwrite");
                    let objectStore = transaction.objectStore("laporankejadian");
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

                $('#gambar_kejadian_0').remove();

                // Delete image from DOM
                this.parentElement.remove();
            }
            imageInner.appendChild(deleteButton);

            document.getElementById('kejadian-preview-0').appendChild(imageInner);

            // var textareaImage = document.createElement('input');
            // textareaImage.setAttribute("type", "hidden");
            // textareaImage.name = 'dokumentasi_kejadian[0][]';
            // textareaImage.setAttribute("value", base64);

            var textareaImage = document.createElement('textarea');
            textareaImage.name = 'dokumentasi_kejadian[0][]';
            textareaImage.id = 'gambar_kejadian_0';
            textareaImage.value = base64;

            document.getElementById('kejadian_value-0').appendChild(textareaImage);

            // Clear input file
            document.getElementById('dokumentasi_kejadian-0').value = '';

            // Save image to indexeddb
            var database = window.indexedDB.open("kejadian_security", 20);

            database.onsuccess = function(event) {
                let db = event.target.result;
                let transaction = db.transaction("laporankejadian", "readwrite");
                let objectStore = transaction.objectStore("laporankejadian");
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

    // Untuk menampilkan gambar Foto Kejadian
    var database = window.indexedDB.open("kejadian_security", 20);
        
    // Retrieve data from indexedDB
    database.onsuccess = function(event) {
        var _id = 'dokumentasi_kejadian-0';
        var db = event.target.result;
        var transaction = db.transaction("laporankejadian", "readonly");
        var objectStore = transaction.objectStore("laporankejadian");

        var request = objectStore.get(_id);

        request.onsuccess = function(event) {
            var data = event.target.result;
            if (data) {
                // document.getElementById(_id).value = data.nilai;
                if (data.images) {
                        document.getElementById('kejadian-preview-0').innerHTML = '';
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
                            imageInner.classList.add('col-6');
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
                                var database = window.indexedDB.open("kejadian_security", 20);

                                // Save data to indexeddb
                                database.onsuccess = function(event) {
                                    let db = event.target.result;
                                    let transaction = db.transaction("laporankejadian", "readwrite");
                                    let objectStore = transaction.objectStore("laporankejadian");
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

                                $('#gambar_kejadian_0').remove();

                                // Delete image from DOM
                                this.parentElement.remove();
                            }
                            imageInner.appendChild(deleteButton);

                            document.getElementById('kejadian-preview-0').appendChild(imageInner);

                            // var textareaImage = document.createElement('input');
                            // textareaImage.setAttribute("type", "hidden");
                            // textareaImage.name = 'dokumentasi_kejadian[0][]';
                            // textareaImage.setAttribute("value", _image);

                            var textareaImage = document.createElement('textarea');
                            textareaImage.name = 'dokumentasi_kejadian[0][]';
                            textareaImage.id = 'gambar_kejadian_0';
                            textareaImage.value = _image;

                            document.getElementById('kejadian_value-0').appendChild(textareaImage);
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
    @foreach(['danru', 'jenis_kejadian', 'nama_korban', 'nik_korban', 'perusahaan_korban', 'bagian_korban', 'lokasi_kejadian', 'yang_terjadi', 'nama_terlapor', 'umur_terlapor', 'ttl_terlapor', 'pekerjaan_terlapor', 'kelurahan_terlapor', 'kecamatan_terlapor', 'provinsi_terlapor', 'status_terlapor', 'agama_terlapor', 'kebangsaan_terlapor', 'no_ktp_terlapor', 'no_simc_terlapor', 'no_hp_terlapor', 'bagaimana_terjadi', 'mengapa_terjadi', 'alamat_terlapor', 'fakta_kejadian-0', 'nama_saksi-0', 'nik_saksi-0', 'departement_saksi-0', 'keterangan_saksi-0', 'keterangan_kejadian-0', 'uraian_kejadian', 'tindakan_pengamanan', 'hasil_daritindakan', 'saran', 'jenis_kejadian_lainnya'] as $id)
        var database = window.indexedDB.open("kejadian_security", 20);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var _id = '{{ $id }}';
            var db = event.target.result;
            var transaction = db.transaction("laporankejadian", "readonly");
            var objectStore = transaction.objectStore("laporankejadian");

            var request = objectStore.get(_id);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    @if($id == 'uraian_kejadian')
                        quillUraianKejadian.root.innerHTML = data.nilai
                    @else
                        document.getElementById(_id).value = data.nilai;
                    @endif

                    @if($id == 'tindakan_pengamanan')
                        quillTindakanPengamanan.root.innerHTML = data.nilai
                    @else
                        document.getElementById(_id).value = data.nilai;
                    @endif
                    
                    @if($id == 'hasil_daritindakan')
                        quillHasilDariTindakan.root.innerHTML = data.nilai
                    @else
                        document.getElementById(_id).value = data.nilai;
                    @endif

                    @if($id == 'saran')
                        quillSaran.root.innerHTML = data.nilai
                    @else
                        document.getElementById(_id).value = data.nilai;
                    @endif

                    if (_id == 'jenis_kejadian') {
                        if (data.nilai === 'lainnya') {
                            document.querySelector('#jenis_kejadian_container').innerHTML = `<input class="form-control mb-3" onchange="changeKejadian('jenis_kejadian_lainnya')" name="jenis_kejadian_lainnya" id="jenis_kejadian_lainnya" placeholder="Jenis Kejadian lainnya" required />`;
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
@endpush