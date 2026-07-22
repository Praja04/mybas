{{-- WT&O Data Table: di luar fullscreen wrap, hanya muncul saat tab WT&O aktif --}}
<div id="wtoExtras" style="display:none;">
    <div class="hd-card" id="wtoJamLemburDetailCard">
        <h5 class="hd-card-toggle" data-target="#wtoJamLemburCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Detail Jam Lembur per Departemen
        </h5>
        <div class="collapse show" id="wtoJamLemburCollapse">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered hd-table" style="white-space: nowrap;">
                    <thead id="wtoJamLemburDetailThead"></thead>
                    <tbody id="wtoJamLemburDetailTbody">
                        <tr><td class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="hd-card" id="wtoDataCard">
        <h5 class="hd-card-toggle" data-target="#wtoTableCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Data Karyawan Lembur
        </h5>
        <div class="collapse show" id="wtoTableCollapse">
            <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:.82rem;">
                <div>
                    <span class="text-muted">Tampilkan</span>
                    <select id="wtoPerPage" class="form-control form-control-sm d-inline-block" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-muted">per halaman</span>
                </div>
                <div class="text-muted" id="wtoInfo"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered hd-table">
                    <thead class="thead-light">
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Dept</th>
                            <th>Sub Departmen</th>
                            <th>Section</th>
                            <th>Tgl In</th>
                            <th>Jam SPKL</th>
                            <th>Jam HOVT</th>
                            <th>No SPKL</th>
                        </tr>
                    </thead>
                    <tbody id="wtoTbody">
                        <tr><td colspan="9" class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2" id="wtoPagination"></div>
        </div>
    </div>

    @if (isset($canViewTopLembur) && $canViewTopLembur)
    <div class="hd-card mt-2" id="wtoTopLemburCard">
        <h5 class="hd-card-toggle" data-target="#wtoTopLemburCollapse" style="cursor:pointer; user-select:none;">
            <span class="hd-chevron"></span> Top 10 Karyawan Lembur
        </h5>
        <div class="collapse show" id="wtoTopLemburCollapse">
            <div class="table-responsive">
                <table class="table table-bordered table-hover hd-table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Dept</th>
                            <th>Sub Departmen</th>
                            <th class="text-right">Jam SPKL</th>
                            <th class="text-right">Jam HOVT</th>
                            <th class="text-right">Total Jam Lembur</th>
                            <th class="text-right">Frekuensi Lembur</th>
                        </tr>
                    </thead>
                    <tbody id="wtoTopLemburTbody">
                        <tr><td colspan="9" class="text-center text-muted">Klik "Terapkan Filter" untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
