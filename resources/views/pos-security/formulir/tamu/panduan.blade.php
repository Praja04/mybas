<div class="modal fade" id="panduanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">

                <!-- Tabs IN / OUT -->
                <div class="card p-4 shadow-sm">
                    <ul class="nav nav-tabs mb-4" id="formTabsInModal" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="formIn-tab" data-bs-toggle="tab"
                                data-bs-target="#formIn" type="button" role="tab" aria-controls="formIn"
                                aria-selected="true">
                                <i class="fas fa-sign-in-alt me-2"></i>MASUK (IN)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="formOut-tab" data-bs-toggle="tab" data-bs-target="#formOut"
                                type="button" role="tab" aria-controls="formOut" aria-selected="false">
                                <i class="fas fa-sign-out-alt me-2"></i>KELUAR (OUT)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="formTabsContentInModal">
                        <!-- TAB IN -->
                        <div class="tab-pane fade show active" id="formIn" role="tabpanel"
                            aria-labelledby="formIn-tab">
                            <div class=" p-4 form-container">

                                <!-- PANDUAN FORMULIR VENDOR / TAMU / TRANSPORTER -->
                                <div class="alert alert-info mb-4" role="alert">
                                    <h5 class="mb-3"><i class="mdi mdi-information-outline me-2"></i>Panduan Pengisian
                                        Formulir Vendor / Tamu / Transporter</h5>
                                    <ul class="mb-0 ps-3">
                                        <li><strong>Nama Vendor / Tamu / Transporter</strong>:
                                            <br>Masukkan nama lengkap dari tamu, vendor, atau transporter yang
                                            berkunjung.
                                        </li>
                                        <li><strong>Nama Perusahaan / Instansi</strong>:
                                            <br>Tulis nama perusahaan / instansi asal pengunjung.
                                        </li>
                                        <li><strong>Jenis Kunjungan</strong>:
                                            <br>Pilih salah satu jenis kunjungan: <strong>Vendor</strong>,
                                            <strong>Tamu</strong>, atau <strong>Transporter</strong>.
                                        </li>
                                        <li><strong>Jumlah Orang</strong>:
                                            <br>Masukkan total jumlah orang yang ikut dalam kunjungan (default 1 orang).
                                        </li>
                                        <li><strong>Keperluan</strong>:
                                            <br>Isi tujuan atau keperluan kunjungan, misalnya: <em>Meeting, Kirim
                                                Barang, Maintenance</em>, dsb.
                                        </li>
                                        <li><strong>No KTP / SIM</strong>:
                                            <br>Masukkan nomor identitas pengunjung (opsional, tapi disarankan untuk
                                            validasi).
                                        </li>
                                        <li><strong>Nama PIC / Bertemu Dengan</strong>:
                                            <br>Tulis nama karyawan internal yang akan ditemui.
                                        </li>
                                        <li><strong>Departemen</strong>:
                                            <br>Isi nama departemen atau divisi tempat PIC bekerja.
                                        </li>
                                        <li><strong>Nomor Kartu</strong>:
                                            <br>Input nomor kartu yang akan diberikan kepada pengunjung (bisa manual
                                            atau scan).
                                        </li>
                                        <li><strong>Foto KTP / SIM</strong>:
                                            <br>Ambil foto KTP atau SIM sebagai bukti identitas.
                                        </li>
                                        <li><strong>Foto Diri</strong>:
                                            <br>Ambil foto selfie pengunjung, untuk keperluan keamanan dan dokumentasi.
                                        </li>
                                        <li><strong class="text-danger">[Khusus untuk Transporter]</strong>:
                                            <ul class="ps-3">
                                                <li><strong>Nomor Polisi</strong>: Wajib diisi dengan plat nomor
                                                    kendaraan yang digunakan.</li>
                                                <li><strong>Tujuan</strong>: Wajib pilih apakah kunjungan untuk
                                                    <strong>Bongkar</strong> atau <strong>Muatan</strong>.
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <hr class="my-3">
                                    <p class="mb-0 text-muted">
                                        Pastikan seluruh data diisi dengan lengkap & benar sebelum menekan tombol
                                        <strong>"Simpan Data"</strong>.
                                        <br>Data digunakan untuk keperluan keamanan, validasi, dan dokumentasi
                                        kunjungan.
                                    </p>
                                </div>

                            </div>
                        </div>

                        <!-- TAB OUT -->
                        <div class="tab-pane fade" id="formOut" role="tabpanel" aria-labelledby="formOut-tab">
                            <div class="p-4 form-container">

                                <!-- PANDUAN OUT -->
                                <div class="alert alert-warning mb-4" role="alert">
                                    <h5 class="mb-3"><i class="mdi mdi-information-outline me-2"></i>Panduan Pengisian
                                        Formulir Supplier (OUT)</h5>
                                    <ul class="mb-0 ps-3">
                                        <li><strong>Scan Kartu RFID</strong>:
                                            <br>Scan kartu RFID yang digunakan saat <strong>masuk area</strong> untuk
                                            memuat data pengunjung.
                                        </li>
                                        <li>Pastikan kartu yang discan benar dan sesuai dengan data saat
                                            <strong>check-in</strong>.
                                        </li>
                                        <li>Setelah berhasil scan, data visitor akan muncul secara otomatis.</li>
                                        <li>Periksa kembali data visitor yang ditampilkan, termasuk:
                                            <ul class="mb-0 ps-3">
                                                <li>Nama Visitor</li>
                                                <li>Nama Perusahaan</li>
                                                <li>Nomor Polisi Kendaraan</li>
                                                <li>Nomor KTP / SIM</li>
                                                <li>Jumlah Orang</li>
                                                <li>Tanggal & Waktu Masuk</li>
                                                <li>Foto KTP / SIM & Foto Diri</li>
                                            </ul>
                                        </li>
                                        <li>Jika data sudah sesuai, klik tombol <strong>"Kartu Dikembalikan"</strong>
                                            untuk mengonfirmasi bahwa visitor telah keluar area dan kartu sudah
                                            dikembalikan.</li>
                                    </ul>
                                    <hr class="my-3">
                                    <p class="mb-0 text-muted">
                                        Pastikan proses pengembalian kartu dilakukan <strong>langsung saat visitor
                                            keluar</strong>, untuk menjaga akurasi log keluar dan pengendalian akses.
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>

    </div>
</div>

<div class="col-xl-12">
    <!-- Custom Tabs with Card Style for POS 2 -->
    <div class="mb-3 row g-3 align-items-stretch">
        <!-- Tab Buttons -->
        <div class="col-auto d-flex flex-wrap gap-2" id="formTabs-pos2" role="tablist">
            <button class="tab-card active" id="tamu-in-tab" data-bs-toggle="tab" data-bs-target="#tamu-in"
                type="button" role="tab" aria-controls="tamu-in" aria-selected="true">
                POS 2 MASUK
            </button>
            <button class="tab-card" id="tamu-out-tab" data-bs-toggle="tab" data-bs-target="#tamu-out" type="button"
                role="tab" aria-controls="tamu-out" aria-selected="false">
                POS 2 KELUAR
            </button>
        </div>

        <!-- Info Card -->
        <div class="col">
            <div class="card overflow-hidden h-100">
                <div class="card-body bg-marketplace d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fs-18 lh-base mb-0">Formulir Tamu / Vendor
                        </h4>
                    </div>
                    <div class="mt-3">
                        <a href="#!" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#panduanModal">
                            <i class="mdi mdi-information-outline"></i> Panduan Pengisian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
