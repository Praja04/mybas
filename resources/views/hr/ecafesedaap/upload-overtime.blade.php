@extends('layouts.base')

@section('content')
<div class="container-fluid">

    <!--begin::Row-->
    <div class="row">

        <div class="col-lg-12">
            <!--begin::Advance Table Widget 4-->    
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <h3 class="card-title">
                        <span class="card-label font-weight-bolder text-dark">Upload Overtime </span>
                    </h3>
                    <div class="card-toolbar">
                        <button data-target="#uploadModal" data-toggle="modal" class="btn btn-sm btn-success font-weight-bold">
                            <i class="flaticon2-cube"></i>Upload Overtime
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label class="form-label col-sm-1 pt-3 text-left">Tanggal</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input @if(isset($_GET['tanggal'])) value="{{ $_GET['tanggal'] }}" @endif id="filter-tanggal" type="date" name="tanggal_mulai" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tbl_overtime">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>NAMA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataOvertime as $key => $value)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $value->nik }}</td>
                                            <td>{{ $value->nama }}</td>
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

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Overtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input required name="tanggal" id="tanggal" type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="file">Pilih File</label>
                        <input required name="file" type="file" id="file" class="form-control">
                    </div>
                    <a href="{{ asset('templates/template-upload-overtime.xlsx') }}">Download template excel</a>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary font-weight-bold">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ url('/') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#tbl_overtime').DataTable();

        $('#filter-tanggal').on('change', function() {
            window.location.href = "{{ url('/ecafesedaap/upload-overtime') }}?tanggal=" + $(this).val();
        });
    </script>
@endpush