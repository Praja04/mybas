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
        <!-- Bagian Filter -->
        {{-- <div class="row mb-3 align-items-end">
            <div class="col-lg-3">
                <label class="form-label font-weight-bold text-muted">Filter Tanggal Checkout</label>
                <select class="form-select form-control shadow-sm" id="tanggalFilter">
                    <option value="" disabled selected>Pilih Tanggal</option>
                    @if ($tanggalTersedia->isEmpty())
                        <option value="" disabled>Belum ada antrean karyawan keluar</option>
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
        </div> --}}

        <!-- Bagian Tabel & Aksi -->
        <div class="row">
            <div class="col-lg-12">
                @include('hr-connect.hrd.partials._table_karyawan_keluar')
            </div>
        </div>
    </div>

    <!-- Modal Upload Excel -->
    <div class="modal fade" id="modalData" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header p-4 bg-light">
                    <h5 class="modal-title fw-bold"><i class="ri-file-upload-line text-primary me-2"></i> Upload Finalisasi
                        Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <input type="file" class="form-control mb-3" id="fileUpload" accept=".xlsx, .xls">
                    <button class="btn btn-primary w-100 fw-bold" id="uploadExcel">
                        <i class="ri-upload-cloud-2-line align-bottom me-1"></i> Proses Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ketentuan Upload -->
    <div class="modal fade" id="modalKetentuanUpload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            @include('hr-connect.hrd.partials._modal_ketentuan_upload')
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

            // --- FILTER LOGIC ---
            $(document).on("change", "#tanggalFilter", function() {
                defaultTanggal = $(this).val();
                showAll = 0;
                $("#tableAjax").DataTable().draw();
            });

            window.tampilkanSemua = function() {
                $("#tanggalFilter").val("");
                defaultTanggal = '';
                showAll = 1;
                $("#tableAjax").DataTable().draw();
            };

            // --- MODALS ---
            window.uploadExcelModal = function() {
                $("#modalData").modal("show");
            }
            window.ketentuanUploadModal = function() {
                $("#modalKetentuanUpload").modal("show");
            }

            // --- DATATABLES INIT ---
            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-hrd/karyawan-keluar/getData') }}",
                    data: function(d) {
                        d.tanggal = defaultTanggal;
                        d.tampilkan_semua = showAll;
                    }
                },
                columns: [{
                        data: 'nama',
                        searchable: true,
                    },
                    {
                        data: 'nik',
                        searchable: true,
                        render: function(data) {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        data: 'kode_bagian',
                        searchable: true,
                    },
                    {
                        data: 'kode_group',
                        searchable: true,
                    },
                    {
                        data: 'tanggal_keluar',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            if (data && data !== '0000-00-00') {
                                return moment(data).format('DD MMM YYYY');
                            }
                            return `<span class="text-muted fst-italic" style="font-size:0.85rem;"><i class="ri-error-warning-line align-bottom"></i> Belum diset</span>`;
                        }
                    },
                    {
                        data: 'alasan_keluar',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            let text = data ? data : 'Tidak ada keterangan';
                            let badgeClass = data ? 'bg-light text-dark border' :
                                'bg-soft-danger text-danger border border-danger';
                            return `<span class="badge ${badgeClass}">${text}</span>`;
                        }
                    },
                    {
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input checklist d-none" value="${row.id}" data-nik="${row.nik}">

                                    <button type="button" class="btn btn-sm btn-outline-success fw-bold btn-validasiSatuan"
                                        data-id="${row.id}" data-nama="${row.nama}" data-nik="${row.nik}"
                                        data-bs-toggle="tooltip" title="Selesaikan dokumen NIK ${row.nik}">
                                        <i class="ri-check-double-line"></i> Finalisasi
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            $('#tableAjax').on('draw.dt', function() {
                if (isBulkMode) {
                    $('#headerTindakan').html(
                        `<input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;" data-bs-toggle="tooltip" title="Pilih Semua">`
                    );
                    $('.btn-validasiSatuan').addClass('d-none');
                    $('.checklist').removeClass('d-none');
                } else {
                    $('#headerTindakan').text('Tindakan');
                    $('.btn-validasiSatuan').removeClass('d-none');
                    $('.checklist').addClass('d-none');
                }
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // --- UX LOGIC: TOGGLE MASSAL ---
            $(document).on('click', '#btnToggleMassal', function() {
                isBulkMode = !isBulkMode;

                if (isBulkMode) {
                    $(this).removeClass('btn-warning').addClass('btn-danger').html(
                        '<i class="ri-close-line align-bottom me-1"></i> Batal Pilih Massal');
                    $('#headerTindakan').html(
                        `<input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;" data-bs-toggle="tooltip" title="Pilih Semua">`
                    );
                    $('.btn-validasiSatuan').addClass('d-none');
                    $('.checklist').removeClass('d-none');
                } else {
                    $(this).removeClass('btn-danger').addClass('btn-warning').html(
                        '<i class="ri-checkbox-multiple-line align-bottom me-1"></i> Pilih Massal');
                    $('#headerTindakan').text('Tindakan');
                    $('.checklist').addClass('d-none').prop('checked', false);
                    $('#selectAll').prop('checked', false);
                    $('.btn-validasiSatuan').removeClass('d-none');
                    $("#btnSubmit").fadeOut();
                    $("#countChecked").text(0);
                }
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // --- UX LOGIC: CHECKBOX ---
            function copyToClipboard(text) {
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("Copy");
                textArea.remove();
                Toastify({
                    text: "NIK Disalin: " + text,
                    duration: 2000,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "#0ab39c"
                }).showToast();
            }

            $(document).on("change", "#selectAll", function() {
                let isChecked = $(this).prop("checked");
                $(".checklist").prop("checked", isChecked);

                let totalChecked = $(".checklist:checked").length;
                $("#countChecked").text(totalChecked);
                totalChecked > 0 ? $("#btnSubmit").fadeIn() : $("#btnSubmit").fadeOut();
            });

            $(document).on("change", ".checklist", function() {
                let totalChecked = $(".checklist:checked").length;
                $("#countChecked").text(totalChecked);
                totalChecked > 0 ? $("#btnSubmit").fadeIn() : $("#btnSubmit").fadeOut();

                if (totalChecked === $(".checklist").length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }

                if (this.checked) {
                    let nik = $(this).data('nik');
                    if (nik) {
                        let splited = String(nik).split('-');
                        let nik_copied = splited.length > 1 ? splited[1] : splited[0];
                        if (nik_copied) copyToClipboard(nik_copied);
                    }
                }
            });

            // --- PROSES VALIDASI SATUAN ---
            $(document).on('click', '.btn-validasiSatuan', function() {
                let idKaryawan = $(this).data('id');
                let nama = $(this).data('nama');

                Swal.fire({
                    title: "Konfirmasi Finalisasi",
                    html: `Anda akan menyelesaikan proses Offboarding (Clearance Dokumen) untuk <b>${nama}</b>. Lanjutkan?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonText: "Batal",
                    confirmButtonText: "Ya, Finalisasi!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesValidasi([{
                            checklistId: idKaryawan,
                            status: 'check'
                        }]);
                    }
                });
            });

            // --- PROSES VALIDASI MASSAL ---
            $(document).on("click", "#btnSubmit", function() {
                let dataToSend = [];
                $(".checklist:checked").each(function() {
                    dataToSend.push({
                        checklistId: $(this).val(),
                        status: 'check'
                    });
                });

                if (dataToSend.length === 0) return;

                Swal.fire({
                    title: "Finalisasi Massal",
                    html: `Anda akan menyelesaikan proses Offboarding untuk <b>${dataToSend.length} karyawan</b> keluar. Pastikan urusan dokumen telah selesai. Apakah Anda yakin?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "Ya, Eksekusi!",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesValidasi(dataToSend);
                    }
                });
            });

            // Reusable AJAX Function
            function prosesValidasi(dataArray) {
                let btn = $("#btnSubmit");
                let originalHtml = btn.html();
                btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Memproses...').prop('disabled',
                    true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/dept-hrd/karyawan-keluar/update') }}",
                    data: {
                        data: dataArray,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Toastify({
                            text: "Data berhasil difinalisasi dan email terkirim!",
                            duration: 3000,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "#0ab39c",
                        }).showToast();

                        $('#tableAjax').DataTable().ajax.reload(null, false);

                        if (isBulkMode) $('#btnToggleMassal').trigger('click');
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat memproses data.', 'error');
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }

            // --- PROSES UPLOAD EXCEL ---
            $(document).on("click", "#uploadExcel", function() {
                let excelFile = $("#fileUpload")[0].files[0];
                if (!excelFile) return Swal.fire('Oops', 'Pilih file excel terlebih dahulu!', 'warning');

                let btn = $(this);
                let originalText = btn.html();
                let formData = new FormData();
                formData.append('excel_file', excelFile);
                formData.append('_token', "{{ csrf_token() }}");

                btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...').prop(
                    'disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/dept-hrd/karyawan-keluar/uploadExcel') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $("#modalData").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#tableAjax').DataTable().ajax.reload(null, false);
                            btn.html(originalText).prop('disabled', false);
                            $("#fileUpload").val('');
                        });
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message :
                            'Terjadi kesalahan sistem.';
                        Swal.fire('Gagal!', msg, 'error');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
