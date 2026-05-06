@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">

    <style>
        :root {
            --bas-primary: #F59E0B;
            --bas-primary-dark: #D97706;
            --bas-primary-light: #FEF3C7;
            --bas-neutral: #6B7280;
            --bas-neutral-light: #F3F4F6;
            --bas-dark: #374151;
            --bas-border: #E5E7EB;
            --bas-surface: #FFFFFF;
            --bas-radius-md: 12px;
            --bas-radius-lg: 18px;
            --bas-transition: all 0.2s ease;
        }

        /* 1. PERBAIKAN STRUKTUR LAYER (Z-INDEX) */

        /* Navbar harus paling atas */
        #kt_header {
            z-index: 1100 !important;
        }

        /* Modal harus di bawah navbar tapi di atas popover */
        .modal {
            z-index: 1070 !important;
        }

        .modal-backdrop {
            z-index: 1060 !important;
        }

        /* Popover harus di bawah modal & navbar agar tidak nembus */
        .popover {
            z-index: 1040 !important;
        }

        /* 2. LAYOUT & HEADER */
        .content {
            background-color: #F9FAFB !important;
            padding-top: 20px;
            /* Kasih space dikit di bawah navbar */
        }

        .bas-header {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--bas-radius-lg);
            position: relative;
            z-index: 1;
            /* Cukup 1 agar tidak balapan sama navbar */
            margin-bottom: 25px;
        }

        .bas-header-icon {
            width: 56px;
            height: 56px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: var(--bas-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--bas-primary);
        }

        .bas-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 0;
        }

        .bas-header-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
        }

        /* 3. TABLE CARD STYLE */
        .bas-tab-card {
            background: var(--bas-surface) !important;
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        #table_group {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
        }

        #table_group thead th {
            background-color: var(--bas-neutral-light);
            color: var(--bas-neutral);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 20px 25px;
            border: none;
        }

        #table_group tbody td {
            padding: 18px 25px;
            vertical-align: middle;
            border-bottom: 1px solid var(--bas-border);
        }

        #table_group tbody tr:hover {
            background-color: #FFFBEB !important;
            transition: var(--bas-transition);
        }

        /* Area Pagination & Search */
        .dataTables_wrapper .row:last-child {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--bas-border);
            background-color: var(--bas-neutral-light);
        }

        .dataTables_filter,
        .dataTables_length {
            padding: 1.5rem;
        }

        /* 4. UTILITY & BUTTONS */
        .bas-btn-primary {
            background: var(--bas-primary);
            border: none;
            color: #FFFFFF;
            font-weight: 600;
            border-radius: var(--bas-radius-md);
            transition: var(--bas-transition);
            height: 45px;
        }

        .bas-btn-primary:hover {
            background: var(--bas-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: #FFFFFF;
        }

        /* 5. PERMISSION GRID & MODAL */
        .auth-permissions {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            width: 100%;
        }

        .permission-item {
            padding: 15px;
            border-radius: var(--bas-radius-md);
            border: 1.5px solid var(--bas-border);
            background-color: var(--bas-surface);
            transition: var(--bas-transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            position: relative;
        }

        .permission-item.selected {
            border-color: var(--bas-primary);
            background-color: var(--bas-primary-light);
        }

        .separator-text {
            grid-column: 1 / -1;
            padding: 20px 0 10px 0;
            font-weight: 800;
            color: var(--bas-dark);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
        }

        .separator-text::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--bas-border);
            margin-left: 15px;
        }

        #modal .modal-body {
            background-color: #FDFDFD;
            max-height: 65vh;
            overflow-y: auto;
            padding: 25px !important;
        }

        /* 6. BADGE STYLING (PENTING) */
        .label.label-light-warning {
            background-color: #FFFBEB !important;
            color: #D97706 !important;
            font-weight: 700;
        }

        .swal2-container {
            z-index: 2000 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-8 py-6">
        {{-- HEADER --}}
        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header d-flex align-items-center justify-content-between p-7 shadow-lg">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title">Auth Group (Role)</h2>
                            <div class="bas-header-sub">Pengelompokan Hak Akses &bull; Wings Food (BAS)</div>
                        </div>
                    </div>
                    <button type="button" onclick="openCreateModal()"
                        class="bas-btn bas-btn-primary px-6 h-45px d-flex align-items-center">
                        <i class="fas fa-plus-circle mr-2"></i> BUAT GROUP
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE CARD --}}
        @include('permission.partials._table_group')
    </div>

    {{-- MODAL MAPPING PERMISSION (Existing) --}}
    @include('permission.partials._modal_group')

    {{-- MODAL CREATE/EDIT GROUP --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--bas-radius-lg);">
                <form id="formCreate" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="group_id_input">
                    <div class="modal-header border-0 pt-8 px-8">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45 symbol-light-warning mr-4">
                                <span class="symbol-label"><i class="fas fa-users-cog text-warning"></i></span>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bolder text-dark" id="modalCreateLabel">Tambah Group Baru
                                </h5>
                                <p class="text-muted mb-0 font-size-sm">Kategorikan hak akses user</p>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>

                    <div class="modal-body px-8 py-4">
                        <div class="form-group mb-2 _name">
                            <label class="font-weight-bolder text-dark-75">Nama Group <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="input_group_name"
                                class="form-control form-control-solid h-50px"
                                placeholder="Contoh: Admin HRGA, IT Support..." style="border-radius: var(--bas-radius-md);"
                                autocomplete="off">
                            <span class="help-block text-danger font-size-sm"></span>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-8 px-8">
                        <button type="button" class="btn btn-light-danger font-weight-bold mr-3 px-8"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" class="bas-btn-primary px-12 h-45px shadow-sm submit-button">
                            <i class="fas fa-save mr-2"></i> SIMPAN GROUP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            // --- 1. INISIALISASI DATATABLE ---
            const table = $('#table_group').DataTable({
                responsive: true,
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    targets: [2],
                    orderable: false
                }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    paginate: {
                        next: "Next",
                        previous: "Prev"
                    }
                },
                // Bersihkan popover SEBELUM ganti halaman/draw
                preDrawCallback: function() {
                    $('[data-toggle="popover"]').popover('dispose');
                },
                // Inisialisasi ulang popover SETELAH tabel tampil
                drawCallback: function() {
                    $('[data-toggle="popover"]').popover({
                        trigger: 'click',
                        placement: 'top',
                        html: true,
                        sanitize: false,
                        container: 'body',
                        boundary: 'viewport',
                        template: '<div class="popover shadow-lg border-0" role="tooltip"><div class="arrow"></div><h3 class="popover-header bg-primary text-white border-0"></h3><div class="popover-body"></div></div>'
                    });
                }
            });

            // --- 2. LOGIC POPOVER (AUTO-CLOSE) ---
            $('body').on('click', function(e) {
                // Tutup popover lain saat klik popover baru
                if ($(e.target).data('toggle') === 'popover') {
                    $('[data-toggle="popover"]').not(e.target).popover('hide');
                }
                // Klik di luar popover untuk menutup semuanya
                if ($(e.target).data('toggle') !== 'popover' && $(e.target).parents('.popover').length ===
                    0) {
                    $('[data-toggle="popover"]').popover('hide');
                }
            });

            // --- 3. FORM RESET HANDLER ---
            // Penting: Memastikan saat modal ditutup, semua status kembali ke awal (Tambah)
            $('#modalCreate').on('hidden.bs.modal', function() {
                $('#formCreate')[0].reset();
                $('#group_id_input').val(''); // Gunakan ID yang konsisten
                $('#modalCreateLabel').text('Tambah Group Baru');
                $('.help-block').text('');
            });

            // --- 4. SUBMIT HANDLER: CREATE/UPDATE GROUP ---
            $('#formCreate').submit(function(e) {
                e.preventDefault();
                const $form = $(this);
                const $submitBtn = $form.find('.submit-button');

                KTApp.block('#modalCreate .modal-content', {
                    overlayColor: '#000000',
                    state: 'primary',
                    message: 'Memproses...'
                });

                let id = $('#group_id_input').val();
                let url = id ? '{{ URL::to('/permission/auth-group/update') }}' :
                    '{{ URL::to('/permission/auth-group/store') }}';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(res) {
                        KTApp.unblock('#modalCreate .modal-content');
                        if (res.success == 1) {
                            // TUTUP MODAL DULU agar tidak menumpuk dengan SweetAlert
                            $('#modalCreate').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: id ? 'Data group diperbarui.' :
                                    'Group baru ditambahkan.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        }
                    },
                    error: function(err) {
                        KTApp.unblock('#modalCreate .modal-content');
                        if (err.status == 422) {
                            $('.help-block').text('');
                            $.each(err.responseJSON.errors, (key, val) => {
                                $('._' + key + ' .help-block').text(val[0]);
                            });
                        }
                    }
                });
            });

            // --- 5. SUBMIT HANDLER: MAPPING PERMISSIONS ---
            $('#form').submit(function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

                $.ajax({
                    url: '{{ URL::to('/permission/auth-group/change-permissions') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Izin Terupdate!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        }
                    },
                    complete: () => btn.removeClass('spinner spinner-white spinner-right').attr(
                        'disabled', false)
                });
            });

            // --- 6. CHECKBOX STYLING ---
            $(document).on('change', '.permission-item input[type="checkbox"]', function() {
                $(this).closest('.permission-item').toggleClass('selected', this.checked);
            });
        });

        // --- GLOBAL FUNCTIONS ---

        function groupPermissions(id) {
            $(".group_id").val(id);
            $(".auth-permissions").html(
                '<div class="text-center p-10"><span class="spinner spinner-primary"></span></div>');
            $("#modal").modal("show");

            $.ajax({
                url: '{{ URL::to('/permission/auth-group/get-permissions') }}',
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    let html = '';
                    if (res.auth_permissions.length > 0) {
                        html += '<div class="separator-text text-primary">Permission Aktif</div>';
                        res.auth_permissions.forEach(data => html += renderPermissionItem(data, true));
                    }
                    if (res.permissions_left.length > 0) {
                        html += '<div class="separator-text">Tersedia</div>';
                        res.permissions_left.forEach(data => html += renderPermissionItem(data, false));
                    }
                    $(".auth-permissions").html(html);
                }
            });
        }

        function renderPermissionItem(data, isChecked) {
            return `
            <li class="permission-item ${isChecked ? 'selected' : ''}">
                <label class="checkbox checkbox-primary">
                    <input type="checkbox" name="permissions[]" ${isChecked ? 'checked' : ''} value="${data.id}">
                    <span></span>
                    <div class="ml-2"><b>${data.codename}</b></div>
                </label>
            </li>`;
        }

        function openCreateModal() {
            $('[data-toggle="popover"]').popover('dispose');
            $('#formCreate')[0].reset();
            $('#group_id_input').val('');
            $('#modalCreateLabel').text('Tambah Group Baru');
            $('.modal-header p').text('Kategorikan hak akses user');
            $('.submit-button').html('<i class="fas fa-plus-circle mr-2"></i> TAMBAH GROUP');
            $('#modalCreate').modal('show');
        }

        function editGroup(id, name) {
            $('[data-toggle="popover"]').popover('dispose');
            $('#group_id_input').val(id);
            $('#input_group_name').val(name);
            $('#modalCreateLabel').text('Edit Nama Group');
            $('.modal-header p').text('Perbarui nama kategori hak akses');
            $('.submit-button').html('<i class="fas fa-edit mr-2"></i> SIMPAN PERUBAHAN');
            $('#modalCreate').modal('show');
        }

        function deleteGroup(id) {
            $('[data-toggle="popover"]').popover('dispose');
            Swal.fire({
                title: 'Hapus Group?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.value) {
                    KTApp.blockPage({
                        overlayColor: '#000',
                        state: 'danger',
                        message: 'Menghapus...'
                    });
                    $.ajax({
                        url: '{{ URL::to('/permission/auth-group/delete') }}',
                        type: 'POST',
                        data: {
                            id: id,
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Group telah dihapus.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        },
                        error: () => KTApp.unblockPage()
                    });
                }
            });
        }
    </script>
@endpush
