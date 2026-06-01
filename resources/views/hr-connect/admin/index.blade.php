@extends('hr-connect.layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <style>
        .checkwish:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* Tune up Select2 biar rapi di dalam tabel */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
            font-size: 0.85rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        /* Styling Table Header */
        .table-custom-header th {
            background-color: #f3f6f9 !important;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        /* Modal Custom Styling */
        .modal-content {
            border-radius: 16px;
            border: 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            background-color: #fafbfc;
            border-radius: 16px 16px 0 0;
        }

        /* Custom Tab Admin */
        .tab-admin .nav-link {
            background-color: #ffffff;
            color: #6c757d;
            border: 1px solid #e9ebec;
            transition: all 0.3s ease;
        }

        .tab-admin .nav-link:hover {
            border-color: #2166db;
            /* Efek hover warna hijau khas velzon */
            color: #2166db;
        }

        .tab-admin .nav-link.active {
            background-color: #2166db !important;
            /* Warna saat aktif */
            color: #ffffff !important;
            border-color: #2166db !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if (!$hrd_ir)
                <div class="col-lg-12">
                    <ul class="nav nav-pills nav-justified tab-admin mb-4 gap-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold shadow-sm rounded" data-bs-toggle="tab" href="#floting"
                                role="tab" style="padding: 14px; font-size: 0.95rem;">
                                <i class="ri-user-add-line align-bottom me-1 fs-5"></i> Proses Karyawan Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold shadow-sm rounded" data-bs-toggle="tab" href="#okb" role="tab"
                                style="padding: 14px; font-size: 0.95rem;">
                                <i class="ri-team-line align-bottom me-1 fs-5"></i> Proses Karyawan Aktif
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            <div class="col-lg-12">
                <div class="tab-content text-muted">
                    @include('hr-connect.admin.partials.table._plot_karyawan')

                    @include('hr-connect.admin.partials.table._checkout_karyawan')
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-xl fade" id="seeCart" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            @include('hr-connect.admin.partials.modal._karyawan_keluar')
        </div>
    </div>

    {{-- Modal Upload Masuk --}}
    <div class="modal fade" id="modalData" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            @include('hr-connect.admin.partials.modal._upload_karyawan_masuk')
        </div>
    </div>

    {{-- Modal Upload Keluar --}}
    <div class="modal fade" id="modalData2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            @include('hr-connect.admin.partials.modal._upload_karyawan_keluar')
        </div>
    </div>

    {{-- Modal Info Plotting --}}
    <div class="modal fade" id="ketentuanUploadPlotingModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            @include('hr-connect.admin.partials.modal._info_plot_karyawan')
        </div>
    </div>

    {{-- Modal Info Checkout --}}
    <div class="modal fade" id="ketentuanUploadCheckoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            @include('hr-connect.admin.partials.modal._info_checkout_karyawan')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/moment/locale/id.js') }}"></script>
    <script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>

    <script>
        const CART_KEY = "karyawan_aktif_cartContainer";

        window.getCart = function() {
            let rawCart = localStorage.getItem(CART_KEY);
            return rawCart ? JSON.parse(rawCart) : [];
        }

        window.setCart = function(cartData) {
            localStorage.setItem(CART_KEY, JSON.stringify(cartData));
        }

        // Modal Toggle
        window.uploadExcelModal = function() {
            $("#modalData").modal("show");
        }
        window.uploadExcelCheckoutModal = function() {
            $("#modalData2").modal("show");
        }
        window.ketentuanUploadPlotingModal = function() {
            $("#ketentuanUploadPlotingModal").modal("show");
        }
        window.ketentuanUploadCheckoutModal = function() {
            $("#ketentuanUploadCheckoutModal").modal("show");
        }

        $(document).ready(function() {
            // Set jumlah badge cart saat halaman pertama kali diload
            $('#cart-count').text(window.getCart().length);
        });
    </script>

    {{-- <script>
        var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

        $(document).ready(function() {
            // Logika Cart Header Select
            $('#pilihAlasanKeluar').change(function() {
                var selectedAlasanKeluar = $(this).val();
                if (selectedAlasanKeluar !== "") {
                    $('.alasanKeluar').val(selectedAlasanKeluar).change();
                    updateCartAlasanKeluar(selectedAlasanKeluar);
                }
            });

            $(document).on('change', '#pilihTanggalKeluar', function() {
                var selectedTanggalKeluar = $(this).val();
                if (selectedTanggalKeluar !== "") {
                    $('.tglKeluar').val(selectedTanggalKeluar).change();
                    updateCartTanggalKeluar(selectedTanggalKeluar);
                }
            });

            updateCartTable();

            // AJAX Upload Excel
            $(document).on("click", "#uploadExcel", function() {
                let excelFile = $("#fileUpload")[0].files[0];
                if (!excelFile) return alert("Pilih file dulu Bro!");

                let formData = new FormData();
                let btn = $(this);
                let originalText = btn.html();

                btn.html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengupload...'
                ).prop('disabled', true);
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
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Terjadi kesalahan.');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            $(document).on("click", "#uploadCheckoutExcel", function() {
                let excelFile = $("#fileUploadCheckout")[0].files[0];
                if (!excelFile) return alert("Pilih file dulu Bro!");

                let formData = new FormData();
                let btn = $(this);
                let originalText = btn.html();

                btn.html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengupload...'
                ).prop('disabled', true);
                formData.append('excel_file', excelFile);

                $.ajax({
                    type: "POST",
                    url: "/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanKeluar",
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
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Terjadi kesalahan.');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });

        // Modals Toggle
        function uploadExcelModal() {
            $("#modalData").modal("show");
        }

        function uploadExcelCheckoutModal() {
            $("#modalData2").modal("show");
        }

        function ketentuanUploadPlotingModal() {
            $("#ketentuanUploadPlotingModal").modal("show");
        }

        function ketentuanUploadCheckoutModal() {
            $("#ketentuanUploadCheckoutModal").modal("show");
        }

        // Start Floting Kode Group
        let table = $("#tableAjax").dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "GET",
                url: "/hr-connect/dept-adm/data-karyawan/getDataFloting"
            },
            data: null,
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
                },
            ]
        });

        table.on('draw.dt', function() {
            $('.js-example-basic-single').select2({
                width: '100%'
            });
        });

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
            let selectedValue = $(this).val();

            if (selectedValue) {
                checkwish.prop('checked', true);
                $("#btnSubmit").removeClass('d-none');
                row.addClass('table-success');
            } else {
                checkwish.prop('checked', false);
                row.removeClass('table-success');
                if ($(".checkwish:checked").length === 0) {
                    $("#btnSubmit").addClass('d-none');
                }
            }
        });

        // Submit Plotting
        $("#btnSubmit").click(function() {
            let dataToSend = [];
            let btn = $(this);
            let originalHtml = btn.html();

            $("#tableAjax tbody input[type=checkbox]:checked").each(function() {
                let row = $(this).closest('tr');
                let idCheckwish = row.find('.checkwish').data("check");
                let kodeGroup = row.find('.kodeGroup').val();
                let kodeAdmin = row.find('.kodeAdmin').val();
                let p_in = row.find('.statusProses').val();

                if (p_in == "IN" && (!kodeGroup || !kodeAdmin)) {
                    Swal.fire('Info', 'Kode Group dan Kode Admin wajib diisi untuk status IN!', 'warning');
                    return false; // Break loop
                }
                dataToSend.push({
                    idCheckwish: idCheckwish,
                    kodeGroup: kodeGroup,
                    kodeAdmin: kodeAdmin,
                    p_in: p_in
                });
            });

            if (dataToSend.length === 0) return;

            btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Menyimpan...').prop('disabled', true);

            $.ajax({
                url: "{{ url('/hr-connect/dept-adm/data-karyawan/setGroupCode') }}",
                type: "POST",
                data: {
                    data: dataToSend,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Toastify({
                        text: "Berhasil memberikan fasilitas grup!",
                        duration: 3000,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: "#0ab39c",
                    }).showToast();

                    table.api().draw(false);
                    table2.api().draw(false);
                    btn.addClass('d-none').html(originalHtml).prop('disabled', false);
                },
                error: function(xhr) {
                    alert('Gagal menyimpan data!');
                    btn.html(originalHtml).prop('disabled', false);
                }
            });
        });
        // End Floting Kode Group

        // Start Karyawan Aktif
        let table2 = $("#tableAjax2").dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "GET",
                url: "/hr-connect/dept-adm/data-karyawan/getDataOkb"
            },
            columns: [{
                    render: function(data, type, row) {
                        var cartContainer = JSON.parse(localStorage.getItem(
                            "karyawan_aktif_cartContainer")) || [];
                        var found = cartContainer.find(cart => cart.id == row.id);

                        if (found) {
                            return `<center><button class="btn btn-sm btn-danger rounded-circle shadow-sm" onClick="removeFromCart('${row.id}')" data-bs-toggle="tooltip" title="Hapus dari Cart"><i class="ri-shopping-cart-2-fill"></i></button></center>`;
                        }
                        return `<center><button class="btn btn-sm btn-soft-dark rounded-circle shadow-sm" onClick="addToCart('${row.id}', '${row.nik}', '${row.nama}', '${row.kode_bagian}','${row.kode_divisi}','${row.kode_bagian}','${row.kode_admin}')" data-bs-toggle="tooltip" title="Masukkan ke Cart"><i class="ri-shopping-cart-2-line"></i></button></center>`;
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
                    data: 'kode_admin',
                    render: function(data) {
                        return `<span class="badge bg-light text-dark border border-secondary">${data}</span>`;
                    }
                },
                {
                    data: 'kode_bagian'
                },
                {
                    data: 'kode_group'
                },
            ],
        });

        table2.on('draw.dt', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $("#btnCart").click(function() {
            $("#seeCart").modal("show");
        });
        $('#cart-count').text(cartContainer.length);

        function addToCart(id, nik, nama, dept, kode_divisi, kode_bagian, kode_admin) {
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            if (!cartContainer.find(cart => cart.id === id)) {
                cartContainer.push({
                    id: id,
                    nik: nik,
                    nama: nama,
                    dept: dept,
                    kode_divisi: kode_divisi,
                    kode_bagian: kode_bagian,
                    kode_admin: kode_admin,
                    alasan_keluar: '',
                    tanggal_keluar: ''
                });
            }
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
            $('#cart-count').text(cartContainer.length);
            table2.api().draw(false);
            updateCartTable();
        }

        function removeFromCart(id) {
            let cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            cartContainer = cartContainer.filter(cart => cart.id !== id);
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
            $('#cart-count').text(cartContainer.length);
            table2.api().draw(false);
            updateCartTable();
        }

        function updateReason(cartId, reason) {
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            cartContainer.forEach(c => {
                if (c.id === cartId) c.alasan_keluar = reason;
            });
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
        }

        function updateTglKeluar(cartId, tglKeluar) {
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            cartContainer.forEach(c => {
                if (c.id === cartId) c.tanggal_keluar = tglKeluar;
            });
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
        }

        function updateCartAlasanKeluar(alasan) {
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            cartContainer.forEach(c => c.alasan_keluar = alasan);
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
            updateCartTable();
        }

        function updateCartTanggalKeluar(tgl) {
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            cartContainer.forEach(c => c.tanggal_keluar = tgl);
            localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
            updateCartTable();
        }

        function updateCartTable() {
            $('#cart-table tbody').empty();
            var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

            if (cartContainer.length === 0) {
                $('#cart-table tbody').append(
                    `<tr><td colspan="6" class="text-center text-muted py-4"><i class="ri-shopping-cart-line fs-1 d-block mb-2"></i>Keranjang masih kosong</td></tr>`
                );
                return;
            }

            cartContainer.forEach(function(cart) {
                $('#cart-table tbody').append(`
                <tr>
                    <td class="fw-bold text-primary">${cart.nama}</td>
                    <td>${cart.nik}</td>
                    <td><span class="badge bg-light text-dark border">${cart.dept}</span></td>
                    <td>
                        <select class="form-select form-select-sm shadow-sm alasanKeluar" data-cart-id="${cart.id}" onChange="updateReason('${cart.id}', this.value)">
                            <option value="">-- Pilih --</option>
                            <option value="Resign" ${cart.alasan_keluar === 'Resign' ? 'selected' : ''}>Resign</option>
                            <option value="Habis Kontrak" ${cart.alasan_keluar === 'Habis Kontrak' ? 'selected' : ''}>Habis Kontrak</option>
                            <option value="Kabur" ${cart.alasan_keluar === 'Kabur' ? 'selected' : ''}>Kabur</option>
                            <option value="Cut Probation" ${cart.alasan_keluar === 'Cut Probation' ? 'selected' : ''}>Cut P</option>
                            <option value="PHK" ${cart.alasan_keluar === 'PHK' ? 'selected' : ''}>PHK</option>
                        </select>
                    </td>
                    <td>
                        <input type="date" class="form-control form-control-sm shadow-sm tglKeluar" data-cart-id="${cart.id}" onChange="updateTglKeluar('${cart.id}', this.value)" value="${cart.tanggal_keluar || ''}">
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-soft-danger rounded-circle removeCartId" onClick="removeFromCart('${cart.id}')"><i class="ri-delete-bin-line"></i></button>
                    </td>
                </tr>`);
            });
        }

        $("#btnCheckout").click(function() {
            let cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
            if (cartContainer.length === 0) return Swal.fire('Oops!', 'Keranjang masih kosong Bro!', 'warning');

            let isValid = true;
            cartContainer.forEach(c => {
                if (!c.alasan_keluar || !c.tanggal_keluar) isValid = false;
            });

            if (!isValid) return Swal.fire('Oops!',
                'Pastikan semua alasan dan tanggal keluar sudah terisi di dalam tabel!', 'warning');

            let btn = $(this);
            let originalBtn = btn.html();
            btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Memproses...').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/hr-connect/dept-adm/data-karyawan/checkout",
                data: {
                    data: cartContainer,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    localStorage.removeItem("karyawan_aktif_cartContainer");
                    $('#cart-count').text(0);
                    updateCartTable();
                    table2.api().draw(false);
                    $("#seeCart").modal("hide");

                    Swal.fire('Sukses!', 'Berhasil melakukan checkout karyawan.', 'success');
                    btn.html(originalBtn).prop('disabled', false);
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                    btn.html(originalBtn).prop('disabled', false);
                }
            });
        });
    </script> --}}
@endpush
