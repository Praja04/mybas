@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">KBBM
                            <span class="d-block text-muted pt-2 font-size-sm">Karyawan Belum Boleh Masuk</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Upload New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Department</th>
                                <th>NIK</th>
                                <th>Nama Lengkap</th>
                                <th>Tanggal Boleh Masuk</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($kbbms as $key => $kbbm)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $kbbm->department }}</td>
                                    <td>{{ $kbbm->nik }}</td>
                                    <td>{{ $kbbm->nama_lengkap }}</td>
                                    <td>
                                        @if($kbbm->tanggal_masuk == '')
                                            <small class="label label-light-warning label-inline">Belum Tentu</small>
                                        @else
                                            {{ date('d/m/Y', strtotime($kbbm->tanggal_masuk)) }}
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y H:i:s', strtotime($kbbm->created_at)) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/hr/kbbm/upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>File Data Karyawan Belum Boleh Masuk</label>
                            <div></div>
                            <div class="custom-file">
                                <input accept=".xls" required name="file" type="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                        <a href="{{ url('/templates/kbbm-template.xls') }}" class="right-0 float-right hover-opacity-80"><i class="fa fa-download"></i> Download template</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.table').DataTable();

        function showModalCreateNew()
        {
            $('#modal-title').text('Upload New Data');
            $('#modal').modal('show');
        }

        $('#submitButton').on('click', function () {
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Submiting...');
            setTimeout(function () {
                $('#submitButton').html('<i class="fa fa-paper-plane"></i> Submit');
            }, 1000)
            // $(this).attr('disabled', true);
        })
    </script>

@endpush
