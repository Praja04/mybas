@extends('hr-connect.layouts.base')

@push('styles')
    <style>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h5 class="card-title mb-0" style="font-weight: 600;">
                                    <i class="ri-gift-line text-info me-2"></i> Persiapan Fasilitas Goodie Bag & APD
                                </h5>
                                <p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">
                                    Konfirmasi ketersediaan paket perlengkapan (Goodie Bag & APD) untuk kloter karyawan
                                    baru.
                                </p>
                            </div>
                            <div class="col-lg-4 text-end mt-3 mt-lg-0">
                                <button class="btn btn-primary font-weight-bolder shadow-sm" id="btnConfirmAll"
                                    data-bs-toggle="tooltip" title="Konfirmasi semua jadwal sekaligus">
                                    <i class="ri-check-double-fill align-bottom me-1"></i> Konfirmasi Semua Persiapan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                                style="width:100%">
                                <thead class="table-light text-muted text-center">
                                    <tr>
                                        <th style="width: 30%;">Jadwal Tanggal Masuk</th>
                                        <th style="width: 30%;">Total Karyawan (Butuh Set)</th>
                                        <th style="width: 40%;">Tindakan</th>
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
    <script src="{{ asset('assets/velzon/libs/moment/locale/id.js') }}"></script>

    <script>
        $(document).ready(function() {
            // ==========================================
            // 1. INIT GLOBAL & DATATABLES
            // ==========================================
            moment.locale('id');

            let table = $('#tableAjax').DataTable({
                processing: true,
                serverSide: false, // Karena data udah di-groupBy dari controller
                ordering: false, // Ordering sudah di-handle controller (desc)
                paging: true,
                searching: false,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-ga/perlengkapan-goodie-apd/getData') }}",
                },
                columns: [{
                        data: "tanggal_masuk",
                        className: "text-center",
                        render: function(data) {
                            return `<span class="fw-bold text-dark">${moment(data).format('DD MMMM YYYY')}</span>`;
                        }
                    },
                    {
                        data: "count",
                        className: "text-center",
                        render: function(data) {
                            return `<span class="badge bg-soft-info text-info fs-13 px-3 py-2 shadow-sm"><i class="ri-group-line me-1 align-bottom"></i> ${data} pcs Goodie Bag</span>`;
                        }
                    },
                    {
                        data: "count",
                        className: "text-center",
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-outline-success fw-bold btnConfirm shadow-sm"
                                    data-id="${row.id}"
                                    data-jumlah="${data}"
                                    data-tgl="${row.tanggal_masuk}"
                                    data-bs-toggle="tooltip"
                                    title="Tandai fasilitas kloter ini siap"
                                    style="font-size: 0.85rem;">
                                    <i class="ri-check-line align-bottom me-1"></i> Konfirmasi Selesai
                                </button>`;
                        }
                    }
                ]
            });

            table.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // ==========================================
            // 2. EVENT LISTENER: KONFIRMASI MASSAL
            // ==========================================
            $('#btnConfirmAll').click(function() {
                let btn = $(this);
                let originalText = btn.html();

                btn.tooltip('hide');

                if (!table.data().any()) {
                    return Swal.fire('Informasi',
                        'Tidak ada jadwal persiapan fasilitas yang perlu dikonfirmasi saat ini.', 'info'
                    );
                }

                Swal.fire({
                    title: "Konfirmasi Massal",
                    html: "Anda akan mengonfirmasi bahwa <b>seluruh</b> persiapan fasilitas Goodie Bag dan APD untuk semua jadwal di daftar ini telah siap didistribusikan. Lanjutkan proses?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "<i class='ri-check-double-line me-1'></i> Ya, Eksekusi Semua",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="spinner-border spinner-border-sm me-1"></i> Memproses Data...'
                        );

                        $.ajax({
                            type: "POST",
                            url: "{{ url('/hr-connect/dept-ga/perlengkapan-goodie-apd/confirmAll') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                            },
                            success: function(res) {
                                table.ajax.reload(null, false);
                                btn.prop('disabled', false).html(originalText);

                                Toastify({
                                    text: res.message ||
                                        "Seluruh jadwal persiapan berhasil dikonfirmasi!",
                                    duration: 4000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#0ab39c",
                                }).showToast();
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(originalText);
                                Swal.fire("Gagal Memproses", xhr.responseJSON
                                    ?.message ||
                                    "Terjadi kesalahan sistem saat memproses konfirmasi massal.",
                                    "error");
                            }
                        });
                    }
                });
            });

            // ==========================================
            // 3. EVENT LISTENER: KONFIRMASI PER-KLOTER
            // ==========================================
            $(document).on("click", ".btnConfirm", function() {
                let btn = $(this);
                let jumlah = btn.data("jumlah");
                let tgl_masuk = btn.data("tgl");
                let originalText = btn.html();
                let tanggalFormat = moment(tgl_masuk).format('DD MMMM YYYY');

                btn.tooltip('hide');

                Swal.fire({
                    title: "Konfirmasi Persiapan",
                    html: `Anda mengonfirmasi kesiapan fasilitas sebanyak <b>${jumlah} set</b> untuk jadwal masuk <b>${tanggalFormat}</b>. Lanjutkan?`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "<i class='ri-check-line me-1'></i> Ya, Sudah Siap",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        btn.prop('disabled', true).html(
                            '<i class="spinner-border spinner-border-sm me-1"></i> Memproses...'
                        );

                        $.ajax({
                            type: "POST",
                            url: "{{ url('/hr-connect/dept-ga/perlengkapan-goodie-apd/updateData') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                tgl_masuk: tgl_masuk,
                                jumlah: jumlah
                            },
                            success: function(res) {
                                table.ajax.reload(null, false);

                                Toastify({
                                    text: res.msg ||
                                        `Konfirmasi persiapan untuk tanggal ${tanggalFormat} berhasil disimpan!`,
                                    duration: 4000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#0ab39c",
                                }).showToast();
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(originalText);
                                Swal.fire("Gagal", xhr.responseJSON?.msg ||
                                    "Terjadi kesalahan sistem saat menyimpan data konfirmasi.",
                                    "error");
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
