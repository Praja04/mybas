@extends('layouts.base-display')

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
                            <span class="card-label font-weight-bolder text-dark">List Display PT. Prakarsa Alam Segar</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">More than 400+ new members</span>
                        </h3>
                     
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">
                        <div class="tab-content">
                            <!--begin::Table-->
                            <div class="table-responsive">
                                <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                    <thead>
                                    <tr class="text-left text-uppercase">
                                        <th style="min-width: 250px" class="pl-7">
                                            <span class="text-dark-75">LIST LINK DISPLAY</span>
                                        </th>
                                        <th style="min-width: 250px" class="pl-7">
                                            <span class="text-dark-75">KETERANGAN</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="{{ url('/display/kbbm') }}">Display Karyawan Belum Boleh Masuk</a></td>
                                            <td>Untuk display scan cek karyawan udah boleh masuk atau belum</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/data-tamu') }}">Display Data Tamu</a></td>
                                            <td>Untuk display data tamu</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/secure-access/') }}">Login Secure Access</a></td>
                                            <td>Untuk login secure access parkir menggunakan username & password</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/cek-suhu/') }}">Cek Suhu</a></td>
                                            <td>Untuk data pengecekan suhu</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/pengambilan-id-card/') }}">Pengambilan ID Card</a></td>
                                            <td>Untuk pengambilan id card calon karyawan</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/pembagian/') }}">Pembagian Untuk Karyawan</a></td>
                                            <td>Untuk karyawan pengambilan product atau seragam</td>
                                        </tr>
                                        <tr>
                                            <td><a href="{{ url('/display/logging_machine/') }}">Logging Machine</a></td>
                                            <td>Untuk Request Permintaan Downtime Mesin Dan Timbangan  </td>
                                        </tr>
                                    </tbody>
                                </table>
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
