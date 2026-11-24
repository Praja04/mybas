@extends('layouts.base')

@section('content')

<style>
    .ipp-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .ipp-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .ipp-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .ipp-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .ipp-badge-info { background:#e3f2fd; color:#0d47a1; }
    .ipp-badge-success { background:#c8e6c9; color:#1b5e20; }
    .ipp-table th { font-size:.8rem; white-space:nowrap; }
    .ipp-table td { font-size:.8rem; vertical-align:middle; }
    .ipp-pagination .page-link { padding:.2rem .5rem; font-size:.85rem; }
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
    .modal-ipp-xl .modal-dialog { max-width: 95%; margin: 1rem auto; }
    .ipp-table-wrapper { max-height: 60vh; overflow:auto; border:1px solid #dee2e6; border-radius:4px; }
    .ipp-table-wrapper table { margin-bottom:0; width:auto; min-width:100%; }
    .ipp-table-wrapper thead th { position:sticky; top:0; background:#f8f9fa; z-index:1; }
    .ipp-target-col { min-width:240px; white-space:nowrap; }
    .ipp-due-col { min-width:130px; white-space:nowrap; }
    .ipp-range-editor .form-control { display:inline-block; width:58px; }
    .ipp-range-editor .ipp-range-row { line-height:1.6; }
    /* Excel-like Column Resizer & Auto-expand Textarea */
    #ippPreviewTable {
        table-layout: fixed;
        width: 1480px;
        margin-bottom: 0;
    }
    #ippPreviewTable thead th {
        position: relative;
        user-select: none;
        background: #f8f9fa;
        vertical-align: middle;
        overflow: visible;
        padding-right: 12px;
    }
    #ippPreviewTable tbody td {
        vertical-align: top;
        overflow: hidden;
    }
    .ipp-col-resizer {
        position: absolute;
        top: 0;
        right: 0;
        width: 8px;
        bottom: 0;
        cursor: col-resize;
        user-select: none;
        z-index: 10;
        border-right: 2px solid transparent;
        transition: border-color .15s, background-color .15s;
    }
    .ipp-col-resizer:hover,
    .ipp-col-resizer.is-resizing {
        border-right: 3px solid #7b1fa2;
        background-color: rgba(123, 31, 162, 0.15);
    }
    body.ipp-col-resizing {
        cursor: col-resize !important;
        user-select: none !important;
    }
    .ipp-auto-expand {
        resize: vertical !important;
        min-height: 33px;
        line-height: 1.35;
        overflow-y: auto;
        font-size: .8rem;
        padding: .25rem .45rem;
        box-sizing: border-box;
        width: 100%;
        display: block;
        white-space: pre-wrap !important;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    .ipp-auto-expand:focus {
        border-color: #7b1fa2;
        box-shadow: 0 0 0 .15rem rgba(123, 31, 162, .15);
    }
    .ipp-month-head { background:#ede7f6 !important; color:#4a148c; }
    .ipp-range-editor .ipp-range-score { display:inline-flex; align-items:center; justify-content:center; }
    .ipp-otorisasi-form { min-height: 210px; }
    .ipp-nik-dropdown, .ipp-lokal-dropdown { position: relative; }
    .ipp-lokal-dropdown { max-width: 340px; }
    .ipp-nik-dropdown .hd-ms-btn, .ipp-lokal-dropdown .hd-ms-btn {
        width: 100%;
        text-align: left;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: .3rem .65rem;
        font-size: .875rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #495057;
        cursor: pointer;
    }
    .ipp-nik-dropdown .hd-ms-btn:hover, .ipp-lokal-dropdown .hd-ms-btn:hover { border-color: #4a148c; }
    .ipp-nik-dropdown .hd-ms-caret, .ipp-lokal-dropdown .hd-ms-caret { font-size: .9rem; color: #4a148c; }
    .ipp-nik-dropdown .hd-ms-dropdown, .ipp-lokal-dropdown .hd-ms-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 2000;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        margin-top: 2px;
        max-height: 300px;
        overflow: hidden;
        flex-direction: column;
    }
    .ipp-nik-dropdown.open .hd-ms-dropdown, .ipp-lokal-dropdown.open .hd-ms-dropdown { display: flex; }
    .ipp-nik-dropdown .hd-ms-search, .ipp-lokal-dropdown .hd-ms-search { margin: 6px; }
    .ipp-nik-dropdown .hd-ms-list, .ipp-lokal-dropdown .hd-ms-list { overflow-y: auto; max-height: 240px; padding: 4px 0; }
    .ipp-nik-dropdown .hd-ms-item, .ipp-lokal-dropdown .hd-ms-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        font-size: .82rem;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ipp-nik-dropdown .hd-ms-item:hover, .ipp-lokal-dropdown .hd-ms-item:hover { background: #f3e5f5; }
    .ipp-lokal-dropdown[data-kind="position"] .hd-ms-btn {
        height: calc(1.5em + 1.1rem + 2px);
        padding: .25rem .5rem;
        border-radius: .2rem;
    }
    .ipp-lokal-dropdown[data-kind="position"] .hd-ms-dropdown {
        width: max-content;
        min-width: 100%;
        max-width: min(700px, 94vw);
    }
    .ipp-lokal-dropdown[data-kind="position"] .hd-ms-item { overflow: visible; text-overflow: clip; }
    .ipp-nik-dropdown.ipp-nik-readonly .hd-ms-btn { background: #e8f5e9; cursor: not-allowed; }
    select.ipp-class-invalid { border-color: #e65100 !important; box-shadow: 0 0 0 .2rem rgba(230,81,0,.15); }
</style>

<div class="container-fluid">
    <h1 class="ipp-page-title">Upload File Template IPP</h1>

    @include('hr.partials.ipp-pk-subnav', ['snPlainBg' => true])

    {{-- TEMPLATE IPP LEVEL 2-3 (RUBRIK) --}}
    <div class="ipp-card">
        <h5>Template IPP Level 2-3 (STAFF)</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            Satu template rubrik untuk <strong>semua peserta Level 2-3</strong> per periode (tanpa Weight/KPI), dengan kolom
            <strong>NO | AREA | COMPETENCY BEHAVIOR | KEY BEHAVIOR | SCORING CRITERIA 1–5</strong>.
            Nilai <strong>Leader Assessment (1–5)</strong> diisi atasan/Associate Manager/Manager pada halaman
            <a href="{{ url('/hr/ipp-user-approval') }}" target="_blank">IPP User Approval</a>
            untuk tahapan Mid Year dan End Year.
        </p>

        <form id="formUpload23" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight:600;">Periode Penilaian (Tahun):</label>
                    <select name="periode" id="ipp23Periode" class="form-control form-control-sm" required>
                        <option value="">— Pilih Periode —</option>
                        @foreach($periodeOptions as $p)
                            <option value="{{ $p }}" @if($loop->first) selected @endif>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" style="font-weight:600;">File Template IPP 2-3 (.xlsx):</label>
                    <input type="file" name="file" id="fileInput23" class="form-control-file" accept=".xlsx,.xls" required>
                </div>
                <div class="col-md-4 d-flex align-items-end" style="gap:.5rem;">
                    <button type="submit" class="btn btn-success" id="btnUpload23">
                        Upload Template
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnDlTemplate23">
                        Download Template
                    </button>
                </div>
            </div>
        </form>
        <div id="ipp23Status" class="text-muted mt-2" style="font-size:.82rem;"></div>
    </div>

    <hr style="border:0; border-top:3px solid #c62828;">

    {{-- UPLOAD SECTION --}}
    <div class="ipp-card">
        <h5>Import Template IPP Excel Level 4 - 7 (SUPERVISOR - ASSOCIATE MANAGER)</h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:1rem;">
            Upload file Template IPP (.xlsx) sesuai format:
            <strong>PERIODE PENILAIAN</strong> pada baris 2 (contoh: 2026 atau 2026 - 2027), header
            <strong>No | Objective | Key Performance Area (KPA) | Key Performance Indicator (KPI) |
            Classification | Unit Of Measurement | Target | Due date</strong> pada baris 3, dan data dimulai baris 4.
            Setiap tahun dalam periode memiliki tahapan Aktual <strong>Mid Year</strong> dan <strong>End Year</strong>.
            Nama karyawan didapatkan setelah menambahkan Otorisasi (NIK Yang Bersangkutan).
        </p>

        <form id="formUpload" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;">File Template IPP:</label>
                    <input type="file" name="file" id="fileInput" class="form-control-file" accept=".xlsx,.xls" required>
                </div>
                <div class="col-md-6 d-flex align-items-end" style="gap:.5rem;">
                    <button type="submit" class="btn btn-success" id="btnUpload">
                        Upload Template
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnDlTemplate">
                        Download Template
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnCreateManual">
                        Buat IPP Baru (Manual)
                    </button>
                    <span class="text-muted" style="font-size:.78rem;">Buat tanpa file Excel, target &amp; aktivitas diisi langsung.</span>
                </div>
            </div>
        </form>
    </div>

    {{-- DAFTAR PESERTA (mapping PK) --}}
    <div class="pk-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 style="margin:0;">Daftar Peserta Penilaian Karya Dan IPP</h5>
            <div class="d-flex" style="gap:.5rem; flex-wrap:wrap;">
                <button type="button" class="btn btn-outline-dark btn-sm" id="btnOpenMappingHeader">
                    Kelola Mapping Manager (Sub Dept)
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="btnOpenAmMapping">
                    Kelola Mapping Associate Manager (Peserta)
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" id="btnOpenSpvMapping">
                    Kelola Mapping SPV
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" id="btnOpenForemanMapping">
                    Kelola Mapping Foreman
                </button>
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

    {{-- HISTORY DAFTAR PESERTA (snapshot per periode) --}}
    <div class="pk-card mt-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 style="margin:0;">History Daftar Peserta Penilaian Karya Dan IPP</h5>
            <div class="d-flex align-items-center" style="gap:.5rem;">
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
                    </tr>
                </thead>
                <tbody id="prtHistTbody">
                    <tr><td colspan="12" class="text-center text-muted">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">
            <span class="text-muted" style="font-size:.85rem;" id="prtHistPageLabel"></span>
            <div id="prtHistPagination" class="pk-pagination"></div>
        </div>
    </div>

    {{-- LIST SECTION --}}
    <div class="ipp-card">
        <h5>Daftar IPP</h5>

        <form id="formSearch" class="d-flex mb-3" style="gap:.5rem;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Periode / Dept / Position / GOLONGAN / Peserta / Upload Oleh...">
            <button type="submit" class="btn btn-primary">Cari Data</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered ipp-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Periode</th>
                        <th>Dept</th>
                        <th>POSITION</th>
                        <th>GOLONGAN</th>
                        <th>KPI</th>
                        <th>Peserta</th>
                        <th>Upload Oleh</th>
                        <th>Tanggal</th>
                        <th style="width:190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ippTbody">
                    <tr><td colspan="10" class="text-center text-muted">Belum ada data IPP.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-2" id="ippPagination"></div>
    </div>
</div>

{{-- PREVIEW / EDIT MODAL --}}
{{-- DOWNLOAD TEMPLATE MODAL --}}
<div class="modal fade" id="dlModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Download Template IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <label style="font-weight:600;">Periode Penilaian (Tahun):</label>
                <select id="dlPeriode" class="form-control form-control-sm">
                    <option value="">— Pilih Periode —</option>
                    @foreach($periodeOptions as $p)
                        <option value="{{ $p }}" @if($loop->first) selected @endif>{{ $p }}</option>
                    @endforeach
                </select>
                @if(empty($periodeOptions))
                    <div class="text-danger" style="font-size:.75rem; margin-top:.35rem;">Belum ada periode PK. Hubungi HR.</div>
                @endif
                <div class="text-muted mt-2" style="font-size:.78rem;">
                    Baris PERIODE PENILAIAN pada template akan diisi sesuai pilihan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnDlGo">Download</button>
            </div>
        </div>
    </div>
</div>

{{-- PREVIEW TEMPLATE IPP LEVEL 2-3 (RUBRIK) --}}
<div class="modal fade" id="ipp23Modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ipp23ModalTitle" style="color:#4a148c; font-weight:700;">Preview Template IPP Level 2-3</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="ipp23HeaderInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>
                <div id="ipp23Alert" class="mb-2" style="display:none;"></div>
                <div class="ipp-table-wrapper">
                    <table class="table table-bordered table-sm ipp-table" id="ipp23PreviewTable">
                        <thead>
                            <tr>
                                <th style="width:60px;">NO</th>
                                <th style="width:140px;">AREA</th>
                                <th style="min-width:240px;">COMPETENCY BEHAVIOR</th>
                                <th style="min-width:240px;">KEY BEHAVIOR</th>
                                <th style="min-width:150px;">1</th>
                                <th style="min-width:150px;">2</th>
                                <th style="min-width:150px;">3</th>
                                <th style="min-width:150px;">4</th>
                                <th style="min-width:150px;">5</th>
                            </tr>
                        </thead>
                        <tbody id="ipp23PreviewBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnSaveIpp23">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ippModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ippModalTitle" style="color:#4a148c; font-weight:700;">Preview Data IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="ippHeaderInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>
                <div id="ippSnapshotWarn" class="mb-2" style="display:none;"></div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddRow">+ Tambah KPI</button>
                    <span class="d-flex align-items-center">
                        <span id="ippWeightTotal" style="font-size:.9rem; font-weight:700; margin-right:1rem;"></span>
                        <span class="text-muted" style="font-size:.85rem;" id="ippModalMode"></span>
                    </span>
                </div>

                <div class="ipp-table-wrapper">
                    <table class="table table-bordered table-sm ipp-table" id="ippPreviewTable">
                        <thead id="ippTableHead"></thead>
                        <tbody id="ippTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnSaveIpp">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- OTORISASI MODAL --}}
<div class="modal fade" id="otorisasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Peserta IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="otorisasiInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm ipp-table">
                        <thead class="thead-light">
                            <tr>
                                <th>NIK Peserta</th>
                                <th>Nama Peserta</th>
                                <th>NIK Manager</th>
                                <th>Nama Manager</th>
                                <th>NIK Assoc. Manager</th>
                                <th>Nama Assoc. Manager</th>
                                <th style="width:130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="otorisasiTbody">
                            <tr><td colspan="7" class="text-center text-muted">Belum ada data peserta.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div id="otorisasiSnapshotWarn" class="mb-2"></div>
                <div class="p-3 ipp-otorisasi-form" id="otorisasiFormArea" style="background:#f5f5f5; border-radius:6px;">
                    <h6 style="font-weight:700; color:#4a148c;" id="otorisasiFormTitle">Tambah Peserta</h6>
                    <input type="hidden" id="otorisasiEditId" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-weight:600;">NIK Peserta:</label>
                            <div class="ipp-nik-dropdown" data-key="ybs">
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
                                <input type="hidden" class="ipp-nik-value" id="nikYbs" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="nikYbsName"></div>
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight:600;">NIK Manager:</label>
                            <div class="ipp-nik-dropdown" data-key="manager" data-role="manager">
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
                                <input type="hidden" class="ipp-nik-value" id="nikManager" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="nikManagerName"></div>
                        </div>
                        <div class="col-md-6" id="wrapAssocManager">
                            <label style="font-weight:600;">NIK Associate Manager:</label>
                            <div class="ipp-nik-dropdown" data-key="associate" data-role="associate">
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
                                <input type="hidden" class="ipp-nik-value" id="nikAssocManager" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="nikAssocManagerName"></div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary btn-sm" id="btnSimpanOtorisasi">Tambah Peserta</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MAPPING MANAGER MODAL --}}
<div class="modal fade" id="mappingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#4a148c; font-weight:700;">Mapping Manager per Sub Departmen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted" style="font-size:.82rem;">
                    Mapping ini dipakai untuk sub departmen yang tidak memiliki manager otomatis (misalnya sub departmen di Factory / Information Technology).
                </p>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm ipp-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Sub Departmen</th>
                                <th>NIK Manager</th>
                                <th>Nama Manager</th>
                                <th style="width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="mappingTbody">
                            <tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3" style="background:#f5f5f5; border-radius:6px;">
                    <h6 style="font-weight:700; color:#4a148c;">Tambah / Update Mapping</h6>
                    <div class="row">
                        <div class="col-md-5">
                            <label style="font-weight:600;">Sub Departmen:</label>
                            <select id="mapSubDept" class="form-control form-control-sm">
                                <option value="">— Pilih Sub Departmen —</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label style="font-weight:600;">NIK Manager:</label>
                            <div class="ipp-nik-dropdown" data-key="map-manager" data-role="manager">
                                <button type="button" class="hd-ms-btn">
                                    <span class="hd-ms-label" data-placeholder="Masukkan NIK">Masukkan NIK</span>
                                    <span class="hd-ms-caret">▾</span>
                                </button>
                                <div class="hd-ms-dropdown">
                                    <input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari NIK / Nama...">
                                    <div class="hd-ms-list">
                                        <div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Pilih dari daftar, atau ketik NIK / Nama...</div>
                                    </div>
                                </div>
                                <input type="hidden" class="ipp-nik-value" id="nikMapManager" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="nikMapManagerName"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm btn-block" id="btnSaveMapping">Simpan</button>
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

{{-- MAPPING ASSOCIATE MANAGER MODAL --}}
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

{{-- MODAL LIHAT JAWABAN AKTUAL --}}
<div class="modal fade" id="lihatModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lihatModalTitle" style="color:#4a148c; font-weight:700;">Jawaban Aktual IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="lihatInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>
                <div id="lihatKaryawanPicker" class="mb-3"></div>
                <div id="lihatTahapanPicker" class="mb-3"></div>
                <div class="ipp-table-wrapper">
                    <table class="table table-bordered" style="font-size:.8rem;">
                        <thead id="lihatTableHead"></thead>
                        <tbody id="lihatTableBody"></tbody>
                    </table>
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

@endsection

@push('scripts')
@include('hr.partials.pk-spv-mapping-script')
@include('hr.partials.pk-foreman-mapping-script')
<script>
    let listPage = 1;
    let currentMode = 'create';   // 'create' | 'edit'
    let currentId = null;
    let parsedData = null;        // hasil parse / data edit
    let manualMode = false;       // true hanya untuk "Buat IPP Baru (Manual)"
    let snapshotOptions = null;   // opsi peserta dari hr_pms_employee_snapshot periode terpilih
    let otorisasiIppPeriode = '';
    const DEPT_OPTIONS = @json($deptOptions);
    const SUB_DEPT_OPTIONS = @json($subDeptOptions);
    const POSITION_OPTIONS = @json($positionOptions);
    const GOLONGAN_OPTIONS = @json($golonganOptions);
    const PERIODE_OPTIONS = @json($periodeOptions);
    const CLASS_OPTIONS = ['High Is Good', 'High Is Bad', 'Max', 'Min', 'Standard Score', 'Range'];

    // Cegah Esc menutup Bootstrap modal saat popup SweetAlert sedang terbuka.
    const IPP_SWAL = Swal.mixin({ keydownListenerCapture: true });

    function classificationSelectHtml(val) {
        let valid = CLASS_OPTIONS.indexOf(val) !== -1;
        let cls = valid ? '' : ' ipp-class-invalid';
        let opts = CLASS_OPTIONS.map(function (c) {
            let s = (c === val) ? ' selected' : '';
            return '<option value="' + c + '"' + s + '>' + c + '</option>';
        }).join('');
        return '<select class="form-control form-control-sm ipp-detail-classification' + cls + '">' +
            '<option value=""' + (valid ? '' : ' selected') + '>— Pilih Classification —</option>' + opts + '</select>';
    }

    function tahapanLabel(t) {
        if (!t) return '-';
        let parts = String(t).split(' ');
        let kind = parts[0];
        let year = parts[1] || '';
        let label = (kind === 'MID' ? 'Mid Year' : kind === 'END' ? 'End Year' : t);
        return year ? label + ' ' + year : label;
    }

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, c => (
            { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]
        ));
    }

    function rangeScaleHtml(dtl, fallbackText) {
        if (dtl.classification !== 'Range' || !dtl.range_tiers) {
            return escapeHtml(fallbackText);
        }
        let tiers = Array.isArray(dtl.range_tiers) ? dtl.range_tiers.slice() : Object.values(dtl.range_tiers);
        if (!tiers.length) {
            return escapeHtml(fallbackText);
        }
        if (tiers.some(function (t) { return t.match_type === 'equals'; })) {
            tiers.sort(function (a, b) { return (a.score || 0) - (b.score || 0); });
            let tHtml = '<div class="ipp-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
            tiers.forEach(function (t) {
                tHtml += '<div class="text-nowrap"><strong>' + t.score + '</strong> = ' + escapeHtml(t.label || '') + '</div>';
            });
            tHtml += '</div>';
            return tHtml;
        }
        // Urut tampilan: skor 1 di paling atas (sama dengan layout editor).
        tiers.sort(function (a, b) { return a.score - b.score; });
        let bounds = [];
        tiers.forEach(function (t) {
            if (t.min_value !== null && t.min_value !== undefined) bounds.push(t.min_value);
            if (t.max_value !== null && t.max_value !== undefined) bounds.push(t.max_value);
        });
        let dec = rangeDecimals(bounds);
        let eps = Math.pow(10, -dec);
        let first = null;
        bounds.forEach(function (v) {
            let n = parseFloat(String(v).replace(',', '.'));
            if (!isNaN(n) && (first === null || n < first)) first = n;
        });
        let html = '<div class="ipp-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
        tiers.forEach(function (t) {
            let range = (t.min_value === null || t.min_value === undefined)
                ? ('&lt; ' + rangeFmt(t.max_value, dec))
                : ((t.max_value === null || t.max_value === undefined)
                    ? ('&gt; ' + rangeFmt(t.min_value, dec))
                    : (rangeFmt((first !== null && parseFloat(String(t.min_value)) === first) ? t.min_value : t.min_value + eps, dec) + ' &ndash; ' + rangeFmt(t.max_value, dec)));
            html += '<div class="text-nowrap"><strong>' + t.score + '</strong> = ' + range + '</div>';
        });
        html += '</div>';
        return html;
    }

    function rangeTextLabels(dtl) {
        let tiers = dtl.range_tiers;
        if (!tiers) return [];
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        return tiers
            .filter(function (t) { return t.match_type === 'equals'; })
            .sort(function (a, b) { return (a.score || 0) - (b.score || 0); })
            .map(function (t) { return t.label || ''; });
    }

    function isTextRange(dtl) {
        return dtl.classification === 'Range' && rangeTextLabels(dtl).length > 0;
    }

    // === Upload & Parse ===
    $('#formUpload').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#btnUpload').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-file-template-ipp/parse') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                currentMode = 'create';
                currentId = null;
                manualMode = false;
                snapshotOptions = null;
                parsedData = res.data;
                openModal('Preview Data IPP', 'Anda akan menyimpan data baru.');
                if (parsedData && parsedData.periode) {
                    loadSnapshotOptions(parsedData.periode);
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Gagal upload file.';
                IPP_SWAL.fire('Error', msg, 'error');
            },
            complete: function () {
                $('#btnUpload').prop('disabled', false).text('Upload & Parse');
            }
        });
    });

    // === Download Template ===
    $('#btnDlTemplate').on('click', function () {
        $('#dlModal').modal('show');
    });

    $('#btnDlGo').on('click', function () {
        let periode = $.trim($('#dlPeriode').val() || '');
        if (!periode) {
            IPP_SWAL.fire('Peringatan', 'Periode Penilaian (Tahun) wajib dipilih.', 'warning');
            return;
        }
        var downloadUrl = "{{ url('/hr/upload-file-template-ipp/download-template') }}?periode=" + encodeURIComponent(periode);
        var link = document.createElement('a');
        link.href = downloadUrl;
        link.target = '_blank';
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    // === Template IPP Level 2-3 (Rubrik) ===
    let parsedForemanData = null;
    let ipp23Mode = 'upload';
    let ipp23Rubrik = null;

    function ipp23StatusLoad() {
        let periode = $.trim($('#ipp23Periode').val() || '');
        if (!periode) {
            $('#ipp23Status').text('');
            return;
        }
        $.get("{{ url('/hr/upload-file-template-ipp/rubric/status') }}", { periode: periode }, function (res) {
            if (res && res.exists) {
                let info = 'Template periode <strong>' + escapeHtml(periode) + '</strong> tersimpan: ' +
                    res.rows + ' baris rubrik, ' + res.peserta + ' peserta, ' + res.filled + ' nilai terisi';
                if (res.uploaded_by) info += ' · diupload oleh ' + escapeHtml(res.uploaded_by);
                if (res.uploaded_at) info += ' · ' + escapeHtml(res.uploaded_at);
                $('#ipp23Status').html(info + '.');
            } else {
                $('#ipp23Status').text('Belum ada template rubrik untuk periode ' + periode + '.');
            }
        });
    }

    $('#ipp23Periode').on('change', ipp23StatusLoad);
    ipp23StatusLoad();

    $('#btnDlTemplate23').on('click', function () {
        var downloadUrl = "{{ url('/hr/upload-file-template-ipp/rubric/download') }}";
        var link = document.createElement('a');
        link.href = downloadUrl;
        link.target = '_blank';
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    $('#formUpload23').on('submit', function (e) {
        e.preventDefault();
        let periode = $.trim($('#ipp23Periode').val() || '');
        if (!periode) {
            IPP_SWAL.fire('Peringatan', 'Periode Penilaian (Tahun) wajib dipilih.', 'warning');
            return;
        }
        let formData = new FormData(this);
        $('#btnUpload23').prop('disabled', true).text('Mengupload...');
        $.ajax({
            url: "{{ url('/hr/upload-file-template-ipp/rubric/parse') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                parsedForemanData = res.data || null;
                if (!parsedForemanData || !parsedForemanData.details || !parsedForemanData.details.length) {
                    IPP_SWAL.fire('Error', 'Tidak ada data rubrik pada file.', 'error');
                    return;
                }
                ipp23Mode = 'upload';
                ipp23Rubrik = null;
                renderIpp23Preview();
                $('#ipp23Modal').modal('show');
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Gagal upload file.';
                IPP_SWAL.fire('Error', msg, 'error');
            },
            complete: function () {
                $('#btnUpload23').prop('disabled', false).text('Upload Template');
            }
        });
    });

    function renderIpp23Preview() {
        if (ipp23Mode === 'upload') {
            let d = parsedForemanData;
            $('#ipp23ModalTitle').text('Preview Template IPP Level 2-3');
            $('#ipp23Alert').hide().html('');
            $('#ipp23HeaderInfo').html(
                '<strong>Periode:</strong> ' + escapeHtml(d.periode || '-') +
                ' &nbsp;|&nbsp; <strong>File:</strong> ' + escapeHtml(d.file_name || '-') +
                ' &nbsp;|&nbsp; <strong>' + d.details.length + ' baris rubrik</strong>'
            );
            let html = '';
            d.details.forEach(function (row) {
                html += renderIpp23TextRow(row);
            });
            $('#ipp23PreviewBody').html(html);
            $('#btnSaveIpp23').show().prop('disabled', false).text('Simpan');
            return;
        }

        let d = ipp23Rubrik || {};
        let isEdit = ipp23Mode === 'edit';
        let details = d.details || [];
        let filled = parseInt(d.filled || 0, 10);

        $('#ipp23ModalTitle').text((isEdit ? 'Edit' : 'Lihat') + ' Template IPP Level 2-3');
        let info = '<strong>Periode:</strong> ' + escapeHtml(d.periode || '-') +
            ' &nbsp;|&nbsp; <strong>' + details.length + ' baris rubrik</strong>' +
            ' &nbsp;|&nbsp; <strong>' + parseInt(d.peserta || 0, 10) + ' peserta</strong>' +
            ' &nbsp;|&nbsp; <strong>' + filled + ' nilai terisi</strong>';
        if (d.uploaded_by) {
            info += ' &nbsp;|&nbsp; <strong>Upload:</strong> ' + escapeHtml(d.uploaded_by);
            if (d.uploaded_at) info += ' (' + escapeHtml(d.uploaded_at) + ')';
        }
        $('#ipp23HeaderInfo').html(info);

        if (isEdit && filled > 0) {
            $('#ipp23Alert').show().html('<div class="alert alert-warning mb-0 py-2" style="font-size:.85rem;">' +
                'Template sudah memiliki penilaian. Edit dinonaktifkan; hapus penilaian terlebih dahulu untuk mengganti isi template.</div>');
        } else {
            $('#ipp23Alert').hide().html('');
        }

        let html = '';
        let lastSection = null;
        details.forEach(function (row) {
            if (row.section && row.section !== lastSection) {
                lastSection = row.section;
                html += '<tr style="background:#f2f2f2;"><td colspan="9" style="font-weight:700;">' + escapeHtml(row.section) + '</td></tr>';
            }
            html += isEdit ? renderIpp23EditRow(row) : renderIpp23TextRow(row);
        });
        $('#ipp23PreviewBody').html(html);

        $('#btnSaveIpp23').toggle(isEdit).prop('disabled', isEdit && filled > 0).text('Simpan');
    }

    function renderIpp23TextRow(row) {
        return '<tr>' +
            '<td class="text-center"><strong>' + escapeHtml(row.no_urut) + '</strong></td>' +
            '<td>' + escapeHtml(row.area) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.competency_behavior) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.key_behavior) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.kriteria_1) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.kriteria_2) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.kriteria_3) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.kriteria_4) + '</td>' +
            '<td style="white-space:pre-wrap;">' + escapeHtml(row.kriteria_5) + '</td>' +
            '</tr>';
    }

    function renderIpp23EditRow(row) {
        function input(field, value) {
            return '<input type="text" class="form-control form-control-sm ipp23-input" data-field="' + field + '" value="' + escapeHtml(value || '') + '">';
        }
        function area(field, value) {
            return '<textarea rows="2" class="form-control form-control-sm ipp23-input" data-field="' + field + '">' + escapeHtml(value || '') + '</textarea>';
        }
        return '<tr>' +
            '<td>' + input('no_urut', row.no_urut) + '<input type="hidden" class="ipp23-section" value="' + escapeHtml(row.section || '') + '"></td>' +
            '<td>' + input('area', row.area) + '</td>' +
            '<td>' + area('competency_behavior', row.competency_behavior) + '</td>' +
            '<td>' + area('key_behavior', row.key_behavior) + '</td>' +
            '<td>' + area('kriteria_1', row.kriteria_1) + '</td>' +
            '<td>' + area('kriteria_2', row.kriteria_2) + '</td>' +
            '<td>' + area('kriteria_3', row.kriteria_3) + '</td>' +
            '<td>' + area('kriteria_4', row.kriteria_4) + '</td>' +
            '<td>' + area('kriteria_5', row.kriteria_5) + '</td>' +
            '</tr>';
    }

    function collectIpp23EditDetails() {
        let details = [];
        $('#ipp23PreviewBody tr').each(function () {
            let $tr = $(this);
            if (!$tr.find('.ipp23-input').length) return;
            let d = { section: $tr.find('.ipp23-section').val() || null };
            $tr.find('.ipp23-input').each(function () {
                d[$(this).data('field')] = String($(this).val() || '');
            });
            details.push(d);
        });
        return details;
    }

    function openRubrikModal(id, mode) {
        $.get("{{ url('/hr/upload-file-template-ipp/rubric') }}/" + id, function (res) {
            if (!res || res.success === false || !res.data) {
                IPP_SWAL.fire('Error', (res && res.message) || 'Gagal memuat template rubrik.', 'error');
                return;
            }
            ipp23Mode = mode;
            ipp23Rubrik = res.data;
            parsedForemanData = null;
            renderIpp23Preview();
            $('#ipp23Modal').modal('show');
        }).fail(function (xhr) {
            let msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal memuat template rubrik.';
            IPP_SWAL.fire('Error', msg, 'error');
        });
    }

    $(document).on('click', '.btn-lihat-rubrik', function () {
        openRubrikModal($(this).data('id'), 'view');
    });

    $(document).on('click', '.btn-edit-rubrik', function () {
        openRubrikModal($(this).data('id'), 'edit');
    });

    function ipp23StoreError(xhr) {
        let msg = xhr.responseJSON?.message || 'Gagal menyimpan template.';
        let errs = xhr.responseJSON?.errors;
        if (errs) {
            let firstKey = Object.keys(errs)[0];
            if (firstKey && errs[firstKey] && errs[firstKey][0]) {
                msg = errs[firstKey][0];
            }
        }
        IPP_SWAL.fire('Error', msg, 'error');
    }

    $('#btnSaveIpp23').on('click', function () {
        let btn = $(this);

        if (ipp23Mode === 'edit') {
            if (!ipp23Rubrik) return;
            if (parseInt(ipp23Rubrik.filled || 0, 10) > 0) {
                IPP_SWAL.fire('Perhatian', 'Template sudah memiliki penilaian. Hapus penilaian terlebih dahulu untuk mengganti isi template.', 'warning');
                return;
            }
            let details = collectIpp23EditDetails();
            if (!details.length) {
                IPP_SWAL.fire('Error', 'Tidak ada baris rubrik untuk disimpan.', 'error');
                return;
            }
            if (details.some(d => !String(d.no_urut || '').trim())) {
                IPP_SWAL.fire('Error', 'Kolom NO wajib diisi untuk semua baris.', 'error');
                return;
            }
            btn.prop('disabled', true).text('Menyimpan...');
            $.ajax({
                url: "{{ url('/hr/upload-file-template-ipp/rubric/store') }}",
                type: 'POST',
                data: {
                    _token: csrf(),
                    periode: ipp23Rubrik.periode,
                    details: details,
                },
                success: function (res) {
                    $('#ipp23Modal').modal('hide');
                    IPP_SWAL.fire('Berhasil', res.message || 'Template tersimpan.', 'success');
                    ipp23Rubrik = null;
                    ipp23StatusLoad();
                    loadList();
                },
                error: ipp23StoreError,
                complete: function () {
                    btn.prop('disabled', false).text('Simpan');
                }
            });
            return;
        }

        if (!parsedForemanData) return;
        btn.prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: "{{ url('/hr/upload-file-template-ipp/rubric/store') }}",
            type: 'POST',
            data: {
                _token: csrf(),
                periode: parsedForemanData.periode,
                details: parsedForemanData.details,
            },
            success: function (res) {
                $('#ipp23Modal').modal('hide');
                IPP_SWAL.fire('Berhasil', res.message || 'Template tersimpan.', 'success');
                parsedForemanData = null;
                ipp23StatusLoad();
                loadList();
            },
            error: ipp23StoreError,
            complete: function () {
                btn.prop('disabled', false).text('Simpan');
            }
        });
    });

    // === Buat IPP Manual (tanpa file Excel) ===
    $('#btnCreateManual').on('click', function () {
        currentMode = 'create';
        currentId = null;
        manualMode = true;
        snapshotOptions = null;
        parsedData = {
            periode: '',
            dept: '',
            position: '',
            golongan: '',
            details: [],
        };
        openModal('Buat IPP Baru (Manual)', 'Data baru diisi manual, tanpa file Excel.');
        setTimeout(function () {
            $('#ippPeriode').trigger('focus');
        }, 400);
    });

    // === Modal render ===
    function openModal(title, modeText) {
        $('#ippModalTitle').text(title);
        $('#ippModalMode').text(modeText);
        renderHeaderInfo();
        renderTargetTable();
        $('#ippModal').modal('show');
    }

    function snapshotStatus() {
        if (!manualMode) return 'n/a';
        if (!snapshotOptions) return 'waiting';
        return snapshotOptions.has_snapshot ? 'ok' : 'empty';
    }

    function snapshotDeptList() {
        return (snapshotOptions && snapshotOptions.has_snapshot) ? (snapshotOptions.dept || []).slice() : [];
    }

    function snapshotPositionList(dept) {
        let rows = (snapshotOptions && snapshotOptions.positions) || [];
        let out = [];
        rows.forEach(function (p) {
            if (dept && String(p.dept || '') !== dept) return;
            if (out.indexOf(p.v) === -1) out.push(p.v);
        });
        return out;
    }

    function snapshotLevelForPosition(pos, dept) {
        let rows = (snapshotOptions && snapshotOptions.positions) || [];
        for (let i = 0; i < rows.length; i++) {
            if (String(rows[i].v) !== pos) continue;
            if (dept && String(rows[i].dept || '') !== dept) continue;
            if (rows[i].level) return String(rows[i].level);
        }
        return null;
    }

    function updateSnapshotWarning() {
        let status = snapshotStatus();
        let html = '';
        if (status === 'waiting') {
            html = '<div class="alert alert-info" style="font-size:.82rem; margin-bottom:0;">Pilih Periode Penilaian (Tahun) terlebih dahulu — opsi Dept, Position, dan Golongan dimuat dari peserta (snapshot) periode tersebut.</div>';
        } else if (status === 'empty') {
            html = '<div class="alert alert-warning" style="font-size:.82rem; margin-bottom:0;">' +
                escapeHtml(snapshotOptions.message || 'Peserta periode ini belum di-capture.') + '</div>';
        }
        $('#ippSnapshotWarn').html(html).toggle(html !== '');
    }

    function loadSnapshotOptions(periode) {
        $.get("{{ url('/hr/upload-file-template-ipp/snapshot-options') }}", { periode: periode }, function (res) {
            if (manualMode && $.trim($('#ippPeriode').val() || '') !== String(res.periode || periode)) return;
            snapshotOptions = res;
            renderHeaderInfo();
            if (manualMode && !res.has_snapshot) {
                IPP_SWAL.fire({
                    title: 'Peserta Belum Di-capture',
                    text: res.message || ('Peserta periode ' + periode + ' belum di-capture.'),
                    icon: 'warning',
                    confirmButtonText: 'Mengerti'
                });
            }
        }).fail(function () {
            snapshotOptions = {
                has_snapshot: false,
                periode: periode,
                dept: [],
                positions: [],
                levels: [],
                peserta: [],
                message: 'Gagal memuat opsi snapshot periode ' + periode + '.'
            };
            renderHeaderInfo();
        });
    }

    function snapshotPesertaList(dept, pos) {
        let rows = (snapshotOptions && snapshotOptions.peserta) || [];
        if (!pos) return [];
        return rows.filter(function (p) {
            if (String(p.position || '') !== pos) return false;
            if (dept && String(p.dept || '') !== dept) return false;
            return true;
        });
    }

    function pesertaBlockHtml() {
        if (currentMode !== 'create') return '';

        let html = '<div class="row"><div class="col-12 mb-2"><label style="font-weight:600;">Peserta IPP:</label>';
        if (!snapshotOptions) {
            html += '<div class="text-muted">' + (manualMode ? 'Pilih Periode Penilaian terlebih dahulu.' : 'Memuat peserta...') + '</div>';
        } else if (!snapshotOptions.has_snapshot) {
            html += '<div class="text-muted">' + escapeHtml(snapshotOptions.message || 'Peserta periode ini belum di-capture.') + '</div>';
        } else {
            let dept = $.trim((parsedData && parsedData.dept) || '');
            let pos = $.trim((parsedData && parsedData.position) || '');
            if (pos === '') {
                html += '<div class="text-muted">Pilih POSITION terlebih dahulu.</div>';
            } else {
                let items = snapshotPesertaList(dept, pos);
                if (!items.length) {
                    html += '<div class="text-muted">— Tidak ada calon peserta —</div>';
                } else if (items.length === 1) {
                    let u = items[0];
                    html += '<label class="ipp-peserta-option" style="font-weight:600; color:#1b5e20;">' +
                        '<input type="checkbox" class="ipp-peserta-chk" value="' + escapeHtml(u.nik) + '" checked> ' +
                        escapeHtml(u.nik + ' - ' + u.nama + ' (L' + u.level + ')') +
                        '</label>';
                } else {
                    html += '<div class="p-2 mb-1" style="background:#fff3cd; border:1px solid #ffeeba; border-radius:4px;">Silakan centang peserta IPP yang ingin didaftarkan (bisa semua bila template/posisi sama).</div>';
                    html += '<div class="mb-1" style="max-height:150px; overflow:auto;">';
                    items.forEach(function (u) {
                        html += '<label class="ipp-peserta-option d-block" style="font-weight:normal;">' +
                            '<input type="checkbox" class="ipp-peserta-chk" value="' + escapeHtml(u.nik) + '"> ' +
                            escapeHtml(u.nik + ' - ' + u.nama + ' (L' + u.level + ')') +
                            '</label>';
                    });
                    html += '</div>';
                    html += '<button type="button" class="btn btn-sm btn-outline-primary btn-peserta-all">Pilih Semua</button> ' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary btn-peserta-clear">Bersihkan</button>';
                    html += '<div class="mt-2" style="border-top:1px dashed #ccc; padding-top:.5rem;">' +
                        '<label class="mb-0" style="font-weight:600;">' +
                        '<input type="checkbox" class="ipp-pisahkan-template"> Pisahkan template per peserta' +
                        '</label>' +
                        '<div class="text-muted" style="font-size:.75rem;">Jika dicentang, dibuat 1 template IPP terpisah untuk setiap peserta yang Anda centang.</div>' +
                        '</div>';
                }
            }
        }
        html += '</div></div>';
        return html;
    }

    function refreshPesertaBlock() {
        let $block = $('#ippPesertaBlock');
        if (!$block.length) return;
        $block.html(pesertaBlockHtml());
    }

    function renderHeaderInfo() {
        if (!parsedData) return;

        let status = snapshotStatus();
        let lockControls = manualMode && status !== 'ok';

        let deptList = (manualMode && status === 'ok') ? snapshotDeptList() : (manualMode ? [] : DEPT_OPTIONS.slice());
        if (parsedData.dept && deptList.indexOf(parsedData.dept) === -1) deptList.push(parsedData.dept);
        deptList.sort();

        let posList = manualMode
            ? (status === 'ok' ? snapshotPositionList(parsedData.dept || '') : [])
            : POSITION_OPTIONS.slice();
        if (parsedData.position && posList.indexOf(parsedData.position) === -1) posList.push(parsedData.position);

        let golList = (manualMode && status === 'ok') ? (snapshotOptions.levels || []).slice() : (manualMode ? [] : GOLONGAN_OPTIONS.slice());
        if (parsedData.golongan && golList.indexOf(parsedData.golongan) === -1) golList.push(parsedData.golongan);
        golList.sort(function (a, b) { return parseInt(a || 0, 10) - parseInt(b || 0, 10); });

        let html = '';
        html += '<div class="row">';
        html += '<div class="col-md-3 mb-2"><label style="font-weight:600;">Periode Penilaian (Tahun):</label>';
        html += periodeSelectHtml(parsedData.periode || '');
        html += '</div>';
        html += '<div class="col-md-3 mb-2"><label style="font-weight:600;">Dept:</label>';
        html += '<select id="ippDept" class="form-control form-control-sm">' +
            '<option value="">— Pilih Dept —</option>' +
            deptList.map(function (d) {
                let s = (d === (parsedData.dept || '')) ? ' selected' : '';
                return '<option value="' + escapeHtml(d) + '"' + s + '>' + escapeHtml(d) + '</option>';
            }).join('') +
            '</select></div>';
        html += '<div class="col-md-3 mb-2"><label style="font-weight:600;">POSITION:</label>';
        html += buildLokalDropdown('position', parsedData.position || 'Pilih Position...', posList.map(function (p) {
            return {
                value: p,
                label: p,
                search: String(p).toLowerCase(),
                html: '<span>' + escapeHtml(p) + '</span>'
            };
        })) + '</div>';
        html += '<div class="col-md-3 mb-2"><label style="font-weight:600;">GOLONGAN:</label>';
        html += '<select id="ippGolongan" class="form-control form-control-sm">' +
            '<option value="">— Pilih Golongan —</option>' +
            golList.map(function (g) {
                let s = (g === (parsedData.golongan || '')) ? ' selected' : '';
                return '<option value="' + escapeHtml(g) + '"' + s + '>' + escapeHtml(g) + '</option>';
            }).join('') +
            '</select></div>';
        html += '</div>';
        html += '<div id="ippPesertaBlock"></div>';
        html += '<div class="text-muted mt-1" style="font-size:.78rem;">' +
            (manualMode
                ? 'Opsi mengikuti peserta periode terpilih dari snapshot. Nama karyawan didapatkan dari Otorisasi (NIK Yang Bersangkutan).'
                : 'Nama karyawan didapatkan dari Otorisasi (NIK Yang Bersangkutan).') +
            '</div>';
        $('#ippHeaderInfo').html(html);

        if (lockControls) {
            $('#ippDept, #ippGolongan').prop('disabled', true);
            $('.ipp-lokal-dropdown[data-kind="position"] .hd-ms-btn').prop('disabled', true);
        }
        if (parsedData.position) {
            $('#ippPosition').val(parsedData.position);
            syncGolonganByPosition(false);
        }

        refreshPesertaBlock();
        updateSnapshotWarning();
    }

    $(document).on('click', '.btn-peserta-all', function () {
        $('#ippPesertaBlock .ipp-peserta-chk').prop('checked', true);
    });

    $(document).on('click', '.btn-peserta-clear', function () {
        $('#ippPesertaBlock .ipp-peserta-chk').prop('checked', false);
    });

    // === Opsi snapshot: periode & cascade dept -> position -> golongan ===
    $(document).on('change', '#ippPeriode', function () {
        if (!parsedData) return;
        let periode = $.trim($(this).val() || '');
        if (!manualMode) {
            parsedData.periode = periode;
            snapshotOptions = null;
            refreshPesertaBlock();
            if (periode !== '') {
                loadSnapshotOptions(periode);
            }
            return;
        }
        parsedData.periode = periode;
        parsedData.dept = '';
        parsedData.position = '';
        parsedData.golongan = '';
        snapshotOptions = null;
        renderHeaderInfo();
        if (periode !== '') {
            loadSnapshotOptions(periode);
        }
    });

    $(document).on('change', '#ippDept', function () {
        if (!parsedData) return;
        parsedData.dept = $.trim($(this).val() || '');
        if (!manualMode) {
            refreshPesertaBlock();
            return;
        }
        if (snapshotStatus() !== 'ok') return;
        parsedData.position = '';
        parsedData.golongan = '';
        renderHeaderInfo();
    });

    function achievementBadge(ach) {
        if (ach === 'Exceed') return '<span class="ipp-badge" style="background:#c8e6c9;color:#1b5e20;">Exceed</span>';
        if (ach === 'Achieve') return '<span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span>';
        if (ach === 'Not Achieve') return '<span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">Not Achieve</span>';
        return '<span class="text-muted">-</span>';
    }

    function periodeSelectHtml(val) {
        let html = '<select id="ippPeriode" class="form-control form-control-sm">';
        html += '<option value="">— Pilih Periode —</option>';
        let found = false;
        PERIODE_OPTIONS.forEach(function (p) {
            let s = (p === val) ? ' selected' : '';
            if (s) found = true;
            html += '<option value="' + escapeHtml(p) + '"' + s + '>' + escapeHtml(p) + '</option>';
        });
        if (val && !found) {
            html += '<option value="' + escapeHtml(val) + '" selected>' + escapeHtml(val) + ' (di luar periode PK)</option>';
        }
        html += '</select>';
        if (!PERIODE_OPTIONS.length) {
            html += '<div class="text-danger" style="font-size:.75rem;">Belum ada periode PK. Hubungi HR.</div>';
        }
        return html;
    }

    function numOr(v) { let n = parseFloat(String(v).replace(',', '.')); return isNaN(n) ? null : n; }
    function normalizeNumText(v) { return String(v === null || v === undefined ? '' : v).trim().replace(',', '.'); }

    // === Helper tampilan Range: gap 0.01 agar batas antar skor tidak kembar ===
    function rangeDecimals(values) {
        let d = 0;
        (values || []).forEach(function (v) {
            let n = numOr(v);
            if (n === null) return;
            let s = String(n);
            let dot = s.indexOf('.');
            let dec = dot === -1 ? 0 : s.length - dot - 1;
            if (dec > d) d = dec;
        });
        return Math.max(2, d);
    }

    function rangeFmt(v, decimals) {
        let n = numOr(v);
        if (n === null) return String(v === null || v === undefined ? '' : v);
        let s = n.toFixed(decimals);
        s = s.replace(/0+$/, '').replace(/\.$/, '');
        return s === '' ? '0' : s;
    }

    // Kumpulkan skor aktif + N-1 boundary dari tier {score,min_value,max_value} yang tersimpan.
    function boundariesFromTiers(tiers) {
        if (!tiers) return null;
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        if (!tiers.length) return null;
        let sorted = tiers.slice().sort(function (a, b) {
            return (parseInt(a.score, 10) || 0) - (parseInt(b.score, 10) || 0);
        });
        let scores = [];
        sorted.forEach(function (t) {
            let s = parseInt(t.score, 10);
            if (s >= 1 && s <= 5 && scores.indexOf(s) === -1) scores.push(s);
        });
        let set = {};
        sorted.forEach(function (t) {
            if (t.min_value !== null && t.min_value !== undefined) set[t.min_value] = 1;
            if (t.max_value !== null && t.max_value !== undefined) set[t.max_value] = 1;
        });
        let nums = Object.keys(set).map(parseFloat).sort(function (a, b) { return a - b; });
        if (!scores.length || nums.length !== scores.length - 1) return null;
        // Arah: tier ujung rendah (min null) punya skor 5 -> low_is_good, skor 1 -> high_is_good.
        let lowSeg = sorted.filter(function (t) { return t.min_value === null || t.min_value === undefined; })[0];
        let direction = (lowSeg && lowSeg.score === 1) ? 'high_is_good' : 'low_is_good';
        return { scores: scores, boundaries: nums, direction: direction };
    }

    // Mode editor + nilai tersimpan: numerik (high_is_good / low_is_good) atau `==` (5 label teks).
    function rangeStateFromTiers(tiers) {
        if (!tiers) return null;
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        if (!tiers.length) return null;
        if (tiers.some(function (t) { return t.match_type === 'equals'; })) {
            let labels = ['', '', '', '', ''];
            tiers.forEach(function (t) {
                let s = parseInt(t.score, 10);
                if (s >= 1 && s <= 5) labels[s - 1] = t.label || '';
            });
            return { mode: 'equals', scores: [1, 2, 3, 4, 5], boundaries: [], labels: labels };
        }
        if (tiers.some(function (t) { return ['gte', 'lte', 'eq', 'between'].indexOf(t.match_type) !== -1; })) {
            let rows = [];
            tiers.slice().sort(function (a, b2) { return (parseInt(a.score, 10) || 0) - (parseInt(b2.score, 10) || 0); })
                .forEach(function (t) {
                    let s = parseInt(t.score, 10);
                    if (s < 1 || s > 5) return;
                    let row = { score: s, op: '-', v1: '', v2: '' };
                    if (t.match_type === 'between') {
                        row.op = '-';
                        row.v1 = t.min_value;
                        row.v2 = t.max_value;
                    } else if (t.match_type === 'gte') {
                        row.op = '=>';
                        row.v1 = t.min_value;
                    } else if (t.match_type === 'lte') {
                        row.op = '=<';
                        row.v1 = t.max_value;
                    } else {
                        row.op = '=';
                        row.v1 = (t.min_value !== null && t.min_value !== undefined) ? t.min_value : t.max_value;
                    }
                    rows.push(row);
                });
            let dec = customScaleDecimals(rows);
            if (dec > 0) {
                rows.forEach(function (r) {
                    ['v1', 'v2'].forEach(function (k) {
                        let n = numOr(r[k]);
                        if (n !== null) r[k] = n.toFixed(dec);
                    });
                });
            }
            return {
                mode: 'custom',
                scores: rows.map(function (r) { return r.score; }),
                boundaries: [],
                direction: 'low_is_good',
                labels: ['', '', '', '', ''],
                customRows: rows
            };
        }
        let b = boundariesFromTiers(tiers);
        if (!b) return null;
        return { mode: b.direction, scores: b.scores, boundaries: b.boundaries, labels: ['', '', '', '', ''] };
    }

    function collectBoundaryValues($tr) {
        let b = [];
        $tr.find('.ipp-range-b').each(function () {
            let k = parseInt($(this).data('bound'), 10);
            if (!isNaN(k) && b[k] === undefined) b[k] = $(this).val();
        });
        return b;
    }

    // Update angka gap (b_prev + 0.01) & mirror tanpa re-render agar fokus input tidak hilang.
    function refreshRangeTexts($tr) {
        let b = collectBoundaryValues($tr);
        let dec = rangeDecimals(b);
        let eps = Math.pow(10, -dec);
        $tr.find('.ipp-range-btext[data-bound-text]').each(function () {
            let k = parseInt($(this).data('bound-text'), 10);
            let v = numOr(b[k]);
            $(this).text(v === null ? '…' : rangeFmt(v, dec));
        });
        $tr.find('.ipp-range-btext[data-lower-of]').each(function () {
            let k = parseInt($(this).data('lower-of'), 10);
            let v = numOr(b[k]);
            $(this).text(v === null ? '…' : rangeFmt(v + eps, dec));
        });
    }

    function collectRange($tr) {
        let mode = $tr.find('.ipp-range-dir:checked').val() || 'low_is_good';
        if (mode === 'custom') {
            let rows = [];
            let filled = 0;
            $tr.find('.ipp-range-row[data-score]').each(function () {
                let $row = $(this);
                let score = parseInt($row.data('score'), 10);
                if (isNaN(score) || score < 1 || score > 5) return;
                let $sel = $row.find('.ipp-range-cop');
                let $ins = $row.find('.ipp-range-cv');
                let op = $sel.length ? String($sel.val() || '=') : '-';
                let v1 = $ins.eq(0).length ? String($ins.eq(0).val() || '').trim() : '';
                let v2 = $ins.eq(1).length ? String($ins.eq(1).val() || '').trim() : '';
                if (v1 !== '' || v2 !== '') filled++;
                rows.push({ score: score, op: op, v1: v1, v2: v2 });
            });
            rows.sort(function (a, b2) { return a.score - b2.score; });
            if (!rows.length) rows = defaultCustomRows();
            return {
                mode: 'custom',
                scores: rows.map(function (r) { return r.score; }),
                boundaries: [],
                direction: 'low_is_good',
                labels: ['', '', '', '', ''],
                customRows: rows,
                filledCount: filled
            };
        }
        if (mode === 'equals') {
            let labels = ['', '', '', '', ''];
            let filled = 0;
            $tr.find('.ipp-range-label').each(function () {
                let k = parseInt($(this).data('score'), 10);
                if (!isNaN(k) && k >= 1 && k <= 5) {
                    let val = $.trim($(this).val() || '');
                    labels[k - 1] = val;
                    if (val !== '') filled++;
                }
            });
            return { mode: 'equals', scores: [1, 2, 3, 4, 5], boundaries: [], direction: 'low_is_good', labels: labels, filledCount: filled };
        }
        let direction0 = (mode === 'high_is_good') ? 'high_is_good' : 'low_is_good';
        // Skor aktif dari baris yang dirender (bisa subset, mis. [1,3,5]).
        let scores = [];
        $tr.find('.ipp-range-row[data-score]').each(function () {
            let s = parseInt($(this).data('score'), 10);
            if (!isNaN(s) && s >= 1 && s <= 5 && scores.indexOf(s) === -1) scores.push(s);
        });
        scores.sort(function (a, b) { return a - b; });
        if (!scores.length) scores = [1, 2, 3, 4, 5];
        let $b = $tr.find('.ipp-range-b');
        if (!$b.length) {
            return { mode: direction0, scores: scores, boundaries: [], direction: direction0, labels: ['', '', '', '', ''], filledCount: 0 };
        }
        // Ambil N-1 nilai kanonik dari input dengan data-bound=0..N-2.
        let boundaries = collectBoundaryValues($tr);
        for (let i = 0; i < scores.length - 1; i++) {
            if (boundaries[i] === undefined) boundaries[i] = '';
        }
        let direction = (mode === 'high_is_good') ? 'high_is_good' : 'low_is_good';
        let filled = boundaries.filter(function (x) { return String(x).trim() !== '' && numOr(x) !== null; });
        return { mode: direction, scores: scores, boundaries: boundaries, direction: direction, labels: ['', '', '', '', ''], filledCount: filled.length };
    }

    function refreshRangeScores($tr) {
        let $ed = $tr.find('.ipp-range-editor');
        if (!$ed.length) return;
        let prevDir = $ed.data('dir');
        let r = collectRange($tr);
        if (!r) return;
        let customRows = r.customRows;
        if (r.mode === 'custom' && prevDir !== 'custom') {
            customRows = defaultCustomRows();
        }
        $ed.closest('td').html(rangeEditorHtml(r.boundaries, r.mode, $tr.data('row'), r.labels, r.scores, customRows));
    }

    function rangeTargetValue($tr) {
        // Untuk Range target dihitung server dari boundary/label/custom; kirim string kosong.
        if ($tr.find('.ipp-range-editor').length) return '';
        return $tr.find('.ipp-detail-target').val();
    }

    // === Mode Custom: baris skor + operator + 1/2 input nilai ===
    function defaultCustomRows() {
        return [1, 2, 3, 4, 5].map(function (s) { return { score: s, op: '-', v1: '', v2: '' }; });
    }

    function customInputHtml(score, idx, value) {
        return '<input type="text" inputmode="decimal" class="form-control form-control-sm ipp-range-cv text-center" ' +
            'data-score="' + score + '" data-idx="' + idx + '" value="' + escapeHtml(value === null || value === undefined ? '' : value) + '" ' +
            'style="display:inline-block; width:58px; min-width:58px; vertical-align:middle; margin:0; padding:.1rem .2rem;">';
    }

    function customOpSelectHtml(score, op) {
        let opts = ['=', '=>', '=<'];
        let html = '<select class="ipp-range-cop form-control form-control-sm" data-score="' + score + '" ' +
            'style="display:inline-block; width:56px; min-width:56px; vertical-align:middle; margin:0; padding:.1rem .2rem; font-size:.72rem;">';
        opts.forEach(function (o) {
            html += '<option value="' + o + '"' + (op === o ? ' selected' : '') + '>' + o + '</option>';
        });
        return html + '</select>';
    }

    function customRowHtml(row) {
        let score = parseInt(row.score, 10);
        let op = row.op || '=';
        let inner = '<span class="ipp-range-score badge badge-secondary" style="min-width:1.4rem;">' + score + '</span>';
        if (op === '-') {
            inner += '<span>=</span>' + customInputHtml(score, 1, row.v1) + '<span>&ndash;</span>' + customInputHtml(score, 2, row.v2) +
                '<button type="button" class="ipp-range-cminus" data-score="' + score + '" title="Satu nilai saja (hapus input kedua)" style="border:1px dashed #6c757d; color:#6c757d; background:#fff; border-radius:4px; line-height:1; padding:0 4px; margin-left:2px;">&minus;</button>';
        } else {
            inner += customOpSelectHtml(score, op) + customInputHtml(score, 1, row.v1) +
                '<button type="button" class="ipp-range-cplus" data-score="' + score + '" title="Jadikan rentang (tambah input kedua)" style="border:1px dashed #a80000; color:#a80000; background:#fff; border-radius:4px; line-height:1; padding:0 4px; margin-left:2px;">+</button>';
        }
        if ([2, 3, 4].indexOf(score) !== -1) {
            inner += '<button type="button" class="ipp-range-delc" data-score="' + score + '" title="Hapus skor ' + score + '" style="border:0; background:transparent; color:#c62828; font-weight:700; line-height:1; padding:0 2px; margin-left:2px;">&times;</button>';
        }
        return '<div class="d-flex align-items-center ipp-range-row" data-score="' + score + '" style="gap:3px; margin-bottom:2px; white-space:nowrap;">' + inner + '</div>';
    }

    function renderCustomEditor($tr, rows) {
        rows.sort(function (a, b) { return a.score - b.score; });
        $tr.find('.ipp-range-editor').closest('td').html(
            rangeEditorHtml([], 'custom', $tr.data('row'), ['', '', '', '', ''], rows.map(function (r) { return r.score; }), rows)
        );
    }

    // Presisi desimal seragam mode Custom (0 bila semua bilangan bulat).
    function customDecimalsOf(text) {
        let s = String(text === null || text === undefined ? '' : text).trim().replace(',', '.');
        let dot = s.indexOf('.');
        if (dot === -1) return 0;
        return Math.max(0, s.length - dot - 1);
    }

    function customScaleDecimals(rows) {
        let d = 0;
        (rows || []).forEach(function (r) {
            d = Math.max(d, customDecimalsOf(r.v1), customDecimalsOf(r.v2));
        });
        return d;
    }

    function customGridSnap(x) {
        return Math.round(x * 1e6) / 1e6;
    }

    function fmtCustomGrid(v, decimals) {
        if (v === Infinity) return '+∞';
        if (v === -Infinity) return '-∞';
        let n = v / Math.pow(10, decimals);
        return decimals > 0 ? n.toFixed(decimals) : String(n);
    }

    // Validasi klien mode Custom: presisi seragam, rapat pada grid &
    // tidak tumpang tindih (mirror IppRangeScale::validateCustomRows).
    function customRowsError(rows) {
        if (!rows || !rows.length) return 'Aturan Custom wajib diisi.';
        let scores = rows.map(function (r) { return r.score; });
        if (scores.indexOf(1) === -1 || scores.indexOf(5) === -1) {
            return 'Aturan Custom wajib menyertakan skor 1 dan 5.';
        }
        let decimals = customScaleDecimals(rows);
        let factor = Math.pow(10, decimals);
        let intervals = [];
        for (let i = 0; i < rows.length; i++) {
            let r = rows[i];
            let v1 = numOr(r.v1);
            let v2 = numOr(r.v2);
            let point;
            if (r.op === '-') {
                if (v1 === null || v2 === null) return 'Skor ' + r.score + ': kedua angka rentang wajib diisi.';
                if (!(v1 < v2)) return 'Skor ' + r.score + ': angka pertama harus lebih kecil dari angka kedua.';
                intervals.push({ score: r.score, lo: Math.ceil(customGridSnap(v1 * factor)), hi: Math.floor(customGridSnap(v2 * factor)) });
            } else if (r.op === '=') {
                if (v1 === null) return 'Skor ' + r.score + ': nilai wajib diisi.';
                point = Math.round(customGridSnap(v1 * factor));
                intervals.push({ score: r.score, lo: point, hi: point });
            } else if (r.op === '=>') {
                if (v1 === null) return 'Skor ' + r.score + ': nilai wajib diisi.';
                intervals.push({ score: r.score, lo: Math.floor(customGridSnap(v1 * factor)) + 1, hi: Infinity });
            } else {
                if (v1 === null) return 'Skor ' + r.score + ': nilai wajib diisi.';
                intervals.push({ score: r.score, lo: -Infinity, hi: Math.ceil(customGridSnap(v1 * factor)) - 1 });
            }
        }
        for (let i = 0; i < intervals.length; i++) {
            for (let j = i + 1; j < intervals.length; j++) {
                if (Math.max(intervals[i].lo, intervals[j].lo) <= Math.min(intervals[i].hi, intervals[j].hi)) {
                    return 'Aturan tumpang tindih antara skor ' + intervals[i].score + ' dan skor ' + intervals[j].score + '.';
                }
            }
        }
        let sorted = intervals.slice().sort(function (a, b) { return a.lo - b.lo; });
        let reach = -Infinity;
        for (let i = 0; i < sorted.length; i++) {
            let iv = sorted[i];
            if (i > 0 && iv.lo > reach + 1) {
                return 'Belum rapat: ada celah antara ' + fmtCustomGrid(reach, decimals) + ' dan ' + fmtCustomGrid(iv.lo, decimals) + '.';
            }
            if (iv.hi > reach) reach = iv.hi;
        }
        return null;
    }

    function customTiersFromRows(rows) {
        let out = [];
        (rows || []).forEach(function (r) {
            let v1 = numOr(r.v1);
            let v2 = numOr(r.v2);
            if (r.op === '-') {
                out.push({ score: r.score, match_type: 'between', min_value: v1, max_value: v2 });
            } else if (r.op === '=') {
                out.push({ score: r.score, match_type: 'eq', min_value: v1, max_value: v1 });
            } else if (r.op === '=>') {
                out.push({ score: r.score, match_type: 'gte', min_value: v1, max_value: null });
            } else {
                out.push({ score: r.score, match_type: 'lte', min_value: null, max_value: v1 });
            }
        });
        return out;
    }

    function rangeEditorHtml(boundaries, mode, uid, labels, scores, customRows) {
        let b = boundaries || [];
        let lb = labels || ['', '', '', '', ''];
        let sc = (scores && scores.length) ? scores.slice().sort(function (a, b2) { return a - b2; }) : [1, 2, 3, 4, 5];
        let dir = (mode === 'equals') ? 'equals' : ((mode === 'high_is_good') ? 'high_is_good' : ((mode === 'custom') ? 'custom' : 'low_is_good'));
        let chkBad = dir === 'low_is_good' ? ' checked' : '';
        let chkGood = dir === 'high_is_good' ? ' checked' : '';
        let chkEq = dir === 'equals' ? ' checked' : '';
        let chkCustom = dir === 'custom' ? ' checked' : '';
        let rname = 'ipp-range-dir-' + (uid === undefined || uid === null || uid === '' ? 'x' : uid);
        let m = sc.length;
        let dec = rangeDecimals(b);
        let eps = Math.pow(10, -dec);
        function boundVal(k) {
            return (b[k] === undefined || b[k] === null) ? '' : b[k];
        }
        function bin(k) {
            return '<input type="text" inputmode="decimal" class="form-control form-control-sm ipp-range-b text-center" ' +
                'data-bound="' + k + '" value="' + escapeHtml(boundVal(k)) + '" style="display:inline-block; width:58px; min-width:58px; vertical-align:middle; margin:0; padding:.1rem .2rem;">';
        }
        function boundText(k) {
            let v = numOr(boundVal(k));
            let txt = (v === null) ? '…' : rangeFmt(v, dec);
            return '<span class="ipp-range-btext" data-bound-text="' + k + '" style="display:inline-block; min-width:34px; text-align:center; font-variant-numeric:tabular-nums;">' + escapeHtml(txt) + '</span>';
        }
        function lowerText(k) {
            let v = numOr(boundVal(k));
            let txt = (v === null) ? '…' : rangeFmt(v + eps, dec);
            return '<span class="ipp-range-btext" data-lower-of="' + k + '" style="display:inline-block; min-width:34px; text-align:center; font-variant-numeric:tabular-nums;">' + escapeHtml(txt) + '</span>';
        }
        function labelInput(k) {
            return '<input type="text" class="form-control form-control-sm ipp-range-label" ' +
                'data-score="' + (k + 1) + '" value="' + escapeHtml(lb[k]) + '" placeholder="Label ' + (k + 1) + '" ' +
                'style="display:inline-block; width:150px; min-width:150px; vertical-align:middle; margin:0; padding:.1rem .2rem;">';
        }
        function seg(i) {
            return '<span class="ipp-range-score badge badge-secondary" style="min-width:1.4rem;">' + i + '</span>';
        }
        function delBtn(score) {
            if ([2, 3, 4].indexOf(score) === -1 || m <= 3) return '';
            return '<button type="button" class="ipp-range-del" data-score="' + score + '" title="Hapus skor ' + score + '" ' +
                'style="border:0; background:transparent; color:#c62828; font-weight:700; line-height:1; padding:0 2px; margin-left:2px;">&times;</button>';
        }
        function row(score, inner) {
            return '<div class="d-flex align-items-center ipp-range-row" data-score="' + score + '" style="gap:3px; margin-bottom:2px; white-space:nowrap;">' + inner + '</div>';
        }
        let html = '<div class="ipp-range-editor" data-dir="' + dir + '" style="font-size:.78rem;">';
        if (dir === 'equals') {
            for (let k = 0; k < 5; k++) {
                html += row(k + 1, seg(k + 1) + '<span>=</span>' + labelInput(k));
            }
        } else if (dir === 'custom') {
            let crow = (customRows && customRows.length) ? customRows.slice() : defaultCustomRows();
            crow.sort(function (a, b2) { return a.score - b2.score; });
            crow.forEach(function (r) { html += customRowHtml(r); });
            if (crow.length < 5) {
                html += '<div class="mt-1"><button type="button" class="ipp-range-caddscore" style="font-size:.72rem; border:1px dashed #a80000; color:#a80000; background:#fff; border-radius:6px; padding:0 6px;">+ Skor</button></div>';
            }
            html += '<div class="mt-1 text-muted" style="font-size:.68rem;">Operator: = tepat, =&gt; lebih dari, =&lt; sampai dengan. Rentang: angka pertama &lt; nilai &le; angka kedua. Aturan Custom wajib rapat &amp; tidak tumpang tindih; skor 1 dan 5 wajib.</div>';
        } else {
            for (let i = 0; i < m; i++) {
                let score = sc[i];
                // Segmen kanonik j: (b[j-1], b[j]]; high_is_good searah baris, low_is_good kebalikannya.
                let j = (dir === 'high_is_good') ? i : (m - 1 - i);
                let inner;
                if (j === 0) {
                    inner = seg(score) + '<span>=</span><span>&lt;</span>' + bin(0);
                } else if (j === m - 1) {
                    inner = seg(score) + '<span>=</span><span>&gt;</span>' + boundText(m - 2);
                } else {
                    inner = seg(score) + '<span>=</span>' + (j === 1 ? boundText(0) : lowerText(j - 1)) + '<span>&ndash;</span>' + bin(j);
                }
                html += row(score, inner + delBtn(score));
            }
            if (m < 5) {
                html += '<div class="mt-1"><button type="button" class="ipp-range-add" style="font-size:.72rem; border:1px dashed #a80000; color:#a80000; background:#fff; border-radius:6px; padding:0 6px;">+ Skor</button></div>';
            }
        }
        html += '<div class="mt-1" style="font-size:.72rem;">' +
            '<label class="mr-2"><input type="radio" class="ipp-range-dir" name="' + rname + '" value="high_is_good"' + chkGood + '> High Is Good</label>' +
            '<label class="mr-2"><input type="radio" class="ipp-range-dir" name="' + rname + '" value="low_is_good"' + chkBad + '> High Is Bad</label>' +
            '<label class="mr-2"><input type="radio" class="ipp-range-dir" name="' + rname + '" value="custom"' + chkCustom + '> Custom</label>' +
            '<label><input type="radio" class="ipp-range-dir" name="' + rname + '" value="equals"' + chkEq + '> == (Teks)</label>' +
            '</div>';
        html += '</div>';
        return html;
    }

    function initRangeEditor($tr) {
        refreshRangeScores($tr);
    }

    function adjustTextareaHeight(el) {
        if (!el) return;
        let userH = $(el).data('user-height') || 0;
        el.style.height = 'auto';
        let scrollH = el.scrollHeight;
        let targetH = Math.max(33, userH, scrollH);
        el.style.height = targetH + 'px';
    }

    function initAutoExpand($container) {
        let $textareas = $container ? $container.find('.ipp-auto-expand') : $('.ipp-auto-expand');
        $textareas.each(function () {
            adjustTextareaHeight(this);
        });
    }

    function initTableColumnResize() {
        let $table = $('#ippPreviewTable');
        if (!$table.length) return;

        $table.find('.ipp-col-resizer').off('mousedown').on('mousedown', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let $resizer = $(this);
            let $th = $resizer.closest('th');
            let startX = e.pageX;
            let startThWidth = $th.outerWidth();
            let startTableWidth = $table.outerWidth();
            let minWidth = parseInt($th.data('min'), 10) || 40;

            $resizer.addClass('is-resizing');
            $('body').addClass('ipp-col-resizing');

            $(document).off('mousemove.ippColResize mouseup.ippColResize');

            $(document).on('mousemove.ippColResize', function (moveEvent) {
                let diff = moveEvent.pageX - startX;
                let newThWidth = Math.max(minWidth, startThWidth + diff);
                let actualDiff = newThWidth - startThWidth;

                $th.css({
                    'width': newThWidth + 'px',
                    'min-width': newThWidth + 'px'
                });
                $table.css('width', (startTableWidth + actualDiff) + 'px');
            });

            $(document).on('mouseup.ippColResize', function () {
                $(document).off('mousemove.ippColResize mouseup.ippColResize');
                $resizer.removeClass('is-resizing');
                $('body').removeClass('ipp-col-resizing');
            });
        });
    }

    function refreshTargetCell($tr, isRange) {
        let $td = $tr.find('.ipp-detail-target, .ipp-range-editor').first().closest('td');
        if (!$td.length) return;
        if (isRange) {
            let cur = collectRange($tr);
            let b = cur ? cur.boundaries : [];
            let mode = cur ? cur.mode : 'low_is_good';
            let labels = cur ? cur.labels : ['', '', '', '', ''];
            let scores = cur ? cur.scores : [1, 2, 3, 4, 5];
            let customRows = cur ? cur.customRows : null;
            $td.html(rangeEditorHtml(b, mode, $tr.data('row'), labels, scores, customRows));
            refreshRangeScores($tr);
        } else {
            let value = $tr.find('.ipp-detail-target').val() || '';
            let $ta = $('<textarea class="form-control form-control-sm ipp-detail-target ipp-auto-expand" rows="1" placeholder="Target"></textarea>');
            $ta.val(value);
            $td.html($ta);
            adjustTextareaHeight($ta[0]);
        }
    }

    function buildRowHtml(d, i) {
        let html = '<tr data-row="' + i + '">';
        html += '<td class="text-center">' + escapeHtml(d.no_urut) + '</td>';
        html += '<td><textarea class="form-control form-control-sm ipp-detail-objective ipp-auto-expand" rows="1" placeholder="Objective">' + escapeHtml(d.objective || '') + '</textarea></td>';
        html += '<td><textarea class="form-control form-control-sm ipp-detail-kpa ipp-auto-expand" rows="1" placeholder="KPA">' + escapeHtml(d.kpa || '') + '</textarea></td>';
        html += '<td><textarea class="form-control form-control-sm ipp-detail-kpi ipp-auto-expand" rows="1" placeholder="KPI">' + escapeHtml(d.kpi || '') + '</textarea></td>';
        html += '<td>' + classificationSelectHtml(d.classification || '') + '</td>';
        html += '<td><textarea class="form-control form-control-sm ipp-detail-uom ipp-auto-expand" rows="1" placeholder="UOM">' + escapeHtml(d.uom || '') + '</textarea></td>';
        html += '<td><input type="number" step="any" min="0" max="100" class="form-control form-control-sm ipp-detail-weight" value="' + escapeHtml(d.weight !== undefined && d.weight !== null ? d.weight : '') + '" placeholder="Weight"></td>';
        if (d.classification === 'Range') {
            let state = rangeStateFromTiers(d.range_tiers);
            let b = state ? state.boundaries : [];
            let mode = state ? state.mode : 'low_is_good';
            let labels = state ? state.labels : ['', '', '', '', ''];
            let scores = state ? state.scores : [1, 2, 3, 4, 5];
            let customRows = state ? state.customRows : null;
            html += '<td class="ipp-target-col" data-range-legacy="' + escapeHtml(d.target || '') + '">' + rangeEditorHtml(b, mode, i, labels, scores, customRows) + '</td>';
        } else {
            html += '<td class="ipp-target-col"><textarea class="form-control form-control-sm ipp-detail-target ipp-auto-expand" rows="1" placeholder="Target">' + escapeHtml(d.target !== undefined && d.target !== null ? d.target : '') + '</textarea></td>';
        }
        html += '<td><textarea class="form-control form-control-sm ipp-detail-due-date ipp-auto-expand" rows="1" placeholder="Due date">' + escapeHtml(d.due_date || '') + '</textarea></td>';
        html += '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger ipp-row-delete" title="Hapus baris">&times;</button></td>';
        html += '</tr>';
        return html;
    }

    function renderTargetTable() {
        if (!parsedData) return;
        let details = parsedData.details || [];

        let thead = '<tr>';
        thead += '<th style="width:50px; min-width:40px;" data-min="40" class="text-center">No<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:180px; min-width:80px;" data-min="80">Objective<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:180px; min-width:80px;" data-min="80">Key Performance Area<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:260px; min-width:100px;" data-min="100">Key Performance Indicator<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:145px; min-width:90px;" data-min="90">Classification<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:120px; min-width:70px;" data-min="70">Unit Of Measurement<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:80px; min-width:60px;" data-min="60" class="text-center">Weight<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:260px; min-width:120px;" data-min="120" class="ipp-target-col">Target<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:140px; min-width:80px;" data-min="80" class="ipp-due-col">Due date<div class="ipp-col-resizer"></div></th>';
        thead += '<th style="width:65px; min-width:55px;" class="text-center">AKSI</th>';
        thead += '</tr>';
        $('#ippTableHead').html(thead);
        $('#ippPreviewTable').css('width', '1480px');

        let tbody = '';
        details.forEach((d, i) => {
            tbody += buildRowHtml(d, i);
        });
        $('#ippTableBody').html(tbody);
        $('#ippTableBody tr[data-row]').each(function () {
            initRangeEditor($(this));
        });
        initAutoExpand($('#ippTableBody'));
        initTableColumnResize();
        updateWeightTotal();
    }

    // === Tambah / hapus baris ===
    $('#btnAddRow').on('click', function () {
        if (!parsedData) return;
        let maxNo = parsedData.details.reduce((m, d) => Math.max(m, parseInt(d.no_urut, 10) || 0), 0);
        parsedData.details.push({ no_urut: maxNo + 1, objective: '', kpa: '', kpi: '', classification: '', uom: '', weight: '', target: '', due_date: '' });
        let idx = parsedData.details.length - 1;
        let $row = $(buildRowHtml(parsedData.details[idx], idx));
        $('#ippTableBody').append($row);
        initRangeEditor($row);
        initAutoExpand($row);
        initTableColumnResize();
        updateWeightTotal();
    });

    $(document).on('input', '.ipp-auto-expand', function () {
        adjustTextareaHeight(this);
    });

    $(document).on('mouseup', '.ipp-auto-expand', function () {
        let h = parseInt(this.style.height, 10);
        if (h && h >= 33) {
            $(this).data('user-height', h);
        }
    });

    $('#ippModal').on('shown.bs.modal', function () {
        initAutoExpand($('#ippTableBody'));
        initTableColumnResize();
    });

    $(document).on('change', '.ipp-detail-classification', function () {
        refreshTargetCell($(this).closest('tr'), $(this).val() === 'Range');
    });

    $(document).on('input change', '.ipp-range-dir', function () {
        refreshRangeScores($(this).closest('tr'));
    });

    $(document).on('input', '.ipp-range-b', function () {
        refreshRangeTexts($(this).closest('tr'));
    });

    // Hapus satu score scale: rentangnya digabung ke skor tetangga yang lebih tinggi.
    $(document).on('click', '.ipp-range-del', function () {
        let $tr = $(this).closest('tr');
        let score = parseInt($(this).data('score'), 10);
        IPP_SWAL.fire({
            title: 'Hapus skor ' + score + '?',
            text: 'Rentang skor ' + score + ' akan digabung ke skor di atasnya.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
        }).then(function (r) {
            if (!r.isConfirmed) return;
            let state = collectRange($tr);
            if (!state || state.mode === 'equals') return;
            let scores = state.scores.slice();
            let bounds = state.boundaries.slice();
            let idx = scores.indexOf(score);
            if (idx === -1 || scores.length <= 3 || [2, 3, 4].indexOf(score) === -1) return;
            scores.splice(idx, 1);
            bounds.splice(idx, 1);
            $tr.find('.ipp-range-editor').closest('td').html(
                rangeEditorHtml(bounds, state.mode, $tr.data('row'), ['', '', '', '', ''], scores)
            );
        });
    });

    // Tambah kembali score scale yang dihapus (boundary baru wajib diisi).
    $(document).on('click', '.ipp-range-add', function () {
        let $tr = $(this).closest('tr');
        let state = collectRange($tr);
        if (!state || state.mode === 'equals') return;
        let scores = state.scores.slice();
        let bounds = state.boundaries.slice();
        if (scores.length >= 5) return;
        let missing = [2, 3, 4].filter(function (s) { return scores.indexOf(s) === -1; });
        if (!missing.length) return;
        let score = missing[0];
        let pos = 0;
        while (pos < scores.length && scores[pos] < score) pos++;
        if (pos === 0) pos = 1;
        scores.splice(pos, 0, score);
        bounds.splice(pos - 1, 0, '');
        $tr.find('.ipp-range-editor').closest('td').html(
            rangeEditorHtml(bounds, state.mode, $tr.data('row'), ['', '', '', '', ''], scores)
        );
    });

    // === Mode Custom: +/- input kedua, hapus/tambah baris skor ===
    $(document).on('click', '.ipp-range-cplus', function () {
        let $tr = $(this).closest('tr');
        let score = parseInt($(this).data('score'), 10);
        let state = collectRange($tr);
        let rows = (state && state.customRows ? state.customRows : defaultCustomRows()).map(function (r) {
            if (r.score === score) return { score: r.score, op: '-', v1: r.v1 || '', v2: r.v2 || '' };
            return r;
        });
        renderCustomEditor($tr, rows);
    });

    $(document).on('click', '.ipp-range-cminus', function () {
        let $tr = $(this).closest('tr');
        let score = parseInt($(this).data('score'), 10);
        let state = collectRange($tr);
        let rows = (state && state.customRows ? state.customRows : []).map(function (r) {
            if (r.score === score) return { score: r.score, op: '=', v1: r.v1 || '', v2: '' };
            return r;
        });
        renderCustomEditor($tr, rows);
    });

    $(document).on('click', '.ipp-range-delc', function () {
        let $tr = $(this).closest('tr');
        let score = parseInt($(this).data('score'), 10);
        if ([2, 3, 4].indexOf(score) === -1) return;
        let state = collectRange($tr);
        let rows = (state && state.customRows ? state.customRows : []).filter(function (r) { return r.score !== score; });
        renderCustomEditor($tr, rows);
    });

    $(document).on('click', '.ipp-range-caddscore', function () {
        let $tr = $(this).closest('tr');
        let state = collectRange($tr);
        let rows = (state && state.customRows ? state.customRows.slice() : []);
        let missing = [2, 3, 4].filter(function (s) {
            return !rows.some(function (r) { return r.score === s; });
        });
        if (!missing.length) return;
        rows.push({ score: missing[0], op: '-', v1: '', v2: '' });
        renderCustomEditor($tr, rows);
    });

    $(document).on('click', '.ipp-row-delete', function () {
        let $tr = $(this).closest('tr');
        let row = $tr.data('row');
        let nama = $tr.find('.ipp-detail-kpi').val() || '';
        IPP_SWAL.fire({
            title: 'Hapus KPI ini?',
            text: nama ? 'Baris "' + nama + '" akan dihapus.' : 'Baris ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
        }).then((r) => {
            if (!r.isConfirmed) return;
            parsedData.details.splice(row, 1);
            $tr.remove();
            reindexRows();
        });
    });

    function reindexRows() {
        $('#ippTableBody tr[data-row]').each(function (idx) {
            $(this).attr('data-row', idx);
        });
        updateWeightTotal();
    }

    function updateWeightTotal() {
        let total = 0;
        $('#ippTableBody tr[data-row]').each(function () {
            let $tr = $(this);
            if (!String($tr.find('.ipp-detail-kpi').val() || '').trim()) return;
            total += parseFloat($tr.find('.ipp-detail-weight').val()) || 0;
        });

        let $el = $('#ippWeightTotal');
        if (!$el.length) return;

        let fmtW = function (n) { return String(Math.round(n * 100) / 100); };
        let ok = Math.abs(total - 100) <= 1e-9;
        $el.text('Total Weight: ' + fmtW(total) + '/100 \u2014 weight wajib 100').css('color', ok ? '#1b5e20' : '#c62828');
    }

    $(document).on('input change', '#ippTableBody .ipp-detail-weight, #ippTableBody .ipp-detail-kpi', updateWeightTotal);

    // === Save ===
    $('#btnSaveIpp').on('click', function () {
        if (!parsedData) return;

        // Serialisasi baris detail dari DOM (skip baris dengan KPI kosong)
        let details = [];
        $('#ippTableBody tr[data-row]').each(function () {
            let $tr = $(this);
            let kpi = $tr.find('.ipp-detail-kpi').val() || '';
            if (!kpi.trim()) return;

            let cls = $tr.find('.ipp-detail-classification').val() || '';
            let row = {
                objective: $tr.find('.ipp-detail-objective').val() || '',
                kpa: $tr.find('.ipp-detail-kpa').val() || '',
                kpi: kpi.trim(),
                classification: cls,
                uom: $tr.find('.ipp-detail-uom').val() || '',
                weight: $tr.find('.ipp-detail-weight').val(),
                target: rangeTargetValue($tr),
                due_date: $tr.find('.ipp-detail-due-date').val() || '',
            };

            if (cls === 'Range') {
                let r = collectRange($tr);
                row.target = '';
                if (r && r.mode === 'equals') {
                    if (r.filledCount > 0) {
                        row.range_match_type = 'equals';
                        row.range_labels = r.labels;
                    }
                } else if (r && r.mode === 'custom') {
                    row.range_match_type = 'custom';
                    row.range_tiers = customTiersFromRows(r.customRows || []);
                } else if (r && r.filledCount > 0) {
                    row.range_scores = r.scores;
                    row.range_boundaries = r.boundaries;
                    row.range_direction = r.direction;
                }
            } else {
                row.target = normalizeNumText(row.target);
            }

            details.push(row);
        });

        if (details.length === 0) {
            IPP_SWAL.fire('Peringatan', 'Minimal 1 KPI harus diisi.', 'warning');
            return;
        }

        let clsInvalid = details.findIndex(function (d) {
            return !d.classification || CLASS_OPTIONS.indexOf(d.classification) === -1;
        });
        if (clsInvalid !== -1) {
            IPP_SWAL.fire('Peringatan', 'Classification baris ke-' + (clsInvalid + 1) + ' wajib salah satu dari: High Is Good, High Is Bad, Max, Min, Standard Score, Range.', 'warning');
            return;
        }

        let rangeInvalid = details.findIndex(function (d) {
            if (d.classification !== 'Range' || d.range_match_type !== 'equals') return false;
            let labels = Array.isArray(d.range_labels) ? d.range_labels : [];
            let filled = labels.filter(function (x) { return String(x).trim() !== ''; });
            return filled.length !== 5;
        });
        if (rangeInvalid !== -1) {
            IPP_SWAL.fire('Peringatan', 'Baris ke-' + (rangeInvalid + 1) + ' mode == wajib mengisi 5 label teks.', 'warning');
            return;
        }

        let commaRows = [];
        $('#ippTableBody tr[data-row]').each(function (idx) {
            let $tr = $(this);
            let kpi = $tr.find('.ipp-detail-kpi').val() || '';
            if (!kpi.trim()) return;
            if (($tr.find('.ipp-detail-classification').val() || '') !== 'Range') return;
            if ($tr.find('.ipp-range-label').length) return;
            let hasComma = false;
            $tr.find('.ipp-range-b, .ipp-range-cv').each(function () {
                if (String($(this).val() || '').indexOf(',') !== -1) hasComma = true;
            });
            if (hasComma) commaRows.push(idx + 1);
        });
        if (commaRows.length) {
            let list = commaRows.map(function (n) { return 'ke-' + n; }).join(', ');
            IPP_SWAL.fire('Peringatan', 'Range baris ' + list + ' harus memakai titik (.) sebagai desimal, bukan koma. Contoh: 10.2', 'warning');
            return;
        }

        let wInvalid = details.findIndex(function (d) {
            let w = d.weight;
            return w === '' || w === null || w === undefined || isNaN(parseFloat(w));
        });
        if (wInvalid !== -1) {
            IPP_SWAL.fire('Peringatan', 'Weight baris ke-' + (wInvalid + 1) + ' wajib diisi (angka 0-100).', 'warning');
            return;
        }

        let totalWeight = 0;
        details.forEach(function (d) { totalWeight += parseFloat(d.weight) || 0; });
        if (Math.abs(totalWeight - 100) > 1e-9) {
            let fmtW = function (n) { return String(Math.round(n * 100) / 100); };
            let diffW = Math.round(Math.abs(100 - totalWeight) * 100) / 100;
            let kata = totalWeight > 100 ? 'kelebihan' : 'kekurangan';
            IPP_SWAL.fire('Peringatan', 'Total Weight ' + fmtW(totalWeight) + '% \u2014 ' + kata + ' ' + fmtW(diffW) + '%. Total weight harus 100%.', 'warning');
            return;
        }

        let targetRows = [];
        let targetNonNum = [];
        let rangeOrderRows = [];
        let customErrors = [];
        $('#ippTableBody tr[data-row]').each(function (idx) {
            let $tr = $(this);
            let kpi = $tr.find('.ipp-detail-kpi').val() || '';
            if (!kpi.trim()) return;
            let cls = $tr.find('.ipp-detail-classification').val() || '';
            if (cls === 'Range') {
                if ($tr.find('.ipp-range-label').length) return;
                let r = collectRange($tr);
                if (r && r.mode === 'custom') {
                    let err = customRowsError(r.customRows || []);
                    if (err) customErrors.push({ line: idx + 1, msg: err });
                    return;
                }
                if (!r || r.scores.length < 3) {
                    targetRows.push(idx + 1);
                    return;
                }
                if (r.filledCount !== r.scores.length - 1) {
                    targetRows.push(idx + 1);
                    return;
                }
                let asc = true;
                for (let i = 1; i < r.boundaries.length; i++) {
                    if (numOr(r.boundaries[i]) === null || numOr(r.boundaries[i - 1]) === null
                        || numOr(r.boundaries[i]) <= numOr(r.boundaries[i - 1])) {
                        asc = false;
                        break;
                    }
                }
                if (!asc) rangeOrderRows.push(idx + 1);
            } else {
                let raw = String($tr.find('.ipp-detail-target').val() || '').trim();
                if (raw === '') {
                    targetRows.push(idx + 1);
                } else if (numOr(raw) === null) {
                    targetNonNum.push(idx + 1);
                }
            }
        });
        if (targetRows.length) {
            let list = targetRows.map(function (n) { return 'ke-' + n; }).join(', ');
            IPP_SWAL.fire('Peringatan', 'Target baris ' + list + ' wajib diisi.', 'warning');
            return;
        }
        if (targetNonNum.length) {
            let list = targetNonNum.map(function (n) { return 'ke-' + n; }).join(', ');
            IPP_SWAL.fire('Peringatan', 'Target baris ' + list + ' harus berupa angka (contoh: 10.2).', 'warning');
            return;
        }
        if (rangeOrderRows.length) {
            let list = rangeOrderRows.map(function (n) { return 'ke-' + n; }).join(', ');
            IPP_SWAL.fire('Peringatan', 'Boundary Range baris ' + list + ' harus berurutan menaik (nilai kecil ke besar).', 'warning');
            return;
        }
        if (customErrors.length) {
            IPP_SWAL.fire('Peringatan', 'Baris ke-' + customErrors[0].line + ' (Custom): ' + customErrors[0].msg, 'warning');
            return;
        }

        details.forEach(function (d) {
            if (d.classification !== 'Range') {
                delete d.range_scores;
                delete d.range_boundaries;
                delete d.range_direction;
                delete d.range_match_type;
                delete d.range_labels;
                delete d.range_tiers;
                delete d.range_complete;
            }
        });

        details.forEach((d, idx) => { d.no_urut = idx + 1; });

        let payload = {
            periode: $('#ippPeriode').val(),
            dept: $('#ippDept').val(),
            position: $('#ippPosition').val(),
            golongan: $('#ippGolongan').val(),
            manual: (manualMode && currentMode === 'create') ? 1 : 0,
            nik_peserta: (currentMode === 'create')
                ? $('#ippPesertaBlock .ipp-peserta-chk:checked').map(function () { return this.value; }).get()
                : [],
            pisahkan_template: (currentMode === 'create' && $('#ippPesertaBlock .ipp-pisahkan-template').is(':checked')) ? 1 : 0,
            details: details,
            _token: csrf()
        };

        if (!payload.periode || !payload.periode.trim()) {
            IPP_SWAL.fire('Peringatan', 'Periode Penilaian (Tahun) wajib dipilih.', 'warning');
            return;
        }

        if (manualMode && currentMode === 'create' && snapshotStatus() !== 'ok') {
            IPP_SWAL.fire(
                'Peringatan',
                (snapshotOptions && snapshotOptions.message) ||
                    'Peserta periode ini belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.',
                'warning'
            );
            return;
        }

        let url = "{{ url('/hr/upload-file-template-ipp/store') }}";
        let msg = 'Data IPP akan disimpan.';

        if (currentMode === 'edit' && currentId) {
            url = "{{ url('/hr/upload-file-template-ipp/update') }}/" + currentId;
            msg = 'Data IPP akan diperbarui.';
        }

        IPP_SWAL.fire({
            title: 'Konfirmasi',
            text: msg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: url,
                type: 'POST',
                data: payload,
                success: function (res) {
                    if (res.warnings && res.warnings.length) {
                        let html = escapeHtml(res.message) + '<hr style="margin:.5rem 0;"><div style="text-align:left; font-size:.85rem;">' +
                            res.warnings.map(function (w) { return '&#9888; ' + escapeHtml(w); }).join('<br>') + '</div>';
                        IPP_SWAL.fire({ title: 'Tersimpan dengan catatan', html: html, icon: 'warning' });
                    } else {
                        IPP_SWAL.fire('Berhasil', res.message, 'success');
                    }
                    $('#ippModal').modal('hide');
                    loadList();
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Gagal menyimpan data.';
                    IPP_SWAL.fire('Error', msg, 'error');
                }
            });
        });
    });

    // === List ===
    function loadList() {
        $.get("{{ url('/hr/upload-file-template-ipp/list') }}",
            {
                search: $('#searchInput').val(),
                page: listPage,
                per_page: 10
            },
            function (res) {
                let rows = '';
                if (!res.data || res.data.length === 0) {
                    rows = '<tr><td colspan="10" class="text-center text-muted">Belum ada data IPP.</td></tr>';
                } else {
                    res.data.forEach((r, idx) => {
                        let start = (res.meta.page - 1) * res.meta.per_page + idx + 1;
                        let pesertaHtml;
                        if (r.is_rubric) {
                            pesertaHtml = (parseInt(r.rubrik_peserta || 0, 10) > 0)
                                ? '<span class="ipp-badge ipp-badge-info">' + parseInt(r.rubrik_peserta, 10) + ' peserta &middot; ' +
                                    parseInt(r.rubrik_filled || 0, 10) + '/' + parseInt(r.rubrik_total || 0, 10) + ' nilai</span>'
                                : '<span class="ipp-badge" style="background:#ffe0b2;color:#e65100;">Belum</span>';
                        } else {
                            pesertaHtml = (r.peserta && r.peserta.length)
                                ? r.peserta.map(p => '<div style="font-size:.78rem; white-space:nowrap;">' +
                                    escapeHtml(p.nik) + ' - ' + escapeHtml(p.nama || '-') + '</div>').join('')
                                : '<span class="ipp-badge" style="background:#ffe0b2;color:#e65100;">Belum</span>';
                        }
                        rows += `
                            <tr>
                                <td>${start}</td>
                                <td>${escapeHtml(r.periode)}</td>
                                <td>${escapeHtml(r.dept || '-')}</td>
                                <td>${escapeHtml(r.position || '-')}</td>
                                <td>${escapeHtml(r.golongan || '-')}</td>
                                <td><span class="ipp-badge ipp-badge-info">${r.detail_count} item${r.is_rubric ? ' &middot; Rubrik 2-3' : ''}</span></td>
                                <td>${pesertaHtml}</td>
                                <td>${escapeHtml(r.uploaded_by || r.send_by_username || '-')}</td>
                                <td>${escapeHtml(r.created_at || '')}</td>
                                <td>
                                    ${r.is_rubric ? `<button class="btn btn-sm btn-secondary btn-lihat-rubrik" data-id="${r.id}">Lihat</button>
                                    <button class="btn btn-sm btn-primary btn-edit-rubrik" data-id="${r.id}">Edit</button>`
                                : `<button class="btn btn-sm btn-secondary btn-lihat-ipp" data-id="${r.id}">Lihat</button>
                                    <button class="btn btn-sm btn-primary btn-edit-ipp" data-id="${r.id}">Edit</button>
                                    <button class="btn btn-sm btn-info btn-otorisasi-ipp" data-id="${r.id}" data-golongan="${escapeHtml(r.golongan || '')}" data-periode="${escapeHtml(r.periode || '')}">Peserta</button>`}
                                    <button class="btn btn-sm btn-danger btn-delete-ipp" data-id="${r.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#ippTbody').html(rows);
                renderPagination(res.meta || {});
            }
        );
    }

    function renderPagination(meta) {
        let page = meta.page || 1;
        let lastPage = meta.last_page || 1;
        let total = meta.total || 0;

        if (total === 0) {
            $('#ippPagination').html('');
            return;
        }

        let html = '<nav><ul class="pagination ipp-pagination">';

        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="1">&laquo;</a>
                </li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page - 1}">&lsaquo;</a>
                </li>`;

        let start = Math.max(1, page - 2);
        let end = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}">
                        <a class="page-link" href="javascript:;" data-page="${i}">${i}</a>
                    </li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="javascript:;" data-page="${lastPage}">${lastPage}</a></li>`;
        }

        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${page + 1}">&rsaquo;</a>
                </li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:;" data-page="${lastPage}">&raquo;</a>
                </li>`;
        html += '</ul></nav>';

        $('#ippPagination').html(html);
    }

    $(document).on('click', '#ippPagination .page-link', function () {
        let li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        listPage = parseInt($(this).data('page'));
        loadList();
    });

    $('#formSearch').on('submit', function (e) {
        e.preventDefault();
        listPage = 1;
        loadList();
    });

    // === Edit ===
    $(document).on('click', '.btn-edit-ipp', function () {
        let id = $(this).data('id');
        $.get("{{ url('/hr/upload-file-template-ipp') }}/" + id, function (res) {
            currentMode = 'edit';
            currentId = res.data.id;
            manualMode = false;
            snapshotOptions = null;
            parsedData = res.data;
            openModal('Edit Data IPP', 'Ubah data IPP.');
        }).fail(function () {
            IPP_SWAL.fire('Error', 'Gagal memuat data IPP.', 'error');
        });
    });

    // === Delete ===
    $(document).on('click', '.btn-delete-ipp', function () {
        let id = $(this).data('id');
        IPP_SWAL.fire({
            title: 'Hapus data IPP ini?',
            text: 'Data detail dan target ikut terhapus. Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-ipp/delete') }}/" + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function (res) {
                    IPP_SWAL.fire('Berhasil', res.message, 'success');
                    loadList();
                },
                error: function () {
                    IPP_SWAL.fire('Error', 'Gagal menghapus data.', 'error');
                }
            });
        });
    });

    // === Otorisasi ===
    let otorisasiIppId = null;
    let otorisasiGolongan = '';
    let nikSearchTimeout = {};
    const NIK_EMPTY_MSG = 'Ketik minimal 2 karakter untuk mencari NIK / Nama...';

    // === NIK Dropdown (gaya dropdown hrdashboard) ===
    $(document).on('click', '.ipp-nik-dropdown .hd-ms-btn, .ipp-lokal-dropdown .hd-ms-btn', function (e) {
        e.stopPropagation();
        let $wrap = $(this).closest('.ipp-nik-dropdown, .ipp-lokal-dropdown');
        $('.ipp-nik-dropdown, .ipp-lokal-dropdown').not($wrap).removeClass('open');
        $wrap.toggleClass('open');
        if ($wrap.hasClass('open')) {
            $wrap.find('.hd-ms-search').focus();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.ipp-nik-dropdown, .ipp-lokal-dropdown').length) {
            $('.ipp-nik-dropdown, .ipp-lokal-dropdown').removeClass('open');
        }
    });

    $(document).on('keyup', '.ipp-nik-dropdown .hd-ms-search', function () {
        let $wrap = $(this).closest('.ipp-nik-dropdown');
        let key = $wrap.data('key');
        let q = $(this).val().trim();
        clearTimeout(nikSearchTimeout[key]);
        nikSearchTimeout[key] = setTimeout(function () {
            searchNikDropdown($wrap, q);
        }, 250);
    });

    function searchNikDropdown($wrap, q) {
        let $list = $wrap.find('.hd-ms-list');
        if (q.length < 2 && !$wrap.data('role')) {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + NIK_EMPTY_MSG + '</div>');
            return;
        }
        $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Memuat...</div>');
        let req = { term: q, role: $wrap.data('role') || '' };
        if ($wrap.data('key') === 'ybs' && otorisasiIppPeriode) req.periode = otorisasiIppPeriode;
        $.get("{{ url('/hr/upload-file-template-ipp/otorisasi/search-nik') }}", req, function (res) {
            if (!$wrap.hasClass('open')) return;
            if (res.has_snapshot === false) {
                $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + escapeHtml(res.message || 'Peserta periode ini belum di-capture.') + '</div>');
                return;
            }
            let items = res.data || [];
            if (items.length === 0) {
                if ($wrap.data('role') === 'associate') {
                    $list.html('<div class="hd-ms-item ipp-nik-item" data-nik="" data-nama="">Tidak Ada</div><div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ditemukan.</div>');
                } else {
                    $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ditemukan.</div>');
                }
                return;
            }
            let html = '';
            if ($wrap.data('role') === 'associate') {
                html += '<div class="hd-ms-item ipp-nik-item" data-nik="" data-nama="">Tidak Ada</div>';
            }
            items.forEach(u => {
                html += '<div class="hd-ms-item ipp-nik-item" data-nik="' + escapeHtml(u.nik) + '" data-nama="' + escapeHtml(u.name) + '">';
                html += '<span><strong>' + escapeHtml(u.nik) + '</strong> - ' + escapeHtml(u.name) + '</span>';
                html += '</div>';
            });
            $list.html(html);
        }).fail(function () {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Gagal memuat data.</div>');
        });
    }

    function setNikValue(key, nik, nama) {
        let $wrap = $('.ipp-nik-dropdown[data-key="' + key + '"]');
        let isEmpty = !nik || nik === '';
        $wrap.find('.ipp-nik-value').val(isEmpty ? '' : nik);
        $wrap.find('.hd-ms-label').text(isEmpty ? 'Tidak Ada' : (nik + ' - ' + nama));
        $wrap.find('.hd-ms-search').val('');
        $wrap.find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + NIK_EMPTY_MSG + '</div>');
        $wrap.removeClass('open');
        let nameMap = { ybs: 'nikYbsName', manager: 'nikManagerName', associate: 'nikAssocManagerName', 'map-manager': 'nikMapManagerName' };
        let $nm = $('#' + nameMap[key]);
        if ($nm.length) $nm.text(isEmpty ? '' : nama);
    }

    $(document).on('click', '.ipp-nik-item', function () {
        let $wrap = $(this).closest('.ipp-nik-dropdown');
        let key = $wrap.data('key');
        setNikValue(key, $(this).data('nik'), $(this).data('nama'));
        if (key === 'ybs' && $(this).data('nik')) {
            loadOtorisasiPeserta($(this).data('nik'));
        }
    });

    function setNikReadonly(key, readonly) {
        let $wrap = $('.ipp-nik-dropdown[data-key="' + key + '"]');
        $wrap.toggleClass('ipp-nik-readonly', !!readonly);
        $wrap.find('.hd-ms-btn').prop('disabled', !!readonly);
        $wrap.find('.hd-ms-search').prop('disabled', !!readonly);
    }

    function applySnapshotAtasan(p) {
        let mgrNik = p.manager_nik || '';
        let amNik = p.associate_manager_nik || '';
        let level = parseInt(p.level || 0, 10);
        let needsAm = (level >= 3 && level <= 5);

        if (mgrNik) {
            setNikValue('manager', mgrNik, p.manager_nama || '');
            setNikReadonly('manager', true);
        } else {
            setNikValue('manager', '', '');
            setNikReadonly('manager', false);
        }

        if (needsAm) {
            $('#wrapAssocManager').show();
            if (amNik) {
                setNikValue('associate', amNik, p.associate_manager_nama || '');
                setNikReadonly('associate', true);
            } else {
                setNikValue('associate', '', '');
                setNikReadonly('associate', false);
            }
        } else {
            $('#wrapAssocManager').hide();
            setNikValue('associate', '', '');
            setNikReadonly('associate', false);
        }
    }

    function loadOtorisasiPeserta(nik) {
        if (!otorisasiIppPeriode || !nik) return;
        $.get("{{ url('/hr/upload-file-template-ipp/otorisasi/peserta') }}", { periode: otorisasiIppPeriode, nik: nik }, function (res) {
            if (!res.found) {
                IPP_SWAL.fire('Peringatan', res.message || ('NIK ' + nik + ' tidak terdaftar pada snapshot periode ini.'), 'warning');
                setNikValue('ybs', '', '');
                setNikReadonly('manager', false);
                setNikReadonly('associate', false);
                return;
            }
            applySnapshotAtasan(res.peserta || {});
        }).fail(function () {
            IPP_SWAL.fire('Error', 'Gagal memuat data peserta dari snapshot.', 'error');
        });
    }

    function checkOtorisasiSnapshot() {
        if (!otorisasiIppPeriode) return;
        $.get("{{ url('/hr/upload-file-template-ipp/snapshot-options') }}", { periode: otorisasiIppPeriode }, function (res) {
            let ok = !!res.has_snapshot;
            $('#otorisasiSnapshotWarn').html(ok ? '' : '<div class="alert alert-warning" style="font-size:.82rem; margin-bottom:0;">' + escapeHtml(res.message || 'Peserta periode ini belum di-capture.') + '</div>');
            $('#otorisasiFormArea').toggle(ok);
        });
    }

    function applyAtasanManualVisibility() {
        let g = String(otorisasiGolongan || '').trim();
        let isLv35 = (g === '3' || g === '4' || g === '5');
        $('#wrapAssocManager').toggle(isLv35);
    }

    $(document).on('click', '.btn-otorisasi-ipp', function () {
        otorisasiIppId = $(this).data('id');
        otorisasiGolongan = String($(this).data('golongan') || '').trim();
        otorisasiIppPeriode = String($(this).data('periode') || '').trim();
        resetOtorisasiForm();
        applyAtasanManualVisibility();
        checkOtorisasiSnapshot();
        loadOtorisasi();
        $('#otorisasiModal').modal('show');
    });

    function resetOtorisasiForm() {
        $('#otorisasiEditId').val('');
        $('#otorisasiFormTitle').text('Tambah Peserta');
        $('#btnSimpanOtorisasi').text('Tambah Peserta');
        $('.ipp-nik-dropdown').each(function () {
            $(this).find('.ipp-nik-value').val('');
            $(this).find('.hd-ms-label').text($(this).find('.hd-ms-label').data('placeholder') || 'Masukkan NIK');
            $(this).find('.hd-ms-search').val('');
            $(this).find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + NIK_EMPTY_MSG + '</div>');
            $(this).removeClass('open');
        });
        $('.ipp-nik-dropdown').removeClass('ipp-nik-readonly').find('.hd-ms-btn, .hd-ms-search').prop('disabled', false);
        $('#wrapAssocManager').show();
        $('#nikYbsName').text('');
        $('#nikManagerName').text('');
        $('#nikAssocManagerName').text('');
    }

    function loadOtorisasi() {
        $.get("{{ url('/hr/upload-file-template-ipp/otorisasi') }}/" + otorisasiIppId, function (res) {
            $('#otorisasiInfo').html('<strong>IPP ID:</strong> ' + otorisasiIppId);
            let rows = '';
            if (!res.data || res.data.length === 0) {
                rows = '<tr><td colspan="7" class="text-center text-muted">Belum ada data peserta.</td></tr>';
            } else {
                res.data.forEach(o => {
                    rows += `
                        <tr>
                            <td>${escapeHtml(o.nik_yang_bersangkutan)}</td>
                            <td>${escapeHtml(o.nama_yang_bersangkutan || '-')}</td>
                            <td>${escapeHtml(o.nik_manager || '-')}</td>
                            <td>${escapeHtml(o.nama_manager || '-')}</td>
                            <td>${escapeHtml(o.nik_associate_manager || '-')}</td>
                            <td>${escapeHtml(o.nama_associate_manager || '-')}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-otorisasi-edit" data-id="${o.id}" data-nik-ybs="${escapeHtml(o.nik_yang_bersangkutan)}" data-nama-ybs="${escapeHtml(o.nama_yang_bersangkutan || '')}" data-nik-manager="${escapeHtml(o.nik_manager || '')}" data-nama-manager="${escapeHtml(o.nama_manager || '')}" data-nik-assoc="${escapeHtml(o.nik_associate_manager || '')}" data-nama-assoc="${escapeHtml(o.nama_associate_manager || '')}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-otorisasi-delete" data-id="${o.id}">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#otorisasiTbody').html(rows);
        }).fail(function () {
            IPP_SWAL.fire('Error', 'Gagal memuat data peserta.', 'error');
        });
    }

    $(document).on('click', '.btn-otorisasi-edit', function () {
        let $btn = $(this);
        $('#otorisasiEditId').val($btn.data('id'));
        $('#otorisasiFormTitle').text('Edit Peserta');
        $('#btnSimpanOtorisasi').text('Simpan Perubahan');
        $('#nikYbsName').text('');
        $('#nikManagerName').text('');
        $('#nikAssocManagerName').text('');
        setNikValue('ybs', $btn.data('nik-ybs'), $btn.data('nama-ybs'));
        setNikValue('manager', $btn.data('nik-manager'), $btn.data('nama-manager'));
        setNikValue('associate', $btn.data('nik-assoc'), $btn.data('nama-assoc'));
        setNikReadonly('manager', false);
        setNikReadonly('associate', false);
        let g = String(otorisasiGolongan || '').trim();
        $('#wrapAssocManager').toggle(g === '3' || g === '4' || g === '5');
    });

    $(document).on('click', '.btn-otorisasi-delete', function () {
        let id = $(this).data('id');
        IPP_SWAL.fire({
            title: 'Hapus otorisasi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/upload-file-template-ipp/otorisasi/delete') }}/" + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function (res) {
                    IPP_SWAL.fire('Berhasil', res.message, 'success');
                    loadOtorisasi();
                },
                error: function () {
                    IPP_SWAL.fire('Error', 'Gagal menghapus otorisasi.', 'error');
                }
            });
        });
    });

    $('#btnSimpanOtorisasi').on('click', function () {
        let nikYbs = $('#nikYbs').val() || '';
        let nikManager = $('#nikManager').val() || '';
        let nikAssoc = $('#nikAssocManager').val() || '';
        let editId = $('#otorisasiEditId').val();

        if (!nikYbs.trim()) {
            IPP_SWAL.fire('Peringatan', 'NIK peserta wajib diisi.', 'warning');
            return;
        }
        if (!nikManager.trim()) {
            IPP_SWAL.fire('Peringatan', 'NIK manager wajib diisi.', 'warning');
            return;
        }

        let url = editId
            ? "{{ url('/hr/upload-file-template-ipp/otorisasi/update') }}/" + editId
            : "{{ url('/hr/upload-file-template-ipp/otorisasi') }}/" + otorisasiIppId;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                nik_yang_bersangkutan: nikYbs,
                nik_manager: nikManager,
                nik_associate_manager: nikAssoc,
                _token: csrf()
            },
            success: function (res) {
                IPP_SWAL.fire('Berhasil', res.message, 'success');
                resetOtorisasiForm();
                applyAtasanManualVisibility();
                loadOtorisasi();
                loadList();
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Gagal menyimpan peserta.';
                IPP_SWAL.fire('Error', msg, 'error');
            }
        });
    });

    // === Lihat Jawaban Aktual ===
    let lihatIppData = null;
    let lihatOtoId = null;
    let lihatTahapan = null;

    function openLihat(id) {
        $.get("{{ url('/hr/upload-file-template-ipp/aktual') }}/" + id, function (res) {
            lihatIppData = res.data;
            lihatOtoId = (res.data.otorisasi || [])[0]?.id ?? null;
            lihatTahapan = (res.data.tahapan || [])[0] ?? null;
            $('#lihatModalTitle').text('Jawaban Aktual IPP - ' + escapeHtml(res.data.periode || ''));
            renderLihatInfo(res.data);
            renderLihatKaryawan(res.data);
            renderLihatTahapan(res.data);
            renderLihatTable(res.data);
            $('#lihatModal').modal('show');
        }).fail(function (xhr) {
            IPP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memuat jawaban Aktual.', 'error');
        });
    }

    function renderLihatInfo(d) {
        let totalSel = (d.tahapan || []).length * (d.details || []).length;
        let terisi = 0;
        (d.otorisasi || []).forEach(function (o) {
            let m = d.aktual[o.id] || {};
            Object.keys(m).forEach(function (detailId) {
                Object.keys(m[detailId]).forEach(function (tahapan) {
                    let v = m[detailId][tahapan];
                    if (v !== null && v !== undefined && v !== '') terisi++;
                });
            });
        });

        let oto = (d.otorisasi || []).find(function (x) { return x.id === lihatOtoId; });
        let atasanLine = '';
        if (oto) {
            if (oto.nama_manager || oto.nik_manager) {
                atasanLine += ' &middot; <strong>Manager:</strong> ' + escapeHtml(oto.nama_manager || oto.nik_manager);
            }
            if (oto.nama_associate_manager || oto.nik_associate_manager) {
                atasanLine += ' &middot; <strong>Assoc. Manager:</strong> ' + escapeHtml(oto.nama_associate_manager || oto.nik_associate_manager);
            }
        }
        let ybsLine = oto
            ? ' &middot; <strong>Peserta:</strong> ' + escapeHtml(oto.nama_yang_bersangkutan || '') +
              ' (' + escapeHtml(oto.nik_yang_bersangkutan || '') + ')' + atasanLine
            : '';

        $('#lihatInfo').html(
            '<strong>Periode:</strong> ' + escapeHtml(d.periode || '-') +
            ' &middot; <strong>Dept:</strong> ' + escapeHtml(d.dept || '-') +
            ' &middot; <strong>POSITION:</strong> ' + escapeHtml(d.position || '-') +
            ' &middot; <strong>GOLONGAN:</strong> ' + escapeHtml(d.golongan || '-') +
            ' &middot; <strong>Karyawan:</strong> ' + (d.otorisasi || []).length + ' orang' +
            ybsLine +
            '<br><span style="font-size:.78rem;">Total sel yang terisi: ' + terisi + ' dari ' + (totalSel * (d.otorisasi || []).length) + '</span>'
        );
    }

    function buildLokalDropdown(kind, label, items) {
        let listHtml = items.length
            ? items.map(function (it) {
                return '<div class="hd-ms-item ipp-lokal-item" data-kind="' + kind +
                    '" data-value="' + it.value +
                    '" data-label="' + escapeHtml(it.label) +
                    '" data-search="' + escapeHtml(it.search.toLowerCase()) + '">' + it.html + '</div>';
            }).join('')
            : '<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ada data.</div>';
        return '<div class="ipp-lokal-dropdown" data-kind="' + kind + '">' +
            '<button type="button" class="hd-ms-btn"><span class="hd-ms-label">' + label + '</span><span class="hd-ms-caret">▾</span></button>' +
            '<div class="hd-ms-dropdown">' +
            '<input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Cari...">' +
            '<div class="hd-ms-list">' + listHtml + '</div>' +
            '</div>' +
            '<input type="hidden" class="ipp-lokal-value"' + (kind === 'position' ? ' id="ippPosition"' : '') + ' value="">' +
            '</div>';
    }

    function renderLihatKaryawan(d) {
        let total = (d.tahapan || []).length * (d.details || []).length;
        let items = (d.otorisasi || []).map(function (o) {
            let filled = 0;
            let m = d.aktual[o.id] || {};
            Object.keys(m).forEach(function (detailId) {
                Object.keys(m[detailId]).forEach(function (tahapan) {
                    let v = m[detailId][tahapan];
                    if (v !== null && v !== undefined && v !== '') filled++;
                });
            });
            let badge = filled >= total
                ? ' style="background:#c8e6c9; color:#1b5e20;"'
                : (filled > 0 ? ' style="background:#fff3cd; color:#8a6d1c;"' : '');
            return {
                value: o.id,
                label: (o.nik_yang_bersangkutan || '') + ' - ' + (o.nama_yang_bersangkutan || ''),
                search: (o.nik_yang_bersangkutan || '') + ' ' + (o.nama_yang_bersangkutan || ''),
                html: '<span><strong>' + escapeHtml(o.nik_yang_bersangkutan) + '</strong> - ' + escapeHtml(o.nama_yang_bersangkutan || '') + '</span>' +
                    ' <span class="badge ml-1"' + badge + '>' + filled + '/' + total + '</span>'
            };
        });

        let current = (d.otorisasi || []).find(function (x) { return x.id === lihatOtoId; });
        let label = current
            ? escapeHtml(current.nik_yang_bersangkutan) + ' - ' + escapeHtml(current.nama_yang_bersangkutan || '')
            : 'Pilih Karyawan...';

        $('#lihatKaryawanPicker').html(
            '<div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem; font-weight:600; white-space:nowrap;">Pilih Karyawan:</span>' +
            buildLokalDropdown('karyawan', label, items) +
            '</div>'
        );
    }

    function renderLihatTahapan(d) {
        let items = (d.tahapan || []).map(function (t) {
            return {
                value: t,
                label: tahapanLabel(t),
                search: tahapanLabel(t),
                html: '<span>' + tahapanLabel(t) + '</span>'
            };
        });

        let label = lihatTahapan ? tahapanLabel(lihatTahapan) : 'Pilih Tahapan...';

        $('#lihatTahapanPicker').html(
            '<div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem; font-weight:600; white-space:nowrap;">Pilih Tahapan:</span>' +
            buildLokalDropdown('tahapan', label, items) +
            '</div>'
        );
    }

    function renderLihatTable(d) {
        let otoId = lihatOtoId;
        let tahapan = lihatTahapan;
        if (!otoId || !tahapan) {
            $('#lihatTableHead').html('');
            $('#lihatTableBody').html('');
            return;
        }

        let aktual = d.aktual[otoId] || {};
        let aktualVarchar = (d.aktual_varchar || {})[otoId] || {};
        let aktualAtasan = (d.aktual_atasan || {})[otoId] || {};
        let aktualAtasanVarchar = (d.aktual_atasan_varchar || {})[otoId] || {};
        let aktualAtasanBy = (d.aktual_atasan_by || {})[otoId] || {};
        let aktualDepartemen = (d.aktual_departemen || {})[otoId] || {};
        let aktualDepartemenVarchar = (d.aktual_departemen_varchar || {})[otoId] || {};
        let aktualDepartemenBy = (d.aktual_departemen_by || {})[otoId] || {};
        let achievement = (d.achievement_category || {})[otoId] || {};

        let thead = '<tr>' +
            '<th style="min-width:40px;">#</th>' +
            '<th style="min-width:150px;">Objective</th>' +
            '<th style="min-width:150px;">Key Performance Area (KPA)</th>' +
            '<th style="min-width:190px;">Key Performance Indicator (KPI)</th>' +
            '<th style="min-width:110px;">Classification</th>' +
            '<th style="min-width:90px;">Unit Of Measurement</th>' +
            '<th class="text-center" style="min-width:70px;">Weight</th>' +
            '<th class="text-center" style="min-width:70px;">Target</th>' +
            '<th style="min-width:110px;">Due date</th>' +
            '<th class="text-center ipp-month-head" style="min-width:150px;">' + escapeHtml(tahapanLabel(tahapan)) + ' Aktual (Departemen)</th>' +
            '<th class="text-center ipp-month-head" style="min-width:150px;">' + escapeHtml(tahapanLabel(tahapan)) + ' Aktual (Karyawan)</th>' +
            '<th class="text-center ipp-month-head" style="min-width:160px;">' + escapeHtml(tahapanLabel(tahapan)) + ' Aktual (Atasan)</th>' +
            '<th class="text-center" style="min-width:110px;">Achievement</th>' +
            '</tr>';
        $('#lihatTableHead').html(thead);

        let tbody = '';
        (d.details || []).forEach(function (dtl) {
            let textRange = isTextRange(dtl);
            let val = aktual[dtl.id] ? aktual[dtl.id][tahapan] : null;
            let valVarchar = aktualVarchar[dtl.id] ? aktualVarchar[dtl.id][tahapan] : null;
            let valAtasan = aktualAtasan[dtl.id] ? aktualAtasan[dtl.id][tahapan] : null;
            let valAtasanVarchar = aktualAtasanVarchar[dtl.id] ? aktualAtasanVarchar[dtl.id][tahapan] : null;
            let byAtasan = aktualAtasanBy[dtl.id] ? aktualAtasanBy[dtl.id][tahapan] : '';
            let valDepartemen = aktualDepartemen[dtl.id] ? aktualDepartemen[dtl.id][tahapan] : null;
            let valDepartemenVarchar = aktualDepartemenVarchar[dtl.id] ? aktualDepartemenVarchar[dtl.id][tahapan] : null;
            let byDepartemen = aktualDepartemenBy[dtl.id] ? aktualDepartemenBy[dtl.id][tahapan] : '';
            if (textRange) {
                val = valVarchar;
                valAtasan = valAtasanVarchar;
                valDepartemen = valDepartemenVarchar;
            }
            let ach = achievement[dtl.id] ? achievement[dtl.id][tahapan] : '';
            let tText = dtl.target !== null && dtl.target !== '' ? dtl.target : '-';
            let wText = (dtl.weight !== null && dtl.weight !== undefined && dtl.weight !== '') ? dtl.weight : '-';
            let aText = (val !== null && val !== undefined && val !== '') ? val : '<span class="text-muted">-</span>';
            let aAtasanText = (valAtasan !== null && valAtasan !== undefined && valAtasan !== '') ? valAtasan : '<span class="text-muted">-</span>';
            let aDepartemenText = (valDepartemen !== null && valDepartemen !== undefined && valDepartemen !== '') ? valDepartemen : '<span class="text-muted">-</span>';
            tbody += '<tr>' +
                '<td class="text-center">' + dtl.no_urut + '</td>' +
                '<td>' + escapeHtml(dtl.objective || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpa || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpi) + '</td>' +
                '<td>' + escapeHtml(dtl.classification || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.uom || '-') + '</td>' +
                '<td class="text-center">' + escapeHtml(wText) + '</td>' +
                '<td class="text-center">' + rangeScaleHtml(dtl, tText) + '</td>' +
                '<td>' + escapeHtml(dtl.due_date || '-') + '</td>' +
                '<td style="text-align:right; font-weight:600;">' + aDepartemenText +
                (byDepartemen ? '<div class="text-muted" style="font-size:.68rem; font-weight:400;">' + escapeHtml(byDepartemen) + '</div>' : '') +
                '</td>' +
                '<td style="text-align:right; font-weight:600;">' + aText + '</td>' +
                '<td style="text-align:right; font-weight:600;">' + aAtasanText +
                (byAtasan ? '<div class="text-muted" style="font-size:.68rem; font-weight:400;">' + escapeHtml(byAtasan) + '</div>' : '') +
                '</td>' +
                '<td class="text-center">' + achievementBadge(ach) + '</td>' +
                '</tr>';
        });
        $('#lihatTableBody').html(tbody);
    }

    $(document).on('click', '.btn-lihat-ipp', function () {
        openLihat($(this).data('id'));
    });

    function syncGolonganByPosition(force) {
        let pos = $.trim($('#ippPosition').val() || '');
        if (pos === '') {
            $('#ippGolongan').val('');
            return;
        }
        if (!force && $('#ippGolongan').val() !== '') return;
        if (manualMode && snapshotStatus() === 'ok') {
            let lv = snapshotLevelForPosition(pos, $.trim($('#ippDept').val() || ''));
            let allowed = (snapshotOptions.levels || []).map(String);
            $('#ippGolongan').val(lv && allowed.indexOf(String(lv)) !== -1 ? String(lv) : '');
            return;
        }
        $.get("{{ url('/hr/upload-file-template-ipp/position-level') }}", { position: pos }, function (res) {
            let lv = res.level;
            $('#ippGolongan').val(lv && GOLONGAN_OPTIONS.indexOf(String(lv)) !== -1 ? String(lv) : '');
        });
    }

    $(document).on('click', '.ipp-lokal-item', function () {
        let $wrap = $(this).closest('.ipp-lokal-dropdown');
        let kind = $wrap.data('kind');
        if (kind === 'karyawan') {
            lihatOtoId = $(this).data('value');
            renderLihatInfo(lihatIppData);
            renderLihatKaryawan(lihatIppData);
            renderLihatTable(lihatIppData);
        } else if (kind === 'tahapan') {
            lihatTahapan = $(this).data('value');
            renderLihatTahapan(lihatIppData);
            renderLihatTable(lihatIppData);
        } else if (kind === 'position') {
            $('#ippPosition').val($(this).data('value'));
            if (parsedData) parsedData.position = String($(this).data('value') || '');
            syncGolonganByPosition(true);
            refreshPesertaBlock();
        }
        $wrap.find('.hd-ms-label').text($(this).data('label'));
        $wrap.removeClass('open');
    });

    $(document).on('keyup', '.ipp-lokal-dropdown .hd-ms-search', function () {
        let $wrap = $(this).closest('.ipp-lokal-dropdown');
        let q = ($(this).val() || '').toLowerCase().trim();
        let $list = $wrap.find('.hd-ms-list');
        let shown = 0;
        $list.find('.ipp-lokal-item').each(function () {
            let show = String($(this).data('search') || '').indexOf(q) !== -1;
            $(this).toggle(show);
            if (show) shown++;
        });
        let $empty = $list.find('.hd-ms-empty');
        if (!shown) {
            if (!$empty.length) {
                $list.append('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ditemukan.</div>');
            } else {
                $empty.show();
            }
        } else if ($empty.length) {
            $empty.hide();
        }
    });

    // === Mapping Manager (Sub Dept) ===
    function loadMapping(tbody, withAction) {
        tbody = tbody || '#mappingTbody';
        withAction = !!withAction;
        let cols = withAction ? 4 : 3;
        $(tbody).html('<tr><td colspan="' + cols + '" class="text-center text-muted">Memuat...</td></tr>');
        $.get("{{ url('/hr/upload-file-template-ipp/mapping') }}", function (res) {
            let rows = res.data || [];
            if (!rows.length) {
                $(tbody).html('<tr><td colspan="' + cols + '" class="text-center text-muted">Belum ada mapping.</td></tr>');
                return;
            }
            let html = '';
            rows.forEach(m => {
                html += '<tr>' +
                    '<td>' + escapeHtml(m.sub_departmen) + '</td>' +
                    '<td>' + escapeHtml(m.nik_manager || '-') + '</td>' +
                    '<td>' + escapeHtml(m.nama_manager || '-') + '</td>';
                if (withAction) {
                    html += '<td class="text-center"><button class="btn btn-sm btn-danger btn-mapping-delete" data-id="' + m.id + '">Hapus</button></td>';
                }
                html += '</tr>';
            });
            $(tbody).html(html);
        }).fail(function () {
            $(tbody).html('<tr><td colspan="' + cols + '" class="text-center text-muted">Gagal memuat.</td></tr>');
        });
    }

    function refreshAllMapping() {
        loadMapping('#mappingTbody', true);
    }

    function resetMappingForm() {
        $('#mapSubDept').val('');
        $('#nikMapManager').val('');
        $('#nikMapManagerName').text('');
        let $wrap = $('.ipp-nik-dropdown[data-key="map-manager"]');
        $wrap.find('.hd-ms-label').text($wrap.find('.hd-ms-label').data('placeholder') || 'Masukkan NIK');
        $wrap.find('.hd-ms-search').val('');
        $wrap.find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Pilih dari daftar, atau ketik NIK / Nama...</div>');
        $wrap.removeClass('open');
    }

    $(document).on('click', '#btnOpenMapping, #btnOpenMappingHeader', function () {
        $('#mapSubDept').empty();
        $('#mapSubDept').append('<option value="">— Pilih Sub Departmen —</option>');
        SUB_DEPT_OPTIONS.forEach(function (s) {
            $('#mapSubDept').append('<option value="' + escapeHtml(s) + '">' + escapeHtml(s) + '</option>');
        });
        resetMappingForm();
        loadMapping('#mappingTbody', true);
        $('#mappingModal').modal('show');
    });

    $(document).on('click', '#btnSaveMapping', function () {
        let sub = $('#mapSubDept').val();
        let nik = $('#nikMapManager').val() || '';
        if (!sub) {
            IPP_SWAL.fire('Error', 'Pilih Sub Departmen terlebih dahulu.', 'error');
            return;
        }
        if (!nik) {
            IPP_SWAL.fire('Error', 'Pilih NIK Manager.', 'error');
            return;
        }
        $.ajax({
            url: "{{ url('/hr/upload-file-template-ipp/mapping') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { sub_departmen: sub, nik_manager: nik },
            success: function () {
                IPP_SWAL.fire('Sukses', 'Mapping berhasil disimpan.', 'success');
                resetMappingForm();
                refreshAllMapping();
            },
            error: function (xhr) {
                IPP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan mapping.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-mapping-delete', function () {
        let id = $(this).data('id');
        IPP_SWAL.fire({
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
                    IPP_SWAL.fire('Sukses', 'Mapping dihapus.', 'success');
                    refreshAllMapping();
                },
                error: function () {
                    IPP_SWAL.fire('Error', 'Gagal menghapus mapping.', 'error');
                }
            });
        });
    });

    // === DAFTAR PESERTA (mapping PK) ===
    let prtPage = 1;
    let prtPerPage = 10;

    function levelBadge(lg) {
        if (lg === '4-5') return '<span class="pk-badge pk-badge-info">Level 4–5</span>';
        if (lg === '6-7') return '<span class="pk-badge pk-badge-success">Level 6–7</span>';
        return '<span class="pk-badge pk-badge-warn">Semua Level</span>';
    }

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

    $('#btnOpenAmMapping').on('click', function () {
        if (amMapSelPeserta) amMapSelPeserta.clear();
        if (amMapSelAm) amMapSelAm.clear();
        $('#pkAmMapSearchPeserta, #pkAmMapSearchAm').val('');
        loadPkAmMapping();
        $('#pkAmMappingModal').modal('show');
    });

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
            IPP_SWAL.fire('Error', 'Pilih Peserta terlebih dahulu.', 'error');
            return;
        }
        if (!nikAm) {
            IPP_SWAL.fire('Error', 'Pilih Associate Manager terlebih dahulu.', 'error');
            return;
        }
        $.ajax({
            url: "{{ url('/hr/upload-file-template-pk/am-mapping') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { nik: nik, nik_associate_manager: nikAm },
            success: function () {
                IPP_SWAL.fire('Sukses', 'Mapping Associate Manager berhasil disimpan.', 'success');
                if (amMapSelPeserta) amMapSelPeserta.clear();
                if (amMapSelAm) amMapSelAm.clear();
                loadPkAmMapping();
                loadPrtList();
            },
            error: function (xhr) {
                IPP_SWAL.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan mapping.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-pk-am-mapping-delete', function () {
        let id = $(this).data('id');
        IPP_SWAL.fire({
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
                    IPP_SWAL.fire('Sukses', 'Mapping Associate Manager dihapus.', 'success');
                    loadPkAmMapping();
                    loadPrtList();
                },
                error: function () {
                    IPP_SWAL.fire('Error', 'Gagal menghapus mapping.', 'error');
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
                html = '<tr><td colspan="12" class="text-center text-muted">Tidak ada data history.</td></tr>';
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
                    '</tr>';
            });
            $('#prtHistTbody').html(html);
            renderPrtHistPagination(res.meta || {});
        }).fail(function () {
            $('#prtHistTbody').html('<tr><td colspan="12" class="text-center text-muted">Gagal memuat history.</td></tr>');
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

    // initial load
    loadList();
    loadPrtList();
    loadPrtHistList();
</script>
@endpush
