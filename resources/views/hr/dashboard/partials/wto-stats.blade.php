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
                    <h4 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jumlah Karyawan Lembur
                    </h4>
                    <div id="wtoChartKaryawan" style="min-height: 240px;"></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h4 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jam Lembur per Departemen
                    </h4>
                    <div style="overflow-x: auto;"><div id="wtoChartJamLemburPerDept" style="min-height: 250px;"></div></div>
                    <div id="wtoChartJamLemburPerDeptLegend" style="
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        align-items: center;
                        gap: .85rem 1.25rem;
                        padding: .55rem .75rem;
                        margin-top: .5rem;
                        background: rgba(74, 20, 140, 0.05);
                        border: 1px solid rgba(74, 20, 140, 0.18);
                        border-radius: 4px;
                        font-size: 14px;
                        font-weight: 600;
                    "></div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="hd-card">
                    <h4 style="display:flex; align-items:center; gap:.4rem;">
                        <span class="hd-chevron" style="visibility:hidden;"></span>
                        Grafik Jam Lembur
                    </h4>
                    <div id="wtoChartJamLembur" style="min-height: 240px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
