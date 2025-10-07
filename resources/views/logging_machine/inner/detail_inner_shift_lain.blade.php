@extends('layouts.base-display')

@section('title', 'DETAIL INNER SHIFT LAIN')

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

            <div class="row gutters-sm">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="card-header">
                                <div class="float-right">
                                    <a href="javascript:history.back()" class="btn btn-info btn-sm mb-3"
                                        style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="timeline">
                                        <li>
                                            <div class="timeline-badge"></div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading">
                                                    <h4 class="timeline-title">Tanggal Upload:
                                                        {{ Carbon\Carbon::parse($join->tgl_pengisian)->format('d-M-Y') }}
                                                    </h4>
                                                    <p><small class="text-dark"><i class="glyphicon glyphicon-time"></i> Jam
                                                            Upload:
                                                            {{ Carbon\Carbon::parse($join->jam_pengisian)->format('H:i') }}
                                                            WIB</small></p>
                                                    <hr>
                                                    <h4 class="timeline-title">Tanggal Edit:
                                                        {{ Carbon\Carbon::parse($join->tgl_edit)->format('d-M-Y') }}</h4>
                                                    <p><small class="text-dark"><i class="glyphicon glyphicon-time"></i> Jam
                                                            Edit:
                                                            {{ Carbon\Carbon::parse($join->jam_edit)->format('H:i') }}
                                                            WIB</small></p>

                                                </div>
                                                <div class="timeline-body">
                                                    <hr>

                                                    <p><small class="text-dark"><i class="glyphicon glyphicon-time"></i>
                                                            Nama : {{ $join->nama }} </small></p>

                                                    <p><small class="text-dark"><i class="glyphicon glyphicon-time"></i>
                                                            Shift/Group : {{ $join->shift_group }} </small></p>

                                                    <hr>
                                                    <p>Dokumen Ditemukan.</p>
                                                    <a href="#show" data-toggle="modal" class="btn btn-primary btn-sm mt-4"
                                                        style="border-radius: 10px"><i class="fas fa-eye"></i> Lihat
                                                        Dokumen</a>
                                                </div>
                                                <div class="timeline-body">
                                                    <a href="#edit" data-toggle="modal" class="btn btn-primary btn-sm mt-4"
                                                        style="border-radius: 10px"><i class="fas fa-edit"></i> Edit
                                                        Dokumen</a>
                                                </div>
                                            </div>
                                        </li>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="avatar" class="editable img-responsive" src="{{ asset('dokumen_lot/' . $join->foto) }}"
                        style="width: 100%" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <form action="/logging_machine/update_inner/{{ Crypt::encrypt($join->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Edit Tracebility</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlFile1">Pilih File</label>
                            <input type="file" class="form-control" name="foto" accept="image/*" capture>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>






@endsection

@push('scripts')

@endpush
