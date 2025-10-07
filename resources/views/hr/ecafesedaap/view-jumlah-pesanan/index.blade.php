@extends('layouts.base')

@section('content')
<div class="container-fluid">

    <!--begin::Row-->
    <div class="row">

        <div class="col-lg-12">
            <!--begin::Advance Table Widget 4-->
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">View Jumlah Update Pesanan</span>
                        
                    </h3>
                </div>
            <form action="{{url('PencarianPesanan')}}" method="post">
            @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="date" name="tanggal" class="form-control">
                            </div>
                        </div>
                      
                        <div class="col-sm-3">
                            <div class="form-group">
                              <select class="form-control" name="shift" id="">
                                <option value="" selected disabled>shift</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                              </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form group">
                                <button type="submit" class="btn btn-info btn-lg">Cari</button>
                            </div>
                        </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tbl_pesanan">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jumlah Pesanan</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Alasan Update</th>
                                            <th>Jenis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$item->jumlah}}</td>
                                            <td>{{$item->tanggal}}</td>
                                            <td>{{$item->shift}}</td>
                                            <td>{{$item->alasan_update}}</td>
                                            <td>{{$item->jenis}}</td>
                                        </tr>
                                        @endforeach
                                   </tbody>
                                </table>
                            </div>
                        </div>
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


@endsection
@push('scripts')
<script src="{{ url('/') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#tbl_pesanan').DataTable({
            'searching': false
        });
    </script>
@endpush