@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Reporting Detail</span>
                        </h3>
                        <div class="float-right">
                            <a href="javascript:history.back()" class="btn btn-lg btn-info"><i
                                    class="fas fa-arrow-circle-left"></i> kembali</a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Add file upload form -->
                        <form action="{{ route('importBatch') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="excel">Upload ulang batch:</label>
                                <input type="file" name="excel" id="excel" accept=".xls,.xlsx" required>
                                <input type="hidden" name="id_mhl" value="{{ $data->id_mhl }}">
                                <input type="hidden" name="tanggal" value="{{ $data->tanggal }}">
                            </div>
                            <button type="submit" class="btn btn-primary mb-4 p-3">Upload revisi</button>
                        </form>


                        {{-- end upload --}}

                        <table class="table table-bordered" id="tbl_report">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>NAMA</th>
                                    <th>DEPARTEMEN</th>
                                    <th>SHIFT</th>
                                    <th>WAKTU SCAN</th>
                                    <th>STATUS KARYAWAN</th>
                                    {{-- <th>EDIT</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $item->nik }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->department }}</td>
                                        <td>{{ $item->shift }}</td>
                                        <td>{{ $item->waktu_scan }}</td>
                                        <td>{{ $item->status_karyawan }}</td>
                                        {{-- <td><a href="{{ url('/masukharilibur/reporting/edit/' . $item->id) }}">Edit</a> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include necessary DataTables JS and initialize the table -->
    <script src="{{ url('/') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script type="text/javascript">
        $('#tbl_report').DataTable({
            dom: "<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>\
            <'row'<'col-sm-12'tr>>\
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        });
    </script>
@endpush
