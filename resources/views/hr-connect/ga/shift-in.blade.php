@extends('hr-connect.layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .copy-nik:hover {
            color: #007bff;
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label font-weight-bold text-muted">Filter Tanggal</label>
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
                <button class="btn btn-soft-secondary w-100 shadow-sm" onclick="tampilkanSemua()">
                    <i class="ri-refresh-line align-bottom me-1"></i> Reset Filter
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h5 class="card-title mb-0" style="font-weight: 600; color: #495057;">
                                    <i class="ri-login-box-line text-success me-2"></i> Plotting Fasilitas Karyawan Masuk
                                </h5>
                            </div>
                            <div class="col-lg-4 text-end">
                                <button class="btn btn-secondary font-weight-bolder shadow-sm" id="btnExportExcel">
                                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Data
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle" style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 25%;">Nama Lengkap</th>
                                        <th style="width: 10%;">NIK</th>
                                        <th style="width: 10%;">Kategori</th>
                                        <th style="width: 20%;">Alokasi Nomor Loker</th>
                                        <th style="width: 5%;">Divisi</th>
                                        <th style="width: 15%;">Tanggal Masuk</th>
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

            let showAll = 1;
            let defaultTanggal = "";

            $(document).on("change", "#tanggalFilter", function() {
                defaultTanggal = $(this).val();
                showAll = 0;
                $("#tableAjax").DataTable().draw();
            });

            window.tampilkanSemua = function() {
                showAll = 1;
                defaultTanggal = '';
                $("#tanggalFilter").val("");
                $("#tableAjax").DataTable().draw();
            };

            function copyToClipboard(text) {
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("Copy");
                textArea.remove();

                Toastify({
                    text: "NIK telah disalin: " + text,
                    duration: 3000,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "#212529",
                }).showToast();
            }

            $(document).on('click', '.copy-nik', function() {
                let nik = $(this).attr('data-nik');
                if (nik) copyToClipboard(nik);
            });

            let table = $("#tableAjax").dataTable({
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
                        d.tanggal = defaultTanggal;
                        d.tampilkan_semua = showAll;
                    }
                },
                columns: [{
                        data: 'nama'
                    },
                    {
                        render: function(data, type, row) {
                            return `<span class="copy-nik fw-bold" style="cursor: pointer;" data-nik="${row.nik}" title="Klik untuk Copy NIK">${row.nik}</span>`;
                        }
                    },
                    {
                        render: function(data, type, row) {
                            return row.staff === 'Y' ?
                                '<span class="badge bg-primary" style="font-size: 0.85rem;">Staff</span>' :
                                '<span class="badge bg-secondary" style="font-size: 0.85rem;">Non Staff</span>';
                        }
                    },
                    {
                        render: function(data, type, row) {
                            let isReadOnly = row.penghuni ? 'disabled' : '';
                            let selectBox = `<select class="form-control lokerNo js-example-basic-single" ${isReadOnly}>
                                <option value="" disabled>Pilih Loker</option>`;

                            if (row.penghuni) {
                                selectBox +=
                                    `<option value="${row.penghuni.id}" selected data-nik="${row.nik}" data-nama="${row.nama}">${row.penghuni.kode_rak} - ${row.penghuni.no_loker}</option>`;
                            } else {
                                let kategori = row.staff === 'Y' ? 'staff' : 'non_staff';
                                let listLoker = row.jenis_kelamin === 'L' ? lokerPria : lokerWanita;
                                let rekomendasiLokerId = null;

                                listLoker.forEach(function(loker) {
                                    let bolehMasuk = false;

                                    if (kategori === 'staff' && loker.total_penghuni == 0) {
                                        bolehMasuk = true;
                                    } else if (kategori === 'non_staff' && loker
                                        .total_penghuni < loker.kapasitas && loker
                                        .kategori_tersedia !== 'staff') {
                                        bolehMasuk = true;
                                    }

                                    if (bolehMasuk) {
                                        if (rekomendasiLokerId === null) {
                                            rekomendasiLokerId = loker.id;
                                        }

                                        let terpilih = (loker.id === rekomendasiLokerId) ?
                                            'selected' : '';

                                        selectBox += `
                                        <option value="${loker.id}" ${terpilih}
                                        data-nik="${row.nik}"
                                        data-nama="${row.nama}"
                                        data-kode-rak="${loker.kode_rak}"
                                        data-no-loker="${loker.no_loker}"
                                        >${loker.kode_rak} - ${loker.no_loker} (Isi: ${loker.total_penghuni}/${loker.kapasitas})
                                        </option>`;
                                    }
                                });
                            }

                            selectBox += '</select>';
                            return selectBox;
                        }
                    },
                    {
                        data: 'kode_divisi'
                    },
                    {
                        render: function(data, type, row) {
                            return row.tanggal_masuk ? moment(row.tanggal_masuk).format(
                                'DD MMM YYYY') : '-';
                        }
                    },
                    {
                        render: function(data, type, row) {
                            if (row.penghuni) {
                                return `<center><span class="badge bg-success px-3 py-2 shadow-sm" style="font-size: 0.85rem;"><i class="ri-check-double-line align-bottom me-1"></i> Tervalidasi</span></center>`;
                            } else {
                                return `
                                <center>
                                    <button type="button" class="btn btn-sm btn-outline-success fw-bold btn-verifikasi shadow-sm" data-idcard="${row.id}" data-rfid="${row.cardnodevice}" style="font-size: 0.85rem;">
                                        <i class="ri-barcode-box-line align-bottom me-1"></i> Verifikasi
                                    </button>
                                </center>
                                `;
                            }
                        }
                    },
                ],
            });

            table.on('draw.dt', function() {
                $('.js-example-basic-single').select2({
                    minimumResultsForSearch: 10
                });
                $('.lokerNo').trigger('change');
            });

            $(document).on('click', '.btn-verifikasi', function() {
                let btn = $(this);
                let tr = btn.closest('tr');
                let rowData = $('#tableAjax').DataTable().row(tr).data();
                let selectedLokerOption = tr.find('.lokerNo option:selected');

                let lokerId = selectedLokerOption.val();
                if (!lokerId) {
                    Swal.fire('Peringatan', 'Silahkan pilih alokasi loker terlebih dahulu!', 'warning');
                    return;
                }

                $('#verif_target_nik').val(rowData.nik);
                $('#verif_nama').val(rowData.nama);

                let textKategori = rowData.staff === 'Y' ? 'Staff' : 'Non Staff';
                $('#verif_kategori').val(textKategori);
                $('#verif_loker_tujuan').val(
                    `${selectedLokerOption.data('kode-rak')} - ${selectedLokerOption.data('no-loker')}`);

                $('#verif_hidden_idcard').val(btn.data('idcard'));
                $('#verif_hidden_lokerid').val(selectedLokerOption.val());
                $('#verif_hidden_koderak').val(selectedLokerOption.data('kode-rak'));
                $('#verif_hidden_noloker').val(selectedLokerOption.data('no-loker'));
                $('#verif_hidden_divisi').val(rowData.kode_divisi);
                $('#verif_hidden_jk').val(rowData.jenis_kelamin);

                $('#verif_detail_container').hide();
                $('#verif_footer').hide();

                $('#verif_rfid_scan').val('').prop('disabled', false);
                $('#verif_foto_img').attr('src', "{{ asset('assets/media/users/default.jpg') }}");

                $('#verif_status_text')
                    .removeClass('bg-success bg-danger bg-warning')
                    .addClass('bg-secondary')
                    .html('<i class="ri-rfid-line mr-1"></i> Menunggu proses scan Kartu ID...');

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
                    $('#verif_status_text')
                        .removeClass('bg-secondary bg-success bg-danger')
                        .addClass('bg-warning')
                        .html(
                            '<i class="spinner-border spinner-border-sm mr-1"></i> Sedang memverifikasi data...'
                        );

                    $('#verif_detail_container').slideUp(200);
                    $('#verif_footer').slideUp(200);
                    $('#btnSimpanVerifikasi').prop('disabled', true);

                    $.ajax({
                        url: "{{ url('/loker/search-karyawan') }}/" + scannedRfid,
                        type: "GET"
                    }).then(response => {
                        if (!response.success) {
                            throw new Error(response.message ||
                                'Data karyawan tidak ditemukan di Database Pusat.');
                        }

                        if (String(response.data.nik) !== String(targetNik)) {
                            throw new Error(
                                `Kartu ID tidak sesuai! Ini milik: ${response.data.nama}`);
                        }

                        if (response.data.foto) {
                            $('#verif_foto_img').attr('src', response.data.foto);
                        }

                        $('#verif_status_text')
                            .removeClass('bg-warning bg-danger bg-secondary')
                            .addClass('bg-success')
                            .html(
                                '<i class="ri-checkbox-circle-line mr-1 text-white"></i> Verifikasi berhasil! Silakan simpan.'
                            );

                        $('#btnSimpanVerifikasi').prop('disabled', false);

                        $('#verif_detail_container').slideDown(400);
                        $('#verif_footer').slideDown(400);

                    }).catch(error => {
                        let errorMsg = error.responseJSON?.message || error.message ||
                            'Terjadi kesalahan sistem.';

                        $('#verif_status_text')
                            .removeClass('bg-warning bg-success bg-secondary')
                            .addClass('bg-danger')
                            .html(
                                `<i class="ri-error-warning-line mr-1 text-white"></i> ${errorMsg}`
                            );

                        $('#verif_rfid_scan').prop('disabled', false).val('').focus();
                    });
                }
            });

            $('#btnSimpanVerifikasi').click(function() {
                let btn = $(this);

                let dataToSend = [{
                    idCard: $('#verif_hidden_idcard').val(),
                    lokerId: $('#verif_hidden_lokerid').val(),
                    kodeRak: $('#verif_hidden_koderak').val(),
                    noLoker: $('#verif_hidden_noloker').val(),
                    nik: $('#verif_target_nik').val(),
                    nama: $('#verif_nama').val(),
                    jk: $('#verif_hidden_jk').val(),
                    divisi: $('#verif_hidden_divisi').val(),
                    staff: $('#verif_kategori').val() === 'STAFF' ? 'staff' : 'non_staff'
                }];

                let originalBtnText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="spinner-border spinner-border-sm me-1"></i> Memproses...');

                $.ajax({
                    url: "{{ url('/hr-connect/dept-ga/karyawan-masuk/updateStatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        data: dataToSend
                    },
                    success: function(response) {
                        let lokerId = $('#verif_hidden_lokerid').val();
                        let jk = $('#verif_hidden_jk').val();
                        let staffValue = $('#verif_kategori').val();
                        let kategoriBaru = staffValue === 'Staff' ? 'staff' : 'non_staff';

                        let targetArray = (jk === 'L') ? lokerPria : lokerWanita;
                        let lokerIndex = targetArray.findIndex(l => l.id == lokerId);

                        if (lokerIndex !== -1) {
                            targetArray[lokerIndex].total_penghuni += 1;
                            if (!targetArray[lokerIndex].kategori_tersedia || targetArray[
                                    lokerIndex].kategori_tersedia == null) {
                                targetArray[lokerIndex].kategori_tersedia = kategoriBaru;
                            }
                        }

                        $('#modalVerifikasi').modal('hide');
                        Toastify({
                            text: "Verifikasi & plotting loker berhasil disimpan!",
                            duration: 4000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#0ab39c",
                        }).showToast();

                        $('#tableAjax').DataTable().draw(false);
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal", xhr.responseJSON?.message ||
                            "Terjadi kesalahan server saat memproses data.", "error");
                        btn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            $('#btnExportExcel').click(function(e) {
                e.preventDefault();

                if (showAll === 0 && (!defaultTanggal || defaultTanggal === '')) {
                    Swal.fire('Peringatan', 'Silahkan pilih tanggal filter terlebih dahulu!', 'warning');
                    return;
                }

                let btn = $(this);
                let originalBtnText = btn.html();
                btn.prop('disabled', true).html(
                    '<i class="spinner-border spinner-border-sm me-1"></i> Mengunduh...');

                let hiddenForm = $('<form>', {
                    'method': 'POST',
                    'action': `{{ url('/hr-connect/dept-ga/karyawan-masuk/export') }}`,
                });

                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': "{{ csrf_token() }}"
                }));
                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'tanggal',
                    'value': defaultTanggal
                }));
                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'tampilkan_semua',
                    'value': showAll
                }));

                $('body').append(hiddenForm);
                hiddenForm.submit();
                hiddenForm.remove();

                setTimeout(function() {
                    btn.prop('disabled', false).html(originalBtnText);
                }, 1500);
            });
        });
    </script>
@endpush
