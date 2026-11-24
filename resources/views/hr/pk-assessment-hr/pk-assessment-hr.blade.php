@extends('layouts.base')

@section('content')
<style>
    .pk-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1.25rem; margin-bottom: 1.25rem; }
    .pk-card h5 { color: #a80000; font-weight: 700; }
    .pk-table th { background: #fdecea; color: #a80000; font-size: .8rem; vertical-align: middle; white-space: nowrap; }
    .pk-table td { font-size: .82rem; vertical-align: middle; }
    .pk-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: .72rem; font-weight: 600; }
    .pk-badge-success { background: #e8f5e9; color: #1b5e20; }
    .pk-badge-warn { background: #fff3e0; color: #e65100; }
    .pk-badge-info { background: #e3f2fd; color: #0d47a1; }
    .pk-badge-mute { background: #eceff1; color: #455a64; }
    .pk-sec-row td { background: #a80000; color: #fff; font-weight: 700; }
    .pk-cat-row td { background: #f7c9c9; color: #a80000; font-weight: 600; }
    .pk-krit-cell { font-size: .74rem; color: #555; }
    .pk-truncate { display: inline-block; max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: bottom; }
    .pk-krit-hit { background: #fdecea; font-weight: 700; color: #a80000; border-radius: 6px; }
    #hrDetailRubricHead th { position: sticky; top: 0; z-index: 3; background: #a80000 !important; color: #fff !important; }
    .pk-rubric-table { border-collapse: separate !important; border-spacing: 0; }
    .pk-rubric-table th, .pk-rubric-table td { border-width: 0 1px 1px 0; }
    .pk-rubric-table tr:first-child > th { border-top-width: 1px; }
    .pk-rubric-table tr > *:first-child { border-left-width: 1px; }
    .pk-cell-text { white-space: pre-line; font-size: .76rem; vertical-align: top; }
    .pk-kol-hit { background: #a80000 !important; color: #fff !important; }
    .pk-kol-hit * { color: #fff !important; }
    .pk-self-val { display: inline-block; min-width: 26px; text-align: center; background: #fdecea; border-radius: 6px; font-weight: 700; color: #a80000; padding: 2px 0; }
    .pk-diskusi-msg { border: 1px solid #f0c9c9; border-radius: 8px; padding: .6rem .8rem; margin-bottom: .6rem; }
    .pk-diskusi-jwb { background: #e8f5e9; border-color: #a5d6a7; }
    .pk-detail-info th { width: 150px; white-space: nowrap; }
</style>

<div class="container-fluid">
    <div class="pk-card">
        <h4 style="color:#a80000; font-weight:700; margin:0;">Monitoring Penilaian Karya (PK)</h4>
        <p class="text-muted mb-0" style="font-size:.85rem;">Rekap Self Assessment &amp; Leader Assessment seluruh peserta (Level 2–7). Halaman ini hanya untuk HR/Admin.</p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="pk-card">
        <h5>Filter</h5>
        <form id="formFilter" class="row" style="gap:0;">
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Periode</label>
                <select id="fltPeriode" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    @foreach ($periodes as $id => $p)
                        <option value="{{ $p }}">{{ $p ?: '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Group</label>
                <select id="fltGroup" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    <option value="2-3">Level 2–3</option>
                    <option value="4-5">Level 4–5</option>
                    <option value="6-7">Level 6–7</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Departemen</label>
                <select id="fltDept" class="form-control form-control-sm"><option value="">— Semua —</option></select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Status</label>
                <select id="fltStatus" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    <option value="belum">Belum diisi</option>
                    <option value="proses">Proses</option>
                    <option value="self_selesai">Self selesai</option>
                    <option value="selesai">Selesai penuh</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Cari</label>
                <input type="text" id="fltSearch" class="form-control form-control-sm" placeholder="NIK / Nama / Jabatan / Sub Dept...">
            </div>
            <div class="col-md-1 mb-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm btn-block">Cari</button>
            </div>
        </form>
    </div>

    <div class="pk-card">
        <h5>Daftar semua penilaian karyawan</h5>
        <div class="table-responsive">
            <table class="table table-bordered pk-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Periode</th>
                        <th>Peserta</th>
                        <th>Jabatan</th>
                        <th>Dept / Sub Dept</th>
                        <th>Group</th>
                        <th>Manager</th>
                        <th>Assoc. Manager</th>
                        <th class="text-center">Self</th>
                        <th class="text-center">Leader</th>
                        <th class="text-center">Diskusi</th>
                        <th class="text-center">Approval</th>
                        <th class="text-center">Status</th>
                        <th style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="hrTbody"><tr><td colspan="14" class="text-center text-muted">Memuat data...</td></tr></tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:.5rem;">
            <span class="text-muted" style="font-size:.85rem;" id="hrPageLabel"></span>
            <div id="hrPagination"></div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="hrDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#a80000; font-weight:700;" id="hrDetailTitle">Detail</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="hrDetailHeader" class="mb-2" style="font-size:.85rem;"></div>
                <ul class="nav nav-tabs mb-2">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabNilai">Nilai</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabDiskusi">Diskusi <span class="badge badge-pill badge-warning" id="hrDiskusiBadge" style="display:none;">0</span></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabNilai">
                        <div class="table-responsive" style="max-height:60vh; overflow:auto;">
                            <table class="table table-bordered pk-table pk-rubric-table mb-0">
                                <thead id="hrDetailRubricHead"></thead>
                                <tbody id="hrDetailTbody"></tbody>
                            </table>
                        </div>
                        <div class="text-right" style="font-size:.85rem;" id="hrDetailAvg"></div>
                    </div>
                    <div class="tab-pane" id="tabDiskusi">
                        <div id="hrDiskusiList" style="max-height:50vh; overflow:auto;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="hrBtnPdf" target="_blank" rel="noopener" class="btn btn-outline-dark">Export PDF</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    function pkDupHeading(sec, cat) {
        if (!cat) return false;
        let norm = function (s) { return String(s || '').replace(/\s+/g, ' ').trim().toUpperCase(); };
        let t = norm(String(sec || '').replace(/^\d+\.\s*/, '').split('(')[0]);
        return t === norm(cat);
    }

    function pkFirstLine(t) {
        return String(t || '').split(/\r?\n/)[0].trim();
    }

    function pkRest(t) {
        let v = String(t || '');
        let i = v.indexOf('\n');
        return i === -1 ? '' : v.slice(i + 1).trim();
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    let page = 1, perPage = 20;
    let deptLoaded = false;

    function statusBadge(s) {
        let m = {
            'belum': ['pk-badge-mute', 'Belum diisi'],
            'proses': ['pk-badge-warn', 'Proses'],
            'self_selesai': ['pk-badge-info', 'Self selesai'],
            'selesai': ['pk-badge-success', 'Selesai'],
            'tanpa_rubrik': ['pk-badge-mute', 'Tanpa rubrik']
        };
        let x = m[s] || ['pk-badge-mute', s];
        return '<span class="pk-badge ' + x[0] + '">' + x[1] + '</span>';
    }

    function approvalBadge(x) {
        if (x.total === 0) return '<span class="text-muted">-</span>';
        function tip(by, at) {
            let t = [];
            if (by) t.push('oleh ' + by);
            if (at) t.push(String(at).substring(0, 16));
            return t.join(' · ');
        }
        let s = '';
        if (x.has_am) {
            s += x.am_approved
                ? '<span class="pk-badge pk-badge-success" title="Disetujui Associate Manager' + (tip(x.am_approved_by_label, x.am_approved_at) ? ': ' + escapeHtml(tip(x.am_approved_by_label, x.am_approved_at)) : '') + '">AM ✓</span> '
                : '<span class="pk-badge pk-badge-mute" title="Menunggu approval Associate Manager">AM -</span> ';
        }
        s += x.mgr_approved
            ? '<span class="pk-badge pk-badge-success" title="Disetujui Manager' + (tip(x.mgr_approved_by_label, x.mgr_approved_at) ? ': ' + escapeHtml(tip(x.mgr_approved_by_label, x.mgr_approved_at)) : '') + '">Mgr ✓</span>'
            : '<span class="pk-badge pk-badge-mute" title="Menunggu approval Manager">Mgr -</span>';
        return s;
    }

    function pkScoreHtml(res) {
        let c = res.pk_components || {};
        let h = res.header || {};
        let fmt = function (v) { return v != null ? Number(v).toFixed(2) : '-'; };
        let detail;
        if (h.group === '2-3') {
            let parts = ['Core Value ' + fmt(c.wings_leader), 'Behavior ' + fmt(c.behavior_leader)];
            parts.push(c.leadership_leader != null
                ? 'Leadership ' + fmt(c.leadership_leader)
                : (h.no_leadership ? 'Leadership tidak dihitung (tanpa bawahan)' : 'Leadership belum diisi'));
            detail = parts.join(' · ');
        } else if (c.wings_leader != null && c.leadership_leader != null) {
            detail = '70% Core Value ' + fmt(c.wings_leader) + ' + 30% Leadership ' + fmt(c.leadership_leader);
        } else if (c.wings_leader != null) {
            detail = 'Core Value ' + fmt(c.wings_leader) + (h.no_leadership ? ' (Leadership tidak dihitung: tanpa bawahan)' : ' (Leadership belum diisi)');
        } else if (c.leadership_leader != null) {
            detail = 'Leadership ' + fmt(c.leadership_leader);
        } else {
            detail = '-';
        }
        return 'PK Score: <strong>' + (res.pk_score != null ? Number(res.pk_score).toFixed(2) : '-') + '</strong> <span class="text-muted">(' + detail + ')</span>';
    }

    function fillBadge(filled, total, avg) {
        let b;
        if (total === 0) b = '<span class="pk-badge pk-badge-mute">-</span>';
        else if (filled >= total) b = '<span class="pk-badge pk-badge-success">' + filled + '/' + total + '</span>';
        else if (filled > 0) b = '<span class="pk-badge pk-badge-warn">' + filled + '/' + total + '</span>';
        else b = '<span class="pk-badge pk-badge-mute">0/' + total + '</span>';
        return b + '<div class="text-muted" style="font-size:.72rem;" title="Skor gaya PK (komponen Core Value/Behavior/Leadership; Compliance tidak dihitung)">avg ' + (avg != null ? avg : '-') + '</div>';
    }

    function loadHr() {
        $('#hrTbody').html('<tr><td colspan="14" class="text-center text-muted">Memuat data...</td></tr>');
        $.get('/hr/pk-assessment-hr/list', {
            page: page,
            per_page: perPage,
            search: $('#fltSearch').val(),
            periode: $('#fltPeriode').val(),
            group: $('#fltGroup').val(),
            departemen: $('#fltDept').val(),
            dept_type: $('#fltDept option:selected').data('type') || 'dept',
            status: $('#fltStatus').val()
        }, function (res) {
            if (!deptLoaded) {
                let cur = $('#fltDept').val();
                let opts = '<option value="">— Semua —</option>';
                let subOpts = '';
                (res.filterOptions || []).forEach(function (o) {
                    let sel = o.v === cur ? ' selected' : '';
                    if (o.t === 'sub') {
                        subOpts += '<option value="' + escapeHtml(o.v) + '" data-type="sub"' + sel + '>' + escapeHtml(o.v) + '</option>';
                    } else {
                        opts += '<option value="' + escapeHtml(o.v) + '" data-type="dept"' + sel + '>' + escapeHtml(o.v) + '</option>';
                    }
                });
                if (subOpts) opts += '<optgroup label="Factory">' + subOpts + '</optgroup>';
                $('#fltDept').html(opts);
                deptLoaded = true;
            }
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="14" class="text-center text-muted">Tidak ada data.</td></tr>';
            }
            items.forEach(function (x, i) {
                html += '<tr>' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td>' + escapeHtml(x.periode || '-') + '</td>' +
                    '<td><strong>' + escapeHtml(x.nama) + '</strong><div class="text-muted" style="font-size:.75rem;">' + escapeHtml(x.nik) + ' · L' + x.level + '</div></td>' +
                    '<td>' + escapeHtml(x.jabatan || '-') + '</td>' +
                    '<td>' + escapeHtml(x.departemen || '-') + '<div class="text-muted" style="font-size:.75rem;">' + escapeHtml(x.sub_departemen || '-') + '</div></td>' +
                    '<td class="text-center">' + escapeHtml(x.group) + '</td>' +
                    '<td>' + (x.manager ? escapeHtml(x.manager) + ' <span class="text-muted" style="font-size:.72rem;">(' + escapeHtml(x.manager_nik) + ')</span>' : '<span class="pk-badge pk-badge-warn">belum ada</span>') + '</td>' +
                    '<td>' + (x.group === '6-7' ? '<span class="text-muted">-</span>' : (x.associate_manager ? escapeHtml(x.associate_manager) : '<span class="pk-badge pk-badge-warn">belum ada</span>')) + '</td>' +
                    '<td class="text-center">' + fillBadge(x.self_filled, x.total, x.avg_self) + '</td>' +
                    '<td class="text-center">' + fillBadge(x.leader_filled, x.total, x.avg_leader) + '</td>' +
                    '<td class="text-center">' + (x.diskusi_open > 0 ? '<span class="pk-badge pk-badge-warn">' + x.diskusi_open + '</span>' : '-') + '</td>' +
                    '<td class="text-center">' + approvalBadge(x) + '</td>' +
                    '<td class="text-center">' + statusBadge(x.status) + '</td>' +
                    '<td class="text-center">' +
                    '<button class="btn btn-sm btn-outline-primary btn-hr-detail" data-pk="' + x.hr_pk_id + '" data-nik="' + escapeHtml(x.nik) + '">Detail</button> ' +
                    '<a class="btn btn-sm btn-outline-dark" target="_blank" rel="noopener" href="/hr/pk-assessment/export-pdf/' + x.hr_pk_id + '/' + encodeURIComponent(x.nik) + '">PDF</a>' +
                    '</td></tr>';
            });
            $('#hrTbody').html(html);
            renderPager(res.meta || {});
        }).fail(function (xhr) {
            $('#hrTbody').html('<tr><td colspan="14" class="text-center text-danger">Gagal memuat (HTTP ' + xhr.status + ').</td></tr>');
        });
    }

    function renderPager(meta) {
        let p = parseInt(meta.page || 1), last = parseInt(meta.last_page || 1), total = parseInt(meta.total || 0);
        if (total === 0) { $('#hrPageLabel').text(''); $('#hrPagination').html(''); return; }
        let prev = p > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 hr-page-btn" data-page="' + (p - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = p < last ? '<button class="btn btn-sm btn-outline-primary hr-page-btn" data-page="' + (p + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) { opts += '<option value="' + n + '"' + (n === perPage ? ' selected' : '') + '>' + n + '</option>'; });
        $('#hrPageLabel').text('Halaman ' + p + ' dari ' + last + ' (' + total + ' baris)');
        $('#hrPagination').html('<div class="d-flex align-items-center" style="gap:.5rem;"><span class="text-muted" style="font-size:.82rem;">Tampilkan</span><select id="hrPerPageSel" class="form-control form-control-sm" style="width:auto;">' + opts + '</select><div>' + prev + next + '</div></div>');
    }

    $(document).on('click', '.hr-page-btn', function () { page = parseInt($(this).data('page')); loadHr(); });
    $(document).on('change', '#hrPerPageSel', function () { perPage = parseInt($(this).val()) || 20; page = 1; loadHr(); });
    $('#formFilter').on('submit', function (e) { e.preventDefault(); page = 1; loadHr(); });
    $('#fltPeriode, #fltGroup, #fltDept, #fltStatus').on('change', function () { page = 1; loadHr(); });

    // ===== DETAIL =====
    $(document).on('click', '.btn-hr-detail', function () {
        let pkId = $(this).data('pk');
        let nik = String($(this).data('nik'));
        $.get('/hr/pk-assessment-hr/detail/' + pkId + '/' + encodeURIComponent(nik), function (res) {
            let h = res.header;
            $('#hrDetailTitle').text('Detail PK — ' + h.nama + ' (' + h.nik + ') — Periode ' + (res.pk.periode || '-'));
            let approvalHtml = (function () {
                let a = res.approval || {};
                var amTip = (a.am_approved_by_label || a.am_approved_by), mgTip = (a.mgr_approved_by_label || a.mgr_approved_by);
                if (a.has_am) {
                    var b = a.am_approved
                        ? '<span class="pk-badge pk-badge-success">AM ✓ ' + escapeHtml([amTip, a.am_approved_at].filter(Boolean).join(' · ')) + '</span> '
                        : '<span class="pk-badge pk-badge-mute">AM menunggu</span> ';
                    return b + (a.mgr_approved
                        ? '<span class="pk-badge pk-badge-success">Mgr ✓ ' + escapeHtml([mgTip, a.mgr_approved_at].filter(Boolean).join(' · ')) + '</span>'
                        : '<span class="pk-badge pk-badge-mute">Mgr menunggu</span>');
                }
                return a.mgr_approved
                    ? '<span class="pk-badge pk-badge-success">Mgr ✓ ' + escapeHtml([mgTip, a.mgr_approved_at].filter(Boolean).join(' · ')) + '</span>'
                    : '<span class="pk-badge pk-badge-mute">Mgr menunggu</span>';
            })();
            $('#hrDetailHeader').html(
                '<table class="table table-sm table-bordered pk-table pk-detail-info mb-2"><tbody>' +
                '<tr><th>Jabatan</th><td>' + escapeHtml(h.jabatan || '-') + '</td>' +
                '<th>Dept / Sub</th><td>' + escapeHtml(h.departemen) + ' / ' + escapeHtml(h.sub_departemen || '-') + '</td></tr>' +
                '<tr><th>Level</th><td>' + h.level + ' (group ' + h.group + ')</td>' +
                '<th>Manager</th><td>' + escapeHtml(h.manager) + '</td></tr>' +
                '<tr><th>Associate Manager</th><td>' + escapeHtml(h.associate_manager) + '</td>' +
                '<th>Approval</th><td>' + approvalHtml + '</td></tr>' +
                '</tbody></table>'
            );
            $('#hrBtnPdf').attr('href', '/hr/pk-assessment/export-pdf/' + pkId + '/' + encodeURIComponent(nik));

            let colspan = 11;
            let head = '<tr><th style="width:44px;">No</th><th style="width:110px;">Area</th>' +
                '<th style="min-width:210px;">Competency Behavior</th><th style="min-width:230px;">Key Behavior</th>';
            for (let i = 1; i <= 5; i++) head += '<th style="min-width:190px;" class="text-center">SKOR ' + i + '</th>';
            head += '<th style="width:128px;" class="text-center" title="Nilai Self Assessment karyawan">Nilai Karyawan</th>' +
                '<th style="width:170px;" class="text-center">Nilai Atasan</th></tr>';
            $('#hrDetailRubricHead').html(head);

            let html = '', lastSec = null, lastCat = null;
            let skipLeadership = !!(res.header && res.header.no_leadership);
            let isLeadRow = function (d) {
                let t = (String(d.section || '') + ' ' + String(d.category || '')).toUpperCase();
                return t.indexOf('LEADERSHIP') !== -1;
            };
            (res.rows || []).forEach(function (d) {
                if (d.section !== lastSec) {
                    html += '<tr class="pk-sec-row"><td colspan="' + colspan + '">' + escapeHtml(d.section) + '</td></tr>';
                    lastSec = d.section; lastCat = null;
                }
                if (d.category && d.category !== lastCat && !pkDupHeading(d.section, d.category)) {
                    html += '<tr class="pk-cat-row"><td colspan="' + colspan + '">' + escapeHtml(d.category) + '</td></tr>';
                    lastCat = d.category;
                }
                let skipped = skipLeadership && isLeadRow(d);
                let kritHtml = '';
                for (let i = 1; i <= 5; i++) {
                    kritHtml += '<td class="pk-cell-text pk-kol' + (d.nilai_leader == i ? ' pk-kol-hit' : '') + '">' + escapeHtml(d.kriteria[i - 1] || '-') + '</td>';
                }
                let skipLbl = '<span class="text-muted" style="font-size:.7rem;">Tidak perlu diisi</span>';
                html += '<tr' + (skipped ? ' class="pk-row-skip" style="background:#f8f9fa;"' : '') + '>' +
                    '<td class="text-center">' + escapeHtml(d.no_urut) + '</td>' +
                    '<td class="pk-cell-text">' + escapeHtml(d.area || '-') + '</td>' +
                    '<td class="pk-cell-text"><b>' + escapeHtml(pkFirstLine(d.competency_behavior)) + '</b>' +
                    (pkRest(d.competency_behavior) ? '\n' + escapeHtml(pkRest(d.competency_behavior)) : '') + '</td>' +
                    '<td class="pk-cell-text">' + escapeHtml(d.key_behavior || '-') + '</td>' +
                    kritHtml +
                    '<td class="text-center">' + (skipped ? skipLbl : (d.nilai_self ? '<span class="pk-self-val">' + d.nilai_self + '</span>' : '<span class="text-muted">-</span>')) + '</td>' +
                    '<td class="text-center">' + (skipped ? skipLbl : (d.nilai_leader ? '<span class="pk-self-val">' + d.nilai_leader + '</span>' : '<span class="text-muted">-</span>')) +
                    '<div class="text-muted" style="font-size:.7rem;">' + escapeHtml([d.leader_by_label, d.leader_at].filter(Boolean).join(' · ')) + '</div></td>' +
                    '</tr>';
            });
            if (!html) html = '<tr><td colspan="' + colspan + '" class="text-center text-muted">Rubrik kosong untuk group ' + h.group + '.</td></tr>';
            $('#hrDetailTbody').html(html);
            $('#hrDetailAvg').html(pkScoreHtml(res));

            let dhtml = '';
            (res.diskusi || []).forEach(function (t) {
                dhtml += '<div class="pk-diskusi-msg">' +
                    '<div class="d-flex justify-content-between align-items-center mb-1">' +
                    '<span class="badge badge-light border" style="font-size:.75rem;">' + escapeHtml(t.item) + '</span>' +
                    (t.status === 'open' ? '<span class="pk-badge pk-badge-warn">menunggu</span>' : '<span class="pk-badge pk-badge-success">dijawab</span>') +
                    '</div>' +
                    '<div><strong style="font-size:.83rem;">' + escapeHtml(t.nama_pengaju) + '</strong> <span class="text-muted" style="font-size:.75rem;">' + escapeHtml(t.created_at) + '</span>' +
                    '<div style="font-size:.85rem;">' + escapeHtml(t.pesan) + '</div></div>' +
                    (t.jawaban ? '<div class="pk-diskusi-msg pk-diskusi-jwb mt-2 mb-0"><div style="font-size:.8rem;"><strong>' + escapeHtml(t.nama_penjawab || 'Atasan') + '</strong> <span class="text-muted">' + escapeHtml(t.answered_at || '') + '</span></div><div style="font-size:.85rem;">' + escapeHtml(t.jawaban) + '</div></div>' : '') +
                    '</div>';
            });
            if (!dhtml) dhtml = '<p class="text-muted mb-0">Belum ada diskusi.</p>';
            $('#hrDiskusiList').html(dhtml);
            let openN = (res.diskusi || []).filter(function (t) { return t.status === 'open'; }).length;
            $('#hrDiskusiBadge').toggle(openN > 0).text(openN);
            $('#hrDetailModal').modal('show');
        }).fail(function (xhr) {
            Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal memuat detail.', 'error');
        });
    });

    loadHr();
});
</script>
@endpush
