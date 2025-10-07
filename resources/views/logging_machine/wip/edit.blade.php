@extends('layouts.base-display')

@section('title', 'Work In Process(WIP)')

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
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{url('/logging_machine/update_wip/'. $detail->id)}}" method="POST">
                                @csrf
                                @method('PATCH')

                               <input type="text" name="nik" value="{{$detail->nik}}" id="" hidden> 
                               <input type="text" name="id_logging_machine" value="{{$detail->id_logging_machine}}" id="" hidden> 

                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Rasa</label>
                                                <input type="text" class="form-control" name="rasa"
                                                    value="{{ $detail->rasa }}" readonly>

                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Inner Reject(Kg)</label>
                                                <input type="text" class="form-control" name="inner_reject"
                                                    placeholder="Masukan Angka Pecahan" value="{{ $detail->inner_reject }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sampah Inner</label>
                                                <input type="text" class="form-control" name="sampah_inner"
                                                    placeholder="Masukan Anngka Pecahan" value="{{ $detail->sampah_inner }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Total WIP</label>
                                                <input type="text" class="form-control" name="total_wip"
                                                    placeholder="Masukan Hanya Angka.."  value="{{ $detail->total_wip }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sortir</label>
                                                <input type="text" class="form-control" name="sortir"
                                                    placeholder="Masukan Hanya Angka" value="{{ $detail->sortir }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sobek</label>
                                                <input type="text" class="form-control" name="sobek"
                                                    placeholder="Masukan Hanya Angka" value="{{ $detail->sobek }}">
                                            </div>
                                        </div>
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

                                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Apakah Anda Yakin?</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
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
    </div>




@endsection

@push('scripts')


@endpush