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
                        <span class="card-label font-weight-bolder text-dark">Reporting {{Carbon\Carbon::parse($tanggal_mulai)->format('d-M-Y')}}
                        </span>
                    </h3>
                </div>
                <form action={{url('/masukharilibur/reporting')}} method="GET">
                    @csrf
                <div class="card-body">
                    <div class="row">
                        <label class="form-label col-sm-3 pt-3 text-right">Cari as Tanggal</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input value="{{ $_GET['tanggal'] ?? '' }}" type="date" name="tanggal" class="form-control">
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
                                            <th>Jumlah Karyawan</th>
                                            <th>Jumlah Scan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($karyawan as  $value)
                                            <tr>
                                                <td>{{Carbon\carbon::parse($tanggal_mulai)->format('d-M-Y')}}</td>
                                                <td>
                                                    {{$karyawan->count()}}
                                                </td>
                                                <td>
                                                    <a href="{{ url('/masukharilibur/reporting/detail/'.$karyawan->id_mhl) }}">Detail</a>
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