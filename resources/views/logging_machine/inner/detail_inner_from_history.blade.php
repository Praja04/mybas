@extends('layouts.base-display')

@section('title', 'INNER LOT')

    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

            .page-header {
                border-bottom: 2px solid #b2b7bd;
            }

            .page-header {
                border-bottom: 2px solid #b2b7bd;
            }

            h1 {
                color: #008b57;
                font-family: 'Open Sans', sans-serif;
                font-weight: 600;

            }

            .timeline {

                font-weight: 400;
                list-style: none;
                padding: 20px 0 20px;
                position: relative;
            }

            .timeline:before {
                top: 0;
                bottom: 0;
                position: absolute;
                content: " ";
                width: 3px;
                background-color: #b10000;
                left: 50%;
                margin-left: -1.5px;
            }

            .timeline>li {
                margin-bottom: 20px;
                position: relative;

            }

            .timeline>li:before,
            .timeline>li:after {
                content: " ";
                display: table;

            }

            .timeline>li:after {
                clear: both;
            }

            .timeline>li:before,
            .timeline>li:after {
                content: " ";
                display: table;
            }

            .timeline>li:after {
                clear: both;
            }

            .timeline>li>.timeline-panel {
                width: 46%;
                float: left;
                border: 1px solid #dbdbdb;
                border-radius: 12px;
                padding: 20px;
                position: relative;
                -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
                box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
                background: #fff;

            }

            .timeline>li>.timeline-panel:before {
                position: absolute;
                top: 26px;
                right: -15px;
                display: inline-block;
                border-top: 15px solid transparent;
                border-left: 15px solid #ccc;
                border-right: 0 solid #ccc;
                border-bottom: 15px solid transparent;
                content: " ";
            }

            .timeline>li>.timeline-panel:after {
                position: absolute;
                top: 27px;
                right: -14px;
                display: inline-block;
                border-top: 14px solid transparent;
                border-left: 14px solid #fff;
                border-right: 0 solid #fff;
                border-bottom: 14px solid transparent;
                content: " ";
            }

            .timeline>li>.timeline-badge {
                width: 20px;
                height: 20px;
                line-height: 20px;
                font-size: 1.4em;
                text-align: center;
                position: absolute;
                top: 30px;
                left: 50%;
                margin-left: -10px;
                background-color: #108c58;
                z-index: 100;
                border-top-right-radius: 50%;
                border-top-left-radius: 50%;
                border-bottom-right-radius: 50%;
                border-bottom-left-radius: 50%;
                border: 4px solid #eaeaea;
            }

            .timeline>li.timeline-inverted>.timeline-panel {
                float: right;
            }

            .timeline>li.timeline-inverted>.timeline-panel:before {
                border-left-width: 0;
                border-right-width: 15px;
                left: -15px;
                right: auto;
            }

            .timeline>li.timeline-inverted>.timeline-panel:after {
                border-left-width: 0;
                border-right-width: 14px;
                left: -14px;
                right: auto;
            }

            .timeline-title {
                margin-top: 0;
                color: inherit;
                font-family: 'Open Sans', sans-serif;
                font-weight: 600;
            }

            .timeline-body>p,
            .timeline-body>ul {
                margin-bottom: 0;
            }

            .timeline-body>p+p {
                margin-top: 5px;
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
                        href="/logging_machine/index/{{ $cek_inner[0]->nik }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>


            <div class="row gutters-sm">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                              <div class="col-md-12">
                               <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">No.</th>
                                                <th>No.Mesin</th>
                                                <th>Shift/Group</th>
                                                <th>Varian</th>
                                                <th>Tanggal Upload</th>
                                                <th>Jam Upload</th>
                                                <th>Tanggal Edit </th>
                                                <th>Jam Edit</th>
                                                <th>Dokumen</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cek_inner as $list)
                                                <tr>
                                                    <td>
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td>
                                                        {{ $list->no_mesin }}
                                                    </td>
                                                    <td>
                                                        {{ $list->rasa }}
                                                    </td>
                                                    <td>
                                                        {{ $list->shift_group }}
                                                    </td>
                                                    <td>
                                                        {{ Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y') }}
                                                    </td>
                                                    <td>
                                                        {{ Carbon\Carbon::parse($list->jam_pengisian)->format('h:i') }} WIB
                                                    </td>
                                                     <td>
                                                        {{ Carbon\Carbon::parse($list->tgl_edit)->format('d-M-Y') }}
                                                    </td>
                                                    <td>
                                                        {{ Carbon\Carbon::parse($list->jam_edit)->format('h:i') }} WIB
                                                    </td>
                                                    <td>
                                                        <div class="symbol symbol-50 symbol-light mr-4">
                                                            <img class="h-75 align-self-end"
                                                                src="{{ asset('dokumen_lot/' . $list->foto) }}" />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="/logging_machine/detail_inner/{{ Crypt::encrypt($list->id) }}"
                                                            class="btn btn-primary btn-sm" style="border-radius: 10px"><i
                                                                class="fas fa-eye"></i>
                                                            Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
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






@endsection

@push('scripts')

@endpush
