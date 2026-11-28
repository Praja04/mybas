<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form class="filter-form-supplier">
                    <div class="row g-3">

                        <!-- Filter Tanggal -->
                        <div class="col-xl-6 col-md-8">
                            <label class="form-label fw-semibold text-muted">Tanggal Masuk</label>
                            <input type="text" class="form-control flatpickr-range" name="tanggal_masuk"
                                placeholder="Pilih rentang tanggal" />
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="col-xl-6 col-md-4 d-flex align-items-end gap-2">
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
