@push('panduan')
    @include('pos-security.formulir.supplier.panduan')
@endpush

{{-- main modal --}}
<div class="tab-pane fade show active" id="supplier-in" role="tabpanel" aria-labelledby="supplier-in-tab">
    <div class="row justify-content-center my-5">
        <div class="col-lg-12">
            <div class="card p-5 shadow-sm form-container">

                {{-- Header --}}
                <div class="text-end mb-4 mt-3">
                    <h2 class="fw-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Form Data Transporter (Bongkar / Muat)
                    </h2>
                    <p class="text-muted mb-0">Silakan isi data supir / kernet yang akan masuk ke area bongkar/muat</p>
                </div>

                <div id="formAlert" class="alert mt-3" style="display: none;"></div>

                <form id="visitorForm" action="{{ route('ajax.pos-security.visitor-transaksi.store') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="return false;">
                    @csrf
                    <input type="hidden" name="createdby" id="createdby">
                    <input type="hidden" name="sumpeople" value="1">

                    <div class="row">
                        <div class="col-lg-6">

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="keterangan">Keterangan Pengunjung <span
                                        class="text-danger">*</span></label>
                                <select name="keterangan" id="keterangan" class="form-select" required>
                                    <option value="" selected disabled> -- Pilih Keterangan --</option>
                                    <option value="supir">Supir</option>
                                    <option value="kernet">Kernet</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="namavisitor">Nama Supir / Kernet <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="namavisitor" id="namavisitor" required
                                    style="text-transform: uppercase;" placeholder="Masukkan nama supir/kernet">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="namacomp">Nama Perusahaan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namacomp" name="namacomp" required
                                    style="text-transform: uppercase;" placeholder="Masukkan nama perusahaan">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nohpdriver">Nomor HP <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nohpdriver" name="nohpdriver" required
                                    style="text-transform: uppercase;" placeholder="Masukkan nomor HP aktif">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nomor-ktp">No. KTP / SIM <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nomorktp" id="nomor-ktp" required
                                    style="text-transform: uppercase;" placeholder="Masukkan nomor identitas">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nopol">Nomor Polisi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nopol" name="nopol" required
                                    style="text-transform: uppercase;" placeholder="Contoh: B 1234 CD">
                            </div>

                            <div class="mb-3" id="parkingSlotContainer" style="display: none;">
                                <label class="form-label fw-semibold d-flex align-items-center justify-content-between"
                                    for="parking_slot_id">
                                    <span><i class="mdi mdi-car-parking-lot text-primary me-1"></i> Plotting Lokasi Area
                                        Parkir</span>
                                    <span class="badge bg-light text-muted border fw-normal">Opsional</span>
                                </label>

                                <input type="hidden" name="parking_slot_id" id="parking_slot_id" value="">

                                <!-- Selected Slot Display Card -->
                                <div id="selectedSlotCard"
                                    class="card border border-success bg-success-subtle shadow-sm mb-2"
                                    style="display: none; border-left: 5px solid #198754 !important;">
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4">
                                                <i class="mdi mdi-parking"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0 fw-bold text-dark fs-5"
                                                        id="selectedSlotCodeDisplay">Slot</h6>
                                                    <span class="badge bg-success"><i
                                                            class="mdi mdi-check-circle me-1"></i>Tersedia</span>
                                                    <span class="badge bg-info-subtle text-info border border-info"
                                                        id="selectedSlotVehicleDisplay" style="display: none;"></span>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="mdi mdi-map-marker text-danger me-1"></i><span
                                                        id="selectedSlotZoneDisplay">Zona</span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold"
                                                onclick="openParkingSlotModal()">
                                                <i class="mdi mdi-swap-horizontal me-1"></i> Ganti Slot
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="clearParkingSlotSelection()" title="Batalkan pilihan slot">
                                                <i class="mdi mdi-close me-1"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty State Trigger Card -->
                                <div id="emptySlotPickerCard"
                                    class="card border border-2 border-dashed border-primary-subtle bg-light text-center p-3 mb-2"
                                    onclick="openParkingSlotModal()"
                                    style="cursor: pointer; transition: all 0.2s ease-in-out;">
                                    <div
                                        class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <div
                                                class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fs-3">
                                                <i class="mdi mdi-car-parking-lot"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark d-block">Pilih Lokasi / Slot Parkir
                                                    (Opsional)</span>
                                                <small class="text-muted">Klik untuk membuka denah visual kotak parkir
                                                    & memilih slot kosong.</small>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-primary px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 text-nowrap">
                                            <i class="mdi mdi-view-grid-plus fs-6"></i>
                                            <span>Pilih Slot Parkir</span>
                                        </button>
                                    </div>
                                </div>

                                <small class="text-muted"><i class="mdi mdi-information-outline me-1"></i>Pilih area
                                    slot parkir yang telah disediakan untuk kendaraan transporter supir.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Kartu ID</label>
                                <input type="text" class="form-control" name="rfid"
                                    placeholder="Scan atau masukkan nomor kartu RFID" disabled>
                                <!-- Pesan ini akan ditambahkan secara otomatis oleh JavaScript -->
                            </div>

                        </div>

                        <div class="col-lg-6">
                            <div class="alert alert-warning border-2 border-danger mt-2">
                                <h5 class="fw-bold text-danger"><i class="fas fa-ban me-1"></i> Peringatan Sistem
                                    Pemblokiran (Blacklist)</h5>
                                <ul class="mb-1">
                                    <li>Setiap pengunjung akan dicek otomatis berdasarkan <strong>Nama</strong> dan
                                        <strong>Nomor KTP/SIM</strong>.
                                    </li>
                                    <li>Jika sudah pernah diblokir (blacklist), meskipun <strong>nomor identitas
                                            berbeda</strong>, sistem tetap akan menolak kunjungan.</li>
                                    <li>Jika sistem mendeteksi identitas yang diblokir, form akan otomatis menolak
                                        proses kunjungan.</li>
                                    <li><strong>Security wajib mencocokkan identitas secara visual</strong> dengan data
                                        blacklist jika ada indikasi mencurigakan.</li>
                                </ul>
                                <p class="mb-0"><strong>Catatan:</strong> Pastikan nama dan nomor identitas
                                    pengunjung dimasukkan dengan benar.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-start mb-4">
                        <a href="{{ route('pos-security.formulir') }}"
                            class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2">
                            <i class="mdi mdi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>

                        <button type="button"
                            class="btn btn-outline-primary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i>
                            <span>Refresh Halaman</span>
                        </button>

                        <button type="button"
                            class="btn btn-outline-secondary px-4 py-2 d-flex align-items-center gap-2"
                            onclick="resetForm()" id="resetBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Kosongkan semua isian">
                            <i class="mdi mdi-eraser"></i>
                            <span>Reset Form</span>
                        </button>

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

{{-- Modal Pilih Slot Parkir --}}
@include('pos-security.formulir.supplier.modal-parking-slot')

@push('scripts')
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-supplier-input2.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/formulir/pages/formulir-supplier-input-store.js') }}"></script>
@endpush
