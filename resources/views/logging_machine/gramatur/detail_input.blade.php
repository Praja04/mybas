@extends('layouts.base-display')

@section('title', 'FORM DETAIL INPUT GRAMATUR')

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
        <div class="main-body">

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/logging_machine/index/{{ $detail->nik }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>


            <div class="row gutters-sm">
                <div class="col-sm-12">
                    <div class="card card-custom">
                        <div class="row">
                            <div class="col-sm-12">
                                <!--begin::Tiles Widget 11-->
                                <div class="card mt-4" style="border-radius: 15px">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <div class="float-right">
                                                    </div>
                                                    <tr>
                                                        <th colspan="9" class="text-center"> HISTORY TIMBANGAN </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="9" class="text-center"> {{ $detail->rasa }} </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">
                                                            JAM KE
                                                        </th>
                                                        @for ($i = 1; $i < 9; $i++)
                                                            <th class="text-center">
                                                                {{ $i }}
                                                            </th>
                                                        @endfor
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            1
                                                        </td>
                                                        {{-- LOGIK SAMPLING --}}
                                                        @foreach ($jam_ke1 as $val)
                                                            <td class="text-center">
                                                                {{ $val->sampling_ke }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">2</td>
                                                        @foreach ($jam_ke2 as $item)
                                                            <td class="text-center">
                                                                {{ $item->sampling_ke }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">3</td>
                                                        @foreach ($jam_ke3 as $item)
                                                            <td class="text-center">
                                                                {{ $item->sampling_ke }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
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
