{{-- Izin Scripts: filter, table, names, chart, PWS dynamic, tab integration --}}
<script src="{{ asset('assets/js/line-chart-non-library-fauzi.js') }}"></script>
<script>
(function () {
    'use strict';

    let izinCurrentPage = 1;
    let currentIzinNama = [];
    let lastIzinStats = null;

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
                lastIzinStats = res.stats;
                updateIzinStatTotal(res.stats);
                $('#izinStatTotalHariCuti').text((res.stats.total_hari_cuti || 0).toLocaleString());
                $('#izinStatTotalHariSakit').text((res.stats.total_hari_sakit || 0).toLocaleString());
                $('#izinStatTotalHariSakitKK').text((res.stats.total_hari_sakit_kk || 0).toLocaleString());
                $('#izinStatTotalHariMangkir').text((res.stats.total_hari_mangkir || 0).toLocaleString());
                $('#izinStatTotalHariMinggu').text((res.stats.total_hari_minggu || 0).toLocaleString());
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
            loadIzinRatioGabungan();
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


    let ratioGabunganChart = null;
    let ratioGabunganDrillDept = null;

    function loadIzinRatioGabungan(drillDept) {
        if ($('#izinRatioGabunganChart').length === 0) return;
        ratioGabunganDrillDept = drillDept || null;

        const sakitParams = getIzinFilterParams(1);
        sakitParams.izin_ijin = ['Sakit'];
        if (drillDept) sakitParams.drilldown_dept = drillDept;

        const mangkirParams = getIzinFilterParams(1);
        mangkirParams.izin_ijin = ['Mangkir'];
        if (drillDept) mangkirParams.drilldown_dept = drillDept;

        $.when(
            $.get("{{ url('/hr/hrdashboard/izin-sakit-ratio-dept') }}?" + $.param(sakitParams)),
            $.get("{{ url('/hr/hrdashboard/izin-mangkir-ratio-dept') }}?" + $.param(mangkirParams))
        ).done(function (sakitRes, mangkirRes) {
            renderIzinRatioGabungan(sakitRes[0], mangkirRes[0]);
        }).fail(function (xhr) {
            console.error('Gagal load ratio Sakit & Mangkir', xhr);
            $('#izinRatioGabunganChart').html(
                '<p class="text-center text-muted p-4">Gagal memuat data ratio.</p>'
            );
        });
    }

    function renderIzinRatioGabungan(sakitRes, mangkirRes) {
        if (!$('#izinRatioGabunganChart').length) return;

        const sakitData = (sakitRes && sakitRes.data) ? sakitRes.data : [];
        const mangkirData = (mangkirRes && mangkirRes.data) ? mangkirRes.data : [];
        const workingDays = (sakitRes && sakitRes.working_days)
            || (mangkirRes && mangkirRes.working_days)
            || 0;
        const tglFrom = (sakitRes && sakitRes.tgl_from)
            || (mangkirRes && mangkirRes.tgl_from)
            || '';
        const tglTo = (sakitRes && sakitRes.tgl_to)
            || (mangkirRes && mangkirRes.tgl_to)
            || '';
        const isDrill = Boolean(
            (sakitRes && sakitRes.is_drilldown) ||
            (mangkirRes && mangkirRes.is_drilldown)
        );
        const drillDept = (sakitRes && sakitRes.drill_dept)
            || (mangkirRes && mangkirRes.drill_dept)
            || '';

        if (sakitData.length === 0 && mangkirData.length === 0) {
            $('#izinRatioGabunganChart').html(
                '<p class="text-center text-muted p-4">Tidak ada data untuk filter ini.</p>'
            );
            $('#izinRatioGabunganToolbar').hide();
            $('#izinRatioGabunganMeta').text('');
            $('#izinRatioGabunganTitle').text('');
            return;
        }

        if (isDrill) {
            $('#izinRatioGabunganToolbar').show();
            $('#izinRatioGabunganTitle').text('Sub Departemen dalam: ' + drillDept);
        } else {
            $('#izinRatioGabunganToolbar').hide();
            $('#izinRatioGabunganTitle').text('');
        }

        let metaText = 'Working Days: ' + workingDays + ' hari (Senin–Sabtu)';
        if (tglFrom && tglTo) {
            metaText += ' &mdash; ' + tglFrom + ' s/d ' + tglTo;
        }
        $('#izinRatioGabunganMeta').html(metaText);

        const sakitMap = {};
        sakitData.forEach(d => { sakitMap[d.label] = d; });
        const mangkirMap = {};
        mangkirData.forEach(d => { mangkirMap[d.label] = d; });

        const allLabels = new Set();
        sakitData.forEach(d => allLabels.add(d.label));
        mangkirData.forEach(d => allLabels.add(d.label));

        const merged = Array.from(allLabels).map(label => {
            const s = sakitMap[label] || {
                ratio: 0,
                sick_days: 0,
                headcount_sick: 0,
                headcount: 0,
                working_days: workingDays,
                dept: '',
                sub_dept: null,
                can_drill: false,
            };
            const m = mangkirMap[label] || {
                ratio: 0,
                mangkir_days: 0,
                headcount_mangkir: 0,
                headcount: 0,
                working_days: workingDays,
                dept: '',
                sub_dept: null,
                can_drill: false,
            };
            return {
                label: label,
                sakitRatio: Number(s.ratio || 0),
                mangkirRatio: Number(m.ratio || 0),
                sakitDays: Number(s.sick_days || 0),
                mangkirDays: Number(m.mangkir_days || 0),
                headcount: Math.max(Number(s.headcount || 0), Number(m.headcount || 0)),
                headcountSick: Number(s.headcount_sick || 0),
                headcountMangkir: Number(m.headcount_mangkir || 0),
                workingDays: Number(s.working_days || m.working_days || workingDays),
                dept: s.dept || m.dept || '',
                subDept: s.sub_dept || m.sub_dept || '',
                canDrill: Boolean(s.can_drill || m.can_drill),
            };
        }).sort((a, b) => String(a.label).localeCompare(String(b.label), 'id'));

        const categories = merged.map(d => d.label);
        const tooltipDataMap = {};
        merged.forEach(d => { tooltipDataMap[d.label] = d; });

        const options = {
            chart: {
                type: 'bar',
                height: Math.max(420, merged.length * 50 + 100),
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
                    text: 'Ratio (%)',
                    style: { fontSize: '14px' },
                },
                labels: {
                    formatter: function () { return this.value.toFixed(1) + '%'; },
                    style: { fontSize: '14px' },
                },
            },
            legend: {
                enabled: true,
                align: 'right',
                verticalAlign: 'top',
                itemStyle: { fontSize: '14px', fontWeight: 600 },
            },
            tooltip: {
                shared: false,
                pointFormatter: function () {
                    const d = tooltipDataMap[this.category];
                    if (!d) return '';
                    if (this.series.name === 'Sakit Ratio') {
                        return '<b style="color:#43a047;">Sakit Ratio: ' + d.sakitRatio.toFixed(2) + '%</b><br/>'
                            + 'Sick Days: ' + d.sakitDays + ' hari<br/>'
                            + 'Headcount: ' + d.headcount + ' orang<br/>'
                            + 'Headcount Sick: ' + d.headcountSick + ' orang<br/>'
                            + 'Working Days: ' + d.workingDays + ' hari';
                    }
                    return '<b style="color:#ee9f27;">Mangkir Ratio: ' + d.mangkirRatio.toFixed(2) + '%</b><br/>'
                        + 'Mangkir Days: ' + d.mangkirDays + ' hari<br/>'
                        + 'Headcount: ' + d.headcount + ' orang<br/>'
                        + 'Headcount Mangkir: ' + d.headcountMangkir + ' orang<br/>'
                        + 'Working Days: ' + d.workingDays + ' hari';
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    pointPadding: 0.0,
                    groupPadding: 0.1,
                    dataLabels: {
                        enabled: true,
                        formatter: function () { return this.y.toFixed(2) + '%'; },
                        style: { fontSize: '14px', fontWeight: '600', color: '#333' },
                    },
                    point: {
                        events: {
                            click: function () {
                                const d = tooltipDataMap[this.category];
                                if (!ratioGabunganDrillDept && d && d.canDrill && d.dept) {
                                    loadIzinRatioGabungan(d.dept);
                                }
                            },
                        },
                    },
                    cursor: 'pointer',
                },
            },
            series: [
                {
                    name: 'Sakit Ratio',
                    color: '#43a047',
                    data: merged.map(d => ({ y: d.sakitRatio, color: '#43a047' })),
                },
                {
                    name: 'Mangkir Ratio',
                    color: '#ee9f27',
                    data: merged.map(d => ({ y: d.mangkirRatio, color: '#ee9f27' })),
                },
            ],
        };

        if (ratioGabunganChart) ratioGabunganChart.destroy();
        ratioGabunganChart = Highcharts.chart('izinRatioGabunganChart', options);
    }

    $(document).on('click', '#btnRatioGabunganBack', function () {
        loadIzinRatioGabungan(null);
    });

    const IZIN_CHART_SERIES = [
        { key: 'Cuti',     name: 'Cuti',     color: '#1565c0' },
        { key: 'Sakit',    name: 'Sakit',    color: '#e65100' },
        { key: 'Sakit KK', name: 'Sakit KK', color: '#f9a825' },
        { key: 'Mangkir',  name: 'Mangkir',  color: '#6a1b9a' },
    ];

    function renderIzinChart(data) {
        if (!data || !data.months || data.months.length === 0) {
            $('#izinChartBulanan').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            $('#izinChartKaryawanBulanan').html('<p class="text-center text-muted p-4">Tidak ada data untuk range ini.</p>');
            return;
        }

        var xLabels = data.months.map(function (m) {
            return new Date(m + '-01').toLocaleDateString('id-ID', { month: 'short' });
        });

        var series1 = IZIN_CHART_SERIES.map(function (s) {
            return {
                name: s.name,
                color: s.color,
                data: (data.rows && data.rows[s.key] || []).map(function (v) { return v || 0; }),
            };
        });

        if (izinLineChart) izinLineChart.destroy();
        izinLineChart = new FauziLineChart({
            container: '#izinChartBulanan',
            height: 320,
            series: series1,
            xLabels: xLabels,
            months: data.months,
            yAxisTitle: 'Jumlah Hari Izin',
            valueUnit: 'hari',
        });

        var series2 = IZIN_CHART_SERIES.map(function (s) {
            return {
                name: s.name,
                color: s.color,
                data: (data.distinct_nik && data.distinct_nik[s.key] || []).map(function (v) { return v || 0; }),
            };
        });

        if (izinLineChartKaryawan) izinLineChartKaryawan.destroy();
        izinLineChartKaryawan = new FauziLineChart({
            container: '#izinChartKaryawanBulanan',
            height: 320,
            series: series2,
            xLabels: xLabels,
            months: data.months,
            yAxisTitle: 'Jumlah Karyawan',
            valueUnit: 'karyawan',
        });
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

    function updateIzinStatTotal(stats) {
        $('#izinStatTotalHariIzin').text((stats.total_hari_kerja_hilang_tipe1 || 0).toLocaleString());
        $('#izinStatTotalHariIzinTipe2').text((stats.total_hari_kerja_hilang_tipe2 || 0).toLocaleString());
    }
})();
</script>
