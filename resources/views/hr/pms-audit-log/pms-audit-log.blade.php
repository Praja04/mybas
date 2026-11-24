@extends('layouts.base')

@section('content')
<style>
    .pms-log-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1.25rem; margin-bottom: 1.25rem; }
    .pms-log-card h5 { color: #a80000; font-weight: 700; }
    .pms-log-table th { background: #fdecea; color: #a80000; font-size: .8rem; vertical-align: middle; white-space: nowrap; }
    .pms-log-table td { font-size: .8rem; vertical-align: middle; }
    .pms-log-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: .72rem; font-weight: 600; }
    .pms-log-badge-view { background: #e3f2fd; color: #0d47a1; }
    .pms-log-badge-save { background: #e8f5e9; color: #1b5e20; }
    .pms-log-badge-approve { background: #fff3e0; color: #e65100; }
    .pms-log-badge-danger { background: #fdecea; color: #a80000; }
    .pms-log-badge-risk { background: #a80000; color: #fff; }
    .pms-log-badge-ok { background: #e8f5e9; color: #1b5e20; }
    .pms-log-badge-mute { background: #eceff1; color: #455a64; }
    .pms-log-uri { font-size: .72rem; color: #666; word-break: break-all; }
    .pms-log-meta { font-size: .72rem; color: #555; }
</style>

<div class="container-fluid">
    <div class="pms-log-card">
        <h4 style="color:#a80000; font-weight:700; margin:0;">Log Aktivitas PMS</h4>
        <p class="text-muted mb-0" style="font-size:.85rem;">Jejak audit siapa yang melihat/mengubah nilai IPP &amp; PK (halaman, persetujuan, export, locking, dsb). Halaman ini hanya untuk HR/Admin.</p>
        <p class="text-muted mb-0" style="font-size:.8rem;">Log dibatasi maksimum {{ number_format($maxRows, 0, ',', '.') }} baris. Log paling lama otomatis dihapus saat batas tercapai.</p>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="pms-log-card">
        <h5>Filter</h5>
        <form id="formFilter" class="row" style="gap:0;">
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Dari tanggal</label>
                <input type="date" id="fltFrom" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Sampai tanggal</label>
                <input type="date" id="fltTo" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">User (NIK/nama)</label>
                <input type="text" id="fltUser" class="form-control form-control-sm" placeholder="Cari user...">
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Halaman</label>
                <select id="fltModule" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    @foreach ($modules as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Aksi</label>
                <select id="fltAction" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    @foreach ($actions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Status</label>
                <select id="fltStatus" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Target NIK</label>
                <input type="text" id="fltTarget" class="form-control form-control-sm" placeholder="NIK yang disentuh...">
            </div>
            <div class="col-12 mb-2">
                <button type="submit" class="btn btn-sm btn-danger">Tampilkan</button>
                <button type="button" id="btnReset" class="btn btn-sm btn-outline-secondary">Reset</button>
            </div>
        </form>
    </div>

    <div class="pms-log-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Daftar Aktivitas</h5>
            <div class="text-muted" style="font-size:.8rem;" id="logSummary"></div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover pms-log-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:40px;">No</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Halaman</th>
                        <th>Aksi</th>
                        <th>Target</th>
                        <th>Detail</th>
                        <th>Route</th>
                        <th>IP</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="logTbody">
                    <tr><td colspan="10" class="text-center text-muted">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size:.8rem;" id="pageInfo"></div>
            <div>
                <button class="btn btn-sm btn-outline-secondary" id="btnPrev">Sebelumnya</button>
                <button class="btn btn-sm btn-outline-secondary" id="btnNext">Berikutnya</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    let page = 1;
    let perPage = 20;
    let lastPage = 1;

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function actionBadge(action) {
        let cls = 'pms-log-badge-mute';
        if (action === 'view') cls = 'pms-log-badge-view';
        else if (action === 'save' || action === 'review') cls = 'pms-log-badge-save';
        else if (action === 'approve') cls = 'pms-log-badge-approve';
        else if (action === 'locking' || action === 'delete') cls = 'pms-log-badge-danger';
        return '<span class="pms-log-badge ' + cls + '">' + escapeHtml(action || '-') + '</span>';
    }

    function statusBadge(code) {
        if (code == null) return '<span class="pms-log-badge pms-log-badge-mute">-</span>';
        let cls = 'pms-log-badge-mute';
        if (code >= 400) cls = 'pms-log-badge-risk';
        else if (code >= 200 && code < 300) cls = 'pms-log-badge-ok';
        return '<span class="pms-log-badge ' + cls + '">' + escapeHtml(code) + '</span>';
    }

    function metaText(meta) {
        if (!meta || typeof meta !== 'object') return '-';
        let parts = [];
        if (meta.periode) parts.push('periode ' + meta.periode);
        if (meta.tahapan) parts.push('tahapan ' + meta.tahapan);
        if (meta.tab) parts.push('tab ' + meta.tab);
        if (meta.mode) parts.push('mode ' + meta.mode);
        if (meta.status) parts.push('status ' + meta.status);
        if (meta.stage) parts.push('stage ' + meta.stage);
        if (meta.is_active !== undefined) parts.push('is_active ' + meta.is_active);
        return parts.length ? escapeHtml(parts.join(' · ')) : '-';
    }

    function loadLog() {
        $('#logTbody').html('<tr><td colspan="10" class="text-center text-muted">Memuat data...</td></tr>');
        $.get('/hr/pms-audit-log/list', {
            page: page,
            per_page: perPage,
            date_from: $('#fltFrom').val(),
            date_to: $('#fltTo').val(),
            user: $('#fltUser').val(),
            module: $('#fltModule').val(),
            action: $('#fltAction').val(),
            status: $('#fltStatus').val(),
            target_nik: $('#fltTarget').val()
        }, function (res) {
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="10" class="text-center text-muted">Tidak ada data.</td></tr>';
            }
            items.forEach(function (x, i) {
                html += '<tr>' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td style="white-space:nowrap;">' + escapeHtml(x.created_at) + '</td>' +
                    '<td><strong>' + escapeHtml(x.user_name || '-') + '</strong>' +
                        '<div class="text-muted" style="font-size:.72rem;">' + escapeHtml(x.user_nik || '') + ' · ' + escapeHtml(x.user_username || '') + '</div></td>' +
                    '<td>' + escapeHtml(x.module_label || x.module || '-') + '</td>' +
                    '<td>' + actionBadge(x.action) + '<div class="text-muted" style="font-size:.72rem;">' + escapeHtml(x.action_label || '') + '</div></td>' +
                    '<td>' + (x.target_nik ? escapeHtml(x.target_nik) : '-') +
                        (x.target_id ? '<div class="text-muted" style="font-size:.72rem;">id ' + escapeHtml(x.target_id) + '</div>' : '') + '</td>' +
                    '<td class="pms-log-meta">' + metaText(x.meta) + '</td>' +
                    '<td class="pms-log-uri">' + escapeHtml(x.http_method || '') + ' ' + escapeHtml(x.uri || '') + '</td>' +
                    '<td style="font-size:.72rem; white-space:nowrap;">' + escapeHtml(x.ip_address || '-') + '</td>' +
                    '<td class="text-center">' + statusBadge(x.status_code) + '</td>' +
                    '</tr>';
            });
            $('#logTbody').html(html);
            $('#pageInfo').text('Halaman ' + res.meta.page + ' dari ' + res.meta.last_page + ' (' + res.meta.total + ' aktivitas)');
            $('#logSummary').text(res.meta.total + ' aktivitas tercatat');
            lastPage = res.meta.lastPage || res.meta.last_page;
            $('#btnPrev').prop('disabled', res.meta.page <= 1);
            $('#btnNext').prop('disabled', res.meta.page >= res.meta.last_page);
        }, 'json').fail(function () {
            $('#logTbody').html('<tr><td colspan="10" class="text-center text-danger">Gagal memuat data.</td></tr>');
        });
    }

    $('#formFilter').on('submit', function (e) {
        e.preventDefault();
        page = 1;
        loadLog();
    });
    $('#btnReset').on('click', function () {
        $('#formFilter')[0].reset();
        page = 1;
        loadLog();
    });
    $('#btnPrev').on('click', function () {
        if (page > 1) { page--; loadLog(); }
    });
    $('#btnNext').on('click', function () {
        if (page < lastPage) { page++; loadLog(); }
    });

    loadLog();
});
</script>
@endpush
