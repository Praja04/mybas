@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data <span style="color: red">Kedatangan Lauk</span>
                                <span class="d-block text-muted pt-2 font-size-sm">Atur Data Jumlah Pesanan</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="pengirimCatering" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="4%">no</th>
                                    <th class="col-md-1" style="color: red;">Nomor Transaksi</th>
                                    <th class="col-md-1">Tanggal</th>
                                    <th class="col-md-1">Catering</th>
                                    <th class="col-md-1">Shift</th>
                                    {{-- <th class="col-md-1">Status Cek Kendaraan</th> --}}
                                    <th class="col-md-1">Status Cek Kedatangan</th>
                                    <th width="8%"><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table kedatangan lauk --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script type="text/javascript">
        // open modal pesanan catering
        function showModalCreateCatering() {
            $('#modal-title-pesanan').text('Upload Data Catering');
            $('#modalPesananCatering').modal('show');
        }

        // getall data pengirim
        $(document).ready(function() {
            var table = $('#pengirimCatering').DataTable({
                paging: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('cateringbas.get.reporting-user') }}",
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
                        data: 'tanggal'
                    },
                    {
                        data: 'catering'
                    },
                    {
                        data: 'shift'
                    },
                    // {
                    //     data: 'status_cek_kendaraan',
                    //     render: function(data, type, row) {
                    //         if (data === 'belum') {
                    //             return '<span class="badge badge-danger">Belum</span>';
                    //         } else if (data === 'sudah') {
                    //             return '<span class="badge badge-success">Sudah</span>';
                    //         } else {
                    //             return data;
                    //         }
                    //     }
                    // },
                    {
                        data: 'status_cek_kedatangan',
                        render: function(data, type, row) {
                            if (data === 'belum') {
                                return '<span class="badge badge-danger">Belum</span>';
                            } else if (data === 'menunggu approval') {
                                return '<span class="badge badge-warning text-white">menunggu Approval</span>';
                            } else if (data === 'sudah') {
                                return '<span class="badge badge-success">Sudah di Approve</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            if (row.status_cek_kedatangan === 'sudah') {
                                return `
                                <div class="mt-4">
                                    <a href="{{ url('/pesanan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-success text-white">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>`;
                            } else if (row.status_cek_kedatangan === 'menunggu approval') {
                                return `
                                <div class="mt-4">
                                    <a href="{{ url('/pesanan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>`;
                            } else if (row.status_cek_kedatangan === 'belum') {
                                return `
                                <div class="mt-4">
                                    <a href="{{ url('/pesanan/') }}/${row.id_transaksi}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fa fa-eye"></i>
                                        </a>
                                </div>`;
                            }
                        }
                    }
                ]
            });
        });
    </script>
@endpush
