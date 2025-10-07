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
                        <span class="card-label font-weight-bolder text-dark">Proses Scan </span>
                    </h3>
                </div>
                <form action={{url('/ProsesScan')}} method="POST">
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
                                <input type="date" name="tanggal" class="form-control">
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
                                <table class="table table-bordered" id="tbl_pencarian">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>RF_ID</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Waktu</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                        </tr>
                                    </thead>
                                    
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
        $('#tbl_pesanan').DataTable
    </script>
@endpush