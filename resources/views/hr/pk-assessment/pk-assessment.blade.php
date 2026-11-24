@extends('layouts.base')

@section('content')
<style>
    .pk-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1.25rem; margin-bottom: 1.25rem; }
    .pk-card h5 { color: #a80000; font-weight: 700; }
    .pk-table th { background: #fdecea; color: #a80000; font-size: .82rem; vertical-align: middle; white-space: nowrap; }
    .pk-table td { font-size: .83rem; vertical-align: middle; }
    .pk-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: .72rem; font-weight: 600; }
    .pk-badge-success { background: #e8f5e9; color: #1b5e20; }
    .pk-badge-warn { background: #fff3e0; color: #e65100; }
    .pk-badge-info { background: #e3f2fd; color: #0d47a1; }
    .pk-badge-mute { background: #eceff1; color: #455a64; }
    .pk-sec-row td { background: #a80000; color: #fff; font-weight: 700; }
    .pk-cat-row td { background: #f7c9c9; color: #a80000; font-weight: 600; }
    #pkRubricHead th { position: sticky; top: 0; z-index: 3; background: #a80000 !important; color: #fff !important; }
    .pk-rubric-table { border-collapse: separate !important; border-spacing: 0; table-layout: fixed; }
    .pk-rubric-table th, .pk-rubric-table td { border-width: 0 1px 1px 0; word-wrap: break-word; overflow-wrap: anywhere; }
    .pk-rubric-table tr:first-child > th { border-top-width: 1px; }
    .pk-rubric-table tr > *:first-child { border-left-width: 1px; }
    @media (min-width: 992px) {
        .pk-rubric-table td:nth-child(1),
        .pk-rubric-table td:nth-child(2),
        .pk-rubric-table td:nth-child(3) { position: sticky; z-index: 2; background: #fff; }
        .pk-rubric-table td:nth-child(1) { left: 0; }
        .pk-rubric-table td:nth-child(2) { left: 48px; }
        .pk-rubric-table td:nth-child(3) { left: 168px; box-shadow: 2px 0 0 #dee2e6; }
        .pk-rubric-table .pk-sec-row > td,
        .pk-rubric-table .pk-cat-row > td { box-shadow: none; }
        .pk-rubric-table tr.pk-sec-row > td:nth-child(1) {
            position: sticky; left: 0; top: auto; z-index: 2;
            background: #a80000 !important; color: #fff !important;
            box-shadow: 2px 0 0 #dee2e6;
        }
        .pk-rubric-table tr.pk-cat-row > td:nth-child(1) {
            position: sticky; left: 0; top: auto; z-index: 2;
            background: #f7c9c9 !important; color: #a80000 !important;
            box-shadow: 2px 0 0 #dee2e6;
        }
        #pkRubricHead th:nth-child(1),
        #pkRubricHead th:nth-child(2),
        #pkRubricHead th:nth-child(3) { position: sticky; z-index: 5; }
        #pkRubricHead th:nth-child(1) { left: 0; }
        #pkRubricHead th:nth-child(2) { left: 48px; }
        #pkRubricHead th:nth-child(3) { left: 168px; box-shadow: 2px 0 0 #dee2e6; }
    }
    .pk-cell-text { white-space: pre-line; font-size: .76rem; vertical-align: top; }
    .pk-kol-hit { background: #c3deca !important; color: #212529 !important; }
    .pk-kol-hit * { color: #212529 !important; }
    .pk-nilai-combo { position: relative; display: inline-block; width: 100%; }
    .pk-nilai-combo .hd-ms-btn { width: 100%; text-align: left; background: #fff; border: 1px solid #ced4da; border-radius: 4px; padding: .2rem .45rem; font-size: .78rem; display: flex; justify-content: space-between; align-items: center; color: #495057; cursor: pointer; }
    .pk-nilai-combo .hd-ms-btn:hover { border-color: #a80000; }
    .pk-nilai-combo .hd-ms-btn:disabled { background: #f8f9fa; color: #adb5bd; cursor: not-allowed; }
    .pk-nilai-combo .hd-ms-btn.is-invalid { border-color: #dc3545; }
    .pk-nilai-combo .hd-ms-caret { font-size: .85rem; color: #a80000; }
    .pk-nilai-combo .hd-ms-dropdown { display: none; position: fixed; z-index: 2000; background: #fff; border: 1px solid #ced4da; border-radius: 6px; box-shadow: 0 8px 20px rgba(0,0,0,.15); margin-top: 2px; max-height: 280px; overflow: hidden; flex-direction: column; }
    .pk-nilai-combo.open .hd-ms-dropdown { display: flex; }
    .pk-nilai-combo .hd-ms-search { margin: 6px; }
    .pk-nilai-combo .hd-ms-actions { display: flex; gap: 4px; padding: 0 6px 6px; border-bottom: 1px solid #eee; }
    .pk-nilai-combo .hd-ms-action { flex: 1; font-size: .72rem; padding: .2rem .4rem; background: #f5f5f5; border: 1px solid #ddd; border-radius: 3px; cursor: pointer; }
    .pk-nilai-combo .hd-ms-action:hover { background: #a80000; color: #fff; border-color: #a80000; }
    .pk-nilai-combo .hd-ms-list { overflow-y: auto; max-height: 200px; padding: 4px 0; }
    .pk-nilai-combo .hd-ms-item { display: flex; align-items: center; gap: 6px; padding: 5px 10px; font-size: .82rem; cursor: pointer; user-select: none; }
    .pk-nilai-combo .hd-ms-item:hover { background: #fdecea; }
    .pk-nilai-combo .hd-ms-item.pk-selected { background: #a80000; color: #fff; font-weight: 600; }
    @media (max-width: 767.98px) {
        .pk-nilai-combo .hd-ms-search { display: none; }
    }
    .pk-salin-chip { display: inline-block; background: #fdecea; padding: 3px 4px; border-radius: 6px; }
    .pk-salin-chip .pk-salin-btn { color: #a80000; border-color: #a80000; }
    .pk-salin-chip .pk-salin-btn:hover, .pk-salin-chip .pk-salin-btn:focus { background: #a80000; border-color: #a80000; color: #fff; }
    .pk-self-val { display: inline-block; min-width: 26px; text-align: center; background: #fdecea; border-radius: 6px; font-weight: 700; color: #a80000; padding: 2px 0; }
    .pk-leader-val { display: inline-block; min-width: 26px; text-align: center; background: #e3f2fd; border-radius: 6px; font-weight: 700; color: #0d47a1; padding: 2px 0; }
    .btn-pk-item-diskusi.btn-danger .pk-disk-count { display: inline-block; min-width: 16px; background: rgba(255,255,255,.9); color: #a80000; border-radius: 8px; font-size: .7rem; font-weight: 700; padding: 0 4px; }
    .pk-diskusi-msg { border: 1px solid #f0c9c9; border-radius: 8px; padding: .6rem .8rem; margin-bottom: .6rem; }
    .pk-diskusi-jwb { background: #e8f5e9; border-color: #a5d6a7; }
    .pk-truncate { display: inline-block; max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: bottom; }
    .pk-tabs .nav-link { color: #a80000; font-weight: 600; font-size: .9rem; border: 1px solid transparent; }
    .pk-tabs .nav-link.active { color: #fff; background: #a80000; border-color: #a80000; border-radius: 8px 8px 0 0; }
    .pk-tabs .nav-link:not(.active):hover { background: #fdecea; border-color: #f0c9c9; }
    .pk-row-skip td { background: #f8f9fa !important; }
    .pk-row-skip .pk-cell-text, .pk-row-skip td { color: #8b949e; }
    .pk-skip-lbl { font-size: .7rem; color: #6c757d; margin-top: 2px; }
    #pkListTeam .pk-table tbody td { vertical-align: middle; }
    #pkListTeam .pk-table tbody tr:hover td:nth-child(-n+11):not([colspan]) { background: #f5f5f5; }

</style>

<div class="container-fluid">
    <div class="pk-card">
        <h4 style="color:#a80000; font-weight:700; margin:0;">Penilaian Karya (PK)</h4>
        <p class="text-muted mb-0" style="font-size:.85rem;">Halo <strong id="myName"></strong> (NIK: <span id="myNik"></span>) — gunakan tab di bawah untuk mengisi penilaian diri sendiri maupun tim sesuai peran Anda.</p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="pk-card">
        <ul class="nav nav-tabs pk-tabs mb-3" id="pkMainTabs" role="tablist">
            <li class="nav-item" id="pkSelfTabItem">
                <a class="nav-link active" id="pkTabSelf" data-toggle="tab" href="#paneSelf" role="tab">Self Assessment</a>
            </li>
            <li class="nav-item" id="pkLeaderTabItem" style="display:none;">
                <a class="nav-link" id="pkTabLeader" data-toggle="tab" href="#paneLeader" role="tab">Leader Assessment — Tim Saya</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="paneSelf" role="tabpanel">
                <div id="pkListSelf"><span class="text-muted">Memuat data...</span></div>

                <div class="pk-card" id="diskusiCard" style="box-shadow:none; border:1px solid #f0c9c9;">
                    <h5>Diskusi PK Anda</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered pk-table mb-0">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Peserta</th>
                                    <th>Item</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Status</th>
                                    <th style="width:90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="diskTbody"><tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:.5rem;">
                        <span class="text-muted" style="font-size:.85rem;" id="diskPageLabel"></span>
                        <div id="diskPagination"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="paneLeader" role="tabpanel">
                <div class="d-flex align-items-center flex-wrap mb-2" style="gap:.5rem;">
                    <label for="pkTeamFilterPeriode" style="margin:0; font-weight:600; font-size:.85rem; color:#a80000;">Periode:</label>
                    <select id="pkTeamFilterPeriode" class="form-control form-control-sm" style="max-width:150px;"></select>
                    <select id="pkTeamFilterApproval" class="form-control form-control-sm" style="max-width:220px;" title="Filter Status Approval">
                        <option value="all">Semua Status</option>
                        <option value="mgr_pending">Belum di-approve Manager</option>
                        <option value="mgr_done">Sudah di-approve Manager</option>
                        <option value="am_wait">Menunggu approve AM</option>
                        <option value="am_done">Sudah di-approve AM</option>
                    </select>
                    <select id="pkTeamFilterProgress" class="form-control form-control-sm" style="max-width:200px;" title="Filter Progress Penilaian">
                        <option value="all">Semua Progress</option>
                        <option value="both_done">Self &amp; Leader lengkap</option>
                        <option value="self_todo">Self belum lengkap</option>
                        <option value="leader_todo">Leader belum lengkap</option>
                        <option value="no_rubric">Tanpa rubrik</option>
                    </select>
                    <select id="pkTeamFilterDiskusi" class="form-control form-control-sm" style="max-width:180px;" title="Filter Diskusi">
                        <option value="all">Semua Diskusi</option>
                        <option value="open">Ada menunggu</option>
                        <option value="none">Tidak ada</option>
                    </select>
                    <select id="pkTeamFilterGroup" class="form-control form-control-sm" style="max-width:180px;" title="Filter Group">
                        <option value="all">Semua Group</option>
                    </select>
                    <input type="text" id="pkTeamSearchInput" class="form-control form-control-sm" style="max-width:260px;" placeholder="Cari NIK / Nama / Periode / Group / Status...">
                    <button type="button" id="pkTeamResetFilter" class="btn btn-sm btn-outline-secondary">Reset</button>
                    <span class="text-muted" style="font-size:.82rem;" id="pkTeamCount"></span>
                </div>
                <div id="pkListTeam"><span class="text-muted">Memuat data...</span></div>
                <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:.5rem;">
                    <span class="text-muted" style="font-size:.85rem;" id="pkTeamPageLabel"></span>
                    <div id="pkTeamPagination"></div>
                </div>

                <div class="pk-card" id="diskusiTeamCard" style="box-shadow:none; border:1px solid #f0c9c9; margin-top:1rem;">
                    <h5>Diskusi Tim Saya</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered pk-table mb-0">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Peserta</th>
                                    <th>Item</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Status</th>
                                    <th style="width:90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="diskTeamTbody"><tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr></tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:.5rem;">
                        <span class="text-muted" style="font-size:.85rem;" id="diskTeamPageLabel"></span>
                        <div id="diskTeamPagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ISI NILAI --}}
<div class="modal fade" id="pkNilaiModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#a80000; font-weight:700;" id="pkNilaiTitle">Isi Nilai</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="pkNilaiInfo" class="mb-2" style="font-size:.85rem;"></div>
                <div id="pkNilaiLockBanner" class="alert alert-warning py-2 px-3" style="display:none; font-size:.85rem;"></div>
                <div id="pkTeamPickWrap" class="mb-3" style="display:none;">
                    <label style="font-weight:600; font-size:.85rem;">Pilih Anggota Tim:</label>
                    <select id="pkTeamPick" class="form-control form-control-sm" style="max-width:480px;"></select>
                </div>
                <div id="pkJbWrap" class="mb-3" style="display:none;">
                    <label style="font-weight:600; font-size:.85rem;">Jumlah Bawahan Langsung: <span class="text-danger">*</span></label>
                    <input type="number" id="pkJumlahBawahan" class="form-control form-control-sm" style="max-width:180px;" min="0" step="1" placeholder="Jumlah bawahan langsung">
                    <small id="pkJbHint" class="text-muted" style="font-size:.75rem; display:none;">Terisi otomatis dari data IPP.</small>
                    <small id="pkSkipLeadHint" class="text-muted" style="font-size:.75rem; display:none;">Bagian LEADERSHIP COMPETENCY (group 4-5) tidak perlu diisi karena Jumlah Bawahan Langsung = 0.</small>
                </div>
                <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                    <table class="table table-bordered pk-table pk-rubric-table mb-0">
                        <colgroup id="pkRubricCols"></colgroup>
                        <thead id="pkRubricHead"></thead>
                        <tbody id="pkRubricTbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSaveNilai">Simpan Nilai</button>
                <button type="button" class="btn btn-success" id="btnModalApprove" style="display:none;">Approve</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DISKUSI --}}
<div class="modal fade" id="pkDiskusiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#a80000; font-weight:700;">Diskusi</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="diskThread" style="max-height:45vh; overflow:auto;"></div>
                <hr>
                <div id="diskAskWrap">
                    <label style="font-weight:600; font-size:.85rem;">Tulis pertanyaan / pesan:</label>
                    <textarea id="diskPesan" class="form-control" rows="3" maxlength="1000"></textarea>
                    <button type="button" class="btn btn-primary btn-sm mt-2" id="btnDiskKirim">Kirim</button>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@include('partials.echo-bootstrap')
<script>
if (window.jQuery && jQuery.fn.modal && jQuery.fn.modal.Constructor && jQuery.fn.modal.Constructor.prototype) {
    jQuery.fn.modal.Constructor.prototype._enforceFocus = function () {};
}
$(function () {
    function csrf() { return $('meta[name="csrf-token"]').attr('content'); }
    function pkDupHeading(sec, cat) {
        if (!cat) return false;
        let norm = function (s) { return String(s || '').replace(/\s+/g, ' ').trim().toUpperCase(); };
        let t = norm(String(sec || '').replace(/^\d+\.\s*/, '').split('(')[0]);
        return t === norm(cat);
    }

    function pkFirstLine(s) {
        return String(s || '').split(/\r?\n/)[0].trim();
    }

    function pkRest(s) {
        let t = String(s || '');
        let i = t.indexOf('\n');
        return i === -1 ? '' : t.slice(i + 1).trim();
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    let myNik = @json($userNik ?? '');
    let diskPage = 1, diskPerPage = 10;
    let diskTeamPage = 1, diskTeamPerPage = 10;
    let showData = null;
    let cur = { pkId: null, view: 'self', nik: null, readonly: false };
    let diskCtx = { pkId: null, nik: null, detailId: null };

    function progressBadge(filled, total) {
        if (total === 0) return '<span class="pk-badge pk-badge-mute">tanpa rubrik</span>';
        if (filled >= total) return '<span class="pk-badge pk-badge-success">' + filled + '/' + total + '</span>';
        if (filled > 0) return '<span class="pk-badge pk-badge-warn">' + filled + '/' + total + '</span>';
        return '<span class="pk-badge pk-badge-mute">0/' + total + '</span>';
    }

    let teamRows = [];
    let teamPage = 1, teamPerPage = 10, teamLastPage = 1, teamTotal = 0, teamTotalAll = 0, teamSearchTimer = null;

    function teamAmBadge(m) {
        if (!m.has_am) return '<span class="pk-badge pk-badge-mute">-</span>';
        return m.am_approved ? '<span class="pk-badge pk-badge-success" title="Disetujui Associate Manager">AM ✓</span>' : '<span class="pk-badge pk-badge-mute">AM -</span>';
    }

    function teamMgrBadge(m) {
        return m.mgr_approved ? '<span class="pk-badge pk-badge-success" title="Disetujui Manager">Mgr ✓</span>' : '<span class="pk-badge pk-badge-mute">Mgr -</span>';
    }

    function approveBlockReason(m) {
        let parts = [];
        if (!(m.total > 0)) {
            parts.push('Nilai belum lengkap untuk disetujui.');
        } else {
            if (m.group !== '2-3' && m.self_filled < m.total) parts.push('Self Assessment peserta belum lengkap (' + m.self_filled + '/' + m.total + ').');
            if (m.leader_filled < m.total) parts.push('Leader Assessment belum lengkap (' + m.leader_filled + '/' + m.total + ').');
        }
        if (m.role === 'manager' && m.has_am && !m.am_approved) parts.push('Tahap Associate Manager harus disetujui terlebih dahulu.');
        return parts.join(' ') || (m.group === '2-3' ? 'Lengkapi penilaian Leader terlebih dahulu.' : 'Lengkapi penilaian Self dan Leader terlebih dahulu.');
    }

    function modalApprovalMember() {
        if (!showData || cur.view !== 'leader') return null;
        return teamRows.find(function (r) {
            return String(r.pkId) === String(cur.pkId) && String(r.nik) === String(cur.nik);
        }) || null;
    }

    function renderModalApproval() {
        let $btn = $('#btnModalApprove');
        let m = modalApprovalMember();
        if (!m) { $btn.hide(); return; }
        let stage = null;
        if (m.role === 'associate_manager' && m.has_am && !m.mgr_approved) stage = 'am';
        else if (m.role === 'manager') stage = 'manager';
        if (!stage) { $btn.hide(); return; }

        let approved = stage === 'am' ? !!m.am_approved : !!m.mgr_approved;
        let selfOk = m.group === '2-3' || m.self_filled >= m.total;
        let complete = m.total > 0 && selfOk && m.leader_filled >= m.total;
        let ready = stage === 'am' ? complete : (complete && (!m.has_am || m.am_approved));
        let amPending = stage === 'manager' && complete && m.has_am && !m.am_approved;

        let locks = (showData && showData.locks) || {};
        let approveLocked = !approved && ((stage === 'am' && !!locks.approval_am) || (stage === 'manager' && !!locks.approval_manager));
        let unapproveLocked = approved && !!locks.unapproval;

        $btn.data({ pk: m.pkId, nik: m.nik, stage: stage }).show().prop('disabled', false).removeAttr('title').removeData('block');
        if (approved) {
            if (unapproveLocked) {
                $btn.text('Unapprove').data('action', 'unapprove')
                    .removeClass('btn-success btn-outline-danger btn-warning').addClass('btn-secondary')
                    .prop('disabled', true).attr('title', 'Unapproval sedang dikunci (LOCKING PK).');
                return;
            }
            $btn.text('Unapprove').data('action', 'unapprove').removeClass('btn-success btn-secondary btn-warning').addClass('btn-outline-danger');
        } else if (approveLocked) {
            $btn.text('Approve').data('action', 'approve')
                .removeClass('btn-success btn-outline-danger btn-warning').addClass('btn-secondary')
                .prop('disabled', true)
                .attr('title', (stage === 'am' ? 'Approval Associate Manager' : 'Approval Manager') + ' sedang dikunci (LOCKING PK).');
        } else if (ready) {
            $btn.text('Approve').data('action', 'approve').removeClass('btn-outline-danger btn-secondary btn-warning').addClass('btn-success');
        } else if (amPending) {
            $btn.text('Approve').data('action', 'approve').data('block', 'am_pending')
                .removeClass('btn-outline-danger btn-success btn-secondary').addClass('btn-warning').attr('title', approveBlockReason(m));
        } else {
            $btn.text('Approve').data('action', 'approve').removeClass('btn-success btn-outline-danger btn-warning').addClass('btn-secondary')
                .prop('disabled', true).attr('title', approveBlockReason(m));
        }
    }

    $(document).on('click', '#btnModalApprove', function () {
        let $b = $(this);
        if ($b.prop('disabled')) return;
        if ($b.data('block') === 'am_pending') {
            Swal.fire('Associate Manager belum approve', 'Peserta ini harus disetujui Associate Manager terlebih dahulu sebelum Manager dapat melakukan approve.', 'warning');
            return;
        }
        $.ajax({
            url: '/hr/pk-assessment/approve/' + $b.data('pk') + '/' + encodeURIComponent($b.data('nik')),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { stage: $b.data('stage'), action: $b.data('action') }
        }).done(function (res) {
            Swal.fire('Berhasil', res.message, 'success');
            loadList().always(function () {
                loadTeamList().always(function () { renderModalApproval(); });
            });
        }).fail(function (x) {
            Swal.fire('Gagal', (x.responseJSON && x.responseJSON.message) || 'Tidak dapat memproses persetujuan.', 'error');
        });
    });

    function populateTeamFilterOptions(opts) {
        let periods = (opts && opts.periods) || [];
        let pOpts = '<option value="all">Semua Periode</option>';
        periods.forEach(function (p) { pOpts += '<option value="' + escapeHtml(p) + '">Periode ' + escapeHtml(p) + '</option>'; });
        let prevP = $('#pkTeamFilterPeriode').val() || 'all';
        $('#pkTeamFilterPeriode').html(pOpts).val(periods.indexOf(prevP) !== -1 ? prevP : 'all');

        let groups = (opts && opts.groups) || [];
        let gOpts = '<option value="all">Semua Group</option>';
        groups.forEach(function (g) { gOpts += '<option value="' + escapeHtml(g) + '">' + escapeHtml(g) + '</option>'; });
        let prevG = $('#pkTeamFilterGroup').val() || 'all';
        $('#pkTeamFilterGroup').html(gOpts).val(groups.indexOf(prevG) !== -1 ? prevG : 'all');
    }

    function teamFiltering() {
        let f = $('#pkTeamFilterPeriode').val() || 'all';
        let a = $('#pkTeamFilterApproval').val() || 'all';
        let p = $('#pkTeamFilterProgress').val() || 'all';
        let d = $('#pkTeamFilterDiskusi').val() || 'all';
        let g = $('#pkTeamFilterGroup').val() || 'all';
        let q = String($('#pkTeamSearchInput').val() || '').trim();
        return f !== 'all' || a !== 'all' || p !== 'all' || d !== 'all' || g !== 'all' || q !== '';
    }

    function loadTeamList() {
        return $.get('/hr/pk-assessment/team-list', {
            page: teamPage,
            per_page: teamPerPage,
            periode: $('#pkTeamFilterPeriode').val() || 'all',
            approval: $('#pkTeamFilterApproval').val() || 'all',
            progress: $('#pkTeamFilterProgress').val() || 'all',
            diskusi: $('#pkTeamFilterDiskusi').val() || 'all',
            group: $('#pkTeamFilterGroup').val() || 'all',
            q: String($('#pkTeamSearchInput').val() || '').trim()
        }, function (res) {
            teamRows = res.data || [];
            let meta = res.meta || {};
            teamPage = parseInt(meta.page || 1);
            teamPerPage = parseInt(meta.per_page || teamPerPage);
            teamLastPage = parseInt(meta.last_page || 1);
            teamTotal = parseInt(meta.total || 0);
            teamTotalAll = parseInt(meta.total_all || meta.total || 0);
            populateTeamFilterOptions(res.options || {});
            renderTeamTable();
            renderTeamPagination();
        });
    }

    function renderTeamTable() {
        let h = 'class="bg-primary text-center" style="color:white !important; vertical-align:middle;"';
        let html = '<div class="table-responsive"><table class="table table-sm table-bordered pk-table mb-1" style="min-width:1100px;"><thead>' +
            '<tr>' +
            '<th rowspan="2" ' + h + '>No</th>' +
            '<th rowspan="2" ' + h + '>Periode</th>' +
            '<th rowspan="2" ' + h + '>NIK</th>' +
            '<th rowspan="2" ' + h + '>Nama</th>' +
            '<th rowspan="2" ' + h + '>Role Saya</th>' +
            '<th rowspan="2" ' + h + '>Group</th>' +
            '<th rowspan="2" ' + h + '>Jumlah Bawahan</th>' +
            '<th colspan="2" ' + h + '>Progres Nilai</th>' +
            '<th rowspan="2" ' + h + '>Diskusi</th>' +
            '<th colspan="2" ' + h + '>Status</th>' +
            '<th rowspan="2" class="bg-primary text-center" style="color:white !important; width:200px; vertical-align:middle;">Aksi</th>' +
            '</tr>' +
            '<tr>' +
            '<th ' + h + '>Nilai Karyawan</th>' +
            '<th ' + h + '>Nilai Atasan</th>' +
            '<th ' + h + '>Associate Manager</th>' +
            '<th ' + h + '>Manager</th>' +
            '</tr>' +
            '</thead><tbody>';
        if (!teamRows.length) {
            html += '<tr><td colspan="13" class="text-center text-muted">' + (teamFiltering() ? 'Tidak ada peserta yang cocok dengan filter.' : 'Tidak ada peserta pada periode ini.') + '</td></tr>';
        }
        let start = (teamPage - 1) * teamPerPage;
        teamRows.forEach(function (m, idx) {
            html += '<tr data-pk="' + m.pkId + '" data-nik="' + escapeHtml(m.nik) + '">' +
                '<td class="text-center">' + (start + idx + 1) + '</td>' +
                '<td title="master: ' + escapeHtml(m.master) + '">' + escapeHtml(m.periode) + '</td>' +
                '<td>' + escapeHtml(m.nik) + '</td>' +
                '<td>' + escapeHtml(m.nama) + '</td>' +
                '<td>' + (m.role === 'associate_manager' ? 'Associate Manager' : 'Manager') + '</td>' +
                '<td class="text-center">' + escapeHtml(m.group) + '</td>' +
                '<td class="text-center">' + (m.jumlah_bawahan == null ? '-' : escapeHtml(m.jumlah_bawahan)) + '</td>' +
                '<td class="text-center">' + (m.group === '2-3' ? '<span class="pk-badge pk-badge-mute">tidak perlu</span>' : progressBadge(m.self_filled, m.total)) + '</td>' +
                '<td class="text-center">' + progressBadge(m.leader_filled, m.total) + '</td>' +
                '<td class="text-center team-cell-diskusi">' + (m.diskusi_open > 0 ? '<span class="pk-badge pk-badge-warn">' + m.diskusi_open + ' menunggu</span>' : '<span class="pk-badge pk-badge-mute">-</span>') + '</td>' +
                '<td class="text-center">' + teamAmBadge(m) + '</td>' +
                '<td class="text-center">' + teamMgrBadge(m) + '</td>' +
                '<td class="text-center">' +
                '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                '<button type="button" class="btn btn-sm btn-primary btn-pk-nilai" data-pk="' + m.pkId + '" data-view="leader" data-nik="' + escapeHtml(m.nik) + '">Nilai</button> ' +
                '<a class="btn btn-sm btn-outline-danger" target="_blank" rel="noopener" href="/hr/pk-assessment/export-pdf/' + m.pkId + '/' + encodeURIComponent(m.nik) + '">PDF</a>' +
                '</div></td>' +
                '</tr>';
        });
        html += '</tbody></table></div>';
        $('#pkListTeam').html(html);
        $('#pkTeamCount').text(teamFiltering() ? (teamTotal + ' dari ' + teamTotalAll + ' peserta') : (teamTotalAll + ' peserta'));
    }

    function renderTeamPagination() {
        if (teamTotal === 0) { $('#pkTeamPageLabel').text(''); $('#pkTeamPagination').html(''); return; }
        let prev = teamPage > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 pk-team-page-btn" data-page="' + (teamPage - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = teamPage < teamLastPage ? '<button class="btn btn-sm btn-outline-primary pk-team-page-btn" data-page="' + (teamPage + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) { opts += '<option value="' + n + '"' + (n === teamPerPage ? ' selected' : '') + '>' + n + '</option>'; });
        $('#pkTeamPageLabel').text('Halaman ' + teamPage + ' dari ' + teamLastPage + ' (' + teamTotal + ' peserta)');
        $('#pkTeamPagination').html('<div class="d-flex align-items-center" style="gap:.5rem;"><span class="text-muted" style="font-size:.82rem;">Tampilkan</span><select id="pkTeamPerPageSel" class="form-control form-control-sm" style="width:auto;">' + opts + '</select><div>' + prev + next + '</div></div>');
    }

    $(document).on('click', '.pk-team-page-btn', function () { teamPage = parseInt($(this).data('page')) || 1; loadTeamList(); });
    $(document).on('change', '#pkTeamPerPageSel', function () { teamPerPage = parseInt($(this).val()) || 10; teamPage = 1; loadTeamList(); });
    $(document).on('change', '#pkTeamFilterPeriode, #pkTeamFilterApproval, #pkTeamFilterProgress, #pkTeamFilterDiskusi, #pkTeamFilterGroup', function () { teamPage = 1; loadTeamList(); });
    $(document).on('input', '#pkTeamSearchInput', function () {
        clearTimeout(teamSearchTimer);
        teamSearchTimer = setTimeout(function () { teamPage = 1; loadTeamList(); }, 350);
    });

    $(document).on('click', '#pkTeamResetFilter', function () {
        $('#pkTeamFilterPeriode, #pkTeamFilterApproval, #pkTeamFilterProgress, #pkTeamFilterDiskusi, #pkTeamFilterGroup').val('all');
        $('#pkTeamSearchInput').val('');
        teamPage = 1;
        loadTeamList();
    });

    // ================= LIST =================
    function loadList() {
        return $.get('/hr/pk-assessment/list', function (res) {
            myNik = res.my.nik;
            $('#myName').text(res.my.nama || '-');
            $('#myNik').text(myNik || '-');
            let selfRows = '';
            let selfNo = 0;
            (res.data || []).forEach(function (pk) {
                if (pk.self && String(pk.self.group) !== '2-3') {
                    let selfAppr = (pk.self.am_approved || pk.self.mgr_approved);
                    let selfLocked = selfAppr || !!pk.input_locked;
                    selfNo++;
                    selfRows += '<tr>' +
                        '<td class="text-center">' + selfNo + '</td>' +
                        '<td style="white-space:nowrap;"><strong>' + escapeHtml(pk.periode || '-') + '</strong>' +
                        '<div style="font-size:.78rem; color:#6c757d;">master: ' + escapeHtml(pk.file_name || '-') + '</div></td>' +
                        '<td><div><strong>' + escapeHtml(res.my.nama || '-') + '</strong></div>' +
                        '<div style="font-size:.78rem; color:#6c757d;">' + escapeHtml(myNik || '-') + '</div></td>' +
                        '<td class="text-center">' + escapeHtml(pk.self.group == null ? '-' : String(pk.self.group)) + '</td>' +
                        '<td class="text-center">' + progressBadge(pk.self.filled, pk.self.total) + '</td>' +
                        '<td class="text-center">' + (pk.self.am_approved ? '<span class="pk-badge pk-badge-success">Approved</span>' : '<span class="text-muted">-</span>') + '</td>' +
                        '<td class="text-center">' + (pk.self.mgr_approved ? '<span class="pk-badge pk-badge-success">Approved</span>' : '<span class="text-muted">-</span>') + '</td>' +
                        '<td class="text-center">' +
                        '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                        (selfLocked
                            ? '<button type="button" class="btn btn-sm btn-primary btn-pk-nilai" data-pk="' + pk.id + '" data-view="self" data-locked="1" style="white-space:nowrap;" title="' + (selfAppr ? 'Self Assessment sudah di-approve' : 'Input Nilai Karyawan sedang dikunci (LOCKING PK)') + ' — nilai hanya dapat dilihat">Lihat Nilai</button> '
                            : '<button class="btn btn-sm btn-primary btn-pk-nilai" data-pk="' + pk.id + '" data-view="self" style="white-space:nowrap;">Isi Nilai</button> ') +
                        '<a class="btn btn-sm btn-outline-danger" target="_blank" rel="noopener" style="white-space:nowrap;" href="/hr/pk-assessment/export-pdf/' + pk.id + '/' + encodeURIComponent(myNik) + '">PDF</a>' +
                        '</div></td></tr>';
                }
            });
            let selfHtml = '';
            if (selfRows) {
                selfHtml = '<div class="table-responsive">' +
                    '<table class="table table-hover table-bordered table-sm mb-0" style="font-size:.85rem; min-width:900px;">' +
                    '<thead><tr>' +
                    '<th class="bg-primary text-center" style="color:white !important; width:40px; vertical-align:middle;">No</th>' +
                    '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Periode</th>' +
                    '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Karyawan</th>' +
                    '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Group</th>' +
                    '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian</th>' +
                    '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Associate Manager</th>' +
                    '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Manager</th>' +
                    '<th class="bg-primary text-center" style="color:white !important; width:200px; vertical-align:middle;">Aksi</th>' +
                    '</tr></thead>' +
                    '<tbody>' + selfRows + '</tbody></table></div>';
            } else if ((res.data || []).length) {
                selfHtml = '<p class="text-muted mb-0">Anda tidak menjadi peserta pada periode-periode ini. Silakan cek tab Leader Assessment bila Anda menilai tim.</p>';
            } else {
                selfHtml = '<p class="text-muted mb-0">Belum ada master PK. Hubungi HR untuk mengunggah template.</p>';
            }
            $('#pkListSelf').html(selfHtml);
            let hasTeam = !!(res.my && res.my.has_team);
            populateTeamFilterOptions(res.filter_options || {});
            if (hasTeam) {
                if (new URLSearchParams(window.location.search).get('diskusi') === 'open') {
                    $('#pkTeamFilterDiskusi').val('open');
                }
            } else {
                $('#pkTeamCount').text('');
                $('#pkListTeam').html('<p class="text-muted mb-0">Anda tidak memiliki tim yang harus dinilai.</p>');
            }
            $('#pkLeaderTabItem').toggle(hasTeam);
            if (hasTeam && new URLSearchParams(window.location.search).get('tab') === 'leader') {
                $('#pkTabLeader').tab('show');
            }
            if (hasTeam && new URLSearchParams(window.location.search).get('diskusi') === 'open') {
                setTimeout(function () {
                    let el = document.getElementById('diskusiTeamCard');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        });
    }

    $('#pkMainTabs a[data-toggle="tab"]').on('shown.bs.tab', function () {
        let t = $(this).attr('href') === '#paneLeader' ? 'leader' : 'self';
        let url = new URL(window.location.href);
        url.searchParams.set('tab', t);
        window.history.replaceState(null, '', url.pathname + '?' + url.searchParams.toString());
        if (t === 'leader') loadTeamList();
    });

    // ================= MODAL NILAI =================
    function openNilaiContext(pkId, view, nik, onOk, onNotApplicable) {
        cur.pkId = pkId;
        cur.view = view;
        cur.nik = nik || myNik;
        $.get('/hr/pk-assessment/' + cur.pkId, function (res) {
            showData = res;
            let memberNiks = (res.team || []).map(function (m) { return m.nik; });
            if (memberNiks.indexOf(String(cur.nik)) === -1 && String(cur.nik) !== String(res.my.nik)) {
                cur.nik = (res.team && res.team.length) ? res.team[0].nik : myNik;
            }
            if (!cur.view) cur.view = memberNiks.indexOf(String(cur.nik)) !== -1 ? 'leader' : 'self';
            if (cur.view === 'self' && !res.self) {
                if (onNotApplicable) { onNotApplicable(); return; }
                Swal.fire('Info', 'Anda bukan peserta Penilaian Karya pada master ini.', 'info');
                return;
            }
            renderTeamPick();
            renderJb();
            renderNilai();
            onOk(res, memberNiks);
        }).fail(function () {
            if (onNotApplicable) onNotApplicable();
        });
    }

    $(document).on('click', '.btn-pk-nilai', function () {
        openNilaiContext($(this).data('pk'), $(this).data('view'), $(this).data('nik') || myNik, function () {
            $('#pkNilaiModal').modal('show');
        });
    });

    function teamMembers() { return (showData && showData.team) || []; }

    function renderTeamPick() {
        if (cur.view !== 'leader') { $('#pkTeamPickWrap').hide(); return; }
        let opts = '';
        teamMembers().forEach(function (m) {
            opts += '<option value="' + escapeHtml(m.nik) + '"' + (String(m.nik) === String(cur.nik) ? ' selected' : '') + '>' +
                escapeHtml(m.nik) + ' — ' + escapeHtml(m.nama) + ' (' + (m.role === 'associate_manager' ? 'AM' : 'Mgr') + ')</option>';
        });
        $('#pkTeamPick').html(opts);
        $('#pkTeamPickWrap').show();
    }

    $(document).on('change', '#pkTeamPick', function () {
        cur.nik = $(this).val();
        renderJb();
        renderNilai();
    });

    function renderJb() {
        if (!showData) { $('#pkJbWrap').hide(); return; }
        let jb;
        if (cur.view === 'self') {
            if (!showData.self) { $('#pkJbWrap').hide(); return; }
            jb = showData.self.jumlah_bawahan;
            $('#pkJbHint').text('Terisi otomatis dari data IPP.')
                .toggle(showData.self.jumlah_bawahan_dari === 'ipp' && jb !== null && jb !== undefined);
        } else {
            let m = currentTeamMember();
            if (!m) { $('#pkJbWrap').hide(); return; }
            jb = m.jumlah_bawahan;
            $('#pkJbHint').text('Isi 0 bila peserta tidak memiliki bawahan langsung — bagian Leadership Competency (group ' + String(m.group) + ') tidak perlu diisi.')
                .toggle(['2-3', '4-5'].indexOf(String(m.group)) !== -1);
        }
        $('#pkJumlahBawahan').val((jb === null || jb === undefined) ? '' : jb);
        $('#pkJbWrap').show();
    }

    function currentGroup() {
        if (cur.view === 'self') return showData.self.group;
        let m = teamMembers().find(function (x) { return String(x.nik) === String(cur.nik); });
        return m ? m.group : '4-5';
    }

    function currentNilaiMap() {
        if (cur.view === 'self') return showData.self.nilai;
        let m = teamMembers().find(function (x) { return String(x.nik) === String(cur.nik); });
        return (m && m.nilai) || {};
    }

    function isLeadershipDetail(d) {
        if (!d) return false;
        let t = (String(d.section || '') + ' ' + String(d.category || '')).toUpperCase();
        return t.indexOf('LEADERSHIP') !== -1;
    }

    function currentTeamMember() {
        return teamMembers().find(function (x) { return String(x.nik) === String(cur.nik); }) || null;
    }

    function noLeadershipActive() {
        if (!showData) return false;
        if (['2-3', '4-5'].indexOf(String(currentGroup())) === -1) return false;
        let v = $.trim(String($('#pkJumlahBawahan').val() === undefined ? '' : $('#pkJumlahBawahan').val()));
        return v !== '' && parseInt(v, 10) === 0;
    }

    function hasSavedLeadershipNilai() {
        if (!showData) return false;
        let map = currentNilaiMap();
        let details = showData.rubric[currentGroup()] || [];
        let col = cur.view === 'self' ? 'nilai_self' : 'nilai_leader';
        return details.some(function (d) {
            if (!isLeadershipDetail(d)) return false;
            let n = map[d.id] || {};
            let v = n[col];
            return v !== null && v !== undefined && String(v) !== '';
        });
    }

    function applyLeadershipSkip() {
        if (!showData) return;
        let skip = noLeadershipActive();
        let details = showData.rubric[currentGroup()] || [];
        details.forEach(function (d) {
            if (!isLeadershipDetail(d)) return;
            let $sel = $('#pkRubricTbody .pk-nilai-sel[data-id="' + d.id + '"]');
            if (!$sel.length) return;
            let $row = $sel.closest('tr');
            let $combo = $sel.closest('.pk-nilai-combo');
            if (skip) {
                $sel.val('').trigger('change');
                $combo.find('.hd-ms-label').text('—');
                $combo.find('.pk-nilai-opt').removeClass('pk-selected');
                $combo.find('.hd-ms-btn').prop('disabled', true).removeClass('is-invalid');
                $combo.removeClass('open');
                $row.addClass('pk-row-skip');
                $row.find('.pk-kol').removeClass('pk-kol-hit');
                $row.find('.pk-skip-lbl').show();
            } else {
                if (!cur.readonly) $combo.find('.hd-ms-btn').prop('disabled', false);
                $row.removeClass('pk-row-skip');
                $row.find('.pk-skip-lbl').hide();
            }
        });
        $('#pkSkipLeadHint').text('Bagian LEADERSHIP COMPETENCY (group ' + String(currentGroup()) + ') tidak perlu diisi karena Jumlah Bawahan Langsung = 0.').toggle(skip);
    }

    $(document).on('input change', '#pkJumlahBawahan', function () {
        applyLeadershipSkip();
    });

    function leaderByLine(n) {
        n = n || {};
        if (!n.nilai_leader || !n.leader_by_label) return '';
        let role = n.leader_by_role ? ' (' + escapeHtml(n.leader_by_role) + ')' : '';
        let at = n.leader_at ? ' · ' + escapeHtml(n.leader_at) : '';
        return '<div class="text-muted" style="font-size:.68rem; margin-top:2px;">Diisi: ' +
            escapeHtml(n.leader_by_label) + role + at + '</div>';
    }

    function atasanCell(n) {
        n = n || {};
        if (!n.nilai_leader) {
            return '<td class="text-center" title="Belum dinilai atasan"><span class="text-muted">-</span></td>';
        }
        let by = n.leader_by_label ? escapeHtml(n.leader_by_label) : (n.leader_by ? escapeHtml(n.leader_by) : '');
        let role = n.leader_by_role ? ' (' + escapeHtml(n.leader_by_role) + ')' : '';
        let at = n.leader_at ? escapeHtml(n.leader_at) : '';
        let t = 'Diisi: ' + (by || 'atasan') + role + (at ? ' &middot; ' + at : '');
        return '<td class="text-center" title="' + t + '"><span class="pk-leader-val">' + n.nilai_leader + '</span></td>';
    }

    function diskusiCell(d, n) {
        n = n || {};
        let open = parseInt(n.diskusi_open, 10) || 0;
        let id = escapeHtml(d.id);
        let label = escapeHtml(d.label || d.no_urut);
        if (open > 0) {
            return '<button class="btn btn-sm btn-danger btn-pk-item-diskusi" title="' + open + ' pertanyaan belum dijawab" ' +
                'data-id="' + id + '" data-label="' + label + '">? <span class="pk-disk-count">' + open + '</span></button>';
        }
        return '<button class="btn btn-sm btn-outline-success btn-pk-item-diskusi" title="Buka diskusi item ini" ' +
            'data-id="' + id + '" data-label="' + label + '">?</button>';
    }

    function renderNilai() {
        let g = currentGroup();
        let details = showData.rubric[g] || [];
        let nilai = currentNilaiMap();

        let who = cur.view === 'self'
            ? 'Penilaian diri sendiri — ' + escapeHtml(showData.my.nama) + ' (' + escapeHtml(showData.my.nik) + ')'
            : (function () {
                let m = teamMembers().find(function (x) { return String(x.nik) === String(cur.nik); });
                return m ? ('Leader Assessment — ' + escapeHtml(m.nama) + ' (' + escapeHtml(m.nik) + '), role Anda: ' + (m.role === 'associate_manager' ? 'Associate Manager' : 'Manager')) : '-';
            })();
        let isLeader = cur.view === 'leader';
        let locks = (showData && showData.locks) || {};
        let inputLocked = isLeader ? !!locks.input_atasan : !!locks.input_karyawan;
        let approvedLocked = !isLeader && !!(showData.self && (showData.self.am_approved || showData.self.mgr_approved));
        cur.readonly = inputLocked || approvedLocked;
        if (cur.readonly) {
            let reasons = [];
            if (inputLocked) {
                reasons.push('Input ' + (isLeader ? 'Nilai Atasan (Leader Assessment)' : 'Nilai Karyawan (Self Assessment)') +
                    ' periode ' + escapeHtml(showData.pk.periode) + ' sedang dikunci (LOCKING PK).');
            }
            if (approvedLocked) {
                let lockStages = [];
                if (showData.self.am_approved) lockStages.push('Associate Manager');
                if (showData.self.mgr_approved) lockStages.push('Manager');
                reasons.push('Self Assessment sudah di-approve ' + lockStages.join(' dan ') + '.');
            }
            $('#pkNilaiLockBanner').html(reasons.join(' ') + ' Nilai hanya dapat dilihat dan tidak dapat diubah.').show();
        } else {
            $('#pkNilaiLockBanner').hide().empty();
        }
        $('#btnSaveNilai').toggle(!cur.readonly);
        $('#pkJumlahBawahan').prop('disabled', cur.readonly);
        $('#pkNilaiTitle').text('Periode ' + showData.pk.periode + ' — ' + (cur.view === 'self' ? 'Self Assessment' : 'Leader Assessment') + ' (group ' + g + ')');
        $('#pkNilaiInfo').html(who + ' <span class="text-muted">' + (cur.readonly ? '— menampilkan nilai Self Assessment yang sudah di-approve.' : '— pilih atau isi angka 1–5 pada kolom ' + (isLeader ? 'Nilai Atasan' : 'Nilai Karyawan') + ' sesuai deskripsi kriteria tiap kompetensi.') + '</span>');

        let colspan = isLeader ? 13 : 12;

        let widths = [48, 120, 220, 240, 145, 145, 145, 145, 145];
        widths.push(128);
        widths.push(116);
        widths.push(64);
        $('#pkRubricCols').html(widths.map(function (w) { return '<col style="width:' + w + 'px;">'; }).join(''));

        let head = '<tr><th>No</th><th>Area</th>' +
            '<th>Competency Behavior</th><th>Key Behavior</th>';
        for (let i = 1; i <= 5; i++) head += '<th class="text-center">SKOR ' + i + '</th>';
        if (isLeader) head += '<th class="text-center" title="Nilai Self Assessment karyawan">Nilai Karyawan</th>';
        head += '<th class="text-center">' + (isLeader
            ? 'Nilai Atasan<br><span class="pk-salin-chip"><button type="button" class="btn btn-xs pk-salin-btn mt-1" id="btnSalinNilaiAtasan" style="font-size:.62rem; padding:.1rem .3rem; line-height:1.15; white-space:normal; max-width:110px;">⧉ Salin dari Nilai Karyawan</button></span>'
            : 'Nilai Karyawan') + '</th>';
        if (!isLeader) head += '<th class="text-center" title="Nilai dari atasan (Leader Assessment)">Nilai Atasan</th>';
        head += '<th class="text-center">Diskusi</th></tr>';
        $('#pkRubricHead').html(head);

        let html = '';
        let lastSec = null, lastCat = null;
        details.forEach(function (d) {
            if (d.section !== lastSec) {
                html += '<tr class="pk-sec-row"><td colspan="' + colspan + '">' + escapeHtml(d.section) + '</td></tr>';
                lastSec = d.section; lastCat = null;
            }
            if (d.category && d.category !== lastCat && !pkDupHeading(d.section, d.category)) {
                html += '<tr class="pk-cat-row"><td colspan="' + colspan + '">' + escapeHtml(d.category) + '</td></tr>';
                lastCat = d.category;
            }
            let n = nilai[d.id] || {};
            let checkedVal = cur.view === 'self' ? n.nilai_self : n.nilai_leader;

            html += '<tr>' +
                '<td class="text-center">' + escapeHtml(d.no_urut) + '</td>' +
                '<td class="pk-cell-text">' + escapeHtml(d.area || '-') + '</td>' +
                '<td class="pk-cell-text"><b>' + escapeHtml(pkFirstLine(d.competency_behavior)) + '</b>' +
                (pkRest(d.competency_behavior) ? '\n' + escapeHtml(pkRest(d.competency_behavior)) : '') + '</td>' +
                '<td class="pk-cell-text">' + escapeHtml(d.key_behavior || '-') + '</td>';
            for (let i = 1; i <= 5; i++) {
                html += '<td class="pk-cell-text pk-kol' + (checkedVal == i ? ' pk-kol-hit' : '') + '" data-id="' + d.id + '" data-kol="' + i + '">' + escapeHtml(d.kriteria[i - 1] || '-') + '</td>';
            }
            if (isLeader) {
                html += '<td class="text-center">' + (n.nilai_self ? '<span class="pk-self-val">' + n.nilai_self + '</span>' : '<span class="text-muted">-</span>') + '</td>';
            }
            html += '<td class="text-center">' +
                '<div class="pk-nilai-combo" data-id="' + d.id + '">' +
                '<input type="hidden" class="pk-nilai-sel" data-id="' + d.id + '" value="' + (checkedVal || '') + '">' +
                '<button type="button" class="hd-ms-btn"><span class="hd-ms-label">' + (checkedVal ? checkedVal : '—') + '</span><span class="hd-ms-caret">▾</span></button>' +
                '<div class="hd-ms-dropdown">' +
                '<input type="text" class="hd-ms-search form-control form-control-sm" placeholder="Ketik 1-5..." inputmode="numeric" autocomplete="off">' +
                '<div class="hd-ms-actions"><button type="button" class="hd-ms-action pk-nilai-clear">Kosongkan</button></div>' +
                '<div class="hd-ms-list">' + [1, 2, 3, 4, 5].map(function (i) {
                    return '<div class="hd-ms-item pk-nilai-opt' + (checkedVal == i ? ' pk-selected' : '') + '" data-val="' + i + '"><span>' + i + '</span></div>';
                }).join('') + '</div>' +
                '</div>' +
                '</div>' +
                (isLeader ? leaderByLine(n) : '') +
                '<div class="pk-skip-lbl" style="display:none;">Tidak perlu diisi</div></td>' +
                (isLeader ? '' : atasanCell(n)) +
                '<td class="text-center">' + diskusiCell(d, n) + '</td></tr>';
        });
        if (!html) html = '<tr><td colspan="' + colspan + '" class="text-center text-muted">Rubrik kosong untuk group ' + g + '.</td></tr>';
        $('#pkRubricTbody').html(html);
        if (cur.readonly) $('#pkRubricTbody .hd-ms-btn').prop('disabled', true);
        applyLeadershipSkip();
        renderModalApproval();
    }

    function pkValidNilai(v) {
        return v === '' || /^[1-5]$/.test(String(v));
    }

    function pkHighlightNilai(id, v) {
        $('#pkRubricTbody .pk-kol[data-id="' + id + '"]').removeClass('pk-kol-hit');
        if (v) $('#pkRubricTbody .pk-kol[data-id="' + id + '"][data-kol="' + v + '"]').addClass('pk-kol-hit');
    }

    $(document).on('input change', '.pk-nilai-sel', function () {
        let id = $(this).data('id');
        let raw = $(this).val();
        let v = $.trim(String(raw === undefined || raw === null ? '' : raw));
        let ok = pkValidNilai(v);
        $(this).closest('.pk-nilai-combo').find('.hd-ms-btn').toggleClass('is-invalid', !ok);
        pkHighlightNilai(id, ok ? v : '');
    });

    function pkNilaiSet($combo, v) {
        if (cur.readonly) return;
        let val = String(v || '');
        let $hidden = $combo.find('.pk-nilai-sel');
        $hidden.val(val).trigger('change');
        $combo.find('.hd-ms-label').text(val !== '' ? val : '—');
        $combo.find('.pk-nilai-opt').removeClass('pk-selected');
        if (val !== '') $combo.find('.pk-nilai-opt[data-val="' + val + '"]').addClass('pk-selected');
        $combo.removeClass('open');
    }

    function pkNilaiPosition($combo) {
        let btn = $combo.find('.hd-ms-btn')[0];
        let dd = $combo.find('.hd-ms-dropdown')[0];
        if (!btn || !dd) return;
        let r = btn.getBoundingClientRect();
        let width = Math.max(r.width, 120);
        let pad = 8;
        let left = Math.min(Math.max(r.left, pad), Math.max(pad, window.innerWidth - width - pad));
        $(dd).css({ left: left + 'px', width: width + 'px' });
        let h = $(dd).outerHeight();
        let vv = window.visualViewport;
        let viewTop = vv ? vv.offsetTop : 0;
        let viewBottom = vv ? (vv.offsetTop + vv.height) : window.innerHeight;
        if (viewBottom - r.bottom < h + pad && r.top - viewTop > h + pad) {
            $(dd).css({ top: (r.top - h - 2) + 'px' });
        } else {
            $(dd).css({ top: (r.bottom + 2) + 'px' });
        }
    }

    function pkIsTouchLike() {
        return !!(window.matchMedia && window.matchMedia('(hover: none), (pointer: coarse)').matches);
    }

    // Saat halaman/tabel di-scroll atau keyboard mengubah viewport, dropdown tetap
    // mengikuti tombolnya dan tidak ikut tertutup.
    function pkRepositionOpenNilai() {
        let $combo = $('.pk-nilai-combo.open').first();
        if (!$combo.length) return;
        let btn = $combo.find('.hd-ms-btn')[0];
        if (!btn) { $combo.removeClass('open'); return; }
        let r = btn.getBoundingClientRect();
        let outside = r.bottom < 0 || r.top > window.innerHeight || r.right < 0 || r.left > window.innerWidth;
        if (outside) { $combo.removeClass('open'); return; }
        pkNilaiPosition($combo);
    }

    $(document).on('click', '.pk-nilai-combo .hd-ms-btn', function (e) {
        if (cur.readonly) return;
        e.stopPropagation();
        let $combo = $(this).closest('.pk-nilai-combo');
        let willOpen = !$combo.hasClass('open');
        $('.pk-nilai-combo').removeClass('open');
        if (!willOpen) return;
        $combo.addClass('open');
        let $search = $combo.find('.hd-ms-search');
        $search.val('').removeClass('is-invalid');
        $combo.find('.pk-nilai-opt').show();
        pkNilaiPosition($combo);
        // Di HP/tablet sentuh jangan autofocus: keyboard akan memicu resize/scroll
        // sehingga dropdown tampak terbuka lalu langsung tertutup.
        if (!pkIsTouchLike() && $search.is(':visible')) $search.focus();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.pk-nilai-combo').length) $('.pk-nilai-combo').removeClass('open');
    });

    $(document).on('keyup', '.pk-nilai-combo .hd-ms-search', function () {
        let q = $.trim(String($(this).val() || ''));
        $(this).closest('.pk-nilai-combo').find('.pk-nilai-opt').each(function () {
            $(this).toggle(String($(this).data('val')).indexOf(q) !== -1);
        });
    });

    $(document).on('keydown', '.pk-nilai-combo .hd-ms-search', function (e) {
        let $combo = $(this).closest('.pk-nilai-combo');
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            let q = $.trim(String($(this).val() || ''));
            if (q !== '' && pkValidNilai(q)) {
                pkNilaiSet($combo, q);
                $combo.find('.hd-ms-btn').focus();
            } else if (q !== '') {
                $(this).addClass('is-invalid');
            }
        } else if (e.key === 'Escape' || e.keyCode === 27) {
            $combo.removeClass('open');
            $combo.find('.hd-ms-btn').focus();
        }
    });

    $(document).on('click', '.pk-nilai-opt', function () {
        let $combo = $(this).closest('.pk-nilai-combo');
        pkNilaiSet($combo, String($(this).data('val')));
    });

    $(document).on('click', '.pk-nilai-clear', function () {
        let $combo = $(this).closest('.pk-nilai-combo');
        pkNilaiSet($combo, '');
        $combo.find('.hd-ms-btn').focus();
    });

    document.addEventListener('scroll', function (e) {
        let t = e.target;
        if (t && t.nodeType === 1 && $(t).closest('.hd-ms-dropdown').length) return;
        if ($('.pk-nilai-combo.open').length) pkRepositionOpenNilai();
    }, true);

    $(window).on('resize', pkRepositionOpenNilai);
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', pkRepositionOpenNilai);
        window.visualViewport.addEventListener('scroll', pkRepositionOpenNilai);
    }

    $(document).on('click', '#btnSalinNilaiAtasan', function () {
        if (cur.view !== 'leader') return;
        let map = currentNilaiMap();
        let filled = 0, skipped = 0;
        $('#pkRubricTbody .pk-nilai-combo').each(function () {
            let $combo = $(this);
            if ($combo.find('.hd-ms-btn').is(':disabled')) { skipped++; return; }
            if ($.trim(String($combo.find('.pk-nilai-sel').val() || '')) !== '') { skipped++; return; }
            let d = map[$combo.data('id')] || {};
            let self = $.trim(String(d.nilai_self === undefined || d.nilai_self === null ? '' : d.nilai_self));
            if (self === '' || !pkValidNilai(self)) { skipped++; return; }
            pkNilaiSet($combo, self);
            filled++;
        });
        if (filled > 0) {
            Swal.fire('Berhasil', filled + ' kolom Nilai Atasan diisi dari Nilai Karyawan.' +
                (skipped > 0 ? ' ' + skipped + ' baris dilewati (sudah terisi / nilai karyawan kosong / tidak perlu diisi).' : ''), 'success');
        } else {
            Swal.fire('Info', 'Tidak ada kolom Nilai Atasan kosong yang bisa diisi dari Nilai Karyawan.', 'info');
        }
    });

    // ================= SIMPAN =================
    $('#btnSaveNilai').on('click', function () {
        if (cur.readonly) return;
        if (!showData) return;
        let jumlahBawahan = $.trim($('#pkJumlahBawahan').val());
        if (jumlahBawahan === '') {
            Swal.fire('Peringatan', 'Isi Jumlah Bawahan Langsung terlebih dahulu.', 'warning');
            return;
        }
        let details = showData.rubric[currentGroup()] || [];
        let skipLead = noLeadershipActive();
        let items = [];
        let invalid = [];
        details.forEach(function (d) {
            let inp = $('#pkRubricTbody .pk-nilai-sel[data-id="' + d.id + '"]');
            let v = inp.length ? $.trim(String(inp.val() || '')) : '';
            if (skipLead && isLeadershipDetail(d)) v = '';
            if (!pkValidNilai(v)) {
                invalid.push(inp);
                return;
            }
            items.push({ detail_id: d.id, nilai: v });
        });
        if (invalid.length) {
            Swal.fire('Peringatan', 'Nilai harus berupa angka 1 sampai 5.', 'warning');
            invalid[0].closest('.pk-nilai-combo').find('.hd-ms-btn').addClass('is-invalid').focus();
            return;
        }

        let doSave = function () {
            let sendData = { mode: cur.view, nik: cur.nik, items: items, jumlah_bawahan: jumlahBawahan };
            $.ajax({
                url: '/hr/pk-assessment/save/' + cur.pkId,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf() },
                data: sendData,
                success: function (res) {
                    Swal.fire('Sukses', res.message || 'Tersimpan.', 'success');
                    if (cur.view === 'leader') {
                        loadList().always(function () {
                            loadTeamList().always(function () {
                                openNilaiContext(cur.pkId, 'leader', cur.nik, function () { renderModalApproval(); });
                            });
                        });
                    } else {
                        $('#pkNilaiModal').modal('hide');
                        loadList();
                    }
                },
                error: function (xhr) {
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan.';
                    if (String(msg).indexOf('sudah di-approve') !== -1) {
                        Swal.fire('Tidak dapat disimpan', msg, 'warning');
                        loadList();
                        return;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        };

        if (['2-3', '4-5'].indexOf(String(currentGroup())) !== -1 && parseInt(jumlahBawahan, 10) === 0 && hasSavedLeadershipNilai()) {
            Swal.fire({
                title: 'Konfirmasi',
                html: 'Jumlah Bawahan Langsung = 0.<br>Nilai Leadership Competency (group ' + escapeHtml(currentGroup()) + ') yang sudah ada akan <b>dihapus</b>. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a80000'
            }).then(function (r) {
                if (r.isConfirmed) { doSave(); } else { renderNilai(); }
            });
            return;
        }

        doSave();
    });

    // ================= DISKUSI =================
    function diskusiRowHtml(x, scope) {
        return '<tr data-disk-id="' + x.id + '" data-pk="' + x.hr_pk_id + '" data-nik="' + escapeHtml(x.nik_yang_dibahas) + '">' +
            '<td>' + escapeHtml(x.periode) + '</td>' +
            '<td>' + escapeHtml(x.nama_yang_dibahas) + (scope === 'self' && !x.is_pengaju ? ' <span class="pk-badge pk-badge-info">tim</span>' : '') + '</td>' +
            '<td><span class="pk-truncate" title="' + escapeHtml(x.item) + '">' + escapeHtml(x.item) + '</span></td>' +
            '<td class="disk-cell-pesan"><span class="pk-truncate" title="' + escapeHtml(x.pesan) + '">' + escapeHtml(x.pesan) + '</span></td>' +
            '<td class="disk-cell-jawaban">' + (x.jawaban ? '<span class="pk-truncate" title="' + escapeHtml(x.jawaban) + '">' + escapeHtml(x.jawaban) + '</span>' : '-') + '</td>' +
            '<td class="disk-cell-status">' + (x.status === 'open' ? '<span class="pk-badge pk-badge-warn">menunggu</span>' : '<span class="pk-badge pk-badge-success">dijawab</span>') + '</td>' +
            '<td><button class="btn btn-sm btn-outline-primary btn-disk-open" data-pk="' + x.hr_pk_id + '" data-nik="' + escapeHtml(x.nik_yang_dibahas) + '">Buka</button></td>' +
            '</tr>';
    }

    function loadDiskusi() {
        $.get('/hr/pk-assessment/diskusi-list', { page: diskPage, per_page: diskPerPage, scope: 'self' }, function (res) {
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="7" class="text-center text-muted">Belum ada diskusi.</td></tr>';
            }
            items.forEach(function (x) {
                html += diskusiRowHtml(x, 'self');
            });
            $('#diskTbody').html(html);
            renderDiskPagination(res.meta || {});
        });
    }

    function loadDiskusiTeam() {
        $.get('/hr/pk-assessment/diskusi-list', { page: diskTeamPage, per_page: diskTeamPerPage, scope: 'team' }, function (res) {
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="7" class="text-center text-muted">Belum ada diskusi tim.</td></tr>';
            }
            items.forEach(function (x) {
                html += diskusiRowHtml(x, 'team');
            });
            $('#diskTeamTbody').html(html);
            renderDiskTeamPagination(res.meta || {});
        });
    }

    function renderDiskPagination(meta) {
        let page = parseInt(meta.page || 1), lastPage = parseInt(meta.last_page || 1), total = parseInt(meta.total || 0);
        if (total === 0) { $('#diskPageLabel').text(''); $('#diskPagination').html(''); return; }
        let prev = page > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 disk-page-btn" data-page="' + (page - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage ? '<button class="btn btn-sm btn-outline-primary disk-page-btn" data-page="' + (page + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50].forEach(function (n) { opts += '<option value="' + n + '"' + (n === diskPerPage ? ' selected' : '') + '>' + n + '</option>'; });
        $('#diskPageLabel').text('Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' diskusi)');
        $('#diskPagination').html('<div class="d-flex align-items-center" style="gap:.5rem;"><span class="text-muted" style="font-size:.82rem;">Tampilkan</span><select id="diskPerPageSel" class="form-control form-control-sm" style="width:auto;">' + opts + '</select><div>' + prev + next + '</div></div>');
    }

    $(document).on('click', '.disk-page-btn', function () { diskPage = parseInt($(this).data('page')); loadDiskusi(); });
    $(document).on('change', '#diskPerPageSel', function () { diskPerPage = parseInt($(this).val()) || 10; diskPage = 1; loadDiskusi(); });

    function renderDiskTeamPagination(meta) {
        let page = parseInt(meta.page || 1), lastPage = parseInt(meta.last_page || 1), total = parseInt(meta.total || 0);
        if (total === 0) { $('#diskTeamPageLabel').text(''); $('#diskTeamPagination').html(''); return; }
        let prev = page > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 disk-team-page-btn" data-page="' + (page - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage ? '<button class="btn btn-sm btn-outline-primary disk-team-page-btn" data-page="' + (page + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50].forEach(function (n) { opts += '<option value="' + n + '"' + (n === diskTeamPerPage ? ' selected' : '') + '>' + n + '</option>'; });
        $('#diskTeamPageLabel').text('Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' diskusi)');
        $('#diskTeamPagination').html('<div class="d-flex align-items-center" style="gap:.5rem;"><span class="text-muted" style="font-size:.82rem;">Tampilkan</span><select id="diskTeamPerPageSel" class="form-control form-control-sm" style="width:auto;">' + opts + '</select><div>' + prev + next + '</div></div>');
    }

    $(document).on('click', '.disk-team-page-btn', function () { diskTeamPage = parseInt($(this).data('page')); loadDiskusiTeam(); });
    $(document).on('change', '#diskTeamPerPageSel', function () { diskTeamPerPage = parseInt($(this).val()) || 10; diskTeamPage = 1; loadDiskusiTeam(); });

    function openDiskusi(pkId, nik, detailId, itemLabel) {
        diskCtx = { pkId: pkId, nik: nik, detailId: detailId };
        $('#diskThread').html('<span class="text-muted">Memuat...</span>');
        return $.get('/hr/pk-assessment/diskusi/' + pkId + '/' + encodeURIComponent(nik), function (res) {
            let html = '';
            (res.data || []).forEach(function (x) {
                html += '<div class="pk-diskusi-msg" data-id="' + x.id + '">' +
                    '<div class="d-flex justify-content-between align-items-center mb-1">' +
                    '<span style="font-size:.78rem;" class="badge badge-light border">' + escapeHtml(x.item) + '</span>' +
                    (x.status === 'open' ? '<span class="pk-badge pk-badge-warn">menunggu</span>' : '<span class="pk-badge pk-badge-success">dijawab</span>') +
                    '</div>' +
                    '<div class="d-flex justify-content-between"><strong style="font-size:.83rem;">' + escapeHtml(x.nama_pengaju) + '</strong><span class="text-muted" style="font-size:.75rem;">' + escapeHtml(x.created_at) + '</span></div>' +
                    '<div style="font-size:.85rem;" class="disk-pesan">' + escapeHtml(x.pesan) + '</div>' +
                    (x.can_edit ? '<div class="text-right mt-1"><button class="btn btn-sm btn-link btn-disk-edit" data-id="' + x.id + '" data-pesan="' + escapeHtml(x.pesan) + '">Edit</button></div>' : '') +
                    (x.jawaban ? '<div class="pk-diskusi-msg pk-diskusi-jwb mt-2 mb-0"><div style="font-size:.8rem;"><strong>' + escapeHtml(x.nama_penjawab || 'Atasan') + '</strong> <span class="text-muted">' + escapeHtml(x.answered_at || '') + '</span></div><div style="font-size:.85rem;">' + escapeHtml(x.jawaban) + '</div></div>' : '') +
                    (x.can_answer ? '<div class="mt-2 disk-answer-wrap"><textarea class="form-control form-control-sm disk-jawaban" rows="2" maxlength="1000" placeholder="Tulis jawaban..."></textarea><button class="btn btn-sm btn-success mt-1 btn-disk-answer" data-id="' + x.id + '">Jawab</button></div>' : '') +
                    '</div>';
            });
            if (!html) html = '<p class="text-muted mb-0">Belum ada diskusi untuk ' + escapeHtml(itemLabel || 'peserta ini') + '.</p>';
            $('#diskThread').html(html);
            $('#diskPesan').val('');
            $('#diskAskWrap').toggle(String(res.my_nik || '') === String(nik));
            if (itemLabel) {
                $('#diskThread').prepend('<div class="alert alert-light border py-1 px-2 mb-2" style="font-size:.8rem;">Item: <strong>' + escapeHtml(itemLabel) + '</strong></div>');
            }
        });
    }

    function showStackedModal(sel) {
        let $m = $(sel);
        $m.off('hidden.bs.modal.stacked')
            .one('hidden.bs.modal.stacked', function () {
                $m.css('z-index', '');
                if ($('.modal.show').length) $('body').addClass('modal-open');
            });
        $m.css('z-index', 1056).modal('show');
        setTimeout(function () { $('.modal-backdrop').last().css('z-index', 1055); }, 0);
    }

    $(document).on('click', '.btn-pk-item-diskusi', function () {
        openDiskusi(cur.pkId, cur.nik, $(this).data('id'), $(this).data('label'));
        showStackedModal('#pkDiskusiModal');
    });

    $(document).on('click', '.btn-disk-open', function () {
        let pkId = $(this).data('pk');
        let nik = String($(this).data('nik'));
        let showDiskusi = function () {
            openDiskusi(pkId, nik, null, null);
            showStackedModal('#pkDiskusiModal');
        };
        openNilaiContext(pkId, null, nik, function () {
            let $nm = $('#pkNilaiModal');
            if ($nm.hasClass('show')) { showDiskusi(); return; }
            $nm.one('shown.bs.modal', showDiskusi);
            $nm.modal('show');
        }, showDiskusi);
    });

    $('#btnDiskKirim').on('click', function () {
        let msg = String($('#diskPesan').val() || '').trim();
        if (!msg) { Swal.fire('Info', 'Tulis pesan terlebih dahulu.', 'info'); return; }
        $.ajax({
            url: '/hr/pk-assessment/diskusi/' + diskCtx.pkId + '/' + encodeURIComponent(diskCtx.nik),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { hr_pk_detail_id: diskCtx.detailId, pesan: msg },
            success: function () {
                openDiskusi(diskCtx.pkId, diskCtx.nik, diskCtx.detailId, null);
                loadDiskusi();
                loadDiskusiTeam();
                loadList();
            },
            error: function (xhr) {
                Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengirim.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-disk-answer', function () {
        let id = $(this).data('id');
        let jwb = String($(this).closest('.disk-answer-wrap').find('.disk-jawaban').val() || '').trim();
        if (!jwb) { Swal.fire('Info', 'Tulis jawaban terlebih dahulu.', 'info'); return; }
        $.ajax({
            url: '/hr/pk-assessment/diskusi/answer/' + id,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf() },
            data: { jawaban: jwb },
            success: function () {
                openDiskusi(diskCtx.pkId, diskCtx.nik, diskCtx.detailId, null);
                loadDiskusi();
                loadDiskusiTeam();
            },
            error: function (xhr) { Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menjawab.', 'error'); }
        });
    });

    $(document).on('click', '.btn-disk-edit', function () {
        let id = $(this).data('id');
        let old = $(this).data('pesan');
        Swal.fire({
            title: 'Edit pesan',
            input: 'textarea',
            inputValue: old,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.value) return;
            $.ajax({
                url: '/hr/pk-assessment/diskusi/update/' + id,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf() },
                data: { pesan: r.value },
                success: function () {
                    openDiskusi(diskCtx.pkId, diskCtx.nik, diskCtx.detailId, null);
                    loadDiskusi();
                    loadDiskusiTeam();
                },
                error: function (xhr) { Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengubah.', 'error'); }
            });
        });
    });

    // ================= REALTIME DISKUSI (WEBSOCKET) =================
    let pkRtTimer = null;
    let pkRtPending = {};
    let pkRtSyncTimer = null;

    function pkRtRow(ev) {
        return {
            id: ev.id,
            hr_pk_id: ev.hr_pk_id,
            periode: ev.periode,
            nik_yang_dibahas: ev.nik_yang_dibahas,
            nama_yang_dibahas: ev.nama_yang_dibahas,
            item: ev.item,
            pesan: ev.pesan,
            jawaban: ev.jawaban,
            status: ev.status,
            is_pengaju: String(ev.nik_pengaju) === String(myNik),
            created_at: ev.created_at,
            answered_at: ev.answered_at
        };
    }

    function pkRtUpsertRow($tbody, row, scope, canInsert) {
        let $tr = $tbody.find('tr[data-disk-id="' + row.id + '"]');
        if ($tr.length) {
            $tr.replaceWith(diskusiRowHtml(row, scope));
            return;
        }
        if (!canInsert) return;
        $tbody.find('td[colspan]').closest('tr').remove();
        $tbody.prepend(diskusiRowHtml(row, scope));
    }

    function pkRtTeamBadge(ev) {
        if (typeof ev.open_count === 'undefined') return;
        let $cell = $('#pkListTeam tr[data-pk="' + ev.hr_pk_id + '"][data-nik="' + ev.nik_yang_dibahas + '"] .team-cell-diskusi');
        if (!$cell.length) return;
        $cell.html(ev.open_count > 0
            ? '<span class="pk-badge pk-badge-warn">' + ev.open_count + ' menunggu</span>'
            : '<span class="pk-badge pk-badge-mute">-</span>');
    }

    function pkRtRefreshThread() {
        let draftAsk = $('#diskPesan').val();
        let drafts = {};
        $('.disk-jawaban').each(function () {
            let id = $(this).closest('.pk-diskusi-msg').data('id');
            if (id !== undefined) drafts[id] = $(this).val();
        });
        let req = openDiskusi(diskCtx.pkId, diskCtx.nik, diskCtx.detailId, null);
        if (req && req.done) {
            req.done(function () {
                if (draftAsk) $('#diskPesan').val(draftAsk);
                Object.keys(drafts).forEach(function (id) {
                    if (!drafts[id]) return;
                    $('.pk-diskusi-msg[data-id="' + id + '"] .disk-jawaban').val(drafts[id]);
                });
            });
        }
    }

    function pkRtToast(ev) {
        let actor = ev.type === 'answered' ? ev.nik_penjawab : ev.nik_pengaju;
        if (String(actor) === String(myNik) || typeof Swal === 'undefined') return;
        let msg = ev.type === 'answered'
            ? 'Diskusi Anda dijawab: ' + (ev.item || 'Umum')
            : 'Diskusi baru dari ' + (ev.nama_pengaju || ev.nik_pengaju) + ' — periode ' + ev.periode + '.';
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: msg,
            showConfirmButton: false,
            timer: 4000
        });
    }

    function pkRtApply(ev) {
        let row = pkRtRow(ev);
        let canInsert = ev.type === 'created';
        if (String(ev.nik_yang_dibahas) === String(myNik)) {
            pkRtUpsertRow($('#diskTbody'), row, 'self', canInsert);
        } else {
            pkRtUpsertRow($('#diskTeamTbody'), row, 'team', canInsert);
        }
        pkRtTeamBadge(ev);
        if ($('#pkDiskusiModal').hasClass('show')
            && diskCtx.pkId && String(diskCtx.pkId) === String(ev.hr_pk_id)
            && String(diskCtx.nik) === String(ev.nik_yang_dibahas)) {
            pkRtRefreshThread();
        }
        pkRtToast(ev);
    }

    function pkRtHandle(ev) {
        pkRtPending[ev.id] = ev;
        clearTimeout(pkRtTimer);
        pkRtTimer = setTimeout(function () {
            let items = pkRtPending;
            pkRtPending = {};
            Object.keys(items).forEach(function (id) { pkRtApply(items[id]); });
        }, 300);
    }

    function pkRtResync() {
        clearTimeout(pkRtSyncTimer);
        pkRtSyncTimer = setTimeout(function () {
            loadDiskusi();
            loadDiskusiTeam();
        }, 500);
    }

    (function pkRtSubscribe() {
        if (!window.Echo || !myNik || typeof window.Echo.private !== 'function') return;
        window.Echo.private('pk-diskusi.' + myNik)
            .listen('.pk-diskusi-created', function (ev) { pkRtHandle(ev); })
            .listen('.pk-diskusi-updated', function (ev) { pkRtHandle(ev); })
            .listen('.pk-diskusi-answered', function (ev) { pkRtHandle(ev); });

        let pusher = window.Echo.connector && window.Echo.connector.pusher;
        if (pusher && pusher.connection && typeof pusher.connection.bind === 'function') {
            let firstConnect = true;
            pusher.connection.bind('connected', function () {
                if (firstConnect) { firstConnect = false; return; }
                pkRtResync();
            });
        }
    })();

    loadList();
    loadDiskusi();
    loadDiskusiTeam();
    if (window.location.hash === '#diskusi') {
        setTimeout(function () {
            let el = document.getElementById('diskusiCard');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    }
});
</script>
@endpush
