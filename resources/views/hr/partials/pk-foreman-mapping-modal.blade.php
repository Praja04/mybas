<style>
    .foreman-nik-dropdown { position: relative; }
    .foreman-nik-dropdown .hd-ms-btn {
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
    .foreman-nik-dropdown .hd-ms-btn:hover { border-color: #017b8f; }
    .foreman-nik-dropdown .hd-ms-caret { font-size: .9rem; color: #017b8f; }
    .foreman-nik-dropdown .hd-ms-dropdown {
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
    .foreman-nik-dropdown.open .hd-ms-dropdown { display: flex; }
    .foreman-nik-dropdown .hd-ms-search { margin: 6px; }
    .foreman-nik-dropdown .hd-ms-list { overflow-y: auto; max-height: 240px; padding: 4px 0; }
    .foreman-nik-dropdown .hd-ms-item {
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
    .foreman-nik-dropdown .hd-ms-item:hover { background: #e0f7fa; }
</style>

<div class="modal fade" id="foremanMappingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#017b8f; font-weight:700;">Mapping Foreman (Peserta PK)</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted" style="font-size:.82rem;">
                    Daftarkan peserta PK <strong>Level 2–3</strong> (tidak wajib punya akun) beserta Manager dan Associate Manager.
                    Grup penilaian peserta ini <strong>tetap Level 2–3</strong> (tidak memakai rubrik Level 4–5), dan mapping ini
                    diprioritaskan di atas Mapping Sub Dept / kandidat otomatis.
                </p>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
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
                        <tbody id="foremanTbody">
                            <tr><td colspan="7" class="text-center text-muted">Belum ada data peserta.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3" style="background:#f5f5f5; border-radius:6px;">
                    <h6 style="font-weight:700; color:#017b8f;" id="foremanFormTitle">Tambah Peserta</h6>
                    <input type="hidden" id="foremanEditId" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-weight:600;">NIK Peserta <span class="text-muted" style="font-weight:400;">(Level 2–3)</span>:</label>
                            <div class="foreman-nik-dropdown" data-key="foreman_ybs">
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
                                <input type="hidden" class="foreman-nik-value" id="foremanNikYbs" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="foremanNikYbsName"></div>
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight:600;">NIK Manager:</label>
                            <div class="foreman-nik-dropdown" data-key="foreman_manager" data-role="manager">
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
                                <input type="hidden" class="foreman-nik-value" id="foremanNikManager" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="foremanNikManagerName"></div>
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight:600;">NIK Associate Manager:</label>
                            <div class="foreman-nik-dropdown" data-key="foreman_associate" data-role="associate">
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
                                <input type="hidden" class="foreman-nik-value" id="foremanNikAssocManager" value="">
                            </div>
                            <div class="mt-1" style="font-size:.8rem; color:#1b5e20; font-weight:600;" id="foremanNikAssocManagerName"></div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnSimpanForeman">Tambah Peserta</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ml-2" id="btnResetForeman">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
