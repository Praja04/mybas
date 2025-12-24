{{-- Main modal --}}
<div class="tab-pane fade show" id="cek-kendaraan-out" role="tabpanel">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card p-4 p-lg-5 shadow-sm form-container">

                <div class="d-flex justify-content-between align-items-center">
                    <!-- HEADER TABLE -->
                    <div id="headerTableOut">
                        <h2 class="fw-bold text-primary">
                            Daftar Kendaraan Belum Dicek (Keluar)
                        </h2>
                        <p class="text-muted">
                            Pilih kendaraan untuk melakukan pengecekan keluar
                        </p>
                    </div>

                    <!-- HEADER FORM -->
                    <div id="headerFormOut" style="display:none">
                        <h2 class="fw-bold text-primary">
                            Form Pengecekan Kendaraan (Keluar)
                        </h2>
                        <p class="text-muted">
                            Lengkapi data pengecekan kendaraan keluar
                        </p>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </button>
                </div>

                <div id="formAlertOut" class="alert mt-3" style="display: none;"></div>

                <!-- STEPPER WRAPPER -->
                <div class="d-flex justify-content-center mb-4">
                    <div id="cekStepperOut" class="cek-stepper">

                        <!-- STEP 1 -->
                        <div class="step-item active" id="step-table-out">
                            <div class="step-circle">1</div>
                            <div class="step-label">Pilih Kendaraan</div>
                        </div>

                        <div class="step-line"></div>

                        <!-- STEP 2 -->
                        <div class="step-item" id="step-form-out">
                            <div class="step-circle">2</div>
                            <div class="step-label">Isi Form Pengecekan Keluar</div>
                        </div>

                    </div>
                </div>

                {{-- Tabel Kendaraan --}}
                <div id="tableWrapperOut">
                    <div class="table-responsive">
                        <table class="kendaraan-out-datatables table nowrap align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Polisi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Main Form --}}
                <div id="formWrapperOut" style="display: none;">
                    <button type="button" class="btn btn-outline-primary mb-4" onclick="backToTableOut()">
                        ← Kembali ke Daftar
                    </button>

                    <form id="cekKendaraanFormOut" method="POST" enctype="multipart/form-data" onsubmit="return false;"
                        action="{{ route('ajax.pos-security.cek-kendaraan.checkout') }}">
                        @csrf
                        <input type="hidden" name="trncekid" id="trncekid">

                        {{-- Card Informasi Kendaraan OUT --}}
                        <div id="section-kendaraan-out" class="mb-4">

                            <div class="alert alert-info mb-3">
                                <i class="mdi mdi-information-outline"></i>
                                <strong>Informasi Kendaraan</strong>
                            </div>

                            <div class="row g-3">

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nomor Polisi</small>
                                            <h6 class="fw-bold mb-0" id="card-nopol-out">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nama Supir</small>
                                            <h6 class="fw-bold mb-0" id="card-nama-supir-out">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nama Perusahaan</small>
                                            <h6 class="fw-bold mb-0" id="card-perusahaan-out">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Waktu Masuk</small>
                                            <h6 class="fw-bold mb-0" id="card-waktu-masuk">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Jenis Muatan</small>
                                            <h6 class="fw-bold mb-0" id="card-jenis-muatan">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Jenis Truk</small>
                                            <h6 class="fw-bold mb-0" id="card-jenis-truk">-</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Form pengecekan --}}
                        <div id="section-pemeriksaan-out">
                            <div class="alert alert-warning mt-3">
                                <strong>Data Pengecekan Keluar (WAJIB DIISI)</strong>
                                <br>Silakan isi data berikut dan lakukan pengambilan foto.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nama_petugas">Nama Petugas Pemeriksa
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_petugas" id="nama_petugas-out"
                                    required placeholder="Masukkan nama petugas yang memeriksa">
                            </div>
                        </div>

                        {{-- Foto Section --}}
                        <div class="row">
                            <div id="fotoSectionOut" class="row mt-3"></div>
                        </div>

                        {{-- Button --}}
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">

                            {{-- <button type="button" class="btn btn-outline-primary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i>
                            <span>Refresh Halaman</span>
                        </button> --}}

                            {{-- <button type="button"
                            class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="resetFormButton()" id="resetBtnOut" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Kosongkan semua isian dan foto">
                            <i class="mdi mdi-eraser"></i>
                            <span>Reset Form</span>
                        </button> --}}

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                                id="submitBtnOut" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Simpan data pengecekan ke sistem">
                                <i class="mdi mdi-content-save"></i>
                                <span>Simpan Data</span>
                            </button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Foto Modal --}}
<div id="myModalOut" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabelOut">Foto (label)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h5 class="fs-15 mb-3">Capture Gambar dari Kamera</h5>

                <!-- Tombol Mulai Kamera -->
                <button id="startCameraOut" class="btn btn-success mb-3">Mulai Kamera</button>

                <!-- Video Stream -->
                <video id="videoOut" width="100%" autoplay class="mb-3 rounded shadow"
                    style="display: none;"></video>

                <!-- Canvas untuk Capture -->
                <canvas id="canvasOut" style="display: none;"></canvas>

                <!-- Preview Hasil Capture -->
                <div id="capturedImageContainerOut" class="mt-3" style="display: none;">
                    <img id="capturedImageOut" class="img-fluid rounded shadow" />
                </div>

                <!-- Tombol Capture & Ulang -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>

                <button id="captureBtnOut" class="btn btn-secondary me-2" style="display: none;">Capture</button>

                <button id="retakeBtnOut" class="btn btn-warning" style="display: none;">Ambil Ulang</button>

                <button id="saveBtnOut" type="button" class="btn btn-primary" style="display:none;">Simpan
                    Foto</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/cek-kendaraan-out-table.js') }}">
    </script>
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-out.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-out-store.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/foto-config.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/truck-options.js') }}"></script> --}}

    <script>
        function hotReload() {
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const outTabButton = document.querySelector(
                '[data-bs-target="#cek-kendaraan-out"]'
            );

            if (!outTabButton) return;

            outTabButton.addEventListener("shown.bs.tab", function() {
                console.log("TAB OUT dibuka");

                // kalau belum pernah init → init
                if (!window.cekKendaraanOutTable) {
                    console.log("INIT DATATABLE OUT");
                    new window.ContentDatatableOut().initialize();
                    return;
                }

                // kalau sudah init → reload data
                console.log("RELOAD DATATABLE OUT");
                window.cekKendaraanOutTable.ajax.reload(null, false);
            });
        });
    </script>
@endpush
