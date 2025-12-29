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
            console.log('🔍 Memulai getDetail');
            console.log('ID Periode:', idPeriode);
            console.log('ID Group:', idGroup);

            $.ajax({
                url: "{{ route('5r-system.report.detail') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_periode: idPeriode,
                    id_group: idGroup
                },
                success: function(response) {
                    console.log('📦 Response diterima:', response);

                    if (response.status !== 'success') {
                        Swal.fire({
                            title: 'Woops!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Kosongkan table
                    $('#table-detail tbody').empty();

                    // Loop semua data berdasarkan jenis
                    $.each(response.data, function(jenis, items) {
                        console.log('\n📋 Jenis:', jenis);
                        console.log('Jumlah items:', items.length);

                        // Loop setiap item/jawaban
                        $.each(items, function(index, jawaban) {
                            console.log('\n  ➤ Jawaban #' + (index + 1));
                            console.log('    Pertanyaan:', jawaban.pertanyaan.item_periksa);
                            console.log('    Foto:', jawaban.foto);
                            console.log('    Temuan:', jawaban.temuan);

                            var fotoHTML = '';

                            // Cek apakah ada foto
                            if (jawaban.foto != null && jawaban.foto.trim() !== '') {

                                // Split foto jadi array
                                var fotoArray = jawaban.foto.split(',');
                                console.log('    📷 Foto array:', fotoArray);

                                // Buat array untuk menyimpan foto dari temuan
                                var fotoTemuan = [];
                                if (jawaban.temuan && Array.isArray(jawaban.temuan) && jawaban
                                    .temuan.length > 0) {
                                    $.each(jawaban.temuan, function(i, temuan) {
                                        if (temuan.foto && temuan.area) {
                                            fotoTemuan.push({
                                                nama: temuan.foto.trim(),
                                                area: temuan.area.nama_area,
                                                deskripsi: temuan.deskripsi ||
                                                    ''
                                            });
                                        }
                                    });
                                }
                                console.log('    🏷️  Foto dari temuan:', fotoTemuan);

                                // Loop setiap foto
                                $.each(fotoArray, function(idx, namaFoto) {
                                    namaFoto = namaFoto.trim();
                                    console.log('\n    🖼️  Foto ke-' + (idx + 1) + ':',
                                        namaFoto);

                                    var urlFoto = '';
                                    var badgeHTML = '';
                                    var deskripsiHTML = '';

                                    // Cari apakah foto ini ada di temuan
                                    var adaDiTemuan = false;
                                    var dataTemuan = null;

                                    $.each(fotoTemuan, function(i, ft) {
                                        if (ft.nama === namaFoto) {
                                            adaDiTemuan = true;
                                            dataTemuan = ft;
                                            return false; // break loop
                                        }
                                    });

                                    if (adaDiTemuan && dataTemuan !== null) {
                                        // ✅ FOTO DARI TEMUAN - pakai path lokal
                                        urlFoto = "{{ asset('images/5r/temuan') }}/" +
                                            namaFoto;

                                        badgeHTML =
                                            '<div class="badge bg-primary mb-2" style="font-size: 11px;">' +
                                            '<i class="mdi mdi-map-marker"></i> Area: ' +
                                            dataTemuan.area +
                                            '</div>';

                                        if (dataTemuan.deskripsi) {
                                            deskripsiHTML =
                                                '<div class="badge bg-info mt-2" style="font-size: 11px;">' +
                                                '<i class="mdi mdi-information"></i> ' +
                                                dataTemuan.deskripsi +
                                                '</div>';
                                        }

                                        console.log('      ✅ Sumber: TEMUAN (lokal)');
                                        console.log('      📍 Area:', dataTemuan.area);
                                    } else {
                                        // 🌐 FOTO BUKAN DARI TEMUAN - pakai server IP 172
                                        urlFoto = 'http://172.21.5.105/images/5r/' +
                                            namaFoto;

                                        badgeHTML =
                                            '<div class="badge bg-success mb-2" style="font-size: 11px;">' +
                                            '<i class="mdi mdi-server"></i> Server Utama' +
                                            '</div>';

                                        console.log(
                                            '      🌐 Sumber: SERVER 172.21.5.105');
                                    }

                                    console.log('      🔗 URL:', urlFoto);

                                    // Buat HTML untuk foto
                                    fotoHTML +=
                                        '<div class="mb-3 p-2 border rounded bg-light">' +
                                        badgeHTML +
                                        '<div class="text-center">' +
                                        '<img src="' + urlFoto + '" ' +
                                        'style="max-width:300px; width:100%; cursor:pointer; border-radius:4px;" ' +
                                        'onclick="showImageModal(\'' + urlFoto +
                                        '\')" ' +
                                        'onerror="handleImageError(this, \'' +
                                        namaFoto + '\')" ' +
                                        'onload="console.log(\'✅ Gambar berhasil dimuat:\', \'' +
                                        urlFoto + '\')">' +
                                        '</div>' +
                                        deskripsiHTML +
                                        '</div>';
                                });

                            } else {
                                // Tidak ada foto
                                fotoHTML = '<p class="text-muted"><i>Tidak ada foto</i></p>';
                                console.log('    ⚠️  Tidak ada foto');
                            }

                            // Append ke table
                            var rowHTML = '<tr>' +
                                '<td>' + jawaban.pertanyaan.jenis + '</td>' +
                                '<td>' +
                                '<div style="width: 300px">' +
                                '<h6 class="mb-2">ITEM PERIKSA</h6>' +
                                '<p>' + jawaban.pertanyaan.item_periksa + '</p>' +
                                '<h6 class="mb-2 mt-3">KETERANGAN</h6>' +
                                '<p>' + jawaban.pertanyaan.keterangan + '</p>' +
                                '</div>' +
                                '</td>' +
                                '<td>' +
                                '<h6 class="mb-2">NILAI</h6>' +
                                '<input type="text" class="form-control" style="width: 100px" disabled value="' +
                                jawaban.nilai + '">' +
                                '<div class="mt-3">' +
                                '<h6 class="mb-2">FOTO</h6>' +
                                fotoHTML +
                                '</div>' +
                                '<div class="mt-3 p-2 rounded bg-light">' +
                                '<h6 class="mb-2">KETERANGAN</h6>' +
                                '<p>' + (jawaban.keterangan || '-') + '</p>' +
                                '</div>' +
                                '</td>' +
                                '</tr>';

                            $('#table-detail tbody').append(rowHTML);
                        });
                    });

                    console.log('\n✅ Selesai render table, buka modal');
                    $('#detailModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('❌ AJAX Error:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);

                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengambil data: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
@endpush
