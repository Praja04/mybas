@extends('layouts.base-display')

@section('title', 'Detail Permintaan Downtime')

    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

        </style>
    @endpush

@section('content')
    <div class="container-fluid">

        <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
            <!--begin::Item-->
            <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                data-placement="right">
                <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                    href="/logging_machine/index/{{ $data->nik }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
        </ul>

        <div class="row">
            <div class="col-md-12">
                <div class="card" style="border-radius: 25px;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card" style="border-radius: 23px;">
                                    <div class="card-body">
                                        <a href="/logging_machine/index/{{ $data->nik }}"
                                            class="btn btn-info btn-sm mb-2"> <i class="fas fa-arrow-left"></i> kembali</a>
                                        <table class="table">
                                            <thead class="bg-primary">
                                                <tr>
                                                    <th colspan="2" class="text-white"><i
                                                            class="fas fa-info-circle text-white"></i> Informasi Permintaan
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Nama:</th>
                                                    <td>{{ $data->nama }}</td>
                                                </tr>
                                                <tr>
                                                    <th>NIK:</th>
                                                    <td>{{ $data->nik }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal Permintaan:</th>
                                                    <td>{{ $data->tgl_pengisian }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jam Permintaan:</th>
                                                    <td>{{ $data->jam_pengisian }} WIB</td>
                                                </tr>
                                                <tr>
                                                    <th>Detail Kerusakan:</th>
                                                    <td>{{ $data->kerusakan }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="card" style="border-radius: 23px;">
                                    <div class="card-body">
                                        <br>
                                        <br>
                                        @if ($data->approval_maintenance == 1)
                                            <table class="table">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th colspan="2" class="text-white"><i
                                                                class="fas fa-hard-hat text-white"></i> Progress Permintaan
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th>Status:</th>
                                                        <td><span class="badge badge-pill badge-warning">Belum Di
                                                                Respon</span></td>
                                                    </tr>

                                                    </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th colspan="2" class="text-white"><i
                                                                class="fas fa-hard-hat text-white"></i> Progress Permintaan
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th>Nama Maintenance:</th>
                                                        <td>{{ $data->approval_maintenance_nama }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Respon Maintenance:</th>
                                                        <td>{{ $data->tgl_respon_maintenance }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jam Mulai Perbaikan:</th>
                                                        <td>{{ $data->jam_mulai_maintenance }} WIB</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jam Selesai Perbaikan:</th>
                                                        @if ($data->jam_selesai_maintenance == null)
                                                            <td> Proses Perbaikan</td>
                                                        @else
                                                            <td>{{ $data->jam_selesai_maintenance }} WIB</td>
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <th style="background-color: #FFD700">Remarks Maintenance:</th>
                                                        @if ($data->approval_maintenance_remarks == null)
                                                            <td style="background-color: #FFD700">-</td>
                                                        @else
                                                            <td style="background-color: #FFD700">
                                                                {{ $data->approval_maintenance_remarks }}</td>
                                                        @endif
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endif

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('scripts')


@endpush
<script type="text/javascript">


</script>
