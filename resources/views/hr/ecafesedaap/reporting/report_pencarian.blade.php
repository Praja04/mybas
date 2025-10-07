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
                        <span class="card-label font-weight-bolder text-dark">Reporting {{Carbon\Carbon::parse($tanggal_mulai)->format('d-M-Y')}} sampai {{Carbon\Carbon::parse($tanggal_selesai)->format('d-M-Y')}}

                        </span>
                    </h3>
                </div>
                <form action={{url('/PencarianReport')}} method="POST">
                    @csrf
                <div class="card-body">
                    <div class="row">
                        <label class="form-label col-sm-1 pt-3 text-right">Tanggal mulai</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input value="{{ $tanggal_mulai }}" type="date" name="tanggal_mulai" class="form-control">
                            </div>
                        </div>
                        <label class="form-label col-sm-2 pt-3 text-right">Tanggal Selesai</label>  
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input value="{{ $tanggal_selesai }}" type="date" name="tanggal_selesai" class="form-control">
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
                                <table class="table table-bordered" id="tbl_report">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Jumlah Pesanan</th>
                                            <th>Jumlah Scan</th>
                                            <th>Selisih</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($master as  $value)
                                            <tr>
                                                <td>{{Carbon\carbon::parse($value->tanggal)->format('d-M-Y')}}</td>
                                                <td>{{$value->shift}}</td>
                                                <td>{{$value->jumlah}}</td>
                                                @if($value->shift == 1)
                                                    <td>{{$scan->where('shift', 1)->where('tanggal', $value->tanggal)->count()}}</td>
                                                    @elseif($value->shift == 2)
                                                    <td>{{$scan->where('shift',2)->where('tanggal', $value->tanggal)->count()}}</td>
                                                    @else
                                                    <td>{{$scan->where('shift', 3)->where('tanggal', $value->tanggal)->count()}}</td>
                                                @endif
                                                    @if($value->shift == 1)
                                                    <td>
                                                        @if($value->jumlah - $scan->where('shift', 1)->where('tanggal', $value->tanggal)->count()==$value->jumlah)
                                                        0
                                                        @else
                                                        {{$value->jumlah -$scan->where('shift', 1)->where('tanggal', $value->tanggal)->count()}}
                                                        @endif
                                                    </td>
                                                    @elseif($value->shift == 2)
                                                    <td>
                                                        @if($value->jumlah - $scan->where('shift', 2)->where('tanggal', $value->tanggal)->count()==$value->jumlah)
                                                        0
                                                        @else
                                                        {{$value->jumlah -$scan->where('shift', 2)->where('tanggal', $value->tanggal)->count()}}
                                                        @endif
                                                    </td>
                                                    @else
                                                    <td>
                                                        @if($value->jumlah - $scan->where('shift', 3)->where('tanggal', $value->tanggal)->count()==$value->jumlah)
                                                        0
                                                        @else
                                                        {{$value->jumlah -$scan->where('shift', 3)->where('tanggal', $value->tanggal)->count()}}
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>
                                                    <a href="{{ url('/ecafesedaap/reporting/detail/'.$value->id_pesanan) }}">Detail</a>
                                                </td>
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
        $('#tbl_report').DataTable()
    </script>
@endpush