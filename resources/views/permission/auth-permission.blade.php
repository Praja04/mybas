@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">

    <style>
        /* Menggunakan Root Variables dari Halaman Loker */
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

        /* Override Body Background agar kartu terlihat pop-out */
        .content {
            background-color: #F9FAFB !important;
        }

        /* ---- HEADER STYLE (Matching Loker) ---- */
        .bas-header {
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--bas-radius-lg);
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
            flex-shrink: 0;
        }

        .bas-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            margin-bottom: 0;
        }

        .bas-header-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        /* ---- TABLE CARD & DATATABLE ---- */
        .bas-tab-card {
            background: var(--bas-surface) !important;
            border: 1.5px solid var(--bas-border);
            border-radius: var(--bas-radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        #table_permission {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
        }

        #table_permission thead th {
            background-color: var(--bas-neutral-light);
            color: var(--bas-neutral);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 18px 15px;
            border: none;
        }

        #table_permission td {
            padding: 16px 15px;
            border-bottom: 1px solid var(--bas-border);
            color: var(--bas-dark);
            vertical-align: middle;
        }

        #table_permission tbody tr:hover {
            background-color: #FFFBEB !important;
            /* Soft yellow hover */
        }

        /* ---- CUSTOM BUTTONS ---- */
        .bas-btn-primary {
            background: var(--bas-primary);
            border: none;
            color: #FFFFFF;
            font-weight: 600;
            border-radius: var(--bas-radius-md);
            transition: var(--bas-transition);
        }

        .bas-btn-primary:hover {
            background: var(--bas-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: #FFFFFF;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-8 py-6">

        {{-- HEADER: Senada dengan Halaman Loker --}}
        <div class="row mb-7">
            <div class="col-12">
                <div class="bas-header d-flex align-items-center justify-content-between p-7 shadow-lg">
                    <div class="d-flex align-items-center">
                        <div class="bas-header-icon mr-5">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h2 class="bas-header-title">Manajemen Permission</h2>
                            <div class="bas-header-sub">Kontrol Hak Akses Sistem &bull; Wings Food (BAS)</div>
                        </div>
                    </div>

                    @if (Auth::user()->auth_group_id == 1)
                        <button type="button" onclick="openModalAdd()"
                            class="bas-btn bas-btn-primary px-6 h-45px d-flex align-items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Akses
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- TABLE CONTAINER --}}
        @include('permission.partials._table_permission')

        @include('permission.partials._modal')
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#table_permission').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                "order": [
                    [3, 'desc']
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ada data yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Data kosong",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                columnDefs: [{
                    targets: [1, -1],
                    orderable: false
                }]
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('#p_name').on('keyup', function() {
                if ($('#permission_id').val() == "") {

                    let name = $(this).val();
                    let slug = name.toLowerCase()
                        .replace(/\s+/g, '_') // Ganti spasi dengan underscore
                        .replace(/[^\w\-]+/g, ''); // Hapus karakter non-alphanumeric

                    $('#p_codename').val(slug);
                }
            });
        });

        function openModalAdd() {
            $('#formPermission')[0].reset();
            $('#permission_id').val('');
            $('#p_codename').val('');
            $('#p_codename').attr('readonly', false).removeClass('bg-secondary text-white');
            $('#modalTitle').text('Tambah Permission Baru');
            $('#modalPermission').modal('show');
        }

        function editPermission(id) {
            $.get(`/permission/auth-permission/get/${id}`, function(data) {
                $('#permission_id').val(data.id);
                $('#p_name').val(data.name);
                $('#p_codename').val(data.codename);
                $('#p_codename').attr('readonly', false).removeClass('bg-secondary text-white');
                $('#modalTitle').text('Edit Permission');
                $('#modalPermission').modal('show');
            });
        }

        function deletePermission(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/permission/auth-permission/delete/${id}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan sistem';
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: msg
                            });
                        }
                    });
                }
            });
        }

        $('#formPermission').on('submit', function(e) {
            e.preventDefault();

            let btn = $(this).find('button[type="submit"]');
            btn.addClass('spinner spinner-white spinner-right').attr('disabled', true);

            $.ajax({
                url: "{{ route('auth-permission.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#modalPermission').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = "";

                    $.each(errors, function(key, value) {
                        errorMsg += value + "<br>";
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: errorMsg
                    });
                },
                complete: function() {
                    btn.removeClass('spinner spinner-white spinner-right').attr('disabled', false);
                }
            });
        });
    </script>
@endpush
