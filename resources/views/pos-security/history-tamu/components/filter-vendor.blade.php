<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="filter-form">
                    <div class="row g-3">

                        <!-- Filter Tanggal -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Tanggal Masuk</label>
                            <input type="text" class="form-control flatpickr-range" name="tanggal_masuk"
                                placeholder="Pilih rentang tanggal" />
                        </div>

                        <!-- Nama Visitor -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Nama Visitor</label>
                            <input type="text" class="form-control" name="nama_visitor"
                                placeholder="Contoh: Budi Santoso" />
                        </div>

                        <!-- No Polisi -->
                        {{-- <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">No. Polisi</label>
                            <input type="text" class="form-control" name="nopol" placeholder="Contoh: B 1234 CD" />
                        </div>

                        <!-- No KTP/SIM -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">No. KTP/SIM</label>
                            <input type="text" class="form-control" name="no_ktp_sim" placeholder="KTP/SIM" />
                        </div> --}}

                        <!-- No. Kartu -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">No. Kartu / RFID</label>
                            <input type="text" class="form-control" name="no_kartu" placeholder="No. Kartu" />
                        </div>

                        <!-- type -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Jenis Kartu</label>
                            <select class="form-select" name="type">
                                <option value="">Semua</option>
                                <option value="vendor">Vendor</option>
                                <option value="tamu">Tamu</option>
                                <option value="transporter">Transporter</option>
                            </select>
                        </div>

                        <!-- Kartu Dikembalikan -->
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">Kartu Dikembalikan</label>
                            <select class="form-select" name="kartu_dikembalikan">
                                <option value="">Semua</option>
                                <option value="1">Sudah</option>
                                <option value="0">Belum</option>
                            </select>
                        </div>

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
