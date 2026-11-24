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
    <h1 class="ipp-page-title">Approval IPP (Semua)</h1>

    <div class="ipp-card">
        <h5>Selamat Datang, <span id="apUserName">{{ $userName }}</span></h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:0;">
            NIK: <strong id="apUserNik">{{ $userNik }}</strong> &mdash; Anda dapat melihat seluruh IPP &amp; peserta,
            status approval, aktual, dan diskusi (mode lihat saja).
        </p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="ipp-card">
        <h5>Daftar Semua IPP</h5>
        <form id="apFormSearch" class="form-inline mb-2" onsubmit="return false;">
            <label class="mr-1" style="font-size:.82rem; font-weight:600;">Periode</label>
            <select id="apFltPeriode" class="form-control form-control-sm mr-2" style="min-width:120px;">
                <option value="">&mdash; Semua &mdash;</option>
                @foreach ($periodeOptions as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
            <input type="text" id="apSearch" class="form-control form-control-sm mr-2" style="min-width:260px;" placeholder="Cari Golongan / Dept / Position / NIK / Nama...">
            <button type="button" class="btn btn-sm btn-primary mr-1" id="apBtnSearch">Cari</button>
            <button type="button" class="btn btn-sm btn-secondary mr-1" id="apBtnReset">Reset</button>
            <label class="ml-2 mr-1" style="font-size:.82rem; font-weight:600;">Baris</label>
            <select id="apPerPage" class="form-control form-control-sm">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </form>
        <div id="apList"></div>
        <div id="apPagination" class="mt-2"></div>
    </div>
</div>

{{-- MODAL DETAIL AKTUAL --}}
<div class="modal fade" id="apModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apModalTitle" style="color:#4a148c; font-weight:700;">Detail Aktual IPP</h5>
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
                <div class="ipp-table-wrapper">
                    <table class="table table-bordered" style="font-size:.975rem;">
                        <thead id="apTableHead"></thead>
                        <tbody id="apTableBody"></tbody>
                    </table>
                </div>
                <div id="apAchSummary" class="mb-2"></div>
                <div id="apAchLegend" class="mb-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSaveAktualDepartemen" style="width:30%;">Simpan Aktual Departemen</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width:30%;">Tutup</button>
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
<script>
    let apCurrentIpp = null;
    let apOtoId = null;
    let apTahapan = null;
    let apDiskusi = [];
    let apDiskusiDetailId = null;
    let apDiskusiTahapan = null;

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
                tHtml += '<div class="text-nowrap"><strong>' + t.score + '</strong>' + rangeLabelSuffix(escapeHtml(t.label || '')) + '</div>';
            });
            tHtml += '</div>';
            return tHtml;
        }
        if (tiers.some(function (t) { return ['gte', 'lte', 'eq', 'between'].indexOf(t.match_type) !== -1; })) {
            tiers.sort(function (a, b) { return (a.score || 0) - (b.score || 0); });
            let customDec = customTierDecimals(tiers);
            let cHtml = '<div class="ipp-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
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
        let html = '<div class="ipp-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
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
    let apListPeriode = '';
    let apListPerPage = 10;

    function loadList() {
        $('#apList').html('<div class="text-center text-muted py-3">Memuat data...</div>');
        $.get("{{ url('/hr/ipp-approval/list') }}", {
            page: apListPage,
            per_page: apListPerPage,
            periode: apListPeriode,
            search: apListSearch
        }, function (res) {
            let meta = res.meta || {};
            apListPage = parseInt(meta.page || 1);
            renderList(res.data || [], meta);
            renderPagination(meta);
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
            '<span class="text-muted" style="font-size:.85rem;">Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' karyawan)</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.ap-page-btn', function () {
        apListPage = parseInt($(this).data('page'));
        loadList();
    });

    $('#apFltPeriode').on('change', function () {
        apListPage = 1;
        apListPeriode = $(this).val() || '';
        loadList();
    });

    $('#apPerPage').on('change', function () {
        apListPage = 1;
        apListPerPage = parseInt($(this).val()) || 10;
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

    function renderList(rows, meta) {
        if (!rows.length) {
            $('#apList').html('<div class="text-center text-muted py-3">Tidak ada data karyawan.</div>');
            return;
        }

        function progBadge(info) {
            return info.filled >= info.total
                ? '<span class="ipp-badge ipp-badge-success">' + info.filled + '/' + info.total + '</span>'
                : (info.filled > 0 ? '<span class="ipp-badge ipp-badge-warning">' + info.filled + '/' + info.total + '</span>'
                    : '<span class="ipp-badge ipp-badge-muted">' + info.filled + '/' + info.total + '</span>');
        }

        function deptProgBadge(info) {
            let f = info.dept_filled || 0;
            let t = info.dept_total || 0;
            return f >= t
                ? '<span class="ipp-badge ipp-badge-success">' + f + '/' + t + '</span>'
                : (f > 0 ? '<span class="ipp-badge ipp-badge-warning">' + f + '/' + t + '</span>'
                    : '<span class="ipp-badge ipp-badge-muted">' + f + '/' + t + '</span>');
        }

        let noBase = (parseInt((meta && meta.page) || 1) - 1) * parseInt((meta && meta.per_page) || 10);
        let bodyHtml = '';
        let rubrikBodyHtml = '';
        let noIdx = 0;

        rows.forEach(function (r) {
            let karyawan = r.karyawan || [];

            if (r.is_rubric) {
                karyawan.forEach(function (k) {
                    let tahapKeys = Object.keys(k.tahapan || {});
                    let rowspan = Math.max(tahapKeys.length, 1);
                    noIdx++;
                    tahapKeys.forEach(function (t, ti) {
                        let isFirst = ti === 0;
                        let info = k.tahapan[t] || {};
                        let needApprove = info.total > 0 && info.filled >= info.total
                            && ((info.two_stage || k.has_am)
                                ? (info.approval_associate_manager === 'approved' && info.approval_manager === null)
                                : info.approval_manager === null);
                        let rowStyle = needApprove ? ' style="background:#fff3cd !important;"' : '';
                        rubrikBodyHtml += '<tr' + rowStyle + '>' +
                            (isFirst
                                ? '<td class="text-center" rowspan="' + rowspan + '">' + (noBase + noIdx) + '</td>' +
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
                            '<td class="text-center font-weight-bold" style="white-space:nowrap;">' + escapeHtml(tahapanLabel(t)) + '</td>' +
                            '<td class="text-center">' + progBadge(info) + '</td>' +
                            '<td class="text-center">' + ((info.two_stage || k.has_am) ? approvalBadge(info.approval_associate_manager) : '<span class="text-muted">-</span>') + '</td>' +
                            '<td class="text-center">' + approvalBadge(info.approval_manager) + '</td>' +
                            '<td class="text-center">' +
                                '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                                '<button class="btn btn-sm btn-info text-white btn-ap-isi" data-id="' + r.ipp_id + '" data-oto="' + k.id + '" data-tahapan="' + t + '" style="white-space:nowrap;">' +
                                '<i class="fa fa-eye mr-1"></i> Detail' +
                                '</button>' +
                                '<div class="btn-group">' +
                                '<button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space:nowrap;">PDF</button>' +
                                '<div class="dropdown-menu dropdown-menu-right">' +
                                '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-approval/export-pdf-foreman') }}/' + r.ipp_id + '/' + encodeURIComponent(k.id) + '?tahapan=' + encodeURIComponent(t) + '">PDF ' + escapeHtml(tahapanLabel(t)) + ' ini</a>' +
                                '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-approval/export-pdf-foreman') }}/' + r.ipp_id + '/' + encodeURIComponent(k.id) + '">PDF Mid+End (1 file)</a>' +
                                '</div></div>' +
                                '</div>' +
                            '</td>' +
                            '</tr>';
                    });
                });
                return;
            }

            karyawan.forEach(function (k) {
                let tahapKeys = Object.keys(k.tahapan || {});
                let rowspan = Math.max(tahapKeys.length, 1);
                noIdx++;
                tahapKeys.forEach(function (t, ti) {
                    let isFirst = ti === 0;
                    let info = k.tahapan[t] || {};
                    let needAction = info.filled >= info.total
                        && (info.approval_associate_manager === null || info.approval_manager === null);
                    let rowStyle = needAction ? ' style="background:#fff3cd !important;"' : '';
                    let dOpen = info.diskusi_open || 0;
                    let dTotal = info.diskusi_total || 0;
                    let diskusiCell = '<span class="text-muted">-</span>';
                    if (dTotal > 0) {
                        diskusiCell = dOpen > 0
                            ? '<span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">? ' + dOpen + ' menunggu</span>'
                            : '<span class="ipp-badge ipp-badge-success">Dijawab</span>';
                    }
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

                    bodyHtml += '<tr' + rowStyle + '>' +
                        (isFirst
                            ? '<td class="text-center" rowspan="' + rowspan + '">' + (noBase + noIdx) + '</td>' +
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
                        '<td class="text-center">' + deptProgBadge(info) + '</td>' +
                        '<td class="text-center">' + (info.two_stage || k.has_am ? approvalBadge(info.approval_associate_manager) : '<span class="text-muted">-</span>') + '</td>' +
                        '<td class="text-center">' + approvalBadge(info.approval_manager) + '</td>' +
                        '<td class="text-center">' + diskusiCell + '</td>' +
                        '<td class="text-center">' +
                            '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                            '<button class="btn btn-sm btn-info text-white btn-ap-isi" data-id="' + r.ipp_id + '" data-oto="' + k.id + '" data-tahapan="' + t + '" style="white-space:nowrap;">' +
                            '<i class="fa fa-eye mr-1"></i> Detail' +
                            '</button>' +
                            '<div class="btn-group">' +
                            '<button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space:nowrap;">PDF</button>' +
                            '<div class="dropdown-menu dropdown-menu-right">' +
                            '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-approval/export-pdf') }}/' + k.id + '?tahapan=' + encodeURIComponent(t) + '">PDF ' + escapeHtml(tahapanLabel(t)) + ' ini</a>' +
                            '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-approval/export-pdf') }}/' + k.id + '">PDF Mid+End (1 file)</a>' +
                            '</div></div>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                });
            });
        });

        let tableHtml = '';
        if (bodyHtml) {
            tableHtml = '<div class="table-responsive" style="overflow:visible;">' +
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
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian Aktual Karyawan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian Aktual Departemen</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Associate Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Diskusi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; width:190px; vertical-align:middle;">Aksi</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' + bodyHtml + '</tbody>' +
            '</table>' +
            '</div>';
        }

        if (rubrikBodyHtml) {
            tableHtml += '<h6 class="mt-3 mb-2" style="color:#4a148c; font-weight:700;">IPP Level 2-3 (Rubrik)</h6>' +
            '<div class="table-responsive" style="overflow:visible;">' +
            '<table class="table table-hover table-bordered table-sm mb-0" style="font-size:.85rem;">' +
            '<thead>' +
            '<tr>' +
            '<th class="bg-primary text-center" style="color:white !important; width:40px; vertical-align:middle;">No</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Periode</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Dept &amp; Posisi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Gol.</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Karyawan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Tahapan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Associate Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; width:190px; vertical-align:middle;">Aksi</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' + rubrikBodyHtml + '</tbody>' +
            '</table>' +
            '</div>';
        }

        if (!tableHtml) {
            tableHtml = '<div class="text-center text-muted py-3">Tidak ada data karyawan.</div>';
        }

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
        renderAktualTable(apCurrentIpp);
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

    // === MODAL ===
    function openApproval(id, otoId, tahapan) {
        $.get("{{ url('/hr/ipp-approval') }}/" + id, function (res) {
            apCurrentIpp = res.data;
            apOtoId = otoId || ((res.data.otorisasi || [])[0]?.id ?? null);
            apTahapan = tahapan || ((res.data.tahapan || [])[0] || null);
            $('#apModalTitle').text((isRubricData(res.data) ? 'Detail IPP Rubrik Level 2-3 - ' : 'Detail Aktual IPP - ') + escapeHtml(res.data.periode || ''));
            renderInfo(res.data);
            renderKaryawanPicker(res.data);
            renderTahapanPicker(res.data);
            renderApprovalBox();
            renderAktualTable(res.data);
            if (isRubricData(res.data)) {
                apDiskusi = [];
            } else {
                loadApDiskusi(id, function () {
                    renderAktualTable(apCurrentIpp);
                });
            }
            $('#apModal').modal('show');
        }).fail(function (xhr) {
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

    function isRubricData(d) {
        return !!(d && d.template_type === 'rubric');
    }

    function currentApprovalStatus(d) {
        if (!apOtoId || !apTahapan) return null;
        let statuses = {};
        if (isRubricData(d)) {
            (d.rubric || []).forEach(function (row) {
                let v = ((d.nilai[apOtoId] || {})[row.id] || {})[apTahapan] || {};
                if (v.approval_associate_manager) statuses[v.approval_associate_manager] = true;
                if (v.approval_manager) statuses[v.approval_manager] = true;
            });
        } else {
            (d.details || []).forEach(function (dtl) {
                let v = ((d.aktual[apOtoId] || {})[dtl.id] || {})[apTahapan] || {};
                if (v.approval_associate_manager) statuses[v.approval_associate_manager] = true;
                if (v.approval_manager) statuses[v.approval_manager] = true;
            });
        }
        if (statuses.rejected) return 'rejected';
        if (statuses.approved) return 'approved';
        return null;
    }

    function renderApprovalBox() {
        let d = apCurrentIpp;
        if (!d || !apOtoId || !apTahapan) {
            $('#apApprovalBox').html('');
            return;
        }
        let status = currentApprovalStatus(d);
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

        $('#apApprovalBox').html(
            '<div class="p-2 rounded" style="background:#f5f5f5; font-size:.85rem; display:inline-block;">' +
            'Status Approval ' + escapeHtml(tahapanLabel(apTahapan)) + ': ' + approvalBadge(status) + info +
            '</div>'
        );
    }

    function diskusiFor(detailId, tahapan) {
        return (apDiskusi || []).filter(function (x) {
            return x.ipp_detail_id == detailId && (x.tahapan === tahapan || (!x.tahapan && !tahapan));
        });
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

    function loadApDiskusi(ippId, cb) {
        if (!ippId) {
            apDiskusi = [];
            if (cb) cb();
            return;
        }
        $.get("{{ url('/hr/ipp-approval/diskusi') }}/" + ippId, function (res) {
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
            html += '<div class="border rounded p-2 mb-2 ap-diskusi-item" style="border-color:#dee2e6 !important;">' +
                '<div class="d-flex justify-content-between align-items-start" style="font-size:.8rem;">' +
                '<div>' + badge +
                ' <span style="color:#4a148c; font-weight:600;">' + escapeHtml(x.nama_pengaju || '-') + '</span>' +
                ' <span class="text-muted">' + escapeHtml(x.created_at || '') + '</span>' +
                (x.tahapan ? ' <span class="text-muted">(' + escapeHtml(x.tahapan) + ')</span>' : '') +
                '</div>' +
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
            }
            html += '</div>';
        });
        $('#apDiskusiThread').html(html);
    }

    // Tabel rubrik Level 2-3 (read-only untuk Admin HR).
    function renderRubricTable(d) {
        $('#apLockNotice').hide().html('');
        $('#btnSaveAktualDepartemen').hide();

        $('#apAchLegend').html(
            '<div class="mt-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">' +
            'Penilaian rubrik <strong>IPP Level 2-3</strong>: skor <strong>Leader Assessment 1–5</strong> per baris rubrik. ' +
            'Total SCORE = rata-rata nilai seluruh baris rubrik. Pengisian &amp; approval dilakukan atasan/AM/Manager di halaman Approval IPP.' +
            '</div>'
        );

        let thead = '<tr>' +
            '<th class="text-center" style="min-width:55px;">NO</th>' +
            '<th style="min-width:140px;">AREA</th>' +
            '<th style="min-width:220px;">COMPETENCY BEHAVIOR</th>' +
            '<th style="min-width:220px;">KEY BEHAVIOR</th>' +
            '<th style="min-width:110px;">1</th>' +
            '<th style="min-width:110px;">2</th>' +
            '<th style="min-width:110px;">3</th>' +
            '<th style="min-width:110px;">4</th>' +
            '<th style="min-width:110px;">5</th>' +
            '<th class="text-center ap-month-head" style="min-width:110px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Nilai</th>' +
            '<th class="text-center" style="min-width:120px;">Diisi oleh</th>' +
            '</tr>';
        $('#apTableHead').html(thead);

        let tbody = '';
        let sectionNow = null;
        let nilaiList = [];

        (d.rubric || []).forEach(function (row) {
            if (row.section && row.section !== sectionNow) {
                sectionNow = row.section;
                tbody += '<tr><td colspan="11" class="font-weight-bold" style="background:#ede7f6; color:#4a148c;">' +
                    escapeHtml(row.section) + '</td></tr>';
            }

            let v = ((d.nilai[apOtoId] || {})[row.id] || {})[apTahapan] || {};
            let nilai = v.nilai;
            if (nilai !== null && nilai !== undefined && nilai !== '') {
                nilaiList.push(parseFloat(nilai));
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
                '<td class="text-center" style="font-weight:600;">' +
                ((nilai !== null && nilai !== undefined && nilai !== '') ? escapeHtml(String(nilai)) : '<span class="text-muted">-</span>') +
                '</td>' +
                '<td class="text-center" style="font-size:.75rem; color:#555;">' + escapeHtml(v.filled_by || '-') + '</td>' +
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

    function renderAktualTable(d) {
        if (!apOtoId || !apTahapan) {
            $('#apTableHead').html('');
            $('#apTableBody').html('');
            $('#apAchLegend').html('');
            $('#apAchSummary').html('');
            return;
        }

        if (isRubricData(d)) {
            renderRubricTable(d);
            return;
        }

        $('#btnSaveAktualDepartemen').show();

        let status = currentApprovalStatus(d);
        let lockInfo = (d.locking || {})[apTahapan] || {};
        let deptLocked = !!lockInfo.departemen && !d.can_edit_locked;

        if (deptLocked) {
            $('#apLockNotice').html(
                '<div class="alert alert-danger py-2 mb-0" style="font-size:.85rem;">' +
                'Kolom <strong>Aktual (Departemen)</strong> tahapan ' + escapeHtml(tahapanLabel(apTahapan)) +
                ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.</div>'
            ).show();
        } else {
            $('#apLockNotice').hide().html('');
        }

        $('#apAchLegend').html(
            '<div class="mt-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">' +
            '<button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start" data-toggle="collapse" data-target="#apAchLegendBody"><i class="fa fa-chevron-down"></i> &nbsp;Keterangan Nilai Achievement</button>' +
            '<div id="apAchLegendBody" class="collapse mt-1">' +
            '<table class="table table-sm table-bordered mt-1 mb-1" style="font-size:.72rem;">' +
            '<thead><tr><th>Achievement</th><th class="text-center">Scale</th><th>Kriteria</th></tr></thead>' +
            '<tbody>' +
            '<tr><td><span class="ipp-badge" style="background:#c8e6c9;color:#1b5e20;">Exceed</span></td><td class="text-center">5</td><td>≥120%</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span></td><td class="text-center">4</td><td>100%–119%</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span></td><td class="text-center">5</td><td>Min/Max/Std/Range</td></tr>' +
            '<tr><td><span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">Not Achieve</span></td><td class="text-center">3 / 2 / 1</td><td>Lihat Score Scale (di bawah)</td></tr>' +
            '</tbody></table>' +
            '<strong>Score Scale:</strong>' +
            '<table class="table table-sm table-bordered mt-1 mb-1" style="font-size:.72rem;">' +
            '<thead><tr><th>Tipe Target</th><th class="text-center">Scale 5</th><th class="text-center">Scale 4</th><th class="text-center">Scale 3</th><th class="text-center">Scale 2</th><th class="text-center">Scale 1</th></tr></thead>' +
            '<tbody>' +
            '<tr><td>High Is Good/Bad</td><td class="text-center">≥120%</td><td class="text-center">100%–119%</td><td class="text-center">80%–99%</td><td class="text-center">70%–79%</td><td class="text-center">≤69%</td></tr>' +
            '<tr><td>Min/Max/Range</td><td class="text-center">96%–100%</td><td class="text-center">91%–95%</td><td class="text-center">81%–90%</td><td class="text-center">71%–80%</td><td class="text-center">≤70%</td></tr>' +
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
            '<th class="text-center ap-month-head" style="min-width:160px;">' + escapeHtml(tahapanLabel(apTahapan)) + ' Aktual (Atasan)</th>' +
            '<th class="text-center" style="min-width:110px;">Achievement Category</th>' +
            '<th class="text-center" style="min-width:90px;">Achievement Score</th>' +
            '<th class="text-center" style="min-width:80px;">Score Scale</th>' +
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
            let locked = v.approval_associate_manager === 'approved' || v.approval_manager === 'approved';
            let tText = target !== null && target !== '' ? target : '-';
            let textRange = isTextRange(dtl);
            let karyawanDisplay = textRange ? aktualVarchar : aktual;
            let atasanDisplay = textRange ? aktualAtasanVarchar : aktualAtasan;
            let deptCell;
            if (textRange) {
                let opts = '<option value="">- Pilih -</option>';
                rangeTextLabels(dtl).forEach(function (label) {
                    let sel = (String(label) === String(deptVarchar)) ? ' selected' : '';
                    opts += '<option value="' + escapeHtml(label) + '"' + sel + '>' + escapeHtml(label) + '</option>';
                });
                deptCell = '<select class="form-control form-control-sm ap-aktual-dept-input" ' +
                    'data-detail="' + dtl.id + '" data-mode="equals" ' +
                    (locked || deptLocked ? 'disabled' : '') + '>' + opts + '</select>';
            } else {
                let dec = apScaleDecimals(dtl);
                let decAttr = (dec === null) ? '' : ' data-decimals="' + dec + '"';
                let modeAttr = (dec === 0) ? 'inputmode="numeric"' : 'inputmode="decimal"';
                let displayDept = (dec === null) ? deptVal : apFmtScaled(deptVal, dec);
                deptCell = '<input type="text" class="form-control form-control-sm ap-aktual-dept-input" ' +
                    'data-detail="' + dtl.id + '" data-mode="number" value="' + escapeHtml(displayDept) + '" placeholder="-" ' + modeAttr + decAttr + rangeTierAttr(dtl) + ' ' +
                    (locked || deptLocked ? 'readonly' : '') + '>';
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
                '<td class="text-center">' + deptCell +
                (byDept ? '<div class="text-muted" style="font-size:.85rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byDept) + '</div>' : '') +
                '</td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm" ' +
                'value="' + escapeHtml(karyawanDisplay) + '" placeholder="-" readonly></td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm ap-aktual-atasan-input" ' +
                'value="' + escapeHtml(atasanDisplay) + '" placeholder="-" readonly>' +
                (byAtasan ? '<div class="text-muted" style="font-size:.85rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byAtasan) + '</div>' : '') +
                '</td>' +
                '<td class="text-center">' + achievementBadge(v.achievement_category || '') + '</td>' +
                '<td class="text-center">' + formatPercent(v.achievement_score) + '</td>' +
                '<td class="text-center" style="font-weight:600;">' + (v.score_scale !== null && v.score_scale !== undefined && v.score_scale !== '' ? v.score_scale : '-') + '</td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-secondary btn-ap-bukti" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + apTahapan + '" style="white-space:nowrap;">Dokumen Pendukung</button></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm ' +
                (diskusiAnswered(dtl.id, apTahapan) ? 'btn-success' : 'btn-outline-primary') + ' btn-ap-diskusi" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + apTahapan + '" style="white-space:nowrap;">? Diskusi' +
                diskusiBadgeHtml(openDiskusiCount(dtl.id, apTahapan)) + '</button></td>' +
                '</tr>';
        });

        $('#apTableBody').html(tbody);
        $('#btnSaveAktualDepartemen').prop('disabled', deptLocked);

        let sumHtml = '';
        if (apOtoId && apTahapan) {
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

    // === EVENTS ===
    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal.show').length) {
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click', '.btn-ap-isi', function () {
        openApproval($(this).data('id'), $(this).data('oto'), $(this).data('tahapan'));
    });

    function refreshApprovalData() {
        if (!apCurrentIpp) return;
        $.get("{{ url('/hr/ipp-approval') }}/" + apCurrentIpp.ipp_id, function (res) {
            apCurrentIpp = res.data;
            renderInfo(res.data);
            renderKaryawanPicker(res.data);
            renderTahapanPicker(res.data);
            renderApprovalBox();
            renderAktualTable(res.data);
            if (isRubricData(res.data)) {
                apDiskusi = [];
            } else {
                loadApDiskusi(apCurrentIpp.ipp_id, function () {
                    renderAktualTable(apCurrentIpp);
                });
            }
        }).fail(function (xhr) {
            AP_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memuat ulang data IPP.', 'error');
        });
    }

    // Bulatkan input Aktual (Departemen) sesuai presisi skala, lalu pastikan nilainya dapat skor.
    $(document).on('blur', '.ap-aktual-dept-input[data-mode="number"]', function () {
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
            AP_SWAL.fire('Peringatan', 'Aktual (Departemen) KPI #' + ($inp.attr('data-no-urut') || '-') + ' tidak masuk rentang skor mana pun.', 'warning');
            $inp.val('');
        }
    });

    $('#btnSaveAktualDepartemen').on('click', function () {
        if (!apCurrentIpp || !apOtoId || !apTahapan) return;

        let values = [];
        let invalidKpi = null;
        $('#apTableBody .ap-aktual-dept-input:not(:disabled)').each(function () {
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
                aktual_departemen: (!isText && val !== '') ? val : null,
                aktual_departemen_varchar: (isText && val !== '') ? val : null
            });
        });

        if (invalidKpi !== null) {
            AP_SWAL.fire('Peringatan', 'Aktual (Departemen) KPI #' + invalidKpi + ' tidak masuk rentang skor mana pun.', 'warning');
            return;
        }

        if (!values.length) {
            AP_SWAL.fire('Peringatan', 'Tidak ada data AKTUAL DEPARTEMEN untuk disimpan.', 'warning');
            return;
        }

        $('#btnSaveAktualDepartemen').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ url('/hr/ipp-approval/save-aktual-departemen') }}/" + apCurrentIpp.ipp_id,
            type: 'POST',
            data: { values: values },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $('#btnSaveAktualDepartemen').prop('disabled', false).text('Simpan Aktual Departemen');
                AP_SWAL.fire('Berhasil', res.message || 'Data AKTUAL DEPARTEMEN berhasil disimpan.', 'success');
                refreshApprovalData();
            },
            error: function (xhr) {
                $('#btnSaveAktualDepartemen').prop('disabled', false).text('Simpan Aktual Departemen');
                let msg = xhr.responseJSON?.message || 'Gagal menyimpan data AKTUAL DEPARTEMEN.';
                AP_SWAL.fire('Error', msg, 'error');
            }
        });
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

    $(function () {
        loadList();
    });
</script>
@endpush