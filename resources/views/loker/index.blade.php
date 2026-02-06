@extends('layouts.base')

@section('title', 'Loker Management')

@section('styles')
    <style>
        .locker-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 12px;
            height: 170px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .locker-code {
            font-size: 12px;
            color: #6c757d;
        }

        .locker-icon {
            text-align: center;
            font-size: 30px;
        }

        .locker-info small {
            font-size: 11px;
            color: #6c757d;
        }

        .locker-status {
            text-align: center;
            font-size: 12px;
            padding: 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .locker-modal {
            border-radius: 14px;
        }

        .locker-user {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
            background: #fff;
        }

        .locker-user h6 {
            margin-bottom: 4px;
            font-weight: 600;
        }

        .locker-user small {
            color: #6b7280;
            display: block;
            font-size: 12px;
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .foto-user img {
            width: 60px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }

        .skeleton {
            position: relative;
            overflow: hidden;
            background-color: #e5e7eb;
        }

        .skeleton::after {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.6),
                    transparent);
            animation: shimmer 1.2s infinite;
        }

        @keyframes shimmer {
            100% {
                left: 100%;
            }
        }

        .foto-skeleton {
            width: 60px;
            height: 80px;
            border-radius: 8px;
        }
    </style>

@section('content')
    {{-- Main Content --}}
    <div class="container-fluid">
        <div class="card card-custom">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title">
                    <h3>Loker Management BAS</h3>
                </div>

                <div>
                    <button class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImportLoker">
                        <i class="fas fa-file-excel me-1"></i>
                        Import Excel
                    </button>

                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalUserLoker">
                        <i class="fas fa-users me-1"></i>
                        List Loker Karyawan Aktif
                    </button>
                </div>

            </div>


            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger" style="border-radius: 13px;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Card Info --}}
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card card-custom bg-light-success">
                            <div class="card-body">
                                <h5 class="card-label">Tersedia</h5>
                                <h1>{{ $countTersedia }}</h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card card-custom bg-light-warning">
                            <div class="card-body">
                                <h5 class="card-label">Terisi</h5>
                                <h1>{{ $countTerisi }}</h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card card-custom bg-light-secondary">
                            <div class="card-body">
                                <h5 class="card-label">Perbaikan</h5>
                                <h1>{{ $countPerbaikan }}</h1>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rak Loker --}}
                <div class="my-8">
                    <div class="card-title">
                        <h3>Rak Loker</h3>
                    </div>

                    {{-- hard code nama rak --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light-primary text-center cursor-pointer" onclick="getBlokByGender('pria')">
                                <div class="card bg-light-primary text-center">
                                    <div class="card-body">
                                        <h5>Loker Pria (Baju & Sepatu)</h5>
                                        <small>
                                            Tersedia: {{ $countPerGender['pria']['tersedia'] }} |
                                            Terisi: {{ $countPerGender['pria']['terisi'] }} |
                                            Perbaikan: {{ $countPerGender['pria']['perbaikan'] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light-primary text-center cursor-pointer"
                                onclick="getBlokByGender('wanita')">
                                <div class="card bg-light-primary text-center">
                                    <div class="card-body">
                                        <h5>Loker Wanita (Baju & Sepatu)</h5>
                                        <small>
                                            Tersedia: {{ $countPerGender['wanita']['tersedia'] }} |
                                            Terisi: {{ $countPerGender['wanita']['terisi'] }} |
                                            Perbaikan: {{ $countPerGender['wanita']['perbaikan'] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="blok-container"></div>

                    <div id="global-loading" class="text-center my-8 d-none">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <div class="mt-2">Memuat data loker...</div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Penghuni --}}
    <div class="modal fade" id="modalLokerDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content locker-modal">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">
                            Loker Blok <span id="mKodeBlok"></span>
                            Nomor <span id="mNoLoker"></span>
                        </h5>
                        <small class="text-muted">
                            <span id="mJenisKelamin"></span>
                        </small>
                    </div>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-sm btn-primary d-none" id="btnTambahPenghuni" onclick="openTambahPenghuni()">
                            <i class="fas fa-plus mr-1"></i> Tambah Penghuni
                        </button>

                        <button class="btn btn-sm btn-secondary d-none" id="btnTandaiRusak" onclick="tandaiRusak()">
                            <i class="fas fa-tools mr-1"></i> Tandai Rusak
                        </button>
                    </div>

                    <div id="penghuniContainer"></div>

                </div>


            </div>
        </div>
    </div>


    {{-- Modal Tambah Penghuni --}}
    <div class="modal fade" id="modalTambahPenghuni" tabindex="-1">
        <div class="modal-dialog">
            <form id="formTambahPenghuni">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Penghuni Loker</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        {{-- context loker --}}
                        <input type="hidden" id="tp_gender">
                        <input type="hidden" id="tp_blok">
                        <input type="hidden" id="tp_no_loker">

                        <div class="mb-2">
                            <label>NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tp_nik" required placeholder="NIK">
                        </div>

                        <div class="mb-2">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tp_nama" required placeholder="Nama">
                        </div>

                        <div class="mb-2">
                            <label>Departemen <span class="text-danger">*</span></label>
                            <select class="form-control" id="tp_divisi" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Kategori Karyawan <span class="text-danger">*</span></label>
                            <select class="form-control" id="tp_staff" required>
                                <option value="">Pilih Kategori</option>
                                <option value="staff">Staff</option>
                                <option value="non_staff">Non Staff</option>
                                <option value="mitra_kerja">Mitra Kerja</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Jenis Kelamin</label>
                            <input type="text" class="form-control bg-light" id="tp_gender_label" readonly>
                        </div>

                        <div class="mb-2">
                            <label>Nomor Loker</label>
                            <input type="text" class="form-control bg-light" id="tp_no_loker_label" readonly>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSubmitTambahPenghuni">
                            Simpan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tarik Kunci --}}
    <div class="modal fade" id="modalTarikKunci" tabindex="-1">
        <div class="modal-dialog">
            <form id="formTarikKunci">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tarik Kunci Loker</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="tk_nik">
                        <div class="mb-2">
                            <label>Alasan Penarikan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="tk_alasan" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" id="btnSubmitTarikKunci">
                            Tarik Kunci
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Datatable --}}
    <div class="modal fade" id="modalUserLoker" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Daftar Karyawan yang Sudah Memiliki Loker
                    </h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    {{-- FILTER JK --}}
                    <ul class="nav nav-tabs mb-3" id="jkTabsModal">
                        <li class="nav-item">
                            <a class="nav-link active" data-jk="">Semua</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-jk="L">Pria</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-jk="P">Wanita</a>
                        </li>
                    </ul>

                    <div class="table-responsive">
                        <table id="tableUserLokerModal" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Dept</th>
                                    <th>JK</th>
                                    <th>Loker Baju</th>
                                    <th>Loker Sepatu</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <div class="modal fade" id="modalImportLoker">
        <div class="modal-dialog" role="document">
            <form id="importForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Loker</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-info">
                            Download template Excel terlebih dahulu, lalu isi sesuai format.
                        </div>

                        <div class="form-group">
                            <a href="{{ asset('templates/template-loker.xlsx') }}" class="btn btn-outline-primary mb-3"
                                target="_blank">
                                <i class="fas fa-download mr-1"></i>
                                Download Template Excel
                            </a>
                        </div>

                        <div class="form-group">
                            <label>
                                File Excel <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" name="file" accept=".xlsx,.xls" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="btnImportSubmit">
                            <i class="fas fa-upload mr-1"></i>
                            Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        const blokLoaded = {};
        let currentLoker = {};
        let currentBlokOpened = null;

        function getBlokByGender(gender) {
            for (let key in blokLoaded) delete blokLoaded[key];
            currentBlokOpened = null;

            showGlobalLoading();

            $.ajax({
                url: `/loker/${gender}/blok`,
                type: 'GET',
                success: function(data) {
                    let html = `
        <div class="card-title mt-4">
            <h3>Loker ${gender.charAt(0).toUpperCase() + gender.slice(1)} Baju & Sepatu</h3>
        </div>
    `;

                    data.forEach(blok => {
                        const blokId = blok.blok_nomor.replace('-', '_');

                        html += `
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white cursor-pointer"
                    onclick="toggleBlok('${gender}', '${blok.blok_nomor}')">
                    <strong>Blok ${blok.blok_nomor}</strong>
                    <span class="float-right">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </div>

                <div class="card-body d-none" id="blok-body-${blokId}">
                    <div class="text-center my-3" id="loading-${blokId}">
                        <i class="fas fa-spinner fa-spin"></i>
                        <div>Memuat loker...</div>
                    </div>
                    <div class="row g-3" id="blok-${blokId}"></div>
                </div>
            </div>
        `;
                    });

                    $('#blok-container').html(html);
                    hideGlobalLoading();
                },
                error: function() {
                    hideGlobalLoading();
                    alert('Gagal memuat blok');
                },
                complete: function() {
                    hideGlobalLoading();
                }
            });
        }

        function toggleBlok(gender, blok) {
            const blokId = blok.replace('-', '_');
            const body = $('#blok-body-' + blokId);

            body.toggleClass('d-none');

            currentBlokOpened = {
                gender,
                blok
            };

            if (blokLoaded[blokId]) {
                return;
            }

            getNomorByBlok(gender, blok);
            blokLoaded[blokId] = true;
        }

        function getNomorByBlok(gender, blok) {
            const blokId = blok.replace('-', '_');

            $.ajax({
                url: '/loker/' + gender + '/blok/' + blok,
                type: 'GET',
                data: {
                    gender,
                    blok
                },
                success: function(data) {
                    let html = '';

                    if (!data.length) {
                        html = `<div class="col-12 text-center text-muted">Tidak ada data</div>`;
                    }

                    data.forEach(loker => {
                        let borderClass, iconClass, icon, statusClass, statusText;
                        let infoHtml = '';

                        // INFO (terisi / kapasitas + kategori)
                        const kategoriText = loker.kategori_staff ?
                            loker.kategori_staff.replace(/_/g, ' ')
                            .toLowerCase()
                            .replace(/\b\w/g, c => c.toUpperCase()) :
                            '-';

                        infoHtml = `
        <div class="locker-info">
            <small><i class="fas fa-user mr-2"></i>${loker.terisi}/${loker.kapasitas} orang</small><br>
            <small><i class="fas fa-id-card mr-2"></i>${kategoriText}</small><br>
        </div>
    `;

                        // STATUS
                        if (loker.status === 'terisi') {
                            borderClass = 'border-warning';
                            iconClass = 'text-warning';
                            icon = 'fa-lock';
                            statusClass = 'bg-warning text-dark';
                            statusText = 'Terisi';
                        } else if (loker.status === 'perbaikan') {
                            borderClass = 'border-secondary bg-light-secondary';
                            iconClass = 'text-secondary';
                            icon = 'fa-tools';
                            statusClass = 'bg-white';
                            statusText = 'Perbaikan';
                            infoHtml = ''; // perbaikan tidak tampilkan info
                        } else {
                            borderClass = 'border-success';
                            iconClass = 'text-success';
                            icon = 'fa-lock-open';
                            statusClass = 'bg-success text-white';
                            statusText = 'Tersedia';
                        }

                        const isPerbaikan = loker.status === 'perbaikan';

                        html += `
                            <div class="col-lg-2 col-md-4 col-6 mb-6">

<div class="locker-card border ${borderClass} cursor-pointer"
    onclick="${
        isPerbaikan
            ? `openAktifkanLoker('${gender}', '${blok}', ${loker.no_loker})`
            : `getLokerDetail('${gender}', '${blok}', ${loker.no_loker})`
    }">   
         
        <div class="locker-code">${loker.no_loker}</div>

        <div class="locker-icon ${iconClass}">
            <i class="fas ${icon}"></i>
        </div>

        ${infoHtml}

        <div class="locker-status ${statusClass}">
            ${statusText}
        </div>
    </div>
</div>

`;
                    });

                    $('#loading-' + blokId).remove();
                    $('#blok-' + blokId).html(html);
                },
                error: function() {
                    $('#loading-' + blokId).html(
                        '<div class="text-danger">Gagal memuat data</div>'
                    );
                }
            });
        }


        function getLokerDetail(gender, blok, noLoker) {
            currentLoker = {
                gender,
                blok: blok,
                no_loker: noLoker
            };


            $('#modalLokerDetail').modal('show');

            $('#btnTambahPenghuni').addClass('d-none');
            $('#btnTandaiRusak').addClass('d-none');

            $('#penghuniContainer').html(
                '<div class="text-muted text-center">Memuat data...</div>'
            );

            $.ajax({
                url: `/loker/${gender}/blok/${blok}/nomor/${noLoker}/detail`,
                type: 'GET',
                success: function(data) {
                    $('#btnTandaiRusak')
                        .prop('disabled', false)
                        .removeClass('disabled');

                    $('#btnTambahPenghuni')
                        .prop('disabled', false)
                        .removeClass('disabled');

                    // ===== HEADER MODAL =====
                    $('#mKodeBlok').text(data.kode_blok);
                    $('#mNoLoker').text(data.no_loker);
                    $('#mJenisKelamin').text(data.jenis_kelamin);

                    // ===== BUILD HTML DULU =====
                    let html = `
                <div class="mb-4">
                    <div class="row mb-2 align-items-center">
                        <div class="col-4">Status</div>
                        <div class="col-8 text-right">
                            <span class="badge badge-success d-none" id="badgeTersedia">Tersedia</span>
                            <span class="badge badge-warning d-none" id="badgeTerisi">Terisi</span>
                            <span class="badge badge-secondary d-none" id="badgePerbaikan">Perbaikan</span>
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-4">Kapasitas</div>
                        <div class="col-8 text-right">
                            <strong id="mKapasitas"></strong>
                        </div>
                    </div>

                    <div class="row mb-2align-items-center">
                        <div class="col-4">Kategori</div>
                        <div class="col-8 text-right">
                            <strong id="mKategori"></strong>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
            `;

                    if (!data.penghuni.length) {
                        html += `<div class="text-muted text-center">Loker kosong</div>`;
                    } else {
                        data.penghuni.forEach(p => {
                            // <div id="foto-${p.nik}" class="foto-user mr-4 skeleton foto-skeleton">
                            //     <div class="bg-secondary rounded" style="width:64px;height:64px"></div>
                            // </div>
                            html += `
<div class="locker-user d-flex align-items-center">

    <div class="flex-grow-1">
        <h6 class="mb-1">${p.nama}</h6>
        <small class="d-block">NIK: ${p.nik}</small>
        <small class="d-block">Departemen: ${p.dept.replace(/_/g, ' ')}</small>
        <small class="d-block">${capitalize(p.staff)}</small>
    </div>

    <div class="ml-2">
        <button class="btn btn-sm btn-danger" onclick="openTarikKunci('${p.nik}', '${p.nama}')">
            <i class="fas fa-key mr-1"></i> 
            Tarik Kunci
        </button>
    </div>
</div>
`;
                        });

                    }

                    // ===== RENDER KE DOM =====
                    $('#penghuniContainer').html(html);

                    // data.penghuni.forEach(p => {
                    //     setTimeout(() => getFoto(p.nik), 50);
                    // });


                    // ===== SET DATA SETELAH DOM ADA =====
                    $('#mKapasitas')
                        .text(`${data.terisi} / ${data.kapasitas} orang`)
                        .removeClass('text-success text-warning text-secondary');

                    let kategori = '-';

                    if (data.penghuni.length > 0) {
                        kategori = capitalize(data.penghuni[0].staff);
                    }

                    $('#mKategori').text(kategori);


                    $('#badgeTersedia, #badgeTerisi, #badgePerbaikan')
                        .addClass('d-none');

                    if (data.status === 'tersedia') {
                        $('#badgeTersedia').removeClass('d-none');
                        $('#mKapasitas').addClass('text-success');
                    }

                    if (data.status === 'terisi') {
                        $('#badgeTerisi').removeClass('d-none');
                        $('#mKapasitas').addClass('text-warning');
                    }

                    if (data.status === 'perbaikan') {
                        $('#badgePerbaikan').removeClass('d-none');
                        $('#mKapasitas').addClass('text-secondary');
                    }

                    if (
                        data.status === 'tersedia' &&
                        data.terisi < data.kapasitas
                    ) {
                        $('#btnTambahPenghuni').removeClass('d-none');
                    }

                    // TOMBOL TANDAI RUSAK
                    if (
                        data.status === 'tersedia' &&
                        data.terisi === 0
                    ) {
                        $('#btnTandaiRusak').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#penghuniContainer').html(
                        '<div class="text-danger text-center">Gagal memuat detail loker</div>'
                    );
                }
            });
        }

        // function getFoto(nik) {
        //     $.ajax({
        //         url: `/loker/foto/${nik}`,
        //         type: 'GET',
        //         success: function(res) {
        //             if (res.success && res.image) {
        //                 $('#foto-' + nik)
        //                     .removeClass('skeleton foto-skeleton')
        //                     .html(`<img src="${res.image}" alt="Foto">`);
        //             } else {
        //                 $('#foto-' + nik).remove();
        //             }
        //         },
        //         error: function() {
        //             $('#foto-' + nik).remove();
        //         }
        //     });
        // }

        function capitalize(text) {
            return text.replace(/_/g, ' ')
                .toLowerCase()
                .replace(/\b\w/g, c => c.toUpperCase());
        }

        function showGlobalLoading() {
            $('#blok-container').empty();
            $('#global-loading').removeClass('d-none');
        }

        function hideGlobalLoading() {
            $('#global-loading').addClass('d-none');
        }

        function openTambahPenghuni() {
            $('#modalLokerDetail').modal('hide');

            setTimeout(() => {

                $('#formTambahPenghuni')[0].reset();
                $('#modalTambahPenghuni').modal('show');

                // Mapping gender pria/wanita → L/P
                let jkValue = currentLoker.gender === 'pria' ? 'L' : 'P';
                let jkText = currentLoker.gender === 'pria' ? 'Pria' : 'Wanita';

                // hidden input (buat dikirim ke backend)
                $('#tp_gender').val(jkValue);

                // label input (buat display)
                $('#tp_gender_label').val(jkText);

                // nomor loker
                $('#tp_no_loker').val(currentLoker.no_loker);
                $('#tp_no_loker_label').val(currentLoker.no_loker);

                // blok kalau perlu
                $('#tp_blok').val(currentLoker.blok);

            }, 300);
        }


        $('#modalTambahPenghuni').on('show.bs.modal', function() {
            $('#tp_nik').val('');
            $('#tp_nama').val('');
            $('#tp_divisi').val('');
            $('#tp_staff').val('');
        });

        $('#formTambahPenghuni').on('submit', function(e) {
            e.preventDefault();

            const btn = $('#btnSubmitTambahPenghuni');

            if (btn.prop('disabled')) return;

            btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

            const dataToSend = {
                _token: '{{ csrf_token() }}',
                jk: $('#tp_gender').val(),
                no_loker: $('#tp_no_loker').val(),
                nik: $('#tp_nik').val(),
                nama: $('#tp_nama').val(),
                divisi: $('#tp_divisi').val(),
                staff: $('#tp_staff').val(),
            };

            console.log({
                dataToSend
            });

            $.ajax({
                url: '/hr-connect/masters/loker-user/store',
                type: 'POST',
                data: dataToSend,
                success: function() {
                    $('#modalTambahPenghuni').modal('hide');

                    reloadCurrentBlok();

                    // refresh detail loker
                    getLokerDetail(
                        currentLoker.gender,
                        currentLoker.blok,
                        currentLoker.no_loker
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Penghuni loker berhasil ditambahkan',
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message ||
                            'Gagal menambah penghuni. Silakan coba lagi.',
                    });
                },
                complete: function() {
                    btn.prop('disabled', false)
                        .html('Simpan');
                }
            });
        });

        function reloadCurrentBlok() {
            if (!currentBlokOpened) return;

            const blokId = currentBlokOpened.blok.replace('-', '_');

            $('#blok-' + blokId).empty();
            $('#loading-' + blokId).show();

            getNomorByBlok(
                currentBlokOpened.gender,
                currentBlokOpened.blok
            );
        }

        function tandaiRusak() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Loker akan ditandai sebagai rusak / perbaikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, tandai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $('#btnTandaiRusak').prop('disabled', true);

                $.ajax({
                    url: '/loker/tandai-rusak',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        gender: currentLoker.gender,
                        blok: currentLoker.blok,
                        no_loker: currentLoker.no_loker
                    },
                    success: function() {
                        $('#modalLokerDetail').modal('hide');

                        reloadCurrentBlok();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Loker berhasil ditandai rusak'
                        });
                    },
                    error: function(xhr) {
                        $('#btnTandaiRusak').prop('disabled', false);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menandai loker'
                        });
                    }
                });
            });
        }

        function openAktifkanLoker(gender, blok, noLoker) {
            Swal.fire({
                title: 'Aktifkan Loker?',
                text: 'Loker akan diaktifkan kembali dan bisa digunakan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, aktifkan',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '/loker/tandai-aktif',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        gender,
                        blok,
                        no_loker: noLoker
                    },
                    success: function() {
                        reloadCurrentBlok();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Loker berhasil diaktifkan kembali'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal mengaktifkan loker'
                        });
                    }
                });
            });
        }

        function openTarikKunci(nik, nama) {
            $('#modalLokerDetail').modal('hide');

            setTimeout(() => {
                $('#tk_nik').val(nik);
                $('#tk_alasan').val('');
                $('#modalTarikKunci').modal('show');
            }, 300);
        }

        $('#formTarikKunci').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSubmitTarikKunci');

            if (btn.prop('disabled')) return;
            const nik = $('#tk_nik').val();
            const alasan = $('#tk_alasan').val();

            btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');

            $.ajax({
                url: '/loker/tarik-kunci',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nik,
                    alasan
                },
                success: function() {
                    $('#modalTarikKunci').modal('hide');
                    $('#modalLokerDetail').modal('hide');
                    reloadCurrentBlok();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Kunci berhasil ditarik'
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Gagal menarik kunci'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false)
                        .html('Tarik Kunci');
                }
            });
        });

        let tableUserLoker;
        let selectedJKModal = '';

        $('#modalUserLoker').on('shown.bs.modal', function() {

            if (!$.fn.DataTable) {
                console.error('DataTable plugin not loaded');
                return;
            }

            if (!tableUserLoker) {
                tableUserLoker = $('#tableUserLokerModal').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: '/hr-connect/masters/loker-user/getData',
                        data: function(d) {
                            d.jk = selectedJKModal;
                        }
                    },
                    columns: [{
                            data: 'nik'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'divisi'
                        },
                        {
                            data: 'jk',
                            render: d => d === 'pria' ? 'Pria' : 'Wanita'
                        },
                        {
                            data: 'loker_baju'
                        },
                        {
                            data: 'loker_sepatu'
                        },
                        {
                            data: 'staff',
                            render: d =>
                                d.replace(/_/g, ' ')
                                .replace(/\b\w/g, c => c.toUpperCase())
                        },
                    ]
                });
            } else {
                tableUserLoker.ajax.reload(null, false);
            }
        });

        $(document).on('click', '#jkTabsModal .nav-link', function() {
            $('#jkTabsModal .nav-link').removeClass('active');
            $(this).addClass('active');

            selectedJKModal = $(this).data('jk');

            if (tableUserLoker) {
                tableUserLoker.ajax.reload();
            }
        });

        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            const btn = $('#btnImportSubmit');
            const formData = new FormData(this);

            btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-1"></i> Mengimpor...');

            $.ajax({
                url: '/loker/import',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Selesai',
                        text: res.message || 'Data berhasil diimport'
                    });

                    $('#modalImportLoker').modal('hide');
                    $('#importForm')[0].reset();

                    // reload datatable
                    $('#tableAjax').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat import'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false)
                        .html('<i class="fas fa-upload me-1"></i> Import');
                }
            });
        });
    </script>
@endpush
