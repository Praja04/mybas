@extends('layouts.base-display')

@section('title', 'DASHBOARD TABLET')

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

    <div class="container">

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

        <div class="main-body">
            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">


                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="mt-3">
                                    <h4>{{ $data->nama }}</h4>
                                    <p class="text-seconamadary mb-1">NIK: {{ $data->nik }}</p>
                                    <hr>
                                     <div class="alert alert-primary" role="alert">
                                       <i class="fas fa-info-circle text-white"></i> INFORMASI
                                       <hr style="background-color: white">
                                       HARAP UNTUK SAMPLING DI INPUT SESUAI JAM YANG SUDAH DI TENTUKAN, KARENA JIKA TERLEWAT SISTEM AKAN MERECORD KETERTINGGALAN WAKTU SAMPLING.
                                       <hr style="background-color: white">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card mb-3">

                        <div class="row">
                            <div class="col-sm-12">
                                <!--begin::Tiles Widget 11-->
                                <div class="card" style="border-radius: 15px">
                                    <div class="card-body">
                                        @if ($cek_timbangan != NULL)
                                            <a href="/logging_machine/detail_input/{{ Crypt::encrypt($data->id) }}"
                                                class="btn btn-sm btn-info mb-3" style="border-radius: 18px"><i class="fas fa-history"></i> History
                                                Sampling Hari ini</a>
                                        @endif

                                        <div class="float-right">
                                            @if ($selesai_sampling)
                                            <div class="alert alert-primary mt-4" role="alert">
                                                <strong>Kamu Sudah Selesai Memasukan Sampling Terakhir, Terimakasih..</strong>
                                            </div>
                                            @else
                                            <a href="/logging_machine/form_input_gramatur/{{$data->pindah_varian}}/{{$data->no_mesin}}/{{ Crypt::encrypt($data->nik) }}"
                                                class="btn btn-lg btn-info mb-3" style="border-radius: 18px"><i class="fas fa-plus"></i> Masukan
                                                Sampling</a>
                                                @endif
                                            </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <div class="float-right">
                                                </div>
                                                <tr>
                                                    <th colspan="3" class="text-center">JADWAL SAMPLING GRAMATUR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="text-center">
                                                        Shift
                                                    </th>
                                                    <th class="text-center">
                                                        Timbangan Ke
                                                    </th>
                                                    <th class="text-center">
                                                        Jam Timbangan
                                                    </th>
                                                </tr>
                                                @foreach ($jadwal as $item)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $item->shift }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $item->jam_ke }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }} WIB
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--end::Tiles Widget 11-->
                            </div>
                        </div>

                        <!--end::Tiles Widget 11-->
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
