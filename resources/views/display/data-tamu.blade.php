@extends('layouts.base-display')

@section('title', 'DISPLAY DATA TAMU')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@push('styles')
    <style type="text/css">
        .hide {
            visibility: hidden;
        }
    </style>
@endpush

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
                            <span class="card-label font-weight-bolder text-dark">DISPLAY DATA TAMU</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Pastikan tamu yang datang boleh masuk</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Lengkap</th>
                                <th>Nama Instansi</th>
                                <th>Jenis Kunjungan</th>
                                <th>No Identitas</th>
                                <th>Tanggal</th>
                                <th>Bertemu Dengan</th>
                                <th>Keterangan</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data_tamu as $key => $data)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $data->nama }}</td>
                                    <td>{{ $data->nama_instansi }}</td>
                                    <td>{{ $data->jenis_kunjungan }}</td>
                                    <td>{{ $data->no_identitas }}</td>
                                    <td>{{ date('d M Y', strtotime($data->tanggal)) }}</td>
                                    <td>{{ $data->bertemu_dengan }}</td>
                                    <td>
                                        @if($data->jawaban_pertanyaan_4 == 'Ya' || $data->jawaban_pertanyaan_5 == 'Ya' || $data->jawaban_pertanyaan_6 == 'Ya')
                                            <span class="label label-inline label-danger" style="white-space: nowrap">Tidak Boleh</span>
                                        @else
                                            <span class="label label-inline label-success" style="white-space: nowrap">Boleh</span>
                                        @endif
                                    </td>
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
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">
        $('.table').DataTable();
    </script>
@endpush
