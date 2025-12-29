@extends('system5r.layouts.base')

@section('title', 'Report For Committee')

@push('styles')
    <style>
        table p {
            margin-bottom: 0 !important
        }

        table td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Report 5R Committee</h3>
                <small class="text-muted">
                    Ringkas • Rapi • Resik • Rawat • Rajin
                </small>
            </div>

            <div style="width: 220px">
                <label class="form-label mb-0">Jadwal Penilaian</label>
                <select id="filter_jadwal" class="form-control form-control-sm">
                    @foreach ($allJadwal as $jadwal)
                        <option value="{{ $jadwal->id_jadwal }}"
                            {{ $jadwal->id_jadwal == $latestJadwal->id_jadwal ? 'selected' : '' }}>
                            {{ $jadwal->tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="report-container"></div>

    </div>

    {{-- MODAL DETAIL (TETAP DIPAKAI) --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DETAIL PENILAIAN</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" id="table-detail">
                        <thead>
                            <tr class="pas-background-color">
                                <th class="text-white">GROUP</th>
                                <th class="text-white">PERTANYAAN</th>
                                <th class="text-white">NILAI</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadReport($('#filter_jadwal').val());

            $('#filter_jadwal').change(function() {
                loadReport($(this).val());
            });

            function loadReport(jadwalId) {
                $('#report-container').html('<div class="text-center p-5">Loading...</div>');

                $.post("{{ route('5r-system.report.committee.data') }}", {
                    _token: "{{ csrf_token() }}",
                    jadwal_id: jadwalId
                }, function(res) {

                    if (res.status !== 'success') return;

                    let html = '';

                    if (res.workspace.length === 0) {
                        $('#report-container').html(
                            '<div class="alert alert-warning">Data tidak ditemukan</div>');
                        return;
                    }

                    res.workspace.forEach(ws => {

                        html += `
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h4 class="mb-3">${ws.name}</h4>
                                ${buildTable(ws.departments)}
                            </div>
                        </div>`;
                    });

                    $('#report-container').html(html);
                });
            }

            // Render TABLE
            function buildTable(departments) {

                // Kumpulkan data per periode
                let periodeMap = {};

                departments.forEach(dep => {
                    if (!dep.periode) return;

                    dep.periode.forEach(p => {

                        if (!periodeMap[p.nama_periode]) {
                            periodeMap[p.nama_periode] = [];
                        }

                        periodeMap[p.nama_periode].push({
                            department: dep.id_department,
                            __total: dep.__total,
                            group: p.group || [],
                            id_periode: p.id_periode,
                            juri: p.juri || []
                        });
                    });
                });

                let html = '';

                // Render per PERIODE
                Object.keys(periodeMap).forEach(namaPeriode => {

                    let rank = 1;

                    // sort ranking dalam 1 tahap
                    periodeMap[namaPeriode].sort((a, b) => b.__total - a.__total);

                    let rows = '';

                    periodeMap[namaPeriode].forEach(item => {
                        const totalGroup = item.group.length;
                        const presentase = totalGroup * 100;

                        if (item.group.length === 0) {
                            rows += `
                                <tr>
                                    <td class="text-center">${rank}</td>
                                    <td>${item.department}</td>
                                    <td class="text-muted">-</td>
                                    <td class="text-muted">-</td>
                                    <td class="text-muted">-</td>
                                    <td>
                                        <span class="badge badge-soft-warning">
                                            Belum Dinilai
                                        </span>
                                    </td>
                                    <td class="text-center">-</td>
                                </tr>
                            `;
                            rank++;
                            return;
                        }

                        item.group.forEach((g, i) => {

                            let printUrl = "{{ route('5r-system.report.print', '') }}/" + g
                                .encryptedKey;

                            rows += `
                                <tr>
                                    <td class="text-center">${i === 0 ? rank : ''}</td>
                                    <td>${i === 0 ? item.department : ''}</td>
                                    <td>${item.juri.length ? item.juri.join(', ') : '-'}</td>
                                    <td>${g.nama_group}</td>
                                     <td>${i === 0 ? presentase + '%' : ''}</td>   
                                    <td>${g.nilaiAkhir.toFixed(1)}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="getDetail('${item.id_periode}','${g.id_group}')">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            <a href="${printUrl}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        rank++;
                    });

                    html += `
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary mb-2">
                                Periode ${namaPeriode}
                            </h5>
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Department</th>
                                        <th>Juri</th>
                                        <th>Group</th>
                                        <th>Presentase</th>
                                        <th>Nilai Akhir</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    `;
                });

                return html;
            }
        });

       
    </script>
@endpush
