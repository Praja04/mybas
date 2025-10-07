@extends('layouts.base-display')

@section('title', 'FORM EDIT MASTER MESIN')

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


            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <a href="/logging_machine/adm_prod/master_mesin" class="btn btn-danger btn-sm mb-3"
                                style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                            <form action="/logging_machine/adm_prod/update_master_mesin/{{ $data->id }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="kolom_item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Line</label>
                                                <select class="form-control" name="line" required>
                                                    <option selected value="{{ $data->line }}">{{ $data->line }}
                                                    </option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    @foreach ($line as $item)
                                                        <option value="{{ $item->line }}">{{ $item->line }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Group</label>
                                                <select class="form-control" name="group" required>
                                                    <option selected value="{{ $data->group }}">{{ $data->group }}
                                                    </option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    @foreach ($group as $item)
                                                        <option value="{{ $item->group }}">{{ $item->group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">No.Mesin</label>
                                                <input type="text" class="form-control" name="no_mesin"
                                                    value="{{ $data->no_mesin }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">NoSeq</label>
                                                <input type="number" class="form-control" name="NoSeq"
                                                    value="{{ $data->NoSeq }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Jenis Mesin</label>
                                                <select class="form-control" name="jenis_mesin" required>
                                                    <option selected value="{{ $data->jenis_mesin }}">
                                                        {{ $data->jenis_mesin }}
                                                    </option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    <option value="Packing">Packing</option>
                                                    <option value="Proses">Proses</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                        </div>
                        <div class="row">
                            <div class="col-sm-5"></div>
                            <div class="col-sm-4"></div>
                            <div class="col-sm-3">
                                <button type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                    class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i
                                        class="fas fa-save"></i> Simpan</button>
                            </div>
                        </div>
                        <hr>
                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Apakah Data Sudah Benar?
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                            Ya, Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>





@endsection

@push('scripts')
<script src="{{ url('/') }}/assets/js/highcharts/highcharts.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/drilldown.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/exporting.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/export-data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/accessibility.js"></script>

@endpush
