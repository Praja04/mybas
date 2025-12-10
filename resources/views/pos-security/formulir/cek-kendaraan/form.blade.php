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

                    <p class="text-muted mb-0">Silakan isi data kendaraan yang akan masuk ke area PT Bumi Alam Segar
                    </p>

                    <div class="mt-3">
                        <a href="#!" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#panduanModal">
                            <i class="mdi mdi-information-outline me-1"></i> Panduan Pengisian
                        </a>
                    </div>
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
                                    title="Cari data pengunjung berdasarkan ID atau Nomor Kartu">
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
                    {{-- todo --}}
                    @csrf
                    {{-- <input type="hidden" name="createdby" id="createdby"> --}}

                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">

                        {{-- <button type="button" class="btn btn-outline-primary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i>
                            <span>Refresh Halaman</span>
                        </button> --}}

                        <button type="button"
                            class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="resetFormButton()" id="resetBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Kosongkan semua isian dan foto">
                            <i class="mdi mdi-eraser"></i>
                            <span>Reset Form</span>
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                            id="submitBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Simpan data pengunjung ke sistem">
                            <i class="mdi mdi-content-save"></i>
                            <span>Simpan Data</span>
                        </button>

                    </div>

                    {{-- Input --}}
                    <div class="row mt-4">
                        {{-- Autofilled field --}}
                        <div class="col-lg-6 order-1 order-lg-1"id="section-kendaraan">
                            <div class="alert alert-info mt-3">
                                <strong>Data Kendaraan (Otomatis)</strong>
                                <br>Data di bawah ini diambil dari sistem dan tidak perlu diubah.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nama-supir">Nama Supir <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="nama_supir" id="nama-supir"
                                    required placeholder="Nama supir" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nama-kernet">Nama Kernet (Jika Ada)</label>
                                <input type="text" class="form-control bg-light" name="nama_kernet" id="nama-kernet"
                                    required placeholder="Nama kernet" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="company">Nama Perusahaan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="company" id="company"
                                    required placeholder="Nama perusahaan" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nomor-polisi">Nomor Polisi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="nomor_polisi"
                                    id="nomor-polisi" required placeholder="Nomor polisi" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="tgl_periksa">Tanggal Pemeriksaan
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" name="tgl_periksa"
                                    id="tgl_periksa" placeholder="yyyy-mm-dd" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="jam_periksa">Jam Pemeriksaan <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control bg-light" name="jam_periksa"
                                    id="jam_periksa" placeholder="HH:MM" readonly>
                            </div>
                        </div>

                        {{-- Form pemeriksaan --}}
                        <div class="col-lg-6 order-2 order-lg-2" id="section-pemeriksaan">
                            <div class="alert alert-warning mt-3">
                                <strong>Data Pemeriksaan (WAJIB DIISI)</strong>
                                <br>Silakan isi data berikut dan lakukan pengambilan foto.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nama_petugas">Nama Petugas Pemeriksa
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_petugas" id="nama_petugas"
                                    required placeholder="Masukkan nama petugas yang memeriksa">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="muatanType">Jenis Muatan <span
                                        class="text-danger">*</span></label>
                                <select required class="form-select" id="muatanType" name="muatan_type" required>
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

                <button id="saveBtn" type="button" class="btn btn-primary" onclick="saveCapture()"
                    style="display:none;">Simpan
                    Foto</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@push('scripts')
    <script src="{{ asset('assets/js/pos-security/formulir-cek-kendaraan/form-kendaraan-input.js') }}"></script>
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
