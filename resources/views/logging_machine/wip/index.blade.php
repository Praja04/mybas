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
                        href="/logging_machine/index/{{ $user->nik }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{url('/logging_machine/post_wip')}}" method="POST">
                                @csrf

                                <input type="text" class="form-control" value="{{ $user->id }}"
                                    name="id_logging_machine" hidden>
                                    
                                <input type="text" class="form-control" value="{{ $user->nik }}" name="nik" hidden>


                                @if ($cek_wip)
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div class="alert alert-primary" role="alert">

                                                        Opps.. Anda Sudah Mengisi Form WIP. Silahkan Lihat Di History.
                                                        <br>
                                                        <a href="/logging_machine/history_wip/{{$user->no_mesin}}/{{ Crypt::encrypt($user->nik) }}"
                                                            class="btn btn-warning btn-md mt-3"> Lihat history <i
                                                                class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Nama</label>
                                                <input type="text" class="form-control" value="{{ $user->nama }}"
                                                    name="nama" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">NIK</label>
                                                <input type="text" class="form-control" value="{{ $user->nik }}"
                                                    name="tgl_permintaan" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Rasa</label>
                                                <input type="text" class="form-control" name="rasa"
                                                    value="{{ $user->rasa }}" readonly>

                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Inner Reject(Kg)</label>
                                                <input type="text" class="form-control" name="inner_reject"
                                                    placeholder="Masukan Angka Pecahan" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sampah Inner</label>
                                                <input type="text" class="form-control" name="sampah_inner"
                                                    placeholder="Masukan Anngka Pecahan" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Total WIP</label>
                                                <input type="text" class="form-control" name="total_wip"
                                                    placeholder="Masukan Hanya Angka.." required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sortir</label>
                                                <input type="text" class="form-control" name="sortir"
                                                    placeholder="Masukan Hanya Angka" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Sobek</label>
                                                <input type="text" class="form-control" name="sobek"
                                                    placeholder="Masukan Hanya Angka" required>
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
                                @endif

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
