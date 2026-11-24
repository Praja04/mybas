@extends('layouts.base')

@section('content')

<style>
    .ipp-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .ipp-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .ipp-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .ipp-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .ipp-badge-success { background:#c8e6c9; color:#1b5e20; }
    .ipp-badge-warning { background:#fff3cd; color:#8a6d1c; }
    .ipp-badge-danger { background:#ffcdd2; color:#b71c1c; }
    .ipp-badge-muted { background:#e9ecef; color:#6c757d; }
    .ipp-badge-info { background:#e3f2fd; color:#0d47a1; }
    .modal-ipp-xl .modal-dialog { max-width: 95%; margin: 1rem auto; }
    .ipp-table-wrapper { max-height: 60vh; overflow:auto; border:1px solid #dee2e6; border-radius:4px; }
    .ipp-table-wrapper table { margin-bottom:0; }
    .ipp-table-wrapper thead th { position:sticky; top:0; background:#f8f9fa; z-index:1; }
    .ipp-table-wrapper thead th.ap-month-head { position:sticky; top:0; background:#ede7f6; color:#4a148c; }
    .ap-aktual-atasan-input { min-width:70px; max-width:110px; text-align:right; }
    #apDiskusiListBody tr { cursor:pointer; }
    #apDiskusiListBody tr:hover { background:#f3e5f5; }
    .ap-diskusi-truncate { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .ap-target-cell { font-size:.9rem; color:#6c757d; text-align:right; white-space:nowrap; }
    .ap-chip { display:inline-block; padding:.15rem .5rem; border-radius:4px; font-size:.75rem; font-weight:600; margin-right:.35rem; margin-bottom:.35rem; }
    .ipp-lokal-dropdown { position: relative; max-width: 340px; }
    .ipp-lokal-dropdown .hd-ms-btn {
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
    .ipp-lokal-dropdown .hd-ms-btn:hover { border-color: #4a148c; }
    .ipp-lokal-dropdown .hd-ms-caret { font-size: .9rem; color: #4a148c; }
    .ipp-lokal-dropdown .hd-ms-dropdown {
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
    .ipp-lokal-dropdown.open .hd-ms-dropdown { display: flex; }
    .ipp-lokal-dropdown .hd-ms-search { margin: 6px; }
    .ipp-lokal-dropdown .hd-ms-list { overflow-y: auto; max-height: 240px; padding: 4px 0; }
    .ipp-lokal-dropdown .hd-ms-item {
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
    .ipp-lokal-dropdown .hd-ms-item:hover { background: #f3e5f5; }
    #apList td { vertical-align: middle !important; }
    #apList thead th { vertical-align: middle !important; }
</style>

<div class="container-fluid">
    <h1 class="ipp-page-title">Approval IPP</h1>

    <div class="ipp-card">
        <h5>Selamat Datang, <span id="apUserName">{{ $userName }}</span></h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:0;">
            NIK: <strong id="apUserNik">{{ $userNik }}</strong> &mdash; Anda dapat melihat, mengedit aktual, dan
            melakukan approval IPP untuk bawahan Anda (per tahapan: Mid Year / End Year).
        </p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="ipp-card">
        <h5>Daftar IPP team anda</h5>
        <form id="apFormSearch" class="form-inline mb-2" onsubmit="return false;">
            <input type="text" id="apSearch" class="form-control form-control-sm mr-2" style="min-width:260px;" placeholder="Cari Periode / Golongan / Dept / Position / NIK / Nama...">
            <button type="button" class="btn btn-sm btn-primary mr-1" id="apBtnSearch">Cari</button>
            <button type="button" class="btn btn-sm btn-secondary" id="apBtnReset">Reset</button>
        </form>
        <div id="apList"></div>
        <div id="apPagination" class="mt-2"></div>
    </div>

    <div class="ipp-card" id="apDiskusiSection" style="display:none;">
        <h5>Diskusi IPP</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th class="text-center" style="font-size:.78rem; color:#4a148c;">Status</th>
                        <th style="font-size:.78rem; color:#4a148c;">Periode</th>
                        <th style="font-size:.78rem; color:#4a148c;">Karyawan</th>
                        <th style="font-size:.78rem; color:#4a148c;">Tahapan</th>
                        <th style="font-size:.78rem; color:#4a148c;">KPI</th>
                        <th style="font-size:.78rem; color:#4a148c;">Pertanyaan</th>
                        <th style="font-size:.78rem; color:#4a148c;">Jawaban</th>
                        <th style="font-size:.78rem; color:#4a148c;">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="apDiskusiListBody"></tbody>
            </table>
        </div>
        <div id="apDiskusiPagination" class="mt-2"></div>
    </div>
</div>

{{-- MODAL ISI & APPROVE AKTUAL --}}
<div class="modal fade" id="apModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apModalTitle" style="color:#4a148c; font-weight:700;">Isi &amp; Approve Aktual IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="apInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>
                <div class="d-flex align-items-center flex-wrap mb-3" style="gap:1rem;">
                    <div id="apKaryawanPicker"></div>
                    <div id="apTahapanPicker"></div>
                </div>
                <div id="apApprovalBox" class="mb-3"></div>
                <div id="apLockNotice" class="mb-2" style="display:none;"></div>
                <div class="mb-3" id="apJbWrap" style="display:none;">
                    <label style="font-weight:600; font-size:.85rem;">Jumlah Bawahan Langsung (bawahan peserta):</label>
                    <input type="number" min="0" step="1" id="apJumlahBawahan" class="form-control form-control-sm" style="max-width:200px;" placeholder="0">
                    <small id="apJbHint" class="text-muted" style="font-size:.78rem; display:block;">Terisi otomatis dari data IPP/snapshot. Isi 0 bila peserta tidak memiliki bawahan langsung.</small>
                </div>
                <div class="mb-3" id="apNoteWrap">
                    <label style="font-weight:600; font-size:.85rem;">Note / Catatan Approval:</label>
                    <textarea id="apApprovalNote" class="form-control" rows="2" placeholder="Wajib diisi saat Approve/Reject. Isi '-' jika tidak ada catatan." style="font-size:.85rem;"></textarea>
                </div>
                <div class="ipp-table-wrapper">
                    <table class="table table-bordered" style="font-size:.975rem;">
                        <thead id="apTableHead"></thead>
                        <tbody id="apTableBody"></tbody>
                    </table>
                </div>
                <div id="apAchSummary" class="mb-2"></div>
                <div id="apAchLegend" class="mb-2"></div>
            </div>
            <div class="modal-footer ap-action-footer" style="display:flex; flex-wrap:wrap; gap:.5rem;">
                <div style="display:flex; flex-wrap:wrap; gap:.5rem; width:100%; justify-content: space-between;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:30%;">Tutup</button>
                    <button type="button" class="btn btn-success" id="btnSaveAktual" style="width:30%;">Simpan Aktual</button>
                    <button type="button" class="btn btn-info" id="btnRejectTahapan" style="width:30%;">Reject Tahapan</button>
                </div>
                <div style="display:flex; gap:.5rem; width:100%; justify-content: space-between;">
                    <button type="button" class="btn btn-warning" id="btnResetApproval" style="width:30%;">Batalkan Approval</button>
                    <button type="button" class="btn btn-primary" id="btnApproveTahapan" style="width:30%;">Approve Tahapan</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DISKUSI --}}
<div class="modal fade" id="apDiskusiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apDiskusiTitle" style="color:#4a148c; font-weight:700;">Diskusi</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="apDiskusiThread" style="max-height:60vh; overflow:auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DOKUMEN PENDUKUNG (read-only) --}}
<div class="modal fade" id="apBuktiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apBuktiTitle" style="color:#4a148c; font-weight:700;">Dokumen Pendukung</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="apBuktiList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@include('partials.echo-bootstrap')
<script>
    let apCurrentIpp = null;
    let apOtoId = null;
    let apTahapan = null;
    let apDiskusi = [];
    let apDiskusiDetailId = null;
    let apDiskusiTahapan = null;
    let apDiskusiAutoOpen = null;
    let apDiskusiPage = 1;
    let apDiskusiPerPage = 10;
    let apKaryawanMap = {};
    let apAmWaiting = null;
    let apMyNik = "{{ $userNik }}";

    const AP_APPROVAL_LABEL = {
        approved: 'Disetujui',
        rejected: 'Ditolak',
        null: 'Belum Diapprove'
    };

    // Cegah Esc menutup Bootstrap modal saat popup SweetAlert sedang terbuka.
    const AP_SWAL = Swal.mixin({ keydownListenerCapture: true });

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/[&<>"']/g, c => (
            { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]
        ));
    }

    function tahapanLabel(t) {
        if (!t) return '-';
        let parts = String(t).split(' ');
        let kind = parts[0];
        let year = parts[1] || '';
        let label = (kind === 'MID' ? 'Mid Year' : kind === 'END' ? 'End Year' : t);
        return year ? label + ' ' + year : label;
    }

    function formatFileSize(bytes) {
        if (bytes === null || bytes === undefined || bytes === '') return '-';
        let units = ['B', 'KB', 'MB', 'GB'];
        let v = Number(bytes);
        let i = 0;
        while (v >= 1024 && i < units.length - 1) { v /= 1024; i++; }
        return v.toFixed(i === 0 ? 0 : 1) + ' ' + units[i];
    }

    function approvalBadge(status) {
        if (status === 'approved') return '<span class="ipp-badge ipp-badge-success">Disetujui</span>';
        if (status === 'rejected') return '<span class="ipp-badge ipp-badge-danger">Ditolak</span>';
        return '<span class="ipp-badge ipp-badge-muted">Belum</span>';
    }

    function rangeTierAch(score) {
        if (score >= 5) return 'Exceed';
        if (score >= 3) return 'Achieve';
        return 'Not Achieve';
    }

    // Gap 0.01 agar batas antar skor tidak kembar (konsisten dengan editor Range).
    function rangeDecimals(values) {
        let d = 0;
        (values || []).forEach(function (v) {
            let n = parseFloat(String(v === null || v === undefined ? '' : v).replace(',', '.'));
            if (isNaN(n)) return;
            let s = String(n);
            let dot = s.indexOf('.');
            let dec = dot === -1 ? 0 : s.length - dot - 1;
            if (dec > d) d = dec;
        });
        return Math.max(2, d);
    }

    function rangeFmt(v, decimals) {
        let n = parseFloat(String(v === null || v === undefined ? '' : v).replace(',', '.'));
        if (isNaN(n)) return String(v === null || v === undefined ? '' : v);
        let s = n.toFixed(decimals);
        s = s.replace(/0+$/, '').replace(/\.$/, '');
        return s === '' ? '0' : s;
    }

    // Presisi desimal seragam mode Custom (0 bila semua bilangan bulat).
    function customTierDecimals(tiers) {
        let d = 0;
        (tiers || []).forEach(function (t) {
            [t.min_value, t.max_value].forEach(function (v) {
                if (v === null || v === undefined) return;
                let s = String(v).replace(',', '.');
                let dot = s.indexOf('.');
                let dec = dot === -1 ? 0 : s.length - dot - 1;
                if (dec > d) d = dec;
            });
        });
        return d;
    }

    function customFmtFixed(v, decimals) {
        let n = parseFloat(String(v === null || v === undefined ? '' : v).replace(',', '.'));
        if (isNaN(n)) return String(v === null || v === undefined ? '' : v);
        return n.toFixed(decimals);
    }

    function customTierText(t, dec) {
        let min = (t.min_value === null || t.min_value === undefined) ? null : parseFloat(String(t.min_value).replace(',', '.'));
        let max = (t.max_value === null || t.max_value === undefined) ? null : parseFloat(String(t.max_value).replace(',', '.'));
        if (dec === undefined || dec === null) {
            dec = customTierDecimals([t]);
        }
        if (t.match_type === 'eq') return '= ' + customFmtFixed(min, dec);
        if (t.match_type === 'gte') return '&gt; ' + customFmtFixed(min, dec);
        if (t.match_type === 'lte') return '&lt; ' + customFmtFixed(max, dec);
        if (t.match_type === 'between') return customFmtFixed(min, dec) + ' &ndash; ' + customFmtFixed(max, dec);
        return '';
    }

    // Separator + label agar operator "=" tidak dobel (mis. "1 => 7", "2 = 3").
    function rangeLabelSuffix(label) {
        let s = String(label === null || label === undefined ? '' : label);
        if (s.indexOf('&gt;') === 0 || s.indexOf('&lt;') === 0 || s.charAt(0) === '>' || s.charAt(0) === '<') {
            return ' =' + s;
        }
        if (s.charAt(0) === '=') {
            return ' ' + s;
        }
        return ' = ' + s;
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
            let tHtml = '<div class="ap-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
            tiers.forEach(function (t) {
                tHtml += '<div class="text-nowrap"><strong>' + t.score + '</strong>' + rangeLabelSuffix(escapeHtml(t.label || '')) + '</div>';
            });
            tHtml += '</div>';
            return tHtml;
        }
        if (tiers.some(function (t) { return ['gte', 'lte', 'eq', 'between'].indexOf(t.match_type) !== -1; })) {
            tiers.sort(function (a, b) { return (a.score || 0) - (b.score || 0); });
            let customDec = customTierDecimals(tiers);
            let cHtml = '<div class="ap-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
            tiers.forEach(function (t) {
                cHtml += '<div class="text-nowrap"><strong>' + t.score + '</strong>' + rangeLabelSuffix(customTierText(t, customDec)) + '</div>';
            });
            cHtml += '</div>';
            return cHtml;
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
        let html = '<div class="ap-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
        tiers.forEach(function (t) {
            let range = (t.min_value === null || t.min_value === undefined)
                ? ('&lt; ' + rangeFmt(t.max_value, dec))
                : ((t.max_value === null || t.max_value === undefined)
                    ? ('&gt; ' + rangeFmt(t.min_value, dec))
                    : (rangeFmt((first !== null && parseFloat(String(t.min_value)) === first) ? t.min_value : t.min_value + eps, dec) + ' &ndash; ' + rangeFmt(t.max_value, dec)));
            html += '<div class="text-nowrap"><strong>' + t.score + '</strong>' + rangeLabelSuffix(range) + '</div>';
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

    // Presisi skala Range per KPI: null = bebas; 0 = wajib bilangan bulat.
    function apScaleDecimals(dtl) {
        if (!dtl || dtl.classification !== 'Range' || isTextRange(dtl)) return null;
        let tiers = dtl.range_tiers;
        if (!tiers) return null;
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        if (!tiers.length) return null;
        return customTierDecimals(tiers);
    }

    // Pembulatan half-away-from-zero (mirror PHP round).
    function apRoundTo(val, decimals) {
        let n = parseFloat(String(val === null || val === undefined ? '' : val).trim().replace(',', '.'));
        if (isNaN(n)) return null;
        let f = Math.pow(10, decimals);
        let r = Math.round(Math.abs(n) * f) / f;
        return (n < 0 ? -1 : 1) * r;
    }

    function apFmtScaled(val, decimals) {
        let r = apRoundTo(val, decimals);
        if (r === null) return '';
        return decimals > 0 ? r.toFixed(decimals) : String(r);
    }

    // Apakah nilai (setelah pembulatan) masuk salah satu tier skor Range.
    function rangeMatchesAnyTier(tiers, actual) {
        if (!tiers) return true;
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        if (!tiers.length) return true;
        if (tiers.some(function (t) { return t.match_type === 'equals'; })) return true;
        let val = function (v) {
            if (v === null || v === undefined) return null;
            let n = parseFloat(String(v).replace(',', '.'));
            return isNaN(n) ? null : n;
        };
        let num = val(actual);
        if (num === null) return true;
        let custom = tiers.some(function (t) { return ['gte', 'lte', 'eq', 'between'].indexOf(t.match_type) !== -1; });
        if (custom) {
            return tiers.some(function (t) {
                let min = val(t.min_value);
                let max = val(t.max_value);
                if (t.match_type === 'eq') return min !== null && num === min;
                if (t.match_type === 'gte') return min !== null && num > min;
                if (t.match_type === 'lte') return max !== null && num < max;
                if (t.match_type === 'between') return min !== null && max !== null && num >= min && num <= max;
                return false;
            });
        }
        let firstMin = null;
        tiers.forEach(function (t) {
            let m = val(t.min_value);
            if (m !== null && (firstMin === null || m < firstMin)) firstMin = m;
        });
        return tiers.some(function (t) {
            let min = val(t.min_value);
            let max = val(t.max_value);
            if (min === null) {
                return max === null || num < max;
            }
            let okMin = num > min || (firstMin !== null && min === firstMin && num >= min);
            let okMax = max === null || num <= max;
            return okMin && okMax;
        });
    }

    // Atribut validasi "harus dapat skor" untuk input numerik KPI Range.
    function rangeTierAttr(dtl) {
        if (!dtl || dtl.classification !== 'Range' || !dtl.range_tiers || isTextRange(dtl)) return '';
        let tiers = Array.isArray(dtl.range_tiers) ? dtl.range_tiers : Object.values(dtl.range_tiers);
        if (!tiers.length) return '';
        let json = JSON.stringify(tiers).replace(/'/g, '&#39;');
        let noUrut = (dtl.no_urut === undefined || dtl.no_urut === null) ? '' : dtl.no_urut;
        return ' data-tiers=\'' + json + '\' data-no-urut=\'' + noUrut + '\'';
    }

    function inputTiers($inp) {
        let raw = $inp.attr('data-tiers');
        if (!raw) return null;
        try { return JSON.parse(raw); } catch (e) { return null; }
    }

    function achievementBadge(ach) {
        if (ach === 'Exceed') return '<span class="ipp-badge" style="background:#c8e6c9;color:#1b5e20;">Exceed</span>';
        if (ach === 'Achieve') return '<span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span>';
        if (ach === 'Not Achieve') return '<span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">Not Achieve</span>';
        return '<span class="text-muted">-</span>';
    }

    function formatPercent(v) {
        if (v === null || v === undefined || v === '') return '-';
        let n = parseFloat(v);
        if (isNaN(n)) return '-';
        return Math.round(n * 100) + '%';
    }

    // === LIST ===
    let apListPage = 1;
    let apListSearch = '';

    function loadList() {
        $('#apList').html('<div class="text-center text-muted py-3">Memuat data...</div>');
        $.get("{{ url('/hr/ipp-user-approval/list') }}", {
            page: apListPage,
            per_page: 10,
            search: apListSearch
        }, function (res) {
            renderList(res.data || []);
            renderPagination(res.meta || {});
        }).fail(function () {
            $('#apList').html('<div class="text-center text-muted py-3">Gagal memuat data.</div>');
            $('#apPagination').html('');
        });
    }

    function renderPagination(meta) {
        let total = parseInt(meta.total || 0);
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        if (total === 0) {
            $('#apPagination').html('');
            return;
        }
        let prev = page > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 ap-page-btn" data-page="' + (page - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage ? '<button class="btn btn-sm btn-outline-primary mr-1 ap-page-btn" data-page="' + (page + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        $('#apPagination').html(
            '<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem;">Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' peserta)</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.ap-page-btn', function () {
        apListPage = parseInt($(this).data('page'));
        loadList();
    });

    $('#apBtnSearch').on('click', function () {
        apListPage = 1;
        apListSearch = $('#apSearch').val().trim();
        loadList();
    });

    $('#apBtnReset').on('click', function () {
        $('#apSearch').val('');
        apListPage = 1;
        apListSearch = '';
        loadList();
    });

    $('#apSearch').on('keydown', function (e) {
        if (e.key === 'Enter') {
            $('#apBtnSearch').trigger('click');
        }
    });

    function renderList(rows) {
        if (!rows.length) {
            $('#apList').html('<div class="text-center text-muted py-3">Tidak ada IPP yang menunggu approval Anda.</div>');
            return;
        }

        function progBadge(info) {
            return info.filled >= info.total
                ? '<span class="ipp-badge ipp-badge-success">' + info.filled + '/' + info.total + '</span>'
                : (info.filled > 0 ? '<span class="ipp-badge ipp-badge-warning">' + info.filled + '/' + info.total + '</span>'
                    : '<span class="ipp-badge ipp-badge-muted">' + info.filled + '/' + info.total + '</span>');
        }

        let bodyHtml = '';
        let noIdx = 0;

        rows.forEach(function (r) {
            let karyawan = r.karyawan || [];
            karyawan.forEach(function (k) {
                let tahapKeys = Object.keys(k.tahapan || {});
                let rowspan = Math.max(tahapKeys.length, 1);
                noIdx++;
                tahapKeys.forEach(function (t, ti) {
                    let isFirst = ti === 0;
                    let info = k.tahapan[t] || {};
                    let needAction = false;
                    if (k.my_role && info.filled >= info.total) {
                        if (k.my_role === 'associate_manager') {
                            needAction = info.approval_associate_manager === null;
                        } else if (info.two_stage || k.has_am) {
                            needAction = info.approval_associate_manager === 'approved' && info.approval_manager === null;
                        } else {
                            needAction = info.approval_manager === null;
                        }
                    }
                    let rowStyle = needAction ? ' style="background:#fff3cd !important;"' : '';
                    let dOpen = info.diskusi_open || 0;
                    let dTotal = info.diskusi_total || 0;
                    let diskusiCell = apDiskusiCellHtml(dOpen, dTotal);
                    let rvFilled = info.reviewed_filled || 0;
                    let rvTotal = info.reviewed_total || 0;
                    let revEmpCell;
                    if (rvFilled >= rvTotal && rvFilled > 0) {
                        revEmpCell = '<span class="ipp-badge ipp-badge-success" title="' + escapeHtml(info.reviewed_by_label || info.reviewed_by || '') + '">Sudah direview</span>';
                    } else if (rvFilled > 0) {
                        revEmpCell = '<span class="ipp-badge ipp-badge-warning">' + rvFilled + '/' + rvTotal + '</span>';
                    } else {
                        revEmpCell = '<span class="text-muted">-</span>';
                    }

                    bodyHtml += '<tr' + rowStyle + ' data-oto="' + k.id + '" data-tahapan="' + escapeHtml(t) + '">' +
                        (isFirst
                            ? '<td class="text-center" rowspan="' + rowspan + '">' + noIdx + '</td>' +
                              '<td style="white-space:nowrap;" rowspan="' + rowspan + '"><strong>' + escapeHtml(r.periode || '-') + '</strong></td>' +
                              '<td rowspan="' + rowspan + '">' +
                                  '<div><strong>' + escapeHtml(r.dept || '-') + '</strong></div>' +
                                  '<div style="font-size:.78rem; color:#6c757d;">' + escapeHtml(r.position || '-') + '</div>' +
                                  '<div style="font-size:.72rem; color:#888;">Dibuat: ' + escapeHtml(r.uploaded_by || '-') + '</div>' +
                              '</td>' +
                              '<td class="text-center" rowspan="' + rowspan + '"><span class="badge badge-secondary">' + escapeHtml(r.golongan || '-') + '</span></td>' +
                              '<td rowspan="' + rowspan + '">' +
                                  '<div><strong>' + escapeHtml(k.nama_yang_bersangkutan || '-') + '</strong></div>' +
                                  '<div style="font-size:.78rem; color:#6c757d;">' + escapeHtml(k.nik_yang_bersangkutan || '-') + '</div>' +
                              '</td>'
                            : '') +
                        '<td class="text-center">' + revEmpCell + '</td>' +
                        '<td class="text-center font-weight-bold" style="white-space:nowrap;">' + escapeHtml(tahapanLabel(t)) + '</td>' +
                        '<td class="text-center">' + progBadge(info) + '</td>' +
                        '<td class="text-center">' + (info.two_stage || k.has_am ? approvalBadge(info.approval_associate_manager) : '<span class="text-muted">-</span>') + '</td>' +
                        '<td class="text-center">' + approvalBadge(info.approval_manager) + '</td>' +
                        '<td class="text-center ap-cell-diskusi">' + diskusiCell + '</td>' +
                        '<td class="text-center">' +
                            '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                            '<button class="btn btn-sm btn-warning text-dark btn-ap-isi" data-id="' + r.ipp_id + '" data-oto="' + k.id + '" data-tahapan="' + t + '" style="white-space:nowrap;">' +
                            '<i class="fa fa-signature text-dark mr-1"></i> Approval' +
                            '</button>' +
                            '<div class="btn-group">' +
                            '<button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space:nowrap;">PDF</button>' +
                            '<div class="dropdown-menu dropdown-menu-right">' +
                            (r.is_rubric
                                ? '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-user-approval/export-pdf-foreman') }}/' + r.ipp_id + '/' + encodeURIComponent(k.id) + '?tahapan=' + encodeURIComponent(t) + '">PDF ' + escapeHtml(tahapanLabel(t)) + ' ini</a>' +
                                  '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-user-approval/export-pdf-foreman') }}/' + r.ipp_id + '/' + encodeURIComponent(k.id) + '">PDF Mid+End (1 file)</a>'
                                : '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-user-approval/export-pdf') }}/' + k.id + '?tahapan=' + encodeURIComponent(t) + '">PDF ' + escapeHtml(tahapanLabel(t)) + ' ini</a>' +
                                  '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-user-approval/export-pdf') }}/' + k.id + '">PDF Mid+End (1 file)</a>') +
                            '</div></div>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                });
            });
        });

        let tableHtml = '<div class="table-responsive" style="overflow:visible;">' +
            '<table class="table table-hover table-bordered table-sm mb-0" style="font-size:.85rem;">' +
            '<thead>' +
            '<tr>' +
            '<th class="bg-primary text-center" style="color:white !important; width:40px; vertical-align:middle;">No</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Periode</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Dept &amp; Posisi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Gol.</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Karyawan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Reviewed by Employee</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Tahapan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review & Approval Associate Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review & Approval Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Diskusi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; width:190px; vertical-align:middle;">Aksi</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' + bodyHtml + '</tbody>' +
            '</table>' +
            '</div>';

        $('#apList').html(tableHtml);
    }

    // === DROPDOWN LOKAL (Karyawan / Tahapan) ===
    function buildLokalDropdown(kind, label, items) {
        let listHtml = items.length
            ? items.map(function (it) {
                return '<div class="hd-ms-item ap-lokal-item" data-kind="' + kind +
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
            '</div></div>';
    }

    $(document).on('click', '.ipp-lokal-dropdown .hd-ms-btn', function (e) {
        e.stopPropagation();
        let $wrap = $(this).closest('.ipp-lokal-dropdown');
        $('.ipp-lokal-dropdown').not($wrap).removeClass('open');
        $wrap.toggleClass('open');
        if ($wrap.hasClass('open')) {
            $wrap.find('.hd-ms-search').focus();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.ipp-lokal-dropdown').length) {
            $('.ipp-lokal-dropdown').removeClass('open');
        }
    });

    $(document).on('click', '.ap-lokal-item', function () {
        let $wrap = $(this).closest('.ipp-lokal-dropdown');
        let kind = $wrap.data('kind');
        if (kind === 'karyawan') {
            apOtoId = $(this).data('value');
            renderKaryawanPicker(apCurrentIpp);
        } else if (kind === 'tahapan') {
            apTahapan = $(this).data('value');
            renderTahapanPicker(apCurrentIpp);
        }
        $wrap.find('.hd-ms-label').text($(this).data('label'));
        $wrap.removeClass('open');
        renderApprovalBox();
        renderJbInfo(apCurrentIpp);
        renderDataTable(apCurrentIpp);
    });

    $(document).on('keyup', '.ipp-lokal-dropdown .hd-ms-search', function () {
        let $wrap = $(this).closest('.ipp-lokal-dropdown');
        let q = ($(this).val() || '').toLowerCase().trim();
        let $list = $wrap.find('.hd-ms-list');
        let shown = 0;
        $list.find('.ap-lokal-item').each(function () {
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

    // === DISKUSI SECTION ===
    function apDiskusiSectionRowHtml(x) {
        let isOpen = x.status === 'open';
        let badge = isOpen
            ? '<span class="ipp-badge ipp-badge-warning">Sedang berlangsung</span>'
            : '<span class="ipp-badge ipp-badge-success">Sudah dijawab</span>';
        let kpi = '<span style="font-weight:600;">KPI #' + (x.no_urut ? x.no_urut : '-') + '</span>' +
            (x.kpi ? '<div class="ap-diskusi-truncate text-muted" style="font-size:.75rem;" title="' + escapeHtml(x.kpi) + '">' + escapeHtml(x.kpi) + '</div>' : '');
        let nikKaryawan = x.nik_karyawan || x.nik_yang_bersangkutan || '';
        let namaKaryawan = x.nama_karyawan || x.nama_yang_bersangkutan || '-';
        let karyawan = '<div style="font-weight:600;">' + escapeHtml(namaKaryawan) + '</div>' +
            '<div class="text-muted" style="font-size:.75rem;">' + escapeHtml(nikKaryawan) + '</div>' +
            (x.nama_pengaju ? '<div style="font-size:.72rem; color:#888;">Tanya: ' + escapeHtml(x.nama_pengaju) + '</div>' : '');
        let tanggal = isOpen ? (x.created_at || '') : (x.answered_at || x.created_at || '');
        return '<tr class="ap-diskusi-row" data-id="' + x.id + '" data-ipp="' + x.ipp_id + '" data-oto="' + x.otorisasi_id + '" data-detail="' + (x.ipp_detail_id || '') + '" data-tahapan="' + escapeHtml(x.tahapan || '') + '">' +
            '<td class="text-center">' + badge + '</td>' +
            '<td style="white-space:nowrap;">' + escapeHtml(x.periode || '-') + '</td>' +
            '<td style="min-width:140px;">' + karyawan + '</td>' +
            '<td style="white-space:nowrap;">' + escapeHtml(tahapanLabel(x.tahapan)) + '</td>' +
            '<td style="min-width:160px;">' + kpi + '</td>' +
            '<td class="ap-diskusi-truncate" title="' + escapeHtml(x.pesan || '') + '">' + escapeHtml(x.pesan || '-') + '</td>' +
            '<td class="ap-diskusi-truncate" title="' + escapeHtml(x.jawaban || '') + '">' + escapeHtml(x.jawaban || '-') + '</td>' +
            '<td class="text-muted" style="font-size:.78rem; white-space:nowrap;">' + escapeHtml(tanggal || '-') + '</td>' +
            '</tr>';
    }

    function loadDiskusiSection() {
        $.get("{{ url('/hr/ipp-user-approval/diskusi-list') }}", {
            page: apDiskusiPage,
            per_page: apDiskusiPerPage
        }, function (res) {
            let items = res.data || [];
            if (!items.length) {
                $('#apDiskusiListBody').html('');
                $('#apDiskusiPagination').html('');
                $('#apDiskusiSection').hide();
                return;
            }
            let html = '';
            items.forEach(function (x) {
                html += apDiskusiSectionRowHtml(x);
            });
            $('#apDiskusiListBody').html(html);
            $('#apDiskusiSection').show();
            renderApDiskusiPagination(res.meta || {});
        }).fail(function () {
            $('#apDiskusiSection').hide();
        });
    }

    function renderApDiskusiPagination(meta) {
        let total = parseInt(meta.total || 0);
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        if (total === 0) {
            $('#apDiskusiPagination').html('');
            return;
        }
        let prev = page > 1
            ? '<button class="btn btn-sm btn-outline-primary mr-1 ap-diskusi-page-btn" data-page="' + (page - 1) + '">Prev</button>'
            : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage
            ? '<button class="btn btn-sm btn-outline-primary ap-diskusi-page-btn" data-page="' + (page + 1) + '">Next</button>'
            : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) {
            opts += '<option value="' + n + '"' + (n === apDiskusiPerPage ? ' selected' : '') + '>' + n + '</option>';
        });
        $('#apDiskusiPagination').html(
            '<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem;">Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' diskusi)</span>' +
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.82rem;">Tampilkan</span>' +
            '<select id="apDiskusiPerPageSel" class="form-control form-control-sm" style="width:auto; display:inline-block;">' + opts + '</select>' +
            '<span class="text-muted" style="font-size:.82rem;">per halaman</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.ap-diskusi-page-btn', function () {
        apDiskusiPage = parseInt($(this).data('page'));
        loadDiskusiSection();
    });

    $(document).on('change', '#apDiskusiPerPageSel', function () {
        apDiskusiPerPage = parseInt($(this).val()) || 10;
        apDiskusiPage = 1;
        loadDiskusiSection();
    });

    $(document).on('click', '.ap-diskusi-row', function () {
        let ippId = $(this).data('ipp');
        if (!ippId) return;
        apDiskusiAutoOpen = $(this).data('detail') || null;
        openApproval(ippId, $(this).data('oto') || null, $(this).data('tahapan') || null);
    });

    // === MODAL ===
    function openApproval(id, otoId, tahapan) {
        $.get("{{ url('/hr/ipp-user-approval') }}/" + id, function (res) {
            apCurrentIpp = res.data;
            apOtoId = otoId || ((res.data.otorisasi || [])[0]?.id ?? null);
            apTahapan = tahapan || ((res.data.tahapan || [])[0] || null);
            $('#apModalTitle').text('Isi & Approve Aktual IPP - ' + escapeHtml(res.data.periode || ''));
            renderInfo(res.data);
            renderKaryawanPicker(res.data);
            renderTahapanPicker(res.data);
            renderApprovalBox();
            renderJbInfo(res.data);
            renderDataTable(res.data);
            loadApDiskusi(id, function () {
                $('#apTableBody .btn-ap-diskusi').each(function () {
                    refreshApDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
                });
                if (apDiskusiAutoOpen) {
                    let dd = apDiskusiAutoOpen;
                    apDiskusiAutoOpen = null;
                    let openD = function () {
                        let $b = $('#apTableBody .btn-ap-diskusi[data-detail="' + dd + '"][data-tahapan="' + apTahapan + '"]');
                        if ($b.length) $b.trigger('click');
                    };
                    if ($('#apModal').hasClass('show')) {
                        openD();
                    } else {
                        $('#apModal').one('shown.bs.modal', openD);
                    }
                }
            });
            $('#apModal').modal('show');
        }).fail(function (xhr) {
            apDiskusiAutoOpen = null;
            AP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memuat data IPP.', 'error');
        });
    }

    function renderInfo(d) {
        let o = (d.otorisasi || []).find(function (x) { return String(x.id) === String(apOtoId); });
        $('#apInfo').html(
            '<strong>Periode:</strong> ' + escapeHtml(d.periode || '-') +
            ' &middot; <strong>Dept:</strong> ' + escapeHtml(d.dept || '-') +
            ' &middot; <strong>POSITION:</strong> ' + escapeHtml(d.position || '-') +
            ' &middot; <strong>GOLONGAN:</strong> ' + escapeHtml(d.golongan || '-') +
            (o ? '<br><strong>Peserta:</strong> ' + escapeHtml(o.nama_yang_bersangkutan || '') +
                ' (' + escapeHtml(o.nik_yang_bersangkutan || '') + ')' +
                (o.nama_manager || o.nik_manager
                    ? ' &middot; <strong>Manager:</strong> ' + escapeHtml(o.nama_manager || o.nik_manager)
                    : '') +
                (o.nama_associate_manager || o.nik_associate_manager
                    ? ' &middot; <strong>Assoc. Manager:</strong> ' + escapeHtml(o.nama_associate_manager || o.nik_associate_manager)
                    : '') : '')
        );
    }

    function renderKaryawanPicker(d) {
        let items = (d.otorisasi || []).map(function (o) {
            return {
                value: o.id,
                label: (o.nik_yang_bersangkutan || '') + ' - ' + (o.nama_yang_bersangkutan || ''),
                search: (o.nik_yang_bersangkutan || '') + ' ' + (o.nama_yang_bersangkutan || ''),
                html: '<span><strong>' + escapeHtml(o.nik_yang_bersangkutan) + '</strong> - ' + escapeHtml(o.nama_yang_bersangkutan || '') + '</span>'
            };
        });

        let current = (d.otorisasi || []).find(function (x) { return String(x.id) === String(apOtoId); });
        let label = current
            ? escapeHtml(current.nik_yang_bersangkutan) + ' - ' + escapeHtml(current.nama_yang_bersangkutan || '')
            : 'Pilih Karyawan...';

        $('#apKaryawanPicker').html(
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem; font-weight:600; white-space:nowrap;">Pilih Karyawan:</span>' +
            buildLokalDropdown('karyawan', label, items) +
            '</div>'
        );
    }

    function renderTahapanPicker(d) {
        let items = (d.tahapan || []).map(function (t) {
            return {
                value: t,
                label: tahapanLabel(t),
                search: tahapanLabel(t),
                html: '<span>' + tahapanLabel(t) + '</span>'
            };
        });

        let label = apTahapan ? tahapanLabel(apTahapan) : 'Pilih Tahapan...';

        $('#apTahapanPicker').html(
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem; font-weight:600; white-space:nowrap;">Pilih Tahapan:</span>' +
            buildLokalDropdown('tahapan', label, items) +
            '</div>'
        );
    }

    function apIsRubric(d) {
        return !!(d && d.template_type === 'rubric');
    }

    function renderJbInfo(d) {
        if (!d || !apIsRubric(d) || String(d.golongan || '').trim() !== '2-3' || !apOtoId) {
            $('#apJbWrap').hide();
            $('#apJumlahBawahan').val('').prop('disabled', false);
            return;
        }

        let o = (d.otorisasi || []).find(function (x) { return String(x.id) === String(apOtoId); });
        let jb = o ? o.jumlah_bawahan : null;
        let myStatus = myStageStatus(d);
        let lockInfo = (d.locking || {})[apTahapan] || {};
        let atasanLocked = !!lockInfo.atasan && !d.can_edit_locked;

        $('#apJumlahBawahan')
            .val(jb === null || jb === undefined ? '' : jb)
            .prop('disabled', myStatus === 'approved' || atasanLocked);
        $('#apJbWrap').show();
    }

    function apStatusRows(d) {
        return apIsRubric(d) ? (d.rubric || []) : (d.details || []);
    }

    function apRowValue(d, rowId, tahapan) {
        let map = apIsRubric(d) ? (d.nilai || {}) : (d.aktual || {});
        return ((map[apOtoId] || {})[rowId] || {})[tahapan] || {};
    }

    function currentApprovalStatus(d) {
        if (!apOtoId || !apTahapan) return null;
        let statuses = {};
        apStatusRows(d).forEach(function (dtl) {
            let v = apRowValue(d, dtl.id, apTahapan);
            if (v.approval_associate_manager) statuses[v.approval_associate_manager] = true;
            if (v.approval_manager) statuses[v.approval_manager] = true;
        });
        if (statuses.rejected) return 'rejected';
        if (statuses.approved) return 'approved';
        return null;
    }

    function myRole() {
        if (!apCurrentIpp) return null;
        let oto = (apCurrentIpp.otorisasi || []).find(function (x) { return String(x.id) === String(apOtoId); });
        return oto ? (oto.my_role || null) : null;
    }

    function myStageStatus(d) {
        let role = myRole();
        if (!role || !apTahapan) return null;
        let col = role === 'manager' ? 'approval_manager' : 'approval_associate_manager';
        let statuses = {};
        apStatusRows(d).forEach(function (dtl) {
            let v = apRowValue(d, dtl.id, apTahapan);
            if (v[col]) statuses[v[col]] = true;
        });
        if (statuses.rejected) return 'rejected';
        if (statuses.approved) return 'approved';
        return null;
    }

    function renderApprovalBox() {
        let d = apCurrentIpp;
        if (!d || !apOtoId || !apTahapan) {
            apAmWaiting = null;
            $('#apApprovalBox').html('');
            return;
        }
        let status = currentApprovalStatus(d);
        let myStatus = myStageStatus(d);
        let ti = ((d.approval_info || {})[apOtoId] || {})[apTahapan] || {};
        let info = '';

        function stageText(stage, label) {
            if (!stage || !stage.status) return '';
            let txt = (stage.status === 'approved' ? ' Disetujui' : ' Ditolak') + ' oleh <strong>' + escapeHtml(stage.by_label || '-') + '</strong>' +
                (stage.at ? ' pada ' + escapeHtml(stage.at) : '');
            return ' &middot; ' + label + ':' + txt;
        }
        info += stageText(ti.associate, 'Assoc. Manager');
        info += stageText(ti.manager, 'Manager');

        let myRole = null;
        let otoRow = null;
        (d.otorisasi || []).forEach(function (o) {
            if (String(o.id) === String(apOtoId) && o.my_role) {
                myRole = o.my_role;
                otoRow = o;
            }
        });
        let amNik = otoRow && otoRow.nik_associate_manager ? String(otoRow.nik_associate_manager).trim() : '';
        let mgrNik = otoRow && otoRow.nik_manager ? String(otoRow.nik_manager).trim() : '';
        let amNama = otoRow && otoRow.nama_associate_manager ? String(otoRow.nama_associate_manager).trim() : '';
        let hasAm = amNik !== '' && amNik !== mgrNik;
        let amApproved = !!(ti.associate && ti.associate.status === 'approved');
        let managerWaitingAm = myRole === 'manager' && hasAm && !amApproved;

        apAmWaiting = {
            waiting: managerWaitingAm,
            nik: amNik,
            nama: amNama,
            tahapan: apTahapan
        };

        let waitingAmHtml = '';
        if (managerWaitingAm) {
            let amLabel = amNama !== '' ? (amNama + ' (' + amNik + ')') : amNik;
            info += ' &middot; <span class="badge badge-warning" style="background:#ffe082;color:#7a5900;">Menunggu approval Assoc. Manager</span>';
            waitingAmHtml =
                '<div class="alert alert-warning mt-2 mb-0 py-2 px-3" style="font-size:.85rem;">' +
                '<i class="fa fa-clock-o"></i> Tahapan <strong>' + escapeHtml(tahapanLabel(apTahapan)) + '</strong> masih menunggu persetujuan Associate Manager' +
                (amLabel !== '' ? ': <strong>' + escapeHtml(amLabel) + '</strong>' : '') + '. ' +
                'Tombol <strong>Approve Tahapan</strong> dapat digunakan setelah Associate Manager menyetujui tahapan ini.' +
                '</div>';
        }

        $('#apApprovalBox').html(
            '<div class="p-2 rounded" style="background:#f5f5f5; font-size:.85rem; display:inline-block;">' +
            'Status Approval ' + escapeHtml(tahapanLabel(apTahapan)) + ': ' + approvalBadge(status) + info +
            '</div>' + waitingAmHtml
        );

        // Sinkronkan tombol (berdasar status PERAN user login, reset berdasar status agregat)
        let lockInfo = (d.locking || {})[apTahapan] || {};
        let atasanLocked = !!lockInfo.atasan && !d.can_edit_locked;
        let approvalLocked = !!lockInfo.batalkan_approval && !d.can_edit_locked;
        $('#btnSaveAktual').prop('disabled', myStatus === 'approved' || atasanLocked);
        $('#btnApproveTahapan').prop('disabled', myStatus === 'approved' || approvalLocked);
        $('#btnRejectTahapan').prop('disabled', myStatus === 'rejected' || approvalLocked);
        $('#btnResetApproval').prop('disabled', status === null || approvalLocked);

        // Note: editable saat belum di-approve, read-only setelah approve
        let myNote = null;
        if (myRole === 'associate_manager' && ti.associate) myNote = ti.associate.note;
        if (myRole === 'manager' && ti.manager) myNote = ti.manager.note;
        let noteReadonly = myStatus === 'approved' || myStatus === 'rejected';
        $('#apApprovalNote').prop('readonly', noteReadonly);
        $('#apApprovalNote').val(myNote === null || myNote === undefined ? '' : myNote);
    }

    function diskusiFor(detailId, tahapan) {
        return (apDiskusi || []).filter(function (x) {
            return x.ipp_detail_id == detailId && (x.tahapan === tahapan || (!x.tahapan && !tahapan));
        });
    }

    function apDiskusiCellHtml(open, total) {
        if (!total) return '<span class="text-muted">-</span>';
        return open > 0
            ? '<span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">? ' + open + ' menunggu</span>'
            : '<span class="ipp-badge ipp-badge-success">Dijawab</span>';
    }

    function openDiskusiCount(detailId, tahapan) {
        return diskusiFor(detailId, tahapan).filter(function (x) { return x.status === 'open'; }).length;
    }

    function diskusiAnswered(detailId, tahapan) {
        return diskusiFor(detailId, tahapan).some(function (x) { return x.status === 'answered'; });
    }

    function diskusiBadgeHtml(n) {
        return n > 0 ? ' <span class="badge badge-danger">' + n + '</span>' : '';
    }

    function refreshApDiskusiButton(detailId, tahapan) {
        let $btn = $('.btn-ap-diskusi[data-detail="' + detailId + '"][data-tahapan="' + tahapan + '"]');
        $btn.toggleClass('btn-success', diskusiAnswered(detailId, tahapan))
            .toggleClass('btn-outline-primary', !diskusiAnswered(detailId, tahapan))
            .html('? Diskusi' + diskusiBadgeHtml(openDiskusiCount(detailId, tahapan)));
    }

    function loadApDiskusi(ippId, cb) {
        if (!ippId) {
            apDiskusi = [];
            if (cb) cb();
            return;
        }
        $.get("{{ url('/hr/ipp-user-approval/diskusi') }}/" + ippId, function (res) {
            apDiskusi = res.data || [];
            if (cb) cb();
        }).fail(function () {
            apDiskusi = [];
            if (cb) cb();
        });
    }

    function renderApDiskusiThread(detailId, tahapan) {
        let items = diskusiFor(detailId, tahapan);
        let html = '';
        if (!items.length) {
            html = '<div class="text-muted" style="font-size:.85rem;">Belum ada pertanyaan diskusi untuk item ini.</div>';
        }
        items.forEach(function (x) {
            let badge = x.status === 'answered'
                ? '<span class="ipp-badge ipp-badge-success">Sudah dijawab</span>'
                : '<span class="ipp-badge ipp-badge-warning">Belum dijawab</span>';
            let canEditAnswer = (x.status === 'answered' && x.nik_penjawab === apMyNik) ? 1 : 0;
            html += '<div class="border rounded p-2 mb-2 ap-diskusi-item" data-id="' + x.id + '" style="border-color:#dee2e6 !important;">' +
                '<div class="d-flex justify-content-between align-items-start" style="font-size:.8rem;">' +
                '<div>' + badge +
                ' <span style="color:#4a148c; font-weight:600;">' + escapeHtml(x.nama_pengaju || '-') + '</span>' +
                ' <span class="text-muted">' + escapeHtml(x.created_at || '') + '</span>' +
                (x.tahapan ? ' <span class="text-muted">(' + escapeHtml(x.tahapan) + ')</span>' : '') +
                '</div>' +
                (canEditAnswer ? '<button type="button" class="btn btn-sm btn-outline-secondary btn-ap-diskusi-edit-answer" style="padding:.1rem .4rem; font-size:.72rem;">Edit Jawaban</button>' : '') +
                '</div>' +
                '<div class="mt-1" style="white-space:pre-wrap; font-size:.85rem;">' + escapeHtml(x.pesan) + '</div>';
            if (x.status === 'open' && x.jawaban) {
                html += '<div class="mt-2 p-2 rounded" style="background:#f5f5f5; font-size:.85rem;">' +
                    '<strong>Jawaban sebelumnya:</strong> ' + escapeHtml(x.jawaban) +
                    '<div class="text-muted" style="font-size:.75rem;">oleh ' + escapeHtml(x.nama_penjawab || '-') +
                    (x.answered_at ? ' pada ' + escapeHtml(x.answered_at) : '') + '</div></div>';
            }
            if (x.status === 'answered') {
                html += '<div class="mt-2 p-2 rounded" style="background:#e8f5e9; font-size:.85rem;">' +
                    '<strong>Jawaban:</strong> ' + escapeHtml(x.jawaban || '') +
                    '<div class="text-muted" style="font-size:.75rem;">oleh ' + escapeHtml(x.nama_penjawab || '-') +
                    (x.answered_at ? ' pada ' + escapeHtml(x.answered_at) : '') + '</div></div>';
                if (canEditAnswer) {
                    html += '<div class="ap-diskusi-edit-answer-form mt-1" style="display:none;">' +
                        '<textarea class="form-control form-control-sm ap-diskusi-jawaban-input" rows="2" style="font-size:.85rem;">' + escapeHtml(x.jawaban || '') + '</textarea>' +
                        '<button type="button" class="btn btn-sm btn-primary mt-1 btn-ap-diskusi-answer-save" data-id="' + x.id + '" style="white-space:nowrap;">Simpan Jawaban</button> ' +
                        '<button type="button" class="btn btn-sm btn-secondary mt-1 btn-ap-diskusi-answer-cancel">Batal</button>' +
                        '</div>';
                }
            } else {
                html += '<div class="mt-2">' +
                    '<textarea class="form-control form-control-sm ap-diskusi-jawab" rows="2" placeholder="Tulis jawaban Anda..." style="font-size:.85rem;"></textarea>' +
                    '<button type="button" class="btn btn-sm btn-primary mt-1 btn-ap-diskusi-jawab" data-id="' + x.id + '" style="white-space:nowrap;">Jawab &amp; Centang Sudah Dijawab</button>' +
                    '</div>';
            }
            html += '</div>';
        });
        $('#apDiskusiThread').html(html);
    }

    function renderAktualTable(d) {
        if (!apOtoId || !apTahapan) {
            $('#apTableHead').html('');
            $('#apTableBody').html('');
            $('#apAchLegend').html('');
            $('#apAchSummary').html('');
            apKaryawanMap = {};
            return;
        }

        let status = currentApprovalStatus(d);
        let myStatus = myStageStatus(d);
        let curOto = (d.otorisasi || []).find(function (x) { return String(x.id) === String(apOtoId); });
        let hideAch = !!(curOto && curOto.my_role === 'associate_manager');
        let lockInfo = (d.locking || {})[apTahapan] || {};
        let atasanLocked = !!lockInfo.atasan && !d.can_edit_locked;
        let approvalLocked = !!lockInfo.batalkan_approval && !d.can_edit_locked;

        let lockNotices = '';
        if (atasanLocked) {
            lockNotices += '<div class="alert alert-danger py-2 mb-1" style="font-size:.85rem;">' +
                'Kolom <strong>Aktual (Atasan)</strong> tahapan ' + escapeHtml(tahapanLabel(apTahapan)) +
                ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.</div>';
        }
        if (approvalLocked) {
            lockNotices += '<div class="alert alert-danger py-2 mb-1" style="font-size:.85rem;">' +
                '<strong>Approval</strong> tahapan ' + escapeHtml(tahapanLabel(apTahapan)) +
                ' sedang dikunci (LOCKING IPP). Aksi Approve/Reject/Batalkan Approval tidak tersedia. Hubungi Admin HR untuk membuka kunci.</div>';
        }
        if (lockNotices) {
            $('#apLockNotice').html(lockNotices).show();
        } else {
            $('#apLockNotice').hide().html('');
        }

        $('#apAchLegend').html(hideAch ?
            '<div class="mt-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">' +
            'Aktual (Departemen) diisi oleh <strong>HRD dan FAC</strong>.' +
            '</div>' :
            '<div class="mt-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">' +
            '<button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start" data-toggle="collapse" data-target="#apAchLegendBody"><i class="fa fa-chevron-down"></i> &nbsp;Keterangan Nilai Achievement</button>' +
            '<div id="apAchLegendBody" class="collapse mt-1">' +
            '<table class="table table-sm table-bordered mt-1 mb-1" style="font-size:.72rem;">' +
            '<thead><tr><th>Achievement</th><th class="text-center">Scale</th><th>Kriteria</th></tr></thead>' +
            '<tbody>' +
            '<tr><td><span class="ipp-badge" style="background:#c8e6c9;color:#1b5e20;">Exceed</span></td><td class="text-center">5</td><td>≥120% · Range skor 5</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span></td><td class="text-center">4</td><td>100%–119% · Range skor 4–3</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span></td><td class="text-center">5</td><td>Min/Max/Std</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">Not Achieve</span></td><td class="text-center">3 / 2 / 1</td><td>Lihat Score Scale · Range skor 2–1</td></tr>' +
            '</tbody></table>' +
            '<strong>Score Scale:</strong>' +
            '<table class="table table-sm table-bordered mt-1 mb-1" style="font-size:.72rem;">' +
            '<thead><tr><th>Tipe Target</th><th class="text-center">Scale 5</th><th class="text-center">Scale 4</th><th class="text-center">Scale 3</th><th class="text-center">Scale 2</th><th class="text-center">Scale 1</th></tr></thead>' +
            '<tbody>' +
            '<tr><td>High Is Good/Bad</td><td class="text-center">≥120%</td><td class="text-center">100%–119%</td><td class="text-center">80%–99%</td><td class="text-center">70%–79%</td><td class="text-center">≤69%</td></tr>' +
            '<tr><td>Min/Max</td><td class="text-center">96%–100%</td><td class="text-center">91%–95%</td><td class="text-center">81%–90%</td><td class="text-center">71%–80%</td><td class="text-center">≤70%</td></tr>' +
            '<tr><td>Range</td><td colspan="5" class="text-center">Skala dinamis 5 tier per KPI (dari hr_ipp_master_target): skor 5 = Exceed, 4–3 = Achieve, 2–1 = Not Achieve</td></tr>' +
            '<tr><td>Standard Score</td><td colspan="2" class="text-center">= Target → Achieve (100% · Scale 5)</td><td colspan="3" class="text-center">≠ Target (lebih tinggi/rendah) → Not Achieve (0% · Scale 1)</td></tr>' +
            '</tbody></table>' +
            '<strong>PA IPP:</strong>' +
            '<table class="table table-sm table-bordered mt-1 mb-1" style="font-size:.72rem;">' +
            '<tbody>' +
            '<tr><th>Predikat</th><th class="text-center">A</th><th class="text-center">B</th><th class="text-center">C</th><th class="text-center">D</th><th class="text-center">E</th></tr>' +
            '<tr><td>Nilai</td><td class="text-center">≥4,2</td><td class="text-center">≥3,8</td><td class="text-center">≥3</td><td class="text-center">≥2</td><td class="text-center">≥1</td></tr>' +
            '</tbody></table>' +
            '<div>Total SCORE = rata-rata Score Scale.</div>' +
            '</div>' +
            '<div style="margin-top:4px;">Aktual (Departemen) diisi oleh <strong>HRD dan FAC</strong>.</div>' +
            '</div>');

        let thead = '<tr>' +
            '<th style="min-width:40px;">#</th>' +
            '<th style="min-width:160px;">Objective</th>' +
            '<th style="min-width:160px;">Key Performance Area (KPA)</th>' +
            '<th style="min-width:200px;">Key Performance Indicator (KPI)</th>' +
            '<th style="min-width:110px;">Classification</th>' +
            '<th style="min-width:90px;">Unit Of Measurement</th>' +
            '<th class="text-center" style="min-width:70px;">Weight</th>' +
            '<th class="text-center" style="min-width:70px;">Target</th>' +
            '<th style="min-width:110px;">Due date</th>' +
            '<th class="text-center" style="min-width:90px;">Review</th>' +
            '<th class="text-center ap-month-head" style="min-width:160px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Aktual (Departemen)</th>' +
            '<th class="text-center ap-month-head" style="min-width:150px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Aktual (Karyawan)</th>' +
            '<th class="text-center ap-month-head" style="min-width:160px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Aktual (Atasan)' +
            '<br><button type="button" class="btn btn-xs btn-outline-info mt-1" id="btnSalinAktualKaryawan" style="font-size:.7rem; padding:.1rem .3rem;" ' + (myStatus === 'approved' || atasanLocked ? 'disabled' : '') + '>⧉ Salin dari Karyawan</button></th>' +
            (hideAch ? '' :
                '<th class="text-center" style="min-width:110px;">Achievement Category</th>' +
                '<th class="text-center" style="min-width:90px;">Achievement Score</th>' +
                '<th class="text-center" style="min-width:80px;">Score Scale</th>') +
            '<th class="text-center" style="min-width:130px;">Dokumen Pendukung</th>' +
            '<th class="text-center" style="min-width:120px;">Diskusi</th>' +
            '</tr>';
        $('#apTableHead').html(thead);

        let tbody = '';
        (d.details || []).forEach(function (dtl) {
            let v = ((d.aktual[apOtoId] || {})[dtl.id] || {})[apTahapan] || {};
            let target = dtl.target;
            let aktual = v.aktual ?? '';
            let aktualVarchar = v.aktual_varchar ?? '';
            let aktualAtasan = v.aktual_atasan ?? '';
            let aktualAtasanVarchar = v.aktual_atasan_varchar ?? '';
            let byAtasan = v.aktual_atasan_by || '';
            let deptVal = v.aktual_departemen ?? '';
            let deptVarchar = v.aktual_departemen_varchar ?? '';
            let byDept = v.aktual_departemen_by || '';
            let tText = target !== null && target !== '' ? target : '-';
            let locked = myStatus === 'approved'
                || (dtl.classification !== 'Range' && (target === null || target === ''));
            let textRange = isTextRange(dtl);
            let karyawanDisplay = textRange ? aktualVarchar : aktual;
            let deptDisplay = textRange ? deptVarchar : deptVal;
            apKaryawanMap[dtl.id] = karyawanDisplay;
            let atasanCell;
            if (textRange) {
                let opts = '<option value="">- Pilih -</option>';
                rangeTextLabels(dtl).forEach(function (label) {
                    let sel = (String(label) === String(aktualAtasanVarchar)) ? ' selected' : '';
                    opts += '<option value="' + escapeHtml(label) + '"' + sel + '>' + escapeHtml(label) + '</option>';
                });
                atasanCell = '<select class="form-control form-control-sm ap-aktual-atasan-input" ' +
                    'data-detail="' + dtl.id + '" data-mode="equals" ' +
                    (locked || atasanLocked ? 'disabled' : '') + '>' + opts + '</select>';
            } else {
                let dec = apScaleDecimals(dtl);
                let decAttr = (dec === null) ? '' : ' data-decimals="' + dec + '"';
                let modeAttr = (dec === 0) ? 'inputmode="numeric"' : 'inputmode="decimal"';
                let displayAtasan = (dec === null) ? aktualAtasan : apFmtScaled(aktualAtasan, dec);
                atasanCell = '<input type="text" class="form-control form-control-sm ap-aktual-atasan-input" ' +
                    'data-detail="' + dtl.id + '" data-mode="number" value="' + escapeHtml(displayAtasan) + '" placeholder="' + (displayAtasan === '' ? 'wajib diisi' : '-') + '" ' + modeAttr + decAttr + rangeTierAttr(dtl) + ' ' +
                    (locked || atasanLocked ? 'disabled' : '') + '>';
            }
            tbody += '<tr>' +
                '<td class="text-center">' + dtl.no_urut + '</td>' +
                '<td>' + escapeHtml(dtl.objective || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpa || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpi) + '</td>' +
                '<td>' + escapeHtml(dtl.classification || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.uom || '-') + '</td>' +
                '<td class="text-center">' + (dtl.weight !== null && dtl.weight !== undefined && dtl.weight !== '' ? dtl.weight : '-') + '</td>' +
                '<td class="ap-target-cell">' + rangeScaleHtml(dtl, tText) + '</td>' +
                '<td>' + escapeHtml(dtl.due_date || '-') + '</td>' +
                '<td class="text-center">' + (v.review
                    ? '<span class="ipp-badge ipp-badge-success" title="' + escapeHtml(v.reviewed_by || '') + '">Sudah direview</span>'
                    : '<span class="text-muted">-</span>') + '</td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm" ' +
                'value="' + escapeHtml(deptDisplay) + '" placeholder="-" readonly>' +
                (byDept ? '<div class="text-muted" style="font-size:.85rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byDept) + '</div>' : '') +
                '</td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm" ' +
                'value="' + escapeHtml(karyawanDisplay) + '" placeholder="-" readonly></td>' +
                '<td class="text-center">' + atasanCell +
                (byAtasan ? '<div class="text-muted" style="font-size:.85rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byAtasan) + '</div>' : '') +
                '</td>' +
                (hideAch ? '' :
                    '<td class="text-center">' + achievementBadge(v.achievement_category || '') + '</td>' +
                    '<td class="text-center">' + formatPercent(v.achievement_score) + '</td>' +
                    '<td class="text-center" style="font-weight:600;">' + (v.score_scale !== null && v.score_scale !== undefined && v.score_scale !== '' ? v.score_scale : '-') + '</td>') +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-secondary btn-ap-bukti" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + apTahapan + '" style="white-space:nowrap;">Dokumen Pendukung</button></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm ' +
                (diskusiAnswered(dtl.id, apTahapan) ? 'btn-success' : 'btn-outline-primary') + ' btn-ap-diskusi" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + apTahapan + '" style="white-space:nowrap;">? Diskusi' +
                diskusiBadgeHtml(openDiskusiCount(dtl.id, apTahapan)) + '</button></td>' +
                '</tr>';
        });

        $('#apTableBody').html(tbody);

        let sumHtml = '';
        if (!hideAch && apOtoId && apTahapan) {
            let scales = [];
            (d.details || []).forEach(function (dtl) {
                let v2 = ((d.aktual[apOtoId] || {})[dtl.id] || {})[apTahapan] || {};
                if (v2.score_scale !== null && v2.score_scale !== undefined && v2.score_scale !== '') {
                    scales.push(parseFloat(v2.score_scale));
                }
            });
            if (scales.length) {
                let avg = scales.reduce(function (s, x) { return s + x; }, 0) / scales.length;
                avg = Math.round(avg * 100) / 100;
                let pa = avg >= 4.2 ? 'A' : avg >= 3.8 ? 'B' : avg >= 3 ? 'C' : avg >= 2 ? 'D' : 'E';
                sumHtml = '<div class="p-2" style="background:#fff8e1; border:1px solid #ffe082; border-radius:4px; font-size:.9rem;">' +
                    '<strong>' + escapeHtml(tahapanLabel(apTahapan)) + ' SCORE:</strong> ' + avg +
                    ' &nbsp;|&nbsp; <strong>PA IPP:</strong> <span class="ipp-badge" style="background:#fff3cd;color:#856404;">' + pa + '</span>' +
                    '</div>';
            }
        }
        $('#apAchSummary').html(sumHtml);
    }

    function renderDataTable(d) {
        if (apIsRubric(d)) {
            renderRubricTable(d);
            return;
        }

        $('#btnSaveAktual').text('Simpan Aktual');
        renderAktualTable(d);
    }

    // === IPP LEVEL 2-3 (RUBRIK): tabel Leader Assessment 1-5 ===
    function renderRubricTable(d) {
        $('#btnSaveAktual').text('Simpan Nilai');

        if (!apOtoId || !apTahapan) {
            $('#apTableHead').html('');
            $('#apTableBody').html('');
            $('#apAchLegend').html('');
            $('#apAchSummary').html('');
            apKaryawanMap = {};
            return;
        }

        let myStatus = myStageStatus(d);
        let lockInfo = (d.locking || {})[apTahapan] || {};
        let atasanLocked = !!lockInfo.atasan && !d.can_edit_locked;
        let approvalLocked = !!lockInfo.batalkan_approval && !d.can_edit_locked;

        let lockNotices = '';
        if (atasanLocked) {
            lockNotices += '<div class="alert alert-danger py-2 mb-1" style="font-size:.85rem;">' +
                'Kolom <strong>Nilai</strong> tahapan ' + escapeHtml(tahapanLabel(apTahapan)) +
                ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.</div>';
        }
        if (approvalLocked) {
            lockNotices += '<div class="alert alert-danger py-2 mb-1" style="font-size:.85rem;">' +
                '<strong>Approval</strong> tahapan ' + escapeHtml(tahapanLabel(apTahapan)) +
                ' sedang dikunci (LOCKING IPP). Aksi Approve/Reject/Batalkan Approval tidak tersedia. Hubungi Admin HR untuk membuka kunci.</div>';
        }
        if (lockNotices) {
            $('#apLockNotice').html(lockNotices).show();
        } else {
            $('#apLockNotice').hide().html('');
        }

        $('#apAchLegend').html(
            '<div class="mt-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">' +
            'Penilaian rubrik <strong>IPP Level 2-3</strong>: pilih skor <strong>Leader Assessment 1–5</strong> sesuai rubrik. ' +
            'Total SCORE = rata-rata nilai seluruh baris rubrik.' +
            '</div>'
        );

        let thead = '<tr>' +
            '<th class="text-center" style="min-width:55px;">NO</th>' +
            '<th style="min-width:140px;">AREA</th>' +
            '<th style="min-width:220px;">COMPETENCY BEHAVIOR</th>' +
            '<th style="min-width:220px;">KEY BEHAVIOR</th>' +
            '<th style="min-width:140px;">1</th>' +
            '<th style="min-width:140px;">2</th>' +
            '<th style="min-width:140px;">3</th>' +
            '<th style="min-width:140px;">4</th>' +
            '<th style="min-width:140px;">5</th>' +
            '<th class="text-center ap-month-head" style="min-width:120px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Nilai</th>' +
            '</tr>';
        $('#apTableHead').html(thead);

        let tbody = '';
        let sectionNow = null;
        let nilaiList = [];

        (d.rubric || []).forEach(function (row) {
            if (row.section && row.section !== sectionNow) {
                sectionNow = row.section;
                tbody += '<tr><td colspan="10" class="font-weight-bold" style="background:#ede7f6; color:#4a148c;">' +
                    escapeHtml(row.section) + '</td></tr>';
            }

            let v = ((d.nilai[apOtoId] || {})[row.id] || {})[apTahapan] || {};
            let nilai = v.nilai;
            if (nilai !== null && nilai !== undefined && nilai !== '') {
                nilaiList.push(parseFloat(nilai));
            }

            let locked = myStatus === 'approved' || atasanLocked;
            let opts = '<option value="">-</option>';
            for (let s = 1; s <= 5; s++) {
                opts += '<option value="' + s + '"' + (String(nilai) === String(s) ? ' selected' : '') + '>' + s + '</option>';
            }

            tbody += '<tr>' +
                '<td class="text-center"><strong>' + escapeHtml(row.no_urut || '') + '</strong></td>' +
                '<td>' + escapeHtml(row.area || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.78rem;">' + escapeHtml(row.competency_behavior || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.78rem;">' + escapeHtml(row.key_behavior || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.75rem; color:#555;">' + escapeHtml(row.kriteria_1 || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.75rem; color:#555;">' + escapeHtml(row.kriteria_2 || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.75rem; color:#555;">' + escapeHtml(row.kriteria_3 || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.75rem; color:#555;">' + escapeHtml(row.kriteria_4 || '') + '</td>' +
                '<td style="white-space:pre-wrap; font-size:.75rem; color:#555;">' + escapeHtml(row.kriteria_5 || '') + '</td>' +
                '<td class="text-center">' +
                '<select class="form-control form-control-sm ap-rubric-nilai" data-row="' + row.id + '"' + (locked ? ' disabled' : '') + '>' + opts + '</select>' +
                (v.filled_by ? '<div class="text-muted" style="font-size:.68rem;">' + escapeHtml(v.filled_by) + '</div>' : '') +
                '</td>' +
                '</tr>';
        });

        $('#apTableBody').html(tbody);

        let sumHtml = '';
        if (nilaiList.length) {
            let avg = nilaiList.reduce(function (s, x) { return s + x; }, 0) / nilaiList.length;
            avg = Math.round(avg * 100) / 100;
            sumHtml = '<div class="p-2" style="background:#fff8e1; border:1px solid #ffe082; border-radius:4px; font-size:.9rem;">' +
                '<strong>' + escapeHtml(tahapanLabel(apTahapan)) + ' SCORE:</strong> ' + avg +
                ' &nbsp;|&nbsp; <span class="text-muted" style="font-size:.8rem;">rata-rata ' + nilaiList.length + ' baris terisi dari ' + (d.rubric || []).length + ' baris</span>' +
                '</div>';
        }
        $('#apAchSummary').html(sumHtml);
    }

    // === EVENTS ===
    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal.show').length) {
            $('body').addClass('modal-open');
        }
        if (this.id === 'apDiskusiModal') {
            loadDiskusiSection();
        }
    });

    // Bulatkan input Aktual (Atasan) sesuai presisi skala, lalu pastikan nilainya dapat skor.
    $(document).on('blur', '.ap-aktual-atasan-input[data-mode="number"]', function () {
        let $inp = $(this);
        if ($inp.is(':disabled')) return;
        let val = $.trim($inp.val() || '');
        if (val === '') return;
        let dec = parseInt($inp.attr('data-decimals'), 10);
        if (!isNaN(dec)) {
            let rounded = apRoundTo(val, dec);
            if (rounded !== null) {
                val = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                $inp.val(val);
            }
        }
        let tiers = inputTiers($inp);
        if (tiers && !rangeMatchesAnyTier(tiers, val)) {
            AP_SWAL.fire('Peringatan', 'Aktual (Atasan) KPI #' + ($inp.attr('data-no-urut') || '-') + ' tidak masuk rentang skor mana pun.', 'warning');
            $inp.val('');
        }
    });

    $(document).on('click', '.btn-ap-isi', function () {
        openApproval($(this).data('id'), $(this).data('oto'), $(this).data('tahapan'));
    });

    $(document).on('click', '.btn-ap-diskusi', function () {
        apDiskusiDetailId = $(this).data('detail');
        apDiskusiTahapan = $(this).data('tahapan');
        let dtl = (apCurrentIpp.details || []).find(function (d) { return d.id == apDiskusiDetailId; });
        $('#apDiskusiTitle').text('Diskusi - KPI #' + (dtl ? dtl.no_urut : apDiskusiDetailId));
        renderApDiskusiThread(apDiskusiDetailId, apDiskusiTahapan);
        $('#apDiskusiModal').modal('show');
    });

    $(document).on('click', '.btn-ap-bukti', function () {
        let detailId = $(this).data('detail');
        let tahapan = $(this).data('tahapan');
        if (!apOtoId) return;
        let dtl = (apCurrentIpp.details || []).find(function (d) { return d.id == detailId; });
        $('#apBuktiTitle').text('Dokumen Pendukung - KPI #' + (dtl ? dtl.no_urut : detailId) +
            ' (' + tahapanLabel(tahapan) + ')');
        $('#apBuktiList').html('<div class="text-center text-muted py-3">Memuat data...</div>');
        $('#apBuktiModal').modal('show');
        $.get("{{ url('/hr/ipp-bukti/list') }}", {
            ipp_otorisasi_id: apOtoId,
            ipp_detail_id: detailId,
            tahapan: tahapan
        }, function (res) {
            let items = res.data || [];
            if (!items.length) {
                $('#apBuktiList').html('<div class="text-muted" style="font-size:.85rem;">Belum ada dokumen pendukung untuk item ini.</div>');
                return;
            }
            let html = '';
            items.forEach(function (x) {
                html += '<div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">' +
                    '<div style="min-width:0;">' +
                    '<div style="font-weight:600; font-size:.85rem; word-break:break-all;">' + escapeHtml(x.original_name) + '</div>' +
                    '<div class="text-muted" style="font-size:.72rem;">' + formatFileSize(x.file_size) +
                    (x.created_at ? ' &middot; diunggah ' + escapeHtml(x.created_at) : '') + '</div>' +
                    '</div>' +
                    '<div class="text-nowrap" style="margin-left:.5rem;">' +
                    '<a href="' + x.view_url + '" target="_blank" class="btn btn-sm btn-outline-primary" style="white-space:nowrap;">Lihat</a> ' +
                    '<a href="' + x.download_url + '" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" style="white-space:nowrap;">Download</a>' +
                    '</div></div>';
            });
            $('#apBuktiList').html(html);
        }).fail(function () {
            $('#apBuktiList').html('<div class="text-center text-danger py-2">Gagal memuat daftar dokumen pendukung.</div>');
        });
    });

    $(document).on('click', '.btn-ap-diskusi-edit-answer', function () {
        $(this).closest('.ap-diskusi-item').find('.ap-diskusi-edit-answer-form').toggle();
    });

    $(document).on('click', '.btn-ap-diskusi-answer-cancel', function () {
        $(this).closest('.ap-diskusi-item').find('.ap-diskusi-edit-answer-form').hide();
    });

    $(document).on('click', '.btn-ap-diskusi-answer-save', function () {
        let $btn = $(this);
        let id = $btn.data('id');
        let $item = $btn.closest('.ap-diskusi-item');
        let jawaban = $.trim($item.find('.ap-diskusi-jawaban-input').val());
        if (!jawaban) {
            AP_SWAL.fire('Peringatan', 'Jawaban tidak boleh kosong.', 'warning');
            return;
        }
        $btn.prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: "{{ url('/hr/ipp-user-approval/diskusi/update') }}/" + id,
            type: 'POST',
            data: { jawaban: jawaban },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $btn.prop('disabled', false).text('Simpan Jawaban');
                let ippId = apCurrentIpp ? apCurrentIpp.ipp_id : null;
                loadApDiskusi(ippId, function () {
                    renderApDiskusiThread(apDiskusiDetailId, apDiskusiTahapan);
                    refreshApDiskusiButton(apDiskusiDetailId, apDiskusiTahapan);
                });
                loadDiskusiSection();
                AP_SWAL.fire('Berhasil', res.message || 'Jawaban diperbarui.', 'success');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Simpan Jawaban');
                AP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui jawaban.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-ap-diskusi-jawab', function () {
        let $btn = $(this);
        let id = $btn.data('id');
        let $item = $btn.closest('.ap-diskusi-item');
        let jawaban = $.trim($item.find('.ap-diskusi-jawab').val());
        if (!jawaban) {
            AP_SWAL.fire('Peringatan', 'Tulis jawaban terlebih dahulu.', 'warning');
            return;
        }
        $btn.prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: "{{ url('/hr/ipp-user-approval/diskusi/answer') }}/" + id,
            type: 'POST',
            data: { jawaban: jawaban },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $btn.prop('disabled', false).text('Jawab & Centang Sudah Dijawab');
                let ippId = apCurrentIpp ? apCurrentIpp.ipp_id : null;
                loadApDiskusi(ippId, function () {
                    renderApDiskusiThread(apDiskusiDetailId, apDiskusiTahapan);
                    refreshApDiskusiButton(apDiskusiDetailId, apDiskusiTahapan);
                });
                loadDiskusiSection();
                AP_SWAL.fire('Berhasil', res.message || 'Diskusi dijawab.', 'success');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Jawab & Centang Sudah Dijawab');
                AP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan jawaban.', 'error');
            }
        });
    });

    $('#btnSaveAktual').on('click', function () {
        if (!apCurrentIpp || !apOtoId || !apTahapan) return;

        if (apIsRubric(apCurrentIpp)) {
            let jumlahBawahan = $.trim($('#apJumlahBawahan').val());
            if (jumlahBawahan === '') {
                AP_SWAL.fire('Peringatan', 'Isi Jumlah Bawahan Langsung (bawahan peserta) terlebih dahulu.', 'warning');
                return;
            }

            let nilaiValues = [];
            $('#apTableBody .ap-rubric-nilai:not(:disabled)').each(function () {
                let val = $.trim($(this).val() || '');
                nilaiValues.push({
                    nik: String(apOtoId),
                    hr_ipp_foreman_id: $(this).data('row'),
                    tahapan: apTahapan,
                    nilai: val === '' ? null : parseInt(val, 10)
                });
            });

            $('#btnSaveAktual').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ url('/hr/ipp-user-approval/save-aktual') }}/" + apCurrentIpp.ipp_id,
                type: 'POST',
                data: {
                    values: nilaiValues,
                    nik: String(apOtoId),
                    jumlah_bawahan: jumlahBawahan
                },
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function (res) {
                    $('#btnSaveAktual').prop('disabled', false).text('Simpan Nilai');
                    AP_SWAL.fire('Berhasil', res.message || 'Nilai berhasil disimpan.', 'success');
                    loadApprovalData();
                },
                error: function (xhr) {
                    $('#btnSaveAktual').prop('disabled', false).text('Simpan Nilai');
                    AP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan nilai.', 'error');
                }
            });
            return;
        }

        let values = [];
        let invalidKpi = null;
        $('#apTableBody .ap-aktual-atasan-input:not(:disabled)').each(function () {
            let $inp = $(this);
            let val = $.trim($inp.val() || '');
            let isText = $inp.data('mode') === 'equals';
            if (!isText && val !== '') {
                let dec = parseInt($inp.attr('data-decimals'), 10);
                if (!isNaN(dec)) {
                    let rounded = apRoundTo(val, dec);
                    if (rounded !== null) {
                        val = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                        $inp.val(val);
                    }
                }
                let tiers = inputTiers($inp);
                if (tiers && !rangeMatchesAnyTier(tiers, val)) {
                    invalidKpi = $inp.attr('data-no-urut') || '-';
                    return false;
                }
            }
            values.push({
                otorisasi_id: apOtoId,
                detail_id: $inp.data('detail'),
                tahapan: apTahapan,
                aktual_atasan: (!isText && val !== '') ? val : null,
                aktual_atasan_varchar: (isText && val !== '') ? val : null
            });
        });

        if (invalidKpi !== null) {
            AP_SWAL.fire('Peringatan', 'Aktual (Atasan) KPI #' + invalidKpi + ' tidak masuk rentang skor mana pun.', 'warning');
            return;
        }

        if (!values.length) {
            AP_SWAL.fire('Peringatan', 'Tidak ada data AKTUAL untuk disimpan.', 'warning');
            return;
        }

        $('#btnSaveAktual').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ url('/hr/ipp-user-approval/save-aktual') }}/" + apCurrentIpp.ipp_id,
            type: 'POST',
            data: { values: values },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $('#btnSaveAktual').prop('disabled', false).text('Simpan Aktual');
                AP_SWAL.fire('Berhasil', res.message || 'Data AKTUAL berhasil disimpan.', 'success');
                loadApprovalData();
            },
            error: function (xhr) {
                $('#btnSaveAktual').prop('disabled', false).text('Simpan Aktual');
                let msg = xhr.responseJSON?.message || 'Gagal menyimpan data AKTUAL.';
                AP_SWAL.fire('Error', msg, 'error');
            }
        });
    });

    $(document).on('click', '#btnSalinAktualKaryawan', function () {
        if (!apCurrentIpp || !apOtoId || !apTahapan) return;

        let filled = 0;
        $('#apTableBody .ap-aktual-atasan-input:not(:disabled)').each(function () {
            let $inp = $(this);
            if ($.trim($inp.val()) === '') {
                let karyawan = apKaryawanMap[$inp.data('detail')];
                if (karyawan !== undefined && karyawan !== null && String(karyawan).trim() !== '') {
                    let val = String(karyawan).trim();
                    let dec = parseInt($inp.attr('data-decimals'), 10);
                    if (!isNaN(dec)) {
                        let rounded = apRoundTo(val, dec);
                        if (rounded !== null) {
                            val = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                        }
                    }
                    let tiers = inputTiers($inp);
                    if (tiers && !rangeMatchesAnyTier(tiers, val)) return;
                    $inp.val(val);
                    filled++;
                }
            }
        });

        if (filled > 0) {
            AP_SWAL.fire('Berhasil', filled + ' kolom Aktual (Atasan) diisi dari Aktual (Karyawan).', 'success');
        } else {
            AP_SWAL.fire('Info', 'Tidak ada kolom Aktual (Atasan) kosong untuk diisi, atau nilai Aktual (Karyawan) masih kosong.', 'info');
        }
    });

    function loadApprovalData() {
        $.get("{{ url('/hr/ipp-user-approval') }}/" + apCurrentIpp.ipp_id, function (res) {
            apCurrentIpp = res.data;
            renderInfo(res.data);
            renderKaryawanPicker(res.data);
            renderTahapanPicker(res.data);
            renderApprovalBox();
            renderJbInfo(res.data);
            renderDataTable(res.data);
            loadList();
        }).fail(function () {
            AP_SWAL.fire('Error', 'Gagal memuat ulang data.', 'error');
        });
    }

    function sendApproval(action, msgConfirm) {
        if (!apCurrentIpp || !apOtoId || !apTahapan) return;

        if (action === 'approve' || action === 'reject') {
            let note = $.trim($('#apApprovalNote').val());
            if (note === '') {
                AP_SWAL.fire('Note wajib diisi', 'Isi Note / Catatan Approval terlebih dahulu. Jika tidak ada catatan, isi "-".', 'warning');
                return;
            }
            if (apIsRubric(apCurrentIpp) && $.trim($('#apJumlahBawahan').val()) === '') {
                AP_SWAL.fire('Jumlah Bawahan Langsung wajib diisi', 'Isi Jumlah Bawahan Langsung (bawahan peserta) terlebih dahulu sebelum Approve/Reject Tahapan.', 'warning');
                return;
            }
        }

        AP_SWAL.fire({
            title: msgConfirm.title,
            text: msgConfirm.text,
            icon: msgConfirm.icon || 'question',
            showCancelButton: true,
            confirmButtonText: msgConfirm.confirm,
        }).then((r) => {
            if (!r.isConfirmed) return;

            let values = [];
            let invalidKpi = null;

            if (apIsRubric(apCurrentIpp)) {
                $('#apTableBody .ap-rubric-nilai:not(:disabled)').each(function () {
                    let val = $.trim($(this).val() || '');
                    values.push({
                        nik: String(apOtoId),
                        hr_ipp_foreman_id: $(this).data('row'),
                        nilai: val === '' ? null : parseInt(val, 10)
                    });
                });
            } else {
                $('#apTableBody .ap-aktual-atasan-input:not(:disabled)').each(function () {
                    let $inp = $(this);
                    let val = $.trim($inp.val() || '');
                    let isText = $inp.data('mode') === 'equals';
                    if (!isText && val !== '') {
                        let dec = parseInt($inp.attr('data-decimals'), 10);
                        if (!isNaN(dec)) {
                            let rounded = apRoundTo(val, dec);
                            if (rounded !== null) {
                                val = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                            }
                        }
                        let tiers = inputTiers($inp);
                        if (tiers && !rangeMatchesAnyTier(tiers, val)) {
                            invalidKpi = $inp.attr('data-no-urut') || '-';
                            return false;
                        }
                    }
                    values.push({
                        detail_id: $inp.data('detail'),
                        aktual_atasan: (!isText && val !== '') ? val : null,
                        aktual_atasan_varchar: (isText && val !== '') ? val : null
                    });
                });
            }

            if (invalidKpi !== null) {
                AP_SWAL.fire('Peringatan', 'Aktual (Atasan) KPI #' + invalidKpi + ' tidak masuk rentang skor mana pun.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ url('/hr/ipp-user-approval/approve') }}/" + apCurrentIpp.ipp_id,
                type: 'POST',
                data: {
                    otorisasi_id: apOtoId,
                    nik: apIsRubric(apCurrentIpp) ? String(apOtoId) : null,
                    tahapan: apTahapan,
                    action: action,
                    note: $('#apApprovalNote').val(),
                    values: values,
                    jumlah_bawahan: apIsRubric(apCurrentIpp) ? $.trim($('#apJumlahBawahan').val()) : null,
                    _token: csrf()
                },
                success: function (res) {
                    AP_SWAL.fire('Berhasil', res.message || 'Approval diproses.', 'success');
                    loadApprovalData();
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Gagal memproses approval.';
                    if (msg.indexOf('belum disetujui Associate Manager') !== -1) {
                        AP_SWAL.fire({
                            icon: 'info',
                            title: 'Menunggu Approval Associate Manager',
                            text: msg
                        });
                        return;
                    }
                    AP_SWAL.fire('Error', msg, 'error');
                }
            });
        });
    }

    $('#btnApproveTahapan').on('click', function () {
        if (apAmWaiting && apAmWaiting.waiting) {
            let amLabel = apAmWaiting.nama !== '' ? (apAmWaiting.nama + ' (' + apAmWaiting.nik + ')') : apAmWaiting.nik;
            AP_SWAL.fire({
                icon: 'info',
                title: 'Menunggu Approval Associate Manager',
                html: 'Tahapan <strong>' + escapeHtml(tahapanLabel(apAmWaiting.tahapan)) + '</strong> belum disetujui Associate Manager' +
                    (amLabel !== '' ? ' <strong>' + escapeHtml(amLabel) + '</strong>' : '') + '.<br><br>' +
                    'Approval Manager dapat dilakukan setelah Associate Manager menyetujui tahapan ini. Silakan hubungi Associate Manager terlebih dahulu.'
            });
            return;
        }

        sendApproval('approve', {
            title: 'Approve tahapan ini?',
            text: 'Semua aktual KPI pada tahapan ' + tahapanLabel(apTahapan) + ' untuk karyawan ini akan disetujui.',
            confirm: 'Ya, Approve'
        });
    });

    $('#btnRejectTahapan').on('click', function () {
        sendApproval('reject', {
            title: 'Tolak tahapan ini?',
            text: 'Semua aktual KPI pada tahapan ' + tahapanLabel(apTahapan) + ' untuk karyawan ini akan ditolak.',
            icon: 'warning',
            confirm: 'Ya, Tolak'
        });
    });

    $('#btnResetApproval').on('click', function () {
        sendApproval('reset', {
            title: 'Batalkan approval?',
            text: 'Status approval tahapan ' + tahapanLabel(apTahapan) + ' akan direset dan aktual bisa diedit kembali.',
            icon: 'warning',
            confirm: 'Ya, Batalkan'
        });
    });

    // ================= REALTIME DISKUSI (WEBSOCKET) =================
    let apRtTimer = null;
    let apRtPending = {};
    let apRtSyncTimer = null;

    function apRtThreadItem(ev) {
        return {
            id: ev.id,
            ipp_detail_id: ev.ipp_detail_id,
            tahapan: ev.tahapan,
            nik_pengaju: ev.nik_pengaju,
            nama_pengaju: ev.nama_pengaju,
            pesan: ev.pesan,
            jawaban: ev.jawaban,
            nik_penjawab: ev.nik_penjawab,
            nama_penjawab: ev.nama_penjawab,
            status: ev.status,
            answered_at: ev.answered_at,
            created_at: ev.created_at
        };
    }

    function apRtRefreshThread() {
        let drafts = {};
        $('#apDiskusiThread .ap-diskusi-item').each(function () {
            let id = $(this).data('id');
            let $open = $(this).find('.ap-diskusi-jawab');
            let $edit = $(this).find('.ap-diskusi-jawaban-input');
            if ($open.length && $open.val()) {
                drafts[id] = $open.val();
            } else if ($edit.length && $(this).find('.ap-diskusi-edit-answer-form').is(':visible')) {
                drafts[id] = $edit.val();
            }
        });
        renderApDiskusiThread(apDiskusiDetailId, apDiskusiTahapan);
        Object.keys(drafts).forEach(function (id) {
            let $item = $('#apDiskusiThread .ap-diskusi-item[data-id="' + id + '"]');
            if (!$item.length) return;
            let $open = $item.find('.ap-diskusi-jawab');
            let $edit = $item.find('.ap-diskusi-jawaban-input');
            if ($open.length) {
                $open.val(drafts[id]);
            } else if ($edit.length) {
                $item.find('.ap-diskusi-edit-answer-form').show();
                $edit.val(drafts[id]);
            }
        });
    }

    function apRtSectionSync() {
        clearTimeout(apRtSyncTimer);
        apRtSyncTimer = setTimeout(function () { loadDiskusiSection(); }, 500);
    }

    function apRtToast(ev) {
        if (typeof Swal === 'undefined') return;
        if (String(ev.aktor_nik) === String(apMyNik)) return;
        let msg;
        if (ev.type === 'answered') {
            msg = (ev.edited ? 'Jawaban diskusi diperbarui' : 'Diskusi dijawab') +
                ' oleh ' + (ev.nama_penjawab || ev.nik_penjawab);
        } else if (ev.type === 'updated') {
            msg = 'Pertanyaan diskusi diperbarui oleh ' + (ev.nama_pengaju || ev.nik_pengaju);
        } else {
            msg = 'Pertanyaan diskusi baru dari ' + (ev.nama_pengaju || ev.nik_pengaju);
        }
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: msg + ' — KPI #' + (ev.no_urut || '-'),
            showConfirmButton: false,
            timer: 4000
        });
    }

    function apRtApply(ev) {
        let item = apRtThreadItem(ev);

        if (apCurrentIpp && String(apCurrentIpp.ipp_id) === String(ev.ipp_id)) {
            let found = false;
            (apDiskusi || []).forEach(function (x, i) {
                if (String(x.id) === String(ev.id)) {
                    apDiskusi[i] = item;
                    found = true;
                }
            });
            if (!found) apDiskusi.push(item);
            $('#apTableBody .btn-ap-diskusi').each(function () {
                refreshApDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
            });
            if ($('#apDiskusiModal').hasClass('show')
                && String(apDiskusiDetailId) === String(ev.ipp_detail_id)
                && String(apDiskusiTahapan || '') === String(ev.tahapan || '')) {
                apRtRefreshThread();
            }
        }

        let $cell = $('#apList tr[data-oto="' + ev.otorisasi_id + '"][data-tahapan="' + ev.tahapan + '"]').find('.ap-cell-diskusi');
        if ($cell.length) {
            $cell.html(apDiskusiCellHtml(ev.open_count, ev.total_count));
        }

        let $row = $('#apDiskusiListBody tr[data-id="' + ev.id + '"]');
        if ($row.length) {
            $row.replaceWith(apDiskusiSectionRowHtml(ev));
        }
        apRtSectionSync();

        apRtToast(ev);
    }

    function apRtHandle(ev) {
        apRtPending[ev.id] = ev;
        clearTimeout(apRtTimer);
        apRtTimer = setTimeout(function () {
            let items = apRtPending;
            apRtPending = {};
            Object.keys(items).forEach(function (id) { apRtApply(items[id]); });
        }, 300);
    }

    function apRtResync() {
        clearTimeout(apRtSyncTimer);
        apRtSyncTimer = setTimeout(function () {
            loadDiskusiSection();
            if (apCurrentIpp && apCurrentIpp.ipp_id) {
                loadApDiskusi(apCurrentIpp.ipp_id, function () {
                    $('#apTableBody .btn-ap-diskusi').each(function () {
                        refreshApDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
                    });
                    if ($('#apDiskusiModal').hasClass('show') && apDiskusiDetailId) {
                        renderApDiskusiThread(apDiskusiDetailId, apDiskusiTahapan);
                    }
                });
            }
        }, 500);
    }

    (function apRtSubscribe() {
        if (!window.Echo || !apMyNik || typeof window.Echo.private !== 'function') return;
        window.Echo.private('ipp-diskusi.' + apMyNik)
            .listen('.ipp-diskusi-created', function (ev) { apRtHandle(ev); })
            .listen('.ipp-diskusi-updated', function (ev) { apRtHandle(ev); })
            .listen('.ipp-diskusi-answered', function (ev) { apRtHandle(ev); });

        let pusher = window.Echo.connector && window.Echo.connector.pusher;
        if (pusher && pusher.connection && typeof pusher.connection.bind === 'function') {
            let firstConnect = true;
            pusher.connection.bind('connected', function () {
                if (firstConnect) { firstConnect = false; return; }
                apRtResync();
            });
        }
    })();

    $(function () {
        loadList();
        loadDiskusiSection();
    });
</script>
@endpush
