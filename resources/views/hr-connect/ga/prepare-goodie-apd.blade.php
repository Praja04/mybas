@extends('hr-connect.layouts.base')

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
                            </div>
                            <div class="col-lg-4 text-end">
                                <button class="btn btn-primary font-weight-bolder shadow-sm" id="btnConfirmAll" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi semua jadwal persiapan sekaligus">
                                    <i class="ri-check-double-fill align-bottom me-1"></i> Konfirmasi Semua Persiapan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4">
                        <div class="table-responsive">
                            <table id="tableAjax" class="table table-bordered table-hover align-middle" style="width:100%">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 30%;">Jadwal Tanggal Masuk</th>
                                        <th style="width: 30%;">Total Karyawan (Set Fasilitas)</th>
                                        <th style="width: 40%; text-align: center;">Tindakan</th>
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
            moment.locale('id');

            let table = $('#tableAjax').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-end'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                ajax: {
                    type: "GET",
                    url: "{{ url('/hr-connect/dept-ga/perlengkapan-goodie-apd/getData') }}",
                },
                columns: [{
                        data: "tanggal_masuk",
                        render: function(data) {
                            return `<span class="fw-bold text-dark">${moment(data).format('DD MMMM YYYY')}</span>`;
                        }
                    },
                    {
                        data: "count",
                        orderable: false,
                        render: function(data) {
                            return `<span class="badge bg-soft-info text-info fs-13 px-3 py-2 shadow-sm"><i class="ri-group-line me-1 align-bottom"></i> ${data} Set Persiapan</span>`;
                        }
                    },
                    {
                        data: "count",
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                            <center>
                                <button class="btn btn-sm btn-outline-success fw-bold btnConfirm shadow-sm"
                                    data-id="${row.id}"
                                    data-jumlah="${data}"
                                    data-tgl="${row.tanggal_masuk}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Tandai fasilitas untuk jadwal ini telah siap"
                                    style="font-size: 0.85rem;">
                                    <i class="ri-check-line align-bottom me-1"></i> Konfirmasi Selesai
                                </button>
                            </center>`;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            table.on('draw.dt', function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            $('#btnConfirmAll').click(function() {
                let btn = $(this);
                let dt = $("#tableAjax").DataTable();

                btn.tooltip('hide');

                if (!dt.data().any()) {
                    Swal.fire('Informasi',
                        'Tidak ada jadwal persiapan fasilitas yang perlu dikonfirmasi saat ini.', 'info');
                    return;
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
                        let originalText = btn.html();
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
                                dt.ajax.reload(null, false);

                                Toastify({
                                    text: res.msg || "Seluruh jadwal persiapan berhasil dikonfirmasi!",
                                    duration: 4000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#0ab39c",
                                }).showToast();

                                btn.prop('disabled', false).html(originalText);
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal Memproses",
                                    "Terjadi kesalahan sistem saat memproses konfirmasi massal.",
                                    "error");
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    }
                });
            });

            $(document).on("click", ".btnConfirm", function() {
                let btn = $(this);
                let jumlah = btn.data("jumlah");
                let tgl_masuk = btn.data("tgl");
                let dt = $("#tableAjax").DataTable();

                let tanggalFormat = moment(tgl_masuk).format('DD MMMM YYYY');

                btn.tooltip('hide');

                Swal.fire({
                    title: "Konfirmasi Persiapan",
                    html: `Anda akan mengonfirmasi kesiapan fasilitas sebanyak <b>${jumlah} set</b> untuk jadwal masuk <b>${tanggalFormat}</b>. Lanjutkan?`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#0ab39c",
                    cancelButtonColor: "#878a99",
                    confirmButtonText: "<i class='ri-check-line me-1'></i> Ya, Sudah Siap",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        let originalText = btn.html();
                        btn.prop('disabled', true).html(
                            '<i class="spinner-border spinner-border-sm me-1"></i> Memproses...'
                        );

                        $.ajax({
                            type: "POST",
                            url: "{{ url('/hr-connect/dept-ga/perlengkapan-goodie-apd/updateData') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                confirm: 'Y',
                                tgl_masuk: tgl_masuk,
                                jumlah: jumlah
                            },
                            success: function(res) {
                                dt.ajax.reload(null, false);

                                Toastify({
                                    text: res.msg || `Konfirmasi persiapan untuk tanggal ${tanggalFormat} berhasil disimpan!`,
                                    duration: 4000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#0ab39c",
                                }).showToast();
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal",
                                    "Terjadi kesalahan sistem saat menyimpan data konfirmasi.",
                                    "error");
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
