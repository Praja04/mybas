<div class="modal fade" id="modalReportLostCard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formReportLostCard">
                <div class="modal-header">
                    <h5 class="modal-title">Lapor Kartu Hilang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row g-3">
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <strong>Nama:</strong> <span id="lost_info_nama">-</span><br>
                                    <strong>No Identitas:</strong> <span id="lost_info_no_identitas">-</span><br>
                                    <strong>No Kartu:</strong> <span id="lost_info_no_kartu">-</span><br>
                                    <strong>Perusahaan:</strong> <span id="lost_info_perusahaan">-</span><br>
                                    <strong>Waktu Masuk:</strong> <span id="lost_info_waktu_masuk">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tujuan:</strong> <span id="lost_info_tujuan">-</span><br>
                                    <strong>No HP Driver:</strong> <span id="lost_info_nohp">-</span><br>
                                    <strong>Nopol Kendaraan:</strong> <span id="lost_info_nopol">-</span><br>
                                    <strong>Nama Kernet:</strong> <span id="lost_info_kernet">-</span><br>
                                    <strong>Plant:</strong> <span id="lost_info_plant">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label">No Kartu</label>
                                <input type="text" class="form-control" id="lost_no_kartu" name="no_kartu"
                                    readonly />
                            </div>
                            <div class="mb-2">
                                <label class="form-label">No Identitas</label>
                                <input type="text" class="form-control" id="lost_no_identitas" name="no_identitas"
                                    readonly />
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" id="lost_nama" name="nama" readonly />
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Alasan Hilang</label>
                                <textarea class="form-control" id="alasan_hilang" name="alasan_hilang" rows="3" required></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Dilaporkan Oleh</label>
                                <input type="text" class="form-control" id="dilaporkan_oleh" name="dilaporkan_oleh"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto KTP</label>
                            <div id="lostKtpFotoContainer" class="border rounded p-2 mb-3">
                                <img id="lostKtpFoto" src="" alt="Foto KTP" class="img-fluid w-100" />
                            </div>
                            <label class="form-label">Foto Selfie</label>
                            <div id="lostSelfieContainer" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Simpan Laporan</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
