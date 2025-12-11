<div class="tab-pane fade show active" id="tamu-in" role="tabpanel">
    <div class="row justify-content-center my-5">
        <div class="col-lg-12">
            <div class="card p-5 shadow-sm form-container">

                {{-- Header --}}
                <div class="text-end mb-4 mt-3">
                    <h2 class="fw-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Form Data Tamu / Vendor / Transporter
                    </h2>
                    <p class="text-muted mb-0">Silakan isi data Tamu / Vendor / Transporter yang akan masuk ke area
                        bongkar/muat</p>
                </div>

                <div id="formAlert" class="alert mt-3" style="display: none;"></div>

                <form id="vendorform" action="{{ route('ajax.pos-security.vendor-transaksi.store_vendor') }}"
                    method="POST" enctype="multipart/form-data" id="vendorform">
                    @csrf

                    {{-- Input Field --}}
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Vendor / Tamu / Transporter <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="namavisitor" class="form-control"
                                    placeholder="Contoh: John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Perusahaan / Instansi <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="namacomp" class="form-control"
                                    placeholder="Contoh: PT Maju Jaya" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Lahir <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control flatpickr-single" name="tgllahir"
                                    id="tglLahir" placeholder="Pilih tanggal lahir">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenis Kunjungan ( Vendor / Tamu / Transporter)
                                    <span class="text-danger">*</span></label>
                                <select class="form-select mb-3" name="jenis" id="jenisSelect" required>
                                    <option value="" disabled selected>-- Pilih Jenis --</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="tamu">Tamu</option>
                                    <option value="transporter">Transporter</option>
                                </select>
                            </div>

                            <div class="mb-3" id="purposeGroup" style="display: none;">
                                <label class="form-label fw-semibold">Tujuan </label>
                                <select class="form-select" name="purpose" id="purposeSelect" required>
                                    <option value="" disabled selected>-- Pilih Tujuan --</option>
                                    <option value="BONGKAR">BONGKAR</option>
                                    <option value="MUAT">MUAT</option>
                                </select>
                            </div>

                            <div class="mb-3" id="nopolGroup" style="display: none;">
                                <label class="form-label fw-semibold">Nomor Polisi </label>
                                <input type="text" class="form-control" name="nopol" id="nopolInput"
                                    placeholder="Contoh: B 1234 CD">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">No. KTP / SIM <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nomorktp" required
                                    placeholder="Masukkan nomor identitas">
                            </div>

                            <div class="mb-3" style="display: none;">
                                <label class="form-label fw-semibold">Jumlah Orang <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="sumpeople" class="form-control" value="1"
                                    min="1" placeholder="Contoh: 1" required readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keperluan <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="keperluan" class="form-control"
                                    placeholder="Contoh: Meeting, Pengiriman Barang">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama PIC <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="host" class="form-control"
                                    placeholder="Contoh: Budi Santoso">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Departemen <span
                                        class="text-danger">*</span></label>
                                <select name="hostdeptid" class="form-select assign-departement-ga w-100">
                                    <option value="" disabled selected>-- Pilih Departemen --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Kartu ID <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="rfid" required
                                    placeholder="Scan atau masukkan nomor kartu RFID">
                                <small class="text-muted d-block mt-1">
                                    <i class="mdi mdi-information-outline"></i>
                                    Mohon lengkapi semua data pengunjung terlebih dahulu sebelum scan atau input nomor
                                    kartu RFID.
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                {{-- FOTO KTP --}}
                                <div class="col-md-6 d-flex flex-column align-items-center mb-4">
                                    <label class="form-label fw-semibold mb-2">Foto KTP/SIM <span
                                            class="text-danger">*</span></label>

                                    <div id="ktpPreview" class="d-flex flex-wrap gap-2 justify-content-center mb-2"
                                        style="width: 100%; min-height: 180px; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
                                        {{-- <img id="ktpImage" class="captured-image" alt="KTP Image"> --}}
                                        {{-- Thumbnail akan dimasukkan dengan JS --}}
                                    </div>

                                    <button type="button" class="btn btn-sm btn-primary w-100"
                                        data-bs-toggle="modal" data-bs-target="#myModal">
                                        <i class="fas fa-camera me-1"></i> Ambil Foto KTP
                                    </button>

                                    <input type="hidden" name="imgvisitorpathin" id="imgvisitorpathin">
                                </div>

                                {{-- FOTO DIRI --}}
                                <div class="col-md-6 col-12 d-flex flex-column align-items-center mb-4">
                                    <label class="form-label fw-semibold mb-2 text-center">Foto Diri (bisa lebih dari
                                        1) <span class="text-danger">*</span></label>

                                    {{-- Preview Gallery --}}
                                    <div id="selfiePreview"
                                        class="d-flex flex-wrap gap-2 justify-content-center align-items-start mb-2"
                                        style="width: 100%; min-height: 180px; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
                                        {{-- Thumbnail akan dimasukkan dengan JS --}}
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                        data-bs-toggle="modal" data-bs-target="#selfieModal">
                                        <i class="fas fa-camera me-1"></i> Ambil Foto Diri
                                    </button>

                                    {{-- Hidden Input untuk menyimpan base64 foto diri dalam bentuk JSON array --}}
                                    <input type="hidden" name="foto" id="selfiePhotos" value="[]">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    {{-- Penjelasan Sistem Blacklist --}}
                                    <div class="col-12 mt-3">
                                        <div class="alert alert-warning border border-2 border-danger">
                                            <h5 class="fw-bold text-danger"><i class="fas fa-ban me-1"></i> Peringatan
                                                Sistem
                                                Pemblokiran (Blacklist)</h5>
                                            <ul class="mb-1">
                                                <li>Setiap pengunjung akan dicek otomatis berdasarkan
                                                    <strong>Nama</strong> dan
                                                    <strong>Tanggal Lahir</strong>.
                                                </li>
                                                <li>Jika sudah pernah diblokir (blacklist), meskipun <strong>nomor
                                                        identitas
                                                        berbeda</strong> (misal pakai SIM/KTP berbeda), sistem tetap
                                                    akan menolak
                                                    kunjungan.</li>
                                                <li>Blacklist dilakukan berdasarkan catatan sebelumnya karena alasan
                                                    tertentu
                                                    seperti pelanggaran, ancaman keamanan, atau masalah lain.</li>
                                                <li>Jika sistem mendeteksi identitas yang diblokir, form akan otomatis
                                                    menolak
                                                    proses kunjungan.</li>
                                                <li><strong>Security wajib mencocokkan identitas secara visual</strong>
                                                    dengan data
                                                    blacklist jika ada indikasi mencurigakan.</li>
                                            </ul>
                                            <p class="mb-0"><strong>Catatan:</strong> Pastikan nama dan tanggal lahir
                                                pengunjung
                                                dimasukkan dengan benar agar sistem bisa mendeteksi blacklist dengan
                                                akurat.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Saran Urutan Pengisian -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info border border-2 border-primary mt-3">
                                        <h5 class="fw-bold text-primary">
                                            <i class="fas fa-info-circle me-1"></i> Petunjuk Pengisian Formulir
                                        </h5>
                                        <ul class="mb-1">
                                            <li>
                                                Disarankan untuk melakukan <strong>pengambilan foto KTP dan selfie
                                                    terlebih dahulu</strong> sebelum mengisi bagian lain dalam formulir.
                                            </li>
                                            <li>
                                                Langkah ini bertujuan untuk <strong>menghindari kelalaian</strong> dalam
                                                proses pengisian data dan memastikan seluruh data yang dibutuhkan telah
                                                tersedia.
                                            </li>
                                            <li>
                                                Dengan mengikuti urutan tersebut, sistem dapat melakukan validasi data
                                                secara <strong>lebih cepat dan akurat</strong>, serta meminimalkan
                                                potensi kesalahan saat memasukkan nomor kartu RFID.
                                            </li>
                                        </ul>
                                        <p class="mb-0">
                                            Silakan gunakan tombol kamera yang tersedia untuk memulai proses pengambilan
                                            foto.
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">
                        <button type="button"
                            class="btn btn-outline-primary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i>
                            <span>Refresh Halaman</span>
                        </button>

                        <!-- Reset Button -->
                        <button type="button"
                            class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="resetForm()" id="resetBtn" data-bs-toggle="tooltip" data-bs-placement="top"
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

                </form>
            </div>
        </div>
    </div>
</div>

<!-- KTP Modals -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Foto KTP</h5>
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

                <button id="saveBtn" type="button" class="btn btn-primary"
                    onclick="saveCaptureIdentitas()">Simpan Foto</button>
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

    {{-- <script type="module" src="{{ asset('assets/js/pos-security/formulir/pages/formulir-tamu-index.js') }}"></script> --}}
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-tamu-input.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-tamu-input-store2.js') }}"></script>

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
@endpush
