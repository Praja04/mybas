<div class="card mb-4">
    <div class="card-body row g-3">
        <div class="col-md-4 col-12">
            <label class="form-label">Jenis Kartu</label>
            <select id="jenis_kartu" class="form-select">
                <option value="">Semua</option>
                <option value="Vendor">Vendor</option>
                <option value="Tamu">Tamu</option>
                <option value="Transporter">Transporter</option>
            </select>
        </div>
        <div class="col-md-4 col-12">
            <label class="form-label">POS</label>
            <select id="pos" class="form-select">
                <option value="POS 1">POS 1</option>
                <option value="POS 2">POS 2</option>
            </select>
        </div>
        <div class="col-md-4 col-12 d-flex flex-column flex-md-row gap-2 align-items-md-end">
            <button id="btn-filter" class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
                type="submit">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <button type="button"
                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center"
                onclick="hotReload()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh halaman
            </button>
        </div>
    </div>
</div>
