<div class="tab-pane {{ $hrd_ir ? 'active' : '' }}" id="okb" role="tabpanel">
    {{-- <div class="tab-pane" id="okb" role="tabpanel"> --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom p-4">
            <div class="row align-items-center gy-3">
                <div class="col-xl-5 col-lg-12">
                    <div class="d-flex align-items-center">
                        <!-- Tambahan flex-shrink-0 di sini -->
                        <div class="avatar-sm flex-shrink-0 me-3">
                            <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-4 shadow-sm">
                                <i class="ri-user-unfollow-line"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" style="font-weight: 600;">Data Karyawan Aktif</h5>
                            <p class="text-muted mb-0 fs-13">Pilih dan masukkan karyawan ke dalam cart untuk memulai
                                proses administrasi keluar</p>
                        </div>
                    </div>
                </div>

                <div
                    class="col-xl-7 col-lg-12 d-flex justify-content-xl-end justify-content-start align-items-center gap-2 flex-wrap">
                    {{-- @if (!$hrd_ir) --}}
                    <a href="{{ url('/hr-connect/dept-adm/template-keluar') }}"
                        class="btn btn-sm btn-soft-info fw-bold shadow-sm">
                        <i class="ri-download-line align-bottom me-1"></i> Template
                    </a>

                    <button class="btn btn-sm btn-soft-secondary fw-bold shadow-sm" id="btnInfoCheckout">
                        <i class="ri-information-line align-bottom me-1"></i> Info
                    </button>

                    <button class="btn btn-sm btn-warning fw-bold shadow-sm text-dark" id="btnUploadExcelCheckout">
                        <i class="ri-file-excel-2-line align-bottom me-1"></i> Upload Excel
                    </button>

                    <div class="vr align-middle d-none d-sm-block mx-1"></div>

                    <button type="button" class="btn btn-sm btn-dark shadow-sm fw-bold position-relative px-3"
                        id="btnCart">
                        <i class="ri-shopping-cart-2-line align-bottom me-1"></i> Lihat Cart
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="cart-count">0</span>
                    </button>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
        <div class="card-body pb-4">
            <div class="table-responsive">
                <table id="tableAjax2" class="table table-bordered table-hover align-middle table-custom-header"
                    style="width:100%">
                    <thead class="table-light text-muted">
                        <tr>
                            {{-- @if (!$hrd_ir) --}}
                            <th class="text-center" style="width: 8%;">Cart</th>
                            {{-- @endif --}}
                            <th style="width: 25%;">Nama Lengkap</th>
                            <th style="width: 15%;">NIK</th>
                            <th>Divisi / Dept</th>
                            <th>Kode Bagian</th>
                            <th>Kode Admin</th>
                            <th>Kode Group</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const masterReason = @json($alasanKeluar);

        $(document).ready(function() {
            let table2 = $("#tableAjax2").DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/getDataOkb') }}"
                },
                columns: [{
                        className: "text-center",
                        render: function(data, type, row) {
                            let cart = window.getCart();
                            if (cart.find(c => c.id == row.id)) {
                                return `<center><button class="btn btn-sm btn-danger rounded-circle shadow-sm btn-remove-item" data-id="${row.id}" data-bs-toggle="tooltip" title="Hapus dari Cart"><i class="ri-shopping-cart-2-fill"></i></button></center>`;
                            }
                            return `<center><button class="btn btn-sm btn-soft-dark rounded-circle shadow-sm btn-add-item" data-id="${row.id}" data-nik="${row.nik}" data-nama="${row.nama}" data-divisi="${row.kode_divisi}" data-bagian="${row.kode_bagian}" data-admin="${row.kode_admin}" data-bs-toggle="tooltip" title="Masukkan ke Cart"><i class="ri-shopping-cart-2-line"></i></button></center>`;
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
                        data: 'text_admin',
                        render: function(data, type, row) {
                            let adm = row.kode_admin ? row.kode_admin : '-';
                            return `<span class="badge bg-light text-dark border border-secondary">${adm}</span>`;
                        }
                    },
                    {
                        data: 'kode_group',
                        render: data => data ? data : '-'
                    },
                ]
            });

            table2.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $("#btnCart").click(function() {
                updateCartTable();
                $("#seeCart").modal("show");
            });

            $("#btnInfoCheckout").click(function() {
                if (typeof window.ketentuanUploadCheckoutModal === "function") {
                    window.ketentuanUploadCheckoutModal();
                }
            });

            $("#btnUploadExcelCheckout").click(function() {
                if (typeof window.uploadExcelCheckoutModal === "function") {
                    window.uploadExcelCheckoutModal();
                }
            });

            $(document).on('click', '.btn-add-item', function() {
                let cart = window.getCart();
                let id = $(this).data('id');

                if (!cart.find(c => c.id == id)) {
                    cart.push({
                        id: id,
                        nik: $(this).data('nik'),
                        nama: $(this).data('nama'),
                        dept: $(this).data('divisi'),
                        kode_bagian: $(this).data('bagian'),
                        kode_admin: $(this).data('admin'),
                        alasan_keluar: '',
                        tanggal_keluar: ''
                    });
                    window.setCart(cart);
                    $('#cart-count').text(cart.length);
                    table2.draw(false);
                }
            });

            $(document).on('click', '.btn-remove-item', function() {
                let id = $(this).data('id');
                let cart = window.getCart();
                let newCart = cart.filter(c => c.id != id);

                window.setCart(newCart);
                $('#cart-count').text(newCart.length);
                table2.draw(false);
                updateCartTable();
            });

            $(document).on('change', '#pilihAlasanKeluar', function() {
                let val = $(this).val();
                if (val) {
                    $('.alasanKeluar').val(val).trigger('change');
                    let cart = window.getCart();
                    cart.forEach(c => c.alasan_keluar = val);
                    window.setCart(cart);
                }
            });

            $(document).on('change', '#pilihTanggalKeluar', function() {
                let val = $(this).val();
                if (val) {
                    $('.tglKeluar').val(val).trigger('change');
                    let cart = window.getCart();
                    cart.forEach(c => c.tanggal_keluar = val);
                    window.setCart(cart);
                }
            });

            window.updateReason = function(cartId, reason) {
                let cart = window.getCart();
                cart.forEach(c => {
                    if (c.id == cartId) c.alasan_keluar = reason;
                });
                window.setCart(cart);
            };

            window.updateTglKeluar = function(cartId, tglKeluar) {
                let cart = window.getCart();
                cart.forEach(c => {
                    if (c.id == cartId) c.tanggal_keluar = tglKeluar;
                });
                window.setCart(cart);
            };

            function updateCartTable() {
                $('#cart-table tbody').empty();
                let cart = window.getCart();

                if (cart.length === 0) {
                    $('#cart-table tbody').append(
                        `<tr><td colspan="6" class="text-center text-muted py-4"><i class="ri-shopping-cart-line fs-1 d-block mb-2"></i>Keranjang kosong</td></tr>`
                    );
                    return;
                }

                cart.forEach(function(c) {
                    let opsiAlasan = `<option value="">-- Pilih --</option>`;

                    masterReason.forEach(function(master) {
                        let isSelected = (c.alasan_keluar === master.nama_reason) ? 'selected' : '';
                        opsiAlasan +=
                            `<option value="${master.nama_reason}" ${isSelected}>${master.nama_reason}</option>`;
                    });

                    $('#cart-table tbody').append(`
                        <tr>
                            <td class="fw-bold text-primary">${c.nama}</td>
                            <td>${c.nik}</td>
                            <td><span class="badge bg-light text-dark border">${c.dept}</span></td>
                            <td>
                                <select class="form-select form-select-sm shadow-sm alasanKeluar" onChange="updateReason('${c.id}', this.value)">
                                    ${opsiAlasan}
                                </select>
                            </td>
                            <td><input type="date" class="form-control form-control-sm shadow-sm tglKeluar" onChange="updateTglKeluar('${c.id}', this.value)" value="${c.tanggal_keluar || ''}"></td>
                            <td class="text-center"><button class="btn btn-sm btn-soft-danger rounded-circle btn-remove-item" data-id="${c.id}"><i class="ri-delete-bin-line"></i></button></td>
                        </tr>
                    `);
                });
            }

            $("#btnCheckout").click(function() {
                let cart = window.getCart();
                if (cart.length === 0) return Swal.fire('Oops!', 'Keranjang kosong!', 'warning');

                let isValid = true;
                cart.forEach(c => {
                    if (!c.alasan_keluar || !c.tanggal_keluar) isValid = false;
                });
                if (!isValid) return Swal.fire('Oops!', 'Lengkapi alasan & tanggal keluar!', 'warning');

                let btn = $(this);
                let originalBtn = btn.html();
                btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Memproses...').prop(
                    'disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/checkout') }}",
                    data: {
                        data: cart,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        localStorage.removeItem(CART_KEY);
                        $('#cart-count').text(0);
                        updateCartTable();
                        table2.draw(false);

                        if ($.fn.DataTable.isDataTable('#tableAjax')) {
                            $('#tableAjax').DataTable().draw(false);
                        }

                        $("#seeCart").modal("hide");
                        Swal.fire('Sukses!', 'Berhasil checkout karyawan.', 'success');
                        btn.html(originalBtn).prop('disabled', false);
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                        btn.html(originalBtn).prop('disabled', false);
                    }
                });
            });

            $(document).on("click", "#uploadCheckoutExcel", function() {
                let excelFile = $("#fileUploadCheckout")[0].files[0];
                if (!excelFile) return Swal.fire('Oops', 'Pilih file dulu!', 'warning');

                let btn = $(this);
                let originalText = btn.html();
                let formData = new FormData();

                btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...').prop(
                    'disabled', true);
                formData.append('excel_file', excelFile);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    type: "POST",
                    url: "{{ url('/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanKeluar') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $("#modalData2").modal("hide");
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.',
                            'error');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
