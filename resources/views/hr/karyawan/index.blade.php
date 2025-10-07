@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@push('scripts')
<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-1 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">HR
                            <span class="d-block text-muted pt-2 font-size-sm">Manage Karyawan</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-data-terkini">
                                        <span class="nav-icon">
                                            <i class="fas fa-database"></i>
                                        </span>
                                        <span class="nav-text">Data Terkini</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-update-data">
                                        <span class="nav-icon">
                                            <i class="flaticon flaticon-rotate"></i>
                                        </span>
                                        <span class="nav-text">Update Data</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade" id="tab-data-terkini">@include('hr.karyawan.data-terkini')</div>
                            <div role="tabpanel" class="tab-pane fade show active" id="tab-update-data">@include('hr.karyawan.update-data')</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

