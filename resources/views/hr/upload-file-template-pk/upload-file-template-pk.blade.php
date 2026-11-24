@extends('layouts.base')

@section('content')

<style>
    .pk-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .pk-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .pk-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .pk-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .pk-badge-info { background:#e3f2fd; color:#0d47a1; }
    .pk-badge-foreman { background:#ede7f6; color:#4a148c; }
    .pk-badge-success { background:#c8e6c9; color:#1b5e20; }
    .pk-badge-warn { background:#fff3e0; color:#e65100; }
    .pk-table th { font-size:.8rem; white-space:nowrap; }
    .pk-table td { font-size:.8rem; vertical-align:middle; }
    .pk-pagination .page-link { padding:.2rem .5rem; font-size:.85rem; }
    .modal-pk-xl .modal-dialog { max-width: 95%; margin: 1rem auto; }
    .pk-table-wrapper { max-height: 60vh; overflow:auto; border:1px solid #dee2e6; border-radius:4px; }
    .pk-table-wrapper table { margin-bottom:0; }
    .pk-table-wrapper thead th { position:sticky; top:0; background:#f8f9fa; z-index:1; }
    .pk-section-row td { background:#ede7f6 !important; color:#4a148c; font-weight:700; font-size:.82rem; }
    .pk-kriteria-cell { min-width:170px; }
    .pk-kriteria-cell textarea { font-size:.75rem; }
    #pkTbody td:last-child { white-space:nowrap; }
    textarea.pk-f { white-space:pre-line; }
    .pk-view-text { white-space:pre-line; font-size:.78rem; vertical-align:top; }
    #pkTableBody td { vertical-align:top; }
</style>

<div class="container-fluid">

<h1 class="pk-page-title">Upload File Template PK (Penilaian Karya)</h1>
@include('hr.partials.ipp-pk-subnav', ['snPlainBg' => true])

<div class="pk-card">
    <h5>Template Penilaian Karya — PMS</h5>
    <p class="text-muted" style="font-size:.85rem;">
        Template berisi rubrik penilaian: kolom <strong>NO | AREA | COMPETENCY BEHAVIOR | KEY BEHAVIOR | SCORING KRITERIA 1–5</strong>.
        Baris section (merged) menentukan kategori: <strong>WINGS CORE VALUE</strong>, <strong>LEADERSHIP COMPETENCY</strong> (level 2–3 / 4–5 / 6–7), <strong>BEHAVIOR</strong> (level 2–3, sebelumnya bernama <strong>SIKAP KERJA</strong>), dan <strong>COMPLIANCE</strong>.
        Peserta penilaian tidak didaftarkan manual — sistem otomatis membaca <strong>hr_master_employee</strong> aktif Level 2–7,
        dan Level menentukan versi rubrik yang dipakai (2–3, 4–5, atau 6–7). Khusus peserta Mapping SPV (mis. Supervisor Level 3) memakai rubrik Level 4–5.
        Master disimpan <strong>per periode (tahun)</strong>: template tahun 2026, 2027, dan seterusnya tersimpan terpisah. Mengunggah periode baru tidak akan menghapus template periode lainnya. Jika periode yang sama diunggah ulang, template untuk periode tersebut akan diperbarui.
    </p>
    <div class="mb-3">
        <span class="pk-badge pk-badge-warn">Peserta aktif Level 2–3: <span id="pkPrt23">{{ $participantStats['lvl_2_3'] }}</span></span>
        <span class="pk-badge pk-badge-info">Level 4–5: <span id="pkPrt45">{{ $participantStats['lvl_4_5'] }}</span></span>
        <span class="pk-badge pk-badge-success">Level 6–7: <span id="pkPrt67">{{ $participantStats['lvl_6_7'] }}</span></span>
        <span class="pk-badge pk-badge-warn">Total: <span id="pkPrtTotal">{{ $participantStats['total'] }}</span></span>
    </div>

    <form id="formUploadPk" enctype="multipart/form-data">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="font-weight:600;">File Template PK:</label>
                <input type="file" name="file" id="fileInputPk" class="form-control-file" accept=".xlsx,.xls" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-weight:600;">Periode (tahun):</label>
                <input type="text" name="periode" id="periodeInput" class="form-control form-control-sm" placeholder="2026" maxlength="10">
            </div>
            <div class="col-md-5 d-flex align-items-end" style="gap:.5rem;">
                <button type="submit" class="btn btn-success" id="btnUploadPk">Upload Template</button>
                <a href="{{ url('/hr/upload-file-template-pk/download-template') }}" target="_blank" rel="noopener" class="btn btn-outline-primary">Download Template</a>
            </div>
        </div>
    </form>
</div>

<div class="pk-card">
    <h5>Daftar Master Penilaian Karya</h5>
    <form id="formSearchPk" class="d-flex mb-3" style="gap:.5rem;">
        <input type="text" id="searchPkInput" class="form-control" placeholder="Cari Periode / File / Upload Oleh / Aspek...">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered pk-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Periode</th>
                    <th>Nama File</th>
                    <th>Jumlah Aspek</th>
                    <th>Upload Oleh</th>
                    <th>Tanggal Upload</th>
                    <th style="width:260px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="pkTbody">
                <tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;" id="pkPaginationWrap">
        <span class="text-muted" style="font-size:.85rem;" id="pkPageLabel"></span>
        <div id="pkPagination" class="pk-pagination"></div>
    </div>
</div>

<div class="pk-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
        <h5 style="margin:0;">Daftar Peserta Penilaian Karya Dan IPP</h5>
        <div class="d-flex" style="gap:.5rem; flex-wrap:wrap;">
            <button type="button" class="btn btn-outline-dark btn-sm" id="btnOpenPkMapping">Kelola Mapping Manager (Sub Dept)</button>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnOpenAmMapping">Kelola Mapping Associate Manager (Peserta)</button>
            <button type="button" class="btn btn-outline-success btn-sm" id="btnOpenSpvMapping">Kelola Mapping SPV</button>
            <button type="button" class="btn btn-outline-info btn-sm" id="btnOpenForemanMapping">Kelola Mapping Foreman</button>
        </div>
    </div>
    <div class="text-muted" style="font-size:.8rem; margin-bottom:.75rem; line-height:1.55;">
        <div><strong>Peserta:</strong> otomatis dari karyawan aktif Level 2–7 pada data <code>hr_master_employee</code> yang di-maintain oleh HR Personalia, ditambah peserta yang terdaftar di Mapping SPV atau Foreman.</div>
        <div><strong>Manager:</strong> diambil dari Mapping SPV atau Mapping Foreman. Kalau tidak ada, dari Mapping Manager Sub Dept. Kalau masih tidak ada, dari kandidat Level 8–10.</div>
        <div><strong>Associate Manager:</strong> diambil dari Mapping SPV atau Mapping Foreman. Kalau tidak ada, dari Mapping AM peserta. Kalau masih tidak ada, dari kandidat Level 6–7. Peserta Level 6–7 tidak memiliki AM.</div>
        <div><strong>Rubrik:</strong> Level 2–3 memakai rubrik 2–3 (Sikap Kerja/Behavior); Level 4–5 memakai rubrik 4–5; Level 6–7 memakai rubrik 6–7. Khusus Mapping SPV di bawah Level 4, rubriknya disamakan dengan Level 4–5. Mapping Foreman tetap memakai rubrik 2–3.</div>
    </div>
    <form id="formSearchPrt" class="d-flex mb-3" style="gap:.5rem;">
        <input type="text" id="searchPrtInput" class="form-control" placeholder="Cari NIK / Nama / Jabatan / Departemen / Sub Departemen...">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered pk-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Sub Departemen</th>
                    <th>Section</th>
                    <th style="width:60px;">Level</th>
                    <th style="width:90px;">Group</th>
                    <th>Manager</th>
                    <th>Associate Manager</th>
                </tr>
            </thead>
            <tbody id="prtTbody">
                <tr><td colspan="11" class="text-center text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">
        <span class="text-muted" style="font-size:.85rem;" id="prtPageLabel"></span>
        <div id="prtPagination" class="pk-pagination"></div>
    </div>
</div>

<div class="pk-card mt-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
        <h5 style="margin:0;">History Daftar Peserta Penilaian Karya Dan IPP</h5>
        <div class="d-flex align-items-center" style="gap:.5rem;">
            <button type="button" class="btn btn-success btn-sm" id="btnOpenPrtHistForm">+ Tambah Peserta</button>
            <label style="font-weight:600; margin:0; font-size:.85rem;">Periode:</label>
            <select id="selPrtHistPeriode" class="form-control form-control-sm" style="width:auto; min-width:110px;">
                <option value="">Memuat...</option>
            </select>
        </div>
    </div>
    <div class="text-muted" style="font-size:.8rem; margin-bottom:.75rem; line-height:1.55;">
        <strong>History adalah “foto” data per periode.</strong>
        Saat template PK di-upload, data peserta dan atasan disalin ke snapshot periode.
        Setelah itu, perubahan data karyawan pada hr_master_employee yang dimiliki HR Personalia (perubahan data) tidak mengubah history periode yang sudah ada.
        Kalau perlu menyesuaikan data history, gunakan tombol <strong>Tambah Peserta</strong> atau <strong>Edit Peserta</strong> secara manual.
        Kalau ada data yang masih kosong, datanya bisa dilengkapi dengan di edit.
        Upload ulang periode yang sama tidak menghapus snapshot yang sudah ada.
    </div>
    <form id="formSearchPrtHist" class="d-flex mb-3" style="gap:.5rem;">
        <input type="text" id="searchPrtHistInput" class="form-control" placeholder="Cari NIK / Nama / Jabatan / Departemen / Sub Departemen...">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered pk-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:70px;">Periode</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Sub Departemen</th>
                    <th>Section</th>
                    <th style="width:60px;">Level</th>
                    <th style="width:90px;">Group</th>
                    <th>Manager</th>
                    <th>Associate Manager</th>
                    <th style="width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="prtHistTbody">
                <tr><td colspan="13" class="text-center text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">
        <span class="text-muted" style="font-size:.85rem;" id="prtHistPageLabel"></span>
        <div id="prtHistPagination" class="pk-pagination"></div>
    </div>
</div>

</div>

{{-- PREVIEW / EDIT MODAL --}}
<div class="modal fade" id="pkModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-pk-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pkModalTitle" style="color:#4a148c; font-weight:700;">Preview Data Penilaian Karya</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
                    <div class="d-flex align-items-center" style="gap:.5rem;">
                        <label style="font-weight:600; margin:0;">Periode:</label>
                        <input type="text" id="pkPeriode" class="form-control form-control-sm" style="width:120px;" placeholder="2026" maxlength="10">
                        <span class="text-muted" style="font-size:.85rem;" id="pkModalMode"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPkRow">+ Tambah Baris</button>
                </div>
                <div class="pk-table-wrapper">
                    <table class="table table-bordered table-sm pk-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px;">NO</th>
                                <th style="min-width:120px;">AREA</th>
                                <th style="min-width:220px;">COMPETENCY BEHAVIOR</th>
                                <th style="min-width:220px;">KEY BEHAVIOR</th>
                                <th class="pk-kriteria-cell">SKOR 1</th>
                                <th class="pk-kriteria-cell">SKOR 2</th>
                                <th class="pk-kriteria-cell">SKOR 3</th>
                                <th class="pk-kriteria-cell">SKOR 4</th>
                                <th class="pk-kriteria-cell">SKOR 5</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="pkTableBody"></tbody>
                    </table>
                </div>
                <p class="text-info" style="font-size:.8rem; margin:.5rem 0 0;" id="pkReplaceNote">
                    <i class="fas fa-info-circle"></i> Master akan disimpan untuk periode tahun ini. Jika periode yang sama sudah ada, template periode tersebut akan diperbarui.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnSavePk">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- MAPPING MANAGER MODAL (sumber data sama dengan halaman Upload Template IPP) --}}
<div class="modal fade" id="pkMappingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Mapping Manager (Sub Dept)</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted" style="font-size:.82rem;">
                    Mapping ini adalah data yang sama dengan halaman <strong>Upload File Template IPP</strong> —
                    dipakai untuk menentukan Manager peserta Penilaian Karya.
                </p>
                <div class="table-responsive mb-3" style="max-height:40vh; overflow:auto;">
                    <table class="table table-bordered table-sm pk-table">
                        <thead>
                            <tr>
                                <th>Sub Departmen</th>
                                <th>NIK Manager</th>
                                <th>Nama Manager</th>
                                <th style="width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pkMapTbody">
                            <tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3" style="background:#f5f5f5; border-radius:6px;">
                    <h6 style="font-weight:700; color:#4a148c;">Tambah / Update Mapping</h6>
                    <div class="row">
                        <div class="col-md-5">
                            <label style="font-weight:600;">Sub Departmen:</label>
                            <select id="pkMapSubDept" class="form-control form-control-sm">
                                <option value="">— Pilih Sub Departmen —</option>
                                @foreach ($subDeptOptions as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label style="font-weight:600;">NIK Manager:</label>
                            <input type="text" id="pkMapNik" class="form-control form-control-sm" placeholder="NIK (harus terdaftar di users)">
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="pkMapNikName"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm btn-block" id="btnSavePkMapping">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MAPPING ASSOCIATE MANAGER (per peserta) MODAL --}}
<div class="modal fade" id="pkAmMappingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Mapping Associate Manager (per Peserta)</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted" style="font-size:.82rem;">
                    Dipakai bila Associate Manager <strong>tidak dapat diambil dari Sub Departmen</strong> peserta
                    (misalnya peserta sub dept <em>Accounting</em> yang AM-nya berada di sub dept <em>Finance</em>).
                    Mapping per NIK peserta ini <strong>mengungguli</strong> pencarian otomatis. Peserta tanpa mapping tetap mengikuti aturan sub departmen.
                </p>
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <input type="text" id="pkAmMapSearchPeserta" class="form-control form-control-sm" placeholder="Cari NIK / Nama Peserta...">
                    </div>
                    <div class="col-md-6 mb-1">
                        <input type="text" id="pkAmMapSearchAm" class="form-control form-control-sm" placeholder="Cari NIK / Nama Associate Manager...">
                    </div>
                </div>
                <div class="table-responsive mb-3" style="max-height:40vh; overflow:auto;">
                    <table class="table table-bordered table-sm pk-table">
                        <thead>
                            <tr>
                                <th>NIK Peserta</th>
                                <th>Nama Peserta</th>
                                <th>NIK Associate Manager</th>
                                <th>Nama Associate Manager</th>
                                <th style="width:130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pkAmMapTbody">
                            <tr><td colspan="5" class="text-center text-muted">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3" style="background:#f5f5f5; border-radius:6px;">
                    <h6 style="font-weight:700; color:#4a148c;">Tambah / Update Mapping</h6>
                    <div class="row">
                        <div class="col-md-5">
                            <label style="font-weight:600;">Peserta <span class="text-muted" style="font-weight:400;">(Level 4–5, punya akun)</span>:</label>
                            <select id="pkAmMapNikSel" placeholder="Cari NIK / Nama / Jabatan..."></select>
                        </div>
                        <div class="col-md-5">
                            <label style="font-weight:600;">Associate Manager <span class="text-muted" style="font-weight:400;">(Level 5–7, punya akun)</span>:</label>
                            <select id="pkAmMapNikAmSel" placeholder="Cari NIK / Nama / Jabatan..."></select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm btn-block" id="btnSavePkAmMapping">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('hr.partials.pk-spv-mapping-modal')
@include('hr.partials.pk-foreman-mapping-modal')

<div class="modal fade" id="prtHistModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;" id="prtHistFormTitle">Tambah Peserta History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="prtHistEditId" value="">
                <div class="row">
                    <div class="col-md-4">
                        <label style="font-weight:600;">Periode:</label>
                        <select id="prtHistPeriode" class="form-control form-control-sm"></select>
                    </div>
                    <div class="col-md-8">
                        <label style="font-weight:600;">NIK <span class="text-muted" style="font-weight:400;">(cari dari master atau isi manual)</span>:</label>
                        <div class="spv-nik-dropdown" data-key="hist_ybs" data-allow-custom="1" data-url="{{ url('/hr/upload-file-template-pk/participants-history/search-nik') }}">
                            <button type="button" class="hd-ms-btn">
                                <span class="hd-ms-label" data-placeholder="Masukkan NIK">Masukkan NIK</span>
                                <span class="hd-ms-caret">▾</span>
                            </button>
                            <div class="hd-ms-dropdown">
                                <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari NIK / Nama / Jabatan...">
                                <div class="hd-ms-list">
                                    <div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Ketik minimal 2 karakter untuk mencari NIK / Nama...</div>
                                </div>
                            </div>
                            <input type="hidden" class="spv-nik-value" id="prtHistNik" value="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Nama:</label>
                        <input type="text" id="prtHistNama" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Jabatan:</label>
                        <input type="text" id="prtHistJabatan" class="form-control form-control-sm" maxlength="200">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Departemen:</label>
                        <input type="text" id="prtHistDepartemen" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Sub Departemen:</label>
                        <input type="text" id="prtHistSubDepartemen" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Section:</label>
                        <input type="text" id="prtHistSection" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-2">
                        <label style="font-weight:600;">Level:</label>
                        <input type="text" id="prtHistLevel" class="form-control form-control-sm" maxlength="10">
                    </div>
                    <div class="col-md-2">
                        <label style="font-weight:600;">Group:</label>
                        <select id="prtHistGroup" class="form-control form-control-sm">
                            <option value="">-</option>
                            <option value="2-3">Level 2–3</option>
                            <option value="4-5">Level 4–5</option>
                            <option value="6-7">Level 6–7</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Jumlah Bawahan:</label>
                        <input type="number" id="prtHistJumlahBawahan" class="form-control form-control-sm" min="0">
                    </div>
                    <div class="col-md-6">
                        <label style="font-weight:600;">Manager NIK:</label>
                        <div class="spv-nik-dropdown" data-key="hist_mgr" data-role="manager">
                            <button type="button" class="hd-ms-btn">
                                <span class="hd-ms-label" data-placeholder="Masukkan NIK">Masukkan NIK</span>
                                <span class="hd-ms-caret">▾</span>
                            </button>
                            <div class="hd-ms-dropdown">
                                <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari NIK / Nama...">
                                <div class="hd-ms-list">
                                    <div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Ketik minimal 2 karakter untuk mencari NIK / Nama...</div>
                                </div>
                            </div>
                            <input type="hidden" class="spv-nik-value" id="prtHistManagerNik" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label style="font-weight:600;">Manager Nama:</label>
                        <input type="text" id="prtHistManagerNama" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label style="font-weight:600;">Associate Manager NIK:</label>
                        <div class="spv-nik-dropdown" data-key="hist_am" data-role="associate">
                            <button type="button" class="hd-ms-btn">
                                <span class="hd-ms-label" data-placeholder="Tidak Ada">Tidak Ada</span>
                                <span class="hd-ms-caret">▾</span>
                            </button>
                            <div class="hd-ms-dropdown">
                                <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari NIK / Nama...">
                                <div class="hd-ms-list">
                                    <div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Ketik minimal 2 karakter untuk mencari NIK / Nama...</div>
                                </div>
                            </div>
                            <input type="hidden" class="spv-nik-value" id="prtHistAmNik" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label style="font-weight:600;">Associate Manager Nama:</label>
                        <input type="text" id="prtHistAmNama" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Pendidikan:</label>
                        <input type="text" id="prtHistPendidikan" class="form-control form-control-sm" maxlength="150">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Tanggal Join:</label>
                        <input type="date" id="prtHistTanggalJoin" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label style="font-weight:600;">Tanggal Lahir:</label>
                        <input type="date" id="prtHistTanggalLahir" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label style="font-weight:600;">Usia:</label>
                        <input type="number" id="prtHistUsia" class="form-control form-control-sm" min="0" max="150">
                    </div>
                </div>
                <p class="text-muted" style="font-size:.8rem; margin:.75rem 0 0;">
                    NIK tidak ada di daftar? Ketik NIK lalu tekan <strong>Enter</strong> atau pilih <strong>Gunakan NIK "…" (isi manual)</strong>.
                    NIK di luar master diperbolehkan, tetapi Nama wajib diisi. Jika Group dikosongkan, sistem mengisi otomatis dari Level (≥6 → Level 6–7, 4–5 → Level 4–5, 2–3 → Level 2–3).
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnSavePrtHist">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@include('hr.partials.pk-spv-mapping-script')
@include('hr.partials.pk-foreman-mapping-script')
<script>
$(function () {

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return $('<div>').text(String(s)).html();
    }

    let pkMode = 'create';
    let pkEditId = null;
    let pkParsed = { periode: null, file_name: null, details: [] };
    let pkPage = 1;
    let pkPerPage = 10;

    function levelBadge(lg) {
        if (lg === '2-3') return '<span class="pk-badge pk-badge-warn">Level 2–3</span>';
        if (lg === '4-5') return '<span class="pk-badge pk-badge-info">Level 4–5</span>';
        if (lg === '6-7') return '<span class="pk-badge pk-badge-success">Level 6–7</span>';
        return '<span class="pk-badge pk-badge-warn">Semua Level</span>';
    }

    function groupFromLevel(level) {
        let lv = parseInt(level, 10);
        if (!lv || isNaN(lv)) return '';
        if (lv >= 6) return '6-7';
        if (lv >= 4) return '4-5';
        if (lv >= 2) return '2-3';
        return '';
    }

    function renderPkTable() {
        let rows = pkParsed.details || [];
        let edit = pkMode !== 'view';
        if (!rows.length) {
            $('#pkTableBody').html('<tr><td colspan="11" class="text-center text-muted">Belum ada data.</td></tr>');
            $('#btnAddPkRow').toggle(edit);
            return;
        }
        $('#btnAddPkRow').hide();
        let html = '';
        let lastSection = null;
        rows.forEach(function (d, idx) {
            if (d.section !== lastSection) {
                lastSection = d.section;
                html += '<tr class="pk-section-row"><td colspan="11">' + escapeHtml(d.section) + ' ' + levelBadge(d.level_group) +
                    (edit ? ' <button type="button" class="btn btn-sm btn-outline-primary float-right btn-add-pk-section" data-gidx="' + idx + '" title="Tambah baris di section ini">+ Baris</button>' : '') +
                    '</td></tr>';
            }
            if (!edit) {
                html += '<tr class="pk-item-row">' +
                    '<td class="text-center">' + escapeHtml(d.no_urut) + '</td>' +
                    '<td class="pk-view-text">' + escapeHtml(d.area) + '</td>' +
                    '<td class="pk-view-text"><strong>' + escapeHtml(d.competency_behavior) + '</strong></td>' +
                    '<td class="pk-view-text">' + escapeHtml(d.key_behavior) + '</td>' +
                    [1, 2, 3, 4, 5].map(function (n) {
                        return '<td class="pk-kriteria-cell pk-view-text">' + escapeHtml(d['kriteria_' + n]) + '</td>';
                    }).join('') +
                    '<td></td></tr>';
                return;
            }
            html += '<tr class="pk-item-row" data-idx="' + idx + '">' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm pk-f text-center" data-field="no_urut" style="width:55px;" value="' + escapeHtml(d.no_urut) + '"></td>' +
                '<td><textarea class="form-control form-control-sm pk-f" data-field="area" rows="2">' + escapeHtml(d.area) + '</textarea></td>' +
                '<td><textarea class="form-control form-control-sm pk-f" data-field="competency_behavior" rows="4">' + escapeHtml(d.competency_behavior) + '</textarea></td>' +
                '<td><textarea class="form-control form-control-sm pk-f" data-field="key_behavior" rows="4">' + escapeHtml(d.key_behavior) + '</textarea></td>' +
                [1, 2, 3, 4, 5].map(function (n) {
                    return '<td class="pk-kriteria-cell"><textarea class="form-control form-control-sm pk-f" data-field="kriteria_' + n + '" rows="5">' + escapeHtml(d['kriteria_' + n]) + '</textarea></td>';
                }).join('') +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-pk-row" title="Hapus baris"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';
        });
        $('#pkTableBody').html(html);
    }

    function syncParsedFromInputs() {
        $('#pkTableBody tr.pk-item-row').each(function () {
            let idx = parseInt($(this).data('idx'));
            if (isNaN(idx) || !pkParsed.details[idx]) return;
            $(this).find('.pk-f').each(function () {
                pkParsed.details[idx][$(this).data('field')] = $(this).val();
            });
        });
    }

    function collectDetails() {
        syncParsedFromInputs();
        return (pkParsed.details || []).filter(function (d) {
            return (d.no_urut && String(d.no_urut).trim() !== '') || (d.competency_behavior && String(d.competency_behavior).trim() !== '');
        });
    }

    function openPkModal(title, modeNote) {
        pkMode = title.mode;
        $('#pkModalTitle').text(title.text);
        $('#pkModalMode').text(modeNote || '');
        $('#pkPeriode').val(pkParsed.periode || $('#periodeInput').val() || '');
        $('#btnSavePk').toggle(title.mode !== 'view');
        $('#pkReplaceNote').toggle(title.mode !== 'view');
        renderPkTable();
        $('#pkPeriode').prop('readonly', title.mode === 'view');
        $('#pkModal').modal('show');
    }

    // === UPLOAD & PARSE ===
    $('#formUploadPk').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#btnUploadPk').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-file-template-pk/parse') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                pkParsed = res.data;
                pkEditId = null;
                if (res.participants) {
                    $('#pkPrt23').text(res.participants.lvl_2_3);
                    $('#pkPrt45').text(res.participants.lvl_4_5);
                    $('#pkPrt67').text(res.participants.lvl_6_7);
                    $('#pkPrtTotal').text(res.participants.total);
                }
                openPkModal({ mode: 'create', text: 'Preview Data Penilaian Karya' }, 'Template akan disimpan untuk periode yang ditentukan.');
            },
            error: function (xhr) {
                Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal upload file.', 'error');
            },
            complete: function () {
                $('#btnUploadPk').prop('disabled', false).text('Upload & Parse');
            }
        });
    });

    // === SIMPAN (create / update) ===
    $('#btnSavePk').on('click', function () {
        let periode = String($('#pkPeriode').val() || '').trim();
        if (!periode) {
            Swal.fire('Perhatian', 'Periode (tahun) wajib diisi.', 'warning');
            $('#pkPeriode').focus();
            return;
        }
        let details = collectDetails();
        if (!details.length) {
            Swal.fire('Perhatian', 'Tidak ada data untuk disimpan.', 'warning');
            return;
        }
        let url = pkMode === 'edit'
            ? "{{ url('/hr/upload-file-template-pk') }}/update/" + pkEditId
            : "{{ url('/hr/upload-file-template-pk/store') }}";

        $('#btnSavePk').prop('disabled', true).text('Menyimpan...');
        $.post(url, {
            _token: csrf(),
            periode: periode,
            file_name: pkParsed.file_name,
            details: details
        }, function (res) {
            $('#pkModal').modal('hide');
            Swal.fire('Berhasil', res.message, 'success');
            loadPkList();
        }).fail(function (xhr) {
            let msg = 'Gagal menyimpan.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (xhr.responseJSON.errors) {
                    let first = Object.values(xhr.responseJSON.errors)[0];
                    if (first && first[0]) msg = first[0];
                }
            }
            Swal.fire('Error', msg, 'error');
        }).always(function () {
            $('#btnSavePk').prop('disabled', false).text('Simpan');
        });
    });

    $('#btnAddPkRow').on('click', function () {
        pkParsed.details = pkParsed.details || [];
        pkParsed.details.push({
            section: '', category: '', level_group: 'all',
            no_urut: '', area: '', competency_behavior: '', key_behavior: '',
            kriteria_1: '', kriteria_2: '', kriteria_3: '', kriteria_4: '', kriteria_5: ''
        });
        renderPkTable();
    });

    $(document).on('click', '.btn-add-pk-section', function () {
        syncParsedFromInputs();
        let gidx = parseInt($(this).data('gidx'));
        if (isNaN(gidx) || !pkParsed.details[gidx]) return;
        let sec = pkParsed.details[gidx].section;
        let lastIdx = gidx;
        let maxNo = 0;
        for (let i = gidx; i < pkParsed.details.length; i++) {
            if (pkParsed.details[i].section !== sec) break;
            lastIdx = i;
            let n = parseInt(pkParsed.details[i].no_urut);
            if (!isNaN(n) && n > maxNo) maxNo = n;
        }
        let ref = pkParsed.details[lastIdx];
        pkParsed.details.splice(lastIdx + 1, 0, {
            section: ref.section,
            category: ref.category,
            level_group: ref.level_group,
            no_urut: maxNo ? String(maxNo + 1) : '',
            area: '',
            competency_behavior: '',
            key_behavior: '',
            kriteria_1: '', kriteria_2: '', kriteria_3: '', kriteria_4: '', kriteria_5: ''
        });
        renderPkTable();
    });

    $(document).on('click', '.btn-remove-pk-row', function () {
        syncParsedFromInputs();
        let idx = parseInt($(this).closest('tr').data('idx'));
        pkParsed.details.splice(idx, 1);
        renderPkTable();
    });

    // === LIST ===
    function loadPkList() {
        $.get("{{ url('/hr/upload-file-template-pk/list') }}", {
            page: pkPage,
            per_page: pkPerPage,
            search: $('#searchPkInput').val()
        }, function (res) {
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="7" class="text-center text-muted">Belum ada master Penilaian Karya.</td></tr>';
            }
            items.forEach(function (x, i) {
                html += '<tr>' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td>' + escapeHtml(x.periode || '-') + '</td>' +
                    '<td>' + escapeHtml(x.file_name || '-') + '</td>' +
                    '<td class="text-center">' + x.details_count + '</td>' +
                    '<td>' + escapeHtml(x.send_by_username || '-') + '</td>' +
                    '<td>' + escapeHtml(x.created_at) + '</td>' +
                    '<td class="text-center">' +
                    '<button class="btn btn-sm btn-outline-info btn-view-pk" data-id="' + x.id + '">Lihat</button> ' +
                    '<button class="btn btn-sm btn-outline-primary btn-edit-pk" data-id="' + x.id + '">Edit</button> ' +
                    '<button class="btn btn-sm btn-outline-success btn-notif-pk" data-id="' + x.id + '" data-periode="' + escapeHtml(x.periode || '') + '"><i class="fas fa-paper-plane"></i> Kirim Notifikasi</button> ' +
                    '<button class="btn btn-sm btn-outline-danger btn-delete-pk" data-id="' + x.id + '"><i class="fas fa-trash"></i></button>' +
                    '</td></tr>';
            });
            $('#pkTbody').html(html);
            if (res.participants) {
                $('#pkPrt23').text(res.participants.lvl_2_3);
                $('#pkPrt45').text(res.participants.lvl_4_5);
                $('#pkPrt67').text(res.participants.lvl_6_7);
                $('#pkPrtTotal').text(res.participants.total);
            }
            renderPkPagination(res.meta || {});
        });
    }

    function renderPkPagination(meta) {
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        let total = parseInt(meta.total || 0);
        $('#pkPageLabel').text(total ? 'Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' master)' : '');
        let prev = page > 1
            ? '<button class="btn btn-sm btn-outline-primary mr-1 pk-page-btn" data-page="' + (page - 1) + '">Prev</button>'
            : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage
            ? '<button class="btn btn-sm btn-outline-primary pk-page-btn" data-page="' + (page + 1) + '">Next</button>'
            : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        $('#pkPagination').html(prev + next);
    }

    $(document).on('click', '.pk-page-btn', function () {
        pkPage = parseInt($(this).data('page'));
        loadPkList();
    });

    $('#formSearchPk').on('submit', function (e) {
        e.preventDefault();
        pkPage = 1;
        loadPkList();
    });

    // === LIHAT / EDIT / HAPUS ===
    function loadDetail(id, mode) {
        $.get("{{ url('/hr/upload-file-template-pk') }}/" + id, function (res) {
            pkParsed = { periode: res.data.periode, file_name: res.data.file_name, details: res.data.details };
            pkEditId = id;
            if (mode === 'edit') {
                openPkModal({ mode: 'edit', text: 'Edit Master Penilaian Karya' }, 'Menyimpan akan mengganti seluruh detail master ini.');
            } else {
                openPkModal({ mode: 'view', text: 'Master Penilaian Karya' }, '');
            }
        }).fail(function () {
            Swal.fire('Error', 'Gagal memuat data.', 'error');
        });
    }

    $(document).on('click', '.btn-view-pk', function () { loadDetail($(this).data('id'), 'view'); });
    $(document).on('click', '.btn-edit-pk', function () { loadDetail($(this).data('id'), 'edit'); });

    $(document).on('click', '.btn-delete-pk', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus master ini?',
            text: 'Seluruh rubrik Penilaian Karya untuk periode ini akan dihapus.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.value) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-pk/delete') }}/" + id,
                type: 'POST',
                data: { _token: csrf(), _method: 'DELETE' },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    loadPkList();
                },
                error: function (xhr) {
                    Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menghapus.', 'error');
                }
            });
        });
    });

    $(document).on('click', '.btn-notif-pk', function () {
        let id = $(this).data('id');
        let periode = $(this).data('periode');
        Swal.fire({
            title: 'Kirim notifikasi periode ' + periode + '?',
            text: 'Email pemberitahuan Penilaian Karya akan dikirim ke peserta level 4-7 periode ini (level 2-3 tidak dikirim).',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.value) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-pk') }}/" + id + "/send-notification",
                type: 'POST',
                data: { _token: csrf() },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                },
                error: function (xhr) {
                    Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengirim notifikasi.', 'error');
                }
            });
        });
    });

    // === DAFTAR PESERTA ===
    let prtPage = 1;
    let prtPerPage = 10;

    function loadPrtList() {
        $.get("{{ url('/hr/upload-file-template-pk/participants') }}", {
            page: prtPage,
            per_page: prtPerPage,
            search: $('#searchPrtInput').val()
        }, function (res) {
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="11" class="text-center text-muted">Tidak ada peserta.</td></tr>';
            }
            items.forEach(function (x, i) {
                let mgr = '- <span class="pk-badge pk-badge-warn ml-1">belum ada</span>';
                if (x.manager) {
                    let src = x.manager.source === 'foreman'
                        ? '<span class="pk-badge pk-badge-foreman ml-1" title="Dari Mapping Foreman (Peserta PK)">foreman</span>'
                        : (x.manager.source === 'otorisasi'
                            ? '<span class="pk-badge pk-badge-info ml-1" title="Dari Mapping SPV (Peserta PK)">spv</span>'
                            : (x.manager.source === 'mapping'
                                ? '<span class="pk-badge pk-badge-info ml-1" title="Dari Mapping Manager (Sub Dept)">map</span>'
                                : '<span class="pk-badge pk-badge-success ml-1" title="Kandidat otomatis Level 8-13 terendah">auto</span>'));
                    mgr = escapeHtml(x.manager.nama) + ' <span class="text-muted">(' + escapeHtml(x.manager.nik) + ')</span> ' + src;
                }
                let am, amTitle;
                if (x.associate_manager) {
                    let amSrc = x.associate_manager.source === 'foreman'
                        ? '<span class="pk-badge pk-badge-foreman ml-1" title="Dari Mapping Foreman (Peserta PK)">foreman</span>'
                        : (x.associate_manager.source === 'otorisasi'
                            ? '<span class="pk-badge pk-badge-info ml-1" title="Dari Mapping SPV (Peserta PK)">spv</span>'
                            : (x.associate_manager.source === 'mapping'
                                ? '<span class="pk-badge pk-badge-info ml-1" title="Dari Mapping Associate Manager (Peserta)">map</span>'
                                : '<span class="pk-badge pk-badge-success ml-1" title="Kandidat otomatis Level 6-7 sub dept sama">auto</span>'));
                    am = escapeHtml(x.associate_manager.nama) + ' <span class="text-muted">(' + escapeHtml(x.associate_manager.nik) + ')</span> ' + amSrc;
                    amTitle = 'Leader Assessment oleh AM, Manager dapat mengedit bila perlu';
                } else if (x.group === '6-7') {
                    am = '<span class="text-muted">-</span>';
                    amTitle = 'Peserta Level 6–7 — Leader Assessment oleh Manager';
                } else {
                    am = '- <span class="pk-badge pk-badge-warn ml-1">belum ada</span>';
                    amTitle = 'Tanpa AM — Leader Assessment langsung oleh Manager';
                }
                html += '<tr>' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td>' + escapeHtml(x.nik) + '</td>' +
                    '<td>' + escapeHtml(x.nama) + '</td>' +
                    '<td>' + escapeHtml(x.jabatan || '-') + '</td>' +
                    '<td>' + escapeHtml(x.departemen || '-') + '</td>' +
                    '<td>' + escapeHtml(x.sub_departemen || '-') + '</td>' +
                    '<td>' + escapeHtml(x.section || '-') + '</td>' +
                    '<td class="text-center">' + x.level + '</td>' +
                    '<td class="text-center">' + levelBadge(x.group) + '</td>' +
                    '<td>' + mgr + '</td>' +
                    '<td title="' + amTitle + '">' + am + '</td>' +
                    '</tr>';
            });
            $('#prtTbody').html(html);
            renderPrtPagination(res.meta || {});
        }).fail(function () {
            $('#prtTbody').html('<tr><td colspan="11" class="text-center text-muted">Gagal memuat peserta.</td></tr>');
        });
    }

    function renderPrtPagination(meta) {
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        let total = parseInt(meta.total || 0);
        if (total === 0) {
            $('#prtPageLabel').text('');
            $('#prtPagination').html('');
            return;
        }
        let prev = page > 1
            ? '<button class="btn btn-sm btn-outline-primary mr-1 prt-page-btn" data-page="' + (page - 1) + '">Prev</button>'
            : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage
            ? '<button class="btn btn-sm btn-outline-primary prt-page-btn" data-page="' + (page + 1) + '">Next</button>'
            : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) {
            opts += '<option value="' + n + '"' + (n === prtPerPage ? ' selected' : '') + '>' + n + '</option>';
        });
        $('#prtPageLabel').text('Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' peserta)');
        $('#prtPagination').html(
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.82rem;">Tampilkan</span>' +
            '<select id="prtPerPageSel" class="form-control form-control-sm" style="width:auto; display:inline-block;">' + opts + '</select>' +
            '<span class="text-muted" style="font-size:.82rem;">per halaman</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.prt-page-btn', function () {
        prtPage = parseInt($(this).data('page'));
        loadPrtList();
    });

    $(document).on('change', '#prtPerPageSel', function () {
        prtPerPage = parseInt($(this).val()) || 10;
        prtPage = 1;
        loadPrtList();
    });

    $('#formSearchPrt').on('submit', function (e) {
        e.preventDefault();
        prtPage = 1;
        loadPrtList();
    });

    // === MAPPING MANAGER (endpoint IPP, satu sumber data) ===
    function loadPkMapping() {
        $('#pkMapTbody').html('<tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>');
        $.get("{{ url('/hr/upload-file-template-ipp/mapping') }}", function (res) {
            let rows = res.data || [];
            if (!rows.length) {
                $('#pkMapTbody').html('<tr><td colspan="4" class="text-center text-muted">Belum ada mapping.</td></tr>');
                return;
            }
            let html = '';
            rows.forEach(function (m) {
                html += '<tr>' +
                    '<td>' + escapeHtml(m.sub_departmen) + '</td>' +
                    '<td>' + escapeHtml(m.nik_manager || '-') + '</td>' +
                    '<td>' + escapeHtml(m.nama_manager || '-') + '</td>' +
                    '<td class="text-center"><button class="btn btn-sm btn-danger btn-pk-mapping-delete" data-id="' + m.id + '">Hapus</button></td>' +
                    '</tr>';
            });
            $('#pkMapTbody').html(html);
        }).fail(function () {
            $('#pkMapTbody').html('<tr><td colspan="4" class="text-center text-muted">Gagal memuat.</td></tr>');
        });
    }

    $('#btnOpenPkMapping').on('click', function () {
        $('#pkMapSubDept').val('');
        $('#pkMapNik').val('');
        $('#pkMapNikName').text('');
        loadPkMapping();
        $('#pkMappingModal').modal('show');
    });

    $('#pkMapNik').on('change blur', function () {
        let nik = String($(this).val() || '').trim();
        $('#pkMapNikName').text('');
        if (nik === '') return;
        $.get("{{ url('/hr/upload-file-template-ipp/otorisasi/check-nik') }}/" + encodeURIComponent(nik), function (res) {
            if (res.valid) {
                $('#pkMapNikName').text(res.name);
            } else {
                $('#pkMapNikName').css('color', '#b71c1c').text(res.message || 'NIK tidak ditemukan.');
            }
        }).fail(function () {
            $('#pkMapNikName').css('color', '#b71c1c').text('NIK tidak ditemukan.');
        });
    });

    $('#btnSavePkMapping').on('click', function () {
        let sub = $('#pkMapSubDept').val();
        let nik = String($('#pkMapNik').val() || '').trim();
        if (!sub) {
            Swal.fire('Error', 'Pilih Sub Departmen terlebih dahulu.', 'error');
            return;
        }
        if (!nik) {
            Swal.fire('Error', 'Isi NIK Manager.', 'error');
            return;
        }
        $.ajax({
            url: "{{ url('/hr/upload-file-template-ipp/mapping') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { sub_departmen: sub, nik_manager: nik },
            success: function () {
                Swal.fire('Sukses', 'Mapping berhasil disimpan.', 'success');
                $('#pkMapSubDept').val('');
                $('#pkMapNik').val('');
                $('#pkMapNikName').text('');
                loadPkMapping();
                loadPrtList();
            },
            error: function (xhr) {
                Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan mapping.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-pk-mapping-delete', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus mapping ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.value) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-ipp/mapping') }}/" + id,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function () {
                    Swal.fire('Sukses', 'Mapping dihapus.', 'success');
                    loadPkMapping();
                    loadPrtList();
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menghapus mapping.', 'error');
                }
            });
        });
    });

    // === MAPPING ASSOCIATE MANAGER (per peserta) ===
    let amMapRows = [];

    function renderPkAmMapping() {
        let qPeserta = String($('#pkAmMapSearchPeserta').val() || '').trim().toLowerCase();
        let qAm = String($('#pkAmMapSearchAm').val() || '').trim().toLowerCase();

        if (!amMapRows.length) {
            $('#pkAmMapTbody').html('<tr><td colspan="5" class="text-center text-muted">Belum ada mapping.</td></tr>');
            return;
        }

        let rows = amMapRows.filter(function (m) {
            let pOk = !qPeserta
                || String(m.nama || '').toLowerCase().indexOf(qPeserta) !== -1
                || String(m.nik || '').toLowerCase().indexOf(qPeserta) !== -1;
            let aOk = !qAm
                || String(m.nama_associate_manager || '').toLowerCase().indexOf(qAm) !== -1
                || String(m.nik_associate_manager || '').toLowerCase().indexOf(qAm) !== -1;
            return pOk && aOk;
        });

        if (!rows.length) {
            $('#pkAmMapTbody').html('<tr><td colspan="5" class="text-center text-muted">Tidak ada mapping yang cocok.</td></tr>');
            return;
        }

        let html = '';
        rows.forEach(function (m) {
            html += '<tr>' +
                '<td>' + escapeHtml(m.nik) + '</td>' +
                '<td>' + escapeHtml(m.nama || '-') + '</td>' +
                '<td>' + escapeHtml(m.nik_associate_manager) + '</td>' +
                '<td>' + escapeHtml(m.nama_associate_manager || '-') + '</td>' +
                '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-primary btn-pk-am-mapping-edit mr-1" data-nik="' + escapeHtml(m.nik) + '" data-nama="' + escapeHtml(m.nama || '') + '" data-am="' + escapeHtml(m.nik_associate_manager) + '" data-amnama="' + escapeHtml(m.nama_associate_manager || '') + '">Edit</button>' +
                '<button class="btn btn-sm btn-danger btn-pk-am-mapping-delete" data-id="' + m.id + '">Hapus</button></td>' +
                '</tr>';
        });
        $('#pkAmMapTbody').html(html);
    }

    $(document).on('input', '#pkAmMapSearchPeserta, #pkAmMapSearchAm', renderPkAmMapping);

    function loadPkAmMapping() {
        $('#pkAmMapTbody').html('<tr><td colspan="5" class="text-center text-muted">Memuat...</td></tr>');
        $.get("{{ url('/hr/upload-file-template-pk/am-mapping') }}", function (res) {
            amMapRows = res.data || [];
            renderPkAmMapping();
        }).fail(function () {
            amMapRows = [];
            $('#pkAmMapTbody').html('<tr><td colspan="5" class="text-center text-muted">Gagal memuat.</td></tr>');
        });
    }

    $('#btnOpenAmMapping').on('click', function () {
        if (amMapSelPeserta) amMapSelPeserta.clear();
        if (amMapSelAm) amMapSelAm.clear();
        $('#pkAmMapSearchPeserta, #pkAmMapSearchAm').val('');
        loadPkAmMapping();
        $('#pkAmMappingModal').modal('show');
    });

    let amMapSelPeserta = null;
    let amMapSelAm = null;

    function initAmSelect(sel, role, placeholder) {
        if (typeof $.fn.selectize !== 'function') {
            return null;
        }

        return $(sel).selectize({
            placeholder: placeholder,
            load: function (query, callback) {
                $.get("{{ url('/hr/upload-file-template-pk/am-mapping/employees') }}", { q: query, role: role }, function (data) {
                    callback((data.results || []).map(function (r) { return { value: r.id, text: r.text }; }));
                }).fail(function () { callback([]); });
            },
            score: function () { return function () { return 1; }; },
            loadThrottle: 350,
            create: false,
            maxItems: 1,
            highlight: false,
            diagnose: false
        })[0].selectize;
    }

    amMapSelPeserta = initAmSelect('#pkAmMapNikSel', 'peserta', 'Cari NIK / Nama / Jabatan...');
    amMapSelAm = initAmSelect('#pkAmMapNikAmSel', 'am', 'Cari NIK / Nama / Jabatan...');

    function setAmSelect(sz, nik, nama) {
        if (!sz) {
            return;
        }

        sz.addOption([{ value: nik, text: nik + ' — ' + (nama || '') }]);
        sz.setValue(nik, true);
    }

    $(document).on('click', '.btn-pk-am-mapping-edit', function () {
        setAmSelect(amMapSelPeserta, String($(this).data('nik')), String($(this).data('nama')));
        setAmSelect(amMapSelAm, String($(this).data('am')), String($(this).data('amnama')));
        $('#pkAmMappingModal').modal('show');
        $('html, body').animate({ scrollTop: $('.selectize-control:first', $('#pkAmMappingModal')).offset().top }, 0);
    });

    $('#btnSavePkAmMapping').on('click', function () {
        let nik = amMapSelPeserta ? String(amMapSelPeserta.getValue() || '').trim() : '';
        let nikAm = amMapSelAm ? String(amMapSelAm.getValue() || '').trim() : '';
        if (!nik) {
            Swal.fire('Error', 'Pilih Peserta terlebih dahulu.', 'error');
            return;
        }
        if (!nikAm) {
            Swal.fire('Error', 'Pilih Associate Manager terlebih dahulu.', 'error');
            return;
        }
        $.ajax({
            url: "{{ url('/hr/upload-file-template-pk/am-mapping') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { nik: nik, nik_associate_manager: nikAm },
            success: function () {
                Swal.fire('Sukses', 'Mapping Associate Manager berhasil disimpan.', 'success');
                if (amMapSelPeserta) amMapSelPeserta.clear();
                if (amMapSelAm) amMapSelAm.clear();
                loadPkAmMapping();
                loadPrtList();
            },
            error: function (xhr) {
                Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan mapping.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-pk-am-mapping-delete', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus mapping ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.value) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-pk/am-mapping') }}/" + id,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function () {
                    Swal.fire('Sukses', 'Mapping Associate Manager dihapus.', 'success');
                    loadPkAmMapping();
                    loadPrtList();
                },
                error: function () {
                    Swal.fire('Error', 'Gagal menghapus mapping.', 'error');
                }
            });
        });
    });

    // === HISTORY DAFTAR PESERTA (snapshot per periode) ===
    let prtHistPage = 1;
    let prtHistPerPage = 10;
    let prtHistPeriode = '';

    function prtHistSrcBadge(src) {
        if (src === 'pk') return '<span class="pk-badge pk-badge-success ml-1" title="Snapshot upload template PK">pk</span>';
        if (src === 'ipp') return '<span class="pk-badge pk-badge-info ml-1" title="Snapshot dari IPP">ipp</span>';
        if (src === 'pk_foreman') return '<span class="pk-badge pk-badge-foreman ml-1" title="Snapshot dari Mapping Foreman (Peserta PK)">foreman</span>';
        if (src === 'pk_otorisasi') return '<span class="pk-badge pk-badge-info ml-1" title="Snapshot dari Mapping SPV (Peserta PK)">spv</span>';
        if (src === 'backfill') return '<span class="pk-badge pk-badge-warn ml-1" title="Snapshot backfill">backfill</span>';
        if (src === 'simulasi') return '<span class="pk-badge pk-badge-warn ml-1" title="Snapshot simulasi">simulasi</span>';
        if (src === 'manual') return '<span class="pk-badge pk-badge-success ml-1" title="Ditambahkan manual di History">manual</span>';
        return '';
    }

    function loadPrtHistList() {
        $.get("{{ url('/hr/upload-file-template-pk/participants-history') }}", {
            page: prtHistPage,
            per_page: prtHistPerPage,
            periode: prtHistPeriode,
            search: $('#searchPrtHistInput').val()
        }, function (res) {
            if (res.periods && res.periods.length) {
                let sel = $('#selPrtHistPeriode');
                if (prtHistPeriode === '') {
                    let opts = '';
                    res.periods.forEach(function (p) {
                        opts += '<option value="' + escapeHtml(p) + '"' + (p === res.periode ? ' selected' : '') + '>' + escapeHtml(p) + '</option>';
                    });
                    sel.html(opts);
                    prtHistPeriode = res.periode || '';
                }
            }
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="13" class="text-center text-muted">Tidak ada data history.</td></tr>';
            }
            items.forEach(function (x, i) {
                let mgr = '- <span class="pk-badge pk-badge-warn ml-1">belum ada</span>';
                if (x.manager) {
                    mgr = escapeHtml(x.manager.nama || '-') + ' <span class="text-muted">(' + escapeHtml(x.manager.nik || '-') + ')</span> ' + prtHistSrcBadge(x.manager.source);
                }
                let am, amTitle;
                if (x.associate_manager) {
                    am = escapeHtml(x.associate_manager.nama || '-') + ' <span class="text-muted">(' + escapeHtml(x.associate_manager.nik || '-') + ')</span> ' + prtHistSrcBadge(x.associate_manager.source);
                    amTitle = 'Associate Manager periode ini';
                } else if (x.group === '6-7') {
                    am = '<span class="text-muted">-</span>';
                    amTitle = 'Peserta Level 6–7 — Leader Assessment oleh Manager';
                } else {
                    am = '- <span class="pk-badge pk-badge-warn ml-1">belum ada</span>';
                    amTitle = 'Tanpa AM — Leader Assessment langsung oleh Manager';
                }
                let rowData = {
                    id: x.id,
                    periode: x.periode,
                    nik: x.nik,
                    nama: x.nama,
                    jabatan: x.jabatan,
                    departemen: x.departemen,
                    sub_departemen: x.sub_departemen,
                    section: x.section,
                    level: x.level,
                    level_group: x.group,
                    manager_nik: x.manager ? (x.manager.nik || '') : '',
                    manager_nama: x.manager ? (x.manager.nama || '') : '',
                    associate_manager_nik: x.associate_manager ? (x.associate_manager.nik || '') : '',
                    associate_manager_nama: x.associate_manager ? (x.associate_manager.nama || '') : '',
                    jumlah_bawahan: x.jumlah_bawahan,
                    pendidikan: x.pendidikan,
                    tanggal_join: x.tanggal_join,
                    tanggal_lahir: x.tanggal_lahir,
                    usia: x.usia
                };
                html += '<tr>' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td class="text-center">' + escapeHtml(x.periode) + '</td>' +
                    '<td>' + escapeHtml(x.nik) + '</td>' +
                    '<td>' + escapeHtml(x.nama || '-') + '</td>' +
                    '<td>' + escapeHtml(x.jabatan || '-') + '</td>' +
                    '<td>' + escapeHtml(x.departemen || '-') + '</td>' +
                    '<td>' + escapeHtml(x.sub_departemen || '-') + '</td>' +
                    '<td>' + escapeHtml(x.section || '-') + '</td>' +
                    '<td class="text-center">' + x.level + '</td>' +
                    '<td class="text-center">' + levelBadge(x.group) + '</td>' +
                    '<td>' + mgr + '</td>' +
                    '<td title="' + amTitle + '">' + am + '</td>' +
                    '<td class="text-center" style="white-space:nowrap;">' +
                    '<button class="btn btn-sm btn-warning btn-prt-hist-edit" data-row="' + encodeURIComponent(JSON.stringify(rowData)) + '">Edit</button> ' +
                    '<button class="btn btn-sm btn-danger btn-prt-hist-delete" data-id="' + x.id + '" data-nik="' + escapeHtml(x.nik) + '" data-source="' + escapeHtml(x.source || '') + '">Hapus</button>' +
                    '</td>' +
                    '</tr>';
            });
            $('#prtHistTbody').html(html);
            renderPrtHistPagination(res.meta || {});
        }).fail(function () {
            $('#prtHistTbody').html('<tr><td colspan="13" class="text-center text-muted">Gagal memuat history.</td></tr>');
        });
    }

    function renderPrtHistPagination(meta) {
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        let total = parseInt(meta.total || 0);
        if (total === 0) {
            $('#prtHistPageLabel').text('');
            $('#prtHistPagination').html('');
            return;
        }
        let prev = page > 1
            ? '<button class="btn btn-sm btn-outline-primary mr-1 prt-hist-page-btn" data-page="' + (page - 1) + '">Prev</button>'
            : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage
            ? '<button class="btn btn-sm btn-outline-primary prt-hist-page-btn" data-page="' + (page + 1) + '">Next</button>'
            : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) {
            opts += '<option value="' + n + '"' + (n === prtHistPerPage ? ' selected' : '') + '>' + n + '</option>';
        });
        $('#prtHistPageLabel').text('Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' peserta)');
        $('#prtHistPagination').html(
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.82rem;">Tampilkan</span>' +
            '<select id="prtHistPerPageSel" class="form-control form-control-sm" style="width:auto; display:inline-block;">' + opts + '</select>' +
            '<span class="text-muted" style="font-size:.82rem;">per halaman</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.prt-hist-page-btn', function () {
        prtHistPage = parseInt($(this).data('page'));
        loadPrtHistList();
    });

    $(document).on('change', '#prtHistPerPageSel', function () {
        prtHistPerPage = parseInt($(this).val()) || 10;
        prtHistPage = 1;
        loadPrtHistList();
    });

    $(document).on('change', '#selPrtHistPeriode', function () {
        prtHistPeriode = $(this).val();
        prtHistPage = 1;
        loadPrtHistList();
    });

    $('#formSearchPrtHist').on('submit', function (e) {
        e.preventDefault();
        prtHistPage = 1;
        loadPrtHistList();
    });

    // === CRUD HISTORY PESERTA ===
    const PRT_HIST_STORE_URL = "{{ url('/hr/upload-file-template-pk/participants-history') }}";
    const PRT_HIST_UPDATE_URL = "{{ url('/hr/upload-file-template-pk/participants-history/update') }}";
    const PRT_HIST_DELETE_URL = "{{ url('/hr/upload-file-template-pk/participants-history/delete') }}";

    function prtHistFillPeriodeSelect(selected) {
        let opts = '';
        $('#selPrtHistPeriode option').each(function () {
            let v = $(this).val();
            if (v === '') return;
            opts += '<option value="' + escapeHtml(v) + '"' + (v === selected ? ' selected' : '') + '>' + escapeHtml(v) + '</option>';
        });
        $('#prtHistPeriode').html(opts).prop('disabled', false);
    }

    function prtHistSetNikField(key, nik, nama) {
        let $wrap = $('#prtHistModal .spv-nik-dropdown[data-key="' + key + '"]');
        let isEmpty = !nik || nik === '';
        let placeholder = $wrap.data('role') === 'associate' ? 'Tidak Ada' : 'Masukkan NIK';
        $wrap.find('.spv-nik-value').val(isEmpty ? '' : nik);
        $wrap.find('.hd-ms-label').text(isEmpty ? placeholder : (nik + ' - ' + nama));
        $wrap.find('.hd-ms-search').val('');
        $wrap.find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Ketik minimal 2 karakter untuk mencari NIK / Nama...</div>');
        $wrap.removeClass('open');
    }

    function prtHistResetForm() {
        $('#prtHistEditId').val('');
        $('#prtHistFormTitle').text('Tambah Peserta History');
        $('#btnSavePrtHist').text('Simpan');
        prtHistFillPeriodeSelect(prtHistPeriode);
        prtHistSetNikField('hist_ybs', '', '');
        prtHistSetNikField('hist_mgr', '', '');
        prtHistSetNikField('hist_am', '', '');
        $('#prtHistNama, #prtHistJabatan, #prtHistDepartemen, #prtHistSubDepartemen, #prtHistSection, #prtHistLevel, #prtHistJumlahBawahan, #prtHistManagerNama, #prtHistAmNama, #prtHistPendidikan, #prtHistTanggalJoin, #prtHistTanggalLahir, #prtHistUsia').val('');
        $('#prtHistGroup').val('');
    }

    function prtHistCollect() {
        return {
            nik: $('#prtHistNik').val() || '',
            nama: $('#prtHistNama').val() || '',
            jabatan: $('#prtHistJabatan').val() || '',
            departemen: $('#prtHistDepartemen').val() || '',
            sub_departemen: $('#prtHistSubDepartemen').val() || '',
            section: $('#prtHistSection').val() || '',
            level: $('#prtHistLevel').val() || '',
            level_group: $('#prtHistGroup').val() || '',
            manager_nik: $('#prtHistManagerNik').val() || '',
            manager_nama: $('#prtHistManagerNama').val() || '',
            associate_manager_nik: $('#prtHistAmNik').val() || '',
            associate_manager_nama: $('#prtHistAmNama').val() || '',
            jumlah_bawahan: $('#prtHistJumlahBawahan').val(),
            pendidikan: $('#prtHistPendidikan').val() || '',
            tanggal_join: $('#prtHistTanggalJoin').val() || '',
            tanggal_lahir: $('#prtHistTanggalLahir').val() || '',
            usia: $('#prtHistUsia').val()
        };
    }

    $(document).on('click', '#btnOpenPrtHistForm', function () {
        prtHistResetForm();
        $('#prtHistModal').modal('show');
    });

    $(document).on('click', '#prtHistModal .spv-nik-item', function () {
        let $wrap = $(this).closest('.spv-nik-dropdown');
        let key = $wrap.data('key');
        let nama = $(this).data('nama') || '';
        if (key === 'hist_ybs') {
            let raw = $(this).attr('data-row');
            if (raw) {
                try {
                    let row = JSON.parse(raw);
                    $('#prtHistNama').val(row.nama || nama);
                    $('#prtHistJabatan').val(row.jabatan || '');
                    $('#prtHistDepartemen').val(row.departemen || '');
                    $('#prtHistSubDepartemen').val(row.sub_departemen || '');
                    $('#prtHistSection').val(row.section || '');
                    $('#prtHistLevel').val(row.level || '');
                    $('#prtHistGroup').val(groupFromLevel(row.level));
                } catch (e) {
                    $('#prtHistNama').val(nama);
                }
            }
        } else if (key === 'hist_mgr') {
            $('#prtHistManagerNama').val(nama);
        } else if (key === 'hist_am') {
            $('#prtHistAmNama').val(nama);
        }
    });

    $(document).on('click', '.btn-prt-hist-edit', function () {
        let x = JSON.parse(decodeURIComponent($(this).data('row')));
        prtHistResetForm();
        $('#prtHistEditId').val(x.id);
        $('#prtHistFormTitle').text('Edit Peserta History');
        $('#btnSavePrtHist').text('Simpan Perubahan');
        $('#prtHistPeriode').val(x.periode).prop('disabled', true);
        prtHistSetNikField('hist_ybs', x.nik, x.nama || '');
        prtHistSetNikField('hist_mgr', x.manager_nik || '', x.manager_nama || '');
        prtHistSetNikField('hist_am', x.associate_manager_nik || '', x.associate_manager_nama || '');
        $('#prtHistNama').val(x.nama || '');
        $('#prtHistJabatan').val(x.jabatan || '');
        $('#prtHistDepartemen').val(x.departemen || '');
        $('#prtHistSubDepartemen').val(x.sub_departemen || '');
        $('#prtHistSection').val(x.section || '');
        $('#prtHistLevel').val(x.level !== null && x.level !== undefined ? x.level : '');
        $('#prtHistGroup').val(x.level_group || '');
        $('#prtHistJumlahBawahan').val(x.jumlah_bawahan !== null && x.jumlah_bawahan !== undefined ? x.jumlah_bawahan : '');
        $('#prtHistManagerNama').val(x.manager_nama || '');
        $('#prtHistAmNama').val(x.associate_manager_nama || '');
        $('#prtHistPendidikan').val(x.pendidikan || '');
        $('#prtHistTanggalJoin').val(x.tanggal_join || '');
        $('#prtHistTanggalLahir').val(x.tanggal_lahir || '');
        $('#prtHistUsia').val(x.usia !== null && x.usia !== undefined ? x.usia : '');
        $('#prtHistModal').modal('show');
    });

    $(document).on('click', '.btn-prt-hist-delete', function () {
        let id = $(this).data('id');
        let nik = $(this).data('nik') || '';
        let source = $(this).data('source') || '';
        let extra = source === 'pk_otorisasi'
            ? ' Baris ini berasal dari Mapping SPV — akan muncul lagi saat upload template PK atau simpan Mapping SPV.'
            : (source === 'pk_foreman'
                ? ' Baris ini berasal dari Mapping Foreman — akan muncul lagi saat upload template PK atau simpan Mapping Foreman.'
                : '');
        Swal.fire({
            title: 'Hapus peserta dari history?',
            html: 'NIK <strong>' + escapeHtml(nik) + '</strong> akan dihapus dari snapshot periode ini.' + extra + ' Nilai PK/IPP dan diskusi lama tetap tersimpan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: PRT_HIST_DELETE_URL + '/' + id,
                type: 'DELETE',
                data: { _token: csrf() },
                success: function (res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    loadPrtHistList();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menghapus peserta.', 'error');
                }
            });
        });
    });

    $('#btnSavePrtHist').on('click', function () {
        let editId = $('#prtHistEditId').val();
        let payload = prtHistCollect();
        if (!payload.nik.trim()) {
            Swal.fire('Peringatan', 'NIK wajib diisi.', 'warning');
            return;
        }
        if (!editId) {
            payload.periode = $('#prtHistPeriode').val() || '';
            if (!payload.periode) {
                Swal.fire('Peringatan', 'Periode wajib dipilih.', 'warning');
                return;
            }
        }
        payload._token = csrf();
        let url = editId ? (PRT_HIST_UPDATE_URL + '/' + editId) : PRT_HIST_STORE_URL;
        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            success: function (res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#prtHistModal').modal('hide');
                loadPrtHistList();
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menyimpan peserta.', 'error');
            }
        });
    });

    loadPkList();
    loadPrtList();
    loadPrtHistList();
});
</script>
@endpush
