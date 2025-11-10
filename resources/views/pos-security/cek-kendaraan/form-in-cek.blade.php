@push('panduan')
    @include('pos-security.formulir.supplier.panduan')
@endpush

{{-- main modal --}}
<div class="tab-pane fade show active" id="supplier-in" role="tabpanel" aria-labelledby="supplier-in-tab">
    <div class="row justify-content-center my-5">
        <div class="col-lg-12">
            <div class="card p-5 shadow-sm form-container">

                {{-- Header --}}
                <div class="mb-4 mt-3">
                    <h2 class="fw-bold text-primary">
                        <i class="fas fa-user-plus"></i>
                        Form Pengecekan Kendaraan
                    </h2>
                    <p class="text-muted mb-0">Silakan isi data kendaraan yang akan masuk ke area bongkar/muat</p>
                </div>

                {{-- Search Nomor Polisi --}}
                <form id="form_vendor_out" onsubmit="return false;">
                    <div class="row g-2 align-items-end mb-4">
                        <!-- Input QR / No Kartu -->
                        <div class="col-md-8">
                            <label for="qrcode_input" class="form-label fw-semibold">
                                Nomor Polisi
                            </label>
                            <input type="text" class="form-control form-control-lg text-center" id="qrcode_input"
                                name="qrcode_input" placeholder="Masukan nomor polisi">
                        </div>

                        <!-- Tombol Cari Data -->
                        <div class="col-md-4">
                            <label class="form-label d-none d-md-block">&nbsp;</label>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <!-- Tombol Cari Data Pengunjung -->
                                <button type="button" class="btn btn-outline-primary w-100 w-md-auto"
                                    id="searchVisitorData" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Cari data pengunjung berdasarkan ID atau Nomor Kartu">
                                    <i class="fas fa-search me-2"></i> Cari
                                </button>

                                <!-- Tombol Refresh -->
                                <button type="button" class="btn btn-outline-primary w-100 w-md-auto"
                                    onclick="hotReload()">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh halaman
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <div id="formAlert" class="alert mt-3" style="display: none;"></div>

                {{-- Main Form --}}
                {{-- todo --}}
                {{-- action="{{ route('ajax.pos-security.visitor-transaksi.store') }}" --}}
                <div id="searchResult" style="display: none;">
                    <form id="visitorForm" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                        @csrf
                        <input type="hidden" name="createdby" id="createdby">

                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">
                            <button type="button"
                                class="btn btn-sm d-flex align-items-center gap-2 btn-outline-primary"
                                onclick="location.reload()">
                                <i class="mdi mdi-refresh"></i> Refresh halaman
                            </button>
                            <!-- Reset Button -->
                            <button type="button"
                                class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2"
                                onclick="resetForm()" id="resetBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Kosongkan semua isian dan foto">
                                <i class="fas fa-rotate-left"></i>
                                <span>Reset Form</span>
                            </button>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                                id="submitBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Simpan data pengunjung ke sistem">
                                <i class="fas fa-paper-plane"></i>
                                <span>Simpan Data</span>
                            </button>
                        </div>

                        {{-- Input --}}
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="namavisitor">Nama Petugas Pemeriksa <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="namavisitor" id="namavisitor"
                                        required placeholder="Masukkan nama petugas yang memeriksa">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="tglLahir">Tanggal Pemeriksaan </label>
                                    <input type="text" class="form-control flatpickr-single" name="tgllahir"
                                        id="tglLahir" placeholder="Pilih tanggal pemeriksaan">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="namavisitor">Jam Pemeriksaan <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="namavisitor" id="namavisitor"
                                        required placeholder="Masukkan jam pemeriksaan">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="muatantype">Jenis Muatan <span
                                            class="text-danger">*</span></label>
                                    <select required class="form-select" id="muatantype" name="muatantype" required>
                                        <option value="" disabled selected>-- Pilih Jenis Muatan --</option>
                                        <option value="LIQUID">LIQUID</option>
                                        <option value="NONLIQUID">NONLIQUID</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="purpose">Jenis Truk <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="purpose" name="purpose" required>
                                        <option value="" disabled selected>-- Pilih Jenis Truk --</option>
                                        <option value="MUAT GULA CAIR">Truk Muat Gula Cair</option>
                                        <option value="BONGKAR MATERIAL">Truck Bongkar Material</option>
                                        <option value="MUAT FINISH GOOD">Truck Muat Finish Good (WFG)</option>
                                        <option value="SPAREPART">Mobil Sparepart/Bahan Bangunan</option>
                                        <option value="VENDOR">Mobil Pribadi Vendor/Perusahaan</option>
                                        {{-- todo: lainnya? --}}
                                    </select>
                                </div>
                            </div>

                            {{-- todo: informasi autofilled  --}}
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="namavisitor">Nama Supir <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="namavisitor" id="namavisitor"
                                        required placeholder="Nama supir" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="namavisitor">Nama Perusahaan<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="namavisitor" id="namavisitor"
                                        required placeholder="Nama perusahaan" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="namavisitor">Nomor Polisi<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="namavisitor" id="namavisitor"
                                        required placeholder="Nomor polisi" disabled>
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
</div>

<!-- KTP Modals -->
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

                <button type="button" class="btn btn-primary" onclick="saveCaptureIdentitas()">Simpan Foto</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Modal Kamera -->
<div id="selfieModal" class="modal fade" tabindex="-1" aria-labelledby="selfieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selfieModalLabel">Ambil Foto Diri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="selfieVideo" autoplay width="100%" class="rounded shadow-sm mb-3"
                    style="display: none;"></video>
                <canvas id="selfieCanvas" style="display: none;"></canvas>

                <button id="startSelfieCamera" class="btn btn-success mb-3">Mulai Kamera</button>

                <div id="capturedSelfieContainer" class="mt-3" style="display: none;">
                    <img id="capturedSelfieImage" class="img-fluid rounded shadow" />
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <div>
                    <button id="retakeSelfieBtn" class="btn btn-warning me-2" style="display: none;">Tambah
                        Foto</button>
                    <button id="captureSelfieBtn" class="btn btn-secondary me-2"
                        style="display: none;">Capture</button>
                    <button id="saveSelfieBtn" class="btn btn-primary" style="display: none;">Simpan Semua</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/velzon/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/flatpickr/l10n/id.js') }}"></script>
    <script>
        flatpickr('.datepicker', {
            locale: 'id'
        });
    </script>

    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-supplier-input2.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-supplier-input-store.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#tglLahir", {
                locale: "id",
                altInput: true,
                altFormat: "j F Y",
                maxDate: "today",
                allowInput: true,
                dateFormat: "Y-m-d", // format value yang dikirim ke backend

                // Parse manual input dari user dalam format DD-MM-YYYY
                parseDate: function(datestr, format) {
                    // Jika format inputnya 15-06-2000
                    const parts = datestr.split("-");
                    if (parts.length === 3) {
                        const [day, month, year] = parts;
                        return new Date(`${year}-${month}-${day}`);
                    }
                    return flatpickr.parseDate(datestr, format);
                },

                // Format value ke dalam format Y-m-d
                formatDate: function(date, format) {
                    const yyyy = date.getFullYear();
                    const mm = String(date.getMonth() + 1).padStart(2, "0");
                    const dd = String(date.getDate()).padStart(2, "0");
                    return `${yyyy}-${mm}-${dd}`;
                },
            });
        });
    </script>

    <script src="{{ asset('assets/js/pos-security/const/photo.js') }}"></script>

    {{-- render photo --}}
    <script>
        $(function() {
            const $purposeSelect = $('#purpose');
            const $fotoSection = $('#fotoSection');

            const createFotoItem = (labelText) => `
                <div class="col-12 col-lg-4 d-flex flex-column align-items-center mb-4">
                    <label class="form-label fw-semibold mb-2">${labelText}</label>
                    <div class="d-flex flex-wrap gap-2 justify-content-center mb-2"
                        style="width: 100%; min-height: 180px; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
                    </div>
                    <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fas fa-camera me-1"></i> Ambil Foto ${labelText}
                    </button>
                    <input type="hidden" name="foto_${labelText.replace(/\s+/g, '_').toLowerCase()}" />
                </div>
            `;

            $purposeSelect.on('change', function() {
                const value = $(this).val();
                const sections = fotoConfig[value] || [];

                $fotoSection.html(sections.map(label => createFotoItem(label)).join(''));
            });
        });
    </script>

    {{-- label in modal --}}
    <script>
        $(function() {
            $(document).on('click', '[data-bs-target="#myModal"]', function() {
                const $btn = $(this);
                const labelText = $btn.closest('.d-flex.flex-column').find('label').text().trim() || 'Foto';
                $('#myModalLabel').text(`Foto ${labelText}`);
            });
        });
    </script>

    {{-- show result --}}
    <script>
        $(function() {
            $('#searchVisitorData').on('click', function() {
                $('#formAlert').hide();
                $('#searchResult').show();
                $('#searchResult')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endpush
