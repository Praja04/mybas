@extends('layouts.base')

@push('styles')
    <style>
        .enlarged-text {
            font-size: 1.2em;
        }

        .enlarged-checkbox {
            transform: scale(1.2);
            margin-right: 5px;
        }

        .input-radio {
            box-shadow: 0px 0px 0px 1px #6d6d6d;
            font-size: 3em;
            width: 12px;
            height: 12px;
            margin-right: 7px;

            border: 4px solid #fff;
            background-clip: border-box;
            border-radius: 50%;
            appearance: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .input-radio.on:checked {
            box-shadow: 0px 0px 0px 4px #00eb27;
            background-color: #51ff6e;
        }

        .input-radio.off:checked {
            box-shadow: 0px 0px 0px 4px #eb0000;
            background-color: #ff5151;
        }
    </style>
@endpush


@section('content')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/introjs.min.css" rel="stylesheet">
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">
            <div class="col-md-3">
                <h3 style="margin-bottom: 13px">Data Catering</h3>
                <div class="card border">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <td>{{ $data->id_transaksi }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ $data->tanggal }}</td>
                                </tr>
                                <tr>
                                    <th>Catering</th>
                                    <td>{{ $data->catering }}</td>
                                </tr>
                                <tr>
                                    <th>Shift</th>
                                    <td>{{ $data->shift }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pengambilan Sampel</th>
                                    <td>
                                        @if ($data->status_pengambilan_sampel === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            @if ($data->status_pengambilan_sampel === 'menunggu approval')
                                                <span class="badge text-bg-success"
                                                    style="background-color: #d4ce13; color: white;">Menunggu
                                                    Approval</span>
                                            @else
                                                <span class="badge text-bg-danger"
                                                    style="background-color: #a80000; color: white;">Belum</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <th>Status Cek Kendaraan</th>
                                    <td>
                                        @if ($data->status_cek_kedatangan === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            <span class="badge text-bg-danger"
                                                style="background-color: #a80000; color: white;">Belum</span>
                                        @endif
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <button onClick="showModalCreateCatering()" type="button"
                    class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal"
                    data-bs-target="#modalCreateGroup">
                    <i class="mdi mdi-plus"></i>
                    Tambah Catering
                </button> --}}
            </div>
            <div class="col-lg-9">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data Pengambilan Sampel
                                <span class="d-block text-muted pt-2 font-size-sm">Atur Pengambilan Sampel</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="card-toolbar">
                                {{-- Ini adalah komentar Blade --}}
                                <?php // var_dump($id_transaksi_pengambilan_sampel, $data->id_transaksi);
                                ?>
                                @if ($id_transaksi_pengambilan_sampel !== $data->id_transaksi || $data->status_pengambilan_sampel == 'menunggu approval')
                                    <a id="uploadJumlahSample" href="javascript:" class="btn btn-primary font-weight-bolder"
                                        onClick="showModalCreateNew()">
                                        <i class="fa fa-plus-square"></i> Upload Sampel
                                    </a>
                                @else
                                    <span class="badge badge-success"
                                        style="{{ $data->status_pengambilan_sampel == 'menunggu approval' ? 'display: none;' : '' }}"
                                        style="font-size: 12px;">
                                        <div class="stats-icon green mb-2">
                                            <img src="{{ asset('assets/mazer/dist/assets/compiled/png/man-like.png') }}"
                                                alt="mobil truck" width="40px">
                                        </div>
                                        Sekarang silahkan klik tombol <br> tambah jam dan foto sampel keluar
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="jumlahPesanan" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-md-3">Tanggal Jam Masuk</th>
                                        <th class="col-sm-2">Foto Sampel Masuk </th>
                                        {{-- <th class="col-sm-2">Keterangan Menu Masuk</th> --}}
                                        <th class="col-sm-2">Tanggal Jam Keluar</th>
                                        <th class="col-sm-2">Foto Sampel Keluar</th>
                                        <th class="col-sm-1">Keterangan</th>
                                        <th class="col-sm-2">Masa Simpan</th>
                                        <th class="col-sm-2">Status</th>
                                        {{-- <th class="col-sm-2">Kategori Staff</th> --}}
                                        {{-- <th width="8%"><i class="fa fa-tools text-dark-75"></i></th> <!-- Kolom Aksi --> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- data table kedatangan lauk --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="button-group pt-4" style="display: flex; gap: 20px;">
                            <a href="{{ url('/update/pengambilan-sampel/' . $data->id_transaksi) }}" id="submitPenilaian"
                                type="submit" style="z-index: 99;" class="btn btn-primary"
                                onclick="return confirm('Apakah Anda Yakin Akan Mengirim Data Sampel?');">
                                <i class="fa fa-paper-plane"></i>Kirim Data Pengambilan Sampel
                            </a>
                            <a id="penilaianTerkirim" class="btn btn-success" style="display: none;">
                                <i class="fa fa-check"></i> Data Pengambilan Sampel Terkirim
                            </a>
                            <a href="/cateringbas/pengambilan-sampel" id="kembalikehome" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    {{-- modal create sample catering --}}
    <div class="modal fade" id="modalPesananCatering" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-pesanan" id="exampleModalLabel"><i class="fa fa-plus-square"></i> Tambah foto
                        sebelum pengambilan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadPesananCatering">
                        <input type="hidden" name="id_transaksi" value="{{ $data->id_transaksi }}">

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right" for="foto-before">Foto Sebelum</label>
                            <div class="col-lg-9">
                                <input type="file" name="foto_before1" id="foto-before1" multiple
                                    data-max-file-size="8MB">
                                <input type="file" name="foto_before2" id="foto-before2" multiple
                                    data-max-file-size="8MB">
                            </div>
                        </div>

                        <!-- Tabel Menu Makanan -->
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <table class="table table-bordered" id="tableSummary">
                                    <thead>
                                        <tr>
                                            <th colspan="3" style="text-align: center;">
                                                Centang Semua
                                                <div style="display: flex; justify-content: center; gap: 13px;">
                                                    <div class="form-check pt-4">
                                                        <input class="enlarged-checkbox input-radio on" type="radio"
                                                            id="checkAllBaik" name="pengambilanmenu">
                                                        <label for="checkAllBaik">Ambil</label>
                                                    </div>
                                                    <div class="form-check pt-4">
                                                        <input class="enlarged-checkbox input-radio off" type="radio"
                                                            id="checkAllTidakBaik" name="pengambilanmenu">
                                                        <label for="checkAllTidakBaik">Tidak</label>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>

                                        <tr>
                                            <th>Kategori Staff</th>
                                            <th>Menu Makanan</th>
                                            <th>Ambil / Tidak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $isKategoriStaff = false;
                                        @endphp

                                        @foreach ($MenuMakanan as $index => $menu)
                                            <tr>
                                                <td rowspan="3">
                                                    @if ($menu['kategori_staff'] !== $isKategoriStaff)
                                                        {{ $menu['kategori_staff'] }}
                                                        @php
                                                            $isKategoriStaff = $menu['kategori_staff'];
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td class="enlarged-text">{{ $menu['nama_menu'] }}</td>
                                                <td>
                                                    <div style="display: flex; gap: 15px;">
                                                        <div class="form-check">
                                                            <input
                                                                class="input-radio on form-check-input baik-checkbox enlarged-checkbox mr-4"
                                                                type="radio" value="1"
                                                                name="pengambilan-{{ $menu['jenis_menu'] }}-{{ $menu['id'] }}"
                                                                id="baik{{ $index }}">
                                                            <label class="form-check-label enlarged-text ml-4 mr-4"
                                                                for="baik{{ $index }}">Ambil</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input
                                                                class="input-radio off form-check-input tidak-baik-checkbox enlarged-checkbox mr-4"
                                                                type="radio" value="0"
                                                                name="pengambilan-{{ $menu['jenis_menu'] }}-{{ $menu['id'] }}"
                                                                id="tidakBaik{{ $index }}">
                                                            <label class="form-check-label enlarged-text ml-4"
                                                                for="tidakBaik{{ $index }}">Tidak</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Space for additional rows if needed -->
                                            <tr></tr>
                                            <tr></tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                        {{-- <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right" for="keterangan-menu">Keterangan
                                Menu</label>
                            <div class="col-lg-9">
                                <input name="keterangan_menu" required placeholder="hasil sebelum pengambilan"
                                    class="form-control" type="text" id="edit-keterangan-menu">
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right" for="kategori-staff">Kategori Staff</label>
                            <div class="col-lg-9">
                                <select required name="kategori_staff" id="kategori-staff" class="form-control">
                                    <option value=""></option>
                                    <option value="staff">Staff</option>
                                    <option value="non staff">Non Staff</option>
                                </select>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <button id="submitButton" type="button" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Tambah Data Sampel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- modal edit jumlah pesanan catering --}}
    <div class="modal fade" id="editmodalPesananCatering" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalSizeSm" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-edit-pesanan" id="editModalLabel"><i class="fa fa-plus-square"></i>Tambah foto
                        sesudah pengambilan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPesananCatering">
                        <input type="hidden" name="id_transaksi" value="{{ $data->id_transaksi }}">
                        <input type="hidden" name="id" id="edit-id">
                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="foto-before">Foto Sebelum</label>
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="image">Image Before</label>
                                    <img src="" id="edit-image-preview-before" class="clickable-image"
                                        style="max-width: 80px; height: auto;" />
                                    <input type="file" name="edit_foto_before" id="foto-before" multiple
                                        data-max-file-size="3MB">
                                </div>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="foto-after">Foto Sesudah</label>
                            <div class="col-9">
                                <div class="form-group">
                                    {{-- <label for="image">Image After</label> --}}
                                    <img src="" id="edit-image-preview-after1" class="clickable-image"
                                        style="max-width: 80px; height: auto;" />
                                    <input type="file" name="foto_after1" id="foto-after1" data-max-file-size="3MB">
                                    <img src="" id="edit-image-preview-after2" class="clickable-image"
                                        style="max-width: 80px; height: auto;" />
                                    <input type="file" name="foto_after2" id="foto-after2" data-max-file-size="3MB">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12">
                                <table class="table table-bordered" id="tableSummary">
                                    <thead>
                                        <tr>
                                            <th>Kategori Staff</th>
                                            <th>Menu Makanan</th>
                                            <th>
                                                Centang Semua Baik / Tidak Baik
                                                <br />
                                                <div style="display: flex; gap: 13px;">
                                                    <div class="form-check pt-4">
                                                        <input class="enlarged-checkbox input-radio on" type="radio"
                                                            id="checkAllBaik" name="penilaianbaik">
                                                        <label for="checkAllBaik">Baik</label>
                                                    </div>
                                                    <div class="form-check pt-4">
                                                        <input class="enlarged-checkbox input-radio off" type="radio"
                                                            id="checkAllTidakBaik" name="penilaianbaik">
                                                        <label for="checkAllTidakBaik">Tidak Baik</label>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $lastKategoriStaff = null;
                                        @endphp

                                        @foreach ($menuPengambilans as $index => $menu)
                                            <tr>
                                                @if ($menu['kategori_staff'] !== $lastKategoriStaff)
                                                    @php
                                                        $count = collect($menuPengambilans)
                                                            ->where('kategori_staff', $menu['kategori_staff'])
                                                            ->count();
                                                    @endphp
                                                    <td rowspan="{{ $count }}">{{ $menu['kategori_staff'] }}</td>
                                                    @php
                                                        $lastKategoriStaff = $menu['kategori_staff'];
                                                    @endphp
                                                @endif
                                                <td class="enlarged-text">{{ $menu['nama_menu'] }}</td>
                                                <td>
                                                    <!-- Form inputs for Baik/Tidak Baik -->
                                                    <div style="display: flex; gap: 15px;">
                                                        <div class="form-check">
                                                            <input
                                                                class="input-radio on form-check-input baik-checkbox enlarged-checkbox mr-4"
                                                                type="radio" value="1"
                                                                name="penilaian-{{ $menu['jenis_menu'] }}-{{ $menu['id'] }}"
                                                                id="baik{{ $index }}">
                                                            <label class="form-check-label enlarged-text ml-4 mr-4"
                                                                for="baik{{ $index }}">Baik</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input
                                                                class="input-radio off form-check-input tidak-baik-checkbox enlarged-checkbox mr-4"
                                                                type="radio" value="0"
                                                                name="penilaian-{{ $menu['jenis_menu'] }}-{{ $menu['id'] }}"
                                                                id="tidakBaik{{ $index }}">
                                                            <label class="form-check-label enlarged-text ml-4 mr-4"
                                                                for="tidakBaik{{ $index }}">Tidak Baik</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-right" for="keterangan-menu">Keterangan
                            </label>
                            <div class="col-lg-9">
                                <input name="keterangan_menu_keluar" required placeholder="hasil sesudah pengambilan"
                                    class="form-control" type="text" id="keterangan-menu-keluar">
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="keterangan-menu">Keterangan Menu</label>
                            <div class="col-9">
                                <input name="keterangan_menu" required placeholder="keterangan menu" class="form-control"
                                    type="text" value="" id="edit-keterangan-menu">
                            </div>
                        </div> --}}

                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-staff">Kategori Staff</label>
                            <div class="col-9">
                                <select required name="kategori_staff" id="edit-kategori-staff" class="form-control">
                                    <option value=""></option>
                                    <option value="staff">Staff
                                    </option>
                                    <option value="non staff">Non Staff
                                    </option>
                                </select>
                            </div>
                        </div> --}}
                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="keterangan">Keterangan</label>
                            <div class="col-9">
                                <select required name="keterangan" id="keterangan" class="form-control">
                                    <option value=""></option>
                                    <option value="sesuai">Sesuai</option>
                                    <option value="tidak sesuai">Tidak Sesuai</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="editSubmitbutton" type="button" class="btn btn-primary"
                                    style="z-index: 99;"><i class="fa fa-paper-plane"></i>Tambah Sampel Keluar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- modal delete jumlah pesanan catering --}}
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    {{-- filepon --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/intro.min.js"></script>
    <script type="text/javascript">
        function showModalCreateNew() {
            $('#modal-title-pesanan').text('Upload Jumlah Pesanan Catering');
            $('#modalPesananCatering').modal('show');
        }

        function openEditModal(id, foto_before, foto_after, id_transaksi, keterangan, keterangan_menu, kategori_staff) {
            $('#edit-id').val(id);
            $('#edit-id-transaksi').val(id_transaksi);
            $('#edit-image-preview-before').attr('src', foto_before);
            $('#edit-image-preview-after').attr('src', foto_after);
            $('#edit-keterangan-menu').val(keterangan_menu);
            // console.log(status_pengambilan_sampel);
            $('#edit-keterangan-sampel option[value="' + keterangan + '"]').prop('selected', true);
            $('#edit-kategori-staff option[value="' + kategori_staff + '"]').prop('selected', true);
            $('#editmodalPesananCatering').modal('show');
        }

        // edit pesanan
        $(document).ready(function() {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageResize,
                FilePondPluginImageTransform,
                FilePondPluginFileValidateType
            );

            const inputElementAfter1 = document.querySelector('input[name="foto_after1"]');
                const pondAfter1 = FilePond.create(inputElementAfter1, {
                    imageResizeTargetWidth: 1920,
                    imageResizeTargetHeight: 1080, 
                    imageResizeMode: 'contain', 
                    imageTransformOutputMimeType: 'image/jpeg',
                    allowFileTypeValidation: true,
                    acceptedFileTypes: ['image/jpeg', 'image/png'],
                    fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                        if (type === 'image/jpeg' || type === 'image/png') {
                            resolve(type);
                        } else {
                            resolve('image/jpeg'); 
                        }
                    }),
                    maxFileSize: '8MB' 
                });

            // Initialize FilePond for foto_after2 with the same options
            const inputElementAfter2 = document.querySelector('input[name="foto_after2"]');
                const pondAfter2 = FilePond.create(inputElementAfter2, {
                    imageResizeTargetWidth: 1024,
                    imageResizeTargetHeight: 768,
                    imageResizeMode: 'contain',
                    imageTransformOutputMimeType: 'image/jpeg',
                    allowFileTypeValidation: true,
                    acceptedFileTypes: ['image/jpeg', 'image/png'],
                    fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                        if (type === 'image/jpeg' || type === 'image/png') {
                            resolve(type);
                        } else {
                            resolve('image/jpeg');
                        }
                    }),
                    maxFileSize: '8MB'
                });

            // Click event for submit button
            $('#editSubmitbutton').click(function(e) {
                e.preventDefault();

                // Fungsi untuk memeriksa apakah ada input radio yang dipilih
                function validateRadioInputs() {
                    const radioInputs = document.querySelectorAll('.input-radio');
                    let isRadioSelected = false;

                    radioInputs.forEach(input => {
                        if (input.checked) {
                            isRadioSelected = true;
                        }
                    });

                    return isRadioSelected;
                }

                // Memeriksa apakah ada input radio yang dipilih
                if (!validateRadioInputs()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Mohon lengkapi data',
                    });
                    // Hentikan AJAX jika input radio belum lengkap
                    return;
                }

                var data = new FormData($('#editPesananCatering')[0]);
                var filesAfter1 = pondAfter1.getFiles();
                var filesAfter2 = pondAfter2.getFiles();

                // Append files from FilePond instances to FormData
                if (filesAfter1.length > 0) {
                    filesAfter1.forEach((file) => {
                        data.append('files_after1[]', file.file);
                    });
                }
                if (filesAfter2.length > 0) {
                    filesAfter2.forEach((file) => {
                        data.append('files_after2[]', file.file);
                    });
                }

                var id = $('#edit-id').val();

                // AJAX request to server
                $.ajax({
                    url: '/cateringbas/sampel/edit/' + id,
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success === 1) {
                            var table = $('#jumlahPesanan').DataTable();
                            table.ajax.reload();
                            $('#editmodalPesananCatering').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim permintaan.',
                        });
                    }
                });
            });
        });


        // get pesanan
        $(document).ready(function() {
            var table = $('#jumlahPesanan').DataTable({
                paging: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '/cateringbas/sample-lauk/get-all/' + encodeURIComponent(
                        '{{ $data->id_transaksi }}'),
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'tanggal_jam_masuk'
                    },
                    {
                        // Combined column for foto_before_1 and foto_before_2
                        data: function(row) {
                            return [row.foto_before_1, row.foto_before_2];
                        },
                        render: function(data) {
                            return data.map(function(imageUrl) {
                                if (imageUrl) {
                                    return `<div class="image-container" style="margin-bottom: 10px;"><a href="${imageUrl}" class="venobox"><img src="${imageUrl}" class="clickable-image" style="max-width: 80px; height: auto;" /></a></div>`;
                                }
                                return '';
                            }).join('');
                        }
                    },
                    /* {
                        data: 'keterangan_menu',
                        render: function(data, type, row) {
                            if (data === 'baik') {
                                return '<span class="badge badge-success text-white">Baik</span>';
                            } else if (data === 'tidak  ') {
                                return '<span class="badge badge-danger text-white">Tidak Baik</span>';
                            }
                            return data;
                        }
                    }, */
                    {
                        data: 'tanggal_jam_keluar',
                        render: function(data) {
                            if (data === null) {
                                return '<span class="badge badge-warning text-white">Data<br> Belum Ditambahkan</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: function(row) {
                            return [row.foto_after_1, row.foto_after_2];
                        },
                        render: function(data) {
                            if (!data[0] && !data[1]) {
                                return '<span class="badge badge-warning text-white">Data<br> foto belum Ditambahkan</span>';
                            }

                            return data.map(function(imageUrl) {
                                if (imageUrl) {
                                    return `<div class="image-container" style="margin-bottom: 10px;"><a href="${imageUrl}" class="venobox"><img src="${imageUrl}" class="clickable-image" style="max-width: 80px; height: auto;" /></a></div>`;
                                }
                                return '';
                            }).join('');
                        }
                    },
                    {
                        data: 'keterangan_menu_keluar',
                        render: function(data, type, row) {
                            if (data === 'baik') {
                                return '<span class="badge badge-success text-white">Baik</span>';
                            } else if (data === 'tidak') {
                                return '<span class="badge badge-danger text-white">Tidak Baik</span>';
                            } else if (data === '') {
                                return '<span> - </span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'masa_simpan',
                        render: function(data) {
                            if (data == null) {
                                return '<span class="badge badge-warning text-white">Data <br>belum ditambahkan</span>';
                            } else {
                                return `<span class="badge badge-info text-white">${data}</span>`;
                            }
                        }
                    },
                    // {
                    //     data: 'kategori_staff'
                    // },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if ('{{ $data->status_pengambilan_sampel }}' == 'belum' && !data
                                .tanggal_jam_keluar) {
                                return `
                                <div class="btn-group">
                                    <a title="Edit" onClick="openEditModal('${row.id}', '${row.foto_before_1}', '${row.foto_before_2}', '${row.foto_after_1}', '${row.foto_after_2}', '${row.id_transaksi}', '${row.keterangan}', '${row.keterangan_menu}', '${row.kategori_staff}')" href="javascript:" class="btn btn-sm btn-primary text-white mx-2">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <a title="Hapus" onClick="deletePesanan('${row.id}')" href="javascript:" class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>`;
                            } else if ('{{ $data->status_pengambilan_sampel }}' == 'belum') {
                                return `
                                <div>
                                    <a title="Hapus" onClick="deletePesanan('${row.id}')" href="javascript:" class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>`;
                            } else {
                                return `<p class="badge badge-success">Data Sampel<br> Sudah Dikirim</p>`;
                            }
                        }
                    }

                ]
            });

            // Initialize VenoBox for images
            new VenoBox({
                selector: '.venobox',
                overlayClose: true,
            });
        });

        // delete pesanan
        function deletePesanan(id) {
            Swal.fire({
                title: 'Yakin mau dihapus?',
                text: "Data mungkin tidak bisa dikembalikan lagi setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Gak jadi',
                confirmButtonText: 'Hapus aja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus data dari server
                    $.ajax({
                        url: "{{ url('/cateringbas/sampel/delete/') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            if (response.success === 1) {
                                var table = $('#jumlahPesanan').DataTable();
                                $('#editmodalPesananCatering').modal(
                                    'hide');
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                })

                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim permintaan.',
                            });
                        }
                    })
                }
            });
        }

        // tambah pesanan 
        $(document).ready(function() {
            const inputElementBefore1 = document.querySelector('input[name="foto_before1"]');
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageResize,
                FilePondPluginImageTransform,
                FilePondPluginFileValidateType
            );

            const pondBefore1 = FilePond.create(inputElementBefore1, {
                imageResizeTargetWidth: 1920, 
                imageResizeTargetHeight: 1080, 
                imageResizeMode: 'contain', 
                imageTransformOutputMimeType: 'image/jpeg',
                allowFileTypeValidation: true,
                acceptedFileTypes: ['image/jpeg', 'image/png'], 
                fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                    if (type === 'image/jpeg' || type === 'image/png') {
                        resolve(type);
                    } else {
                        resolve('image/jpeg');
                    }
                }),
                maxFileSize: '8MB'
            });

            const inputElementBefore2 = document.querySelector('input[name="foto_before2"]');
            const pondBefore2 = FilePond.create(inputElementBefore2, {
                imageResizeTargetWidth: 1920,
                imageResizeTargetHeight: 1080, 
                imageResizeMode: 'contain', 
                imageTransformOutputMimeType: 'image/jpeg', 
                allowFileTypeValidation: true,
                acceptedFileTypes: ['image/jpeg', 'image/png'],
                fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                    if (type === 'image/jpeg' || type === 'image/png') {
                        resolve(type);
                    } else {
                        resolve('image/jpeg');
                    }
                }),
                maxFileSize: '8MB' 
            });

            $('#submitButton').click(function(e) {
                e.preventDefault();

                // Fungsi untuk memeriksa apakah ada input radio yang dipilih
                function validateRadioInputs() {
                    const radioInputs = document.querySelectorAll('.input-radio');
                    let isRadioSelected = false;

                    radioInputs.forEach(input => {
                        if (input.checked) {
                            isRadioSelected = true;
                        }
                    });

                    return isRadioSelected;
                }

                // Memeriksa apakah ada input radio yang dipilih
                if (!validateRadioInputs()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Mohon lengkapi data',
                    });
                    return; // Hentikan AJAX jika input radio belum lengkap
                }

                var data = new FormData($('#uploadPesananCatering')[0]);

                var filesBefore1 = pondBefore1.getFiles();
                if (filesBefore1.length > 0) {
                    filesBefore1.forEach((file) => {
                        data.append('files_before1[]', file.file);
                    });
                }

                var filesBefore2 = pondBefore2.getFiles();
                if (filesBefore2.length > 0) {
                    filesBefore2.forEach((file) => {
                        data.append('files_before2[]', file.file);
                    });
                }

                $.ajax({
                    type: "POST",
                    url: '{{ route('cateringbas.tambah.sampel') }}',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success === 1) {
                            var table = $('#jumlahPesanan').DataTable();
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(function() {
                                $('#modalPesananCatering').modal('hide');
                                table.ajax.reload();
                            });

                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim permintaan.',
                        });
                    }
                });
            });
        });


        $(document).ready(function() {
            $('#checkAllBaik').click(function() {
                if (this.checked) {
                    $('.baik-checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.baik-checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });

            $('#checkAllTidakBaik').click(function() {
                if (this.checked) {
                    $('.tidak-baik-checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.tidak-baik-checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });
        });

        $(document).ready(function() {
            // Checkbox handlers
            $('#checkAllBaik, #checkAllTidakBaik').click(function() {
                var targetClass = $(this).attr('id') === 'checkAllBaik' ? '.baik-checkbox' :
                    '.tidak-baik-checkbox';
                $(targetClass).prop('checked', this.checked);
                updateKeteranganMenu();
            });

            $('.baik-checkbox, .tidak-baik-checkbox').change(function() {
                updateKeteranganMenu();
            });

            function updateKeteranganMenu() {
                var jumlahBaik = $('.baik-checkbox:checked').length;
                var jumlahTidakBaik = $('.tidak-baik-checkbox:checked').length;
                var keterangan;

                if (jumlahBaik > jumlahTidakBaik) {
                    keterangan = 'baik';
                } else if (jumlahBaik < jumlahTidakBaik) {
                    keterangan = 'tidak';
                } else {
                    keterangan = '';
                }

                // $('#edit-keterangan-menu').val(keterangan);
                $('#keterangan-menu').val(keterangan);
                // $('#keterangan-menu-keluar').val(keterangan);
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->status_pengambilan_sampel }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'menunggu approval') {
                document.getElementById("submitPenilaian").style.display = "none";
                document.getElementById("uploadJumlahSample").style.display = "none";
                document.getElementById("penilaianTerkirim").style.display = "inline-block";
            }
        });
    </script>
@endpush
