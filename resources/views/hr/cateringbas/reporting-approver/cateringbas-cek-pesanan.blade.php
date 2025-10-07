@extends('layouts.base')

@section('content')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Approver Cek Kedatangan Lauk</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action={{ url('/reporting/get-tanggal-pesanan') }} method="GET">
                            <div class="row">
                                {{-- <label class="form-label col-sm-1 pt-3 text-right">Tanggal</label>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" value="{{ $_GET['tanggal'] ?? '' }}" name="tanggal"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form group">
                                        <button type="submit" class="btn btn-info btn-lg">Cari</button>
                                    </div>
                                </div> --}}
                                <div class="pl-4 d-flex justify-content-end">
                                    <div class="form-group">
                                        <a id="SubmitApprovePesanan"
                                            href="{{ url('/reporting/approve-pesanan/' . $data->id_transaksi) }}"
                                            class="btn btn-primary btn-lg"
                                            onclick="return confirm('Apakah Anda Yakin Akan Approve Data Ini?');">
                                            Approve Data Kedatangan Lauk <i class="fas fa-check ml-4"></i>
                                        </a>
                                    </div>
                                    <div class="form-group">
                                        <a id="dataApprovePesanan" class="btn btn-success btn-lg" style="display: none;">
                                            <div class="p-2">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>{{ $data->approval_cek_pesanan_at }}</span>
                                            </div>
                                            <div class="p-2">
                                                <i class="fas fa-user"></i>
                                                <span>{{ $data->approval_cek_pesanan_by }}</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tbl_report">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>no</th>
                                                <th>Id transaksi</th>
                                                <th>Nama Menu Utama</th>
                                                <th>Nama Menu Pendamping</th>
                                                <th>Kategori</th>
                                                <th>Shift</th>
                                                <th>Jumlah Order BAS</th>
                                                <th>Jumlah Order Yang Datang</th>
                                                <th>Keterangan</th>
                                                {{-- <th>Status Approval</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- test --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="button-group pt-4" style="display: flex; gap: 20px;">
                            <a href="/cateringbas/reporting-GA" id="kembalikehome" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    {{-- modal delete jumlah pesanan catering --}}
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    {{-- filepon --}}
    {{-- <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script> --}}
    <script type="text/javascript">
        // get pesanan
        $(document).ready(function() {

            var table = $('#tbl_report').DataTable({
                paging: true,
                responsive: true,
                ajax: {
                    url: '/cateringbas/kedatangan-lauk/get-all/' + encodeURIComponent(
                        '{{ $data->id_transaksi }}'),
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'id_transaksi'
                    },
                    {
                        data: 'nama_menu_utama'
                    },
                    {
                        data: 'nama_menu_pendamping'
                    },
                    {
                        data: 'kategori_staff'
                    },
                    {
                        data: 'shift'
                    },
                    {
                        data: 'jumlah_order_bas'
                    },
                    {
                        data: 'jumlah_order'
                    },
                    {
                        data: 'keterangan',
                        render: function(data, type, row) {
                            if (data === 'tidak sesuai') {
                                return '<span class="badge badge-danger">belum sesuai</span>';
                            } else if (data === 'sesuai') {
                                return '<span class="badge badge-success">sesuai</span>';
                            } else {
                                return data;
                            }
                        }
                    }
                    // {
                    //     data: 'approved_at'
                    // },
                    // {
                    //     data: 'approved_by'
                    // }
                    // {
                    //     data: 'status_approval',
                    //     render: function(data, type, provsan, meta) {
                    //         if (type === 'display') {
                    //             var id_transaksi = provsan.id_transaksi
                    //             var approveUrl =
                    //                 `/reporting/approve-pesanan/${provsan.id_transaksi}`;
                    //             var rejectUrl = '';
                    //             return `
                //             <div style="margin-bottom: 10px;">
                //                 <a href="${approveUrl}" class="btn btn-sm text-white"
                //                     onclick="return confirm('Apakah Anda Yakin Akan Approve Data Ini?');"
                //                     style="background-color: rgb(60, 246, 40); border-radius: 15px;">Approve</a>
                //             </div>
                //             <div>
                //                 <a href="${rejectUrl}" class="btn btn-sm text-white"
                //                     onclick="return confirm('Apakah Anda Yakin Akan Reject Data Ini?');"
                //                     style="background-color: rgb(246, 20, 20); border-radius: 15px;">Reject</a>
                //             </div>`;
                    //         } else {
                    //             return data;
                    //         }
                    //     }

                    // }
                ]
            });


            // Inisiasi VenoBox untuk gambar
            new VenoBox({
                selector: '.venobox',
                overlayClose: true,
            });
        });

        $(document).ready(function() {
            function showConfirmationModal() {
                Swal.fire({
                    icon: 'info',
                    title: 'Hanya Reminder',
                    text: 'Pastikan jumlah pesanan sudah sesuai.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Tutup'
                });
            }
            showConfirmationModal();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->approval_cek_pesanan }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'Y') {
                document.getElementById("SubmitApprovePesanan").style.display = "none";
                document.getElementById("dataApprovePesanan").style.display = "inline-block";
            }
        });
    </script>
@endpush
