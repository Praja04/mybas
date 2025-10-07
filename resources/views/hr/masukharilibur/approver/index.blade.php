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
                            <span class="card-label font-weight-bolder text-dark">Approver</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action={{ url('/masukharilibur/approver') }} method="GET">
                            <div class="row">
                                <label class="form-label col-sm-1 pt-3 text-right">Tanggal</label>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <input type="date" value="{{ $_GET['tanggal'] ?? '' }}" name="tanggal"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form group">
                                        <button type="submit" class="btn btn-info btn-lg">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tbl_report">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Action</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Jumlah Data Karyawan Masuk</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($master as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ url('/masukharilibur/approve/' . $item->id_mhl) }}"
                                                            class="btn btn-sm text-white"
                                                            onclick="return confirm('Apakah Anda Yakin Akan Approve Data Ini?');"
                                                            style="background-color: rgb(60, 246, 40); border-radius: 15px;">Approv</a>
                                                        |
                                                        <a href="{{ url('/masukharilibur/reject/' . $item->id_mhl) }}"
                                                            class="btn btn-sm text-white"
                                                            onclick="return confirm('Apakah Anda Yakin Akan Reject Data Ini?');"
                                                            style="background-color: rgb(246, 20, 20); border-radius: 15px;">Reject</a>

                                                    </td>
                                                    <td>{{ $item->tanggal }}</td>
                                                    <td>{{ $item->created_by }}</td>
                                                    <td>{{ $item->jumlah_karyawan }}</td>
                                                    <td>
                                                        <a href="{{ url('/masukharilibur/approver/detail/' . $item->id_mhl) }}"
                                                            class="btn btn-sm text-white"
                                                            style="background-color: rgb(35, 69, 207); border-radius: 15px;">Detail</a>
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
