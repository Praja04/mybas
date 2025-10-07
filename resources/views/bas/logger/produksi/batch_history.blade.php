@extends('bas.layout.master')

    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

        </style>
    @endpush

@section('content')

    <div class="container">
        <div class="main-body">

                 <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 100%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/bas_logger/operator/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

            <div class="row gutters-sm">
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header text-center">
                         <i class="fas fa-history text-dark"></i> <b>HISTORY BATCH IDENTITY </b>
                        </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                              <tr class="text-center">
                                                <th>No.</th>
                                                <th>Tanggal Pasteurisasi</th>
                                                <th>Tanggal Produksi</th>
                                                <th>Jenis Sampel</th>
                                                <th>Varian</th>
                                                <th>Group</th>
                                                <th>Main Blending</th>
                                                <th>Main Batch</th>
                                                <th>Storage</th>
                                                <th>Opsi</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                @foreach ($data as $list)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$list->tgl_pasteurisasi}}</td>
                                                    <td>{{$list->tgl_produksi}}</td>
                                                    <td>{{$list->jenis_sampel}}</td>
                                                    <td>{{$list->jenis_varian}}</td>
                                                    <td>{{$list->group}}</td>
                                                    <td>{{$list->main_blending}}</td>
                                                    <td>{{$list->main_batch}}</td>
                                                    <td>{{$list->storage}}</td>
                                                    <td style="width: 20%">
                                                        <a href="/bas_logger/operator/detail_batch_identity/{{$list->id}}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i></a>
                                                        <a href="/bas_logger/operator/destroy_batch_identity/{{$list->id}}" class="btn btn-primary btn-sm"><i class="fas fa-trash-alt" data-toggle="tooltip" title="Hapus"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
       <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>


    <script type="text/javascript">
        $('.table').DataTable();
 
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

@endpush
