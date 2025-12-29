@extends('system5r.layouts.base')

@section('title', 'Report For Management')

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
                <h3 class="mb-0">Report 5R Management</h3>
                <small class="text-muted">
                    Ringkas • Rapi • Resik • Rawat • Rajin • Digitalisasi
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

                $.post("{{ route('5r-system.report.management.data') }}", {
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
                                <h4 class="mb-3 fw-bold">${ws.name}</h4>
                                ${buildTable(ws.departments)}
                            </div>
                        </div>`;
                    });

                    $('#report-container').html(html);
                });
            }

            // Render TABLE
            function buildTable(departments) {
                let periodeMap = {};

                departments.forEach(dep => {
                    if (!dep.periode) return;

                    dep.periode.forEach(p => {

                        if (!periodeMap[p.nama_periode]) {
                            periodeMap[p.nama_periode] = [];
                        }

                        let groupArray = [];
                        if (Array.isArray(p.group)) {
                            groupArray = p.group;
                        } else if (p.group && typeof p.group === 'object') {
                            groupArray = Object.values(p.group);
                        }

                        periodeMap[p.nama_periode].push({
                            department: dep
                                .nama_department,
                            __total: dep.__total,
                            group: groupArray,
                            id_periode: p.id_periode,
                            juri: Array.isArray(p.juri) ? p.juri : []
                        });
                    });
                });

                let html = '';

                Object.keys(periodeMap).forEach(namaPeriode => {
                    let rank = 1;

                    periodeMap[namaPeriode].sort((a, b) => b.__total - a.__total);

                    let rows = '';

                    periodeMap[namaPeriode].forEach(item => {
                        const totalGroup = item.group.length;
                        const presentase = totalGroup * 100;

                        if (totalGroup === 0) {
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
                        <td>${i === 0 ? (item.juri.length ? item.juri.join(', ') : '-') : ''}</td>
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
                        <h5 class="fw-medium text-primary mb-2">
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

        function getDetail(idPeriode, idGroup) {
            console.log('=== MULAI getDetail ===');
            console.log('idPeriode:', idPeriode);
            console.log('idGroup:', idGroup);

            $.ajax({
                url: "{{ route('5r-system.report.detail') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}", // ⚠️ TAMBAHKAN INI
                    id_periode: idPeriode,
                    id_group: idGroup
                },
                success: function(response) {
                    console.log('=== RESPONSE DITERIMA ===');
                    console.log('Response:', response);

                    if (response.status == 'success') {
                        $('#table-detail tbody').html('');
                        var noParent = 1;

                        Object.values(response.data).forEach(function(item) {
                            console.log('=== Processing Item ===');
                            console.log('Item:', item);

                            var no = 1;
                            item.forEach(function(jawaban) {
                                console.log('--- Processing Jawaban ---');
                                console.log('Jawaban:', jawaban);
                                console.log('Foto jawaban:', jawaban.foto);
                                console.log('Temuan jawaban:', jawaban.temuan);

                                var foto = '';
                                if (jawaban.foto != null) {
                                    var fotoNameArray = jawaban.foto.split(',');
                                    console.log('fotoNameArray:', fotoNameArray);

                                    // Ambil informasi area dari temuan jika ada
                                    var temuanAreas = [];
                                    if (jawaban.temuan && jawaban.temuan.length > 0) {
                                        jawaban.temuan.forEach(function(temuan) {
                                            if (temuan.area && temuan.foto) {
                                                temuanAreas.push({
                                                    foto: temuan.foto,
                                                    area: temuan.area.nama_area,
                                                    deskripsi: temuan.deskripsi
                                                });
                                            }
                                        });
                                    }
                                    console.log('temuanAreas:', temuanAreas);

                                    fotoNameArray.forEach(function(fotoName, index) {
                                        console.log(
                                            `\n--- Foto ${index + 1}: ${fotoName} ---`
                                        );

                                        let fotoPath = '';
                                        let fallbackPath = '';
                                        let areaLabel = '';
                                        let deskripsiLabel = '';

                                        // Cek apakah foto ini ada di temuan
                                        const temuanMatch = temuanAreas.find(t => t
                                            .foto === fotoName);
                                        console.log('temuanMatch:', temuanMatch);

                                        if (temuanMatch) {
                                            // FOTO ADA DI TEMUAN - gunakan path lokal
                                            fotoPath =
                                                "{{ asset('images/5r/temuan/') }}/" +
                                                fotoName;
                                            fallbackPath = fotoPath;

                                            areaLabel = `
                                            <div class="badge bg-primary mb-1" style="font-size: 11px;">
                                                <i class="mdi mdi-map-marker"></i> Area: ${temuanMatch.area}
                                            </div>
                                        `;

                                            // Tambahkan deskripsi jika ada
                                            if (temuanMatch.deskripsi) {
                                                deskripsiLabel = `
                                            <div class="badge bg-info mt-1" style="font-size: 11px;">
                                                <i class="mdi mdi-information"></i> ${temuanMatch.deskripsi}
                                            </div>
                                            `;
                                            }

                                            console.log('✅ Foto DARI TEMUAN');
                                        } else {
                                            // FOTO TIDAK ADA DI TEMUAN - gunakan PROXY Laravel
                                            fotoPath =
                                                "{{ route('5r-system.proxy-image', '') }}/" +
                                                fotoName;
                                            fallbackPath =
                                                "{{ asset('images/placeholder.jpg') }}"; // Gambar placeholder jika gagal

                                            console.log(
                                                `  ║ 🌐 SOURCE: Remote via Proxy`);

                                            areaLabel = `
                                                <div class="badge bg-success mb-1" style="font-size: 11px;">
                                                    <i class="mdi mdi-server"></i> Foto dari Server Utama
                                                </div>
                                            `;
                                        }

                                        console.log(`  ║ 🔗 Path: ${fotoPath}`);
                                        console.log(
                                            `  ╚═══════════════════════════════\n`);

                                        foto += `
                                            <div class="mb-2 p-2 border rounded bg-light">
                                                ${areaLabel}
                                                <div class="d-flex justify-content-center">
                                                    <img
                                                        src="${fotoPath}"
                                                        style="max-width:300px;width:100%;cursor:pointer"
                                                        onclick="showImageModal('${fotoPath}')"
                                                        onerror="
                                                            console.error('❌ Image load failed:', '${fotoPath}');
                                                            this.onerror=null;
                                                            this.src='${fallbackPath}';
                                                            this.parentElement.parentElement.querySelector('.badge').classList.remove('bg-success');
                                                            this.parentElement.parentElement.querySelector('.badge').classList.add('bg-danger');
                                                            this.parentElement.parentElement.querySelector('.badge').innerHTML = '<i class=\'mdi mdi-alert\'></i> Foto Tidak Tersedia';
                                                        "
                                                        onload="console.log('✅ Image loaded:', '${fotoPath}')"
                                                    />
                                                </div>
                                                ${deskripsiLabel}
                                            </div>
                                        `;
                                    });
                                } else {
                                    foto = `<i class="text-muted">No Foto</i>`;
                                    console.log('⚠️ Tidak ada foto');
                                }

                                $('#table-detail tbody').append(`
                            <tr>
                                <td>${jawaban.pertanyaan.jenis}</td>
                                <td>
                                    <div style="width: 300px">
                                        <h6>ITEM PERIKSA</h6>
                                        ${jawaban.pertanyaan.item_periksa}
                                        <h6 class="mt-3">KETERANGAN</h6>
                                        ${jawaban.pertanyaan.keterangan}
                                    </div>
                                </td>
                                <td>
                                    <h6>NILAI</h6>
                                    <input style="width: 100px" class="form-control" disabled value="${jawaban.nilai}" />
                                    <div class="mt-3">
                                        <h6>FOTO</h6>
                                        ${foto}
                                    </div>
                                    <div class="mt-3 rounded bg-light p-1">
                                        <h6>KETERANGAN</h6>
                                        <p>${jawaban.keterangan}</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                                no++;
                            });
                            noParent++;
                        });

                        console.log('=== MODAL AKAN DITAMPILKAN ===');
                        $('#detailModal').modal('show');
                    } else {
                        console.error('Response status bukan success:', response);
                        Swal.fire({
                            title: 'Woops!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('=== AJAX ERROR ===');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);

                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
@endpush
