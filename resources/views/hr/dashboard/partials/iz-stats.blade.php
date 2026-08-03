{{-- Izin Stat Cards + Line Charts: inside fullscreen wrap --}}
<div class="hd-tab-content" id="hdIzinSection" style="display:none;">
    <div class="hd-section">
        <div class="d-flex justify-content-center align-items-center mb-2">
            <h5 class="mb-0" style="color:#4a148c; font-weight:700;">Lost Workdays Dashboard</h5>
            <button type="button" class="hd-fs-btn" data-target="hdFullscreenWrap" title="Fullscreen / Auto-cycle">
                <span class="hd-fs-icon-expand">&#x26F6;</span>
            </button>
        </div>

        <div class="row" id="izinStatsRow" style="display:flex; align-items:stretch;">
            <div class="col mb-3">
                <div class="hd-stat h-100" style="background:#fce4ec; color:#c2185b;">
                    <div class="hd-stat-value" id="izinStatTotalHariIzin">0</div>
                    <div class="hd-stat-label">Total Hari Kerja Hilang</div>
                    <div class="hd-stat-label" id="izinStatSubtitle">(Sakit + Sakit KK + Mangkir + Cuti + Minggu)</div>
                    <div class="mt-1" id="izinTipeToggle" style="display:flex; gap:4px; justify-content:center;">
                        <button type="button" class="btn btn-sm btn-light izin-tipe-btn active" data-tipe="1" style="font-size:.7rem; padding:1px 8px; border:1px solid #c2185b; color:#c2185b;">Tipe 1</button>
                        <button type="button" class="btn btn-sm btn-light izin-tipe-btn" data-tipe="2" style="font-size:.7rem; padding:1px 8px; border:1px solid #c2185b; color:#c2185b;">Tipe 2</button>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="hd-stat hd-stat-orange h-100">
                    <div class="hd-stat-value" id="izinStatTotalHariSakit">0</div>
                    <div class="hd-stat-label">Total Hari Sakit</div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="hd-stat h-100" style="background:#fff3e0; color:#e65100;">
                    <div class="hd-stat-value" id="izinStatTotalHariSakitKK">0</div>
                    <div class="hd-stat-label">Total Hari Sakit KK</div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="hd-stat h-100" style="background:#f3e5f5; color:#6a1b9a;">
                    <div class="hd-stat-value" id="izinStatTotalHariMangkir">0</div>
                    <div class="hd-stat-label">Total Hari Mangkir</div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="hd-stat hd-stat-blue h-100">
                    <div class="hd-stat-value" id="izinStatTotalHariCuti">0</div>
                    <div class="hd-stat-label">Total Hari Cuti</div>
                </div>
            </div>
            <div class="col mb-3 izin-card-tipe1-only">
                <div class="hd-stat h-100" style="background:#e8f5e9; color:#2e7d32;">
                    <div class="hd-stat-value" id="izinStatTotalHariMinggu">0</div>
                    <div class="hd-stat-label">Total Hari Minggu</div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h5 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jumlah Hari Ijin Setiap Bulan
                    </h5>
                    <div id="izinChartBulanan" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h5 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jumlah Karyawan Ijin Setiap Bulan
                    </h5>
                    <div id="izinChartKaryawanBulanan" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
