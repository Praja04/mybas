{{-- Main modal --}}
<div class="tab-pane fade show active" id="cek-kendaraan-in" role="tabpanel">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card p-5 shadow-sm form-container">

                {{-- Header --}}
                <div class="mb-4">
                    <h2 class="fw-bold text-primary">
                        Form Pengecekan Kendaraan (Masuk)
                    </h2>

                    <p class="text-muted mb-0">Silakan isi data kendaraan yang akan masuk ke area PT
                    </p>
                </div>

                {{-- Search Nomor Polisi --}}
                <form id="formSearchNopol" onsubmit="return false;">
                    <div class="row g-2 align-items-end mb-4">

                        <div class="col-md-8">
                            <label for="nopol-search" class="form-label fw-semibold">
                                Nomor Polisi
                            </label>
                            <input type="text" class="form-control form-control-lg text-center" id="nopol-search"
                                name="nopol-search" placeholder="Masukkan nomor polisi">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label d-none d-md-block">&nbsp;</label>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <button type="button" class="btn btn-primary w-100 w-md-auto" id="searchVisitorData"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Cari data kendaraan berdasarkan nomor polisi kendaraan">
                                    <i class="mdi mdi-account-search"></i>
                                    Cari
                                </button>

                                <button type="button" class="btn btn-outline-primary w-100 w-md-auto"
                                    onclick="hotReload()">
                                    <i class="mdi mdi-refresh"></i> Refresh halaman
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <div id="formAlert" class="alert mt-3" style="display: none;"></div>

                {{-- Main Form --}}
                <form id="cekKendaraanForm" method="POST" enctype="multipart/form-data" onsubmit="return false;"
                    action="{{ route('ajax.pos-security.cek-kendaraan.store') }}" style="display: none;">
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

                        </div>
                    </div>

                    {{-- Form pemeriksaan --}}
                    <div id="section-pemeriksaan">
                        <div class="alert alert-warning mt-3">
                            <i class="mdi mdi-information-outline"></i>

                            <strong>Data Pemeriksaan Masuk (WAJIB DIISI)</strong>
                            <br>Silakan isi data berikut dan lakukan pengambilan foto.
                        </div>

                        <div class="pb-2">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nama_petugas">Nama Petugas Pemeriksa
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_petugas" id="nama_petugas"
                                    required placeholder="Masukkan nama petugas yang memeriksa">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="muatanType">Jenis Muatan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="muatanType" name="muatan_type" required>
                                    <option value="" disabled selected>-- Pilih Jenis Muatan --</option>
                                    <option value="LIQUID">LIQUID</option>
                                    <option value="NONLIQUID">NONLIQUID</option>
                                </select>
                            </div>

                            <div class="mb-3" id="truckTypeContainer" style="display: none;">
                                <label class="form-label fw-semibold" for="truckType">Jenis Truk <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="truckType" name="truck_type" required>
                                    <option value="" disabled selected>-- Pilih Jenis Truk --</option>
                                </select>
                            </div>

                            <div class="mb-3" id="otherTruckContainer" style="display: none;">
                                <label class="form-label fw-semibold" for="otherTruckType">
                                    Jenis Truk Lainnya <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="otherTruckType" id="otherTruckType"
                                    placeholder="Contoh: Truk Tangki Limbah, Truk Tangki Air, dll">
                            </div>
                        </div>

                    </div>

                    {{-- Foto Section --}}
                    <div class="row">
                        <div id="fotoSection" class="row mt-3"></div>
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
                            onclick="resetFormButton()" id="resetBtn" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Kosongkan semua isian dan foto">
                            <i class="mdi mdi-eraser"></i>
                            <span>Reset Form</span>
                        </button> --}}

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                            id="submitBtn" data-bs-toggle="tooltip" data-bs-placement="top"
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

{{-- Foto Modal --}}
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Foto (label)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h5 class="fs-15 mb-3">Capture Gambar dari Kamera</h5>

                <!-- Tombol Mulai Kamera -->
                <button id="startCamera" class="btn btn-success mb-3">Mulai Kamera</button>

                <!-- Video Stream -->
                <video id="video" width="100%" autoplay class="mb-3 rounded shadow"
                    style="display: none;"></video>

                <!-- Canvas untuk Capture -->
                <canvas id="canvas" style="display: none;"></canvas>

                <!-- Preview Hasil Capture -->
                <div id="capturedImageContainer" class="mt-3" style="display: none;">
                    <img id="capturedImage" class="img-fluid rounded shadow" />
                </div>

                <!-- Tombol Capture & Ulang -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>

                <button id="captureBtn" class="btn btn-secondary me-2" style="display: none;">Capture</button>

                <button id="retakeBtn" class="btn btn-warning" style="display: none;">Ambil Ulang</button>

                <button id="saveBtn" type="button" class="btn btn-primary" style="display:none;">Simpan
                    Foto</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-input.js') }}">
    </script>
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-input-store.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/foto-config.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/truck-options.js') }}"></script> --}}

    <script>
        function hotReload() {
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url);
        }
    </script>
@endpush
