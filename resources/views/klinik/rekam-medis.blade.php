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
                <div class="card-header flex-wrap">
                    <div class="card-title">
                        <h3 class="card-label">Klinik > Rekam Medis Karyawan</h3>
                    </div>
                    <div class="card-toolbar">
                    </div>
                </div>
                <div class="card-body">
                    <form action="" class="mb-10">
                        <div class="d-flex">
                            <input value="{{ isset($_GET['nik']) ? $_GET['nik'] : '' }}" required name="nik" type="text" class="form-control" placeholder="Masukan NIK Karyawan">
                            <button class="btn btn-primary ml-2">Cari</button>
                        </div>
                    </form>
                    {{-- <hr /> --}}
                    @if(isset($_GET['nik']))
                        @if($data == null)
                        <div class="alert alert-warning">
                            Data Karyawan dengan nik tersebut tidak ditemukan
                        </div>
                        @else
                        <div>
                            <div class="employee-info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6 class="border-bottom pb-3">Detail Karyawan</h6>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <table class="table table-borderless table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>NIK</th>
                                                    <td>:</td>
                                                    <td>{{ $data->NIK }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama</th>
                                                    <td>:</td>
                                                    <td>{{ $data->EMPNM }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Departemen</th>
                                                    <td>:</td>
                                                    <td>{{ $data->DEPTID }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="rekam-medis-info mt-5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h6 class="border-bottom pb-3">Rekam Medis</h6>
                                    </div>
                                </div>
                                @if(count($data->rekamMedis) == 0)
                                <div class="alert alert-info">
                                    Belum ada catatan medis untuk karyawan ini
                                </div>
                                @else
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr class="bg-dark text-white">
                                                    <th>Keluhan</th>
                                                    <th>Diagnosa</th>
                                                    <th>Tindakan</th>
                                                    <th>Suhu</th>
                                                    <th>Tensi</th>
                                                    <th>Tanggal</th>
                                                    <th>Jam</th>
                                                    <th>PIC</th>
                                                    <th>Dokter</th>
                                                    <th>Keterangan</th>
                                                    <th>Jenis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data->rekamMedis as $pemeriksaan)
                                                <tr>
                                                    <td>{{ $pemeriksaan->keluhan }}</td>
                                                    <td>{{ $pemeriksaan->data_diagnosa->nama_diagnosa }}</td>
                                                    <td>{{ $pemeriksaan->tindakan }}</td>
                                                    <td>{{ $pemeriksaan->suhu }}</td>
                                                    <td>{{ $pemeriksaan->tensi }}</td>
                                                    <td>{{ formatTanggalIndonesia2($pemeriksaan->tanggal_pemeriksaan) }}</td>
                                                    <td>{{ $pemeriksaan->waktu_pemeriksaan }}</td>
                                                    <td>{{ $pemeriksaan->pic }}</td>
                                                    <td>{{ $pemeriksaan->dokter }}</td>
                                                    <td>{{ $pemeriksaan->keterangan }}</td>
                                                    <td>
                                                        <span class="label label-inline @if($pemeriksaan->jenis_pemeriksaan == 'skd') label-light-danger @else label-light-warning @endif">{{ $pemeriksaan->jenis_pemeriksaan }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
</div>
@endsection

@push('scripts')
@endpush