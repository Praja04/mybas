<div class="tab-pane {{ !$hrd_ir ? 'active' : '' }}" id="floting" role="tabpanel">
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0" style="font-weight: 600;">
                <i class="ri-git-merge-line text-primary me-2"></i> Proses Plotting Karyawan
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ url('/hr-connect/dept-adm/template-masuk') }}"
                    class="btn btn-sm btn-soft-info fw-bold shadow-sm">
                    <i class="ri-download-line align-bottom me-1"></i> Template
                </a>
                <button class="btn btn-sm btn-soft-secondary fw-bold shadow-sm" id="btnInfoPloting">
                    <i class="ri-information-line align-bottom me-1"></i> Info
                </button>
                <button class="btn btn-sm btn-primary fw-bold shadow-sm" id="btnUploadExcelPloting">
                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Upload Excel
                </button>
            </div>
        </div>
        <div class="card-body pb-4">
            <div class="table-responsive">
                <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                    style="width:100%">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="text-center" style="width: 5%;">Pilih</th>
                            <th style="width: 20%;">Nama Lengkap</th>
                            <th style="width: 10%;">NIK</th>
                            <th>Dept</th>
                            <th>Kode Bagian</th>
                            <th style="width: 12%;">Proses</th>
                            <th style="width: 12%;">Kode Admin</th>
                            <th style="width: 12%;">Kode Group</th>
                            <th style="width: 10%;">Tgl Masuk</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-success fw-bold shadow-sm px-4 d-none" id="btnSubmitPlotting">
                    <i class="ri-save-3-line align-bottom me-1"></i> Simpan Plotting
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const listAdmin = @json($pkw_admin);
            const listGroup = @json($pkw_group);

            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/getDataFloting') }}"
                },
                columns: [{
                        className: "text-center",
                        render: function(data, type, row) {
                            let isChecked = (row.kode_group && row.kode_admin) ? 'checked' : '';
                            return `<div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input checkwish" type="checkbox" data-check="${row.id}" ${isChecked} disabled style="transform: scale(1.3); cursor: not-allowed;">
                                    </div>`;
                        }
                    },
                    {
                        data: 'nama',
                        render: data => `<span class="fw-bold">${data}</span>`
                    },
                    {
                        data: 'nik',
                        render: data => `<span class="fw-bold text-secondary">${data}</span>`
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
                        render: function(data, type, row) {
                            return `
                                <select class="form-select form-select-sm shadow-sm statusProses">
                                    <option value="IN">IN</option>
                                    <option value="NO-IN">NO-IN</option>
                                </select>`;
                        }
                    },
                    {
                        data: 'kode_admin',
                        render: function(data, type, row) {
                            let options = `<option value="">-- Pilih --</option>`;
                            listAdmin.forEach(function(admin) {
                                let selected = (row.kode_admin == admin.kode_admin) ?
                                    "selected" : "";
                                options +=
                                    `<option value="${admin.kode_admin}" ${selected}>${admin.kode_admin}</option>`;
                            });

                            return `<select class="js-example-basic-single kodeAdmin form-control">${options}</select>`;

                            // return `
                        //     <select class="js-example-basic-single kodeAdmin form-control">
                        //         <option value="">-- Pilih --</option>
                        //         @foreach ($pkw_admin as $admin)
                        //             <option value="{{ $admin->kode_admin }}">{{ $admin->kode_admin }}</option>
                        //         @endforeach
                        //     </select>`;
                        }
                    },
                    {
                        data: 'kode_group',
                        render: function(data, type, row) {
                            let options = `<option value="">-- Pilih --</option>`;

                            let dbGroup = data ? data.toString().trim().toUpperCase() : "";
                            let isMatchFound = false;

                            listGroup.forEach(function(group) {
                                // Ambil kode dan nama dari master pkw_group
                                let loopGroupKode = group.kode_group ? group.kode_group
                                    .toString().trim().toUpperCase() : "";
                                let loopGroupNama = group.nama_group ? group.nama_group
                                    .toString().trim().toUpperCase() : "";

                                let selected = "";

                                // LOGIKA PEMBERSIH DATA: Cek apakah cocok dengan KODE atau cocok dengan NAMA
                                if (dbGroup === loopGroupKode || dbGroup ===
                                    loopGroupNama) {
                                    selected = "selected";
                                    isMatchFound = true;
                                }

                                // Tampilkan di dropdown: "BAS01 - DEFAULT" biar makin informatif
                                options +=
                                    `<option value="${group.kode_group}" ${selected}>${group.kode_group}</option>`;
                            });

                            // JAGA-JAGA: Kalau datanya emang ngaco dan gak ada di Kode maupun Nama
                            if (dbGroup !== "" && !isMatchFound) {
                                options +=
                                    `<option value="${data}" selected class="text-danger">⚠️ ${data} (Tidak ada di Master)</option>`;
                            }

                            return `<select class="js-example-basic-single kodeGroup form-control">${options}</select>`;

                            // return `
                        //     <select class="js-example-basic-single kodeGroup form-control">
                        //         <option value="">-- Pilih --</option>
                        //         @foreach ($pkw_group as $group)
                        //             <option value="{{ $group->kode_group }}">{{ $group->kode_group }}</option>
                        //         @endforeach
                        //     </select>`;
                        }
                    },
                    {
                        data: 'tanggal_masuk',
                        render: data => data ? moment(data).format('DD MMM YYYY') : '-'
                    }
                ]
            });

            table.on('draw.dt', function() {
                $('.js-example-basic-single').each(function() {
                    if ($(this).data('select2')) $(this).select2('destroy');
                });
                $('.js-example-basic-single').select2({
                    width: '100%'
                });

                $('#tableAjax tbody tr').each(function() {
                    let checkwish = $(this).find('.checkwish');
                    if (checkwish.is(":checked")) {
                        $(this).addClass('table-success');
                    }
                });

                toggleSubmitButton();
            });

            $("#btnInfoPloting").click(function() {
                if (typeof window.ketentuanUploadPlotingModal === "function") {
                    window.ketentuanUploadPlotingModal();
                }
            });

            $("#btnUploadExcelPloting").click(function() {
                if (typeof window.uploadExcelModal === "function") {
                    window.uploadExcelModal();
                }
            });

            // 1. EVENT STATUS PROSES (IN / NO-IN)
            $(document).on("change", ".statusProses", function() {
                let row = $(this).closest("tr");
                let statusProses = $(this).val();
                let checkwish = row.find(".checkwish");
                let kodeGroup = row.find('.kodeGroup');
                let kodeAdmin = row.find('.kodeAdmin');

                if (statusProses === "NO-IN") {
                    // Trigger change dikerjakan duluan biar gak nabrak centangan
                    kodeGroup.prop("disabled", true).val('').trigger('change');
                    kodeAdmin.prop("disabled", true).val('').trigger('change');

                    // BARU kita paksa centang
                    checkwish.prop("checked", true);
                    row.removeClass('table-success').addClass('table-danger');
                } else {
                    // Kalau balik lagi ke IN
                    kodeGroup.prop("disabled", false);
                    kodeAdmin.prop("disabled", false);
                    row.removeClass('table-danger');

                    if (kodeGroup.val()) {
                        checkwish.prop("checked", true);
                        row.addClass('table-success');
                    } else {
                        checkwish.prop("checked", false);
                    }
                }
                toggleSubmitButton();
            });

            // 2. EVENT KODE GROUP
            $(document).on('change', '.kodeGroup', function() {
                let row = $(this).closest('tr');
                let checkwish = row.find('.checkwish');
                let statusProses = row.find('.statusProses').val();

                // FIX BUG: PENGAMAN JALUR VIP!
                // Kalau statusnya NO-IN, cuekin aja event ini, jangan hapus centangnya!
                if (statusProses === "NO-IN") {
                    return;
                }

                if ($(this).val()) {
                    checkwish.prop('checked', true);
                    row.addClass('table-success');
                } else {
                    checkwish.prop('checked', false);
                    row.removeClass('table-success');
                }
                toggleSubmitButton();
            });

            function toggleSubmitButton() {
                if ($(".checkwish:checked").length > 0) {
                    $("#btnSubmitPlotting").removeClass('d-none');
                } else {
                    $("#btnSubmitPlotting").addClass('d-none');
                }
            }

            $("#btnSubmitPlotting").click(function() {
                let dataToSend = [];
                let btn = $(this);
                let originalHtml = btn.html();
                let isValid = true;

                $("#tableAjax tbody input[type=checkbox]:checked").each(function() {
                    let row = $(this).closest('tr');
                    let idCheckwish = row.find('.checkwish').data("check");
                    let kodeGroup = row.find('.kodeGroup').val();
                    let kodeAdmin = row.find('.kodeAdmin').val();
                    let p_in = row.find('.statusProses').val();

                    if (p_in === "IN" && (!kodeGroup || !kodeAdmin)) {
                        Swal.fire('Info', 'Kode Group dan Kode Admin wajib diisi untuk status IN!',
                            'warning');
                        isValid = false;
                        return false;
                    }
                    dataToSend.push({
                        idCheckwish,
                        kodeGroup,
                        kodeAdmin,
                        p_in
                    });
                });

                if (!isValid || dataToSend.length === 0) return;

                btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Menyimpan...').prop(
                    'disabled', true);

                $.ajax({
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/setGroupCode') }}",
                    type: "POST",
                    data: {
                        data: dataToSend,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        Toastify({
                            text: "Berhasil ploting karyawan!",
                            duration: 3000,
                            gravity: "top",
                            position: 'right',
                            backgroundColor: "#0ab39c"
                        }).showToast();

                        table.draw(false);

                        if ($.fn.DataTable.isDataTable('#tableAjax2')) {
                            $('#tableAjax2').DataTable().draw(false);
                        }

                        btn.addClass('d-none').html(originalHtml).prop('disabled', false);
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Gagal menyimpan data plotting!', 'error');
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            $(document).on("click", "#uploadExcel", function() {
                let excelFile = $("#fileUpload")[0].files[0];
                if (!excelFile) return Swal.fire('Oops', 'Pilih file terlebih dahulu!', 'warning');

                let btn = $(this);
                let originalText = btn.html();
                let formData = new FormData();

                btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...').prop(
                    'disabled', true);

                formData.append('excel_file', excelFile);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanMasuk') }}",
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
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.message ||
                            'Terjadi kesalahan sistem.', 'error');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
