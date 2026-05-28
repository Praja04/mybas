<div class="tab-pane {{ !$hrd_ir ? 'active' : '' }}" id="floting" role="tabpanel">
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0" style="font-weight: 600;">
                <i class="ri-git-merge-line text-primary me-2"></i> Proses Plotting Karyawan
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('hr-connect.admin.template-masuk') }}" class="btn btn-sm btn-soft-info fw-bold">
                    <i class="ri-download-line align-bottom me-1"></i> Template
                </a>
                <button class="btn btn-sm btn-soft-secondary fw-bold" onClick="ketentuanUploadPlotingModal()">
                    <i class="ri-information-line align-bottom me-1"></i> Info
                </button>
                <button class="btn btn-sm btn-primary fw-bold shadow-sm" onClick="uploadExcelModal()">
                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Upload Excel
                </button>
            </div>
        </div>
        <div class="card-body pb-4">
            <div class="table-responsive">
                <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">Pilih</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Dept</th>
                            <th>Kode Bagian</th>
                            <th>Proses</th>
                            <th>Kode Admin</th>
                            <th>Kode Group</th>
                            <th>Tgl Masuk</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-success fw-bold shadow-sm px-4 d-none" id="btnSubmit">
                    <i class="ri-save-3-line align-bottom me-1"></i> Simpan Plotting
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // --- 1. INIT DATATABLES PLOTTING ---
            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "/hr-connect/dept-adm/data-karyawan/getDataFloting"
                },
                columns: [{
                        render: function(data, type, row) {
                            return `<center><div class="form-check d-flex justify-content-center"><input class="form-check-input checkwish" type="checkbox" data-check="${row.id}" disabled style="transform: scale(1.3);"></div></center>`;
                        }
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'nik',
                        render: function(data) {
                            return `<span class="fw-bold">${data}</span>`;
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
                        <select class="form-select form-select-sm shadow-sm statusProses">
                            <option value="IN">IN</option>
                            <option value="NO-IN">NO-IN</option>
                        </select>`;
                        }
                    },
                    {
                        render: function(data, type, row) {
                            return `
                    <select class="js-example-basic-single kodeAdmin form-control">
                        <option value="">-- Pilih --</option>
                        @foreach ($pkw_admin as $admin)
                        <option value="{{ $admin->kode_admin }}">{{ $admin->kode_admin }}</option>
                        @endforeach
                    </select>`;
                        }
                    },
                    {
                        render: function(data, type, row) {
                            return `
                    <select class="js-example-basic-single kodeGroup form-control">
                        <option value="">-- Pilih --</option>
                        @foreach ($pkw_group as $group)
                        <option value="{{ $group->kode_group }}">{{ $group->kode_group }}</option>
                        @endforeach
                    </select>`;
                        }
                    },
                    {
                        data: 'tanggal_masuk'
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
            });

            // --- 2. LOGIKA ROW PLOTTING ---
            $(document).on("change", ".statusProses", function() {
                let row = $(this).closest("tr");
                let statusProses = $(this).val();
                let checkwish = row.find(".checkwish");
                let kodeGroup = row.find('.kodeGroup');
                let kodeAdmin = row.find('.kodeAdmin');

                if (statusProses == "NO-IN") {
                    checkwish.prop("checked", true);
                    kodeGroup.prop("disabled", true).val('').trigger('change');
                    kodeAdmin.prop("disabled", true).val('').trigger('change');
                    row.addClass('table-danger');
                    $("#btnSubmit").removeClass('d-none');
                } else {
                    kodeGroup.prop("disabled", false);
                    kodeAdmin.prop("disabled", false);
                    row.removeClass('table-danger');
                    if (!kodeGroup.val()) checkwish.prop("checked", false);
                }
            });

            $(document).on('change', '.kodeGroup', function() {
                let row = $(this).closest('tr');
                let checkwish = row.find('.checkwish');

                if ($(this).val()) {
                    checkwish.prop('checked', true);
                    $("#btnSubmit").removeClass('d-none');
                    row.addClass('table-success');
                } else {
                    checkwish.prop('checked', false);
                    row.removeClass('table-success');
                    if ($(".checkwish:checked").length === 0) $("#btnSubmit").addClass('d-none');
                }
            });

            // --- 3. SUBMIT PLOTTING KE SERVER ---
            $("#btnSubmit").click(function() {
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

                    if (p_in == "IN" && (!kodeGroup || !kodeAdmin)) {
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
                        // Refresh tabel checkout jika ada
                        if ($.fn.DataTable.isDataTable('#tableAjax2')) $('#tableAjax2')
                            .DataTable().draw(false);
                        btn.addClass('d-none').html(originalHtml).prop('disabled', false);
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Gagal menyimpan data!', 'error');
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // --- 4. UPLOAD EXCEL PLOTTING ---
            $(document).on("click", "#uploadExcel", function() {
                let excelFile = $("#fileUpload")[0].files[0];
                if (!excelFile) return Swal.fire('Oops', 'Pilih file dulu Bro!', 'warning');

                let btn = $(this);
                let originalText = btn.html();
                let formData = new FormData();

                btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...').prop(
                    'disabled', true);
                formData.append('excel_file', excelFile);

                $.ajax({
                    type: "POST",
                    url: "/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanMasuk",
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
