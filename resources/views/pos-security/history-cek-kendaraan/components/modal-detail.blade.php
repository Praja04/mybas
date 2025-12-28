<div class="modal fade" id="modalCekKendaraanDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Visitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    <!-- Informasi Kendaraan -->
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Kendaraan</h6>
                        <dl class="row">
                            <dt class="col-sm-4">Nomor Polisi</dt>
                            <dd class="col-sm-8" id="detailNomorPolisi"></dd>

                            <dt class="col-sm-4">Nama Supir</dt>
                            <dd class="col-sm-8" id="detailNamaSupir"></dd>

                            <dt class="col-sm-4">Perusahaan</dt>
                            <dd class="col-sm-8" id="detailNamaPerusahaan"></dd>

                            <dt class="col-sm-4">Jenis Muatan</dt>
                            <dd class="col-sm-8" id="detailJenisMuatan"></dd>

                            <dt class="col-sm-4">Jenis Truk</dt>
                            <dd class="col-sm-8" id="detailJenisTruk"></dd>

                            <dt class="col-sm-4">Jenis Truk Lainnya</dt>
                            <dd class="col-sm-8" id="detailJenisTrukLainnya"></dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8" id="detailStatus"></dd>
                        </dl>
                    </div>

                    <div class="row">

                        <!-- Informasi Masuk -->
                        <div class="col-12 col-lg-6 mb-3">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Masuk</h6>
                            <dl class="row">
                                <dt class="col-sm-5">Nama Petugas Masuk</dt>
                                <dd class="col-sm-7" id="detailNamaPetugasMasuk"></dd>

                                <dt class="col-sm-5">Waktu Masuk</dt>
                                <dd class="col-sm-7" id="detailWaktuMasuk"></dd>
                            </dl>
                        </div>

                        <!-- Informasi Keluar -->
                        <div class="col-12 col-lg-6 mb-3">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Keluar</h6>
                            <dl class="row">
                                <dt class="col-sm-5">Nama Petugas Keluar</dt>
                                <dd class="col-sm-7" id="detailNamaPetugasKeluar"></dd>

                                <dt class="col-sm-5">Waktu Keluar</dt>
                                <dd class="col-sm-7" id="detailWaktuKeluar"></dd>

                                <dt class="col-sm-5">Durasi</dt>
                                <dd class="col-sm-7" id="detailDurasi"></dd>
                            </dl>
                        </div>

                    </div>

                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            Foto Pengecekan Kendaraan (MASUK)
                        </h6>
                        <div class="row" id="detailFotoContainer"></div>

                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            Foto Pengecekan Kendaraan (kELUAR)
                        </h6>
                        <div class="row" id="detailFotoKeluarContainer"></div>
                    </div>

                    <div class="col-12">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="imageOverlay"
    style="
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        cursor: zoom-out;
     ">
    <img id="overlayImage"
        style="
            max-width: 90%;
            max-height: 90%;
            border-radius: 6px;
            object-fit: contain;
         ">
</div>
