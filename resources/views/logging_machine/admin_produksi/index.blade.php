@extends('layouts.base-display')

@section('title', 'DASHBOARD ADMIN PRODUKSI SEASSONING 2')

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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="/">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-8">
                    <center>
                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <!--begin::Tiles Widget 11-->
                                        <div class="card card-custom bg-primary gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">

                                                <i class="fas fa-hard-hat fa-icon-white" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/adm_prod/master_mesin/"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">MASTER
                                                    MESIN</a>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                    <div class="col-sm-6">
                                       <div class="card card-custom bg-success gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="fas fa-list" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/adm_prod/get_master_reason"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">MASTER
                                                    REASON DOWNTIME</a>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <!--begin::Tiles Widget 11-->
                                        <div class="card card-custom bg-dark gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="fa fa-balance-scale" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/adm_prod/master_sampling"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">MASTER
                                                    WAKTU SAMPLING</a>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                    <div class="col-sm-6">
                                        <!--begin::Tiles Widget 11-->
                                        <div class="card card-custom bg-info gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="far fa-clipboard" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/adm_prod/master_varian"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">MASTER
                                                    VARIAN RASA</a>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                    {{-- <div class="col-sm-12">
                                            <div class="card card-custom bg-success gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="fas fa-list" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/adm_prod/get_master_reason"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">MASTER
                                                    REASON DOWNTIME</a>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </center>
                </div>
            </div>
        </div>
    </div>



    @endsection

    @push('scripts')
        <script type="text/javascript">


        </script>

    @endpush
