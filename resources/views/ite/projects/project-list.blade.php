@extends('layouts.base')

@push('styles')
    <style type="text/css">
        .selesai {
            display: none
        }
        .project {
            transition-duration: 0.2s
        }
        .project:hover {
            box-shadow: 20px 20px 20px 0px rgba(34, 27, 43, 0.1)
        }
        #orders-panel.offcanvas, #jobs-panel.offcanvas{
            width: 500px;
            right: -510px;
        }
        .offcanvas-on {
            right: 0 !important
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-5">
                        <div class="card-title">
                            <h3 class="card-label">Hybrid Project Manager
                                <span class="d-block text-muted pt-2 font-size-sm">Manage semua project PT. PAS</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-5">
            <div class="col-md-3">
                <label class="checkbox bg-white p-1 rounded">
                    <input id="toggle-done" type="checkbox" name="show-done">
                    <span></span> Tampilkan yang sudah selesai
                </label>
            </div>
        </div>
        {{-- Here gona bi some dashboard or something --}}
        <div class="row mt-5">
            @foreach($projects as $key => $project)
                <div class="col-sm-3 @if($project->status == 3) selesai @endif">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b card-stretch project">
                        <!--begin::Body-->
                        <div class="card-body p-5">
                            <!--begin::Info-->
                            <div class="d-flex align-items-center">
                                <!--begin::Pic-->
                                <div class="flex-shrink-0 symbol symbol-60 mr-5 symbol-circle">
                                    <span class="symbol symbol-35 symbol-light-warning">
                                        <span class="symbol-label font-size-h5 font-weight-bold text-dark">{{ $project->department->name }}</span>
                                    </span>
                                </div>
                                <!--end::Pic-->
                                <!--begin::Info-->
                                <div class="d-flex flex-column mr-auto">
                                    <!--begin: Title-->
                                    <div class="d-flex flex-column mr-auto">
                                        <a href="javascript:" class="text-dark text-hover-primary font-size-h6 font-weight-bolder mb-1">{{ $project->name }}</a>
                                        <span class="text-muted font-weight-bold">{{ $project->pic }}</span>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Info-->
                                <!--begin::Toolbar-->
                                <div class="card-toolbar mb-10">
                                    <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                                        <a href="#" class="btn btn-clean btn-hover-light-info btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ki ki-bold-more-hor"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                            <!--begin::Navigation-->
                                            <ul class="navi navi-hover">
                                                <li class="navi-header pb-1">
                                                    <span class="text-primary text-uppercase font-weight-bold font-size-sm">Actions:</span>
                                                </li>
                                                <li class="navi-item">
                                                    <a href="#" class="navi-link">
                                                        <span class="navi-icon">
                                                            <i class="flaticon2-trash"></i>
                                                        </span>
                                                        <span class="navi-text">Delete</span>
                                                    </a>
                                                </li>
                                                <li class="navi-item">
                                                    <a href="#" class="navi-link">
                                                        <span class="navi-icon">
                                                            <i class="flaticon2-edit"></i>
                                                        </span>
                                                        <span class="navi-text">Edit</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <!--end::Navigation-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Toolbar-->
                            </div>
                            <div class="d-flex flex-wrap mt-5">
                                <div class="mr-5 d-flex flex-column mb-7">
                                    <span class="d-block font-weight-bold mb-4">Start Date</span>
                                    <span class="btn btn-light-secondary btn-sm font-weight-bold btn-upper btn-text text-dark">{{ formatTanggalIndonesia($project->start_date) }}</span>
                                </div>
                                <div class="mr-5 d-flex flex-column mb-2">
                                    <span class="d-block font-weight-bold mb-4">End Date</span>
                                    <span class="btn btn-light-danger btn-sm font-weight-bold btn-upper btn-text">{{ formatTanggalIndonesia($project->target_date) }}</span>
                                </div>
                                <!--begin::Progress-->
                                <div class="flex-row-fluid mb-7">
                                    <span class="d-block font-weight-bold mb-4">Progress</span>
                                    <div class="d-flex align-items-center pt-2">
                                        <div class="progress progress-xs mt-2 mb-2 w-100">
                                            <div class="progress-bar @if($project->status == 3) bg-success @endif" role="progressbar" style="width: @if($project->status == 3) 100%; @else 0%; @endif" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3 font-weight-bolder">@if($project->status == 3) 100% @else 0% @endif</span>
                                    </div>
                                </div>
                                <!--end::Progress-->
                            </div>
                            <div class="d-flex">
                                <div class="d-flex align-items-center mr-7">
                                    <i class="flaticon2-list-3"></i>
                                    <a onClick="openJobsPanel('{{ $project->id }}')" href="javascript:" class="font-weight-bolder text-dark ml-2">{{ count($project->jobs) }} Jobs</a>
                                </div>
                                <div class="d-flex align-items-center mr-7">
                                    <i class="flaticon2-shopping-cart-1"></i>
                                    <a onClick="openOrdersPanel('{{ $project->id }}')" href="javascript:" class="font-weight-bolder text-info ml-2">Material</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Card-->
                </div>
            @endforeach
        </div>
    </div>

    <div id="orders-panel" class="offcanvas offcanvas-right p-10">
        <!--begin::Header-->
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
            <h3 class="font-weight-bold m-0"><i class="flaticon2-shopping-cart-1"></i>  Material Orders</h3>
            <a onClick="closeOrdersPanel()" href="javascript:" class="btn btn-xs btn-icon btn-light btn-hover-primary">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="separator separator-dashed mt-8 mb-5"></div>
        <div class="navi navi-spacer-x-0 p-0">
            <!--begin::Item-->
            <a href="javascript:" class="navi-item">
                <div class="navi-link">
                    <div class="symbol symbol-40 bg-light mr-3">
                        <div class="symbol-label">
                            <i class="flaticon2-shopping-cart-1"></i>
                        </div>
                    </div>
                    <div class="navi-text">
                        <div class="font-weight-bold">30001225425</div>
                        <div class="text-muted">05 Desember 2020</div>
                    </div>
                </div>
            </a>
        </div>
        <!--end::Nav-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-7"></div>
    </div>

    <div id="jobs-panel" class="offcanvas offcanvas-right p-10">
        <!--begin::Header-->
        <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
            <h3 class="font-weight-bold m-0"><i class="flaticon2-list-3"></i>  Jobs List</h3>
            <a onClick="closeJobsPanel()" href="javascript:" class="btn btn-xs btn-icon btn-light btn-hover-primary">
                <i class="ki ki-close icon-xs text-muted"></i>
            </a>
        </div>
        <div class="separator separator-dashed mt-8 mb-5"></div>
        <div class="table-responsive">
            <!--begin::Items-->
            <div class="list list-hover min-w-400px">
                <div id="jobs-container"></div>
            </div>
            <!--end::Items-->
        </div>
        <div class="separator separator-dashed my-7"></div>
    </div>

@endsection

@push("scripts")
    <script type="text/javascript">
        $("#toggle-done").on("change", function() {
            if($("#toggle-done:checked").length > 0) {
                $(".selesai").show();
            }else{
                $(".selesai").hide();
            }
        });

        function closeOrdersPanel()
        {
            $("#orders-panel").removeClass("offcanvas-on");
            $("#orders-panel-overlay").remove();
        }

        function closeJobsPanel()
        {
            $("#jobs-panel").removeClass("offcanvas-on");
            $("#jobs-panel-overlay").remove();
        }

        function openOrdersPanel(id)
        {
            $("#orders-panel").addClass("offcanvas-on");
            $("body").append("<div id='orders-panel-overlay' class='offcanvas-overlay orders-panel-overlay'></div>");
            $(".offcanvas-overlay").on("click", function () {
                $("#orders-panel").removeClass("offcanvas-on");
                $("#orders-panel-overlay").remove();
            });
        }

        function openJobsPanel(id)
        {
            var jobs = '<div class="d-flex align-items-start list-item card-spacer-x py-2 px-2">';
                    jobs += '<div class="d-flex mt-1 mr-2">';
                        jobs += '<div class="symbol symbol-light-secondary symbol-30 mr-3">';
                            jobs += '<span class="symbol-label font-weight-bolder text-dark-50">01</span>';
                        jobs += '</div>';
                    jobs += '</div>';
                    jobs += '<div class="flex-grow-1 mt-1 mr-2">';
                        jobs += '<div class="font-weight-bolder mr-2">Pemasangan Kamera CCTV rollpress</div>';
                    jobs += '</div>';
                    jobs += '<div class="d-flex align-items-center justify-content-end flex-wrap">';
                        jobs += '<div class="font-weight-bold text-muted">05 Desember 2020</div>';
                        jobs += '<div class="symbol-group symbol-hover py-2">';
                            jobs += '<div class="symbol symbol-30" data-toggle="tooltip" data-placement="top" title="" data-original-title="Alice Stone">';
                                jobs += '<img alt="Pic" src="{{ asset('/images/1569326300-arifin.JPG') }}">';
                            jobs += '</div>';
                            jobs += '<div class="symbol symbol-30" data-toggle="tooltip" data-placement="top" title="" data-original-title="Anna Krox">';
                                jobs += '<img alt="Pic" src="{{ asset('/images/1569326253-WhatsApp Image 2019-09-24 at 18.57.12.jpeg') }}">';
                            jobs += '</div>';
                            jobs += '<div class="symbol symbol-30" data-toggle="tooltip" data-placement="top" title="" data-original-title="Nick Nilson">';
                                jobs += '<img alt="Pic" src="{{ asset('/images/1569326392-rohmat.JPG') }}">';
                            jobs += '</div>';
                        jobs += '</div>';
                    jobs += '</div>';
                jobs += '</div>';
            $('#jobs-container').html(jobs);
            $("#jobs-panel").addClass("offcanvas-on");
            $("body").append("<div id='jobs-panel-overlay' class='offcanvas-overlay orders-panel-overlay'></div>");
            $(".offcanvas-overlay").on("click", function () {
                $("#jobs-panel").removeClass("offcanvas-on");
                $("#jobs-panel-overlay").remove();
            });
        }
    </script>
@endpush