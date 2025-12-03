<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="filter-form-kartu-sering"> {{-- ID unik untuk form ini --}}
                    <div class="row g-3">

                        <!-- Filter POS -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">POS</label>
                            <select class="form-select" name="pos">
                                {{-- <option value="POS 1">POS 1</option> --}}
                                <option value="POS 2" selected>POS 2</option> {{-- Default ke POS 2 --}}
                            </select>
                        </div>

                        <!-- Type Kartu -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Type Kartu</label>
                            <select class="form-select" name="type">
                                <option value="">Semua Tipe</option>
                                <option value="VENDOR">VENDOR</option>
                                <option value="TAMU">TAMU</option>
                                <option value="TRANSPORTER">TRANSPORTER</option>
                                {{-- Tambahkan opsi lain jika ada --}}
                            </select>
                        </div>

                        <!-- No. Kartu (Opsional untuk filter) -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">No. Kartu (Opsional)</label>
                            <input type="text" class="form-control" name="no_kartu"
                                placeholder="Cari No. Kartu Spesifik" />
                        </div>

                        <!-- Filter Tanggal (Opsional) -->
                        {{-- Jika kamu ingin menambahkan filter tanggal, aktifkan bagian ini --}}
                        {{-- 
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Tanggal Penggunaan</label>
                            <input type="text" class="form-control flatpickr-range" name="tanggal_penggunaan"
                                placeholder="Pilih rentang tanggal" />
                        </div>
                        --}}

                        <!-- Tombol Aksi -->
                        <div class="col-xl-3 col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-search-line me-1 align-middle"></i> Filter
                            </button>
                            <button type="reset" class="btn btn-light w-100 border">
                                <i class="ri-refresh-line me-1 align-middle"></i> Reset
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
