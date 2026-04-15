<div class="modal fade" id="modalPlotting" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-8 px-8 pb-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45 symbol-light-primary mr-4" data-toggle="tooltip"
                        title="Gunakan form ini untuk pendaftaran unit baru atau relokasi karyawan">
                        <span class="symbol-label">
                            <i class="flaticon2-plus text-primary icon-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-0" id="modalPlottingTitle">
                            Penempatan Karyawan
                        </h5>
                        <small class="text-muted font-weight-bold">Kelola unit loker karyawan secara efisien</small>
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
                                <label class="font-weight-bold text-dark-75 mb-2">NIK Karyawan</label>
                                <div class="input-group input-group-lg input-search-nik shadow-none"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Input NIK secara teliti untuk sinkronisasi data foto dan divisi">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0 px-5">
                                            <i class="flaticon-users-1 text-primary"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="nik" id="plot_nik"
                                        class="form-control border-0 bg-light font-weight-bold pl-4"
                                        placeholder="Ketik NIK..." required autocomplete="off" style="height: 55px;"
                                        onkeypress="if(event.key === 'Enter') { event.preventDefault(); cariKaryawan(); }">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary font-weight-bolder px-8" type="button"
                                            id="btnCariKaryawan" onclick="cariKaryawan()">
                                            <i class="fas fa-search mr-2"></i> CARI
                                        </button>
                                    </div>
                                </div>
                                <span class="form-text text-muted font-size-xs mt-3 ml-1">
                                    <i class="fa fa-info-circle text-primary mr-1"></i> Tekan <b>Enter</b> untuk
                                    pencarian instan.
                                </span>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Nama Lengkap</label>
                                    <input type="text" id="plot_nama"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="-">
                                </div>
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Departemen</label>
                                    <input type="text" id="plot_dept"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="-">
                                </div>
                            </div>

                            <div class="form-group mb-4" data-toggle="tooltip" data-placement="top"
                                title="Data unit lama akan dikosongkan secara otomatis jika relokasi berhasil">
                                <label class="text-danger font-weight-bolder font-size-sm">Loker Saat Ini</label>
                                <div
                                    class="d-flex align-items-center bg-light-danger rounded-xl p-4 border border-danger-o-20">
                                    <div class="symbol symbol-30 symbol-danger mr-3">
                                        <span class="symbol-label"><i
                                                class="fas fa-exchange-alt font-size-sm"></i></span>
                                    </div>
                                    <input type="text" id="plot_loker_lama"
                                        class="form-control-plaintext font-weight-bolder text-danger py-0" readonly
                                        value="-">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Gender</label>
                                    <input type="text" id="plot_gender_label"
                                        class="form-control form-control-solid border-0" readonly placeholder="-">
                                    <input type="hidden" name="gender" id="plot_gender_val">
                                </div>
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Kategori</label>
                                    <input type="text" id="plot_kategori_label"
                                        class="form-control form-control-solid border-0" readonly placeholder="-">
                                    <input type="hidden" name="kategori_karyawan" id="plot_kategori_val">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 pl-md-10 text-center d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-5 text-left">Profil & Penempatan</h6>
                                <div class="symbol symbol-120 symbol-lg-150 mb-6 shadow-sm p-1 bg-white"
                                    style="border-radius: 20px; border: 1px solid #eee;" data-toggle="tooltip"
                                    title="Verifikasi wajah karyawan sebelum memberikan kunci">
                                    <img id="plot_foto_img" src="{{ asset('assets/media/users/default.jpg') }}"
                                        alt="Foto" style="border-radius: 18px; object-fit: cover;">
                                </div>

                                <div class="form-group text-left mt-5">
                                    <label class="font-weight-bolder text-primary mb-2">Pilih Unit Loker Baru</label>
                                    <div class="input-group input-group-solid shadow-none"
                                        style="border-radius: 12px; overflow: hidden;" data-toggle="tooltip"
                                        title="Pilihan unit hanya menampilkan yang tersedia (kosong)">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light-primary border-0">
                                                <i class="fas fa-th-large text-primary"></i>
                                            </span>
                                        </div>
                                        <select name="no_loker" id="select_no_loker"
                                            class="form-control border-0 font-weight-bolder" style="height: 50px;">
                                            <option value="">-- Pilih Unit --</option>
                                        </select>
                                    </div>
                                    <p class="text-muted font-size-xs mt-3">
                                        <span class="badge badge-light-primary badge-inline" data-toggle="tooltip"
                                            title="Sistem mencegah plotting lintas gender atau kategori">Sistem Filter
                                            Aktif</span><br>
                                        Unit ditampilkan berdasarkan Gender & Kategori.
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
                        data-theme="dark" title="Tombol aktif setelah NIK dan Unit terpilih">
                        <i class="fas fa-check-circle mr-2"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
