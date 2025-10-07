@extends('layouts.base-display')

@section('title', 'MASUKAN EMAIL ANDA')

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

            
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/internal_memo/menu/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card card-custom card-stretch">
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                                            <br>
                                            <br>
                                    <i class="symbol-badge bg-success mt-4"></i>
                                </div>
                                <div>
                                    <p class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary ml-4 mt-2">{{Auth::user()->name}}</p>
                                    <p class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary ml-4 mt-2">
                                        DEPT: {{$dept_me->name}}
                                    </p>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <br>
                            <br>
                            <div class="alert alert-primary " role="alert">
                                <h4 class="alert-heading">Informasi!</h4>
                                <hr>
                                <p><b>Untuk Menggunakan Aplikasi Ini, Kamu Di Wajibkan Mengisi E-mail.</b> </p>
                            </div>
                        </div>
                    </div>
                </div>
            <div>
        </div>
    </div>
</div>
    <div class="col-sm-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ url('internal_memo/menu/post_email/'. Auth::user()->id )}}" method="post">
                    @method('PATCH')
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Email</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control" type="email" placeholder="Masukan Email" name="email" autofocus  required/>
                                    <span class="form-text text-danger mt-2">Pastikan Memasukan Email Aktif, Untuk Sistem Mengirim Pemberitahuan IM </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6"></div>
                        <div class="col-sm-2"></div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-sm btn-primary btn-block" style="border-radius: 10px;"><i class="fas fa-save mr-2"></i> Simpan</button>
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

<script type="text/javascript">

    </script>

@endpush
