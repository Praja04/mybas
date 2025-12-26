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
                                <i class="fas fa-sign-in-alt me-2"></i>Supplier IN
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="formOut-tab" data-bs-toggle="tab" data-bs-target="#formOut"
                                type="button" role="tab" aria-controls="formOut" aria-selected="false">
                                <i class="fas fa-sign-out-alt me-2"></i>Supplier OUT
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="formTabsContentInModal">
                        <!-- TAB IN -->
                        <div class="tab-pane fade show active" id="formIn" role="tabpanel"
                            aria-labelledby="formIn-tab">
                            <div class=" p-4 form-container">

                                <!-- PANDUAN IN -->
                                <div class="alert alert-primary mb-4" role="alert">
                                    <h5 class="mb-3"><i class="mdi mdi-information-outline me-2"></i>Panduan Pengisian
                                        Formulir Supplier (IN)</h5>
                                    <ul class="mb-0 ps-3">
                                        <li><strong>Nama Supir / Kernet</strong>: Isi dengan nama lengkap supir atau
                                            kernet yang
                                            membawa kendaraan.</li>
                                        <li><strong>Keterangan Pengunjung</strong>: Pilih peran dari
                                            nama pengunjung yang diinput. Jika yang bersangkutan berperan sebagai supir,
                                            pilih
                                            <strong>Supir</strong>. Jika berperan sebagai kernet, pilih
                                            <strong>Kernet</strong>.

                                        </li>
                                        <li><strong>Nama Perusahaan</strong>: Isi nama perusahaan / supplier yang
                                            mengirimkan barang.</li>
                                        <li><strong>Tanggal Lahir</strong>: Masukkan tanggal lahir driver sesuai
                                            identitas (KTP/SIM). Wajib untuk keperluan pengecekan blacklist sistem.</li>
                                        <li><strong>Nomor KTP / SIM</strong>: Masukkan nomor KTP atau SIM driver.</li>
                                        <li><strong>Tujuan</strong>: Pilih <strong>MUAT</strong> jika akan membawa
                                            barang keluar, pilih <strong>BONGKAR</strong> jika membawa barang masuk.
                                        </li>
                                        <li><strong>Nomor Polisi</strong>: Isi dengan plat nomor kendaraan yang
                                            digunakan (contoh: B 1234 AB). <strong>Nomor polisi ini akan digunakan untuk
                                                proses
                                                pengecekan kendaraan.
                                            </strong></li>
                                        <li><strong>No HP Driver</strong>: Masukkan nomor handphone aktif driver
                                        </li>
                                        <li><strong>Nomor Kartu ID (RFID)</strong>: Masukkan nomor kartu ID yang akan
                                            digunakan oleh driver.</li>
                                        <li><strong>Foto KTP / SIM</strong>: Wajib ambil foto KTP atau SIM yang masih
                                            berlaku milik driver.</li>
                                        <li><strong>Foto Diri</strong>: Ambil foto selfie driver di lokasi.</li>
                                    </ul>
                                    <hr class="my-3">
                                    <p class="mb-0 text-muted">
                                        Pastikan seluruh data yang diisi sesuai dan valid sebelum klik tombol
                                        <strong>"Simpan Data"</strong>.
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
                                        <li>Setelah berhasil scan, data pengunjung akan muncul secara otomatis.</li>
                                        <li>Periksa kembali data pengunjung yang ditampilkan, termasuk:
                                            <ul class="mb-0 ps-3">
                                                <li>Nama Supir/Kernet</li>
                                                <li>Sebagai Apa (Supir/Kernet)</li>
                                                <li>Nama Perusahaan</li>
                                                <li>Nomor Kartu</li>
                                                <li>Nomor KTP / SIM</li>
                                                <li>Nomor Polisi Kendaraan</li>
                                                <li>Jumlah Orang</li>
                                                <li>Tanggal & Waktu Masuk</li>
                                                <li>Status Kartu</li>
                                                <li>Foto KTP / SIM & Foto Diri</li>
                                            </ul>
                                        </li>
                                        <li>Jika data sudah sesuai, klik tombol <strong>"Kartu Dikembalikan"</strong>
                                            untuk mengonfirmasi bahwa pengunjung telah keluar area dan kartu sudah
                                            dikembalikan.</li>
                                    </ul>
                                    <hr class="my-3">
                                    <p class="mb-0 text-muted">
                                        Pastikan proses pengembalian kartu dilakukan <strong>langsung saat pengunjung
                                            keluar</strong>, untuk menjaga akurasi data waktu keluar.
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
    <!-- Custom Tabs with Card Style -->
    <div class="mb-3 row align-items-stretch g-3">
        <!-- Tab Buttons -->
        <div class="col-auto d-flex flex-wrap gap-2" id="formTabs-pos1" role="tablist">
            <button class="tab-card active" id="supplier-in-tab" data-bs-toggle="tab" data-bs-target="#supplier-in"
                type="button" role="tab" aria-controls="supplier-in" aria-selected="true">
                MASUK (IN) </button>
            <button class="tab-card" id="supplier-out-tab" data-bs-toggle="tab" data-bs-target="#supplier-out"
                type="button" role="tab" aria-controls="supplier-out" aria-selected="false">
                KELUAR (OUT) </button>
        </div>

        <!-- Info Card -->
        <div class="col">
            <div class="card overflow-hidden h-100">
                <div class="card-body bg-marketplace d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fs-18 lh-base mb-0">Formulir Data Supplier/Transporter
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
