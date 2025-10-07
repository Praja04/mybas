@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-9">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Reporting</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action={{ url('/masukharilibur/reporting') }} method="GET">
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
                                                <th>Tanggal Masuk</th>
                                                <th>Jumlah Data Karyawan Masuk</th>
                                                <th>Sudah Scan</th>
                                                <th>Belum/Tidak Scan</th>
                                                <th>Nama Approval</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Status</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($master as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->tanggal }}</td>
                                                    <td>{{ $item->jumlah_karyawan }}</td>
                                                    <td>{{ $item->jumlah_scan }}</td>
                                                    <td>{{ $item->tidak_scan }}</td>
                                                    <td>{{ $item->nama_approval }}</td>
                                                    <td>{{ $item->created_by }}</td>
                                                    <td>
                                                        @if ($item->status == 0)
                                                            <button type="submit" class="btn btn-sm text-white"
                                                                style="background-color: rgb(60, 246, 40); border-radius: 15px;">Approved</button>
                                                        @elseif($item->status == 1)
                                                            <button type="submit" class="btn btn-sm text-balck"
                                                                style="background-color: rgb(243, 255, 6); border-radius: 15px;">Pending</button>
                                                        @else
                                                            <button type="submit" class="btn btn-sm text-white"
                                                                style="background-color: rgb(246, 20, 20); border-radius: 15px;">Rejected</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/masukharilibur/reporting/detail/' . $item->id_mhl) }}"
                                                            class="btn btn-sm text-white"
                                                            style="background-color: rgb(35, 69, 207); border-radius: 15px;">Detail</a>
                                                    </td>
                                                </tr>
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
            <div class="col-lg-3">
                <!--begin::Summary Table Widget-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Summary</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Shift</th>
                                    <th>Jumlah Karyawan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalKaryawan = 0; ?>
                                <!-- Variabel untuk menyimpan total karyawan -->
                                @foreach ($summary as $data)
                                    <tr>
                                        <td>{{ $data->status_karyawan }}</td>
                                        <td>Shift {{ $data->shift }}</td>
                                        <td>{{ $data->jumlah_karyawan }}</td>
                                    </tr>
                                    <?php $totalKaryawan += $data->jumlah_karyawan; ?>
                                    <!-- Menambahkan jumlah karyawan ke total -->
                                @endforeach
                                <tr>
                                    <td colspan="2" align="right"><strong>Total Karyawan:</strong></td>
                                    <td><strong>{{ $totalKaryawan }}</strong></td> <!-- Menampilkan total karyawan -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Summary Table Widget-->
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
