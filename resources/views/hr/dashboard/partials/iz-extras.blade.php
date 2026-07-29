{{-- Izin Data Table: outside fullscreen wrap --}}
<div id="izinExtras" style="display:none;">
    <div class="hd-card" id="izinDataCard">
        <h5 class="hd-card-toggle" data-target="#izinTableCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Data Karyawan Izin
        </h5>
        <div class="collapse show" id="izinTableCollapse">
            <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.82rem;">
                <div>
                    <span class="text-muted">Tampilkan</span>
                    <select id="izinPerPage" class="form-control form-control-sm d-inline-block" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-muted">per halaman</span>
                </div>
                <div class="text-muted" id="izinInfo"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered hd-table">
                    <thead class="thead-light">
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Dept</th>
                            <th>Sub Departmen</th>
                            <th>Group</th>
                            <th>Section</th>
                            <th>TGL</th>
                            <th>Kode Ijin</th>
                            <th>Ijin</th>
                            <th>Keterangan</th>
                            <th>No SPI</th>
                        </tr>
                    </thead>
                    <tbody id="izinTbody">
                        <tr><td colspan="11" class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2" id="izinPagination"></div>
        </div>
    </div>

    @if (isset($canViewTopSkmk) && $canViewTopSkmk)
    {{-- Web Speech API: zero server-side dependency, suara dari browser --}}
    <div class="hd-card mt-2" id="izinTopSkmkLoopCard" style="background:linear-gradient(135deg,#f3e5f5 0%,#e1f5fe 100%);">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
            <div>
                <h5 style="margin:0; font-size:1rem; font-weight:700; color:#4a148c;">
                    <i class="la la-volume-up"></i> Dengarkan Berulang (Web Speech API)
                </h5>
                <small class="text-muted">Top 10 Mangkir → loop. Memerlukan browser modern (Chrome/Edge/Firefox/Safari).</small>
            </div>
            <div class="d-flex align-items-center" style="gap:.5rem;">
                <span id="izinLoopStatusBadge" class="badge badge-secondary" style="font-size:.75rem; display:none;">
                    <i class="la la-pause"></i> <span id="izinLoopStatusText">Idle</span>
                </span>
                <button type="button" class="btn btn-sm btn-success" id="btnPlayTopSkmkLoop">
                    <i class="la la-play"></i> Dengarkan Berulang
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="btnStopTopSkmkLoop" style="display:none;">
                    <i class="la la-stop"></i> Berhenti
                </button>
            </div>
        </div>
    </div>

    <div class="hd-card mt-2" id="izinTopSakitCard">
        <h5 class="hd-card-toggle" data-target="#izinTopSakitCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Top 10 Karyawan Sakit
        </h5>
        <div class="collapse show" id="izinTopSakitCollapse">
            <div class="table-responsive">
                <table class="table table-bordered table-hover hd-table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Dept</th>
                            <th>Sub Departmen</th>
                            <th>Group</th>
                            <th class="text-right">Jumlah Hari Sakit</th>
                        </tr>
                    </thead>
                    <tbody id="izinTopSakitTbody">
                        <tr><td colspan="7" class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="hd-card mt-2" id="izinTopMangkirCard">
        <h5 class="hd-card-toggle" data-target="#izinTopMangkirCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Top 10 Karyawan Mangkir
        </h5>
        <div class="collapse show" id="izinTopMangkirCollapse">
            <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.82rem;">
                <button type="button" class="btn btn-sm btn-primary" id="btnPlayTopMangkir">
                    <i class="la la-volume-up"></i> Dengarkan
                </button>
                <span class="text-muted">Klik tombol untuk membacakan data melalui speaker (Web Speech API).</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover hd-table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Dept</th>
                            <th>Sub Departmen</th>
                            <th>Group</th>
                            <th class="text-right">Jumlah Hari Mangkir</th>
                        </tr>
                    </thead>
                    <tbody id="izinTopMangkirTbody">
                        <tr><td colspan="7" class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if (isset($canViewSakitRatio) && $canViewSakitRatio)
    <div class="hd-card mt-2" id="izinSakitRatioDeptCard">
        <h5 class="hd-card-toggle" data-target="#izinSakitRatioDeptCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Sakit Ratio per Departemen
        </h5>
        <div class="collapse show" id="izinSakitRatioDeptCollapse">
            <div class="d-flex justify-content-between align-items-center mb-2" id="izinSakitRatioDeptToolbar" style="display:none;">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSakitRatioBack">
                    <i class="la la-arrow-left"></i> Kembali ke Departemen
                </button>
                <span class="text-muted" id="izinSakitRatioDeptTitle" style="font-size:.82rem; font-weight:600;"></span>
            </div>
            <div class="px-2 mb-1" id="izinSakitRatioDeptMeta" style="font-size:.75rem; color:#666;"></div>
            <div id="izinSakitRatioDeptChart" style="min-height: 420px;">
                <p class="text-center text-muted p-4">Klik "Terapkan Filter" untuk memuat data.</p>
            </div>
        </div>
    </div>
    @endif
</div>
