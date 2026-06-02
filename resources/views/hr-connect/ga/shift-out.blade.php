@extends('hr-connect.layouts.base')

@push('styles')
    <style>
        .checklist {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        .form-check-input.checklist:checked {
            background-color: #0ab39c !important;
            border-color: #0ab39c !important;
        }

        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        #tableAjax tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- <div class="row mb-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label font-weight-bold text-muted">Filter Tanggal Checkout (Admin)</label>
                <select class="form-select form-control shadow-sm" id="tanggalFilter">
                    <option value="" disabled selected>Pilih Tanggal</option>
                    @if ($tanggalTersedia->isEmpty())
                        <option value="" disabled>Belum ada daftar karyawan keluar</option>
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
                    <div class="card-header border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <h5 class="card-title mb-0" style="font-weight: 600;">
                                    <i class="ri-logout-box-r-line text-warning me-2"></i> Clearance Fasilitas Loker
                                </h5>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">
                                    Daftar karyawan yang telah di-checkout oleh Admin. Segera lakukan penarikan loker fisik
                                    (Clearance).
                                </p>
                            </div>
                            <div class="col-lg-7 text-end mt-3 mt-lg-0">
                                <button class="btn btn-sm btn-soft-secondary font-weight-bolder shadow-sm me-2"
                                    id="btnExportExcel" data-bs-toggle="tooltip" title="Unduh data ke format Excel">
                                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Data
                                </button>

                                <button class="btn btn-sm btn-warning font-weight-bolder shadow-sm me-2"
                                    id="btnToggleMassal" data-bs-toggle="tooltip" title="Aktifkan mode cabut loker massal">
                                    <i class="ri-checkbox-multiple-line align-bottom me-1"></i> Mode Pilih Massal
                                </button>

                                <button class="btn btn-sm btn-success font-weight-bolder shadow-sm" id="btnSubmit"
                                    style="display: none;">
                                    <i class="ri-key-2-line align-bottom me-1"></i> Proses Cabut Massal (<span
                                        id="countChecked">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 25%;">Nama Lengkap</th>
                                        <th style="width: 10%;">NIK</th>
                                        <th style="width: 10%; text-align: center;">Kategori</th>
                                        <th style="width: 15%; text-align: center;">Nomor Loker</th>
                                        <th style="width: 10%;">Divisi</th>
                                        <th style="width: 10%;">Bagian</th>
                                        <th style="width: 20%; text-align: center;" id="headerTindakan">Tindakan</th>
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
            // ==========================================
            // 1. GLOBAL VARIABLES & STATE
            // ==========================================
            let state = {
                showAll: 1,
                defaultTanggal: '',
                isBulkMode: false
            };

            // ==========================================
            // 2. DATATABLES INITIALIZATION
            // ==========================================
            let table = $("#tableAjax").DataTable({
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
                        d.tanggal = state.defaultTanggal;
                        d.tampilkan_semua = state.showAll;
                    }
                },
                columns: [{
                        data: 'nama',
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'nik'
                    },
                    {
                        data: 'checkStaff',
                        className: 'text-center',
                        render: data => data === 'Y' ? '<span class="badge bg-secondary">Staff</span>' :
                            '<span class="badge bg-warning">Non Staff</span>'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.penghuni) {
                                let rak = (row.penghuni.kode_rak == 'LP') ? 'P' : 'W';
                                return `<span class="badge bg-light text-dark border shadow-sm px-2 py-1"><i class="ri-archive-line me-1"></i> Rak ${rak} - ${row.penghuni.no_loker}</span>`;
                            }
                            return `<span class="text-muted fst-italic" style="font-size: 0.85rem;"><i class="ri-forbid-line me-1"></i> Tidak Ada Loker</span>`;
                        }
                    },
                    {
                        data: 'kode_divisi',
                        render: data => data ? data : '-'
                    },
                    {
                        data: 'kode_bagian',
                        render: data => data ? data : '-'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input checklist d-none shadow-sm" value="${row.id}" data-nik="${row.nik}">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapusSatuan fw-bold shadow-sm w-100"
                                        data-id="${row.id}" data-nama="${row.nama}" data-nik="${row.nik}" title="Cabut loker untuk NIK ${row.nik}">
                                        <i class="ri-key-2-line align-bottom me-1"></i> Cabut Loker
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            table.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Mencegah status nyangkut pas pindah page Datatables
                if (state.isBulkMode) {
                    $('#headerTindakan').html(
                        `<input type="checkbox" id="selectAll" class="form-check-input shadow-sm" style="cursor: pointer;" title="Pilih Semua di Halaman Ini">`
                        );
                    $('.btn-hapusSatuan').addClass('d-none');
                    $('.checklist').removeClass('d-none');
                } else {
                    $('#headerTindakan').text('Tindakan');
                    $('.btn-hapusSatuan').removeClass('d-none');
                    $('.checklist').addClass('d-none').prop('checked', false);
                }

                // Reset Hitungan
                $("#btnSubmit").hide();
                $("#countChecked").text(0);
            });

            // ==========================================
            // 3. EVENT LISTENERS
            // ==========================================
            $('#tanggalFilter').on('change', function() {
                state.defaultTanggal = $(this).val();
                state.showAll = 0;
                table.draw();
            });

            $('#btnResetFilter').on('click', function() {
                $('#tanggalFilter').val('');
                state.defaultTanggal = '';
                state.showAll = 1;
                table.draw();
            });

            // Toggle Bulk Mode
            $('#btnToggleMassal').on('click', function() {
                state.isBulkMode = !state.isBulkMode;
                $(this).tooltip('hide');

                if (state.isBulkMode) {
                    $(this).removeClass('btn-warning').addClass('btn-danger').html(
                        '<i class="ri-close-line align-bottom me-1"></i> Batal Massal');
                    table.draw(false); // Trigger draw buat re-render header & kolom
                } else {
                    $(this).removeClass('btn-danger').addClass('btn-warning').html(
                        '<i class="ri-checkbox-multiple-line align-bottom me-1"></i> Mode Pilih Massal');
                    table.draw(false);
                }
            });

            // Select All Checkbox (Khusus per halaman Datatables)
            $(document).on("change", "#selectAll", function() {
                let isChecked = $(this).prop("checked");
                $(".checklist").prop("checked", isChecked);
                updateCheckedCount();
            });

            $(document).on("change", ".checklist", function() {
                updateCheckedCount();

                // Jika salah satu uncheck, matiin SelectAll
                if (!$(this).prop("checked")) {
                    $("#selectAll").prop("checked", false);
                } else if ($(".checklist:checked").length === $(".checklist").length) {
                    $("#selectAll").prop("checked", true);
                }
            });

            function updateCheckedCount() {
                let total = $(".checklist:checked").length;
                $("#countChecked").text(total);
                total > 0 ? $("#btnSubmit").fadeIn() : $("#btnSubmit").fadeOut();
            }

            // ==========================================
            // 4. ACTION PROCESS: MASSAL & SATUAN
            // ==========================================
            function executeClearance(dataArray, isMassal = false) {
                let textPeringatan = isMassal ?
                    `Anda akan mencabut fasilitas loker untuk <b>${dataArray.length} karyawan terpilih</b>. Aksi ini akan mengosongkan aset loker secara permanen.` :
                    `<div class="text-start mt-2">Nama: <b>${dataArray[0].nama}</b><br>NIK: <b>${dataArray[0].nik}</b><br><br>Masukkan alasan clearance loker karyawan ini:</div>`;

                Swal.fire({
                    title: isMassal ? "Konfirmasi Cabut Massal" : "Konfirmasi Cabut Satuan",
                    html: textPeringatan,
                    input: isMassal ? null : 'text',
                    inputPlaceholder: isMassal ? null : 'Contoh: Resign, Habis Kontrak, dll...',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "<i class='ri-key-2-line me-1'></i> Proses Clearance",
                    cancelButtonText: "Batal",
                    reverseButtons: true,
                    inputValidator: (value) => {
                        if (!isMassal && !value) return 'Alasan pencabutan wajib diisi!';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Terapkan alasan jika satuan
                        if (!isMassal) dataArray[0].alasan = result.value;

                        $.ajax({
                            type: 'POST',
                            url: "{{ url('/hr-connect/dept-ga/karyawan-keluar/update') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                data: dataArray
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Memproses Data...',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                });
                            },
                            success: function(res) {
                                Swal.close();
                                Toastify({
                                    text: res.message ||
                                        "Clearance loker berhasil diproses!",
                                    duration: 4000,
                                    backgroundColor: "#0ab39c",
                                    gravity: "top",
                                    position: "right"
                                }).showToast();
                                table.draw(false);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal Memproses!', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan sistem internal.', 'error');
                            }
                        });
                    }
                });
            }

            // Submit Massal
            $(document).on("click", "#btnSubmit", function() {
                let payload = [];
                $(".checklist:checked").each(function() {
                    payload.push({
                        id_karyawan: $(this).val(),
                        nik: $(this).attr('data-nik'),
                        alasan: "Pencabutan Loker Massal (Clearance GA)"
                    });
                });
                if (payload.length > 0) executeClearance(payload, true);
            });

            // Submit Satuan
            $(document).on('click', '.btn-hapusSatuan', function() {
                let btn = $(this);
                let payload = [{
                    id_karyawan: btn.data('id'),
                    nik: btn.attr('data-nik'),
                    nama: btn.attr('data-nama'), // Disimpan sementara buat UI Alert aja
                    alasan: ""
                }];
                executeClearance(payload, false);
            });

            // ==========================================
            // 5. EXPORT EXCEL
            // ==========================================
            $('#btnExportExcel').click(function(e) {
                e.preventDefault();
                if (state.showAll === 0 && !state.defaultTanggal) return Swal.fire('Peringatan',
                    'Silakan pilih filter tanggal terlebih dahulu!', 'warning');

                let btn = $(this);
                let originalBtnText = btn.html();

                btn.prop('disabled', true).html(
                    '<i class="spinner-border spinner-border-sm me-1"></i> Mengunduh...');

                let hiddenForm = $('<form>', {
                    'method': 'POST',
                    'action': `{{ url('/hr-connect/dept-ga/karyawan-keluar/export') }}`
                });
                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': "{{ csrf_token() }}"
                }));
                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'tanggal',
                    'value': state.defaultTanggal
                }));

                // FIX BUG EXCEL: Harus kirim parameter tampilkan_semua, bukan show_all
                hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'tampilkan_semua',
                    'value': state.showAll
                }));

                $('body').append(hiddenForm);
                hiddenForm.submit();
                hiddenForm.remove();

                setTimeout(() => btn.prop('disabled', false).html(originalBtnText), 1500);
            });
        });
    </script>
@endpush
