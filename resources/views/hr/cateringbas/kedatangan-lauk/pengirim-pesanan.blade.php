@extends('layouts.base')

@push('styles')
    <style>
        .form-check-input:disabled+.form-check-label {
            color: #000;
        }
    </style>
@endpush

@section('content')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/introjs.min.css" rel="stylesheet">
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">
            <div class="col-md-3">
                <h3 style="margin-bottom: 13px">Data Catering</h3>
                <div class="card border">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <td>{{ $data->id_transaksi }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ $data->tanggal }}</td>
                                </tr>
                                <tr>
                                    <th>Catering</th>
                                    <td>{{ $data->catering }}</td>
                                </tr>
                                <tr>
                                    <th>Shift</th>
                                    <td>{{ $data->shift }}</td>
                                </tr>
                                <tr>
                                    <th>Status Cek Kedatangan Pesanan</th>
                                    <td>
                                        @if ($data->status_cek_kedatangan === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            @if ($data->status_cek_kedatangan === 'menunggu approval')
                                                <span class="badge text-bg-success"
                                                    style="background-color: #d4ce13; color: white;">Menunggu
                                                    Approval</span>
                                            @else
                                                <span class="badge text-bg-danger"
                                                    style="background-color: #a80000; color: white;">Belum</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <th>Status Cek Kendaraan</th>
                                    <td>
                                        @if ($data->status_cek_kedatangan === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            <span class="badge text-bg-danger"
                                                style="background-color: #a80000; color: white;">Belum</span>
                                        @endif
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <button onClick="showModalCreateCatering()" type="button"
                    class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal"
                    data-bs-target="#modalCreateGroup">
                    <i class="mdi mdi-plus"></i>
                    Tambah Catering
                </button> --}}
            </div>
            <div class="col-lg-9">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">DATA JUMLAH KEDATANGAN LAUK
                                <span class="d-block text-muted pt-2 font-size-sm">Atur Jumlah Kedatangan</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a id="uploadJumlahPesanan" href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateNew()"><i class="fa fa-plus-square"></i> Upload Jumlah Pesanan
                                Catering</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="jumlahPesanan" class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th width="4%">no</th> --}}
                                    {{-- <th class="col-md-1">Id transaksi</th> --}}
                                    <th class="col-md-1">Kategori</th>
                                    {{-- <th class="col-md-1">Shift</th> --}}
                                    <th class="col-md-1">Menu Makanan Utama</th>
                                    <th class="col-md-1">Menu Makanan Lainnya</th>
                                    <th class="col-md-1">Jumlah Order Yang Datang</th>
                                    <th class="col-md-1">Jumlah Order BAS</th>
                                    <th class="col-md-1">Keterangan</th>
                                    <th width="8%"><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table kedatangan lauk --}}
                            </tbody>
                        </table>
                        <div class="button-group pt-4" style="display: flex; gap: 20px;">
                            <a href="{{ url('/update/kedatangan-catering/' . $data->id_transaksi) }}" id="submitPenilaian"
                                type="submit" style="z-index: 99;" class="btn btn-primary btn-lg"
                                onclick="return confirm('Apakah Anda Yakin Akan mengirim Data Kedatangan Lauk?');">
                                <i class="fa fa-paper-plane"></i>Kirim Kedatangan Catering
                            </a>
                            <a id="penilaianTerkirim" class="btn btn-success" style="display: none;">
                                <i class="fa fa-check"></i> Kedatangan catering Terkirim
                            </a>
                            <a href="/cateringbas/kedatangan-lauk" id="kembalikehome" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    {{-- modal create jumlah pesanan catering --}}
    <div class="modal fade" id="modalPesananCatering" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-pesanan" id="exampleModalLabel"><i class="fa fa-plus-square"></i> Upload Jumlah
                        Pesanan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadPesananCatering">
                        <input type="hidden" name="id_transaksi" value="{{ $data->id_transaksi }}">
                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-makanan">Gambar Catering</label>
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" name="foto" id="image" multiple data-max-file-size="3MB">
                                </div>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label style="display: none" class="col-3 col-form-label text-right" for="total-order-bas">total
                                order bas</label>
                            <div class="col-9">
                                <input type="hidden" value="{{ $totalAmount }}" name="tanggal"
                                    id="tanggal_upload_ecafesedap" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-staff">Kategori<span
                                    style="color: red;"> *</span></label></label>
                            <div class="col-9">
                                <select required name="kategori_staff" id="kategori-staff" class="form-control">
                                    <option value=""></option>
                                    <option value="staff">Staff</option>
                                    <option value="non-staff">Non Staff</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="kategori-shift" name="shift" value="{{ $shift }}">

                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-shift">Shift Karyawan</label>
                            <div class="col-9">
                                <select required name="shift" id="kategori-shift" class="form-control">
                                    <option value=""></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-makanan">Menu
                                Utama<span style="color: red;"> *</span></label>
                            <div class="col-9">
                                <button class="btn btn-primary btn-sm mb-3" id="btn-menu-utama">
                                    Tambah Menu Utama
                                </button>
                                <div id="form-menu-utama"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-menu">Menu Lainnya</label>
                            <div class="col-9">
                                <button class="btn btn-primary btn-sm" id="btn-menu-pendamping">
                                    Tambah Menu Lainnya
                                </button>
                                <div id="form-menu-pendamping"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label style="font-weight: bold" class="col-3 col-form-label text-right"
                                for="totalQtyUtama">Total Menu Utama</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="totalQtyUtama"
                                    placeholder="Jumlah Order Hasil dari perhitungan menu utama" disabled>
                            </div>
                        </div>

                        {{-- di komentar dulu --}}

                        <div class="form-group row">
                            <label style="font-weight: bold" class="col-3 col-form-label text-right"
                                for="totalQtyUtama">Total Order Bas</label>
                            <div class="col-9">
                                <input id="jumlah_qty_bas" type="text" name="jumlah_order_bas" required
                                    placeholder="Jumlah Order BAS hasil dari tanggal upload" class="form-control"
                                    readonly>
                            </div>
                        </div>


                        {{-- <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="keterangan">Keterangan</label>
                            <div class="col-9">
                                <select required name="keterangan" id="keterangan" class="form-control">
                                    <option value=""></option>
                                    <option value="sesuai">Sesuai</option>
                                    <option value="tidak sesuai">Tidak Sesuai</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="submitButton" type="button" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i>Tambah Jumlah Pesanan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- modal edit jumlah pesanan catering --}}
    <div class="modal fade" id="editmodalPesananCatering" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalSizeSm" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><i class="fa fa-plus-square"></i> Edit Jumlah Pesanan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPesananCatering">
                        <input type="hidden" name="edit_id" id="edit-id">
                        <input type="hidden" name="edit_id_transaksi" id="modalIdTransaksi">
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="edit-kategori-staff">Kategori</label>
                            <div class="col-8">
                                <select name="edit_kategori_staff" id="edit-kategori-staff" class="form-control"
                                    disabled>
                                    <option value="staff">Staff</option>
                                    <option value="non-staff">Non-Staff</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right">Menu Utama Baru</label>
                            <div class="col-9">
                                <button type="button" class="btn btn-primary btn-sm mb-3" id="btn-add-menu-utama">
                                    Tambah Menu Utama
                                </button>
                                <div id="form-add-menu-utama"></div>
                            </div>
                        </div>

                        <!-- Menu Utama Table -->
                        <table class="table table-bordered" id="menu-utama-table">
                            <thead>
                                <tr>
                                    <th colspan="3">Menu Utama</th>
                                </tr>
                                <tr>
                                    <th>Nama Menu</th>
                                    <th>Qty</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Menu Utama rows will be appended here -->
                            </tbody>
                        </table>

                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right">Total Menu Utama</label>
                            <div class="col-9">
                                <input name="jumlah_order_bas_utama" type="number" id="totalQtyBaru"
                                    class="form-control" disabled>
                            </div>
                        </div>

                        <!-- Tombol Tambah Menu Pendamping -->
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right">Menu Pendamping Baru</label>
                            <div class="col-9">
                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-menu-pendamping">
                                    Tambah Menu Pendamping
                                </button>
                                <div id="form-add-menu-pendamping"></div>
                            </div>
                        </div>

                        <!-- Menu Pendamping Table -->
                        <table class="table table-bordered" id="menu-pendamping-table">
                            <thead>
                                <tr>
                                    <th colspan="3">Menu Pendamping</th>
                                </tr>
                                <tr>
                                    <th>Nama Menu</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Menu Pendamping rows will be appended here -->
                            </tbody>
                        </table>

                        <div class="form-group row">
                            <div class="col-12 text-center"> <!-- Ubah ke col-12 dan tambahkan text-center -->
                                <button id="editSubmitButton" type="button" class="btn btn-primary btn-block">
                                    <i class="fa fa-paper-plane"></i> Edit Pesanan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal delete jumlah pesanan catering --}}
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/intro.min.js"></script>
    <script src="https://kit.fontawesome.com/e1f618f385.js" crossorigin="anonymous"></script>

    {{-- filepon --}}
    {{-- <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script> --}}
    <script>
        // $('#tanggal_upload_ecafesedap').on('change', function() {
        //     // Mendapatkan nilai tanggal yang dipilih
        //     var selectedDate = $(this).val();

        //     // Memanggil fungsi untuk melakukan pemanggilan API dengan tanggal yang dipilih
        //     callApiOnDateChange(selectedDate);
        // });

        // di komen dulu 
        // Fungsi untuk memanggil API dengan tanggal yang dipilih
        // function callApiOnDateChange(selectedDate) {
        //     // Gantilah URL_API dengan URL sebenarnya dari API yang ingin Anda panggil
        //     var apiUrl = '/cateringbas/get-sum-qty-ecafesedap/?tanggal=' + selectedDate;

        //     // Menggunakan AJAX untuk melakukan pemanggilan API
        //     $.ajax({
        //         url: apiUrl,
        //         type: 'GET',
        //         success: function(response) {
        //             if (response === 0) {
        //                 Swal.fire(
        //                     'Data Tidak Ditemukan',
        //                     'Tidak ada data pada tanggal yang dipilih.',
        //                     'error'
        //                 );
        //             } else {
        //                 Swal.fire({
        //                     title: 'Data Ditemukan',
        //                     html: 'Jumlah data upload pesanan: <span style="color: red;">' + response +
        //                         '</span>',
        //                     icon: 'success'
        //                 });
        //                 $('#jumlah_qty_bas').val(response);
        //             }
        //         },
        //         error: function(error) {
        //             // Handle kesalahan pemanggilan API sesuai kebutuhan Anda
        //             console.error('API Error:', error);
        //         }
        //     });

        // }
    </script>
    <script>
        let menuUtamaCount = 1;
        let menuPendampingCount = 1;

        // Fungsi untuk mengosongkan form menu utama
        function clearMenuUtama() {
            $('#form-menu-utama').empty();
            menuUtamaCount = 1;
        }

        // Fungsi untuk mengosongkan form menu pendamping
        function clearMenuPendamping() {
            $('#form-menu-pendamping').empty();
            menuPendampingCount = 1;
        }

        // Event handler untuk perubahan pada kategori staff
        $('#kategori-staff').on('change', function() {
            clearMenuUtama();
            clearMenuPendamping();
        });

        $('#btn-menu-utama').click(function() {
            var menuUtamaForm = `
        <label class="col-3 col-form-label" for="kategori-makanan"> Menu Utama ${menuUtamaCount}</label>
        <div class="row">
            <div class="col-6">
                <input name="nama_menu_utama[]" required placeholder="Nama Menu" class="form-control" type="text" value="">
            </div>
            <div class="col-4">
                <input name="qty_utama[]" required placeholder="Qty" class="form-control qty-utama-input" type="number" value="">
            </div>
            <div class="col-2">
                <button class="btn btn-danger btn-sm btn-delete-menu">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `;
            $('#form-menu-utama').append(menuUtamaForm);
            menuUtamaCount++;
            updateTotalQtyUtama();
        });

        $('#form-menu-utama').on('click', '.btn-delete-menu', function() {
            $(this).closest('.row').prev('label').remove();
            $(this).closest('.row').remove();
            menuUtamaCount--;
            updateTotalQtyUtama();
        });

        $('#form-menu-utama').on('input', '.qty-utama-input', function() {
            updateTotalQtyUtama();
        });

        function updateTotalQtyUtama() {
            var totalQtyUtama = 0;
            $('.qty-utama-input').each(function() {
                var qty = parseInt($(this).val()) || 0;
                totalQtyUtama += qty;
            });
            $('#totalQtyUtama').val(totalQtyUtama);
        }

        let pendampingCount = 1;

        $('#btn-menu-pendamping').click(function() {
            var menuPendampingForm = `
                <label class="col-6 col-form-label" for="kategori-makanan">Menu Lainnya ${pendampingCount}</label>
                <div class="row">
                    <div class="col-6">
                        <input name="nama_menu_pendamping[]" required placeholder="Nama Menu" class="form-control" type="text" value="">
                    </div>
                    <div class="col-4">
                        <input name="qty_pendamping[]" required placeholder="Qty" class="form-control" type="number" value="">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-danger btn-sm btn-delete-menu">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#form-menu-pendamping').append(menuPendampingForm);
            pendampingCount++;
        });

        // Menggunakan event delegation untuk menangani button delete pada form-menu-pendamping
        $('#form-menu-pendamping').on('click', '.btn-delete-menu', function() {
            $(this).closest('.row').prev('label').remove();
            $(this).closest('.row').remove();
            pendampingCount--;
        });
    </script>
    <script type="text/javascript">
        function showModalCreateNew() {
            $('#modal-title-pesanan').text('Upload Jumlah Pesanan Catering');
            $('#modalPesananCatering').modal('show');
        }

        // tambah lagi menu baru saat edit modal di jalankan 

        // shift
        // komen dulu <td>${menu.nama_menu}</td>
        function openEditModal(id) {
            $.ajax({
                url: '/cateringbas/edit-pesanan/' + id,
                method: "GET",
                success: function(response) {
                    $('#edit-kategori-staff').val(response.kategori_staff);
                    $('#edit-id').val(response.id);
                    $('#modalIdTransaksi').val(response.id_transaksi);

                    var menuUtamaTable = $('#menu-utama-table tbody');
                    var menuPendampingTable = $('#menu-pendamping-table tbody');

                    menuUtamaTable.empty();
                    menuPendampingTable.empty();

                    response.menu_utama.forEach(function(menu) {
                        var row = `
                    <tr id="menuRowUtama-${menu.id}">
                        <td>
                            <input type="hidden" name="menu_utama_id[]" value="${menu.id}" class="form-control">
                            <input type="text" name="menu_utama_nama[]" value="${menu.nama_menu}" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="menu_utama_qty[]" value="${menu.qty}" class="form-control">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="deleteMenuUtama(${menu.id})">Delete</button>
                        </td>
                    </tr>
                `;
                        menuUtamaTable.append(row);
                    });

                    response.menu_pendamping.forEach(function(menu) {
                        var row = `
                    <tr id="menuRowPendamping-${menu.id}">
                        <td>
                            <input type="hidden" name="menu_pendamping_id[]" value="${menu.id}" class="form-control">
                            <input type="text" name="menu_pendamping_nama[]" value="${menu.nama_menu}" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="menu_pendamping_qty[]" value="${menu.qty}" class="form-control">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="deleteMenuPendamping(${menu.id})">Delete</button>
                        </td>
                    </tr>
                `;
                        menuPendampingTable.append(row);
                    });

                    $('input[name="menu_utama_qty[]"]').on('input', function() {
                        updateTotalQty();
                    });

                    $('input[name="menu_utama_qty_baru[]"]').on('input', function() {
                        updateTotalQty();
                    });

                    // Call updateTotalQty initially
                    updateTotalQty();

                    $('#editmodalPesananCatering').modal('show');
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }

        $(document).on('input', 'input[name="menu_utama_qty[]"], input[name="menu_utama_qty_baru[]"]', function() {
            updateTotalQty();
        });

        // summary edit total qty
        function updateTotalQty() {
            let totalQty = 0;
            let totalQtyBaru = 0;

            $('input[name="menu_utama_qty[]"]').each(function() {
                totalQty += parseInt($(this).val()) || 0;
                console.log(totalQty);
            });

            $('input[name="menu_utama_qty_baru[]"]').each(function() {
                totalQtyBaru += parseInt($(this).val()) || 0;
                console.log(totalQtyBaru);
            });

            let total = totalQty + totalQtyBaru;
            $('#totalQtyBaru').val(total);
        }

        // delete menu utama
        function deleteMenuUtama(menuId) {
        $.ajax({
            url: '/cateringbas/delete-menu-utama/' + menuId,
            type: 'GET',
            success: function(response) {
                $('#menuRowUtama-' + menuId).remove();
                updateTotalQty(); 
            },
            error: function() {
                alert('Error deleting menu item.');
            }
        });
    }

        // delete menu pendamping 
        function deleteMenuPendamping(menuId) {
            $.ajax({
                url: '/cateringbas/delete-menu-pendamping/' + menuId,
                type: 'GET',
                success: function(response) {
                    $('#menuRowPendamping-' + menuId).remove();
                },
                error: function() {
                    alert('Error deleting menu item.');
                }
            });
        }

        // masih proses agar bisa create row 
        $(document).ready(function() {
            var menuUtamaCounter = 0;
            var menuPendampingCounter = 0;

            $(document).on('input', 'input[name="menu_utama_qty[]"], input[name="menu_utama_qty_baru[]"]',
                function() {
                    updateTotalQty();
                });

            $('#btn-add-menu-utama').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <input type="text" name="menu_utama_nama_baru[]" class="form-control" placeholder="Nama Menu Utama">
                        </td>
                        <td>
                            <input type="number" name="menu_utama_qty_baru[]" class="form-control" placeholder="Quantity">
                        </td>
                    </tr>
                `;
                $('#menu-utama-table tbody').append(newRow);
                menuUtamaCounter++;
                updateTotalQty();
            });

            $('#btn-add-menu-pendamping').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <input type="text" name="menu_pendamping_nama_baru[]" class="form-control" placeholder="Nama Menu Pendamping">
                        </td>
                        <td>
                            <input type="number" name="menu_pendamping_qty_baru[]" class="form-control" placeholder="Quantity">
                        </td>
                    </tr>
                `;
                $('#menu-pendamping-table tbody').append(newRow);
                menuPendampingCounter++;
            });
        });

        // edit pesanan
        $(document).ready(function() {
            $('#editSubmitButton').click(function() {
                var formData = new FormData($('#editPesananCatering')[0]);

                $.ajax({
                    url: "{{ route('cateringbas.edit.pesanan', ['id' => $data->id]) }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success === 1) {
                            var table = $('#jumlahPesanan').DataTable();
                            table.ajax.reload();
                            console.log(response.message);
                            $('#editmodalPesananCatering').modal('hide');
                            Swal.fire(
                                'Success!',
                                'Your order has been updated.',
                                'success'
                            );
                        } else {
                            console.error(response.message);
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        Swal.fire(
                            'Error!',
                            'An unexpected error occurred.',
                            'error'
                        );
                    }
                });
            });
        });

        // get pesanan
        $(document).ready(function() {

            var table = $('#jumlahPesanan').DataTable({
                paging: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '/cateringbas/kedatangan-lauk/get-all/' + encodeURIComponent(
                        '{{ $data->id_transaksi }}'),
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    // {
                    //     data: 'id'
                    // },
                    // {
                    //     data: 'id_transaksi'
                    // },
                    {
                        data: 'kategori_staff'
                    },
                    // {
                    //     data: 'shift'
                    // },
                    {
                        data: 'nama_menu_utama'
                    },
                    {
                        data: 'nama_menu_pendamping'
                    },
                    {
                        data: 'jumlah_order'
                    },

                    {
                        data: 'jumlah_order_bas'
                    },
                    {
                        data: 'keterangan',
                        render: function(data, type, row) {
                            if (data === 'tidak sesuai') {
                                return '<span class="badge badge-danger">tidak Sesuai</span>';
                            } else if (data === 'sesuai') {
                                return '<span class="badge badge-success">sesuai</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {

                        // hide dulu button edit
                        // <a title="Hapus" onClick="deletePesanan('${data}')" href="javascript:" class="btn btn-sm btn-danger text-white mx-2">
                        //     <i class="fa fa-trash"></i> Hapus
                        // </a>
                        // <a title="Edit" onClick="openEditModal('${data}', '${row.id_transaksi}', '${row.kategori_staff}', '${row.jumlah_order_bas}', '${row.jumlah_order}', '${row.keterangan}', '${row.nama_makanan}', '${row.shift}')" href="javascript:" class="btn btn-sm btn-warning text-white mx-1">
                        //      <i class="fa fa-edit"></i> Edit
                        //  </a>

                        // parameter shift di hide dulu
                        // , '${row.shift}'
                        data: 'id',
                        render: function(data, type, row) {
                            if ('{{ $data->status_cek_kedatangan }}' == 'belum') {
                                return `
                                <div style="display: flex; gap: 10px;">
                                    <a title="Hapus" onClick="deletePesanan('${data}')" href="javascript:" class="btn btn-sm btn-danger text-white mx-2">
                                        <i class="fa fa-trash"></i> Hapus
                                    </a>
                                        <a title="Edit" onClick="openEditModal('${data}')" href="javascript:" class="btn btn-sm btn-warning text-white mx-1">
                                    <i class="fa fa-edit"></i> Edit
                                    </a>
                                </div>
                                    `;
                            } else {
                                return `<p class="badge badge-success">Data Pesanan<br> Sudah Dikirim</p>`;
                            }
                        }
                    }
                ]
            });


            // Inisiasi VenoBox untuk gambar
            new VenoBox({
                selector: '.venobox',
                overlayClose: true,
            });
        });

        // delete pesanan
        function deletePesanan(id) {
            Swal.fire({
                title: 'Yakin mau dihapus?',
                text: "Data mungkin tidak bisa dikembalikan lagi setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Gak jadi',
                confirmButtonText: 'Hapus aja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus data dari server
                    $.ajax({
                        url: "{{ url('/cateringbas/pesanan/delete/') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            if (response.success === 1) {
                                var table = $('#jumlahPesanan').DataTable();
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Silahkan Lengkapi Data!.',
                            });
                        }
                    })
                }
            });
        }

        // tambah pesanan 
        $(document).ready(function() {
            $('#submitButton').click(function(e) {
                e.preventDefault();

                var validNamaMenuUtama = true;
                $('input[name="nama_menu_utama[]"]').each(function() {
                    if (!/^[a-zA-Z ]+$/.test($(this).val())) {
                        validNamaMenuUtama = false;
                        return false;
                    }
                });

                // Validate 'nama_menu_pendamping' inputs
                var validNamaMenuPendamping = true;
                $('input[name="nama_menu_pendamping[]"]').each(function() {
                    if (!/^[a-zA-Z ]+$/.test($(this).val())) {
                        validNamaMenuPendamping = false;
                        return false;
                    }
                });

                if (!validNamaMenuUtama || !validNamaMenuPendamping) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Nama menu harus diisi dengan huruf (alphabet).',
                    });
                    return;
                }

                var data = new FormData($('#uploadPesananCatering')[0]);

                $.ajax({
                    type: "POST",
                    url: '{{ route('cateringbas.tambah.cateringpesanan') }}',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var table = $('#jumlahPesanan').DataTable();

                        if (response.success === 1 || response.success === 2) {
                            table.ajax.reload();
                        }

                        if (response.success === 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(function() {
                                $('#modalPesananCatering').modal('hide');
                                table.ajax.reload();
                            });
                        } else if (response.success === 2) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: response.message,
                            }).then(function() {
                                $('#modalPesananCatering').modal('hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengirim permintaan.',
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->status_cek_kedatangan }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'menunggu approval') {
                document.getElementById("submitPenilaian").style.display = "none";
                document.getElementById("uploadJumlahPesanan").style.display = "none";
                document.getElementById("penilaianTerkirim").style.display = "inline-block";
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Ketika nilai select berubah
            $("#kategori-staff").change(function() {
                // Ambil nilai kategori_staff yang dipilih
                var selectedCategory = $(this).val();


                // Kirim permintaan ke API
                $.ajax({
                    url: "/cateringbas/get-sum-qty-ecafesedap",
                    method: "GET",
                    data: {
                        kategori_staff: selectedCategory,
                        tanggal: '{{ $tanggal_pengiriman }}',
                        shift: '{{ $data->shift }}'
                    },
                    success: function(response) {
                        // Tindakan yang ingin Anda lakukan dengan respons dari API
                        $('#jumlah_qty_bas').val(response)
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });

            });
        });
    </script>

    {{-- intro js --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->status_cek_kedatangan }}";
            console.log(statusCekKendaraan);
        
            if (statusCekKendaraan === 'belum') {
                introJs().setOptions({
                    steps: [{
                        intro: "Selamat datang! Yuk update informasi kedatangan katering."
                    },
                    {
                        element: document.querySelector('#uploadJumlahPesanan'),
                        intro: "Klik di sini untuk mengunggah jumlah pesanan katering yang tiba.",
                        position: 'bottom'
                    },
                    {
                        element: document.querySelector('#submitPenilaian'),
                        intro: "Setelah memperbarui, klik di sini untuk mengirimkan informasi kedatangan katering.",
                        position: 'bottom'
                    }],
                    'skipLabel': '<i class="fas fa-times" id="skipTutorial"></i>', 
                    'nextLabel': 'Lanjut', 
                    'prevLabel': 'Kembali', 
                    'doneLabel': 'Selesai',
                    exitOnOverlayClick: false,
                    showBullets: true,
                    showStepNumbers: true,
                    disableInteraction: true
                }).onbeforeexit(function() {
                    console.log('Tour is about to end');
                    return true; 
                }).start();

                var skipIcon = document.getElementById('skipTutorial');
                if (skipIcon) {
                    skipIcon.style.color = 'black'; 
                    skipIcon.style.fontWeight = 'bold'; 
                }
            }
        });
    </script>
        
@endpush
