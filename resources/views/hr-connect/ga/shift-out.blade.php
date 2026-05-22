@extends('hr-connect.layouts.base')

@push('styles')
    <style>
        .checklist {
            width: 1.3rem;
            height: 1.3rem;
            cursor: pointer;
        }

        .form-check-input.checklist:checked {
            background-color: #0ab39c !important;
            border-color: #0ab39c !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label font-weight-bold text-muted">Filter Tanggal Keluar</label>
                <select class="form-select form-control shadow-sm" id="tanggalFilter">
                    <option value="" disabled selected>Pilih Tanggal</option>
                    @if ($tanggalTersedia->isEmpty())
                        <option value="" disabled>Belum ada data karyawan keluar</option>
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
                    <div class="card-header border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h5 class="card-title mb-0" style="font-weight: 600;">
                                    <i class="ri-logout-box-r-line text-warning me-2"></i> Clearance Loker Karyawan
                                </h5>
                            </div>
                            <div class="col-lg-6 text-end">
                                <button class="btn btn-secondary font-weight-bolder shadow-sm me-2"
                                    id="btnExportExcel" data-bs-toggle="tooltip" data-bs-placement="top" title="Unduh data ke format Excel">
                                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Data
                                </button>

                                <button class="btn btn-warning font-weight-bolder shadow-sm me-2" id="btnToggleMassal" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktifkan mode cabut loker massal">
                                    <i class="ri-checkbox-multiple-line align-bottom me-1"></i> Pilih Massal
                                </button>

                                <button class="btn btn-soft-success font-weight-bolder shadow-sm" id="btnSubmit"
                                    style="display: none;">
                                    <i class="ri-key-2-line align-bottom me-1"></i> Proses Cabut Massal (<span
                                        id="countChecked">0</span>)
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
                                        <th style="width: 20%;">Nomor Loker</th>
                                        <th style="width: 5%;">Divisi</th>
                                        <th style="width: 8%;">Bagian</th>
                                        <th style="width: 22%; text-align: center;" id="headerTindakan">Tindakan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>

    <script>
        $(document).ready(function() {
            let showAll = 1;
            let defaultTanggal = '';
            let isBulkMode = false;

            $(document).on('change', '#tanggalFilter', function() {
                defaultTanggal = $(this).val();
                showAll = 0;
                $("#tableAjax").DataTable().draw();
            });

            window.tampilkanSemua = function() {
                $('#tanggalFilter').val('');
                defaultTanggal = '';
                showAll = 1;
                $("#tableAjax").DataTable().draw();
            };

            let table = $("#tableAjax").dataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-ga/karyawan-keluar/getData') }}",
                    data: function(d) {
                        d.tanggal = defaultTanggal;
                        d.tampilkan_semua = showAll;
                    }
                },
                columns: [{
                        data: 'nama'
                    },
                    {
                        data: 'nik'
                    },
                    {
                        render: function(data, type, row) {
                            return row.checkStaff === 'Y' ?
                                '<span class="badge bg-secondary" style="font-size: 0.85rem;">Staff</span>' :
                                '<span class="badge bg-warning" style="font-size: 0.85rem;">Non Staff</span>';
                        }
                    },
                    {
                        render: function(data, type, row) {
                            if (row.penghuni) {
                                let kodeRak = row.penghuni.kode_rak;
                                let rak = (kodeRak == 'LP') ? 'P' : 'W';

                                return `
                                <div class="text-center">
                                    <span class="badge bg-secondary shadow-sm px-2 py-1" style="font-size: 0.85rem;"><i class="ri-archive-line me-1"></i> ${rak} - ${row.penghuni.no_loker}</span>
                                </div>
                                `;
                            }
                            return `
                            <div class="text-center">
                                <span class="text-muted fst-italic" style="font-size: 0.85rem;"><i class="ri-forbid-line me-1"></i> Tanpa Fasilitas Loker</span>
                            </div>`;
                        }
                    },
                    {
                        data: 'kode_divisi'
                    },
                    {
                        data: 'kode_bagian'
                    },
                    {
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input checklist d-none"
                                        value="${row.id}"
                                        data-nik="${row.nik}">

                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapusSatuan fw-bold"
                                        data-id="${row.id}"
                                        data-nama="${row.nama}"
                                        data-nik="${row.nik}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Proses clearance untuk NIK ${row.nik}" style="font-size: 0.85rem;">
                                        <i class="ri-key-2-line"></i> Cabut Loker
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            $('#tableAjax').on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();

                if (isBulkMode) {
                    $('#headerTindakan').html(`
            <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;" data-bs-toggle="tooltip" title="Pilih Semua">
        `);
                    $('.btn-hapusSatuan').addClass('d-none');
                    $('.checklist').removeClass('d-none');
                } else {
                    $('#headerTindakan').text('Tindakan');
                    $('.btn-hapusSatuan').removeClass('d-none');
                    $('.checklist').addClass('d-none');
                }

                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $(document).on("change", "#selectAll", function() {
                let isChecked = $(this).prop("checked");

                if (isChecked && !isBulkMode) {
                    $('#btnToggleMassal').trigger('click');
                }

                $(".checklist").prop("checked", isChecked);

                let totalChecked = $(".checklist:checked").length;
                if (totalChecked > 0) {
                    $("#countChecked").text(totalChecked);
                    $("#btnSubmit").fadeIn();
                } else {
                    $("#btnSubmit").fadeOut();
                }
            })

            $(document).on('click', '#btnToggleMassal', function() {
                isBulkMode = !isBulkMode;

                if (isBulkMode) {
                    $(this).removeClass('btn-warning').addClass('btn-danger')
                        .html('<i class="ri-close-line align-bottom me-1"></i> Batal Pilih Massal');

                    $('#headerTindakan').html(`
            <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;" data-bs-toggle="tooltip" title="Pilih Semua">
        `);

                    $('.btn-hapusSatuan').addClass('d-none');
                    $('.checklist').removeClass('d-none');
                } else {
                    $(this).removeClass('btn-danger').addClass('btn-warning')
                        .html('<i class="ri-checkbox-multiple-line align-bottom me-1"></i> Pilih Massal');

                    $('#headerTindakan').text('Tindakan');

                    $('.checklist').addClass('d-none').prop('checked', false);
                    $('#selectAll').prop('checked', false);
                    $('.btn-hapusSatuan').removeClass('d-none');

                    $("#btnSubmit").fadeOut();
                    $("#countChecked").text(0);
                }

                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $(document).on("change", ".checklist", function() {
                let totalChecked = $(".checklist:checked").length;

                $("#countChecked").text(totalChecked);

                if (totalChecked > 0) {
                    $("#btnSubmit").fadeIn();
                } else {
                    $("#btnSubmit").fadeOut();
                    $('#selectAll').prop('checked', false);
                }
            });

            $(document).on("click", "#btnSubmit", function() {
                let dataToSend = [];

                $(".checklist:checked").each(function() {
                    dataToSend.push({
                        id_karyawan: $(this).val(),
                        nik: $(this).attr('data-nik'),
                        alasan: "Pencabutan Loker Massal"
                    });
                });

                if (dataToSend.length === 0) return;

                Swal.fire({
                    title: "Konfirmasi Clearance Massal",
                    html: `Anda akan mencabut fasilitas loker untuk <b>${dataToSend.length} karyawan terpilih</b>. Aksi ini akan mengosongkan aset loker secara permanen. Lanjutkan proses?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "<i class='ri-check-double-line me-1'></i> Ya, Eksekusi Semua",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/hr-connect/dept-ga/karyawan-keluar/update') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                data: dataToSend
                            },
                            success: function(response) {
                                Toastify({
                                    text: `Proses massal berhasil! Sebanyak ${dataToSend.length} fasilitas loker telah dicabut.`,
                                    duration: 4000,
                                    backgroundColor: "#0ab39c",
                                    gravity: "top",
                                    position: "right"
                                }).showToast();

                                $("#tableAjax").DataTable().draw(false);
                                $("#btnSubmit").fadeOut();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal Memproses!',
                                    'Terjadi kesalahan sistem saat memproses data clearance massal.',
                                    'error');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-hapusSatuan', function() {
                let btn = $(this);
                let idKaryawan = btn.data('id');
                let nik = btn.attr('data-nik');
                let nama = btn.attr('data-nama');

                let rowData = $('#tableAjax').DataTable().row(btn.closest('tr')).data();
                let hasLoker = rowData.penghuni != null;

                if (hasLoker) {
                    Swal.fire({
                        title: "Konfirmasi Cabut Loker",
                        html: `<div class="text-start mt-3">Nama: <b>${nama}</b><br>NIK: <b>${nik}</b><br>Loker: <b>${rowData.penghuni.kode_rak} - ${rowData.penghuni.no_loker}</b><br><br>Masukkan alasan clearance:</div>`,
                        input: 'text',
                        inputPlaceholder: 'Contoh: Resign, Habis Kontrak...',
                        showCancelButton: true,
                        confirmButtonColor: "#0ab39c",
                        confirmButtonText: "Proses Clearance",
                        cancelButtonText: "Batal",
                        reverseButtons: true,
                        inputValidator: (value) => {
                            if (!value) return 'Alasan pencabutan wajib diisi!'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            prosesClearance(idKaryawan, nik, result.value);
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Peringatan Fasilitas Loker",
                        html: `Nama: <b>${nama}</b><br>NIK: <b>${nik}</b><br><br>Karyawan ini tidak terdaftar memiliki fasilitas loker. Tetap lanjutkan proses clearance?`,
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#0ab39c",
                        confirmButtonText: "Ya, Lanjutkan",
                        cancelButtonText: "Batal",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            prosesClearance(idKaryawan, nik, 'Clearance Karyawan Tanpa Loker');
                        }
                    });
                }
            });

            function prosesClearance(idKaryawan, nik, alasan) {
                let dataToSend = [{
                    id_karyawan: idKaryawan,
                    nik: nik,
                    alasan: alasan
                }];

                $.ajax({
                    type: 'POST',
                    url: "{{ url('/hr-connect/dept-ga/karyawan-keluar/update') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        data: dataToSend
                    },
                    success: function() {
                        Toastify({
                            text: "Clearance data karyawan berhasil diproses!",
                            duration: 3000,
                            backgroundColor: "#0ab39c"
                        }).showToast();
                        $("#tableAjax").DataTable().draw(false);
                    },
                    error: function() {
                        Swal.fire('Gagal Memproses!', 'Terjadi kesalahan sistem internal.', 'error');
                    }
                });
            }

            $('#btnExportExcel').click(function(e) {
                e.preventDefault();

                let tanggal = $('#tanggalFilter').val() || '';
                let showAll = (tanggal === '') ? 1 : 0;

                let btn = $(this);
                let originalBtnText = btn.html();

                btn.prop('disabled', true)
                    .html('<i class="spinner-border spinner-border-sm me-1"></i> Mengunduh Dokumen...')

                let hiddenForm = $('<form>', {
                    'method': 'POST',
                    'action': `{{ url('/hr-connect/dept-ga/karyawan-keluar/export') }}`,
                });

                hiddenForm.append(
                    $('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': "{{ csrf_token() }}",
                    }),
                    $('<input>', {
                        'type': 'hidden',
                        'name': 'tanggal',
                        'value': tanggal,
                    }),
                    $('<input>', {
                        'type': 'hidden',
                        'name': 'show_all',
                        'value': showAll,
                    })
                );

                $('body').append(hiddenForm);
                hiddenForm.submit();
                hiddenForm.remove();

                setTimeout(() => {
                    btn.prop('disabled', false)
                        .html(originalBtnText);
                }, 1500);
            });
        });
    </script>
@endpush
