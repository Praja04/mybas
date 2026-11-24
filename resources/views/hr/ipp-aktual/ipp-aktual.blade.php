@extends('layouts.base')

@section('content')

<style>
    .ipp-page-title { color:#4a148c; font-weight:700; font-size:1.6rem; margin-bottom:1rem; }
    .ipp-card { background:#fff; border-radius:6px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.05); }
    .ipp-card h5 { font-weight:700; margin-bottom:0.5rem; }
    .ipp-badge { display:inline-block; padding:.15rem .55rem; border-radius:4px; font-size:.75rem; font-weight:600; }
    .ipp-badge-info { background:#e3f2fd; color:#0d47a1; }
    .ipp-badge-success { background:#c8e6c9; color:#1b5e20; }
    .ipp-badge-warning { background:#fff3cd; color:#8a6d1c; }
    .ipp-badge-muted { background:#e9ecef; color:#6c757d; }
    .ipp-badge-danger { background:#ffcdd2; color:#b71c1c; }
    .modal-ipp-xl .modal-dialog { max-width: 95%; margin: 1rem auto; }
    #akDiskusiModal, #akBuktiModal { z-index: 2000; }
    #akDiskusiModal + .modal-backdrop, #akBuktiModal + .modal-backdrop { z-index: 1990; }
    .swal2-container { z-index: 2200 !important; }
    .ipp-table-wrapper { max-height: 60vh; overflow:auto; border:1px solid #dee2e6; border-radius:4px; }
    .ipp-table-wrapper table { margin-bottom:0; }
    .ipp-table-wrapper thead th { position:sticky; top:0; background:#f8f9fa; z-index:1; }
    .ipp-table-wrapper thead th.ak-month-head { position:sticky; top:0; background:#ede7f6; color:#4a148c; }
    .ak-pdf-menu-portal { position:fixed; z-index:2100; }
    .ak-aktual-input { min-width:70px; max-width:110px; text-align:right; }
    .ak-target-cell { font-size:.72rem; color:#6c757d; text-align:right; white-space:nowrap; }
    .ak-month-chip { display:inline-block; padding:.15rem .5rem; border-radius:4px; font-size:.75rem; font-weight:600; margin-right:.35rem; margin-bottom:.35rem; }
    #akDiskusiListBody tr { cursor:pointer; }
    #akDiskusiListBody tr:hover { background:#f3e5f5; }
    .ak-diskusi-truncate { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
</style>

<div class="container-fluid">
    <h1 class="ipp-page-title">Isi Aktual IPP</h1>

    <div class="ipp-card">
        <h5>Selamat Datang, <span id="akUserName">{{ $userName }}</span></h5>
        <p class="text-muted" style="font-size:.85rem; margin-bottom:0;">
            NIK: <strong id="akUserNik">{{ $userNik }}</strong> &mdash; Silakan lengkapi data AKTUAL pada periode IPP yang diotorisasikan untuk Anda.
            Aktual diisi dalam 2 tahapan: <strong>Mid Year</strong> (tengah tahun) dan <strong>End Year</strong> (akhir tahun).
        </p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="ipp-card">
        <h5>Daftar IPP Anda</h5>
        <div id="akList"></div>
    </div>

    <div class="ipp-card" id="akDiskusiSection" style="display:none;">
        <h5>Diskusi IPP Anda</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th class="text-center" style="font-size:.78rem; color:#4a148c;">Status</th>
                        <th style="font-size:.78rem; color:#4a148c;">Periode</th>
                        <th style="font-size:.78rem; color:#4a148c;">Tahapan</th>
                        <th style="font-size:.78rem; color:#4a148c;">KPI</th>
                        <th style="font-size:.78rem; color:#4a148c;">Pertanyaan</th>
                        <th style="font-size:.78rem; color:#4a148c;">Jawaban</th>
                        <th style="font-size:.78rem; color:#4a148c;">Tanggal</th>
                    </tr>
                </thead>
                <tbody id="akDiskusiListBody"></tbody>
            </table>
        </div>
        <div id="akDiskusiPagination" class="mt-2"></div>
    </div>
</div>

{{-- MODAL ISI AKTUAL --}}
<div class="modal fade" id="akModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-ipp-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="akModalTitle" style="color:#4a148c; font-weight:700;">Isi Aktual IPP</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="akInfo" class="mb-3 p-2" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;"></div>
                <div id="akLockNotice" class="mb-3" style="display:none;"></div>
                <div class="mb-3 p-2" id="akBawahanWrap" style="background:#f5f5f5; font-size:.85rem; border-radius:4px;">
                    <label class="mb-1" style="font-weight:600;">Jumlah Bawahan Langsung (bawahan peserta):</label>
                    <input type="text" id="akJumlahBawahan" class="form-control form-control-sm" style="max-width:200px;" placeholder="0">
                </div>
                <div id="akTahapanPicker" class="mb-3"></div>
                <div class="ipp-table-wrapper">
                    <table class="table table-bordered" style="font-size:.8rem;">
                        <thead id="akTableHead"></thead>
                        <tbody id="akTableBody"></tbody>
                    </table>
                </div>
                <div class="mb-2 p-2" style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:.8rem;">
                    Aktual (Departemen) diisi oleh <strong>HRD dan FAC</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnSaveAktual">Simpan Aktual</button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- MODAL DISKUSI --}}
<div class="modal fade" id="akDiskusiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="akDiskusiTitle" style="color:#4a148c; font-weight:700;">Diskusi</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="akDiskusiThread" class="mb-3" style="max-height:50vh; overflow:auto;"></div>
                <textarea id="akDiskusiPesan" class="form-control" rows="2" placeholder="Tulis pertanyaan diskusi Anda di sini..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnAkDiskusiKirim">Kirim Pertanyaan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DOKUMEN PENDUKUNG --}}
<div class="modal fade" id="akBuktiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="akBuktiTitle" style="color:#4a148c; font-weight:700;">Dokumen Pendukung</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="akBuktiUploadWrap" class="border rounded p-2 mb-3">
                    <label style="font-weight:600; font-size:.85rem;">Tambah Dokumen Pendukung (PDF / JPG / PNG / DOC / DOCX / XLS / XLSX / XLSB / CSV):</label>
                    <input type="file" id="akBuktiFiles" class="form-control-file" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.xlsb,.csv">
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="btnAkBuktiUpload">Unggah</button>
                </div>
                <div id="akBuktiList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@include('partials.echo-bootstrap')
<script>
    let akCurrentIpp = null;
    let akCurrentTahapan = null;
    let akOtoId = null;
    let akDiskusi = [];
    let akDiskusiDetailId = null;
    let akDiskusiTahapan = null;
    let akDiskusiAutoOpen = null;
    let akMyNik = "{{ $userNik }}";
    let akBuktiDetailId = null;
    let akBuktiTahapan = null;

    // Cegah Esc menutup Bootstrap modal saat popup SweetAlert sedang terbuka.
    const AK_SWAL = Swal.mixin({ keydownListenerCapture: true });

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
            let tHtml = '<div class="ak-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
            tiers.forEach(function (t) {
                tHtml += '<div class="text-nowrap"><strong>' + t.score + '</strong>' + rangeLabelSuffix(escapeHtml(t.label || '')) + '</div>';
            });
            tHtml += '</div>';
            return tHtml;
        }
        if (tiers.some(function (t) { return ['gte', 'lte', 'eq', 'between'].indexOf(t.match_type) !== -1; })) {
            tiers.sort(function (a, b) { return (a.score || 0) - (b.score || 0); });
            let customDec = customTierDecimals(tiers);
            let cHtml = '<div class="ak-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
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
        let html = '<div class="ak-range-scale" style="text-align:left; font-size:.72rem; line-height:1.35;">';
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
    function akScaleDecimals(dtl) {
        if (!dtl || dtl.classification !== 'Range' || isTextRange(dtl)) return null;
        let tiers = dtl.range_tiers;
        if (!tiers) return null;
        if (!Array.isArray(tiers)) tiers = Object.values(tiers);
        if (!tiers.length) return null;
        return customTierDecimals(tiers);
    }

    // Pembulatan half-away-from-zero (mirror PHP round).
    function akRoundTo(val, decimals) {
        let n = parseFloat(String(val === null || val === undefined ? '' : val).trim().replace(',', '.'));
        if (isNaN(n)) return null;
        let f = Math.pow(10, decimals);
        let r = Math.round(Math.abs(n) * f) / f;
        return (n < 0 ? -1 : 1) * r;
    }

    function akFmtScaled(val, decimals) {
        let r = akRoundTo(val, decimals);
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

    // === LIST ===
    function loadList() {
        if (akPdfMenu) closeAkPdfMenu();
        $('#akList').html('<div class="text-center text-muted py-3">Memuat data...</div>');
        $.get("{{ url('/hr/ipp-aktual/list') }}", function (res) {
            renderList(res.data || []);
        }).fail(function () {
            $('#akList').html('<div class="text-center text-muted py-3">Gagal memuat data.</div>');
        });
    }

    function diskusiFor(detailId, tahapan) {
        return (akDiskusi || []).filter(function (x) {
            return x.ipp_detail_id == detailId && (x.tahapan === tahapan || (!x.tahapan && !tahapan));
        });
    }

    function akDiskusiCellHtml(open, total) {
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

    function refreshDiskusiButton(detailId, tahapan) {
        let $btn = $('.btn-ak-diskusi[data-detail="' + detailId + '"][data-tahapan="' + tahapan + '"]');
        $btn.toggleClass('btn-success', diskusiAnswered(detailId, tahapan))
            .toggleClass('btn-outline-primary', !diskusiAnswered(detailId, tahapan))
            .html('? Diskusi' + diskusiBadgeHtml(openDiskusiCount(detailId, tahapan)));
    }

    // === BUKTI FILE ===
    function buktiFor(detailId, tahapan) {
        let d = (akCurrentIpp && akCurrentIpp.details || []).find(function (x) { return x.id == detailId; });
        return ((d && d.bukti) || {})[tahapan] || [];
    }

    function buktiCount(detailId, tahapan) {
        return buktiFor(detailId, tahapan).length;
    }

    function formatFileSize(bytes) {
        if (bytes === null || bytes === undefined || bytes === '') return '-';
        let units = ['B', 'KB', 'MB', 'GB'];
        let v = Number(bytes);
        let i = 0;
        while (v >= 1024 && i < units.length - 1) { v /= 1024; i++; }
        return v.toFixed(i === 0 ? 0 : 1) + ' ' + units[i];
    }

    function renderBuktiList(items) {
        let canUpload = akCurrentIpp && !isTahapanProcessed(akCurrentIpp, akBuktiTahapan);
        $('#akBuktiUploadWrap').toggle(!!canUpload);
        if (!items.length) {
            $('#akBuktiList').html('<div class="text-muted" style="font-size:.85rem;">Belum ada dokumen pendukung untuk item ini.</div>');
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
                '<a href="' + x.download_url + '" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" style="white-space:nowrap;">Download</a> ' +
                (x.can_delete ? '<button type="button" class="btn btn-sm btn-outline-danger btn-ak-bukti-delete" data-id="' + x.id + '" style="white-space:nowrap;">Hapus</button>' : '') +
                '</div></div>';
        });
        $('#akBuktiList').html(html);
    }

    function refreshBukti() {
        if (!akOtoId || !akBuktiDetailId || !akBuktiTahapan) return;
        $.get("{{ url('/hr/ipp-bukti/list') }}", {
            ipp_otorisasi_id: akOtoId,
            ipp_detail_id: akBuktiDetailId,
            tahapan: akBuktiTahapan
        }, function (res) {
            let items = res.data || [];
            let dtl = (akCurrentIpp.details || []).find(function (x) { return x.id == akBuktiDetailId; });
            if (dtl) {
                dtl.bukti = dtl.bukti || {};
                dtl.bukti[akBuktiTahapan] = items;
            }
            renderBuktiList(items);
            $('.btn-ak-bukti[data-detail="' + akBuktiDetailId + '"][data-tahapan="' + akBuktiTahapan + '"]')
                .text('Dokumen Pendukung (' + items.length + ')');
        }).fail(function () {
            AK_SWAL.fire('Error', 'Gagal memuat daftar dokumen pendukung.', 'error');
        });
    }

    function loadDiskusi(cb) {
        if (!akOtoId) {
            akDiskusi = [];
            if (cb) cb();
            return;
        }
        $.get("{{ url('/hr/ipp-aktual/diskusi') }}/" + akOtoId, function (res) {
            akDiskusi = res.data || [];
            if (cb) cb();
        }).fail(function () {
            akDiskusi = [];
            if (cb) cb();
        });
    }

    function renderDiskusiThread(detailId, tahapan) {
        let items = diskusiFor(detailId, tahapan);
        let html = '';
        if (!items.length) {
            html = '<div class="text-muted" style="font-size:.85rem;">Belum ada pertanyaan diskusi untuk item ini.</div>';
        }
        items.forEach(function (x) {
            let badge = x.status === 'answered'
                ? '<span class="ipp-badge ipp-badge-success">Sudah dijawab</span>'
                : '<span class="ipp-badge ipp-badge-warning">Belum dijawab</span>';
            let canEdit = (x.nik_pengaju === akMyNik) ? 1 : 0;
            html += '<div class="border rounded p-2 mb-2 ak-diskusi-item" data-id="' + x.id + '" style="border-color:#dee2e6 !important;">' +
                '<div class="d-flex justify-content-between align-items-start" style="font-size:.8rem;">' +
                '<div>' + badge +
                ' <span style="color:#4a148c; font-weight:600;">' + escapeHtml(x.nama_pengaju || '-') + '</span>' +
                ' <span class="text-muted">' + escapeHtml(x.created_at || '') + '</span>' +
                (x.tahapan ? ' <span class="text-muted">(' + escapeHtml(x.tahapan) + ')</span>' : '') +
                '</div>' +
                (canEdit ? '<button type="button" class="btn btn-sm btn-outline-secondary btn-ak-diskusi-edit" data-id="' + x.id + '" style="padding:.1rem .4rem; font-size:.72rem;">Edit</button>' : '') +
                '</div>' +
                '<div class="mt-1 ak-diskusi-pesan" style="white-space:pre-wrap; font-size:.85rem;">' + escapeHtml(x.pesan) + '</div>' +
                '<div class="ak-diskusi-edit-form mt-1" style="display:none;">' +
                '<textarea class="form-control form-control-sm ak-diskusi-pesan-input" rows="2" style="font-size:.85rem;">' + escapeHtml(x.pesan) + '</textarea>' +
                '<button type="button" class="btn btn-sm btn-primary mt-1 btn-ak-diskusi-save" data-id="' + x.id + '" style="white-space:nowrap;">Simpan</button> ' +
                '<button type="button" class="btn btn-sm btn-secondary mt-1 btn-ak-diskusi-cancel">Batal</button>' +
                '</div>';
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
        $('#akDiskusiThread').html(html);
    }

    function progressBadge(filled, total) {
        if (total <= 0) return '<span class="ipp-badge ipp-badge-muted">0/' + total + '</span>';
        if (filled >= total) return '<span class="ipp-badge ipp-badge-success">' + filled + '/' + total + '</span>';
        if (filled > 0) return '<span class="ipp-badge ipp-badge-warning">' + filled + '/' + total + '</span>';
        return '<span class="ipp-badge ipp-badge-muted">' + filled + '/' + total + '</span>';
    }

    function approvalBadge(status) {
        if (status === 'approved') return '<span class="ipp-badge ipp-badge-success">Disetujui</span>';
        if (status === 'rejected') return '<span class="ipp-badge ipp-badge-danger">Ditolak</span>';
        if (status === 'associate_approved') return '<span class="ipp-badge ipp-badge-warning">Menunggu Manager</span>';
        return '<span class="ipp-badge ipp-badge-muted">Belum</span>';
    }

    function achievementBadge(ach) {
        if (ach === 'Exceed') return '<span class="ipp-badge" style="background:#c8e6c9;color:#1b5e20;">Exceed</span>';
        if (ach === 'Achieve') return '<span class="ipp-badge" style="background:#e3f2fd;color:#0d47a1;">Achieve</span>';
        if (ach === 'Not Achieve') return '<span class="ipp-badge" style="background:#ffcdd2;color:#b71c1c;">Not Achieve</span>';
        return '<span class="text-muted">-</span>';
    }

    function isTahapanProcessed(d, t) {
        let s = (d.approval_status || {})[t];
        return s === 'approved' || s === 'rejected' || s === 'associate_approved';
    }

    function renderList(rows) {
        if (!rows.length) {
            $('#akList').html('<div class="text-center text-muted py-3">Belum ada IPP yang diotorisasi untuk Anda.</div>');
            return;
        }

        let anyTwoStage = false;
        rows.forEach(function (r) {
            if (r.is_rubric) return;
            (r.karyawan || []).forEach(function (k) {
                Object.keys(k.tahapan || {}).forEach(function (t) {
                    if (k.tahapan[t] && (k.tahapan[t].two_stage || k.has_am)) anyTwoStage = true;
                });
            });
        });
        let colCount = anyTwoStage ? 12 : 11;

        let head = '<tr>' +
            '<th class="bg-primary text-center" style="color:white !important; width:40px; vertical-align:middle;">No</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Periode</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Dept &amp; Posisi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Gol.</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Dibuat Oleh</th>' +
            '<th class="bg-primary" style="color:white !important; vertical-align:middle;">Karyawan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Tahapan</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Progress Pengisian</th>' +
            (anyTwoStage ? '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Associate Manager</th>' : '') +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Review &amp; Approval Manager</th>' +
            '<th class="bg-primary text-center" style="color:white !important; vertical-align:middle;">Diskusi</th>' +
            '<th class="bg-primary text-center" style="color:white !important; width:260px; vertical-align:middle;">Aksi</th>' +
            '</tr>';

        let bodyHtml = '';
        let noIdx = 0;

        rows.forEach(function (r) {
            if (r.is_rubric) return;
            let karyawan = r.karyawan || [];
            karyawan.forEach(function (k) {
                let tahapKeys = Object.keys(k.tahapan || {});
                let rowspan = Math.max(tahapKeys.length, 1);
                noIdx++;
                let groupCells = '<td class="text-center" rowspan="' + rowspan + '">' + noIdx + '</td>' +
                    '<td style="white-space:nowrap;" rowspan="' + rowspan + '"><strong>' + escapeHtml(r.periode || '-') + '</strong></td>' +
                    '<td rowspan="' + rowspan + '">' +
                        '<div><strong>' + escapeHtml(r.dept || '-') + '</strong></div>' +
                    '<div style="font-size:.78rem; color:#6c757d;">' + escapeHtml(r.position || '-') + '</div>' +
                    '</td>' +
                    '<td class="text-center" rowspan="' + rowspan + '"><span class="badge badge-secondary">' + escapeHtml(r.golongan || '-') + '</span></td>' +
                    '<td class="text-center" style="white-space:nowrap; font-size:.78rem;" rowspan="' + rowspan + '">' + escapeHtml(r.uploaded_by || '-') + '</td>' +
                    '<td rowspan="' + rowspan + '">' +
                        '<div><strong>' + escapeHtml(k.nama_yang_bersangkutan || '-') + '</strong></div>' +
                        '<div style="font-size:.78rem; color:#6c757d;">' + escapeHtml(k.nik_yang_bersangkutan || '-') + '</div>' +
                    '</td>';
                if (!tahapKeys.length) {
                    bodyHtml += '<tr>' + groupCells +
                        '<td class="text-center text-muted" colspan="' + (colCount - 6) + '">-</td>' +
                        '</tr>';
                    return;
                }
                tahapKeys.forEach(function (t, ti) {
                    let isFirst = ti === 0;
                    let info = k.tahapan[t] || {};
                    let dOpen = info.diskusi_open || 0;
                    let dTotal = info.diskusi_total || 0;
                    let diskusiCell = akDiskusiCellHtml(dOpen, dTotal);
                    bodyHtml += '<tr data-oto="' + k.id + '" data-tahapan="' + escapeHtml(t) + '">' +
                        (isFirst ? groupCells : '') +
                        '<td class="text-center font-weight-bold" style="white-space:nowrap;">' + escapeHtml(tahapanLabel(t)) + '</td>' +
                        '<td class="text-center">' + progressBadge(info.filled || 0, info.total || 0) + '</td>' +
                        (anyTwoStage ? '<td class="text-center">' + (info.two_stage || k.has_am ? approvalBadge(info.approval_associate_manager) : '<span class="text-muted">-</span>') + '</td>' : '') +
                        '<td class="text-center">' + approvalBadge(info.approval_manager) + '</td>' +
                        '<td class="text-center ak-cell-diskusi">' + diskusiCell + '</td>' +
                        '<td class="text-center">' +
                            '<div class="d-flex justify-content-center align-items-center" style="gap:.35rem;">' +
                            '<button class="btn btn-sm btn-primary btn-ak-isi" data-id="' + r.ipp_id + '" data-tahapan="' + t + '" style="white-space:nowrap;">Isi Aktual</button>' +
                            '<div class="btn-group ak-pdf-group">' +
                            '<button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space:nowrap;">PDF</button>' +
                            '<div class="dropdown-menu dropdown-menu-right">' +
                            '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-aktual/export-pdf') }}/' + r.ipp_id + '?tahapan=' + encodeURIComponent(t) + '">PDF ' + escapeHtml(tahapanLabel(t)) + ' ini</a>' +
                            '<a class="dropdown-item" target="_blank" rel="noopener" href="{{ url('/hr/ipp-aktual/export-pdf') }}/' + r.ipp_id + '">PDF Mid+End (1 file)</a>' +
                            '</div></div></div>' +
                        '</td>' +
                        '</tr>';
                });
            });
        });

        let tableHtml = '<div class="table-responsive">' +
            '<table class="table table-hover table-bordered table-sm mb-0" style="font-size:.85rem; min-width:980px;">' +
            '<thead>' + head + '</thead>' +
            '<tbody>' + bodyHtml + '</tbody>' +
            '</table>' +
            '</div>';

        $('#akList').html(tableHtml);
    }

    // === DROPDOWN PDF (portal) ===
    // Menu PDF dipindah ke body saat dibuka agar tidak terpotong container scroll tabel.
    let akPdfMenu = null;

    function closeAkPdfMenu() {
        if (!akPdfMenu) return;
        let $menu = akPdfMenu.$menu;
        let $group = akPdfMenu.$group;
        akPdfMenu = null;
        $menu.removeClass('ak-pdf-menu-portal show').removeAttr('style').appendTo($group);
    }

    function positionAkPdfMenu() {
        if (!akPdfMenu) return;
        let $toggle = akPdfMenu.$group.find('.dropdown-toggle');
        let $menu = akPdfMenu.$menu;
        if (!$toggle.length || !$menu.length) return;
        let rect = $toggle[0].getBoundingClientRect();
        let pad = 8;
        let menuW = $menu.outerWidth();
        let menuH = $menu.outerHeight();
        let top = rect.bottom + 2;
        let left = rect.right - menuW;
        if (top + menuH + pad > window.innerHeight) top = rect.top - menuH - 2;
        if (top < pad) top = pad;
        if (left < pad) left = pad;
        if (left + menuW + pad > window.innerWidth) left = window.innerWidth - menuW - pad;
        // Hentikan Popper agar posisi fixed tidak ditimpa saat update internal Bootstrap.
        let inst = $toggle.data('bs.dropdown');
        if (inst && inst._popper) {
            try { inst._popper.destroy(); } catch (err) { /* abaikan */ }
            inst._popper = null;
        }
        let el = $menu[0];
        if (!el) return;
        el.style.setProperty('position', 'fixed', 'important');
        el.style.setProperty('top', top + 'px', 'important');
        el.style.setProperty('left', left + 'px', 'important');
        el.style.setProperty('right', 'auto', 'important');
        el.style.setProperty('bottom', 'auto', 'important');
        el.style.setProperty('transform', 'none', 'important');
        el.style.setProperty('z-index', '2100', 'important');
    }

    $(document).on('show.bs.dropdown', '#akList .ak-pdf-group', function () {
        if (akPdfMenu) closeAkPdfMenu();
        akPdfMenu = { $group: $(this), $menu: $(this).find('.dropdown-menu') };
        akPdfMenu.$menu.addClass('ak-pdf-menu-portal').appendTo('body');
    });

    $(document).on('shown.bs.dropdown', '#akList .ak-pdf-group', function () {
        positionAkPdfMenu();
    });

    $(document).on('hidden.bs.dropdown', '#akList .ak-pdf-group', function () {
        if (!akPdfMenu) return;
        let $menu = akPdfMenu.$menu;
        let $group = akPdfMenu.$group;
        akPdfMenu = null;
        window.setTimeout(function () {
            if ($menu.parent()[0] !== document.body) return;
            if ($menu.hasClass('show')) return;
            $menu.removeClass('ak-pdf-menu-portal').removeAttr('style').appendTo($group);
        }, 0);
    });

    $(window).on('resize', positionAkPdfMenu);

    // Tutup dropdown saat halaman/tabel di-scroll agar menu fixed tidak tertinggal.
    window.addEventListener('scroll', function () {
        if (!akPdfMenu) return;
        let $toggle = akPdfMenu.$group.find('.dropdown-toggle');
        if ($toggle.length && $toggle.attr('aria-expanded') === 'true') {
            $toggle.dropdown('toggle');
        }
    }, true);

    // === DISKUSI SECTION ===
    let akDiskusiPage = 1;
    let akDiskusiPerPage = 10;

    function akDiskusiSectionRowHtml(x) {
        let isOpen = x.status === 'open';
        let badge = isOpen
            ? '<span class="ipp-badge ipp-badge-warning">Sedang berlangsung</span>'
            : '<span class="ipp-badge ipp-badge-success">Sudah dijawab</span>';
        let kpi = '<span style="font-weight:600;">KPI #' + (x.no_urut ? x.no_urut : '-') + '</span>' +
            (x.kpi ? '<div class="ak-diskusi-truncate text-muted" style="font-size:.75rem;" title="' + escapeHtml(x.kpi) + '">' + escapeHtml(x.kpi) + '</div>' : '');
        let tanggal = isOpen ? (x.created_at || '') : (x.answered_at || x.created_at || '');
        return '<tr class="ak-diskusi-row" data-id="' + x.id + '" data-ipp="' + x.ipp_id + '" data-detail="' + (x.ipp_detail_id || '') + '" data-tahapan="' + escapeHtml(x.tahapan || '') + '">' +
            '<td class="text-center">' + badge + '</td>' +
            '<td style="white-space:nowrap;">' + escapeHtml(x.periode || '-') + '</td>' +
            '<td style="white-space:nowrap;">' + escapeHtml(tahapanLabel(x.tahapan)) + '</td>' +
            '<td style="min-width:160px;">' + kpi + '</td>' +
            '<td class="ak-diskusi-truncate" title="' + escapeHtml(x.pesan || '') + '">' + escapeHtml(x.pesan || '-') + '</td>' +
            '<td class="ak-diskusi-truncate" title="' + escapeHtml(x.jawaban || '') + '">' + escapeHtml(x.jawaban || '-') + '</td>' +
            '<td class="text-muted" style="font-size:.78rem; white-space:nowrap;">' + escapeHtml(tanggal || '-') + '</td>' +
            '</tr>';
    }

    function loadDiskusiSection() {
        $.get("{{ url('/hr/ipp-aktual/diskusi-list') }}", {
            page: akDiskusiPage,
            per_page: akDiskusiPerPage
        }, function (res) {
            let items = res.data || [];
            if (!items.length) {
                $('#akDiskusiListBody').html('');
                $('#akDiskusiPagination').html('');
                $('#akDiskusiSection').hide();
                return;
            }
            let html = '';
            items.forEach(function (x) {
                html += akDiskusiSectionRowHtml(x);
            });
            $('#akDiskusiListBody').html(html);
            $('#akDiskusiSection').show();
            renderAkDiskusiPagination(res.meta || {});
        }).fail(function () {
            $('#akDiskusiSection').hide();
        });
    }

    function renderAkDiskusiPagination(meta) {
        let total = parseInt(meta.total || 0);
        let page = parseInt(meta.page || 1);
        let lastPage = parseInt(meta.last_page || 1);
        if (total === 0) {
            $('#akDiskusiPagination').html('');
            return;
        }
        let prev = page > 1
            ? '<button class="btn btn-sm btn-outline-primary mr-1 ak-diskusi-page-btn" data-page="' + (page - 1) + '">Prev</button>'
            : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = page < lastPage
            ? '<button class="btn btn-sm btn-outline-primary ak-diskusi-page-btn" data-page="' + (page + 1) + '">Next</button>'
            : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) {
            opts += '<option value="' + n + '"' + (n === akDiskusiPerPage ? ' selected' : '') + '>' + n + '</option>';
        });
        $('#akDiskusiPagination').html(
            '<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.85rem;">Halaman ' + page + ' dari ' + lastPage + ' (' + total + ' diskusi)</span>' +
            '<div class="d-flex align-items-center" style="gap:.5rem;">' +
            '<span class="text-muted" style="font-size:.82rem;">Tampilkan</span>' +
            '<select id="akDiskusiPerPageSel" class="form-control form-control-sm" style="width:auto; display:inline-block;">' + opts + '</select>' +
            '<span class="text-muted" style="font-size:.82rem;">per halaman</span>' +
            '<div>' + prev + next + '</div>' +
            '</div>' +
            '</div>'
        );
    }

    $(document).on('click', '.ak-diskusi-page-btn', function () {
        akDiskusiPage = parseInt($(this).data('page'));
        loadDiskusiSection();
    });

    $(document).on('change', '#akDiskusiPerPageSel', function () {
        akDiskusiPerPage = parseInt($(this).val()) || 10;
        akDiskusiPage = 1;
        loadDiskusiSection();
    });

    // === MODAL ===
    function openAktual(id, tahapan) {
        $.get("{{ url('/hr/ipp-aktual') }}/" + id, function (res) {
            akCurrentIpp = res.data;
            let list = res.data.tahapan || [];
            akCurrentTahapan = (tahapan && list.indexOf(tahapan) !== -1) ? tahapan : (list[0] || null);
            akOtoId = res.data.otorisasi ? res.data.otorisasi.id : null;
            let jb = (res.data.otorisasi || {}).jumlah_bawahan;
            $('#akJumlahBawahan').val(jb !== null && jb !== undefined && jb !== '' ? jb : '');
            $('#akModalTitle').text('Isi Aktual IPP - ' + escapeHtml(res.data.periode || ''));
            renderAktualInfo(res.data);
            renderTahapanPicker(res.data);
            renderAkTable(res.data);
            loadDiskusi(function () {
                $('#akTableBody .btn-ak-diskusi').each(function () {
                    refreshDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
                });
                if (akDiskusiAutoOpen) {
                    let dd = akDiskusiAutoOpen;
                    akDiskusiAutoOpen = null;
                    let openD = function () {
                        let $b = $('#akTableBody .btn-ak-diskusi[data-detail="' + dd + '"][data-tahapan="' + akCurrentTahapan + '"]');
                        if ($b.length) $b.trigger('click');
                    };
                    if ($('#akModal').hasClass('show')) {
                        openD();
                    } else {
                        $('#akModal').one('shown.bs.modal', openD);
                    }
                }
            });
            $('#akModal').modal('show');
        }).fail(function (xhr) {
            akDiskusiAutoOpen = null;
            AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memuat data IPP.', 'error');
        });
    }

    function renderAktualInfo(d) {
        let o = d.otorisasi || {};
        $('#akInfo').html(
            '<strong>Periode:</strong> ' + escapeHtml(d.periode || '-') +
            ' &middot; <strong>Dept:</strong> ' + escapeHtml(d.dept || '-') +
            ' &middot; <strong>POSITION:</strong> ' + escapeHtml(d.position || '-') +
            ' &middot; <strong>GOLONGAN:</strong> ' + escapeHtml(d.golongan || '-') +
            '<br><strong>NIK Peserta:</strong> ' + escapeHtml(o.nik_yang_bersangkutan || '-') +
            ' (' + escapeHtml(o.nama_yang_bersangkutan || '-') + ')' +
            (o.nama_manager || o.nik_manager
                ? ' &middot; <strong>Manager:</strong> ' + escapeHtml(o.nama_manager || o.nik_manager)
                : '') +
            (o.nama_associate_manager || o.nik_associate_manager
                ? ' &middot; <strong>Assoc. Manager:</strong> ' + escapeHtml(o.nama_associate_manager || o.nik_associate_manager)
                : '')
        );
    }

    function renderTahapanPicker(d) {
        let tahapanList = d.tahapan || [];
        let total = (d.details || []).length;
        let html = '<span class="text-muted" style="font-size:.85rem; font-weight:600;">Pilih Tahapan:</span> ';
        tahapanList.forEach(function (t) {
            let filled = (d.details || []).filter(function (dtl) {
                let v = dtl.aktual[t];
                let vc = (dtl.aktual_varchar || {})[t];
                let vFilled = v !== undefined && v !== null && v !== '';
                let vcFilled = vc !== undefined && vc !== null && vc !== '';
                return vFilled || vcFilled;
            }).length;
            let active = (t === akCurrentTahapan)
                ? ' style="background:#4a148c; color:#fff; border-color:#4a148c;"'
                : '';
            let badge = filled >= total
                ? ' style="background:#c8e6c9; color:#1b5e20;"'
                : (filled > 0 ? ' style="background:#fff3cd; color:#8a6d1c;"' : '');
            let status = (d.approval_status || {})[t];
            html += '<button type="button" class="btn btn-sm btn-outline-secondary ak-tahapan-btn mb-1" data-tahapan="' + t + '"' + active + '>' +
                escapeHtml(tahapanLabel(t)) + ' <span class="badge"' + badge + '>' + filled + '/' + total + '</span> ' +
                approvalBadge(status) + '</button> ';
        });
        $('#akTahapanPicker').html(html);
    }

    function renderAktualTable(d) {
        let t = akCurrentTahapan;
        if (!t) {
            $('#akTableHead').html('');
            $('#akTableBody').html('');
            $('#akLockNotice').hide();
            return;
        }

        let processed = isTahapanProcessed(d, t);
        let status = (d.approval_status || {})[t];
        let info = (d.approval_info || {})[t] || {};
        let lockInfo = (d.locking || {})[t] || {};
        let karyawanLocked = !!lockInfo.karyawan && !d.can_edit_locked;

        let stageParts = '';
        ['associate', 'manager'].forEach(function (role) {
            let st = info[role];
            if (!st || !st.status) return;
            stageParts += ' &middot; <strong>' + (role === 'associate' ? 'Assoc. Manager' : 'Manager') + ':</strong> ' +
                (st.status === 'approved' ? 'disetujui' : 'ditolak') + ' oleh ' + escapeHtml(st.by_label || '-') +
                (st.at ? ' pada ' + escapeHtml(st.at) : '');
        });

        let processedLabel = status === 'associate_approved'
            ? 'tahap 1 disetujui (menunggu approval Manager)'
            : (status === 'approved' ? 'disetujui' : 'ditolak');
        let lockMsg = '';
        if (processed) {
            lockMsg += '<div class="alert alert-warning py-2 mb-0" style="font-size:.85rem;">' +
              'Tahapan ini sudah <strong>' + processedLabel + '</strong>' + stageParts +
              '. Input aktual dikunci dan tidak dapat diubah.</div>';
        }
        if (karyawanLocked) {
            lockMsg += '<div class="alert alert-danger py-2 mb-0" style="font-size:.85rem;">' +
              'Kolom <strong>Aktual (Karyawan)</strong> tahapan ' + escapeHtml(tahapanLabel(t)) +
              ' sedang dikunci (LOCKING IPP). Hubungi Admin HR untuk membuka kunci.</div>';
        }
        if (lockMsg !== '') {
            $('#akLockNotice').html(lockMsg).show();
        } else {
            $('#akLockNotice').hide();
        }

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
            '<th class="text-center" style="min-width:70px;">Review</th>' +
            '<th class="text-center ak-month-head" style="min-width:150px;">' + escapeHtml(tahapanLabel(t)) + ' Aktual (Departemen)</th>' +
            '<th class="text-center ak-month-head" style="min-width:150px;">' + escapeHtml(tahapanLabel(t)) + ' Aktual (Karyawan)' +
            '<br><button type="button" class="btn btn-xs btn-outline-info mt-1" id="btnSalinDepartemen" style="font-size:.7rem; padding:.1rem .3rem;" ' + (processed || karyawanLocked ? 'disabled' : '') + '>⧉ Salin dari Departemen</button></th>' +
            '<th class="text-center ak-month-head" style="min-width:160px;">' + escapeHtml(tahapanLabel(t)) + ' Aktual (Atasan)</th>' +
            '<th class="text-center" style="min-width:130px;">Dokumen Pendukung</th>' +
            '<th class="text-center" style="min-width:110px;">Diskusi</th>' +
            '</tr>';
        $('#akTableHead').html(thead);

        let tbody = '';
        (d.details || []).forEach(function (dtl) {
            let target = dtl.target;
            let aktual = dtl.aktual[t] ?? '';
            let aktualVarchar = (dtl.aktual_varchar || {})[t] ?? '';
            let aktualAtasan = (dtl.aktual_atasan || {})[t] ?? '';
            let aktualAtasanVarchar = (dtl.aktual_atasan_varchar || {})[t] ?? '';
            let byAtasan = (dtl.aktual_atasan_by || {})[t] || '';
            let aktualDepartemen = (dtl.aktual_departemen || {})[t] ?? '';
            let aktualDepartemenVarchar = (dtl.aktual_departemen_varchar || {})[t] ?? '';
            let byDepartemen = (dtl.aktual_departemen_by || {})[t] || '';
            let reviewedMap = (dtl.review || {});
            let reviewed = Object.values(reviewedMap).some(Boolean);
            let tText = target !== null && target !== '' ? target : '-';
            let wText = (dtl.weight !== null && dtl.weight !== undefined && dtl.weight !== '') ? dtl.weight : '-';
            let locked = processed || karyawanLocked || target === null || target === '';
            let reviewLocked = processed;
            let textRange = isTextRange(dtl);
            let deptDisplay = textRange ? aktualDepartemenVarchar : aktualDepartemen;
            let atasanDisplay = textRange ? aktualAtasanVarchar : aktualAtasan;
            let aktualCell;
            if (textRange) {
                let opts = '<option value="">- Pilih -</option>';
                rangeTextLabels(dtl).forEach(function (label) {
                    let sel = (String(label) === String(aktualVarchar)) ? ' selected' : '';
                    opts += '<option value="' + escapeHtml(label) + '"' + sel + '>' + escapeHtml(label) + '</option>';
                });
                aktualCell = '<select class="form-control form-control-sm ak-aktual-input" ' +
                    'data-detail="' + dtl.id + '" data-tahapan="' + t + '" data-mode="equals" ' +
                    (locked ? 'disabled' : '') + '>' + opts + '</select>';
            } else {
                let dec = akScaleDecimals(dtl);
                let decAttr = (dec === null) ? '' : ' data-decimals="' + dec + '"';
                let modeAttr = (dec === 0) ? 'inputmode="numeric"' : 'inputmode="decimal"';
                let displayAktual = (dec === null) ? aktual : akFmtScaled(aktual, dec);
                aktualCell = '<input type="text" class="form-control form-control-sm ak-aktual-input" ' +
                    'data-detail="' + dtl.id + '" data-tahapan="' + t + '" data-mode="number" value="' + escapeHtml(displayAktual) + '" placeholder="' + (displayAktual === '' ? 'Wajib diisi' : '-') + '" ' + modeAttr + decAttr + rangeTierAttr(dtl) + ' ' +
                    (locked ? 'disabled' : '') + '>';
            }
            tbody += '<tr>' +
                '<td class="text-center">' + dtl.no_urut + '</td>' +
                '<td>' + escapeHtml(dtl.objective || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpa || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.kpi) + '</td>' +
                '<td>' + escapeHtml(dtl.classification || '-') + '</td>' +
                '<td>' + escapeHtml(dtl.uom || '-') + '</td>' +
                '<td class="text-center">' + escapeHtml(wText) + '</td>' +
                '<td class="ak-target-cell">' + rangeScaleHtml(dtl, tText) + '</td>' +
                '<td>' + escapeHtml(dtl.due_date || '-') + '</td>' +
                '<td class="text-center"><input type="checkbox" class="ak-review-checkbox" ' +
                'data-ipp="' + akCurrentIpp.ipp_id + '" data-detail="' + dtl.id + '" data-tahapan="' + t + '" ' +
                (reviewed ? 'checked disabled' : '') +
                (reviewLocked && !reviewed ? ' disabled' : '') +
                ' style="cursor:pointer; width:17px; height:17px;"></td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm" ' +
                'value="' + escapeHtml(deptDisplay) + '" placeholder="-" readonly>' +
                (byDepartemen ? '<div class="text-muted" style="font-size:.68rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byDepartemen) + '</div>' : '') +
                '</td>' +
                '<td class="text-center">' + aktualCell + '</td>' +
                '<td class="text-center"><input type="text" class="form-control form-control-sm" ' +
                'value="' + escapeHtml(atasanDisplay) + '" placeholder="-" readonly>' +
                (byAtasan ? '<div class="text-muted" style="font-size:.68rem; line-height:1.1; margin-top:2px;">' + escapeHtml(byAtasan) + '</div>' : '') +
                '</td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-secondary btn-ak-bukti" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + t + '" style="white-space:nowrap;">Dokumen Pendukung (' + buktiCount(dtl.id, t) + ')</button></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm ' +
                (diskusiAnswered(dtl.id, t) ? 'btn-success' : 'btn-outline-primary') + ' btn-ak-diskusi" ' +
                'data-detail="' + dtl.id + '" data-tahapan="' + t + '" style="white-space:nowrap;">? Diskusi' +
                diskusiBadgeHtml(openDiskusiCount(dtl.id, t)) + '</button></td>' +
                '</tr>';
        });

        $('#akTableBody').html(tbody);
        $('#btnSaveAktual').prop('disabled', processed || karyawanLocked);
    }

    function renderAkTable(d) {
        $('#btnSaveAktual').text('Simpan Aktual');
        renderAktualTable(d);
    }

    // === EVENTS ===
    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal.show').length) {
            $('body').addClass('modal-open');
        }
    });

    // Bulatkan input aktual sesuai presisi skala, lalu pastikan nilainya dapat skor.
    $(document).on('blur', '.ak-aktual-input[data-mode="number"]', function () {
        let $inp = $(this);
        if ($inp.is(':disabled')) return;
        let val = $.trim($inp.val() || '');
        if (val === '') return;
        let dec = parseInt($inp.attr('data-decimals'), 10);
        if (!isNaN(dec)) {
            let rounded = akRoundTo(val, dec);
            if (rounded !== null) {
                val = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                $inp.val(val);
            }
        }
        let tiers = inputTiers($inp);
        if (tiers && !rangeMatchesAnyTier(tiers, val)) {
            AK_SWAL.fire('Peringatan', 'Aktual KPI #' + ($inp.attr('data-no-urut') || '-') + ' tidak masuk rentang skor mana pun.', 'warning');
            $inp.val('');
        }
    });

    $(document).on('change', '.ak-review-checkbox', function () {
        let $cb = $(this);
        if ($cb.is(':disabled')) return;
        let ippId = $cb.data('ipp');
        let detailId = $cb.data('detail');
        let tahapan = $cb.data('tahapan');
        if (!$cb.is(':checked')) return;
        if (!ippId) {
            $cb.prop('checked', false);
            AK_SWAL.fire('Error', 'Data IPP belum dimuat. Silakan tutup dan buka kembali IPP ini.', 'error');
            return;
        }
        try {
            AK_SWAL.fire({
                title: 'Peringatan',
                text: 'Anda sudah memahami IPP Anda?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Paham',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#4a148c',
                cancelButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) {
                    $cb.prop('checked', false);
                    return;
                }
                $cb.prop('checked', true).prop('disabled', true);
                $.ajax({
                    url: "{{ url('/hr/ipp-aktual/review') }}/" + ippId,
                    type: 'POST',
                    data: { detail_id: detailId, tahapan: tahapan },
                    headers: { 'X-CSRF-TOKEN': csrf() },
                    success: function () {
                        if (akCurrentIpp) {
                            let dtl = (akCurrentIpp.details || []).find(function (d) { return d.id == detailId; });
                            if (dtl) {
                                dtl.review = dtl.review || {};
                                (akCurrentIpp.tahapan || []).forEach(function (tk) { dtl.review[tk] = true; });
                                dtl.review[tahapan] = true;
                            }
                        }
                        $('#akTableBody .ak-review-checkbox[data-detail="' + detailId + '"]').prop('checked', true).prop('disabled', true);
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Review tersimpan.', timer: 1800, timerProgressBar: true, showConfirmButton: false });
                    },
                    error: function (xhr) {
                        $cb.prop('checked', false).prop('disabled', false);
                        AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan review.', 'error');
                    }
                });
            });
        } catch (err) {
            $cb.prop('checked', false);
            console.error(err);
            AK_SWAL.fire('Error', 'Terjadi kesalahan: ' + (err && err.message ? err.message : err), 'error');
        }
    });
    $(document).on('click', '.btn-ak-isi', function () {
        openAktual($(this).data('id'), $(this).data('tahapan'));
    });

    $(document).on('click', '.ak-diskusi-row', function () {
        let ippId = $(this).data('ipp');
        if (!ippId) return;
        akDiskusiAutoOpen = $(this).data('detail') || null;
        openAktual(ippId, $(this).data('tahapan') || null);
    });

    $(document).on('click', '.btn-ak-diskusi', function () {
        akDiskusiDetailId = $(this).data('detail');
        akDiskusiTahapan = $(this).data('tahapan');
        let dtl = (akCurrentIpp.details || []).find(function (d) { return d.id == akDiskusiDetailId; });
        $('#akDiskusiTitle').text('Diskusi - KPI #' + (dtl ? dtl.no_urut : akDiskusiDetailId));
        renderDiskusiThread(akDiskusiDetailId, akDiskusiTahapan);
        $('#akDiskusiPesan').val('');
        $('#akDiskusiModal').modal('show');
    });

    $(document).on('click', '.btn-ak-diskusi-edit', function () {
        $(this).closest('.ak-diskusi-item').find('.ak-diskusi-edit-form').toggle();
    });

    $(document).on('click', '.btn-ak-diskusi-cancel', function () {
        $(this).closest('.ak-diskusi-item').find('.ak-diskusi-edit-form').hide();
    });

    $(document).on('click', '.btn-ak-diskusi-save', function () {
        let $btn = $(this);
        let id = $btn.data('id');
        let $item = $btn.closest('.ak-diskusi-item');
        let pesan = $.trim($item.find('.ak-diskusi-pesan-input').val());
        if (!pesan) {
            AK_SWAL.fire('Peringatan', 'Pertanyaan tidak boleh kosong.', 'warning');
            return;
        }
        $btn.prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: "{{ url('/hr/ipp-aktual/diskusi/update') }}/" + id,
            type: 'POST',
            data: { pesan: pesan },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $btn.prop('disabled', false).text('Simpan');
                loadDiskusi(function () {
                    renderDiskusiThread(akDiskusiDetailId, akDiskusiTahapan);
                    refreshDiskusiButton(akDiskusiDetailId, akDiskusiTahapan);
                });
                loadDiskusiSection();
                AK_SWAL.fire('Berhasil', res.message || 'Pertanyaan diperbarui.', 'success');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Simpan');
                AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal memperbarui pertanyaan.', 'error');
            }
        });
    });

    $('#btnAkDiskusiKirim').on('click', function () {
        let pesan = $.trim($('#akDiskusiPesan').val());
        if (!pesan) {
            AK_SWAL.fire('Peringatan', 'Tulis pertanyaan diskusi terlebih dahulu.', 'warning');
            return;
        }
        if (!akOtoId) return;
        let $btn = $(this).prop('disabled', true).text('Mengirim...');
        $.ajax({
            url: "{{ url('/hr/ipp-aktual/diskusi') }}/" + akOtoId,
            type: 'POST',
            data: {
                ipp_detail_id: akDiskusiDetailId,
                tahapan: akDiskusiTahapan,
                pesan: pesan
            },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $btn.prop('disabled', false).text('Kirim Pertanyaan');
                $('#akDiskusiPesan').val('');
                loadDiskusi(function () {
                    renderDiskusiThread(akDiskusiDetailId, akDiskusiTahapan);
                    refreshDiskusiButton(akDiskusiDetailId, akDiskusiTahapan);
                });
                loadDiskusiSection();
                AK_SWAL.fire('Berhasil', res.message || 'Pertanyaan berhasil dikirim.', 'success');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Kirim Pertanyaan');
                AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal mengirim pertanyaan.', 'error');
            }
        });
    });

    $(document).on('click', '.ak-tahapan-btn', function () {
        akCurrentTahapan = $(this).data('tahapan');
        renderTahapanPicker(akCurrentIpp);
        renderAkTable(akCurrentIpp);
    });

    $(document).on('click', '.btn-ak-bukti', function () {
        akBuktiDetailId = $(this).data('detail');
        akBuktiTahapan = $(this).data('tahapan');
        let dtl = (akCurrentIpp.details || []).find(function (x) { return x.id == akBuktiDetailId; });
        $('#akBuktiTitle').text('Dokumen Pendukung - KPI #' + (dtl ? dtl.no_urut : akBuktiDetailId) +
            ' (' + tahapanLabel(akBuktiTahapan) + ')');
        $('#akBuktiFiles').val('');
        refreshBukti();
        $('#akBuktiModal').modal('show');
    });

    $('#btnAkBuktiUpload').on('click', function () {
        if (!akOtoId || !akBuktiDetailId || !akBuktiTahapan) return;
        let files = document.getElementById('akBuktiFiles').files;
        if (!files.length) {
            AK_SWAL.fire('Peringatan', 'Pilih minimal satu file (PDF/JPG/PNG/DOC/DOCX/XLS/XLSX/XLSB/CSV).', 'warning');
            return;
        }
        let fd = new FormData();
        fd.append('ipp_otorisasi_id', akOtoId);
        fd.append('ipp_detail_id', akBuktiDetailId);
        fd.append('tahapan', akBuktiTahapan);
        for (let i = 0; i < files.length; i++) {
            fd.append('files[]', files[i]);
        }
        let $btn = $(this).prop('disabled', true).text('Mengunggah...');
        $.ajax({
            url: "{{ url('/hr/ipp-aktual/bukti/upload') }}",
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $btn.prop('disabled', false).text('Unggah');
                $('#akBuktiFiles').val('');
                refreshBukti();
                AK_SWAL.fire('Berhasil', res.message || 'Dokumen pendukung berhasil diunggah.', 'success');
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Unggah');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let msgs = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    AK_SWAL.fire({ icon: 'error', title: 'Error', html: msgs });
                    return;
                }
                AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal mengunggah dokumen pendukung.', 'error');
            }
        });
    });

    $(document).on('click', '.btn-ak-bukti-delete', function () {
        let id = $(this).data('id');
        AK_SWAL.fire({
            title: 'Hapus dokumen pendukung ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: "{{ url('/hr/ipp-bukti') }}/" + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf() },
                success: function (res) {
                    refreshBukti();
                    AK_SWAL.fire('Berhasil', res.message || 'Dokumen pendukung berhasil dihapus.', 'success');
                },
                error: function (xhr) {
                    AK_SWAL.fire('Error', xhr.responseJSON?.message || 'Gagal menghapus dokumen pendukung.', 'error');
                }
            });
        });
    });

    $('#btnSaveAktual').on('click', function () {
        if (!akCurrentIpp) return;

let jumlahBawahan = $.trim($('#akJumlahBawahan').val());
        if (jumlahBawahan === '') {
            AK_SWAL.fire('Peringatan', 'Isi Jumlah Bawahan Langsung (bawahan peserta) terlebih dahulu.', 'warning');
            return;
        }

        let uncheckedReview = $('#akTableBody .ak-review-checkbox:not(:disabled)')
            .filter(function () { return !$(this).is(':checked'); });
        if (uncheckedReview.length) {
            AK_SWAL.fire('Peringatan', 'Centang Review pada semua KPI untuk mengonfirmasi bahwa Anda sudah memahami IPP Anda.', 'warning');
            return;
        }

        let values = [];
        let invalidKpi = null;
        $('#akTableBody .ak-aktual-input:not(:disabled)').each(function () {
            let $inp = $(this);
            let val = $.trim($inp.val() || '');
            let isText = $inp.data('mode') === 'equals';
            if (!isText && val !== '') {
                let dec = parseInt($inp.attr('data-decimals'), 10);
                if (!isNaN(dec)) {
                    let rounded = akRoundTo(val, dec);
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
            let reviewed = $inp.closest('tr').find('.ak-review-checkbox').is(':checked');
            values.push({
                detail_id: $inp.data('detail'),
                tahapan: $inp.data('tahapan'),
                aktual: (!isText && val !== '') ? val : null,
                aktual_varchar: (isText && val !== '') ? val : null,
                review: reviewed ? 1 : 0
            });
        });

        if (invalidKpi !== null) {
            AK_SWAL.fire('Peringatan', 'Aktual KPI #' + invalidKpi + ' tidak masuk rentang skor mana pun.', 'warning');
            return;
        }

        if (!values.length) {
            AK_SWAL.fire('Peringatan', 'Tidak ada data AKTUAL untuk disimpan.', 'warning');
            return;
        }

        $('#btnSaveAktual').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ url('/hr/ipp-aktual/save') }}/" + akCurrentIpp.ipp_id,
            type: 'POST',
            data: {
                values: values,
                jumlah_bawahan: jumlahBawahan
            },
            headers: { 'X-CSRF-TOKEN': csrf() },
            success: function (res) {
                $('#btnSaveAktual').prop('disabled', false).text('Simpan Aktual');
                renderTahapanPicker(akCurrentIpp);
                AK_SWAL.fire('Berhasil', res.message || 'Data AKTUAL berhasil disimpan.', 'success');
                loadList();
            },
            error: function (xhr) {
                $('#btnSaveAktual').prop('disabled', false).text('Simpan Aktual');
                let msg = xhr.responseJSON?.message || 'Gagal menyimpan data AKTUAL.';
                AK_SWAL.fire('Error', msg, 'error');
            }
        });
    });

    $(document).on('click', '#btnSalinDepartemen', function () {
        if (!akCurrentIpp || !akCurrentTahapan) return;
        let copied = 0;
        $('#akTableBody .ak-aktual-input:not(:disabled)').each(function () {
            let $inp = $(this);
            if ($.trim($inp.val()) !== '') return;
            let $row = $inp.closest('tr');
            let deptVal = $.trim($row.find('td:eq(10) input').val());
            if (deptVal === '' || deptVal === '-') return;
            let dec = parseInt($inp.attr('data-decimals'), 10);
            if (!isNaN(dec)) {
                let rounded = akRoundTo(deptVal, dec);
                if (rounded !== null) {
                    deptVal = dec > 0 ? rounded.toFixed(dec) : String(rounded);
                }
            }
            let tiers = inputTiers($inp);
            if (tiers && !rangeMatchesAnyTier(tiers, deptVal)) return;
            $inp.val(deptVal);
            copied++;
        });
        AK_SWAL.fire('Selesai', copied + ' nilai disalin dari Departemen.', 'success');
    });

    // ================= REALTIME DISKUSI (WEBSOCKET) =================
    let akRtTimer = null;
    let akRtPending = {};
    let akRtSyncTimer = null;

    function akRtThreadItem(ev) {
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

    function akRtRefreshThread() {
        let draftAsk = $('#akDiskusiPesan').val();
        let drafts = {};
        $('#akDiskusiThread .ak-diskusi-item').each(function () {
            let id = $(this).data('id');
            let $form = $(this).find('.ak-diskusi-edit-form');
            if ($form.length && $form.is(':visible')) {
                drafts[id] = $(this).find('.ak-diskusi-pesan-input').val();
            }
        });
        renderDiskusiThread(akDiskusiDetailId, akDiskusiTahapan);
        if (draftAsk) $('#akDiskusiPesan').val(draftAsk);
        Object.keys(drafts).forEach(function (id) {
            let $item = $('#akDiskusiThread .ak-diskusi-item[data-id="' + id + '"]');
            if (!$item.length) return;
            $item.find('.ak-diskusi-edit-form').show();
            $item.find('.ak-diskusi-pesan-input').val(drafts[id]);
        });
    }

    function akRtSectionSync() {
        clearTimeout(akRtSyncTimer);
        akRtSyncTimer = setTimeout(function () { loadDiskusiSection(); }, 500);
    }

    function akRtToast(ev) {
        if (typeof Swal === 'undefined') return;
        if (String(ev.aktor_nik) === String(akMyNik)) return;
        let msg;
        if (ev.type === 'answered') {
            msg = (ev.edited ? 'Jawaban diskusi diperbarui' : 'Diskusi KPI Anda dijawab') +
                ' oleh ' + (ev.nama_penjawab || ev.nik_penjawab);
        } else if (ev.type === 'updated') {
            msg = 'Pertanyaan diskusi diperbarui';
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

    function akRtApply(ev) {
        let item = akRtThreadItem(ev);

        if (akOtoId && String(akOtoId) === String(ev.otorisasi_id)) {
            let found = false;
            (akDiskusi || []).forEach(function (x, i) {
                if (String(x.id) === String(ev.id)) {
                    akDiskusi[i] = item;
                    found = true;
                }
            });
            if (!found) akDiskusi.push(item);
            $('#akTableBody .btn-ak-diskusi').each(function () {
                refreshDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
            });
            if ($('#akDiskusiModal').hasClass('show')
                && String(akDiskusiDetailId) === String(ev.ipp_detail_id)
                && String(akDiskusiTahapan || '') === String(ev.tahapan || '')) {
                akRtRefreshThread();
            }
        }

        let $cell = $('#akList tr[data-oto="' + ev.otorisasi_id + '"][data-tahapan="' + ev.tahapan + '"]').find('.ak-cell-diskusi');
        if ($cell.length) {
            $cell.html(akDiskusiCellHtml(ev.open_count, ev.total_count));
        }

        let $row = $('#akDiskusiListBody tr[data-id="' + ev.id + '"]');
        if ($row.length) {
            $row.replaceWith(akDiskusiSectionRowHtml(ev));
        }
        akRtSectionSync();

        akRtToast(ev);
    }

    function akRtHandle(ev) {
        akRtPending[ev.id] = ev;
        clearTimeout(akRtTimer);
        akRtTimer = setTimeout(function () {
            let items = akRtPending;
            akRtPending = {};
            Object.keys(items).forEach(function (id) { akRtApply(items[id]); });
        }, 300);
    }

    function akRtResync() {
        clearTimeout(akRtSyncTimer);
        akRtSyncTimer = setTimeout(function () {
            loadDiskusiSection();
            if (akOtoId) {
                loadDiskusi(function () {
                    $('#akTableBody .btn-ak-diskusi').each(function () {
                        refreshDiskusiButton($(this).data('detail'), $(this).data('tahapan'));
                    });
                });
            }
        }, 500);
    }

    (function akRtSubscribe() {
        if (!window.Echo || !akMyNik || typeof window.Echo.private !== 'function') return;
        window.Echo.private('ipp-diskusi.' + akMyNik)
            .listen('.ipp-diskusi-created', function (ev) { akRtHandle(ev); })
            .listen('.ipp-diskusi-updated', function (ev) { akRtHandle(ev); })
            .listen('.ipp-diskusi-answered', function (ev) { akRtHandle(ev); });

        let pusher = window.Echo.connector && window.Echo.connector.pusher;
        if (pusher && pusher.connection && typeof pusher.connection.bind === 'function') {
            let firstConnect = true;
            pusher.connection.bind('connected', function () {
                if (firstConnect) { firstConnect = false; return; }
                akRtResync();
            });
        }
    })();

    $(function () {
        loadList();
        loadDiskusiSection();
    });
</script>
@endpush
