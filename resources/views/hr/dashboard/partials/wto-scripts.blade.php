{{-- WT&O Scripts: tab switching (no fullscreen exit), filter + table --}}
<script>
(function () {
    'use strict';

    window.loadWtoData        = loadWtoData;
    window.switchDashboardTab = switchDashboardTab;

    const WTO_TABS    = ['hdStatsSection', 'hdWtoSection', 'hdIzinSection'];
    const WTO_CYCLE_MS = 10000;
    let   currentTab   = 'hdStatsSection';
    let   cycleTimer   = null;
    let   cycleEnabled = false;
    let   wtoCurrentPage = 1;
    // Persistent wto_nama selection (saved on Terapkan Filter click).
    // Reload-safe: DOM checkboxes can be re-rendered by loadWtoNames() but
    // this variable holds the selection so the chart request still includes it.
    let   currentWtoNama = [];

    // Use a global "generation" counter to invalidate stale intervals
    // if the IIFE is accidentally re-evaluated
    if (typeof window.__wtoCycleGen === 'undefined') window.__wtoCycleGen = 0;
    if (typeof window.__wtoCycleActive === 'undefined') window.__wtoCycleActive = false;

    function fmtDate(s) {
        if (!s) return '';
        return String(s).substring(0, 10);
    }

    function parseTglInRange() {
        const raw = ($('#wtoTglInRange').val() || '').trim();
        if (!raw) return { from: '', to: '' };
        const parts = raw.split(/\s+to\s+|\s+-\s+/);
        if (parts.length === 2) return { from: parts[0].trim(), to: parts[1].trim() };
        const single = parts[0].trim();
        return { from: single, to: single };
    }

    function getWtoFilterParams(page) {
        const p = {
            page: page || wtoCurrentPage,
            per_page: $('#wtoPerPage').val() || 25,
            departmen: getMultiSelectValues('wto_departmen'),
            sub_departmen: getMultiSelectValues('wto_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('wto_tipe_karyawan'),
            pws: getMultiSelectValues('wto_pws'),
            wto_nama: currentWtoNama.length > 0
                ? currentWtoNama.slice()
                : getMultiSelectValues('wto_nama'),
            @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            type_karyawan: @json($typeKaryawanMode),
            @endif
        };
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;
        if (Array.isArray(p.pws) && p.pws.length === 0) delete p.pws;
        if (Array.isArray(p.wto_nama) && p.wto_nama.length === 0) delete p.wto_nama;

        const tgl = parseTglInRange();
        if (tgl.from) p.wto_tgl_in_from = tgl.from;
        if (tgl.to)   p.wto_tgl_in_to   = tgl.to;

        return p;
    }

    // Build filter params for wto-names endpoint (excludes wto_nama itself)
    function getWtoNamesFilterParams() {
        const p = {
            departmen: getMultiSelectValues('wto_departmen'),
            sub_departmen: getMultiSelectValues('wto_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('wto_tipe_karyawan'),
            pws: getMultiSelectValues('wto_pws'),
            @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            type_karyawan: @json($typeKaryawanMode),
            @endif
        };
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;
        if (Array.isArray(p.pws) && p.pws.length === 0) delete p.pws;
        return p;
    }

    function loadWtoPwsGroups() {
        const params = {
            departmen: getMultiSelectValues('wto_departmen'),
            sub_departmen: getMultiSelectValues('wto_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('wto_tipe_karyawan'),
        };
        if (Array.isArray(params.departmen) && params.departmen.length === 0) delete params.departmen;
        if (Array.isArray(params.sub_departmen) && params.sub_departmen.length === 0) delete params.sub_departmen;
        if (Array.isArray(params.tipe_karyawan) && params.tipe_karyawan.length === 0) delete params.tipe_karyawan;

        const savedSelection = getMultiSelectValues('wto_pws');

        $.get("{{ url('/hr/hrdashboard/pws-groups') }}", params, function (res) {
            const groups = res.groups || [];
            const $list = $('#wtoPwsList');
            $list.empty();
            groups.forEach(function (g) {
                const safe = String(g).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                const isChecked = savedSelection.indexOf(g) !== -1 ? 'checked' : '';
                $list.append(
                    '<label class="hd-ms-item">' +
                        '<input type="checkbox" name="wto_pws[]" value="' + safe + '" ' + isChecked + '>' +
                        '<span>' + safe + '</span>' +
                    '</label>'
                );
            });
            const $wrapper = $('.hd-multi-select[data-target="wto_pws"]');
            if ($wrapper.length && typeof updateMsLabel === 'function') {
                updateMsLabel($wrapper);
            }
        });
    }

    function loadWtoData(page) {
        page = page || wtoCurrentPage;
        wtoCurrentPage = page;

        const params = $.param(getWtoFilterParams(page));

        $.get("{{ url('/hr/hrdashboard/wto-data') }}?" + params, function (res) {
            if (res.stats) {
                $('#wtoStatTotalLembur').text((res.stats.total_jam_lembur || 0).toLocaleString());
                $('#wtoStatHariKerja').text((res.stats.jam_lembur_hari_kerja || 0).toLocaleString());
                $('#wtoStatHariLibur').text((res.stats.jam_lembur_hari_libur || 0).toLocaleString());
                $('#wtoStatKaryawanLembur').text((res.stats.total_karyawan_lembur || 0).toLocaleString());
            }

            let rows = '';
            if (!res.data || res.data.length === 0) {
                rows = '<tr><td colspan="10" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>';
            } else {
                res.data.forEach(r => {
                    rows += `
                        <tr>
                            <td>${escapeHtml(r.nik)}</td>
                            <td>${escapeHtml(r.nama || '')}</td>
                            <td>${escapeHtml(r.dept || '')}</td>
                            <td>${escapeHtml(r.sub_departmen || '')}</td>
                            <td>${escapeHtml(r.section || '')}</td>
                            <td>${escapeHtml(r.pws || '')}</td>
                            <td>${fmtDate(r.tgl_in)}</td>
                            <td>${r.jam_spkl ?? ''}</td>
                            <td>${r.jam_hovt ?? ''}</td>
                            <td>${escapeHtml(r.no_spkl || '')}</td>
                        </tr>
                    `;
                });
            }
            $('#wtoTbody').html(rows);

            renderWtoPagination(res.meta || {});

            // Reload chart with same filter
            loadWtoChart();

            // Reload top 10 with same filter
            loadWtoTopLembur();
        }).fail(function (xhr) {
            if (xhr.status === 403) {
                Swal.fire('Akses Ditolak', 'Anda tidak memiliki akses ke dashboard ini.', 'error');
            } else {
                Swal.fire('Error', 'Gagal memuat data WT&O.', 'error');
            }
        });
    }

    function renderWtoPagination(meta) {
        const total    = meta.total || 0;
        const page     = meta.page || 1;
        const lastPage = meta.last_page || 1;
        const perPage  = meta.per_page || 25;

        if (total === 0) {
            $('#wtoInfo').text('Menampilkan 0 dari 0 data');
            $('#wtoPagination').html('');
            return;
        }

        const from = (page - 1) * perPage + 1;
        const to   = Math.min(page * perPage, total);
        $('#wtoInfo').text(`Menampilkan ${from}\u2013${to} dari ${total} data`);

        let html = '<nav><ul class="pagination hd-pagination">';
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="1">&laquo;</a></li>`;
        html += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" data-page="${page - 1}">&lsaquo;</a></li>`;
        let start = Math.max(1, page - 2);
        let end   = Math.min(lastPage, page + 2);
        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">\u2026</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            let active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">\u2026</span></li>`;
            html += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#wtoPagination').html(html);
    }

    $(document).on('click', '#wtoPagination .page-link', function () {
        const li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        const page = parseInt($(this).data('page'));
        if (!isNaN(page) && page > 0) loadWtoData(page);
    });

    // ===== WTO LINE CHARTS =====
    let wtoLineChartKaryawan = null;
    let wtoLineChartJamLembur = null;
    let wtoLineChartJamLemburPerDept = null;

    function loadWtoChart() {
        const filterParams = getWtoFilterParams(1);
        filterParams.periode = $('#wtoPeriode').val() || '1-akhir';
        const params = $.param(filterParams);
        $.get("{{ url('/hr/hrdashboard/wto-chart') }}?" + params, function (res) {
            renderWtoCharts(res);
        }).fail(function (xhr) {
            console.error('Gagal load chart WT&O', xhr);
        });
    }

    function loadWtoNames() {
        // Skip if filter card not rendered (no permission or tab hidden)
        const $list = $('#wtoNamaList');
        if ($list.length === 0) return;

        // Capture current selection BEFORE clearing the list
        // (otherwise getMultiSelectValues returns empty after $list.html())
        const savedSelection = getMultiSelectValues('wto_nama');

        const params = $.param(getWtoNamesFilterParams());
        $list.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Memuat...</div>');

        $.get("{{ url('/hr/hrdashboard/wto-names') }}?" + params, function (res) {
            renderWtoNamaList(res.names || [], savedSelection);
        }).fail(function (xhr) {
            if (xhr.status === 403) {
                $list.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Tidak ada akses</div>');
            } else {
                $list.html('<div class="text-danger text-center p-2" style="font-size:.78rem;">Gagal memuat</div>');
            }
        });
    }

    let $wtoNamaList;
    function renderWtoNamaList(names, savedSelection) {
        if (!$wtoNamaList || $wtoNamaList.length === 0) {
            $wtoNamaList = $('#wtoNamaList');
        }
        // Use saved selection if provided, otherwise try to get from DOM
        let selected = savedSelection;
        if (!Array.isArray(selected)) {
            selected = getMultiSelectValues('wto_nama');
        }

        $wtoNamaList.empty();
        if (!names || names.length === 0) {
            $wtoNamaList.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Tidak ada nama</div>');
            return;
        }

        names.forEach(name => {
            const safe = String(name).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            const isChecked = selected.indexOf(name) !== -1 ? 'checked' : '';
            $wtoNamaList.append(
                '<label class="hd-ms-item">' +
                    '<input type="checkbox" name="wto_nama[]" value="' + safe + '" ' + isChecked + '>' +
                    '<span>' + safe + '</span>' +
                '</label>'
            );
        });

        // Update button label to show selection count
        const $wrapper = $('.hd-multi-select[data-target="wto_nama"]');
        if ($wrapper.length && typeof updateMsLabel === 'function') {
            updateMsLabel($wrapper);
        }
    }

    function loadWtoTopLembur() {
        // Skip if user doesn't have permission (card not rendered)
        if ($('#wtoTopLemburTbody').length === 0) return;

        const params = $.param(getWtoFilterParams(1));
        $.get("{{ url('/hr/hrdashboard/wto-top-lembur') }}?" + params, function (res) {
            renderWtoTopLembur(res);
        }).fail(function (xhr) {
            console.error('Gagal load top 10 lembur', xhr);
        });
    }

    function renderWtoTopLembur(res) {
        if (!res || !res.data || res.data.length === 0) {
            $('#wtoTopLemburTbody').html(
                '<tr><td colspan="9" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>'
            );
            return;
        }
        const fmt = (v) => {
            const n = Number(v) || 0;
            return n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        };
        let html = '';
        res.data.forEach((r, i) => {
            const rank = i + 1;
            const badge = rank === 1 ? 'background:#f3e5f5;color:#6a1b9a;font-weight:700;'
                       : rank === 2 ? 'background:#e3f2fd;color:#1565c0;font-weight:600;'
                       : rank === 3 ? 'background:#fff3e0;color:#e65100;font-weight:600;'
                       : '';
            html += `
                <tr>
                    <td><span class="wt-badge" style="${badge}padding:.2rem .5rem;border-radius:4px;">#${rank}</span></td>
                    <td>${escapeHtml(r.nik)}</td>
                    <td>${escapeHtml(r.nama || '')}</td>
                    <td>${escapeHtml(r.dept || '')}</td>
                    <td>${escapeHtml(r.sub_departmen || '')}</td>
                    <td class="text-right">${fmt(r.total_spkl)}</td>
                    <td class="text-right">${fmt(r.total_hovt)}</td>
                    <td class="text-right" style="font-weight:700;color:#4a148c;">${fmt(r.total_lembur)}</td>
                    <td class="text-right">${r.total_records}</td>
                </tr>
            `;
        });
        $('#wtoTopLemburTbody').html(html);
    }

    function buildYearBoundaries(months) {
        if (!months || months.length === 0) return { plotBands: [], plotLines: [] };
        const yearSet = new Set();
        months.forEach(m => yearSet.add(parseInt(m.substring(0, 4))));
        const years = Array.from(yearSet).sort();
        const plotBands = [];
        const plotLines = [];
        const midPoints = [];
        years.forEach((year, idx) => {
            const yearMonths = months.filter(m => m.startsWith(String(year)));
            if (yearMonths.length === 0) return;
            const firstMonth = yearMonths[0];
            const lastMonth  = yearMonths[yearMonths.length - 1];
            const fromTs = new Date(firstMonth + '-01').getTime();
            const lastMonthTs = new Date(lastMonth + '-01').getTime();
            const lm = parseInt(lastMonth.substring(5, 7));
            const ly = parseInt(lastMonth.substring(0, 4));
            let ny = ly, nm = lm + 1;
            if (nm > 12) { nm = 1; ny++; }
            const toTs = new Date(`${ny}-${String(nm).padStart(2, '0')}-01`).getTime();
            let midTs = null;
            if (idx < years.length - 1) {
                const nextYearMonths = months.filter(m => m.startsWith(String(years[idx + 1])));
                const nextYearFirstMonth = nextYearMonths[0];
                const nextTs = new Date(nextYearFirstMonth + '-01').getTime();
                midTs = (lastMonthTs + nextTs) / 2;
            }
            const bandFrom = idx === 0 ? fromTs : midPoints[idx - 1];
            const bandTo   = (idx === years.length - 1) ? toTs : midTs;
            if (midTs !== null) midPoints.push(midTs);
            plotBands.push({
                from: bandFrom,
                to: bandTo,
                color: [
                    'rgba(220, 232, 255, 0.50)',
                    'rgba(255, 243, 205, 0.50)',
                    'rgba(212, 237, 218, 0.50)',
                    'rgba(248, 215, 218, 0.50)',
                ][idx % 4],
                label: {
                    text: `<b>${year}</b>`,
                    align: 'center',
                    verticalAlign: 'bottom',
                    y: 40,
                    style: { fontSize: '16px', color: '#333', fontWeight: '700' }
                }
            });
            if (midTs !== null) {
                plotLines.push({
                    value: midTs,
                    color: '#666',
                    width: 1.5,
                    dashStyle: 'Solid',
                    zIndex: 4
                });
            }
        });
        return { plotBands, plotLines };
    }

    function buildWtoChartOptions(data, field, containerId, color) {
        const seriesData = data.months.map((m, i) => [
            new Date(m + '-01').getTime(),
            data[field][i],
        ]);
        const { plotBands, plotLines } = buildYearBoundaries(data.months);

        return {
            chart: {
                type: 'line',
                marginTop: 40,
                height: 230,
                marginBottom: 60,
                renderTo: containerId,
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                type: 'datetime',
                tickInterval: 30 * 24 * 3600 * 1000,
                labels: {
                    formatter: function () {
                        return new Date(this.value).toLocaleDateString('id-ID', { month: 'short' });
                    },
                    style: { fontSize: '16px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jumlah Karyawan', style: { fontSize: '16px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '16px' },
                },
                min: 0,
            },
            legend: { enabled: false },
            credits: { enabled: false },
            tooltip: {
                headerFormat: '<b>{point.x:%B %Y}</b><br/>',
                pointFormatter: function () {
                    return 'Karyawan Lembur: <b>' + Math.round(this.y) + '</b>';
                },
            },
            plotOptions: {
                line: {
                    color: color,
                    lineWidth: 3,
                    marker: {
                        enabled: true,
                        radius: 5,
                        fillColor: color,
                        lineColor: '#fff',
                        lineWidth: 2,
                    },
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '16px', color: '#000', textOutline: '1px contrast' },
                        formatter: function () { return Math.round(this.y); },
                        y: -8,
                    },
                },
            },
            series: [{
                name: field === 'hari_kerja' ? 'Karyawan Lembur (Hari Kerja)' : 'Karyawan Lembur (Hari Libur)',
                data: seriesData,
            }],
        };
    }

    function renderWtoCharts(data) {
        if (!data || !data.months || data.months.length === 0) {
            $('#wtoChartKaryawan').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            $('#wtoChartJamLembur').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            $('#wtoChartJamLemburPerDept').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            return;
        }

        // Chart 1: Gabungan Hari Kerja + Hari Libur (2 line dalam 1 chart)
        const seriesKerja = data.months.map((m, i) => [
            new Date(m + '-01').getTime(),
            Number(data.hari_kerja[i]) || 0,
        ]);
        const seriesLibur = data.months.map((m, i) => [
            new Date(m + '-01').getTime(),
            Number(data.hari_libur[i]) || 0,
        ]);
        const { plotBands, plotLines } = buildYearBoundaries(data.months);

        const optionsKaryawan = {
            chart: {
                type: 'line',
                height: 230,
                marginTop: 45,
                marginBottom: 80,
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                type: 'datetime',
                tickInterval: 30 * 24 * 3600 * 1000,
                labels: {
                    formatter: function () {
                        return new Date(this.value).toLocaleDateString('id-ID', { month: 'short' });
                    },
                    style: { fontSize: '16px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jumlah Karyawan', style: { fontSize: '16px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '16px' },
                },
                min: 0,
            },
            legend: {
                enabled: true,
                position: 'top',
                align: 'right',
                itemStyle: { fontSize: '16px', fontWeight: 600 },
            },
            credits: { enabled: false },
            tooltip: {
                headerFormat: '<b>{point.x:%B %Y}</b><br/>',
                pointFormatter: function () {
                    return '<span style="color:' + this.series.color + '">\u25CF</span> '
                        + this.series.name + ': <b>' + Math.round(this.y) + '</b>';
                },
            },
            plotOptions: {
                line: {
                    lineWidth: 3,
                    marker: {
                        enabled: true,
                        radius: 4,
                        lineColor: '#fff',
                        lineWidth: 2,
                    },
                },
            },
            series: [
                {
                    name: 'Karyawan Lembur (Hari Kerja)',
                    data: seriesKerja,
                    color: '#1e88e5',
                    marker: { fillColor: '#1e88e5' },
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'allow',
                        useHTML: true,
                        style: { fontSize: '16px', color: '#1e88e5', fontWeight: '700' },
                        formatter: function () {
                            const chart = this.series.chart;
                            const idx = this.point.index;
                            const sLibur = chart.series[1];
                            const vKerja = Number(this.y) || 0;
                            const vLibur = (sLibur && sLibur.yData) ? Number(sLibur.yData[idx]) || 0 : 0;
                            const yAxis = chart.yAxis[0];
                            const distance = Math.abs(yAxis.toPixels(vLibur) - yAxis.toPixels(vKerja));
                            if (distance < 20) return null;
                            return vKerja ? String(Math.round(vKerja)) : null;
                        },
                        y: -8,
                    },
                },
                {
                    name: 'Karyawan Lembur (Hari Libur)',
                    data: seriesLibur,
                    color: '#e65100',
                    marker: { fillColor: '#e65100' },
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'allow',
                        useHTML: true,
                        style: { fontSize: '16px', color: '#e65100', fontWeight: '700' },
                        formatter: function () {
                            const chart = this.series.chart;
                            const idx = this.point.index;
                            const sKerja = chart.series[0];
                            const vKerja = (sKerja && sKerja.yData) ? Number(sKerja.yData[idx]) || 0 : 0;
                            const vLibur = Number(this.y) || 0;
                            const yAxis = chart.yAxis[0];
                            const distance = Math.abs(yAxis.toPixels(vLibur) - yAxis.toPixels(vKerja));
                            const fmt = (n) => n ? String(Math.round(n)) : '-';
                            if (distance < 20) {
                                return '<div style="text-align:center;line-height:1.3;">'
                                    + '<div style="color:#1e88e5;font-weight:700;">' + fmt(vKerja) + '</div>'
                                    + '<div style="color:#e65100;font-weight:700;">' + fmt(vLibur) + '</div>'
                                    + '</div>';
                            }
                            return fmt(vLibur);
                        },
                        y: -8,
                    },
                },
            ],
        };
        if (wtoLineChartKaryawan) wtoLineChartKaryawan.destroy();
        wtoLineChartKaryawan = Highcharts.chart('wtoChartKaryawan', optionsKaryawan);

        // Chart 2: Grafik Jam Lembur (multi-line: 2 series)
        renderWtoChartJamLembur(data);

        // Chart 3: Grafik Jam Lembur per Departemen (grouped column)
        renderWtoChartJamLemburPerDept(data);
    }

    function renderWtoChartJamLembur(data) {
        const { plotBands, plotLines } = buildYearBoundaries(data.months);

        const allJamKerja = (data.jam_kerja || []).map((v, i) => [
            new Date((data.months[i] || '') + '-01').getTime(),
            Number(v) || 0,
        ]);
        const allJamLibur = (data.jam_libur || []).map((v, i) => [
            new Date((data.months[i] || '') + '-01').getTime(),
            Number(v) || 0,
        ]);

        const options = {
            chart: {
                type: 'line',
                height: 230,
                marginBottom: 80,
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                type: 'datetime',
                tickInterval: 30 * 24 * 3600 * 1000,
                labels: {
                    formatter: function () {
                        return new Date(this.value).toLocaleDateString('id-ID', { month: 'short' });
                    },
                    style: { fontSize: '16px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jam Lembur', style: { fontSize: '16px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '16px' },
                },
                min: 0,
            },
            legend: {
                enabled: true,
                verticalAlign: 'bottom',
                align: 'center',
                layout: 'horizontal',
                y: 10,
                itemStyle: { fontSize: '16px', fontWeight: 600 },
            },
            credits: { enabled: false },
            tooltip: {
                headerFormat: '<b>{point.x:%B %Y}</b><br/>',
                pointFormatter: function () {
                    return '<span style="color:' + this.series.color + '">\u25CF</span> '
                        + this.series.name + ': <b>' + Number(this.y).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + '</b>';
                },
            },
            plotOptions: {
                line: {
                    lineWidth: 3,
                    marker: {
                        enabled: true,
                        radius: 5,
                        lineColor: '#fff',
                        lineWidth: 2,
                    },
                },
            },
            series: [
                {
                    name: 'Total Jam Kerja (SPKL)',
                    data: allJamKerja,
                    color: '#1565c0',
                    marker: { fillColor: '#1565c0' },
                    dataLabels: {
                        enabled: true,
                        allowOverlap: true,
                        style: { fontSize: '16px', color: '#1565c0', fontWeight: '700', textOutline: '1px contrast' },
                        formatter: function () { return Math.round(this.y); },
                        y: -10,
                    },
                },
                {
                    name: 'Total Jam Libur (HOVT)',
                    data: allJamLibur,
                    color: '#bf360c',
                    marker: { fillColor: '#bf360c' },
                    dataLabels: {
                        enabled: true,
                        allowOverlap: true,
                        style: { fontSize: '16px', color: '#bf360c', fontWeight: '700', textOutline: '1px contrast' },
                        formatter: function () { return Math.round(this.y); },
                        y: -10,
                    },
                },
            ],
        };

        if (wtoLineChartJamLembur) wtoLineChartJamLembur.destroy();
        wtoLineChartJamLembur = Highcharts.chart('wtoChartJamLembur', options);

        renderWtoJamLemburDetailTable(data);
    }

    function renderWtoChartJamLemburPerDept(data) {
        if (!data.departments || data.departments.length === 0) {
            $('#wtoChartJamLemburPerDept').html('<p class="text-center text-muted p-4">Tidak ada data departemen untuk range ini.</p>');
            $('#wtoChartJamLemburPerDeptLegend').empty();
            if (wtoLineChartJamLemburPerDept) wtoLineChartJamLemburPerDept.destroy();
            wtoLineChartJamLemburPerDept = null;
            return;
        }

        const { plotBands, plotLines } = buildYearBoundaries(data.months);

        const deptColors = [
            '#1e88e5', '#e65100', '#43a047', '#8e24aa', '#e53935',
            '#00acc1', '#6d4c41', '#3949ab', '#f4511e', '#00897b',
        ];

        const allMonths = data.months || [];
        const depts = data.departments || [];

        // Default hanya 1 tahun terakhir (12 bulan)
        const MONTHS_LIMIT = 12;
        const months = allMonths.length > MONTHS_LIMIT ? allMonths.slice(-MONTHS_LIMIT) : allMonths;
        const offset = allMonths.length > MONTHS_LIMIT ? allMonths.length - MONTHS_LIMIT : 0;

        const series = [];

        depts.forEach((dept, idx) => {
            const color = deptColors[idx % deptColors.length];
            const deptData = data.department_series[dept] || { jam_kerja: [], jam_libur: [] };

            const datapoints = months.map((m, i) => [
                new Date(m + '-01').getTime(),
                (Number(deptData.jam_kerja[i + offset]) || 0) + (Number(deptData.jam_libur[i + offset]) || 0),
            ]);

            series.push({
                name: dept,
                data: datapoints,
                color: color,
            });
        });

        // Render custom HTML legend (di luar chart, di luar scroll container,
        // sehingga tetap terlihat walau user scroll horizontal).
        const $legend = $('#wtoChartJamLemburPerDeptLegend');
        $legend.empty();
        depts.forEach((dept, idx) => {
            const color = deptColors[idx % deptColors.length];
            const safeName = $('<div>').text(dept).html();
            $legend.append(
                '<span style="display:inline-flex; align-items:center; gap:.4rem; white-space:nowrap;">'
                + '<span style="display:inline-block; width:14px; height:14px; border-radius:3px; background:' + color + ';"></span>'
                + '<span style="color:#333;">' + safeName + '</span>'
                + '</span>'
            );
        });

        const chartWidth = Math.max(600, depts.length * months.length * 30 + 200);

            const options = {
            chart: {
                type: 'column',
                height: 210,
                width: chartWidth,
                marginTop: 20,
                marginBottom: 40,
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                type: 'datetime',
                tickInterval: 30 * 24 * 3600 * 1000,
                labels: {
                    formatter: function () {
                        return new Date(this.value).toLocaleDateString('id-ID', { month: 'short' });
                    },
                    style: { fontSize: '16px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jam Lembur', style: { fontSize: '16px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '16px' },
                },
                min: 0,
            },
            legend: {
                enabled: false,
            },
            credits: { enabled: false },
            tooltip: {
                headerFormat: '<b>{point.x:%B %Y}</b><br/>',
                pointFormatter: function () {
                    return '<span style="color:' + this.series.color + '">\u25CF</span> '
                        + this.series.name + ': <b>' + Number(this.y).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + '</b>';
                },
            },
            plotOptions: {
                column: {
                    grouping: true,
                    pointPadding: 0.0,
                    groupPadding: 0.1,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        allowOverlap: true,
                        rotation: -90,
                        align: 'center',
                        verticalAlign: 'bottom',
                        inside: false,
                        crop: false,
                        overflow: 'allow',
                        style: { fontSize: '16px', fontWeight: 700 },
                        formatter: function () { return Math.round(this.y); },
                        y: -4,
                    },
                },
            },
            series: series,
        };

        if (wtoLineChartJamLemburPerDept) wtoLineChartJamLemburPerDept.destroy();
        wtoLineChartJamLemburPerDept = Highcharts.chart('wtoChartJamLemburPerDept', options);
        document.getElementById('wtoChartJamLemburPerDept').style.minWidth = chartWidth + 'px';
    }

    function renderWtoJamLemburDetailTable(data) {
        if (!data.departments || !data.months || data.departments.length === 0) {
            $('#wtoJamLemburDetailThead').html('');
            $('#wtoJamLemburDetailTbody').html('<tr><td class="text-center text-muted">Tidak ada data untuk range ini.</td></tr>');
            return;
        }
        const months = data.months;
        const depts = data.departments;
        const series = data.department_series || {};

        let thead = '<tr><th rowspan="2" class="sticky-col-l" style="min-width:120px;">Departemen</th>';
        months.forEach(m => {
            const label = new Date(m + '-01').toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
            thead += '<th colspan="3" class="text-center">' + label + '</th>';
        });
        thead += '<th rowspan="2" class="text-center sticky-col-r" style="min-width:70px;">Total</th>';
        thead += '</tr><tr>';
        months.forEach(() => {
            thead += '<th class="text-right" style="min-width:55px;">SPKL</th><th class="text-right" style="min-width:55px;">HOVT</th><th class="text-right" style="min-width:55px;">Total</th>';
        });
        thead += '</tr>';

        let tbody = '';
        depts.forEach(dept => {
            const d = series[dept] || {};
            let totalDept = 0;
            tbody += '<tr><td class="sticky-col-l">' + escapeHtml(dept) + '</td>';
            months.forEach((m, mi) => {
                const spkl = Number(d.jam_kerja?.[mi]) || 0;
                const hovt = Number(d.jam_libur?.[mi]) || 0;
                const total = spkl + hovt;
                totalDept += total;
                const fmt = (v) => v.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
                tbody += '<td class="text-right">' + fmt(spkl) + '</td>';
                tbody += '<td class="text-right">' + fmt(hovt) + '</td>';
                tbody += '<td class="text-right"><strong>' + fmt(total) + '</strong></td>';
            });
            tbody += '<td class="text-right sticky-col-r"><strong>' + totalDept.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '</strong></td>';
            tbody += '</tr>';
        });

        $('#wtoJamLemburDetailThead').html(thead);
        $('#wtoJamLemburDetailTbody').html(tbody);
    }

    $('#wtoPerPage').on('change', function () {
        wtoCurrentPage = 1;
        loadWtoData();
    });

    $(document).on('change', 'input[name="wto_nama[]"]', function () {
        currentWtoNama = getMultiSelectValues('wto_nama');
    });

    $('#btnWtoApply').on('click', function () {
        wtoCurrentPage = 1;
        currentWtoNama = getMultiSelectValues('wto_nama');
        loadWtoData();
        loadWtoNames();
        loadWtoPwsGroups();
    });

    $('#btnWtoReset').on('click', function () {
        $('#wtoTglInRange').val('');
        $('#wtoPeriode').val('1-akhir');
        ['wto_departmen', 'wto_sub_departmen', 'wto_tipe_karyawan', 'wto_pws', 'wto_nama'].forEach(function (target) {
            const $wrapper = $('.hd-multi-select[data-target="' + target + '"]');
            $wrapper.find('input[type="checkbox"]').prop('checked', false);
            if ($wrapper.length && typeof updateMsLabel === 'function') {
                updateMsLabel($wrapper);
            }
        });
        @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            // Mode terkunci: re-apply auto-select untuk wto_tipe_karyawan
            $('input[name="wto_tipe_karyawan[]"]').prop('checked', true);
            const $wtoTipeWrap = $('.hd-multi-select[data-target="wto_tipe_karyawan"]');
            if ($wtoTipeWrap.length && typeof updateMsLabel === 'function') {
                updateMsLabel($wtoTipeWrap);
            }
        @endif
        currentWtoNama = [];
        wtoCurrentPage = 1;
        loadWtoData();
        loadWtoNames();
        loadWtoPwsGroups();
    });

    $('#btnWtoExport').on('click', function () {
        const params = $.param(getWtoFilterParams(1));
        window.open("{{ url('/hr/hrdashboard/wto-export') }}?" + params, '_blank');
    });

    $('#wtoPeriode').on('change', function () {
        loadWtoChart();
    });

    // Init flatpickr for Tgl In range
    flatpickr('#wtoTglInRange', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });

    // ===== TAB SWITCHING (no fullscreen management) =====
    function switchDashboardTab(tabId) {
        if (!WTO_TABS.includes(tabId)) return;
        currentTab = tabId;

        WTO_TABS.forEach(id => {
            const $el = $('#' + id);
            if (id === tabId) {
                $el.show();
            } else {
                $el.hide();
            }
        });

        // Update tab buttons
        $('#hdDashboardTabs .hd-tab-btn').each(function () {
            const target = $(this).data('target');
            if (target === tabId) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });

        // Show/hide WT&O filter + data table based on active tab
        if (tabId === 'hdWtoSection') {
            $('#wtoFilterCard').show();
            $('#wtoExtras').show();
            $('#izinFilterCard').hide();
            $('#izinExtras').hide();
            $('#dataCard').hide();
            $('#filterDataCard').hide();
            loadWtoData(1);
            loadWtoNames();
            loadWtoPwsGroups();
            // Restart chart scroll jika auto-cycle aktif
            if (cycleEnabled) {
                setTimeout(startChartScroll, 1000);
            }
        } else if (tabId === 'hdIzinSection') {
            $('#wtoFilterCard').hide();
            $('#wtoExtras').hide();
            $('#izinFilterCard').show();
            $('#izinExtras').show();
            $('#dataCard').hide();
            $('#filterDataCard').hide();
            if (typeof window.loadIzinData === 'function') {
                window.loadIzinData(1);
                window.loadIzinNames();
            }
        } else {
            $('#wtoFilterCard').hide();
            $('#wtoExtras').hide();
            $('#izinFilterCard').hide();
            $('#izinExtras').hide();
            $('#dataCard').show();
            $('#filterDataCard').show();
        }
    }

    // Wire tab buttons
    $(document).off('click.wtoTab').on('click.wtoTab', '#hdDashboardTabs .hd-tab-btn', function () {
        const target = $(this).data('target');
        if (target) {
            switchDashboardTab(target);
        }
    });

    // ===== AUTO-CYCLE TOGGLE (works in normal view AND fullscreen) =====
    let chartScrollTimer = null;

    function performCycle() {
        // Triple-guard: generation counter + global flag + local flag + timer
        if (!window.__wtoCycleActive || !cycleEnabled || !cycleTimer) return;
        const curIdx = WTO_TABS.indexOf(currentTab);
        const nextIdx = (curIdx + 1) % WTO_TABS.length;
        switchDashboardTab(WTO_TABS[nextIdx]);
    }

    function updateCycleButton() {
        const $btn = $('#btnToggleAutoCycle');
        const isOn = window.__wtoCycleActive && cycleEnabled && cycleTimer !== null;
        $('#hdTabAutoStatusText').text(isOn ? 'ON' : 'OFF');
        if (isOn) {
            $btn.removeClass('btn-outline-secondary').addClass('btn-success');
        } else {
            $btn.removeClass('btn-success').addClass('btn-outline-secondary');
        }
    }

    function startChartScroll() {
        stopChartScroll();
        const $container = $('#wtoChartJamLemburPerDept').parent();
        if ($container.length === 0) return;
        const el = $container[0];
        const maxScroll = el.scrollWidth - el.clientWidth;
        if (maxScroll <= 0) return;

        let pos = 0;
        let dir = 1;
        chartScrollTimer = setInterval(function () {
            if (!window.__wtoCycleActive) {
                stopChartScroll();
                return;
            }
            pos += dir * 7;
            if (pos >= maxScroll) {
                pos = maxScroll;
                dir = -1;
            } else if (pos <= 0) {
                pos = 0;
                dir = 1;
            }
            el.scrollLeft = pos;
        }, 20);
    }

    function stopChartScroll() {
        if (chartScrollTimer) {
            clearInterval(chartScrollTimer);
            chartScrollTimer = null;
        }
        const $c = $('#wtoChartJamLemburPerDept').parent();
        if ($c.length) $c[0].scrollLeft = 0;
    }

    function startCycle() {
        stopCycle(); // Always clear any existing interval first
        window.__wtoCycleActive = true;
        window.__wtoCycleGen++;
        cycleEnabled = true;
        cycleTimer = setInterval(performCycle, WTO_CYCLE_MS);
        startChartScroll();
        updateCycleButton();
    }

    function stopCycle() {
        window.__wtoCycleActive = false;
        window.__wtoCycleGen++;
        cycleEnabled = false;
        if (cycleTimer) {
            clearInterval(cycleTimer);
        }
        cycleTimer = null;
        stopChartScroll();
        updateCycleButton();
    }

    // Toggle button click — user explicitly enables/disables auto-cycle
    // Use .off().on() to prevent duplicate bindings if IIFE runs multiple times
    $(document).off('click.wtoAutoCycle').on('click.wtoAutoCycle', '#btnToggleAutoCycle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        if (cycleEnabled) {
            stopCycle();
        } else {
            startCycle();
        }
    });

    // Listen for fullscreen changes — does NOT touch auto-cycle state
    document.addEventListener('fullscreenchange', function () {
        // No-op
    });

    // When fullscreen button is clicked, ensure the section it's in is active
    $(document).off('click.wtoFs').on('click.wtoFs', '.hd-fs-btn', function () {
        const $section = $(this).closest('.hd-tab-content');
        if ($section.length) {
            const sectionId = $section.attr('id');
            if (sectionId && sectionId !== currentTab) {
                switchDashboardTab(sectionId);
            }
        }
        // The existing handler in index.blade.php will handle the actual
        // requestFullscreen on the data-target element (hdFullscreenWrap).
    });

    // Init
    $(function () {
        switchDashboardTab('hdStatsSection');
    });
})();
</script>
