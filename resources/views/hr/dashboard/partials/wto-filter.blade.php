{{-- WT&O Filter: diletakkan di bawah filter Master Employee, hanya muncul saat tab WT&O aktif --}}
<div class="hd-card" id="wtoFilterCard" style="display:none;">
    <h5 class="hd-card-toggle" data-target="#wtoFilterCollapse" style="cursor:pointer; user-select:none;">
        <span class="hd-chevron"></span> Filter Data
    </h5>
    <div class="collapse show" id="wtoFilterCollapse">
        <div class="row mt-2">
            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Departemen</label>
                <div class="hd-multi-select" data-target="wto_departmen" data-placeholder="-- Semua Departemen --">
                    <button type="button" class="hd-ms-btn">
                        <span class="hd-ms-label">-- Semua Departemen --</span>
                        <span class="hd-ms-caret">&#9662;</span>
                    </button>
                    <div class="hd-ms-dropdown">
                        <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari departemen...">
                        <div class="hd-ms-actions">
                            <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                            <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                        </div>
                        <div class="hd-ms-list">
                            @foreach ($departments as $d)
                                <label class="hd-ms-item">
                                    <input type="checkbox" name="wto_departmen[]" value="{{ $d }}">
                                    <span>{{ $d }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Sub Departemen</label>
                <div class="hd-multi-select" data-target="wto_sub_departmen" data-placeholder="-- Semua Sub Departemen --">
                    <button type="button" class="hd-ms-btn">
                        <span class="hd-ms-label">-- Semua Sub Departemen --</span>
                        <span class="hd-ms-caret">&#9662;</span>
                    </button>
                    <div class="hd-ms-dropdown">
                        <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari sub departmen...">
                        <div class="hd-ms-actions">
                            <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                            <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                        </div>
                        <div class="hd-ms-list">
                            @foreach ($subDepartments as $sd)
                                <label class="hd-ms-item">
                                    <input type="checkbox" name="wto_sub_departmen[]" value="{{ $sd }}">
                                    <span>{{ $sd }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Tipe Karyawan</label>
                <div class="hd-multi-select" data-target="wto_tipe_karyawan" data-placeholder="-- Semua Tipe Karyawan --">
                    <button type="button" class="hd-ms-btn">
                        <span class="hd-ms-label">-- Semua Tipe Karyawan --</span>
                        <span class="hd-ms-caret">&#9662;</span>
                    </button>
                    <div class="hd-ms-dropdown">
                        <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari tipe...">
                        <div class="hd-ms-actions">
                            <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                            <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                        </div>
                        <div class="hd-ms-list">
                            @foreach ($types as $t)
                                <label class="hd-ms-item">
                                    <input type="checkbox" name="wto_tipe_karyawan[]" value="{{ $t }}">
                                    <span>{{ $t }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Tanggal Lembur</label>
                <input type="text"
                       class="form-control form-control-sm flatpickr-range"
                       id="wtoTglInRange"
                       placeholder="Pilih rentang tanggal Tgl In..."
                       autocomplete="off">
            </div>
        </div>

        <div class="row align-items-end">
            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Nama Karyawan</label>
                <div class="hd-multi-select" data-target="wto_nama" data-placeholder="-- Pilih Nama --">
                    <button type="button" class="hd-ms-btn">
                        <span class="hd-ms-label">-- Pilih Nama --</span>
                        <span class="hd-ms-caret">&#9662;</span>
                    </button>
                    <div class="hd-ms-dropdown">
                        <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari nama...">
                        <div class="hd-ms-actions">
                            <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                            <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                        </div>
                        <div class="hd-ms-list" id="wtoNamaList">
                            <div class="text-muted text-center p-2" style="font-size:.78rem;">
                                Buka tab WT&amp;O untuk memuat daftar nama
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- PWS / Group --}}
            <div class="col-md-3 mb-2">
                <label class="hd-form-label">Group</label>
                <div class="hd-multi-select" data-target="wto_pws" data-placeholder="-- Semua Group --">
                    <button type="button" class="hd-ms-btn">
                        <span class="hd-ms-label">-- Semua Group --</span>
                        <span class="hd-ms-caret">&#9662;</span>
                    </button>
                    <div class="hd-ms-dropdown">
                        <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari group...">
                        <div class="hd-ms-actions">
                            <button type="button" class="hd-ms-action" data-action="all">Pilih Semua</button>
                            <button type="button" class="hd-ms-action" data-action="none">Kosongkan</button>
                        </div>
                        <div class="hd-ms-list" id="wtoPwsList">
                            @foreach ($pwsGroups as $g)
                                <label class="hd-ms-item">
                                    <input type="checkbox" name="wto_pws[]" value="{{ $g }}">
                                    <span>{{ $g }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-2 d-flex align-items-end" style="gap:.5rem;">
                <button type="button" class="btn btn-primary btn-sm" id="btnWtoApply">Terapkan Filter</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnWtoReset">Reset</button>
                <button type="button" class="btn btn-success btn-sm" id="btnWtoExport">
                    <i class="la la-download"></i> Export CSV
                </button>
            </div>
        </div>
    </div>
</div>
