{{-- WT&O Scripts: tab switching (no fullscreen exit), filter + table --}}
<script>
(function () {
    'use strict';

    window.loadWtoData        = loadWtoData;
    window.switchDashboardTab = switchDashboardTab;

    const WTO_TABS    = ['hdStatsSection', 'hdWtoSection'];
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
        return { from: '', to: parts[0].trim() };
    }

    function getWtoFilterParams(page) {
        const p = {
            page: page || wtoCurrentPage,
            per_page: $('#wtoPerPage').val() || 25,
            departmen: getMultiSelectValues('wto_departmen'),
            sub_departmen: getMultiSelectValues('wto_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('wto_tipe_karyawan'),
            wto_nama: currentWtoNama.length > 0
                ? currentWtoNama.slice()
                : getMultiSelectValues('wto_nama'),
        };
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;
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
        };
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;
        return p;
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
                rows = '<tr><td colspan="9" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>';
            } else {
                res.data.forEach(r => {
                    rows += `
                        <tr>
                            <td>${escapeHtml(r.nik)}</td>
                            <td>${escapeHtml(r.nama || '')}</td>
                            <td>${escapeHtml(r.dept || '')}</td>
                            <td>${escapeHtml(r.sub_departmen || '')}</td>
                            <td>${escapeHtml(r.section || '')}</td>
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
    let wtoLineChartHariKerja = null;
    let wtoLineChartHariLibur = null;
    let wtoLineChartJamLembur = null;

    function loadWtoChart() {
        const filterParams = getWtoFilterParams(1);
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
                    style: { fontSize: '12px', color: '#333', fontWeight: '700' }
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
                height: 210,
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
                    style: { fontSize: '11px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jumlah Karyawan', style: { fontSize: '11px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '11px' },
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
                        style: { fontSize: '10px', color: '#000', textOutline: '1px contrast' },
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
            $('#wtoChartKaryawanLibur').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            $('#wtoChartJamLembur').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            return;
        }

        // Chart 1: Hari Kerja (biru)
        const optKerja = buildWtoChartOptions(data, 'hari_kerja', 'wtoChartKaryawan', '#1e88e5');
        if (wtoLineChartHariKerja) wtoLineChartHariKerja.destroy();
        wtoLineChartHariKerja = Highcharts.chart('wtoChartKaryawan', optKerja);

        // Chart 2: Hari Libur (oranye/merah)
        const optLibur = buildWtoChartOptions(data, 'hari_libur', 'wtoChartKaryawanLibur', '#e65100');
        if (wtoLineChartHariLibur) wtoLineChartHariLibur.destroy();
        wtoLineChartHariLibur = Highcharts.chart('wtoChartKaryawanLibur', optLibur);

        // Chart 3: Grafik Jam Lembur (multi-line: 2 series)
        renderWtoChartJamLembur(data);
    }

    function renderWtoChartJamLembur(data) {
        const seriesKerja = data.months.map((m, i) => [
            new Date(m + '-01').getTime(),
            Number(data.jam_kerja[i]) || 0,
        ]);
        const seriesLibur = data.months.map((m, i) => [
            new Date(m + '-01').getTime(),
            Number(data.jam_libur[i]) || 0,
        ]);
        const { plotBands, plotLines } = buildYearBoundaries(data.months);

        const options = {
            chart: {
                type: 'line',
                height: 210,
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
                    style: { fontSize: '11px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: 'Jam Lembur', style: { fontSize: '11px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '11px' },
                },
                min: 0,
            },
            legend: {
                enabled: true,
                position: 'top',
                align: 'right',
                itemStyle: { fontSize: '11px', fontWeight: 600 },
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
                        radius: 4,
                        lineColor: '#fff',
                        lineWidth: 2,
                    },
                },
            },
            series: [
                {
                    name: 'Sum of Jam Lembur (Hari Kerja)',
                    data: seriesKerja,
                    color: '#1e88e5',
                    marker: { fillColor: '#1e88e5' },
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'allow',
                        useHTML: true,
                        style: { fontSize: '9px', color: '#1e88e5', fontWeight: '700' },
                        formatter: function () {
                            const chart = this.series.chart;
                            const idx = this.point.index;
                            const sLibur = chart.series[1];
                            const vKerja = Number(this.y) || 0;
                            const vLibur = (sLibur && sLibur.yData) ? Number(sLibur.yData[idx]) || 0 : 0;
                            const yAxis = chart.yAxis[0];
                            const distance = Math.abs(yAxis.toPixels(vLibur) - yAxis.toPixels(vKerja));
                            if (distance < 20) return null;
                            return vKerja ? String(Math.round(vKerja * 10) / 10) : null;
                        },
                        y: -8,
                    },
                },
                {
                    name: 'Sum of Jam Lembur (Hari Libur)',
                    data: seriesLibur,
                    color: '#e65100',
                    marker: { fillColor: '#e65100' },
                    dataLabels: {
                        enabled: true,
                        crop: false,
                        overflow: 'allow',
                        useHTML: true,
                        style: { fontSize: '9px', color: '#e65100', fontWeight: '700' },
                        formatter: function () {
                            const chart = this.series.chart;
                            const idx = this.point.index;
                            const sKerja = chart.series[0];
                            const vKerja = (sKerja && sKerja.yData) ? Number(sKerja.yData[idx]) || 0 : 0;
                            const vLibur = Number(this.y) || 0;
                            const yAxis = chart.yAxis[0];
                            const distance = Math.abs(yAxis.toPixels(vLibur) - yAxis.toPixels(vKerja));
                            const fmt = (n) => n ? String(Math.round(n * 10) / 10) : '-';
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
        if (wtoLineChartJamLembur) wtoLineChartJamLembur.destroy();
        wtoLineChartJamLembur = Highcharts.chart('wtoChartJamLembur', options);
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
    });

    $('#btnWtoReset').on('click', function () {
        $('#wtoTglInRange').val('');
        ['wto_departmen', 'wto_sub_departmen', 'wto_tipe_karyawan', 'wto_nama'].forEach(function (target) {
            const $wrapper = $('.hd-multi-select[data-target="' + target + '"]');
            $wrapper.find('input[type="checkbox"]').prop('checked', false);
            if ($wrapper.length && typeof updateMsLabel === 'function') {
                updateMsLabel($wrapper);
            }
        });
        currentWtoNama = [];
        wtoCurrentPage = 1;
        loadWtoData();
        loadWtoNames();
    });

    $('#btnWtoExport').on('click', function () {
        const params = $.param(getWtoFilterParams(1));
        window.open("{{ url('/hr/hrdashboard/wto-export') }}?" + params, '_blank');
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
            $('#dataCard').hide();
            $('#filterDataCard').hide();
            loadWtoData(1);
            loadWtoNames();
        } else {
            $('#wtoFilterCard').hide();
            $('#wtoExtras').hide();
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
    function performCycle() {
        // Triple-guard: generation counter + global flag + local flag + timer
        if (!window.__wtoCycleActive || !cycleEnabled || !cycleTimer) return;
        const nextTab = currentTab === WTO_TABS[0] ? WTO_TABS[1] : WTO_TABS[0];
        switchDashboardTab(nextTab);
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

    function startCycle() {
        stopCycle(); // Always clear any existing interval first
        window.__wtoCycleActive = true;
        window.__wtoCycleGen++;
        cycleEnabled = true;
        cycleTimer = setInterval(performCycle, WTO_CYCLE_MS);
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
