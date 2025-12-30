<div class="tab-pane fade" id="supplier-out" role="tabpanel" aria-labelledby="supplier-out-tab">
    <div class="row justify-content-center my-5">
        <div class="col-lg-12">
            <div class="card p-5 shadow-sm form-container">
                <!-- FORM OUT -->
                <form id="form_supplier_out" onsubmit="return false;">
                    <div class="row g-2 align-items-end mb-4">
                        <!-- Input QR / No Kartu -->
                        <div class="col-md-8">
                            <label for="qrcode_input" class="form-label fw-semibold">
                                Visitor ID / Nomor Kartu
                            </label>
                            <input type="text" class="form-control form-control-lg text-center" id="qrcode_input"
                                name="qrcode_input" placeholder="Ketik nomor kartu atau scan kartu">
                        </div>

                        <div class="col-md-4">
                            <!-- Biar label ini sejajar dengan input di kolom lain -->
                            <label class="form-label d-none d-md-block">&nbsp;</label>

                            <!-- Wrapper tombol -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <!-- Tombol Cari Data -->
                                <button type="button" class="btn btn-primary w-100" id="searchVisitorData"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Cari data pengunjung berdasarkan ID atau Nomor Kartu">
                                    <i class="mdi mdi-account-search"></i>
                                    Cari Data Pengunjung
                                </button>

                                <!-- Tombol Refresh -->
                                <button type="button" class="btn btn-outline-primary w-100 w-md-auto"
                                    onclick="hotReload()">
                                    <i class="mdi mdi-refresh"></i> Refresh Halaman
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <div id="visitorResult" class="p-4" style="display: none;">
                    <h4 class="fw-bold mb-4 text-center text-primary">
                        <i class="fas fa-id-card-alt me-2"></i> Detail Transporter Terakhir
                    </h4>

                    <!-- Row Pertama -->
                    <div class="row g-3 mb-4">
                        <!-- Informasi Pengunjung -->
                        <div class="col-md-4 col-6">
                            <div class="card bg-light border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">
                                        Informasi Transporter
                                    </h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>Nama Supir/Kernet:</strong> <span id="visitorName"></span></li>
                                        <li><strong>Sebagai Apa:</strong> <span id="visitorKeterangan"></span></li>
                                        <li><strong>Perusahaan:</strong> <span id="visitorCompany"></span></li>
                                        <li><strong>No Kartu:</strong> <span id="visitorCard"></span></li>
                                        <li><strong>No KTP/SIM:</strong> <span id="visitorKTP"></span></li>
                                        <li><strong>No Polisi:</strong> <span id="visitorNopol"></span></li>
                                        <li><strong>Pakai Kacamata:</strong> <span id="visitorIsKacamata"></span></li>
                                        <li><strong>Kondisi Kacamata:</strong> <span id="visitorKondisiKacamata"></span>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Status Kunjungan -->
                        <div class="col-md-4 col-6">
                            <div class="card bg-light border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">
                                        Status Kunjungan
                                    </h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>Masuk:</strong> <span id="visitorDateIn"></span> <span
                                                id="visitorTimeIn"></span></li>
                                        <li><strong>Keluar:</strong> <span id="visitorDateOut"></span> <span
                                                id="visitorTimeOut"></span></li>
                                        <li><strong>Status Kartu:</strong>
                                            <span id="visitorCardStatus" class="badge bg-warning text-dark"></span>
                                        </li>
                                        <li><strong>Gate ID:</strong> <span id="visitorGateIdOut"></span></li>
                                        <li><strong>Gate Line:</strong> <span id="visitorGateLineIdOut"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Status Cek Kendaraan --}}
                        {{-- <div class="col-md-3 col-6">
                            <div class="card bg-light border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-clock text-danger"></i>Status Pengecekan Kendaraan
                                    </h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>Nama Petugas:</strong> <span id="visitorNamaPetugas"></span></li>
                                        <li><strong>Jenis Muatan:</strong> <span id="visitorJenisMuatan"></span></li>
                                        <li><strong>Jenis Truk:</strong> <span id="visitorJenisTruk"></span></li>
                                        <li><strong>Jam Pengecekan Masuk:</strong> <span id="visitorCheckDate"></span>
                                            <span id="visitorCheckTime"></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
 --}}
                        <!-- Foto KTP -->
                        <div class="col-md-4 col-12">
                            <div class="card bg-light bg-light border-0 shadow-sm rounded-4 h-100 text-center">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-id-card me-2 text-success"></i>Foto KTP/SIM
                                    </h6>
                                    <img id="visitorKTPImage" src="" alt="Foto KTP"
                                        class="img-fluid rounded shadow-sm w-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row Kedua: Foto Selfie -->
                    <div class="card bg-light border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-camera me-2 text-info"></i>Foto Selfie
                            </h6>
                            <div id="visitorSelfieImages" class="d-flex flex-wrap justify-content-start gap-3">
                                <!-- Multiple Foto Selfie muncul di sini -->
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="text-start mt-3">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm"
                            id="returnCard" data-trnvisitorid="TRN-ID-DARI-RESPONSE">
                            <i class="fas fa-undo me-2"></i> Tandai Kartu Dikembalikan
                        </button>
                    </div>
                </div>

                <!-- Modal Preview Image -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-body text-center p-0">
                                <img id="imagePreviewModalImg" src="" class="img-fluid" alt="Preview Image">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL SCAN QR -->
                <div class="modal fade" id="scanQrModal" tabindex="-1" aria-labelledby="scanQrModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content p-3">
                            <div class="modal-header">
                                <h5 class="modal-title" id="scanQrModalLabel">Scan QR Code</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    id="closeModalBtn"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div id="qr-reader" style="width:100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- <script src="{{ asset('assets/plugins/custom/html5-qrcode/html5-qrcode.min.js') }}"></script> --}}
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-supplier-out.js') }}"></script>
    <script>
        function hotReload() {
            // Tambahkan query unik agar browser gak ambil dari cache
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url); // replace supaya gak nambah history
        }
    </script>
@endpush
