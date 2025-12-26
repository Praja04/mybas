{{-- Guide Modal --}}
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
                                <i class="fas fa-sign-in-alt me-2"></i>Kendaraan IN
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="formOut-tab" data-bs-toggle="tab" data-bs-target="#formOut"
                                type="button" role="tab" aria-controls="formOut" aria-selected="false">
                                <i class="fas fa-sign-out-alt me-2"></i>Kendaraan OUT
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
                                    <h5 class="mb-3"><i class="mdi mdi-information-outline me-2"></i>
                                        Panduan Pengisian Formulir Pengecekan Kendaraan Masuk
                                    </h5>

                                    <ul class="mb-0 ps-3">
                                        <li>
                                            Saat halaman dibuka, sistem akan menampilkan daftar kendaraan yang
                                            <strong>belum melakukan
                                                pengecekan masuk</strong>. Pilih nomor kendaraan yang akan diperiksa.
                                        </li>
                                        <li>
                                            Setelah memilih nomor polisi, data pengunjung akan ditampilkan secara
                                            otomatis.
                                        </li>
                                        <li>
                                            Periksa kembali data pengunjung yang ditampilkan, termasuk:
                                            <ul class="mb-0 ps-3">
                                                <li>Nama Supir</li>
                                                <li>Nama Perusahaan</li>
                                                <li>Nomor Polisi Kendaraan</li>
                                            </ul>
                                        </li>
                                        <li>
                                            Masukkan data pemeriksaan, meliputi:
                                            <ul class="mb-0 ps-3">
                                                <li>
                                                    <strong>Nama Petugas Pemeriksa</strong>: Isi dengan nama lengkap
                                                    petugas yang
                                                    melakukan pemeriksaan kendaraan.
                                                </li>
                                                <li>
                                                    <strong>Jenis Muatan</strong>: Pilih jenis muatan truk
                                                    (LIQUID: cairan, NONLIQUID: bukan cairan).
                                                </li>
                                                <li>
                                                    <strong>Jenis Truk</strong>: Akan muncul menyesuaikan dengan jenis
                                                    muatan yang
                                                    dipilih. Pilih jenis truk yang sesuai.
                                                </li>
                                                <li>
                                                    <strong>Jenis Truk Lainnya</strong>: Jika memilih "Lainnya", kolom
                                                    ini akan muncul.
                                                    Masukkan jenis truk yang tidak tersedia pada pilihan.
                                                </li>
                                                <li>
                                                    <strong>Foto Kendaraan</strong>: Ambil dan unggah foto-foto yang
                                                    diperlukan sesuai
                                                    dengan jenis truk.
                                                </li>
                                            </ul>
                                        </li>
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
                                    <h5 class="mb-3"><i class="mdi mdi-information-outline me-2"></i>
                                        Panduan Pengisian Formulir Pengecekan Kendaraan Masuk
                                    </h5>

                                    <ul class="mb-0 ps-3">
                                        <li>
                                            Saat halaman dibuka, sistem akan menampilkan daftar kendaraan yang
                                            <strong>sudah melakukan
                                                pengecekan masuk</strong>, tetapi <strong>belum melakukan pengecekan
                                                keluar</strong>.
                                            Pilih nomor polisi kendaraan yang akan diperiksa.
                                        </li>
                                        <li>
                                            Setelah memilih nomor polisi, data pengunjung akan ditampilkan secara
                                            otomatis.
                                        </li>
                                        <li>
                                            Periksa kembali data pengunjung yang ditampilkan, termasuk:
                                            <ul class="mb-0 ps-3">
                                                <li>Nama Supir</li>
                                                <li>Nama Perusahaan</li>
                                                <li>Nomor Polisi Kendaraan</li>
                                                <li>Waktu Masuk</li>
                                                <li>Jenis Muatan</li>
                                                <li>Jenis Truk</li>
                                            </ul>
                                        </li>
                                        <li>
                                            Masukkan data pemeriksaan, meliputi:
                                            <ul class="mb-0 ps-3">
                                                <li>
                                                    <strong>Nama Petugas Pemeriksa</strong>: Isi dengan nama lengkap
                                                    petugas yang
                                                    melakukan pemeriksaan kendaraan.
                                                </li>
                                                <li>
                                                    <strong>Foto Kendaraan</strong>: Ambil dan unggah foto-foto yang
                                                    diperlukan sesuai
                                                    dengan jenis truk.
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>

                                    <hr class="my-3">
                                    <p class="mb-0 text-muted">
                                        Pastikan seluruh data yang diisi sesuai dan valid sebelum klik tombol
                                        <strong>"Simpan Data"</strong>.
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

{{-- Card IN - OUT --}}
<div class="col-xl-12">
    <!-- Custom Tabs with Card Style -->
    <div class="mb-3 row align-items-stretch g-3">
        <!-- Tab Buttons -->
        <div class="col-auto d-flex flex-wrap gap-2" id="formTabs-pos1" role="tablist">
            <button class="tab-card active" id="cek-kendaraan-in-tab" data-bs-toggle="tab"
                data-bs-target="#cek-kendaraan-in" type="button" role="tab" aria-controls="cek-kendaraan-in"
                aria-selected="true">
                MASUK (IN) </button>
            <button class="tab-card" id="cek-kendaraan-out-tab" data-bs-toggle="tab" data-bs-target="#cek-kendaraan-out"
                type="button" role="tab" aria-controls="cek-kendaraan-out" aria-selected="false">
                KELUAR (OUT) </button>
        </div>

        <!-- Info Card -->
        <div class="col">
            <div class="card overflow-hidden h-100">
                <div class="card-body bg-marketplace d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fs-18 lh-base mb-0">Formulir Pengecekan Kendaraan
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
