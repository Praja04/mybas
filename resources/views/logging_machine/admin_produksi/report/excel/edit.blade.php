@extends('layouts.base-display')

@section('title', 'Form Edit')

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
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success"
                        href="/logging_machine/adm_prod/report">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>


            <div class="row gutters-sm">
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('/logging_machine/adm_prod/update_file/'. $detail->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Prod Ord</label>
                                            <input type="text" class="form-control" value="{{ $detail->prod_order }}" name="prod_order">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Varian</label>
                                            <input type="text" class="form-control" value="{{ $detail->varian }}"
                                                name="varian">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Shift</label>
                                            <select class="form-control" name="shift">
                                                <option value="{{$detail->shift}}" selected>{{$detail->shift}}</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Group</label>
                                            <select class="form-control" name="group">
                                                <option value="{{$detail->group}}" selected>{{$detail->group}}</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-3">
                                        <button type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                            class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i
                                                class="fas fa-save"></i> Update</button>
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
                                                    Ya, Update</button>
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
