{{-- Izin Scripts: filter, table, names, chart, PWS dynamic, tab integration --}}
<script>
(function () {
    'use strict';

    let izinCurrentPage = 1;
    let currentIzinNama = [];

    const KODE_IJIN_MAP = {
        'Cuti':     ['CB', 'CDC1', 'CDC2', 'CDC3', 'CIM', 'CK', 'CKT', 'CH', 'CM', 'CNA', 'CHJ', 'C2', 'C', 'CUT'],
        'Sakit':    ['CHD', 'IM', 'KD', 'S'],
        'Sakit KK': ['SKK'],
        'Mangkir':  ['A'],
    };
    function getKategoriIjin(kode) {
        if (!kode) return '';
        const upper = String(kode).toUpperCase().trim();
        for (const [kategori, kodes] of Object.entries(KODE_IJIN_MAP)) {
            if (kodes.includes(upper)) return kategori;
        }
        return upper;
    }

    function fmtDate(s) {
        if (!s) return '';
        return String(s).substring(0, 10);
    }

    function parseIzinTglRange() {
        const raw = ($('#izinTglRange').val() || '').trim();
        if (!raw) return { from: '', to: '' };
        const parts = raw.split(/\s+to\s+|\s+-\s+/);
        if (parts.length === 2) return { from: parts[0].trim(), to: parts[1].trim() };
        const single = parts[0].trim();
        return { from: single, to: single };
    }

    function getIzinFilterParams(page) {
        const p = {
            page: page || izinCurrentPage,
            per_page: $('#izinPerPage').val() || 25,
            departmen: getMultiSelectValues('izin_departmen'),
            sub_departmen: getMultiSelectValues('izin_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('izin_tipe_karyawan'),
            pws: getMultiSelectValues('izin_pws'),
            izin_ijin: getMultiSelectValues('izin_ijin'),
            izin_nama: currentIzinNama.length > 0
                ? currentIzinNama.slice()
                : getMultiSelectValues('izin_nama'),
            @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            type_karyawan: @json($typeKaryawanMode),
            @endif
        };
        if (Array.isArray(p.departmen) && p.departmen.length === 0) delete p.departmen;
        if (Array.isArray(p.sub_departmen) && p.sub_departmen.length === 0) delete p.sub_departmen;
        if (Array.isArray(p.tipe_karyawan) && p.tipe_karyawan.length === 0) delete p.tipe_karyawan;
        if (Array.isArray(p.pws) && p.pws.length === 0) delete p.pws;
        if (Array.isArray(p.izin_ijin) && p.izin_ijin.length === 0) delete p.izin_ijin;
        if (Array.isArray(p.izin_nama) && p.izin_nama.length === 0) delete p.izin_nama;

        const tgl = parseIzinTglRange();
        if (tgl.from) p.izin_tgl_from = tgl.from;
        if (tgl.to)   p.izin_tgl_to   = tgl.to;

        return p;
    }

    function getIzinNamesFilterParams() {
        const p = {
            departmen: getMultiSelectValues('izin_departmen'),
            sub_departmen: getMultiSelectValues('izin_sub_departmen'),
            tipe_karyawan: getMultiSelectValues('izin_tipe_karyawan'),
            pws: getMultiSelectValues('izin_pws'),
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

    function loadIzinData(page) {
        page = page || izinCurrentPage;
        izinCurrentPage = page;

        const params = $.param(getIzinFilterParams(page));

        $.get("{{ url('/hr/hrdashboard/izin-data') }}?" + params, function (res) {
            if (res.stats) {
                $('#izinStatTotalHariIzin').text((res.stats.total_hari_izin || 0).toLocaleString());
                $('#izinStatTotalHariCuti').text((res.stats.total_hari_cuti || 0).toLocaleString());
                $('#izinStatTotalHariSakit').text((res.stats.total_hari_sakit || 0).toLocaleString());
                $('#izinStatTotalHariMangkir').text((res.stats.total_hari_mangkir || 0).toLocaleString());
            }

            let rows = '';
            if (!res.data || res.data.length === 0) {
                rows = '<tr><td colspan="11" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>';
            } else {
                res.data.forEach(r => {
                    rows += `
                        <tr>
                            <td>${escapeHtml(r.nik)}</td>
                            <td>${escapeHtml(r.nama || '')}</td>
                            <td>${escapeHtml(r.dept || '')}</td>
                            <td>${escapeHtml(r.sub_departmen || '')}</td>
                            <td>${escapeHtml(r.pws || '')}</td>
                            <td>${escapeHtml(r.section || '')}</td>
                            <td>${fmtDate(r.tgl)}</td>
                            <td>${escapeHtml(r.kode_ijin || '')}</td>
                            <td>${escapeHtml(getKategoriIjin(r.kode_ijin))}</td>
                            <td>${escapeHtml(r.keterangan || '')}</td>
                            <td>${escapeHtml(r.no_spi || '')}</td>
                        </tr>
                    `;
                });
            }
            $('#izinTbody').html(rows);
            renderIzinPagination(res.meta || {});
            loadIzinChart();
            loadIzinTopSakit();
            loadIzinTopMangkir();
            loadIzinSakitRatioDept();
        }).fail(function (xhr) {
            if (xhr.status === 403) {
                Swal.fire('Akses Ditolak', 'Anda tidak memiliki akses ke dashboard ini.', 'error');
            } else {
                Swal.fire('Error', 'Gagal memuat data Izin.', 'error');
            }
        });
    }

    function renderIzinPagination(meta) {
        const total    = meta.total || 0;
        const page     = meta.page || 1;
        const lastPage = meta.last_page || 1;
        const perPage  = meta.per_page || 25;

        if (total === 0) {
            $('#izinInfo').text('Menampilkan 0 dari 0 data');
            $('#izinPagination').html('');
            return;
        }

        const from = (page - 1) * perPage + 1;
        const to   = Math.min(page * perPage, total);
        $('#izinInfo').text(`Menampilkan ${from}\u2013${to} dari ${total} data`);

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
            const active = i === page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" data-page="${i}">${i}</a></li>`;
        }
        if (end < lastPage) {
            if (end < lastPage - 1) html += `<li class="page-item disabled"><span class="page-link">\u2026</span></li>`;
            html += `<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`;
        }
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${page + 1}">&rsaquo;</a></li>`;
        html += `<li class="page-item ${page === lastPage ? 'disabled' : ''}"><a class="page-link" data-page="${lastPage}">&raquo;</a></li>`;
        html += '</ul></nav>';
        $('#izinPagination').html(html);
    }

    $(document).on('click', '#izinPagination .page-link', function () {
        const li = $(this).closest('li');
        if (li.hasClass('disabled') || li.hasClass('active')) return;
        const page = parseInt($(this).data('page'));
        if (!isNaN(page) && page > 0) loadIzinData(page);
    });

    $('#izinPerPage').on('change', function () {
        izinCurrentPage = 1;
        loadIzinData();
    });

    $(document).on('change', 'input[name="izin_nama[]"]', function () {
        currentIzinNama = getMultiSelectValues('izin_nama');
    });

    function loadIzinNames() {
        const $list = $('#izinNamaList');
        if ($list.length === 0) return;

        const savedSelection = getMultiSelectValues('izin_nama');
        const params = $.param(getIzinNamesFilterParams());
        $list.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Memuat...</div>');

        $.get("{{ url('/hr/hrdashboard/izin-names') }}?" + params, function (res) {
            renderIzinNamaList(res.names || [], savedSelection);
        }).fail(function (xhr) {
            if (xhr.status === 403) {
                $list.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Tidak ada akses</div>');
            } else {
                $list.html('<div class="text-danger text-center p-2" style="font-size:.78rem;">Gagal memuat</div>');
            }
        });
    }

    let $izinNamaList;
    function renderIzinNamaList(names, savedSelection) {
        if (!$izinNamaList || $izinNamaList.length === 0) {
            $izinNamaList = $('#izinNamaList');
        }
        let selected = savedSelection;
        if (!Array.isArray(selected)) {
            selected = getMultiSelectValues('izin_nama');
        }

        $izinNamaList.empty();
        if (!names || names.length === 0) {
            $izinNamaList.html('<div class="text-muted text-center p-2" style="font-size:.78rem;">Tidak ada nama</div>');
            return;
        }

        names.forEach(name => {
            const safe = String(name).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            const isChecked = selected.indexOf(name) !== -1 ? 'checked' : '';
            $izinNamaList.append(
                '<label class="hd-ms-item">' +
                    '<input type="checkbox" name="izin_nama[]" value="' + safe + '" ' + isChecked + '>' +
                    '<span>' + safe + '</span>' +
                '</label>'
            );
        });

        const $wrapper = $('.hd-multi-select[data-target="izin_nama"]');
        if ($wrapper.length && typeof updateMsLabel === 'function') {
            updateMsLabel($wrapper);
        }
    }

    let izinLineChart = null;
    let izinLineChartKaryawan = null;

    function buildIzinYearBoundaries(months) {
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
                    style: { fontSize: '14px', color: '#333', fontWeight: '700' }
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

    function loadIzinChart() {
        const params = $.param(getIzinFilterParams(1));
        $.get("{{ url('/hr/hrdashboard/izin-chart') }}?" + params, function (res) {
            renderIzinChart(res);
        }).fail(function (xhr) {
            console.error('Gagal load chart izin', xhr);
        });
    }

    function getIzinTopSakitFilterParams() {
        const p = getIzinFilterParams(1);
        p.izin_ijin = ['Sakit'];
        return p;
    }

    function loadIzinTopSakit() {
        if ($('#izinTopSakitTbody').length === 0) return;
        const params = $.param(getIzinTopSakitFilterParams());
        $.get("{{ url('/hr/hrdashboard/izin-top-sakit') }}?" + params, function (res) {
            renderIzinTopSakit(res);
        }).fail(function (xhr) {
            console.error('Gagal load top 10 sakit', xhr);
        });
    }

    function renderIzinTopSakit(res) {
        if (!res || !res.data || res.data.length === 0) {
            $('#izinTopSakitTbody').html(
                '<tr><td colspan="7" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>'
            );
            return;
        }
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
                    <td>${escapeHtml(r.pws || '')}</td>
                    <td class="text-right" style="font-weight:700;color:#c2185b;">${r.total_records}</td>
                </tr>
            `;
        });
        $('#izinTopSakitTbody').html(html);
    }

    function getIzinTopMangkirFilterParams() {
        const p = getIzinFilterParams(1);
        p.izin_ijin = ['Mangkir'];
        return p;
    }

    function loadIzinTopMangkir() {
        if ($('#izinTopMangkirTbody').length === 0) return;
        const params = $.param(getIzinTopMangkirFilterParams());
        $.get("{{ url('/hr/hrdashboard/izin-top-mangkir') }}?" + params, function (res) {
            renderIzinTopMangkir(res);
        }).fail(function (xhr) {
            console.error('Gagal load top 10 mangkir', xhr);
        });
    }

    function renderIzinTopMangkir(res) {
        if (!res || !res.data || res.data.length === 0) {
            $('#izinTopMangkirTbody').html(
                '<tr><td colspan="7" class="text-center text-muted">Tidak ada data untuk filter ini.</td></tr>'
            );
            return;
        }
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
                    <td>${escapeHtml(r.pws || '')}</td>
                    <td class="text-right" style="font-weight:700;color:#6a1b9a;">${r.total_records}</td>
                </tr>
            `;
        });
        $('#izinTopMangkirTbody').html(html);
    }


    function getIzinSakitRatioDeptFilterParams() {
        const p = getIzinFilterParams(1);
        p.izin_ijin = ['Sakit'];
        return p;
    }

    let sakitRatioChart = null;
    let sakitRatioDrillDept = null;

    function loadIzinSakitRatioDept(drillDept) {
        if ($('#izinSakitRatioDeptChart').length === 0) return;
        sakitRatioDrillDept = drillDept || null;
        const filterParams = getIzinSakitRatioDeptFilterParams();
        if (drillDept) {
            filterParams.drilldown_dept = drillDept;
        }
        const params = $.param(filterParams);
        $.get("{{ url('/hr/hrdashboard/izin-sakit-ratio-dept') }}?" + params, function (res) {
            renderIzinSakitRatioDept(res);
        }).fail(function (xhr) {
            console.error('Gagal load sakit ratio dept', xhr);
        });
    }

    function renderIzinSakitRatioDept(res) {
        if (!res || !res.data || res.data.length === 0) {
            $('#izinSakitRatioDeptChart').html(
                '<p class="text-center text-muted p-4">Tidak ada data untuk filter ini.</p>'
            );
            $('#izinSakitRatioDeptToolbar').hide();
            $('#izinSakitRatioDeptMeta').text('');
            $('#izinSakitRatioDeptTitle').text('');
            return;
        }

        if (res.is_drilldown) {
            $('#izinSakitRatioDeptToolbar').show();
            $('#izinSakitRatioDeptTitle').text('Sub Departemen dalam: ' + res.drill_dept);
        } else {
            $('#izinSakitRatioDeptToolbar').hide();
            $('#izinSakitRatioDeptTitle').text('');
        }

        let metaText = 'Working Days: ' + res.working_days + ' hari (Senin–Sabtu)';
        if (res.tgl_from && res.tgl_to) {
            metaText += ' &mdash; ' + res.tgl_from + ' s/d ' + res.tgl_to;
        }
        $('#izinSakitRatioDeptMeta').html(metaText);

        const data = res.data.slice().sort((a, b) => a.ratio - b.ratio);
        const categories = data.map(d => d.label);
        const ratios = data.map(d => d.ratio);

        const tooltipDataMap = {};
        data.forEach(d => { tooltipDataMap[d.label] = d; });

        const options = {
            chart: {
                type: 'bar',
                height: Math.max(420, data.length * 42 + 80),
            },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                categories: categories,
                title: { text: null },
                labels: {
                    style: { fontSize: '14px' },
                    formatter: function () {
                        const val = this.value || '';
                        return val.length > 28 ? val.substring(0, 28) + '…' : val;
                    },
                },
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Sakit Ratio (%)',
                    style: { fontSize: '14px' },
                },
                labels: {
                    formatter: function () { return this.value.toFixed(1) + '%'; },
                    style: { fontSize: '14px' },
                },
            },
            legend: { enabled: false },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br/>',
                pointFormatter: function () {
                    const d = tooltipDataMap[this.category];
                    if (!d) return '';
                    return '<b style="color:#c2185b;">Ratio: ' + d.ratio.toFixed(2) + '%</b><br/>'
                        + 'Sick Days: ' + d.sick_days + ' hari<br/>'
                        + 'Headcount: ' + d.headcount + ' orang<br/>'
                        + 'Headcount Sick: ' + (d.headcount_sick || 0) + ' orang<br/>'
                        + 'Working Days: ' + d.working_days + ' hari';
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    dataLabels: {
                        enabled: true,
                        formatter: function () { return this.y.toFixed(2) + '%'; },
                        style: { fontSize: '14px', fontWeight: '600', color: '#333' },
                    },
                    point: {
                        events: {
                            click: function () {
                                if (!sakitRatioDrillDept && this.category) {
                                    loadIzinSakitRatioDept(this.category);
                                }
                            },
                        },
                    },
                    cursor: 'pointer',
                },
            },
            series: [{
                name: 'Sakit Ratio',
                data: ratios.map(r => ({ y: r, color: '#43a047' })),
            }],
        };

        if (sakitRatioChart) sakitRatioChart.destroy();
        sakitRatioChart = Highcharts.chart('izinSakitRatioDeptChart', options);
    }

    $(document).on('click', '#btnSakitRatioBack', function () {
        loadIzinSakitRatioDept(null);
    });

    const IZIN_CHART_SERIES = [
        { key: 'Cuti',     name: 'Cuti',     color: '#1565c0' },
        { key: 'Sakit',    name: 'Sakit',    color: '#e65100' },
        { key: 'Sakit KK', name: 'Sakit KK', color: '#f9a825' },
        { key: 'Mangkir',  name: 'Mangkir',  color: '#6a1b9a' },
    ];

    function buildIzinMultiLineChartOptions(months, valuesByKategori, yAxisTitle, valueLabel) {
        const { plotBands, plotLines } = buildIzinYearBoundaries(months);
        const series = IZIN_CHART_SERIES.map(s => ({
            name: s.name,
            color: s.color,
            data: months.map((m, i) => [
                new Date(m + '-01').getTime(),
                (valuesByKategori[s.key] || [])[i] || 0,
            ]),
            dataLabels: {
                enabled: true,
                crop: false,
                overflow: 'allow',
                useHTML: false,
                allowOverlap: true,
                style: {
                    fontSize: '14px',
                    color: s.color,
                    textOutline: '1px contrast',
                    fontWeight: '600',
                },
                formatter: function () {
                    return Math.round(this.y);
                },
            },
        }));

        return {
            chart: {
                type: 'line',
                height: 320,
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
                    style: { fontSize: '14px' },
                },
                plotBands: plotBands,
                plotLines: plotLines,
            },
            yAxis: {
                title: { text: yAxisTitle, style: { fontSize: '14px' } },
                labels: {
                    formatter: function () { return Math.round(this.value); },
                    style: { fontSize: '14px' },
                },
                min: 0,
            },
            legend: {
                enabled: true,
                position: 'top',
                align: 'right',
                itemStyle: { fontSize: '14px', fontWeight: 600 },
            },
            tooltip: {
                headerFormat: '<b>{point.x:%B %Y}</b><br/>',
                shared: true,
                pointFormatter: function () {
                    return '<span style="color:' + this.series.color + '">●</span> '
                        + '<b>' + this.series.name + '</b>: ' + Math.round(this.y) + ' ' + valueLabel;
                },
            },
            plotOptions: {
                line: {
                    lineWidth: 2.5,
                    marker: {
                        enabled: true,
                        radius: 4,
                        lineColor: '#fff',
                        lineWidth: 2,
                    },
                },
            },
            series: series,
        };
    }

    function renderIzinChart(data) {
        if (!data || !data.months || data.months.length === 0) {
            $('#izinChartBulanan').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            $('#izinChartKaryawanBulanan').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            return;
        }

        const optRows = buildIzinMultiLineChartOptions(
            data.months,
            data.rows || {},
            'Jumlah Hari Izin',
            'hari'
        );
        if (izinLineChart) izinLineChart.destroy();
        izinLineChart = Highcharts.chart('izinChartBulanan', optRows);

        const optKaryawan = buildIzinMultiLineChartOptions(
            data.months,
            data.distinct_nik || {},
            'Jumlah Karyawan',
            'karyawan'
        );
        if (izinLineChartKaryawan) izinLineChartKaryawan.destroy();
        izinLineChartKaryawan = Highcharts.chart('izinChartKaryawanBulanan', optKaryawan);
    }

    $('#btnIzinApply').on('click', function () {
        izinCurrentPage = 1;
        currentIzinNama = getMultiSelectValues('izin_nama');
        loadIzinData();
        loadIzinNames();
    });

    $('#btnIzinReset').on('click', function () {
        $('#izinTglRange').val('');
        ['izin_departmen', 'izin_sub_departmen', 'izin_tipe_karyawan', 'izin_pws', 'izin_ijin', 'izin_nama'].forEach(function (target) {
            const $wrapper = $('.hd-multi-select[data-target="' + target + '"]');
            $wrapper.find('input[type="checkbox"]').prop('checked', false);
            if ($wrapper.length && typeof updateMsLabel === 'function') {
                updateMsLabel($wrapper);
            }
        });
        @if(!empty($typeKaryawanMode) && in_array($typeKaryawanMode, ['mitra_kerja', 'BAS'], true))
            $('input[name="izin_tipe_karyawan[]"]').prop('checked', true);
            const $izinTipeWrap = $('.hd-multi-select[data-target="izin_tipe_karyawan"]');
            if ($izinTipeWrap.length && typeof updateMsLabel === 'function') {
                updateMsLabel($izinTipeWrap);
            }
        @endif
        currentIzinNama = [];
        izinCurrentPage = 1;
        loadIzinData();
        loadIzinNames();
    });

    $('#btnIzinExport').on('click', function () {
        const params = $.param(getIzinFilterParams(1));
        window.open("{{ url('/hr/hrdashboard/izin-export') }}?" + params, '_blank');
    });

    /* ------------------------------------------------------------------
     * Web Speech API: Text-to-Speech client-side (zero server dependency).
     *
     * Browser support:
     *   - Chrome/Edge (Desktop + Android): ✅ Indonesian voice
     *   - Safari (macOS/iOS): ⚠️ Fallback ke English voice
     *   - Firefox: ⚠️ Mungkin tidak ada Indonesian voice
     *
     * Flow:
     *   1. User klik tombol "Dengarkan" / "Dengarkan Berulang"
     *   2. JS extract data dari tabel yang sudah di-render
     *   3. Build narasi teks (Bahasa Indonesia)
     *   4. speechSynthesis.speak(utterance) — browser yang berbicara
     *   5. Untuk loop: pakai onend event untuk chain ke list berikutnya
     * ------------------------------------------------------------------ */

    let cachedIndonesianVoice = null;
    let voicesLoaded = false;

    function loadVoices() {
        if (! ('speechSynthesis' in window)) return;
        const voices = speechSynthesis.getVoices();
        if (voices.length === 0) return;
        // Cari voice Indonesia: chrome "Google Bahasa Indonesia" / lang "id-ID"
        cachedIndonesianVoice = voices.find(function (v) {
            return v.lang && v.lang.toLowerCase().indexOf('id') === 0;
        }) || null;
        voicesLoaded = true;
    }

    if ('speechSynthesis' in window) {
        loadVoices();
        // voices kadang load async di Chrome
        if (typeof speechSynthesis.onvoiceschanged !== 'undefined') {
            speechSynthesis.onvoiceschanged = loadVoices;
        }
    }

    /**
     * Extract data dari tabel Top 10 (Sakit/Mangkir) dan bangun narasi teks.
     * Format: "1. Nama, N hari."
     */
    function buildNarrationFromTable(tbodyId, opening) {
        const $tbody = $('#' + tbodyId);
        if ($tbody.length === 0) {
            return opening + ' Tabel tidak ditemukan.';
        }
        const txt = $tbody.text().toLowerCase();
        if (txt.indexOf('tidak ada data') >= 0 || txt.indexOf('klik') >= 0) {
            return opening + ' Tidak ada data.';
        }
        const rows = $tbody.find('tr');
        if (rows.length === 0) {
            return opening + ' Tidak ada data.';
        }
        let narration = opening + ' ';
        let totalHari = 0;
        rows.each(function (i) {
            const $cells = $(this).find('td');
            if ($cells.length < 7) return;
            // Kolom: 0=rank, 1=NIK, 2=Nama, 3=Dept, 4=Sub, 5=Group, 6=Hari
            const rank   = ($cells.eq(0).text() || '').replace('#', '').trim() || String(i + 1);
            const nama   = $cells.eq(2).text().trim();
            const hariTxt = $cells.eq(6).text().trim();
            const hari   = parseInt(hariTxt, 10) || 0;
            if (! nama) return;
            totalHari += hari;
            narration += rank + '. ' + nama + ', ' + hari + ' hari. ';
        });
        narration += 'Total ' + totalHari + ' hari.';
        return narration;
    }

    /**
     * Speak text pakai Web Speech API.
     */
    function speakText(text, onEnd, onError) {
        if (! ('speechSynthesis' in window)) {
            if (onError) onError(new Error('Browser tidak support Web Speech API.'));
            return;
        }
        if (speechSynthesis.speaking || speechSynthesis.pending) {
            speechSynthesis.cancel();
        }
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang  = 'id-ID';
        utter.rate  = 1.0;
        utter.pitch = 1.0;
        utter.volume = 1.0;
        if (cachedIndonesianVoice) {
            utter.voice = cachedIndonesianVoice;
        }
        if (onEnd)   utter.onend   = onEnd;
        if (onError) utter.onerror = onError;
        speechSynthesis.speak(utter);
    }

    /* ------------------------------------------------------------------
     * Single playback: tombol "Dengarkan" di card Top 10 Sakit / Mangkir.
     * ------------------------------------------------------------------ */
    function playSingleTable(tbodyId, opening, btn) {
        if (! ('speechSynthesis' in window)) {
            Swal.fire('Error', 'Browser tidak support Web Speech API. Gunakan Chrome/Edge/Safari/Firefox versi terbaru.', 'error');
            return;
        }
        const text = buildNarrationFromTable(tbodyId, opening);
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="la la-volume-up"></i> Membacakan...';
        speakText(
            text,
            function () {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            },
            function (e) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (e.error !== 'canceled' && e.error !== 'interrupted') {
                    Swal.fire('Error', 'Gagal membacakan teks: ' + (e.error || 'unknown'), 'error');
                }
            }
        );
    }

    $('#btnPlayTopMangkir').on('click', function () {
        playSingleTable('izinTopMangkirTbody', 'Top 10 Karyawan Mangkir.', this);
    });

    /* ------------------------------------------------------------------
     * Loop playback: tombol "Dengarkan Berulang" — membacakan
     * Top 10 Mangkir → loop sampai klik "Berhenti".
     * (Top 10 Karyawan Sakit tidak punya TTS.)
     * ------------------------------------------------------------------ */
    const LOOP_SEQUENCE = ['mangkir'];
    let loopState = {
        active:  false,
        stepIdx: 0,
    };

    function updateLoopStatus(state, text) {
        const $badge = $('#izinLoopStatusBadge');
        if ($badge.length === 0) return;
        $badge.show();
        $badge.removeClass('badge-secondary badge-success badge-warning badge-danger');
        let icon = 'la-pause';
        if (state === 'loading') {
            $badge.addClass('badge-warning');
            icon = 'la-spinner la-spin';
        } else if (state === 'playing') {
            $badge.addClass('badge-success');
            icon = 'la-volume-up';
        } else if (state === 'stopped') {
            $badge.addClass('badge-danger');
            icon = 'la-stop';
        } else {
            $badge.addClass('badge-secondary');
        }
        $badge.html('<i class="la ' + icon + '"></i> ' + text);
    }

    function speakNextLoop() {
        if (! loopState.active) return;
        // Saat ini hanya Mangkir yang di-loop. Top 10 Karyawan Sakit tidak punya TTS.
        const tbodyId = 'izinTopMangkirTbody';
        const opening = '10 Karyawan Mangkir Teratas.';
        const text = buildNarrationFromTable(tbodyId, opening);

        updateLoopStatus('playing', 'Top 10 Mangkir');

        speakText(
            text,
            function () {
                if (! loopState.active) return;
                loopState.stepIdx = (loopState.stepIdx + 1) % LOOP_SEQUENCE.length;
                // Sedikit delay supaya browser tidak bingung antar utterance
                setTimeout(speakNextLoop, 400);
            },
            function (e) {
                if (! loopState.active) return;
                if (e.error === 'canceled' || e.error === 'interrupted') return;
                console.error('[Web Speech API] error:', e);
                stopTopSkmkLoop();
                Swal.fire('Error', 'Gagal membacakan teks: ' + (e.error || 'unknown'), 'error');
            }
        );
    }

    function playTopSkmkLoop() {
        if (! ('speechSynthesis' in window)) {
            Swal.fire('Error', 'Browser tidak support Web Speech API. Gunakan Chrome/Edge/Safari/Firefox versi terbaru.', 'error');
            return;
        }
        if (! voicesLoaded) loadVoices();
        if (! cachedIndonesianVoice) {
            console.warn('[Web Speech API] Indonesian voice tidak ditemukan. Akan fallback ke default voice.');
        }
        loopState.active  = true;
        loopState.stepIdx = 0;
        $('#btnPlayTopSkmkLoop').hide();
        $('#btnStopTopSkmkLoop').show();
        updateLoopStatus('loading', 'Mulai...');
        speakNextLoop();
    }

    function stopTopSkmkLoop() {
        loopState.active = false;
        if ('speechSynthesis' in window) {
            speechSynthesis.cancel();
        }
        $('#btnPlayTopSkmkLoop').show();
        $('#btnStopTopSkmkLoop').hide();
        updateLoopStatus('stopped', 'Dihentikan');
    }

    $('#btnPlayTopSkmkLoop').on('click', function (e) {
        e.preventDefault();
        playTopSkmkLoop();
    });
    $('#btnStopTopSkmkLoop').on('click', function (e) {
        e.preventDefault();
        stopTopSkmkLoop();
    });

    flatpickr('#izinTglRange', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        locale: 'id',
        allowInput: true,
        showMonths: 2,
    });

    window.loadIzinData = loadIzinData;
    window.loadIzinNames = loadIzinNames;
})();
</script>
