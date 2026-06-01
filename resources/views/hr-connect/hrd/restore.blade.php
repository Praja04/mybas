@extends('hr-connect.layouts.base')

@push('styles')
    <style>
        .table-custom-header th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <h5 class="card-title mb-0" style="font-weight: 600;">
                                    <i class="ri-history-line text-info me-2"></i> Pemulihan Data Karyawan (Restore)
                                </h5>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">
                                    Daftar karyawan yang batal masuk (NO-IN) atau dalam proses Offboarding. Lakukan
                                    pemulihan jika terjadi kesalahan data.
                                </p>
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
                                        <th style="width: 15%;">NIK</th>
                                        <th>Departemen / Bagian</th>
                                        <th>Keterangan / Status</th>
                                        <th style="width: 15%; text-align: center;">Tindakan</th>
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
    <script>
        $(document).ready(function() {
            // --- INISIALISASI DATATABLES ---
            let table = $("#tableAjax").DataTable({
                processing: true,
                serverSide: false, // Karena dari Controller balikin response()->json(['data' => $data]) murni
                ordering: true,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-hrd/pemulihan-data/getData') }}"
                },
                columns: [{
                        data: 'nama',
                        render: function(data) {
                            return `<span class="fw-bold text-primary">${data}</span>`;
                        }
                    },
                    {
                        data: 'nik',
                        render: function(data) {
                            return `<span class="fw-bold">${data}</span>`;
                        }
                    },
                    {
                        data: 'kode_bagian',
                        render: function(data) {
                            return `<span class="badge bg-light text-dark border">${data || '-'}</span>`;
                        }
                    },
                    {
                        data: 'alasan_keluar',
                        render: function(data, type, row) {
                            // LOGIKA PINTAR: Membedakan NO-IN dan Karyawan Keluar (Checkout)
                            if (row.p_no === 'Y') {
                                return `<span class="badge bg-soft-danger text-danger border border-danger px-2 py-1"><i class="ri-close-circle-line align-bottom me-1"></i> Batal Masuk (NO-IN)</span>`;
                            }

                            let alasan = data ? data : 'Proses Checkout';
                            return `<span class="badge bg-soft-warning text-warning border border-warning px-2 py-1"><i class="ri-logout-box-r-line align-bottom me-1"></i> ${alasan}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <center>
                                    <button class="btn btn-sm btn-outline-info fw-bold btn-restore shadow-sm"
                                        data-nik="${row.nik}" data-nama="${row.nama}">
                                        <i class="ri-refresh-line align-bottom me-1"></i> Pulihkan
                                    </button>
                                </center>
                            `;
                        }
                    }
                ]
            });

            // --- PROSES PEMULIHAN (RESTORE) ---
            $(document).on('click', '.btn-restore', function() {
                let nik = $(this).data('nik');
                let nama = $(this).data('nama');
                let btn = $(this);
                let originalHtml = btn.html();

                Swal.fire({
                    title: 'Konfirmasi Pemulihan',
                    html: `Apakah Anda yakin ingin memulihkan data <b>${nama}</b>?<br><small class="text-muted">Data ini akan dikembalikan ke status aktif.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0ab39c',
                    cancelButtonColor: '#878a99',
                    confirmButtonText: '<i class="ri-refresh-line me-1"></i> Ya, Pulihkan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        btn.html('<i class="spinner-border spinner-border-sm"></i>').prop(
                            'disabled', true);

                        $.ajax({
                            url: `{{ url('/hr-connect/dept-hrd/pemulihan-data') }}`,
                            type: 'PUT',
                            data: {
                                nik: nik,
                                _token: "{{ csrf_token() }}" // PENGAMAN FATAL: Wajib ada CSRF Token untuk PUT/POST
                            },
                            success: function(res) {
                                Toastify({
                                    text: "Data berhasil dipulihkan!",
                                    duration: 3000,
                                    gravity: "top",
                                    position: 'right',
                                    backgroundColor: "#0ab39c"
                                }).showToast();

                                table.ajax.reload(null,
                                false); // Reload datatables tanpa mereset pagination
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat memulihkan data.',
                                    'error'
                                );
                                btn.html(originalHtml).prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
