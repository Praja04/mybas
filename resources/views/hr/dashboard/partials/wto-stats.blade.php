{{-- WT&O Stat Cards: stays INSIDE fullscreen wrap (will be fullscreened) --}}
<x-highcharts-js/>
<div class="hd-tab-content" id="hdWtoSection" style="display:none;">
    <div class="hd-section">
        <div class="d-flex justify-content-center align-items-center mb-2">
            <h5 class="mb-0" style="color:#4a148c; font-weight:700;">Working Time &amp; Overtime</h5>
            <button type="button" class="hd-fs-btn" data-target="hdFullscreenWrap" title="Fullscreen / Auto-cycle">
                <span class="hd-fs-icon-expand">&#x26F6;</span>
            </button>
        </div>

        <div class="row" id="wtoStatsRow" style="display:flex; align-items:stretch;">
            <div class="col-md-3 mb-3">
                <div class="hd-stat hd-stat-blue h-100">
                    <div class="hd-stat-value" id="wtoStatTotalLembur">0</div>
                    <div class="hd-stat-label">Total Jam Lembur</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="hd-stat hd-stat-green h-100">
                    <div class="hd-stat-value" id="wtoStatHariKerja">0</div>
                    <div class="hd-stat-label">Jam Lembur (Hari Kerja)</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="hd-stat hd-stat-orange h-100">
                    <div class="hd-stat-value" id="wtoStatHariLibur">0</div>
                    <div class="hd-stat-label">Jam Lembur (Hari Libur)</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="hd-stat h-100" style="background:#f3e5f5; color:#6a1b9a;">
                    <div class="hd-stat-value" id="wtoStatKaryawanLembur">0</div>
                    <div class="hd-stat-label">Total Karyawan Lembur</div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h5 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jumlah Karyawan Lembur (Hari Kerja)
                    </h5>
                    <div id="wtoChartKaryawan" style="min-height: 210px;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h5 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jumlah Karyawan Lembur (Hari Libur)
                    </h5>
                    <div id="wtoChartKaryawanLibur" style="min-height: 210px;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h5 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jam Lembur
                    </h5>
                    <div id="wtoChartJamLembur" style="min-height: 210px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
