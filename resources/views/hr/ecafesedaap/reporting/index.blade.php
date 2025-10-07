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
                        <span class="card-label font-weight-bolder text-dark">Reporting </span>
                    </h3>
                </div>
                <form action={{url('/PencarianReport')}} method="POST">
                    @csrf
                <div class="card-body">
                    <div class="row">
                        <label class="form-label col-sm-1 pt-3 text-right">Tanggal mulai</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="date" name="tanggal_mulai" class="form-control">
                            </div>
                        </div>
                        <label class="form-label col-sm-2 pt-3 text-right">Tanggal Selesai</label>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="date" name="tanggal_selesai" class="form-control">
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
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Jumlah Pesanan</th>
                                            <th>Jumlah Scan</th>
                                            <th>Selisih</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$item->tanggal}}</td>
                                            <td>{{$item->shift}}</td>
                                            <td>{{$item->jumlah}}</td>
                                            @if(date('l') == 'Saturday')
                                            @php
                                            $start_shift1 = $item->tanggal.' 06:00:00';
                                            $end_shift1 = $item->tanggal.' 11:00:00';
                                            $start_shift2 = $item->tanggal.' 11:00:00';
                                            $end_shift2 = $item->tanggal.' 16:00:00';
                                            $start_shift3 = $item->tanggal.' 16:00:00';
                                            $end_shift3 = $item->tanggal.' 21:00:00';
                                            @endphp
                                        @else
                                            @php
                                                $start_shift1 = $item->tanggal.' 09:00:00';
                                                $end_shift1 = $item->tanggal.' 16:00:00';
                                                $start_shift2 = $item->tanggal.' 16:00:00';
                                                $end_shift2 = $item->tanggal.' 22:30:00';
                                                $start_shift3 = $item->tanggal.' 22:30:00';
                                                $end_shift3 = date('Y-m-d', strtotime( $item->tanggal . ' + 1 Days')).' 07:00:00';
                                            @endphp
                                        @endif
                                        @if($item->shift == 1)
                                            <td>{{$scan->whereBetween('waktu', [$start_shift1, $end_shift1])->count()}}</td>
                                        @elseif($item->shift == 2)
                                            <td>{{$scan->whereBetween('waktu', [$start_shift2, $end_shift2])->count()}}</td>
                                        @else
                                            <td>{{$scan->whereBetween('waktu', [$start_shift3, $end_shift3])->count()}}</td>
                                        @endif
                                        <td>
                                            @if($item->jumlah - $scan->where('shift', $item->shift)->whereBetween('tanggal', [$item->tanggal, $item->tanggal])->count()==$item->jumlah)
                                            0
                                            @else
                                            {{$item->jumlah - $scan->where('shift', $item->shift)->whereBetween('tanggal', [$item->tanggal, $item->tanggal])->count() }}
                                            @endif
                                        
                                        </td>
                                        <td>
                                            <a href="{{ url('/ecafesedaap/reporting/detail/'.$item->id_pesanan) }}">Detail</a>
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
        $('#tbl_report').DataTable({
            'searching': false
        });
    </script>
@endpush