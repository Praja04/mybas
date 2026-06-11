@extends('hr-connect.layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 36px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        .copy-nik {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-nik:hover {
            color: #0ab39c !important;
            text-decoration: underline;
        }

        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- <div class="row mb-3 align-items-end">
        <div class="col-lg-3">
            <label class="form-label font-weight-bold text-muted">Filter Jadwal Masuk</label>
            <select class="form-select form-control shadow-sm" id="tanggalFilter">
                <option value="" disabled selected>Pilih Tanggal</option>
                @if ($tanggalTersedia->isEmpty())
                <option value="" disabled>Belum ada jadwal karyawan masuk</option>
                @else
                @foreach ($tanggalTersedia as $date)
                <option value="{{ $date }}">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-soft-secondary w-100 shadow-sm" id="btnResetFilter">
                <i class="ri-refresh-line align-bottom me-1"></i> Reset Filter
            </button>
        </div>
    </div> --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-success text-success rounded-circle fs-4 shadow-sm">
                                    <i class="ri-login-box-line"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-1" style="font-weight: 600;">Alokasi Loker Karyawan Baru</h5>
                                <p class="text-muted mb-0 fs-13">Manajemen pembagian fasilitas loker fisik dan history
                                    penempatan</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-success fw-bold shadow-sm" id="btnExportExcel"
                                data-bs-toggle="tooltip" title="Unduh data alokasi ke Excel">
                                <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 20%;">Nama Lengkap</th>
                                        <th style="width: 10%;">NIK</th>
                                        <th style="width: 10%;">Kategori</th>
                                        <th style="width: 25%;">Alokasi Loker</th>
                                        <th style="width: 5%;">Divisi</th>
                                        <th style="width: 15%;">Tgl Masuk</th>
                                        <th style="width: 15%; text-align: center;">Tindakan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('hr-connect.ga.partials._modal_verifikasi')
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>

    <script>
        $(document).ready(function() {
            const lokerPria = {!! json_encode($lokerPria) !!};
            const lokerWanita = {!! json_encode($lokerWanita) !!};
            let state = {
                showAll: 1,
                defaultTanggal: ""
            };


            function copyToClipboard(text) {
                let tempInput = $("<input>");
                $("body").append(tempInput);
                tempInput.val(text).select();
                document.execCommand("copy");
                tempInput.remove();

                Toastify({
                    text: "NIK Disalin: " + text,
                    duration: 3000,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "#0ab39c",
                }).showToast();
            }

            function renderLokerDropdown(selectLokerElement, rowData, chosenDivisi) {
                let kategori = rowData.checkStaff === 'Y' ? 'staff' : 'non_staff';
                let listLoker = rowData.jenis_kelamin === 'L' ? lokerPria : lokerWanita;
                let opsiLoker = "";
                let firstSelected = true;

                listLoker.forEach(function(loker) {
                    let bolehMasuk = false;

                    if (kategori === 'staff' && loker.total_penghuni == 0) {
                        bolehMasuk = true;
                    } else if (kategori === 'non_staff' && loker.total_penghuni < loker.kapasitas && loker
                        .kategori_tersedia !== 'staff') {
                        if (loker.total_penghuni == 0) {
                            bolehMasuk = true;
                        } else {
                            let lokerDivisi = loker.divisi_tersedia ? loker.divisi_tersedia.trim() : '';

                            if (chosenDivisi && chosenDivisi === lokerDivisi) {
                                bolehMasuk = true;
                            }
                        }
                    }

                    if (bolehMasuk) {
                        let opsiRak = (loker.kode_rak === 'LP') ? 'P' : ((loker.kode_rak === 'LW') ? 'W' :
                            loker.kode_rak);

                        let selectedAttr = firstSelected ? 'selected' : '';
                        firstSelected = false;

                        opsiLoker +=
                            `<option value="${loker.id}" data-nik="${rowData.nik}" data-nama="${rowData.nama}" data-kode-rak="${loker.kode_rak}" data-no-loker="${loker.no_loker}" ${selectedAttr}>${opsiRak} - ${loker.no_loker} (Isi: ${loker.total_penghuni} / ${loker.kapasitas})</option>`;
                    }
                });

                if (opsiLoker === "") {
                    opsiLoker = `<option value="" disabled selected>Tidak ada loker tersedia</option>`;
                }

                selectLokerElement.html(opsiLoker)
                selectLokerElement.prop('disabled', false)
                selectLokerElement.select2({
                    width: "100%"
                });

                selectLokerElement.trigger('change');
            }

            // function generateLokerOptions(row) {
            //     let kategori = row.checkStaff === 'Y' ? 'staff' : 'non_staff';
            //     let listLoker = row.jenis_kelamin === 'L' ? lokerPria : lokerWanita;
            //     let rekomendasiLokerId = null;
            //     let opsiLoker = `<option value="" disabled selected>Pilih Loker</option>`;

            //     let karyawanDivisi = row.kode_divisi ? row.kode_divisi.trim() : '';

            //     if (karyawanDivisi.startsWith('PRD')) {
            //         karyawanDivisi = 'PRD BAS';
            //     } else if (karyawanDivisi.startsWith('QC')) {
            //         karyawanDivisi = 'QCB BAS';
            //     }

            //     listLoker.forEach(function(loker) {
            //         let bolehMasuk = false;

            //         if (kategori === 'staff' && loker.total_penghuni == 0) {
            //             bolehMasuk = true;
            //         } else if (kategori === 'non_staff' && loker.total_penghuni < loker.kapasitas && loker
            //             .kategori_tersedia !== 'staff') {

            //             if (loker.total_penghuni == 0) {
            //                 bolehMasuk = true;
            //             } else {
            //                 let lokerDivisi = loker.divisi_tersedia ? loker.divisi_tersedia.trim() : '';

            //                 if (karyawanDivisi === lokerDivisi) {
            //                     bolehMasuk = true;
            //                 }
            //             }
            //         }

            //         if (bolehMasuk) {
            //             if (rekomendasiLokerId === null) rekomendasiLokerId = loker.id;
            //             let terpilih = (loker.id === rekomendasiLokerId) ? 'selected' : '';
            //             let opsiRak = (loker.kode_rak === 'LP') ? 'P' : ((loker.kode_rak === 'LW') ? 'W' :
            //                 loker.kode_rak);

            //             opsiLoker += `<option value="${loker.id}" ${terpilih} data-nik="${row.nik}" data-nama="${row.nama}" data-kode-rak="${loker.kode_rak}" data-no-loker="${loker.no_loker}">
        //                             ${opsiRak} - ${loker.no_loker} (Isi: ${loker.total_penghuni}/${loker.kapasitas})
        //                           </option>`;
            //         }
            //     });
            //     return opsiLoker;
            // }

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                ordering: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    url: "{{ url('/hr-connect/dept-ga/karyawan-masuk/getData') }}",
                    type: "GET",
                    data: function(d) {
                        d.tanggal = state.defaultTanggal;
                        d.tampilkan_semua = state.showAll;
                    }
                },
                columns: [{
                    data: 'nama',
                    render: data => `<span class="fw-bold">${data}</span>`
                }, {
                    data: 'nik',
                    render: data =>
                        `<span class="copy-nik fw-bold text-dark" data-nik="${data}" data-bs-toggle="tooltip" title="Salin NIK">${data}</span>`
                }, {
                    data: 'checkStaff',
                    render: data => data === 'Y' ? '<span class="badge bg-secondary">Staff</span>' :
                        '<span class="badge bg-warning">Non Staff</span>'
                }, {
                    data: null,
                    render: function(data, type, row) {
                        if (row.penghuni) {
                            let rak = (row.penghuni.kode_rak === 'LP') ? 'P' : ((row.penghuni
                                .kode_rak === 'LW') ? 'W' : row.penghuni.kode_rak);
                            return `<select class="form-select form-select-sm bg-light" disabled><option>${rak} - ${row.penghuni.no_loker}</option></select>`;
                        }

                        let karyawanDivisi = row.kode_divisi ? row.kode_divisi.trim() : '';
                        if (karyawanDivisi.startsWith('PRD') || karyawanDivisi.startsWith(
                                'PRO')) {
                            karyawanDivisi = 'PRD BAS';
                        } else if (karyawanDivisi.startsWith('QC') || karyawanDivisi.startsWith(
                                'QCB')) {
                            karyawanDivisi = 'QCB BAS';
                        } else {
                            karyawanDivisi = '';
                        }

                        let divisiList = ['PRD BAS', 'QCB BAS'];
                        // if (karyawanDivisi && !divisiList.includes(karyawanDivisi)) {
                        //     divisiList.push(karyawanDivisi);
                        // }

                        let opsiDivisi =
                            `<option value=""></option>`;
                        divisiList.forEach(div => {
                            let sel = (div === karyawanDivisi) ? 'selected' : '';
                            opsiDivisi +=
                                `<option value="${div}" ${sel}>${div}</option>`;
                        });

                        let uiLoker = `
                                <div class="wrapper-select-loker d-none mb-1">
                                    <div class="mb-1">
                                        <select class="form-select form-select-sm divisiLoker" style="width: 100%;">
                                            ${opsiDivisi}
                                        </select>
                                    </div>
                                    <div>
                                        <select class="form-select form-select-sm lokerNo" style="width: 100%;" disabled>
                                            <option value="" disabled selected>Pilih Divisi Bagian Dulu</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" class="status-tanpa-loker" value="0">
                            `;

                        if (row.in_complete === 'Y') {
                            return `
                                    <div class="d-flex flex-column align-items-center gap-1 mb-1 wrapper-tombol-awal">
                                        <span class="badge bg-soft-danger text-danger border border-danger w-100 py-1 mb-1"><i class="ri-close-circle-line align-bottom"></i> Tanpa Loker</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-butuh-loker w-100" title="Tambahkan loker susulan">
                                            <i class="ri-add-circle-fill me-1"></i> Susulkan Loker
                                        </button>
                                    </div>
                                    ${uiLoker}
                                `;
                        } else {
                            return `
                                    <div class="d-flex gap-1 mb-1 wrapper-tombol-awal">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-butuh-loker w-50" title="Beri Loker"><i class="ri-add-circle-fill me-1"></i> Butuh</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-tanpa-loker w-50" title="Tanpa Loker"><i class="ri-close-circle-fill me-1"></i> Tanpa</button>
                                    </div>
                                    ${uiLoker}
                                    <div class="wrapper-teks-tanpa-loker d-none mb-1 text-center">
                                        <span class="badge bg-soft-danger text-danger border border-danger w-100 py-2"><i class="ri-close-circle-line align-bottom me-1"></i> Tidak Butuh Loker</span>
                                    </div>
                                `;
                        }
                    }
                }, {
                    data: 'kode_divisi',
                    render: data => data ? data : '-'
                }, {
                    data: 'tanggal_masuk',
                    render: data => data ? moment(data).format('DD MMM YYYY') : '-'
                }, {
                    data: null,
                    render: function(data, type, row) {
                        if (row.penghuni) {
                            return `<center><span class="badge bg-success px-3 py-2 shadow-sm"><i class="ri-check-double-line align-bottom me-1"></i> Tervalidasi</span></center>`;
                        } else if (row.in_complete === 'Y') {
                            return `
                                    <center>
                                        <span class="badge bg-danger px-3 py-2 shadow-sm mb-2 d-block badge-status-verif"><i class="ri-check-double-line align-bottom me-1"></i> Tervalidasi (Tanpa Loker)</span>
                                        <button type="button" class="btn btn-sm btn-dark fw-bold btn-verifikasi shadow-sm d-none" data-idcard="${row.id}" data-rfid="${row.cardnodevice}" disabled><i class="ri-barcode-box-line align-bottom me-1"></i> Verif Susulan</button>
                                    </center>`;
                        } else {
                            return `<center><button type="button" class="btn btn-sm btn-dark fw-bold btn-verifikasi shadow-sm" data-idcard="${row.id}" data-rfid="${row.cardnodevice}" disabled><i class="ri-barcode-box-line align-bottom me-1"></i> Verifikasi</button></center>`;
                        }
                    }
                }, ]
            });

            // table.on('draw.dt', function() {
            //     $('.js-example-basic-single').select2({
            //         minimumResultsForSearch: 10
            //     });
            //     $('.lokerNo').trigger('change');
            //     $('[data-bs-toggle="tooltip"]').tooltip();
            // });

            // Filter Events
            $("#tanggalFilter").on("change", function() {
                state.defaultTanggal = $(this).val();
                state.showAll = 0;
                table.draw();
            });

            $("#btnResetFilter").on("click", function() {
                state.showAll = 1;
                state.defaultTanggal = '';
                $("#tanggalFilter").val("");
                table.draw();
            });

            // Action UI Events
            $(document).on('click', '.copy-nik', function() {
                let nik = $(this).data('nik');
                if (nik) copyToClipboard(nik);
            });

            $(document).on('click', '.btn-butuh-loker', function(e) {
                e.preventDefault();
                let tr = $(this).closest('tr');
                $(this).closest('.wrapper-tombol-awal').addClass('d-none');

                let wrapperLoker = tr.find('.wrapper-select-loker').removeClass('d-none');
                // wrapperLoker.find('.js-example-basic-single').select2().trigger('change');
                let divisiSelect = wrapperLoker.find('.divisiLoker');
                divisiSelect.select2(({
                    tags: true,
                    placeholder: "--Pilih Divisi Bagian--",
                    allowClear: true,
                    width: '100%'
                }));

                divisiSelect.trigger('change');

                tr.find('.badge-status-verif').addClass('d-none');
                tr.find('.btn-verifikasi').removeClass('d-none');
            });

            $(document).on('click', '.btn-tanpa-loker', function(e) {
                e.preventDefault();
                let tr = $(this).closest('tr');
                $(this).closest('.wrapper-tombol-awal').addClass('d-none');

                tr.find('.wrapper-teks-tanpa-loker').removeClass('d-none');
                tr.find('.status-tanpa-loker').val('1');

                tr.find('.btn-verifikasi').prop('disabled', false)
                    .removeClass('btn-dark').addClass('btn-outline-danger')
                    .html('<i class="ri-barcode-box-line"></i> Verifikasi (Tanpa Loker)');
            });

            $(document).on('change', '.divisiLoker', function() {
                let tr = $(this).closest('tr');
                let rowData = $('#tableAjax').DataTable().row(tr).data();
                let chosenDivisi = $(this).val();
                let selectLoker = tr.find('.lokerNo');
                let btnVerif = tr.find('.btn-verifikasi');

                if (chosenDivisi) {
                    renderLokerDropdown(selectLoker, rowData, chosenDivisi);
                } else {
                    selectLoker.html(
                        '<option value="" disabled selected>Pilih Divisi Bagian Dulu</option>');
                    selectLoker.prop('disabled', true).select2({
                        width: '100%'
                    });

                    btnVerif.prop('disabled', true).removeClass('btn-outline-success').addClass('btn-dark');
                }
            });

            $(document).on('change', '.lokerNo', function() {
                let tr = $(this).closest('tr');
                let btnVerif = tr.find('.btn-verifikasi');

                if ($(this).val()) {
                    btnVerif.prop('disabled', false).removeClass('btn-dark').addClass(
                        'btn-outline-success');
                } else {
                    btnVerif.prop('disabled', true).removeClass('btn-outline-success').addClass('btn-dark');
                }
            })

            // $(document).on('change', '.wrapper-select-loker select', function() {
            //     let tr = $(this).closest('tr');
            //     let btnVerif = tr.find('.btn-verifikasi');
            //     if ($(this).val() && !$(this).closest('.wrapper-select-loker').hasClass('d-none')) {
            //         btnVerif.prop('disabled', false).removeClass('btn-dark').addClass(
            //             'btn-outline-success');
            //     } else {
            //         btnVerif.prop('disabled', true).removeClass('btn-outline-success').addClass('btn-dark');
            //     }
            // });

            // Modal Verification Events
            $(document).on('click', '.btn-verifikasi', function() {
                let btn = $(this);
                let tr = btn.closest('tr');
                let rowData = $('#tableAjax').DataTable().row(tr).data();
                let isWithoutLoker = tr.find('.status-tanpa-loker').val() === '1';
                let selectedLoker = tr.find('.lokerNo option:selected');

                if (isWithoutLoker) {
                    $('#verif_loker_tujuan, #verif_hidden_lokerid, #verif_hidden_koderak, #verif_hidden_noloker')
                        .val('');
                    $('#wrapper_verif_loker').hide();
                } else {
                    if (!selectedLoker.val()) return Swal.fire('Peringatan',
                        'Pilih nomor alokasi loker terlebih dahulu!', 'warning');

                    let rakRaw = selectedLoker.data('kode-rak');
                    let rakDisplay = (rakRaw === 'LP') ? 'P' : ((rakRaw === 'LW') ? 'W' : rakRaw);
                    $('#verif_loker_tujuan').val(`${rakDisplay} - ${selectedLoker.data('no-loker')}`);
                    $('#verif_hidden_lokerid').val(selectedLoker.val());
                    $('#verif_hidden_koderak').val(rakRaw);
                    $('#verif_hidden_noloker').val(selectedLoker.data('no-loker'));
                    $('#wrapper_verif_loker').show();
                }

                $('#verif_target_nik').val(rowData.nik);
                $('#verif_nama').val(rowData.nama);
                $('#verif_kategori').val(rowData.staff === 'Y' ? 'Staff' : 'Non Staff');
                $('#verif_hidden_idcard').val(btn.data('idcard'));
                // $('#verif_hidden_divisi').val(rowData.kode_divisi);
                let chosenDivisi = tr.find('.divisiLoker').val();
                $('#verif_hidden_divisi').val(chosenDivisi || rowData.kode_divisi);
                $('#verif_hidden_jk').val(rowData.jenis_kelamin);

                $('#verif_detail_container, #verif_footer').hide();
                $('#verif_rfid_scan').val('').prop('disabled', false);
                $('#verif_foto_img').attr('src', "{{ asset('assets/media/users/default.jpg') }}");
                $('#verif_status_text').removeClass('bg-success bg-danger bg-warning').addClass(
                    'bg-secondary').html('<i class="ri-rfid-line mr-1"></i> Menunggu scan Kartu ID...');

                $('#btnSimpanVerifikasi').prop('disabled', true);
                $('#modalVerifikasi').modal('show');
            });

            $('#modalVerifikasi').on('shown.bs.modal', function() {
                $('#verif_rfid_scan').focus();
            });

            $('#verif_rfid_scan').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let scannedRfid = $(this).val().trim();
                    let targetNik = $('#verif_target_nik').val();
                    if (!scannedRfid) return;

                    $(this).prop('disabled', true);
                    $('#verif_status_text').removeClass('bg-secondary bg-success bg-danger').addClass(
                        'bg-warning').html(
                        '<i class="spinner-border spinner-border-sm mr-1"></i> Memverifikasi...');
                    $('#verif_detail_container, #verif_footer').slideUp(200);

                    $.ajax({
                        url: "{{ url('/loker/search-karyawan') }}/" + scannedRfid,
                        type: "GET"
                    }).then(res => {
                        if (!res.success) throw new Error(res.message || 'Data tidak ditemukan.');
                        if (String(res.data.nik) !== String(targetNik)) throw new Error(
                            `Kartu ID tidak sesuai! Ini milik: ${res.data.nama}`);

                        if (res.data.foto) $('#verif_foto_img').attr('src', res.data.foto);
                        $('#verif_status_text').removeClass('bg-warning bg-danger').addClass(
                            'bg-success').html(
                            '<i class="ri-checkbox-circle-line mr-1"></i> Verifikasi berhasil!');

                        $('#btnSimpanVerifikasi').prop('disabled', false);
                        $('#verif_detail_container, #verif_footer').slideDown(400);
                    }).catch(err => {
                        $('#verif_status_text').removeClass('bg-warning bg-success').addClass(
                            'bg-danger').html(
                            `<i class="ri-error-warning-line mr-1"></i> ${err.message || 'Terjadi kesalahan'}`
                        );
                        $('#verif_rfid_scan').prop('disabled', false).val('').focus();
                    });
                }
            });

            // Submit Verifikasi Event
            $('#btnSimpanVerifikasi').click(function() {
                let btn = $(this);
                let originalHtml = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="spinner-border spinner-border-sm me-1"></i> Menyimpan...');

                let payload = [{
                    idCard: $('#verif_hidden_idcard').val(),
                    lokerId: $('#verif_hidden_lokerid').val(),
                    kodeRak: $('#verif_hidden_koderak').val(),
                    noLoker: $('#verif_hidden_noloker').val(),
                    nik: $('#verif_target_nik').val(),
                    nama: $('#verif_nama').val(),
                    jk: $('#verif_hidden_jk').val(),
                    divisi: $('#verif_hidden_divisi').val(),
                    staff: $('#verif_kategori').val() === 'Staff' ? 'staff' : 'non_staff'
                }];

                $.post("{{ url('/hr-connect/dept-ga/karyawan-masuk/updateStatus') }}", {
                    _token: "{{ csrf_token() }}",
                    data: payload
                }).done(function(res) {
                    $('#modalVerifikasi').modal('hide');
                    Toastify({
                        text: payload[0].lokerId ?
                            "Fasilitas loker berhasil dialokasikan!" :
                            "Karyawan tercatat tanpa fasilitas loker.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#0ab39c"
                    }).showToast();
                    $('#tableAjax').DataTable().ajax.reload(null, false);
                }).fail(function(xhr) {
                    Swal.fire("Gagal", xhr.responseJSON?.message || "Terjadi kesalahan server.",
                        "error");
                    btn.prop('disabled', false).html(originalHtml);
                });
            });

            // Export Excel
            $('#btnExportExcel').click(function(e) {
                e.preventDefault();
                if (state.showAll === 0 && !state.defaultTanggal) return Swal.fire('Peringatan',
                    'Silakan pilih tanggal!', 'warning');

                let btn = $(this);
                let originalText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="spinner-border spinner-border-sm me-1"></i> Mengunduh...');

                let form = $('<form>', {
                    method: 'POST',
                    action: `{{ url('/hr-connect/dept-ga/karyawan-masuk/export') }}`
                });
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: "{{ csrf_token() }}"
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'tanggal',
                    value: state.defaultTanggal
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'tampilkan_semua',
                    value: state.showAll
                }));

                $('body').append(form);
                form.submit();
                form.remove();

                setTimeout(() => btn.prop('disabled', false).html(originalText), 1500);
            });
        });
    </script>
@endpush
