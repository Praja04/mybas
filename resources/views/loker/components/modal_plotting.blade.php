<div class="modal fade" id="modalPlotting" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-8 px-8 pb-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45 symbol-light-primary mr-4" data-toggle="tooltip"
                        title="Daftarkan alokasi baru atau pindahkan (relokasi) loker karyawan">
                        <span class="symbol-label">
                            <i class="flaticon2-plus text-primary icon-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-0" id="modalPlottingTitle">
                            Formulir Alokasi & Relokasi Loker
                        </h5>
                        <small class="text-muted font-weight-bold">Pindai Kartu Identitas (RFID) atau ketik NIK
                            Karyawan</small>
                    </div>
                </div>
                <button type="button" class="close btn btn-xs btn-icon btn-light btn-hover-primary"
                    data-dismiss="modal">
                    <i class="ki ki-close"></i>
                </button>
            </div>

            <form id="formPlotting">
                @csrf
                <div class="modal-body p-8">
                    <div class="row">
                        <div class="col-md-7 pr-md-10 border-right">
                            <div class="form-group mb-8">
                                <label class="font-weight-bold text-dark-75 mb-2">Identitas Karyawan</label>
                                <div class="input-group input-group-lg input-search-nik shadow-none"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Silakan pindai (tap) kartu pada RFID Reader atau ketik NIK secara manual">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0 px-5">
                                            <i class="fas fa-id-card text-primary"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="nik" id="plot_nik"
                                        class="form-control border-0 bg-light font-weight-bold pl-4"
                                        placeholder="Pindai Kartu atau Ketik NIK..." required autocomplete="off"
                                        style="height: 55px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary font-weight-bolder px-8" type="button"
                                            id="btnCariKaryawan" onclick="cariKaryawan()">
                                            <i class="fas fa-search mr-2"></i> CARI
                                        </button>
                                    </div>
                                </div>
                                <span class="form-text text-muted font-size-xs mt-3 ml-1">
                                    <i class="fa fa-info-circle text-primary mr-1"></i>
                                    Sistem mendukung <b>RFID Scanner</b>. Klik kolom NIK lalu pindai kartu identitas.
                                </span>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Nama Lengkap</label>
                                    <input type="text" id="plot_nama"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="Data Otomatis">
                                </div>
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Departemen</label>
                                    <input type="text" id="plot_dept"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="Data Otomatis">
                                </div>
                            </div>

                            <div class="form-group mb-4" data-toggle="tooltip" data-placement="top"
                                title="Loker saat ini akan otomatis berstatus tersedia (kosong) setelah proses penempatan baru disimpan">
                                <label class="text-danger font-weight-bolder font-size-sm">Informasi Loker Saat
                                    Ini</label>
                                <div
                                    class="d-flex align-items-center bg-light-danger rounded-xl p-4 border border-danger-o-20">
                                    <div class="symbol symbol-30 symbol-danger mr-3">
                                        <span class="symbol-label"><i class="fas fa-history font-size-sm"></i></span>
                                    </div>
                                    <input type="text" id="plot_loker_lama"
                                        class="form-control-plaintext font-weight-bolder text-danger py-0" readonly
                                        value="Belum dialokasikan">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Jenis Kelamin</label>

                                    <div id="plot_gender_label_container">
                                        <input type="text" id="plot_gender_label"
                                            class="form-control form-control-solid border-0" readonly placeholder="-">
                                    </div>

                                    <div id="plot_gender_select_container" style="display: none;">
                                        <select id="plot_gender_val_manual"
                                            class="form-control font-weight-bolder border-primary">
                                            <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                            <option value="L">LAKI-LAKI</option>
                                            <option value="P">PEREMPUAN</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="gender" id="plot_gender_val">
                                </div>

                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Kategori</label>

                                    <div id="plot_kategori_label_container">
                                        <input type="text" id="plot_kategori_label"
                                            class="form-control form-control-solid border-0" readonly placeholder="-">
                                    </div>

                                    <div id="plot_kategori_select_container" style="display: none;">
                                        <select id="plot_kategori_val_manual"
                                            class="form-control font-weight-bolder border-primary">
                                            <option value="" selected disabled>Pilih Kategori</option>
                                            <option value="staff">STAFF</option>
                                            <option value="non_staff">NON-STAFF</option>
                                            <option value="mitra">MITRA KERJA</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="kategori_karyawan" id="plot_kategori_val">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 pl-md-10 text-center d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-5 text-left">Preview & Validasi</h6>
                                <div class="symbol symbol-120 symbol-lg-150 mb-6 shadow-sm p-1 bg-white"
                                    style="border-radius: 20px; border: 1px solid #eee;" data-toggle="tooltip"
                                    title="Pastikan foto sesuai dengan pemegang kartu ID">
                                    <img id="plot_foto_img" src="{{ asset('assets/media/users/default.jpg') }}"
                                        alt="Foto" style="border-radius: 18px; object-fit: cover;">
                                </div>

                                <div class="form-group text-left mt-5">
                                    <label class="font-weight-bolder text-primary mb-2">Pemilihan Loker Tujuan</label>
                                    <div class="input-group input-group-solid shadow-none"
                                        style="border-radius: 12px; overflow: hidden;" data-toggle="tooltip"
                                        title="Hanya menampilkan daftar loker yang tersedia sesuai kriteria karyawan">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light-primary border-0">
                                                <i class="fas fa-sign-in-alt text-primary"></i>
                                            </span>
                                        </div>
                                        <select name="no_loker" id="select_no_loker"
                                            class="form-control border-0 font-weight-bolder" style="height: 50px;">
                                            <option value="">-- Pilih Loker Tersedia --</option>
                                        </select>
                                    </div>
                                    <p class="text-muted font-size-xs mt-3">
                                        <span class="badge badge-light-primary badge-inline">Smart Filtering</span><br>
                                        Loker difilter secara sistematis menyesuaikan regulasi area dan kategori
                                        karyawan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light-o-50 border-0 py-6 px-8">
                    <button type="button" class="btn btn-text-dark-50 btn-hover-light-primary font-weight-bold px-10"
                        data-dismiss="modal">Batal</button>
                    <button type="button" id="btnSimpanPlot" onclick="simpanPlotting()"
                        class="btn btn-primary font-weight-bolder px-12 shadow-sm" disabled data-toggle="tooltip"
                        data-theme="dark" title="Tombol akan aktif setelah NIK tervalidasi dan Loker tujuan terpilih">
                        <i class="fas fa-check-double mr-2"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>