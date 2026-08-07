{{-- Main modal --}}
<div class="tab-pane fade show active" id="cek-kendaraan-in" role="tabpanel">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card p-4 p-lg-5 shadow-sm form-container">

                <div class="d-flex justify-content-between align-items-center">
                    <!-- HEADER TABLE -->
                    <div id="headerTable">
                        <h2 class="fw-bold text-primary">
                            Daftar Kendaraan Belum Dicek (Masuk)
                        </h2>
                        <p class="text-muted">
                            Pilih kendaraan untuk melakukan pengecekan masuk
                        </p>
                    </div>

                    <!-- HEADER FORM -->
                    <div id="headerForm" style="display:none">
                        <h2 class="fw-bold text-primary">
                            Form Pengecekan Kendaraan (Masuk)
                        </h2>
                        <p class="text-muted">
                            Lengkapi data pengecekan kendaraan masuk
                        </p>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="if(window.cekKendaraanInTable) { window.cekKendaraanInTable.reload(null, true); } else { location.reload(); }">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </button>
                </div>

                <div id="formAlert" class="alert mt-3" style="display: none;"></div>

                <!-- STEPPER WRAPPER -->
                <div class="d-flex justify-content-center mb-4">
                    <div id="cekStepper" class="cek-stepper">

                        <!-- STEP 1 -->
                        <div class="step-item active" id="step-table">
                            <div class="step-circle">1</div>
                            <div class="step-label">Pilih Kendaraan</div>
                        </div>

                        <div class="step-line"></div>

                        <!-- STEP 2 -->
                        <div class="step-item" id="step-form">
                            <div class="step-circle">2</div>
                            <div class="step-label">Isi Form Pengecekan Masuk</div>
                        </div>

                    </div>
                </div>

                <div id="tableWrapper">
                    <!-- Controls (Per Page & Search) -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <select id="perPageSelectIn" class="form-select form-select-sm" style="width: auto;">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-muted small">entri per halaman</span>
                        </div>
                        <div style="width: 250px;" class="max-w-100">
                            <input type="text" id="searchInputIn" class="form-control form-control-sm" placeholder="Cari nomor polisi...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table nowrap align-middle" id="tableInCustom" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>Nomor Polisi</th>
                                    <th>Status</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
                        <div id="paginationInfoIn" class="text-muted small"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationListIn">
                            </ul>
                        </nav>
                    </div>
                </div>

                {{-- Main Form --}}
                <div id="formWrapper" style="display: none;">
                    <button type="button" class="btn btn-outline-primary mb-4" onclick="backToTable()">
                        ← Kembali ke Daftar
                    </button>

                    <form id="cekKendaraanForm" method="POST" enctype="multipart/form-data" onsubmit="return false;" action="{{ route('ajax.pos-security.cek-kendaraan.store') }}">
                        @csrf

                        <input type="hidden" name="trnvisitorid" id="trnvisitorid">
                        <input type="hidden" name="nama_supir" id="nama-supir">
                        <input type="hidden" name="company" id="company">
                        <input type="hidden" name="nomor_polisi" id="nomor-polisi">

                        {{-- Card Informasi Kendaraan --}}
                        <div id="section-kendaraan">

                            <div class="alert alert-info mb-3">
                                <i class="mdi mdi-information-outline"></i>
                                <strong>Informasi Kendaraan</strong>
                            </div>

                            <div class="row g-3">
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nomor Polisi</small>
                                            <h6 class="fw-bold mb-0" id="card-nopol">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nama Supir</small>
                                            <h6 class="fw-bold mb-0" id="card-nama-supir">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Nama Perusahaan</small>
                                            <h6 class="fw-bold mb-0" id="card-perusahaan">-</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <small class="text-muted">Waktu Masuk POS 1</small>
                                            <h6 class="fw-bold mb-0" id="card-waktu-daftar">-</h6>
                                            <small class="text-muted" id="card-waktu-lalu"></small>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        {{-- ===================================================== --}}
                        {{-- Foto Identitas Supir (KTP + Selfie) — BARU --}}
                        {{-- ===================================================== --}}
                        <div id="section-identitas" class="mt-4">
                            <div class="alert alert-secondary mb-3">
                                <i class="mdi mdi-card-account-details-outline"></i>
                                <strong>Foto Identitas Supir</strong>
                                <br>
                                <small>Ambil foto diri supir sebelum melakukan pengecekan kendaraan.</small>
                            </div>

                            <div class="row g-3">
                                {{-- Slot Foto Diri --}}
                                <div class="col-md-12">
                                    <div class="card shadow-sm h-100 foto-slot-identitas" data-key="foto_diri">
                                        <div class="card-body d-flex flex-column">
                                            <label class="form-label fw-semibold mb-1">
                                                <i class="mdi mdi-account-box text-primary me-1"></i>
                                                Foto Diri / Selfie
                                                <span class="text-danger">*</span>
                                                <span class="badge bg-danger ms-1">Wajib</span>
                                            </label>
                                            <p class="text-muted small mb-2">Ambil foto wajah supir secara langsung</p>

                                            <div id="preview-foto_diri" class="d-flex justify-content-center align-items-center mb-3 rounded" style="min-height: 130px; background:#f8f9fa; border: 2px dashed #dee2e6;">
                                                <span class="text-muted small" id="placeholder-foto_diri">
                                                    <i class="mdi mdi-image-outline fs-4 d-block text-center mb-1"></i>
                                                    Belum ada foto
                                                </span>
                                            </div>

                                            <input type="hidden" name="photos[foto_diri]" id="input-foto_diri" value="">

                                            <button type="button" class="btn btn-outline-primary btn-sm mt-auto open-supplier-photo" data-key="foto_diri" data-label="Foto Diri Supir" data-bs-toggle="modal" data-bs-target="#myModalSupplier">
                                                <i class="mdi mdi-account-camera"></i> Ambil Foto Diri
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ===================================================== --}}

                        {{-- Form pemeriksaan --}}
                        <div id="section-pemeriksaan">
                            <div class="alert alert-warning mt-3">
                                <i class="mdi mdi-information-outline"></i>

                                <strong>Data Pengecekan Kendaraan Masuk (WAJIB DIISI)</strong>
                                <br>Silakan isi data berikut dan lakukan pengambilan foto.
                            </div>

                            <div class="pb-2">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="nama_petugas">Nama Petugas Pemeriksa
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_petugas" id="nama_petugas" required placeholder="Masukkan nama petugas yang memeriksa">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="muatanType">Jenis Muatan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="muatanType" name="muatan_type" required>
                                        <option value="" disabled selected>-- Pilih Jenis Muatan --</option>
                                        <option value="LIQUID">LIQUID</option>
                                        <option value="NONLIQUID">NON LIQUID</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="truckTypeContainer" style="display: none;">
                                    <label class="form-label fw-semibold" for="truckType">Jenis Truk <span class="text-danger">*</span></label>
                                    <select class="form-select" id="truckType" name="truck_type" required>
                                        <option value="" disabled selected>-- Pilih Jenis Truk --</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="otherTruckContainer" style="display: none;">
                                    <label class="form-label fw-semibold" for="otherTruckType">
                                        Jenis Truk Lainnya <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="otherTruckType" id="otherTruckType" placeholder="Contoh: Truk Tangki Limbah, Truk Tangki Air, dll">
                                </div>
                            </div>

                        </div>

                        {{-- Alert foto --}}
                        <div id="alertFotoWajib" class="alert alert-primary d-none">
                            <strong>Mohon ambil foto bagian:</strong>
                            <ul class="mb-0 mt-2"></ul>
                        </div>


                        {{-- Foto Section --}}
                        <div class="row">
                            <div id="fotoSection" class="row my-3"></div>
                        </div>

                        {{-- Button --}}
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2" id="submitBtn" data-bs-toggle="tooltip" data-bs-placement="top">
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

{{-- Foto Modal (Kendaraan) --}}
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Foto (label)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="video" autoplay width="100%" class="rounded shadow-sm mb-3" style="display: none;"></video>
                <canvas id="canvas" style="display: none;"></canvas>

                <button id="startCamera" class="btn btn-success mb-3">Mulai Kamera</button>

                <div id="capturedImageContainer" class="mt-3" style="display: none;">
                    <img id="capturedImage" class="img-fluid rounded shadow" />
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <div>
                    <button id="retakeBtn" class="btn btn-warning me-2" style="display: none;">Tambah
                        Foto</button>
                    <button id="captureBtn" class="btn btn-secondary me-2" style="display: none;">Capture</button>
                    <button id="saveBtn" class="btn btn-primary" style="display: none;">Simpan Semua</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kamera Identitas (KTP & Selfie) --}}
<div id="myModalSupplier" class="modal fade" tabindex="-1" aria-labelledby="myModalLabelSupplier" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabelSupplier">Foto Identitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="videoSupplier" autoplay width="100%" class="rounded shadow-sm mb-3" style="display: none;"></video>
                <canvas id="canvasSupplier" style="display: none;"></canvas>

                <button id="startCameraSupplier" class="btn btn-success mb-3">Mulai Kamera</button>

                <div id="capturedImageContainerSupplier" class="mt-3" style="display: none;">
                    <img id="capturedImageSupplier" class="img-fluid rounded shadow" />
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <div>
                    <button id="retakeBtnSupplier" class="btn btn-warning me-2" style="display: none;">Ulangi</button>
                    <button id="captureBtnSupplier" class="btn btn-secondary me-2" style="display: none;">Capture</button>
                    <button id="saveBtnSupplier" class="btn btn-primary" style="display: none;">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/indexeddb-helper.js') }}"></script>
<script type="module" src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/cek-kendaraan-in-table.js') }}">
</script>
<script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-input.js') }}"></script>
<script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-input-store.js') }}"></script>
<script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/foto-config.js') }}"></script>

<script>
    function hotReload() {
        const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
        window.location.replace(url);
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // === TAB MASUK (IN) ===
        const inTabButton = document.querySelector(
            '[data-bs-target="#cek-kendaraan-in"]'
        );

        if (inTabButton) {
            inTabButton.addEventListener("shown.bs.tab", function() {
                console.log("TAB IN dibuka");

                // belum pernah init
                if (!window.cekKendaraanInTable) {
                    console.log("INIT DATATABLE IN");
                    new window.ContentDatatableIn().initialize();
                    return;
                }

                // sudah init → reload
                console.log("RELOAD DATATABLE IN");
                window.cekKendaraanInTable.ajax.reload(null, false);
            });
        }
    });
</script>
@endpush